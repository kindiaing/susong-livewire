<?php

namespace App\Services;

use App\Models\LossOrder;
use App\Models\LossOrderItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * 损耗单服务
 *
 * 封装损耗单的完整生命周期：创建（含入库差异自动创建）→审核→执行→关闭/作废
 * 执行时联动 InventoryService 扣减库存和写入日志。
 *
 * 核心断链修复：入库差异自动生成损耗单，执行时调用 InventoryService::stockOut()
 */
class LossOrderService
{
    public function __construct(
        private InventoryService $inventoryService,
    ) {}

    /**
     * 从采购入库差异自动创建损耗单
     *
     * 当实际入库数量 < 采购数量时，差异部分生成损耗单
     *
     * @param PurchaseOrder $order 采购单
     * @return LossOrder|null 如果没有差异返回null
     */
    public function createFromDiscrepancy(PurchaseOrder $order): ?LossOrder
    {
        // 收集有差异的明细
        $discrepancyItems = $order->items()
            ->where('discrepancy_quantity', '>', 0)
            ->whereNull('loss_order_id')
            ->get();

        if ($discrepancyItems->isEmpty()) {
            return null;
        }

        return DB::transaction(function () use ($order, $discrepancyItems) {
            $totalAmount = 0;

            $lossOrder = LossOrder::create([
                'loss_no' => $this->generateLossNo(),
                'source_type' => 'purchase_order',
                'source_id' => $order->id,
                'warehouse_id' => $order->warehouse_id,
                'total_amount' => 0,
                'loss_type' => LossOrder::LOSS_OTHER,
                'status' => LossOrder::STATUS_PENDING,
                'approval_status' => LossOrder::APPROVAL_PENDING,
                'applicant_id' => Auth::id(),
                'reason' => "采购单 {$order->order_no} 入库差异自动生成",
            ]);

            foreach ($discrepancyItems as $item) {
                $costPrice = $item->actual_price ?: $item->price;
                $lossAmount = intdiv($item->discrepancy_quantity * $costPrice, 1000);

                LossOrderItem::create([
                    'loss_order_id' => $lossOrder->id,
                    'purchase_order_item_id' => $item->id,
                    'purchase_order_id' => $order->id,
                    'sku_id' => $item->sku_id,
                    'loss_type' => LossOrder::LOSS_OTHER,
                    'quantity' => $item->discrepancy_quantity,
                    'cost_price' => $costPrice,
                    'loss_amount' => $lossAmount,
                    'responsible_party' => LossOrderItem::PARTY_PLATFORM,
                    'reason' => $item->discrepancy_reason ?: '入库差异',
                ]);

                // 关联损耗单到采购明细
                $item->update(['loss_order_id' => $lossOrder->id]);

                $totalAmount += $lossAmount;
            }

            $lossOrder->update(['total_amount' => $totalAmount]);

            return $lossOrder->fresh();
        });
    }

    /**
     * 手动创建损耗单
     *
     * @param int $warehouseId 仓库ID
     * @param int $lossType 损耗类型
     * @param array $items [{sku_id, quantity, cost_price, responsible_party, supplier_id, reason}]
     * @param string $reason 原因
     * @return LossOrder
     */
    public function create(int $warehouseId, int $lossType, array $items, string $reason = ''): LossOrder
    {
        return DB::transaction(function () use ($warehouseId, $lossType, $items, $reason) {
            $totalAmount = 0;

            $lossOrder = LossOrder::create([
                'loss_no' => $this->generateLossNo(),
                'source_type' => 'manual',
                'source_id' => null,
                'warehouse_id' => $warehouseId,
                'total_amount' => 0,
                'loss_type' => $lossType,
                'status' => LossOrder::STATUS_PENDING,
                'approval_status' => LossOrder::APPROVAL_PENDING,
                'applicant_id' => Auth::id(),
                'reason' => $reason,
            ]);

            foreach ($items as $item) {
                $quantity = (int) $item['quantity'];
                $costPrice = (int) $item['cost_price'];
                $lossAmount = intdiv($quantity * $costPrice, 1000);

                LossOrderItem::create([
                    'loss_order_id' => $lossOrder->id,
                    'sku_id' => $item['sku_id'],
                    'loss_type' => $lossType,
                    'quantity' => $quantity,
                    'cost_price' => $costPrice,
                    'loss_amount' => $lossAmount,
                    'responsible_party' => $item['responsible_party'] ?? LossOrderItem::PARTY_PLATFORM,
                    'supplier_id' => $item['supplier_id'] ?? null,
                    'reason' => $item['reason'] ?? null,
                ]);

                $totalAmount += $lossAmount;
            }

            $lossOrder->update(['total_amount' => $totalAmount]);

            return $lossOrder->fresh();
        });
    }

    /**
     * 审核通过（待审核→已通过）
     */
    public function approve(LossOrder $lossOrder): LossOrder
    {
        if ($lossOrder->status !== LossOrder::STATUS_PENDING) {
            throw new \Exception('仅待审核状态的损耗单可审核');
        }

        $lossOrder->update([
            'status' => LossOrder::STATUS_APPROVED,
            'approval_status' => LossOrder::APPROVAL_APPROVED,
            'reviewer_id' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return $lossOrder->fresh();
    }

    /**
     * 审核拒绝
     */
    public function reject(LossOrder $lossOrder, string $reason = ''): LossOrder
    {
        if ($lossOrder->status !== LossOrder::STATUS_PENDING) {
            throw new \Exception('仅待审核状态的损耗单可拒绝');
        }

        $lossOrder->update([
            'status' => LossOrder::STATUS_CANCELLED,
            'approval_status' => LossOrder::APPROVAL_REJECTED,
            'reviewer_id' => Auth::id(),
            'reviewed_at' => now(),
            'reason' => $reason ?: $lossOrder->reason,
        ]);

        return $lossOrder->fresh();
    }

    /**
     * 执行损耗（已通过→已执行）
     *
     * 核心断链修复：调用 InventoryService::stockOut() 扣减库存
     */
    public function execute(LossOrder $lossOrder): LossOrder
    {
        if ($lossOrder->status !== LossOrder::STATUS_APPROVED) {
            throw new \Exception('仅已通过状态的损耗单可执行');
        }

        return DB::transaction(function () use ($lossOrder) {
            foreach ($lossOrder->items as $item) {
                if ($item->quantity > 0) {
                    $this->inventoryService->stockOut(
                        warehouse: $lossOrder->warehouse_id,
                        sku: $item->sku_id,
                        quantity: $item->quantity,
                        sourceType: 'loss_order',
                        sourceId: $lossOrder->id,
                        reason: "损耗单 {$lossOrder->loss_no} 执行扣减",
                    );
                }
            }

            $lossOrder->update([
                'status' => LossOrder::STATUS_EXECUTED,
                'executed_at' => now(),
            ]);

            return $lossOrder->fresh();
        });
    }

    /**
     * 关闭损耗单（已执行→已关闭）
     */
    public function close(LossOrder $lossOrder): LossOrder
    {
        if ($lossOrder->status !== LossOrder::STATUS_EXECUTED) {
            throw new \Exception('仅已执行状态的损耗单可关闭');
        }

        $lossOrder->update([
            'status' => LossOrder::STATUS_CLOSED,
            'closed_at' => now(),
        ]);

        return $lossOrder->fresh();
    }

    /**
     * 作废损耗单（仅待审核状态可作废）
     */
    public function cancel(LossOrder $lossOrder): LossOrder
    {
        if ($lossOrder->status !== LossOrder::STATUS_PENDING) {
            throw new \Exception('仅待审核状态的损耗单可作废');
        }

        $lossOrder->update([
            'status' => LossOrder::STATUS_CANCELLED,
        ]);

        return $lossOrder->fresh();
    }

    /**
     * 生成损耗单号
     */
    private function generateLossNo(): string
    {
        return 'LO' . date('YmdHis') . str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
    }
}
