<?php

namespace App\Livewire\Inventory;

use App\Livewire\Traits\WithToast;
use App\Models\AuditLog;
use App\Models\Discrepancy;
use App\Models\PickingTask;
use App\Models\PickingTaskItem;
use App\Models\User;
use App\Services\DiscrepancyService;
use App\Services\InventoryService;
use App\Services\UnitConversionService;
use Livewire\Component;

class PickingTaskDetail extends Component
{
    use WithToast;

    public int $pickingTaskId;

    public ?PickingTask $pickingTask = null;

    public string $viewMode = 'sku'; // 'sku' = SKU汇总, 'merchant' = 商家分组

    // 实拣录入弹窗（SKU 汇总行）
    public bool $showPickedModal = false;

    public ?int $editingSkuId = null;

    public string $editingSkuName = '';

    public int $editingRequiredQty = 0;

    public string $inputPickedQty = '';

    public int $inputDiffType = 1; // 差异类型：1少收2拒收3残次4其他

    public string $inputDiffReason = '';

    // 状态操作确认
    public bool $showConfirmModal = false;

    public string $confirmAction = '';

    public string $confirmTitle = '';

    public function mount(int $id): void
    {
        $this->pickingTaskId = $id;
        $this->loadPickingTask();
    }

    public function getIsSuperAdminProperty(): bool
    {
        return can_rollback_status();
    }

    private function loadPickingTask(): void
    {
        $this->pickingTask = PickingTask::with([
            'deliveryRoute',
            'warehouse',
            'picker',
            'items.sku',
            'items.order',
            'auditLogs.operator',
        ])->findOrFail($this->pickingTaskId);
    }

    // ========== 数据聚合 ==========

    /**
     * 按SKU汇总（给拣货员用）
     *
     * @return array<array{sku_id: int, sku_name: string, unit: string, total_quantity: int, picked_quantity: int, status: int}>
     */
    public function getSkuSummary(): array
    {
        if (! $this->pickingTask) {
            return [];
        }

        $grouped = $this->pickingTask->items->groupBy('sku_id');

        return $grouped->map(function ($items, $skuId) {
            $first = $items->first();
            $totalQuantity = $items->sum('required_quantity');
            $pickedQuantity = $items->sum('picked_quantity');

            // 状态判定：全部已拣货→已拣货，有差异→差异，否则待拣货
            $allPicked = $items->every(fn ($item) => $item->status === PickingTaskItem::STATUS_PICKED);
            $anyDiscrepancy = $items->contains(fn ($item) => $item->status === PickingTaskItem::STATUS_DISCREPANCY);

            $status = PickingTaskItem::STATUS_PENDING;
            if ($allPicked) {
                $status = PickingTaskItem::STATUS_PICKED;
            } elseif ($anyDiscrepancy || ($pickedQuantity > 0 && $pickedQuantity < $totalQuantity)) {
                $status = PickingTaskItem::STATUS_DISCREPANCY;
            }

            // 数量格式化：880斤 (14箱4斤)
            $baseUnitName = $first->sku?->baseUnit?->name;
            $totalDisplay = $first->sku?->base_unit_id
                ? format_quantity($skuId, $totalQuantity, $baseUnitName)
                : (string) $totalQuantity;
            $pickedDisplay = $first->sku?->base_unit_id
                ? format_quantity($skuId, $pickedQuantity, $baseUnitName)
                : (string) $pickedQuantity;

            return [
                'sku_id' => $skuId,
                'sku_name' => $first->sku?->sku_name ?? $first->sku?->sku_code ?? '',
                'unit' => $first->sku?->baseUnit?->name ?? '',
                'total_quantity' => $totalQuantity,
                'picked_quantity' => $pickedQuantity,
                'total_qty_display' => $totalDisplay,
                'picked_qty_display' => $pickedDisplay,
                'status' => $status,
            ];
        })->values()->all();
    }

    /**
     * 按商家分组汇总（给分货/配送用）
     *
     * @return array<int, array{merchant_id: int, merchant_name: string, items: array}>
     */
    public function getMerchantGroups(): array
    {
        if (! $this->pickingTask) {
            return [];
        }

        $grouped = $this->pickingTask->items->groupBy('merchant_id');

        return $grouped->map(function ($items, $merchantId) {
            $merchant = $items->first()->merchant;
            $totalRequired = $items->sum('required_quantity');
            $totalPicked = $items->sum('picked_quantity');

            $allPicked = $items->every(fn ($item) => $item->status === PickingTaskItem::STATUS_PICKED);
            $anyDiscrepancy = $items->contains(fn ($item) => $item->status === PickingTaskItem::STATUS_DISCREPANCY);

            $status = PickingTaskItem::STATUS_PENDING;
            if ($allPicked) {
                $status = PickingTaskItem::STATUS_PICKED;
            } elseif ($anyDiscrepancy) {
                $status = PickingTaskItem::STATUS_DISCREPANCY;
            }

            return [
                'merchant_id' => $merchantId,
                'merchant_name' => $merchant?->name ?? '未知商家',
                'total_quantity' => $totalRequired,
                'picked_quantity' => $totalPicked,
                'sku_count' => $items->count(),
                'status' => $status,
                'items' => $items->map(function ($item) {
                    $skuId = $item->sku_id;
                    $uName = $item->sku?->baseUnit?->name;
                    $requiredDisplay = $item->sku?->base_unit_id
                        ? format_quantity($skuId, $item->required_quantity, $uName)
                        : (string) $item->required_quantity;
                    $pickedDisplay = $item->sku?->base_unit_id
                        ? format_quantity($skuId, $item->picked_quantity, $uName)
                        : (string) $item->picked_quantity;

                    return [
                        'id' => $item->id,
                        'sku_name' => $item->sku?->sku_name ?? $item->sku?->sku_code ?? '',
                        'unit' => $item->sku?->baseUnit?->name ?? '',
                        'order_no' => $item->order?->order_no ?? '',
                        'required_quantity' => $item->required_quantity,
                        'picked_quantity' => $item->picked_quantity,
                        'required_qty_display' => $requiredDisplay,
                        'picked_qty_display' => $pickedDisplay,
                        'status' => $item->status,
                    ];
                })->values()->all(),
            ];
        })
            ->sortBy('merchant_name')
            ->values()
            ->all();
    }

    /**
     * 切换视图模式
     */
    public function switchView(string $mode): void
    {
        $this->viewMode = $mode;
    }

    // ========== 操作方法 ==========

    /**
     * 分配拣货员
     */
    public function assignPicker(int $pickerId): void
    {
        $picker = User::findOrFail($pickerId);

        $this->pickingTask->update(['picker_id' => $pickerId]);
        $this->loadPickingTask();
        $this->toastSuccess('已分配拣货员：'.$picker->name);
    }

    /**
     * 开始拣货
     */
    public function startPicking(): void
    {
        if ($this->pickingTask->status !== PickingTask::STATUS_PENDING) {
            $this->toastError('仅待分配状态可开始拣货');

            return;
        }

        $this->pickingTask->update([
            'status' => PickingTask::STATUS_PICKING,
            'started_at' => now(),
        ]);
        $this->loadPickingTask();
        $this->toastSuccess('已开始拣货');
    }

    /**
     * 确认单条拣货数量
     */
    public function confirmItem(int $itemId, int $pickedQty): void
    {
        $item = PickingTaskItem::where('picking_task_id', $this->pickingTaskId)
            ->findOrFail($itemId);

        $itemStatus = $pickedQty >= $item->required_quantity
            ? PickingTaskItem::STATUS_PICKED
            : PickingTaskItem::STATUS_DISCREPANCY;

        $item->update([
            'picked_quantity' => $pickedQty,
            'status' => $itemStatus,
        ]);

        // 检查是否所有 item 都已拣货完成（已拣货或差异）
        $allDone = PickingTaskItem::where('picking_task_id', $this->pickingTaskId)
            ->whereIn('status', [PickingTaskItem::STATUS_PICKED, PickingTaskItem::STATUS_DISCREPANCY])
            ->count() === PickingTaskItem::where('picking_task_id', $this->pickingTaskId)->count();

        if ($allDone) {
            $this->pickingTask->update([
                'status' => PickingTask::STATUS_COMPLETED,
                'completed_at' => now(),
            ]);
        }

        $this->loadPickingTask();
        $this->toastSuccess('拣货数量已确认');
    }

    /**
     * 打开 SKU 实拣录入弹窗
     */
    public function openPickedModal(int $skuId): void
    {
        if ($this->pickingTask->status !== PickingTask::STATUS_PICKING) {
            $this->toastError('仅拣货中状态可录入实拣数量');

            return;
        }

        $items = $this->pickingTask->items->where('sku_id', $skuId);
        if ($items->isEmpty()) {
            return;
        }

        $this->editingSkuId = $skuId;
        $this->editingSkuName = $items->first()->sku?->sku_name ?? $items->first()->sku?->sku_code ?? '';
        $this->editingRequiredQty = (int) $items->sum('required_quantity');
        $this->inputPickedQty = (string) (int) $items->sum('picked_quantity');
        $this->inputDiffType = Discrepancy::TYPE_SHORT;
        $this->inputDiffReason = '';
        $this->showPickedModal = true;
    }

    public function closePickedModal(): void
    {
        $this->showPickedModal = false;
        $this->editingSkuId = null;
        $this->inputPickedQty = '';
        $this->inputDiffReason = '';
    }

    /**
     * 保存 SKU 实拣数量：按订单明细顺序分摊，不足部分登记差异单
     */
    public function savePickedQuantity(): void
    {
        if ($this->pickingTask->status !== PickingTask::STATUS_PICKING || ! $this->editingSkuId) {
            $this->toastError('当前状态不允许录入');

            return;
        }

        $pickedQty = (int) $this->inputPickedQty;
        if ($pickedQty < 0 || $pickedQty > $this->editingRequiredQty) {
            $this->toastError("实拣数量须在 0 ~ {$this->editingRequiredQty} 之间");

            return;
        }

        $hasDiff = $pickedQty < $this->editingRequiredQty;

        // 严格模式（不允许差异）：直接阻断，须收发一致
        if ($hasDiff && ! setting('picking_allow_discrepancy', true)) {
            $this->toastError('当前设置为收发一致模式，不允许拣货差异。请联系运营调整订单或由管理员回退处理');

            return;
        }

        if ($hasDiff && trim($this->inputDiffReason) === '') {
            $this->toastError('实拣数量少于需求数量时，请填写差异原因');

            return;
        }

        // 允差阈值：差异率不超过阈值时只标记留痕，不生成差异单
        $thresholdPct = (int) setting('picking_discrepancy_threshold', 0);
        $diffRatePct = $this->editingRequiredQty > 0
            ? round(($this->editingRequiredQty - $pickedQty) / $this->editingRequiredQty * 100, 2)
            : 0;
        $withinThreshold = $hasDiff && $diffRatePct <= $thresholdPct;

        $items = PickingTaskItem::where('picking_task_id', $this->pickingTaskId)
            ->where('sku_id', $this->editingSkuId)
            ->orderBy('id')
            ->get();

        $discrepancyService = app(DiscrepancyService::class);
        $remaining = $pickedQty;
        $diffNos = [];

        foreach ($items as $item) {
            $alloc = min($remaining, $item->required_quantity);
            $remaining -= $alloc;

            $item->update([
                'picked_quantity' => $alloc,
                'status' => $alloc >= $item->required_quantity
                    ? PickingTaskItem::STATUS_PICKED
                    : PickingTaskItem::STATUS_DISCREPANCY,
            ]);

            // 分摊后仍有缺口且超出允差阈值：登记差异单（同明细同环节未完结时跳过，由服务层唯一性兜底）
            if (! $withinThreshold && $alloc < $item->required_quantity) {
                try {
                    $discrepancy = $discrepancyService->create([
                        'order_id' => $item->order_id,
                        'order_item_id' => $item->order_item_id,
                        'picking_task_id' => $this->pickingTaskId,
                        'sku_id' => $item->sku_id,
                        'warehouse_id' => $this->pickingTask->warehouse_id,
                        'stage' => Discrepancy::STAGE_PICKING,
                        'type' => $this->inputDiffType,
                        'expected_quantity' => $item->required_quantity,
                        'actual_quantity' => $alloc,
                        'reason' => trim($this->inputDiffReason),
                    ]);
                    $diffNos[] = $discrepancy->discrepancy_no;
                } catch (\Exception $e) {
                    // 已存在未完结差异单等情况，跳过不阻断主流程
                }
            }
        }

        // 全部明细已确认（已拣货或差异）则任务自动完成
        $total = PickingTaskItem::where('picking_task_id', $this->pickingTaskId)->count();
        $done = PickingTaskItem::where('picking_task_id', $this->pickingTaskId)
            ->whereIn('status', [PickingTaskItem::STATUS_PICKED, PickingTaskItem::STATUS_DISCREPANCY])
            ->count();
        if ($total > 0 && $done === $total) {
            $this->pickingTask->update([
                'status' => PickingTask::STATUS_COMPLETED,
                'completed_at' => now(),
            ]);
        }

        $this->closePickedModal();
        $this->loadPickingTask();

        if ($withinThreshold) {
            $this->toastSuccess("实拣已保存，差异率 {$diffRatePct}% 在允差阈值 {$thresholdPct}% 内，仅标记留痕");
        } elseif ($diffNos !== []) {
            $this->toastSuccess('实拣已保存，登记差异单 '.implode('、', $diffNos).' 待处理');
        } else {
            $this->toastSuccess('实拣数量已保存');
        }
    }

    /**
     * 完成拣货：状态变更为已完成，并按拣货数量执行出库
     */
    public function completePicking(): void
    {
        if ($this->pickingTask->status !== PickingTask::STATUS_PICKING) {
            $this->toastError('仅拣货中状态可完成');

            return;
        }

        // 执行出库：按 SKU 汇总实际拣货数量，逐一出库
        $inventoryService = app(InventoryService::class);
        $warehouseId = $this->pickingTask->warehouse_id;
        $items = PickingTaskItem::where('picking_task_id', $this->pickingTaskId)
            ->with('sku')
            ->get();

        $outErrors = [];
        foreach ($items as $item) {
            if ($item->picked_quantity <= 0) {
                continue;
            }
            try {
                $inventoryService->stockOut(
                    warehouse: $warehouseId,
                    sku: $item->sku_id,
                    quantity: $item->picked_quantity,
                    sourceType: 'picking',
                    sourceId: $this->pickingTaskId,
                    reason: '拣货出库 - '.($this->pickingTask->task_no ?? ''),
                );
            } catch (\Exception $e) {
                $outErrors[] = $item->sku?->sku_code ?? "SKU#{$item->sku_id}".': '.$e->getMessage();
            }
        }

        $this->pickingTask->update([
            'status' => PickingTask::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);
        $this->loadPickingTask();

        if (! empty($outErrors)) {
            $this->toastWarning('拣货已完成，部分出库异常：'.implode('；', $outErrors));
        } else {
            $this->toastSuccess('拣货已完成，库存已出库');
        }
    }

    // ── 超管状态回退 ──

    public function confirmRollback(string $toStatus): void
    {
        $statusLabels = PickingTask::statusMap();
        $this->confirmAction = 'rollback_'.$toStatus;
        $this->confirmTitle = "确认回退到【{$statusLabels[$toStatus]}】状态？";
        $this->showConfirmModal = true;
    }

    public function closeConfirmModal(): void
    {
        $this->showConfirmModal = false;
        $this->resetErrorBag();
    }

    public function executeConfirm(): void
    {
        try {
            if (str_starts_with($this->confirmAction, 'rollback_')) {
                abort_unless(can_rollback_status(), 403, '仅超级管理员可执行状态回退');

                $toStatus = (int) substr($this->confirmAction, 9);
                $oldStatus = $this->pickingTask->status;
                $this->pickingTask->update(['status' => $toStatus]);

                AuditLog::log(
                    modelType: PickingTask::class,
                    modelId: $this->pickingTaskId,
                    action: 'rollback',
                    beforeData: ['status' => $oldStatus],
                    afterData: ['status' => $toStatus],
                );

                $this->loadPickingTask();
                $this->showConfirmModal = false;
                $this->toastSuccess('状态已回退');

                return;
            }
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function render()
    {
        $pickingTask = $this->pickingTask;
        $skuSummary = $this->getSkuSummary();
        $merchantGroups = $this->getMerchantGroups();
        $isSuperAdmin = $this->isSuperAdmin;

        // 本任务产生的差异单（就近可视，处理进度一目了然）
        $discrepancies = Discrepancy::where('picking_task_id', $this->pickingTaskId)
            ->with('sku')
            ->latest('id')
            ->get();

        $auditLogs = $this->pickingTask->auditLogs()->orderByDesc('id')->take(50)->get();

        return view('livewire.inventory.picking-task-detail', compact(
            'pickingTask', 'skuSummary', 'merchantGroups', 'isSuperAdmin', 'discrepancies', 'auditLogs'
        ))
            ->layout('components.app-layout')
            ->title('拣货总单详情 - '.($pickingTask->task_no ?? ''));
    }
}
