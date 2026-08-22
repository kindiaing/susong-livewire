<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\PurchaseReturn;
use App\Models\SupplierSettlement;
use Illuminate\Support\Facades\DB;

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
}
