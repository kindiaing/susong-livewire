<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\PurchaseOrder;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * 采购退货服务
 *
 * 封装采购退货的完整生命周期：创建→审核→出库→完成/作废
 * 出库操作联动 InventoryService 扣减库存和写入日志。
 *
 * 核心断链修复：退货出库时调用 InventoryService::stockOut()
 */
class PurchaseReturnService
{
    public function __construct(
        private InventoryService $inventoryService,
    ) {}

    /**
     * 创建关联退货单（关联采购单，调取采购单明细）
     *
     * @param  PurchaseOrder  $order  关联采购单
     * @param  array  $items  退货明细 [{purchase_order_item_id, quantity, reason}]
     * @param  string  $reason  退货原因
     *
     * @throws \Exception
     */
    public function createLinked(PurchaseOrder $order, array $items, string $reason = ''): PurchaseReturn
    {
        // 采购单必须已入库才能退货
        if (! in_array($order->status, [PurchaseOrder::STATUS_STOCKED, PurchaseOrder::STATUS_COMPLETED])) {
            throw new \Exception('仅已入库/完成状态的采购单可创建退货');
        }

        // 不能有进行中的退货单
        if ($order->hasActiveReturn()) {
            throw new \Exception('该采购单已有进行中的退货单，请先处理');
        }

        if (empty($items)) {
            throw new \Exception('退货明细不能为空');
        }

        return DB::transaction(function () use ($order, $items, $reason) {
            $return = PurchaseReturn::create([
                'return_no' => $this->generateReturnNo(),
                'return_type' => PurchaseReturn::TYPE_LINKED,
                'purchase_order_id' => $order->id,
                'supplier_id' => $order->supplier_id,
                'warehouse_id' => $order->warehouse_id,
                'status' => PurchaseReturn::STATUS_PENDING,
                'total_amount' => 0,
                'actual_amount' => 0,
                'reason' => $reason,
                'operator_id' => Auth::id(),
            ]);

            foreach ($items as $item) {
                $orderItem = $order->items()->findOrFail($item['purchase_order_item_id']);
                $quantity = (int) $item['quantity'];

                if ($quantity <= 0) {
                    throw new \Exception('退货数量必须大于0');
                }

                // 退货数量不能大于实际入库数量（决策 1A：退的货必须在库里）
                if ($quantity > $orderItem->actual_quantity) {
                    throw new \Exception("退货数量({$quantity})不能大于实际入库数量({$orderItem->actual_quantity})");
                }

                $price = $orderItem->actual_price ?: $orderItem->price;
                $amount = $quantity * $price;

                PurchaseReturnItem::create([
                    'purchase_return_id' => $return->id,
                    'purchase_order_item_id' => $orderItem->id,
                    'sku_id' => $orderItem->sku_id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'amount' => $amount,
                    'actual_quantity' => 0,
                    'actual_price' => 0,
                    'actual_amount' => 0,
                    'reason' => $item['reason'] ?? null,
                ]);
            }

            // 重算退货总金额
            $this->recalculateAmounts($return);

            return $return->fresh();
        });
    }

    /**
     * 创建普通退货单（自定义明细，不关联采购单）
     *
     * @param  int  $supplierId  供应商ID
     * @param  int  $warehouseId  出库仓库ID
     * @param  array  $items  退货明细 [{sku_id, quantity, price(厘), reason}]
     * @param  string  $reason  退货原因
     *
     * @throws \Exception
     */
    public function createNormal(int $supplierId, int $warehouseId, array $items, string $reason = ''): PurchaseReturn
    {
        if (empty($items)) {
            throw new \Exception('退货明细不能为空');
        }

        return DB::transaction(function () use ($supplierId, $warehouseId, $items, $reason) {
            $return = PurchaseReturn::create([
                'return_no' => $this->generateReturnNo(),
                'return_type' => PurchaseReturn::TYPE_NORMAL,
                'purchase_order_id' => null,
                'supplier_id' => $supplierId,
                'warehouse_id' => $warehouseId,
                'status' => PurchaseReturn::STATUS_PENDING,
                'total_amount' => 0,
                'actual_amount' => 0,
                'reason' => $reason,
                'operator_id' => Auth::id(),
            ]);

            foreach ($items as $item) {
                $quantity = (int) ($item['quantity'] ?? 0);
                $price = (int) ($item['price'] ?? 0);

                if ($quantity <= 0) {
                    throw new \Exception('退货数量必须大于0');
                }

                if ($price < 0) {
                    throw new \Exception('退货单价不能为负数');
                }

                PurchaseReturnItem::create([
                    'purchase_return_id' => $return->id,
                    'purchase_order_item_id' => null,
                    'sku_id' => (int) $item['sku_id'],
                    'quantity' => $quantity,
                    'price' => $price,
                    'amount' => $quantity * $price,
                    'actual_quantity' => 0,
                    'actual_price' => 0,
                    'actual_amount' => 0,
                    'reason' => $item['reason'] ?? null,
                ]);
            }

            $this->recalculateAmounts($return);

            return $return->fresh();
        });
    }

    /**
     * 审核通过（待审核→已审核）
     */
    public function approve(PurchaseReturn $return): PurchaseReturn
    {
        if ($return->status !== PurchaseReturn::STATUS_PENDING) {
            throw new \Exception('仅待审核状态的退货单可审核');
        }

        $oldStatus = $return->status;

        $return->update([
            'status' => PurchaseReturn::STATUS_APPROVED,
            'audited_by' => Auth::id(),
            'audited_at' => now(),
        ]);

        $this->auditStatusChange($return, 'approve', $oldStatus, PurchaseReturn::STATUS_APPROVED);

        return $return->fresh();
    }

    /**
     * 退货出库（已审核→已出库）
     *
     * 核心断链修复：调用 InventoryService::stockOut() 扣减库存
     */
    public function ship(PurchaseReturn $return, ?array $actualItems = null): PurchaseReturn
    {
        if ($return->status !== PurchaseReturn::STATUS_APPROVED) {
            throw new \Exception('仅已审核状态的退货单可出库');
        }

        $order = $return->purchaseOrder;
        // 普通退货无关联采购单，出库仓库取退货单头
        $warehouseId = $order?->warehouse_id ?? $return->warehouse_id;

        return DB::transaction(function () use ($return, $warehouseId, $actualItems) {
            foreach ($return->items as $item) {
                $actualQuantity = $actualItems
                    ? ((int) ($actualItems[$item->id]['actual_quantity'] ?? $item->quantity))
                    : $item->quantity;

                $actualPrice = $actualItems
                    ? ((int) ($actualItems[$item->id]['actual_price'] ?? $item->price))
                    : $item->price;

                $actualAmount = $actualQuantity * $actualPrice;

                // 更新明细实际出库数据
                $item->update([
                    'actual_quantity' => $actualQuantity,
                    'actual_price' => $actualPrice,
                    'actual_amount' => $actualAmount,
                ]);

                // 核心断链修复：调用 InventoryService::stockOut() 扣减库存
                if ($actualQuantity > 0) {
                    $this->inventoryService->stockOut(
                        warehouse: $warehouseId,
                        sku: $item->sku_id,
                        quantity: $actualQuantity,
                        sourceType: 'purchase_return',
                        sourceId: $return->id,
                        reason: "采购退货单 {$return->return_no} 出库",
                    );
                }
            }

            $return->update([
                'status' => PurchaseReturn::STATUS_SHIPPED,
                'shipped_at' => now(),
            ]);

            $this->auditStatusChange($return, 'ship', PurchaseReturn::STATUS_APPROVED, PurchaseReturn::STATUS_SHIPPED);

            $this->recalculateAmounts($return);

            return $return->fresh();
        });
    }

    /**
     * 完成退货（已出库→完成）
     */
    public function complete(PurchaseReturn $return): PurchaseReturn
    {
        if ($return->status !== PurchaseReturn::STATUS_SHIPPED) {
            throw new \Exception('仅已出库状态的退货单可完成');
        }

        return DB::transaction(function () use ($return) {
            $return->update([
                'status' => PurchaseReturn::STATUS_COMPLETED,
                'completed_at' => now(),
            ]);

            // 审计日志
            $this->auditStatusChange($return, 'complete', PurchaseReturn::STATUS_SHIPPED, PurchaseReturn::STATUS_COMPLETED);

            // 更新采购单的退货状态（仅关联退货）
            $return->purchaseOrder?->updateReturnStatus();

            return $return->fresh();
        });
    }

    /**
     * 作废退货
     */
    public function cancel(PurchaseReturn $return): PurchaseReturn
    {
        if (in_array($return->status, [PurchaseReturn::STATUS_SHIPPED, PurchaseReturn::STATUS_COMPLETED])) {
            throw new \Exception('已出库/完成的退货单不可作废');
        }

        return DB::transaction(function () use ($return) {
            $oldStatus = $return->status;

            $return->update([
                'status' => PurchaseReturn::STATUS_CANCELLED,
                'cancelled_at' => now(),
            ]);

            // 审计日志
            $this->auditStatusChange($return, 'cancel', $oldStatus, PurchaseReturn::STATUS_CANCELLED);

            // 更新采购单的退货状态（仅关联退货）
            $return->purchaseOrder?->updateReturnStatus();

            return $return->fresh();
        });
    }

    /**
     * 生成退货单号
     * 格式：PR-YYYYMMDD-5位序号
     */
    private function generateReturnNo(): string
    {
        return generate_sequence_no('PR', 'purchase_returns', 'return_no');
    }

    /**
     * 重算退货总金额
     */
    private function recalculateAmounts(PurchaseReturn $return): void
    {
        $return->total_amount = $return->items()->sum('amount');
        $return->actual_amount = $return->items()->sum('actual_amount');
        $return->save();
    }

    /**
     * 记录退货单状态变更审计日志
     */
    private function auditStatusChange(PurchaseReturn $return, string $action, int $oldStatus, int $newStatus, ?string $reason = null): void
    {
        if (! setting('audit_purchase_return', true)) {
            return;
        }

        AuditLog::log(
            modelType: PurchaseReturn::class,
            modelId: $return->id,
            action: $action,
            beforeData: ['status' => $oldStatus, 'status_label' => PurchaseReturn::statusMap()[$oldStatus] ?? '未知'],
            afterData: ['status' => $newStatus, 'status_label' => PurchaseReturn::statusMap()[$newStatus] ?? '未知'],
            reason: $reason,
            relationType: 'purchase_return',
            relationId: $return->id,
        );
    }
}
