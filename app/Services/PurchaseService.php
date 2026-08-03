<?php

namespace App\Services;

use App\Models\PurchaseItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 采购单服务
 *
 * 封装采购单的完整生命周期：创建→提交→接单→发货→入库→完成/取消
 * 入库操作联动 InventoryService 更新库存和写入日志。
 */
class PurchaseService
{
    public function __construct(
        private InventoryService $inventoryService,
        private LossOrderService $lossOrderService,
    ) {}

    /**
     * 从待采清单生成采购单
     *
     * 按供应商分组，同一供应商的待采项合并为一个采购单。
     * 自动匹配 SKU 的默认供应商。
     *
     * @param array $purchaseItemIds 待采清单ID数组
     * @return array 创建的采购单ID数组
     */
    public function createFromItems(array $purchaseItemIds): array
    {
        $items = PurchaseItem::with('sku.suppliers')
            ->whereIn('id', $purchaseItemIds)
            ->where('status', PurchaseItem::STATUS_PENDING)
            ->get();

        if ($items->isEmpty()) {
            throw new \Exception('没有可用的待采项');
        }

        // 按供应商分组：sku_id → supplier_id
        $grouped = [];
        foreach ($items as $item) {
            $defaultSupplier = $item->sku?->suppliers()
                ->wherePivot('is_default', true)
                ->wherePivot('status', 1)
                ->first();
            $supplierId = $defaultSupplier?->id ?? $item->sku?->suppliers()->wherePivot('status', 1)->first()?->id;
            if (!$supplierId) {
                throw new \Exception("SKU {$item->sku_id} 没有可用供应商");
            }
            $grouped[$supplierId][] = $item;
        }

        $orderIds = [];

        DB::transaction(function () use ($grouped, &$orderIds) {
            foreach ($grouped as $supplierId => $items) {
                $order = PurchaseOrder::create([
                    'order_no' => PurchaseOrder::generateOrderNo(),
                    'supplier_id' => $supplierId,
                    'status' => PurchaseOrder::STATUS_PENDING,
                    'total_amount' => 0,
                    'actual_amount' => 0,
                    'operator_id' => Auth::id(),
                    'ordered_at' => now(),
                ]);

                foreach ($items as $item) {
                    $skuSupplier = $item->sku->suppliers()
                        ->where('supplier_id', $supplierId)
                        ->first();

                    $price = $skuSupplier?->pivot?->purchase_price ?? $item->sku->purchase_price ?? 0;
                    $amount = intdiv($item->quantity * $price, 1000);

                    PurchaseOrderItem::create([
                        'purchase_order_id' => $order->id,
                        'sku_id' => $item->sku_id,
                        'quantity' => $item->quantity,
                        'price' => $price,
                        'amount' => $amount,
                        'actual_quantity' => 0,
                        'actual_price' => 0,
                        'actual_amount' => 0,
                        'strategy_price' => 0,
                        'strategy_amount' => 0,
                    ]);

                    // 标记待采项为已生成
                    $item->update(['status' => PurchaseItem::STATUS_ORDERED]);
                }

                $order->recalculateAmounts();
                $orderIds[] = $order->id;
            }
        });

        return $orderIds;
    }

    /**
     * 提交采购单（待接单→备货中）
     */
    public function submit(PurchaseOrder $order): PurchaseOrder
    {
        if (!$order->canTransitionTo(PurchaseOrder::STATUS_PREPARING)) {
            throw new \Exception('当前状态不允许提交');
        }

        $order->update([
            'status' => PurchaseOrder::STATUS_PREPARING,
        ]);

        return $order->fresh();
    }

    /**
     * 发货（备货中→已发货）
     */
    public function ship(PurchaseOrder $order): PurchaseOrder
    {
        if (!$order->canTransitionTo(PurchaseOrder::STATUS_SHIPPED)) {
            throw new \Exception('当前状态不允许发货');
        }

        $order->update([
            'status' => PurchaseOrder::STATUS_SHIPPED,
            'shipped_at' => now(),
        ]);

        return $order->fresh();
    }

    /**
     * 入库核心操作（已发货→已入库）
     *
     * 断链修复：入库时计算差异数量(discrepancy_quantity)
     * 当系统配置 stockin_auto_create_loss=true 且差异>0 时，自动创建损耗单
     *
     * @param PurchaseOrder $order 采购单
     * @param int $warehouseId 入库仓库
     * @param array $items 入库明细 [{id: purchaseOrderItemId, actual_quantity, actual_price, discrepancy_reason}]
     * @param string $batchNo 批次号
     * @return PurchaseOrder
     */
    public function stockIn(PurchaseOrder $order, int $warehouseId, array $items, string $batchNo): PurchaseOrder
    {
        if (!$order->canTransitionTo(PurchaseOrder::STATUS_STOCKED)) {
            throw new \Exception('当前状态不允许入库');
        }

        return DB::transaction(function () use ($order, $warehouseId, $items, $batchNo) {
            // 更新采购单仓库和状态
            $order->update([
                'warehouse_id' => $warehouseId,
                'status' => PurchaseOrder::STATUS_STOCKED,
                'stocked_at' => now(),
            ]);

            foreach ($items as $item) {
                $orderItem = PurchaseOrderItem::findOrFail($item['id']);
                $actualQuantity = (int) $item['actual_quantity'];
                $actualPrice = (int) ($item['actual_price'] ?? $orderItem->price);
                $actualAmount = intdiv($actualQuantity * $actualPrice, 1000);

                // 计算差异数量（采购数量 - 实际入库数量）
                $discrepancyQuantity = max(0, $orderItem->quantity - $actualQuantity);

                // 更新明细的实际数量、金额和差异数量
                $orderItem->update([
                    'actual_quantity' => $actualQuantity,
                    'actual_price' => $actualPrice,
                    'actual_amount' => $actualAmount,
                    'discrepancy_reason' => $item['discrepancy_reason'] ?? null,
                    'discrepancy_quantity' => $discrepancyQuantity,
                ]);

                // 入库数量 > 0 时才更新库存
                if ($actualQuantity > 0) {
                    $this->inventoryService->stockIn(
                        warehouse: $warehouseId,
                        sku: $orderItem->sku_id,
                        quantity: $actualQuantity,
                        batchNo: $batchNo,
                        sourceType: 'purchase_order',
                        sourceId: $order->id,
                        reason: "采购单 {$order->order_no} 入库",
                    );
                }
            }

            // 重算实际入库金额
            $order->recalculateAmounts();

            // 断链修复：当存在入库差异且系统配置开启时，自动创建损耗单
            $autoCreateLoss = (bool) setting('stockin_auto_create_loss', true);
            if ($autoCreateLoss) {
                $this->lossOrderService->createFromDiscrepancy($order->fresh());
            }

            return $order->fresh();
        });
    }

    /**
     * 完成（已入库→完成）
     *
     * 前置校验：存在未处理的入库差异（discrepancy_quantity > 0 且无关联损耗单）时阻止完成
     */
    public function complete(PurchaseOrder $order): PurchaseOrder
    {
        if (!$order->canTransitionTo(PurchaseOrder::STATUS_COMPLETED)) {
            throw new \Exception('当前状态不允许完成');
        }

        // 断链修复：检查是否有未处理的入库差异
        $unresolvedDiscrepancy = $order->items()
            ->where('discrepancy_quantity', '>', 0)
            ->whereNull('loss_order_id')
            ->exists();

        if ($unresolvedDiscrepancy) {
            throw new \Exception('存在未处理的入库差异，请先处理损耗单后再完成');
        }

        $order->update([
            'status' => PurchaseOrder::STATUS_COMPLETED,
        ]);

        return $order->fresh();
    }

    /**
     * 取消
     *
     * 断链修复：已入库/完成状态的采购单禁止直接取消，必须走退货流程
     */
    public function cancel(PurchaseOrder $order): PurchaseOrder
    {
        if (!$order->canTransitionTo(PurchaseOrder::STATUS_CANCELLED)) {
            // 给出更明确的错误提示
            if (in_array($order->status, [PurchaseOrder::STATUS_STOCKED, PurchaseOrder::STATUS_COMPLETED])) {
                throw new \Exception('已入库/完成的采购单禁止直接取消，请走退货流程');
            }
            throw new \Exception('当前状态不允许取消');
        }

        $order->update([
            'status' => PurchaseOrder::STATUS_CANCELLED,
        ]);

        return $order->fresh();
    }

    /**
     * 添加采购单明细
     */
    public function addItem(PurchaseOrder $order, int $skuId, int $quantity, int $price): PurchaseOrderItem
    {
        if ($order->status !== PurchaseOrder::STATUS_PENDING && $order->status !== PurchaseOrder::STATUS_PREPARING) {
            throw new \Exception('仅待接单/备货中状态可添加明细');
        }

        $item = PurchaseOrderItem::create([
            'purchase_order_id' => $order->id,
            'sku_id' => $skuId,
            'quantity' => $quantity,
            'price' => $price,
            'amount' => intdiv($quantity * $price, 1000),
            'actual_quantity' => 0,
            'actual_price' => 0,
            'actual_amount' => 0,
            'strategy_price' => 0,
            'strategy_amount' => 0,
        ]);

        $order->recalculateAmounts();

        return $item;
    }

    /**
     * 删除采购单明细
     */
    public function removeItem(PurchaseOrderItem $item): void
    {
        $order = $item->purchaseOrder;

        if ($order->status !== PurchaseOrder::STATUS_PENDING && $order->status !== PurchaseOrder::STATUS_PREPARING) {
            throw new \Exception('仅待接单/备货中状态可删除明细');
        }

        $item->delete();
        $order->recalculateAmounts();
    }
}
