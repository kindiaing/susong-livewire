<?php

namespace App\Imports;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * 采购单专用导入类
 *
 * 与 GenericImport 不同，采购单需要创建单据头（PurchaseOrder）+ 多条明细（PurchaseOrderItem）。
 * Excel 每行一条明细，相同采购单号的行归为同一张采购单。
 *
 * 列映射（中文表头 → 字段）：
 *   采购单号     → order_no（可选，留空自动生成）
 *   供应商ID     → supplier_id（必填）
 *   仓库ID       → warehouse_id（必填）
 *   采购日期     → purchase_date（可选，格式 Y-m-d）
 *   SKU ID       → sku_id（明细必填）
 *   采购数量     → quantity（明细必填，最小单位数量）
 *   采购单价(元) → price（明细必填，元→厘转换）
 *   明细备注     → item_remark（可选）
 *   备注         → remark（单据头备注，同一采购单号取首行）
 */
class PurchaseOrderImport implements ToCollection, WithHeadingRow
{
    protected int $importedCount = 0;

    protected int $skippedCount = 0;

    protected array $errorRows = [];

    public function collection(Collection $rows): void
    {
        // 按采购单号分组（空号归入 '__auto__' 组，每组各自生成）
        $grouped = [];

        foreach ($rows as $index => $row) {
            $lineNo = $index + 2; // 行号（1=表头）

            try {
                $orderNo = $this->getRowValue($row, ['采购单号', 'order_no']);
                $supplierId = $this->getRowValue($row, ['供应商ID', 'supplier_id', '供应商id']);
                $warehouseId = $this->getRowValue($row, ['仓库ID', 'warehouse_id', '仓库id']);
                $purchaseDate = $this->getRowValue($row, ['采购日期', 'purchase_date']);
                $skuId = $this->getRowValue($row, ['SKU ID', 'sku_id', 'sku', 'SKU_ID', 'SKUID']);
                $quantity = $this->getRowValue($row, ['采购数量', 'quantity']);
                $price = $this->getRowValue($row, ['采购单价(元)', '采购单价', 'price']);
                $remark = $this->getRowValue($row, ['备注', 'remark']);
                $itemRemark = $this->getRowValue($row, ['明细备注', 'item_remark']);

                // 校验必填字段
                $missing = [];
                if ($supplierId === null || $supplierId === '') {
                    $missing[] = '供应商ID';
                }
                if ($warehouseId === null || $warehouseId === '') {
                    $missing[] = '仓库ID';
                }
                if ($skuId === null || $skuId === '') {
                    $missing[] = 'SKU ID';
                }
                if ($quantity === null || $quantity === '') {
                    $missing[] = '采购数量';
                }
                if ($price === null || $price === '') {
                    $missing[] = '采购单价(元)';
                }

                if (! empty($missing)) {
                    $this->errorRows[] = [
                        'row' => $lineNo,
                        'error' => '缺少必填字段：'.implode('、', $missing),
                    ];
                    continue;
                }

                // 数值转换
                $supplierId = (int) $supplierId;
                $warehouseId = (int) $warehouseId;
                $skuId = (int) $skuId;
                $quantity = (int) $quantity;
                $priceCents = money_to_cents((string) $price);
                $amountCents = $priceCents * $quantity;

                $groupKey = trim((string) $orderNo) ?: '__auto_'.$lineNo;

                if (! isset($grouped[$groupKey])) {
                    $grouped[$groupKey] = [
                        'order_no' => trim((string) $orderNo) ?: '',
                        'supplier_id' => $supplierId,
                        'warehouse_id' => $warehouseId,
                        'purchase_date' => $purchaseDate ?: null,
                        'remark' => $remark ?: null,
                        'items' => [],
                    ];
                }

                $grouped[$groupKey]['items'][] = [
                    'sku_id' => $skuId,
                    'quantity' => $quantity,
                    'price' => $priceCents,
                    'amount' => $amountCents,
                    'remark' => $itemRemark ?: null,
                    'line_no' => $lineNo,
                ];
            } catch (\Exception $e) {
                $this->errorRows[] = [
                    'row' => $lineNo,
                    'error' => $e->getMessage(),
                ];
            }
        }

        // 按组创建采购单 + 明细
        foreach ($grouped as $group) {
            try {
                DB::transaction(function () use ($group) {
                    $orderNo = $group['order_no'];

                    // 如果指定了采购单号，检查是否已存在
                    if ($orderNo !== '') {
                        $exists = PurchaseOrder::where('order_no', $orderNo)->exists();
                        if ($exists) {
                            $this->skippedCount++;
                            return;
                        }
                    } else {
                        $orderNo = PurchaseOrder::generateOrderNo();
                    }

                    $totalAmount = array_sum(array_column($group['items'], 'amount'));

                    $order = PurchaseOrder::create([
                        'order_no' => $orderNo,
                        'supplier_id' => $group['supplier_id'],
                        'warehouse_id' => $group['warehouse_id'],
                        'status' => PurchaseOrder::STATUS_PENDING,
                        'total_amount' => $totalAmount,
                        'actual_amount' => 0,
                        'operator_id' => Auth::id(),
                        'ordered_at' => now(),
                        'purchase_date' => $group['purchase_date'],
                        'remark' => $group['remark'],
                    ]);

                    foreach ($group['items'] as $itemData) {
                        PurchaseOrderItem::create([
                            'purchase_order_id' => $order->id,
                            'sku_id' => $itemData['sku_id'],
                            'quantity' => $itemData['quantity'],
                            'price' => $itemData['price'],
                            'amount' => $itemData['amount'],
                            'actual_quantity' => 0,
                            'actual_price' => 0,
                            'actual_amount' => 0,
                            'remark' => $itemData['remark'],
                        ]);
                    }

                    $this->importedCount++;
                });
            } catch (\Exception $e) {
                // 记录该组第一条明细的行号
                $firstLineNo = $group['items'][0]['line_no'] ?? 0;
                $this->errorRows[] = [
                    'row' => $firstLineNo,
                    'error' => '创建采购单失败：'.$e->getMessage(),
                ];
            }
        }
    }

    /**
     * 从行数据中按多个可能的键名取值（兼容大小写/空格差异）
     */
    private function getRowValue(Collection $row, array $keys): mixed
    {
        foreach ($keys as $key) {
            $value = $row->get($key);
            if ($value !== null && $value !== '') {
                return $value;
            }
            // 尝试小写
            $value = $row->get(strtolower($key));
            if ($value !== null && $value !== '') {
                return $value;
            }
            // 尝试去除空格
            $value = $row->get(str_replace(' ', '', $key));
            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return null;
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }

    public function getErrorRows(): array
    {
        return $this->errorRows;
    }
}
