<?php

namespace App\Services;

use App\Models\InventoryLog;
use App\Models\LossOrder;
use App\Models\LossOrderItem;
use App\Models\PurchaseOrder;
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
     * 当实际入库数量 < 采购数量时，差异部分生成损耗单。
     * 传入允差阈值（%）时，差异率不超过阈值的明细只留痕不纳入损耗单。
     *
     * @param  PurchaseOrder  $order  采购单
     * @param  int|null  $thresholdPct  允差阈值（差异率%），null 表示不过滤
     * @return LossOrder|null 如果没有差异返回null
     */
    public function createFromDiscrepancy(PurchaseOrder $order, ?int $thresholdPct = null): ?LossOrder
    {
        // 收集有差异的明细
        $discrepancyItems = $order->items()
            ->where('discrepancy_quantity', '>', 0)
            ->whereNull('loss_order_id')
            ->get();

        // 允差阈值过滤：差异率 ≤ 阈值的明细仅留痕，不生成损耗单
        if ($thresholdPct !== null) {
            $discrepancyItems = $discrepancyItems->filter(function ($item) use ($thresholdPct) {
                if ($item->quantity <= 0) {
                    return true;
                }

                return $item->discrepancy_quantity / $item->quantity * 100 > $thresholdPct;
            })->values();
        }

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
                $lossAmount = $item->discrepancy_quantity * $costPrice;

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
     * @param  int  $warehouseId  仓库ID
     * @param  int  $lossType  损耗类型
     * @param  array  $items  [{sku_id, quantity, cost_price, responsible_party, supplier_id, discrepancy_id, reason}]
     * @param  string  $reason  原因
     * @param  string  $sourceType  来源类型（manual/discrepancy 等）
     * @param  int|null  $sourceId  来源单据ID
     */
    public function create(int $warehouseId, int $lossType, array $items, string $reason = '', string $sourceType = 'manual', ?int $sourceId = null): LossOrder
    {
        return DB::transaction(function () use ($warehouseId, $lossType, $items, $reason, $sourceType, $sourceId) {
            $totalAmount = 0;

            $lossOrder = LossOrder::create([
                'loss_no' => $this->generateLossNo(),
                'source_type' => $sourceType,
                'source_id' => $sourceId,
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
                $lossAmount = $quantity * $costPrice;

                LossOrderItem::create([
                    'loss_order_id' => $lossOrder->id,
                    'sku_id' => $item['sku_id'],
                    'loss_type' => $lossType,
                    'quantity' => $quantity,
                    'cost_price' => $costPrice,
                    'loss_amount' => $lossAmount,
                    'responsible_party' => $item['responsible_party'] ?? LossOrderItem::PARTY_PLATFORM,
                    'supplier_id' => $item['supplier_id'] ?? null,
                    'discrepancy_id' => $item['discrepancy_id'] ?? null,
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
     * 核心断链修复：调用 InventoryService::stockOut() 扣减库存。
     * 兜底保护：来源=采购入库差异的损耗单只作账务留痕（差异部分的货未入库），
     * 执行时不扣库存，防止双重扣减。
     */
    public function execute(LossOrder $lossOrder): LossOrder
    {
        if ($lossOrder->status !== LossOrder::STATUS_APPROVED) {
            throw new \Exception('仅已通过状态的损耗单可执行');
        }

        $skipStockOut = $lossOrder->source_type === 'purchase_order';

        return DB::transaction(function () use ($lossOrder, $skipStockOut) {
            foreach ($lossOrder->items as $item) {
                if ($item->quantity > 0 && ! $skipStockOut) {
                    $this->inventoryService->stockOut(
                        warehouse: $lossOrder->warehouse_id,
                        sku: $item->sku_id,
                        quantity: $item->quantity,
                        sourceType: 'loss_order',
                        sourceId: $lossOrder->id,
                        reason: "损耗单 {$lossOrder->loss_no} 执行扣减",
                        type: InventoryLog::TYPE_LOSS_REPORT,
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
     * 格式：LO-YYYYMMDD-5位序号
     */
    private function generateLossNo(): string
    {
        return generate_sequence_no('LO', 'loss_orders', 'loss_no');
    }
}
