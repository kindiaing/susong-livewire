<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\PurchaseReturn;
use App\Models\SettlementPayment;
use App\Models\SupplierSettlement;
use App\Services\ApprovalService;
use App\Models\ApprovalTypeConfig;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 供应商结算服务（周期结算模式）
 *
 * 口径 A：应付 = Σ 周期内已入库采购单的实际入库金额 − 周期内采购退货扣减 − 服务费。
 * 入库差异天然持平：少收的货按实际入库金额计价，差异损耗单作为留痕证据。
 */
class SupplierSettlementService
{
    /**
     * 按结算周期生成结算单
     *
     * @param  int  $supplierId  供应商ID
     * @param  string  $startDate  周期开始（Y-m-d）
     * @param  string  $endDate  周期结束（Y-m-d）
     * @param  int  $serviceFee  服务费（厘）
     *
     * @throws \Exception 周期内没有可结算的已入库采购单时抛出
     */
    public function generate(int $supplierId, string $startDate, string $endDate, int $serviceFee = 0): SupplierSettlement
    {
        // 周期内已入库/已完成的采购单，排除已被其他结算单结算过的（防重复结算）
        $settledOrderIds = DB::table('supplier_settlement_items')->pluck('purchase_order_id');

        $orders = PurchaseOrder::where('supplier_id', $supplierId)
            ->whereIn('status', [PurchaseOrder::STATUS_STOCKED, PurchaseOrder::STATUS_COMPLETED])
            ->whereDate('stocked_at', '>=', $startDate)
            ->whereDate('stocked_at', '<=', $endDate)
            ->whereNotIn('id', $settledOrderIds)
            ->get();

        if ($orders->isEmpty()) {
            throw new \Exception('该结算周期内没有可结算的已入库采购单（或已全部结算）');
        }

        // 周期内已完成的采购退货，按实际出库金额扣减应付
        $returnAmount = (int) PurchaseReturn::where('supplier_id', $supplierId)
            ->where('status', PurchaseReturn::STATUS_COMPLETED)
            ->whereDate('completed_at', '>=', $startDate)
            ->whereDate('completed_at', '<=', $endDate)
            ->sum('actual_amount');

        $totalAmount = (int) $orders->sum('actual_amount');
        $payableAmount = max(0, $totalAmount - $returnAmount - $serviceFee);

        return DB::transaction(function () use ($supplierId, $startDate, $endDate, $serviceFee, $orders, $totalAmount, $returnAmount, $payableAmount) {
            $settlement = SupplierSettlement::create([
                'settlement_no' => generate_sequence_no('SS', 'supplier_settlements', 'settlement_no'),
                'supplier_id' => $supplierId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'total_amount' => $totalAmount,
                'service_fee' => $serviceFee,
                'return_amount' => $returnAmount,
                'payable_amount' => $payableAmount,
                'paid_amount' => 0,
                'status' => SupplierSettlement::STATUS_PENDING,
            ]);

            foreach ($orders as $order) {
                $settlement->items()->create([
                    'purchase_order_id' => $order->id,
                    'amount' => $order->actual_amount,
                ]);
            }

            return $settlement->fresh(['items']);
        });
    }

    /**
     * 审核通过后执行付款（由 Approvals 组件分发调用）
     * target_id 是 settlement_payments 表的 ID
     */
    public function applyPayment(int $paymentId): void
    {
        $payment = SettlementPayment::find($paymentId);
        if (! $payment) {
            return;
        }

        $settlement = SupplierSettlement::find($payment->settlement_id);
        if (! $settlement) {
            return;
        }

        DB::transaction(function () use ($payment, $settlement) {
            $payment->update([
                'approval_status' => SettlementPayment::APPROVAL_APPROVED,
            ]);

            $newPaidAmount = $settlement->paid_amount + $payment->amount;
            $newStatus = $newPaidAmount >= $settlement->payable_amount
                ? SupplierSettlement::STATUS_SETTLED
                : SupplierSettlement::STATUS_PARTIAL;

            $settlement->update([
                'paid_amount' => $newPaidAmount,
                'status' => $newStatus,
                'settled_at' => $newStatus === SupplierSettlement::STATUS_SETTLED ? now() : null,
            ]);
        });
    }

    /**
     * 审核拒绝后标记付款记录为已拒绝（由 Approvals 组件分发调用）
     * target_id 是 settlement_payments 表的 ID
     */
    public function rejectPayment(int $paymentId): void
    {
        $payment = SettlementPayment::find($paymentId);
        if (! $payment) {
            return;
        }

        $payment->update([
            'approval_status' => SettlementPayment::APPROVAL_REJECTED,
        ]);
    }

    /**
     * 登记付款（受 supplier_payment 审核开关控制）
     * - 审核开启时：创建付款记录（待审核）+ 审批记录，不更新结算单已付金额
     * - 审核关闭时：创建付款记录（已通过）+ 直接更新结算单已付金额
     */
    public function recordPayment(int $settlementId, int $amount, int $paymentMethod, ?string $remark = null): SettlementPayment
    {
        $settlement = SupplierSettlement::findOrFail($settlementId);
        $remaining = $settlement->payable_amount - $settlement->paid_amount;

        if ($amount <= 0) {
            throw new \Exception('付款金额必须大于0');
        }
        if ($amount > $remaining) {
            throw new \Exception('付款金额超出剩余应付 ' . money_format($remaining) . ' 元');
        }

        $needsApproval = ApprovalTypeConfig::isApprovalRequired('supplier_payment');

        return DB::transaction(function () use ($settlement, $amount, $paymentMethod, $remark, $needsApproval) {
            $approvalStatus = $needsApproval
                ? SettlementPayment::APPROVAL_PENDING
                : SettlementPayment::APPROVAL_APPROVED;

            $payment = SettlementPayment::create([
                'settlement_id' => $settlement->id,
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'operator_id' => Auth::id(),
                'approval_status' => $approvalStatus,
                'remark' => $remark,
            ]);

            if ($needsApproval) {
                ApprovalService::createRequest(
                    typeCode: 'supplier_payment',
                    targetType: 'settlement_payment',
                    targetId: $payment->id,
                    beforeData: ['paid_amount' => $settlement->paid_amount, 'status' => $settlement->status],
                    afterData: ['amount' => $amount, 'payment_method' => $paymentMethod],
                    amount: $amount,
                    reason: "结算单 {$settlement->settlement_no} 付款待审",
                );
            } else {
                // 无需审核，直接更新结算单已付金额
                $newPaidAmount = $settlement->paid_amount + $amount;
                $newStatus = $newPaidAmount >= $settlement->payable_amount
                    ? SupplierSettlement::STATUS_SETTLED
                    : SupplierSettlement::STATUS_PARTIAL;

                $settlement->update([
                    'paid_amount' => $newPaidAmount,
                    'status' => $newStatus,
                    'settled_at' => $newStatus === SupplierSettlement::STATUS_SETTLED ? now() : null,
                ]);
            }

            return $payment;
        });
    }
}
