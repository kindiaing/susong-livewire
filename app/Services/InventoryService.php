<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\InventoryLog;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\Sku;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * 统一库存服务
 *
 * 封装入库/出库/调整操作的库存变动 + 日志写入，确保数据一致性。
 * 所有库存变动应通过此服务执行，禁止直接操作 Inventory 模型。
 */
class InventoryService
{
    /**
     * 入库
     *
     * @param Warehouse|int $warehouse 仓库或仓库ID
     * @param Sku|int $sku SKU或SKU ID
     * @param int $quantity 入库数量（正数）
     * @param string $batchNo 批次号
     * @param string|null $sourceType 业务来源类型（如 purchase_order）
     * @param int|null $sourceId 业务来源ID
     * @param string|null $reason 原因
     * @param string|null $expiryDate 效期 (Y-m-d)
     * @return Inventory 更新后的库存记录
     */
    public function stockIn(
        $warehouse,
        $sku,
        int $quantity,
        string $batchNo,
        ?string $sourceType = null,
        ?int $sourceId = null,
        ?string $reason = null,
        ?string $expiryDate = null,
    ): Inventory {
        $warehouseId = is_int($warehouse) ? $warehouse : $warehouse->id;
        $skuId = is_int($sku) ? $sku : $sku->id;

        return DB::transaction(function () use ($warehouseId, $skuId, $quantity, $batchNo, $sourceType, $sourceId, $reason, $expiryDate) {
            // 查找或创建库存记录（按仓库+SKU+批次唯一）
            $inventory = Inventory::where('warehouse_id', $warehouseId)
                ->where('sku_id', $skuId)
                ->where('batch_no', $batchNo)
                ->lockForUpdate()
                ->first();

            $beforeStock = $inventory?->total_stock ?? 0;

            if ($inventory) {
                $inventory->increment('total_stock', $quantity);
                $inventory->increment('available_stock', $quantity);
                $inventory->refresh();
            } else {
                $inventory = Inventory::create([
                    'warehouse_id' => $warehouseId,
                    'sku_id' => $skuId,
                    'total_stock' => $quantity,
                    'locked_stock' => 0,
                    'available_stock' => $quantity,
                    'batch_no' => $batchNo,
                    'expiry_date' => $expiryDate,
                    'warning_value' => 0,
                ]);
            }

            $afterStock = $inventory->total_stock;

            // 写入库存日志
            $this->writeLog(
                warehouseId: $warehouseId,
                skuId: $skuId,
                type: InventoryLog::TYPE_IN,
                quantity: $quantity,
                beforeStock: $beforeStock,
                afterStock: $afterStock,
                sourceType: $sourceType,
                sourceId: $sourceId,
                reason: $reason ?? '入库',
            );

            return $inventory;
        });
    }

    /**
     * 出库
     *
     * @param Warehouse|int $warehouse
     * @param Sku|int $sku
     * @param int $quantity 出库数量（正数）
     * @param string|null $batchNo 指定批次号（null则从最早批次出库 FIFO）
     * @param string|null $sourceType
     * @param int|null $sourceId
     * @param string|null $reason
     * @return bool
     * @throws \Exception 库存不足时抛出异常
     */
    public function stockOut(
        $warehouse,
        $sku,
        int $quantity,
        ?string $batchNo = null,
        ?string $sourceType = null,
        ?int $sourceId = null,
        ?string $reason = null,
    ): bool {
        $warehouseId = is_int($warehouse) ? $warehouse : $warehouse->id;
        $skuId = is_int($sku) ? $sku : $sku->id;

        return DB::transaction(function () use ($warehouseId, $skuId, $quantity, $batchNo, $sourceType, $sourceId, $reason) {
            $query = Inventory::where('warehouse_id', $warehouseId)
                ->where('sku_id', $skuId)
                ->where('available_stock', '>=', 1);

            if ($batchNo) {
                $query->where('batch_no', $batchNo);
            }

            $inventory = $query->orderBy('expiry_date') // FIFO：效期最近的先出
                ->lockForUpdate()
                ->first();

            if (!$inventory || $inventory->available_stock < $quantity) {
                throw new \Exception("库存不足：仓库ID {$warehouseId}，SKU ID {$skuId}，需出库 {$quantity}，可用库存 " . ($inventory?->available_stock ?? 0));
            }

            $beforeStock = $inventory->total_stock;
            $inventory->decrement('total_stock', $quantity);
            $inventory->decrement('available_stock', $quantity);
            $inventory->refresh();

            $afterStock = $inventory->total_stock;

            $this->writeLog(
                warehouseId: $warehouseId,
                skuId: $skuId,
                type: InventoryLog::TYPE_OUT,
                quantity: -$quantity,
                beforeStock: $beforeStock,
                afterStock: $afterStock,
                sourceType: $sourceType,
                sourceId: $sourceId,
                reason: $reason ?? '出库',
            );

            return true;
        });
    }

    /**
     * 库存调整
     *
     * @param Warehouse|int $warehouse
     * @param Sku|int $sku
     * @param int $diff 调整差额（正数=增加，负数=减少）
     * @param string $reason 调整原因（必填）
     * @param string|null $batchNo
     * @param string|null $sourceType
     * @param int|null $sourceId
     * @return Inventory
     */
    public function adjust(
        $warehouse,
        $sku,
        int $diff,
        string $reason,
        ?string $batchNo = '',
        ?string $sourceType = null,
        ?int $sourceId = null,
    ): Inventory {
        $warehouseId = is_int($warehouse) ? $warehouse : $warehouse->id;
        $skuId = is_int($sku) ? $sku : $sku->id;

        return DB::transaction(function () use ($warehouseId, $skuId, $diff, $reason, $batchNo, $sourceType, $sourceId) {
            $inventory = Inventory::where('warehouse_id', $warehouseId)
                ->where('sku_id', $skuId)
                ->when($batchNo !== '', fn($q) => $q->where('batch_no', $batchNo))
                ->lockForUpdate()
                ->first();

            if (!$inventory) {
                throw new \Exception("库存记录不存在：仓库ID {$warehouseId}，SKU ID {$skuId}");
            }

            $beforeStock = $inventory->total_stock;

            $inventory->increment('total_stock', $diff);
            $inventory->available_stock = max(0, $inventory->available_stock + $diff);
            $inventory->save();
            $inventory->refresh();

            $afterStock = $inventory->total_stock;

            $type = $diff > 0 ? InventoryLog::TYPE_OVER : InventoryLog::TYPE_ADJUST;

            $this->writeLog(
                warehouseId: $warehouseId,
                skuId: $skuId,
                type: $type,
                quantity: $diff,
                beforeStock: $beforeStock,
                afterStock: $afterStock,
                sourceType: $sourceType,
                sourceId: $sourceId,
                reason: $reason,
            );

            return $inventory;
        });
    }

    /**
     * 写入库存变动日志
     */
    private function writeLog(
        int $warehouseId,
        int $skuId,
        int $type,
        int $quantity,
        int $beforeStock,
        int $afterStock,
        ?string $sourceType = null,
        ?int $sourceId = null,
        ?string $reason = null,
    ): InventoryLog {
        return InventoryLog::create([
            'warehouse_id' => $warehouseId,
            'sku_id' => $skuId,
            'type' => $type,
            'quantity' => $quantity,
            'before_stock' => $beforeStock,
            'after_stock' => $afterStock,
            'reason' => $reason,
            'operator_id' => Auth::id(),
            'source_type' => $sourceType,
            'source_id' => $sourceId,
        ]);
    }
}
