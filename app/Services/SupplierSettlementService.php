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
     * 审核通过后标记付款记录为「已通过待执行」（由 Approvals 组件分发调用）
     * target_id 是 settlement_payments 表的 ID
     *
     * 注意：审核通过不直接累计已付金额，需出纳在结算单详情页执行付款后才生效。
     */
    public function applyPayment(int $paymentId): void
    {
        $payment = SettlementPayment::find($paymentId);
        if (! $payment) {
            return;
        }

        $payment->update([
            'approval_status' => SettlementPayment::APPROVAL_APPROVED,
        ]);
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
     * 出纳执行付款：审核通过的付款记录，由出纳确认付款信息后真正生效
     *
     * 只有 APPROVAL_APPROVED（已通过待执行）的付款记录才能执行；
     * 执行后累计已付金额、更新结算单状态（部分付款/已结清）。
     *
     * @param  int  $paymentId  付款记录ID
     * @param  int  $paymentMethod  付款方式：1银行转账，2线下现金，3后台手工
     * @param  string|null  $transactionNo  第三方交易号（非必填）
     * @param  string|null  $remark  执行备注（非必填）
     * @param  string|null  $evidenceUrls  付款凭证图片数组 JSON 字符串（非必填）
     *
     * @throws \Exception 付款记录不存在、状态不允许或金额异常时抛出
     */
    public function executePayment(int $paymentId, int $paymentMethod = SettlementPayment::METHOD_MANUAL, ?string $transactionNo = null, ?string $remark = null, ?string $evidenceUrls = null): SettlementPayment
    {
        $payment = SettlementPayment::findOrFail($paymentId);

        if ($payment->approval_status !== SettlementPayment::APPROVAL_APPROVED) {
            throw new \Exception('仅已通过待执行的付款记录可执行');
        }

        $settlement = SupplierSettlement::findOrFail($payment->settlement_id);

        return DB::transaction(function () use ($payment, $settlement, $paymentMethod, $transactionNo, $remark, $evidenceUrls) {
            // 金额校验：累计后不得超过应付
            $newPaidAmount = $settlement->paid_amount + $payment->amount;
            if ($newPaidAmount > $settlement->payable_amount) {
                throw new \Exception('付款金额超出剩余应付，无法执行');
            }

            $payment->update([
                'approval_status' => SettlementPayment::APPROVAL_EXECUTED,
                'payment_method' => $paymentMethod,
                'transaction_no' => $transactionNo,
                'remark' => $remark,
                'evidence_urls' => $evidenceUrls ? json_decode($evidenceUrls, true) : null,
            ]);

            $newStatus = $newPaidAmount >= $settlement->payable_amount
                ? SupplierSettlement::STATUS_SETTLED
                : SupplierSettlement::STATUS_PARTIAL;

            $settlement->update([
                'paid_amount' => $newPaidAmount,
                'status' => $newStatus,
                'settled_at' => $newStatus === SupplierSettlement::STATUS_SETTLED ? now() : null,
            ]);

            return $payment->fresh();
        });
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
            // 审核开启：登记为待审核；审核关闭：直接视为已执行付款（已累计金额）
            $approvalStatus = $needsApproval
                ? SettlementPayment::APPROVAL_PENDING
                : SettlementPayment::APPROVAL_EXECUTED;

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
