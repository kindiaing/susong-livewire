<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Discrepancy;
use App\Models\InventoryLog;
use App\Models\LossOrder;
use App\Models\LossOrderItem;
use App\Models\OrderItem;
use App\Models\PurchaseOrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 差异单服务
 *
 * 差异（事实登记，不动库存和钱）与损耗（处置，动库存成本）分离：
 * - create()：登记差异事实，只写差异单，不触碰库存/账户
 * - process()：定责任方 + 处理决策，按决策分流：
 *   - 报损：受 discrepancy_auto_create_loss 开关控制，自动生成待审核损耗单（库存由损耗单执行时扣减）
 *   - 补货：仅登记决策，补货生成待采项为二期
 *   - 退款/扣款：仅登记决策（approval_status 待审核），金额调整为二期
 *   - 不计：直接关闭
 * - restockToWarehouse()：拒收/残次货品回库，走 InventoryService::stockIn 留痕
 *
 * 所有状态变更受 audit_discrepancy 开关控制写审计日志。
 */
class DiscrepancyService
{
    public function __construct(
        private InventoryService $inventoryService,
        private LossOrderService $lossOrderService,
    ) {}

    /**
     * 登记差异单（只登记事实，不动库存和钱）
     *
     * 同一订单明细 + 同一环节存在未完结（待处理/处理中/争议中）差异单时，拒绝重复登记。
     *
     * @param  array{order_id?: ?int, order_item_id?: ?int, purchase_order_id?: ?int, picking_task_id?: ?int, delivery_task_id?: ?int, delivery_note_item_id?: ?int, sku_id?: ?int, warehouse_id?: ?int, unit_id?: ?int, unit_quantity?: int, stage: int, type: int, expected_quantity: int, actual_quantity: int, amount_diff?: int, reason?: ?string, remark?: ?string}  $data
     */
    public function create(array $data): Discrepancy
    {
        $orderItemId = $data['order_item_id'] ?? null;
        $purchaseOrderItemId = $data['purchase_order_item_id'] ?? null;
        $stage = (int) $data['stage'];

        // 重复登记检查：同一订单明细 + 同一环节，或同一采购明细 + 入库环节
        if ($orderItemId) {
            $exists = Discrepancy::where('order_item_id', $orderItemId)
                ->where('stage', $stage)
                ->whereIn('status', [Discrepancy::STATUS_PENDING, Discrepancy::STATUS_PROCESSING, Discrepancy::STATUS_DISPUTED])
                ->exists();
            if ($exists) {
                throw new \Exception('该明细在此环节已有未完结的差异单，请勿重复登记');
            }
        } elseif ($purchaseOrderItemId) {
            $exists = Discrepancy::where('purchase_order_item_id', $purchaseOrderItemId)
                ->where('stage', $stage)
                ->whereIn('status', [Discrepancy::STATUS_PENDING, Discrepancy::STATUS_PROCESSING, Discrepancy::STATUS_DISPUTED])
                ->exists();
            if ($exists) {
                throw new \Exception('该采购明细已有未完结的差异单，请勿重复登记');
            }
        }

        return DB::transaction(function () use ($data, $orderItemId, $purchaseOrderItemId, $stage) {
            $expected = (int) $data['expected_quantity'];
            $actual = (int) $data['actual_quantity'];
            $quantityDiff = $expected - $actual;

            // 订单明细只查一次：金额估算与 sku_id 回填共用
            $orderItem = $orderItemId ? OrderItem::find($orderItemId) : null;

            // 采购明细只查一次：金额估算与 sku_id 回填共用
            $purchaseOrderItem = $purchaseOrderItemId ? PurchaseOrderItem::find($purchaseOrderItemId) : null;

            // 金额差异未显式传入时，按明细单价自动估算（厘）
            $amountDiff = $data['amount_diff'] ?? null;
            if ($amountDiff === null && $orderItem) {
                $amountDiff = $quantityDiff * (int) ($orderItem->price ?? 0);
            } elseif ($amountDiff === null && $purchaseOrderItem) {
                $amountDiff = $quantityDiff * (int) ($purchaseOrderItem->actual_price ?: $purchaseOrderItem->price ?? 0);
            }

            // sku_id 回填优先级：显式传入 > 订单明细 > 采购明细
            $skuId = $data['sku_id'] ?? $orderItem?->sku_id ?? $purchaseOrderItem?->sku_id ?? null;

            $discrepancy = Discrepancy::create([
                'discrepancy_no' => generate_sequence_no('DIS', 'discrepancies', 'discrepancy_no'),
                'order_id' => $data['order_id'] ?? null,
                'order_item_id' => $orderItemId,
                'purchase_order_id' => $data['purchase_order_id'] ?? null,
                'purchase_order_item_id' => $purchaseOrderItemId,
                'picking_task_id' => $data['picking_task_id'] ?? null,
                'delivery_task_id' => $data['delivery_task_id'] ?? null,
                'delivery_note_item_id' => $data['delivery_note_item_id'] ?? null,
                'sku_id' => $skuId,
                'warehouse_id' => $data['warehouse_id'] ?? null,
                'unit_id' => $data['unit_id'] ?? null,
                'unit_quantity' => (int) ($data['unit_quantity'] ?? 1),
                'stage' => $stage,
                'type' => (int) $data['type'],
                'expected_quantity' => $expected,
                'actual_quantity' => $actual,
                'quantity_diff' => $quantityDiff,
                'amount_diff' => (int) ($amountDiff ?? 0),
                'reason' => $data['reason'] ?? null,
                'status' => Discrepancy::STATUS_PENDING,
                'approval_status' => Discrepancy::APPROVAL_PENDING,
                'remark' => $data['remark'] ?? null,
            ]);

            $this->audit($discrepancy, 'create', null, [
                'stage' => $stage,
                'stage_label' => Discrepancy::stageMap()[$stage] ?? '未知',
                'type' => $discrepancy->type,
                'type_label' => $discrepancy->type_label,
                'quantity_diff' => $discrepancy->quantity_diff,
            ], $discrepancy->reason);

            return $discrepancy->fresh();
        });
    }

    /**
     * 处理差异单：定责任方 + 处理决策
     *
     * @param  int|null  $warehouseId  决策为报损且自动生成损耗单时必填（损耗扣减的仓库）
     */
    public function process(Discrepancy $discrepancy, int $responsibleParty, int $decision, ?string $remark = null, ?int $warehouseId = null): Discrepancy
    {
        if (! in_array($discrepancy->status, [Discrepancy::STATUS_PENDING, Discrepancy::STATUS_PROCESSING, Discrepancy::STATUS_DISPUTED])) {
            throw new \Exception('仅待处理/处理中/争议中的差异单可处理');
        }

        return DB::transaction(function () use ($discrepancy, $responsibleParty, $decision, $remark, $warehouseId) {
            $before = [
                'status' => $discrepancy->status,
                'status_label' => $discrepancy->status_label,
                'responsible_party' => $discrepancy->responsible_party,
                'decision' => $discrepancy->decision,
            ];

            $lossOrder = null;
            $newStatus = Discrepancy::STATUS_RESOLVED;

            if ($decision === Discrepancy::DECISION_LOSS && setting('discrepancy_auto_create_loss', true)) {
                $lossOrder = $this->createLossOrder($discrepancy, $responsibleParty, $warehouseId);
            }

            if ($decision === Discrepancy::DECISION_IGNORE) {
                $newStatus = Discrepancy::STATUS_CLOSED;
            }

            $discrepancy->update([
                'responsible_party' => $responsibleParty,
                'decision' => $decision,
                'status' => $newStatus,
                'handler_id' => Auth::id(),
                'handled_at' => now(),
                'remark' => $remark ?: $discrepancy->remark,
            ]);

            $this->audit($discrepancy, 'process', $before, [
                'status' => $newStatus,
                'status_label' => Discrepancy::statusMap()[$newStatus] ?? '未知',
                'responsible_party' => $responsibleParty,
                'decision' => $decision,
                'loss_order_no' => $lossOrder?->loss_no,
            ], $remark);

            // 差异处理完成后回写应收（防重复：检查 is_amount_adjusted）
            // 仅当有关联订单、金额差异非 0、且决策不是「不计」时回写
            if ($discrepancy->order_id
                && $discrepancy->amount_diff
                && ! $discrepancy->is_amount_adjusted
                && $decision !== Discrepancy::DECISION_IGNORE
            ) {
                try {
                    app(ReceivableService::class)->applyDiscrepancyAmount(
                        (int) $discrepancy->order_id,
                        (int) $discrepancy->amount_diff,
                    );
                    $discrepancy->update(['is_amount_adjusted' => 1]);
                } catch (\Exception $e) {
                    Log::warning('差异回写应收失败', [
                        'discrepancy_id' => $discrepancy->id,
                        'order_id' => $discrepancy->order_id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            return $discrepancy->fresh();
        });
    }

    /**
     * 标记为处理中（待处理 → 处理中）
     */
    public function markProcessing(Discrepancy $discrepancy, ?string $remark = null): Discrepancy
    {
        if ($discrepancy->status !== Discrepancy::STATUS_PENDING) {
            throw new \Exception('仅待处理的差异单可标记为处理中');
        }

        return DB::transaction(function () use ($discrepancy, $remark) {
            $before = ['status' => $discrepancy->status, 'status_label' => $discrepancy->status_label];

            $discrepancy->update([
                'status' => Discrepancy::STATUS_PROCESSING,
                'handler_id' => Auth::id(),
                'handled_at' => now(),
                'remark' => $remark ?: $discrepancy->remark,
            ]);

            $this->audit($discrepancy, 'mark_processing', $before, [
                'status' => Discrepancy::STATUS_PROCESSING,
                'status_label' => '处理中',
            ], $remark);

            return $discrepancy->fresh();
        });
    }

    /**
     * 标记为争议中（待处理/处理中 → 争议中）
     */
    public function markDisputed(Discrepancy $discrepancy, ?string $remark = null): Discrepancy
    {
        if (! in_array($discrepancy->status, [Discrepancy::STATUS_PENDING, Discrepancy::STATUS_PROCESSING])) {
            throw new \Exception('仅待处理/处理中的差异单可标记为争议中');
        }

        return DB::transaction(function () use ($discrepancy, $remark) {
            $before = ['status' => $discrepancy->status, 'status_label' => $discrepancy->status_label];

            $discrepancy->update([
                'status' => Discrepancy::STATUS_DISPUTED,
                'handler_id' => Auth::id(),
                'handled_at' => now(),
                'remark' => $remark ?: $discrepancy->remark,
            ]);

            $this->audit($discrepancy, 'mark_disputed', $before, [
                'status' => Discrepancy::STATUS_DISPUTED,
                'status_label' => '争议中',
            ], $remark);

            return $discrepancy->fresh();
        });
    }

    /**
     * 拒收/残次货品回库（库存回补留痕）
     *
     * 货带回仓库、仓管确认可再售后调用；不可再售的走决策=报损。
     */
    public function restockToWarehouse(Discrepancy $discrepancy, int $warehouseId, ?string $remark = null): Discrepancy
    {
        if (! in_array($discrepancy->type, [Discrepancy::TYPE_REJECT, Discrepancy::TYPE_DEFECT])) {
            throw new \Exception('仅拒收/残次类型的差异单支持回库');
        }
        if ($discrepancy->quantity_diff <= 0) {
            throw new \Exception('差异数量必须大于 0 才可回库');
        }
        if (! in_array($discrepancy->status, [Discrepancy::STATUS_PENDING, Discrepancy::STATUS_PROCESSING, Discrepancy::STATUS_DISPUTED])) {
            throw new \Exception('当前状态不允许回库');
        }

        $skuId = $this->resolveSkuId($discrepancy);
        if (! $skuId) {
            throw new \Exception('无法确定差异单关联的 SKU，不能回库');
        }

        return DB::transaction(function () use ($discrepancy, $warehouseId, $skuId, $remark) {
            $this->inventoryService->stockIn(
                warehouse: $warehouseId,
                sku: $skuId,
                quantity: $discrepancy->quantity_diff,
                batchNo: 'RE-'.$discrepancy->discrepancy_no,
                sourceType: 'discrepancy',
                sourceId: $discrepancy->id,
                reason: "差异单 {$discrepancy->discrepancy_no} 拒收回库",
                type: InventoryLog::TYPE_REJECT_RETURN,
            );

            $before = ['status' => $discrepancy->status, 'status_label' => $discrepancy->status_label];

            $discrepancy->update([
                'status' => Discrepancy::STATUS_RESOLVED,
                'handler_id' => Auth::id(),
                'handled_at' => now(),
                'remark' => $remark ?: $discrepancy->remark,
            ]);

            $this->audit($discrepancy, 'restock', $before, [
                'status' => Discrepancy::STATUS_RESOLVED,
                'status_label' => '已处理',
                'warehouse_id' => $warehouseId,
                'quantity' => $discrepancy->quantity_diff,
            ], $remark);

            return $discrepancy->fresh();
        });
    }

    /**
     * 从差异单生成待审核损耗单（决策=报损）
     */
    private function createLossOrder(Discrepancy $discrepancy, int $responsibleParty, ?int $warehouseId): LossOrder
    {
        if (! $warehouseId) {
            throw new \Exception('报损需要指定扣减库存的仓库');
        }

        $skuId = $this->resolveSkuId($discrepancy);
        if (! $skuId) {
            throw new \Exception('无法确定差异单关联的 SKU，不能生成损耗单');
        }

        $costPrice = (int) ($discrepancy->orderItem?->price ?? $discrepancy->purchaseOrderItem?->actual_price ?? $discrepancy->purchaseOrderItem?->price ?? 0);

        // 责任方枚举映射：差异单 1供应商/2平台/3司机/4商家 → 损耗单明细 2供应商/1平台（司机/商家暂归平台承担，原始责任方在损耗原因中留痕）
        $lossParty = $responsibleParty === Discrepancy::PARTY_SUPPLIER
            ? LossOrderItem::PARTY_SUPPLIER
            : LossOrderItem::PARTY_PLATFORM;

        return $this->lossOrderService->create(
            warehouseId: $warehouseId,
            lossType: LossOrder::LOSS_OTHER,
            items: [[
                'sku_id' => $skuId,
                'quantity' => $discrepancy->quantity_diff,
                'cost_price' => $costPrice,
                'responsible_party' => $lossParty,
                'discrepancy_id' => $discrepancy->id,
                'reason' => "差异单 {$discrepancy->discrepancy_no} 报损".($discrepancy->reason ? "：{$discrepancy->reason}" : ''),
            ]],
            reason: "差异单 {$discrepancy->discrepancy_no} 决策报损自动生成",
            sourceType: 'discrepancy',
            sourceId: $discrepancy->id,
        );
    }

    /**
     * 解析 SKU ID：优先差异单自包含的 sku_id，其次从订单明细间接获取
     */
    private function resolveSkuId(Discrepancy $discrepancy): ?int
    {
        return $discrepancy->sku_id ?? $discrepancy->orderItem?->sku_id ?? $discrepancy->purchaseOrderItem?->sku_id;
    }

    /**
     * 差异单审计日志（受 audit_discrepancy 开关控制）
     */
    private function audit(Discrepancy $discrepancy, string $action, ?array $beforeData, ?array $afterData, ?string $reason = null): void
    {
        if (! setting('audit_discrepancy', true)) {
            return;
        }

        AuditLog::log(
            modelType: Discrepancy::class,
            modelId: $discrepancy->id,
            action: $action,
            beforeData: $beforeData,
            afterData: $afterData,
            reason: $reason,
            relationType: 'discrepancy',
            relationId: $discrepancy->id,
        );
    }
}
