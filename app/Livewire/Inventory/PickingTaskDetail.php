<?php

namespace App\Livewire\Inventory;

use App\Models\PickingTask;
use App\Models\PickingTaskItem;
use App\Models\User;
use App\Livewire\Traits\WithToast;
use Livewire\Component;

class PickingTaskDetail extends Component
{
    use WithToast;

    public int $pickingTaskId;
    public ?PickingTask $pickingTask = null;
    public string $viewMode = 'sku'; // 'sku' 或 'merchant'

    public function mount(int $id): void
    {
        $this->pickingTaskId = $id;
        $this->loadPickingTask();
    }

    private function loadPickingTask(): void
    {
        $this->pickingTask = PickingTask::with([
            'deliveryRoute',
            'warehouse',
            'picker',
            'items.sku',
            'items.order',
            'items.merchant',
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
        if (!$this->pickingTask) {
            return [];
        }

        $grouped = $this->pickingTask->items->groupBy('sku_id');

        return $grouped->map(function ($items, $skuId) {
            $first = $items->first();
            $totalQuantity = $items->sum('required_quantity');
            $pickedQuantity = $items->sum('picked_quantity');

            // 状态判定：全部已拣货→已拣货，有差异→差异，否则待拣货
            $allPicked = $items->every(fn($item) => $item->status === PickingTaskItem::STATUS_PICKED);
            $anyDiscrepancy = $items->contains(fn($item) => $item->status === PickingTaskItem::STATUS_DISCREPANCY);

            $status = PickingTaskItem::STATUS_PENDING;
            if ($allPicked) {
                $status = PickingTaskItem::STATUS_PICKED;
            } elseif ($anyDiscrepancy || ($pickedQuantity > 0 && $pickedQuantity < $totalQuantity)) {
                $status = PickingTaskItem::STATUS_DISCREPANCY;
            }

            return [
                'sku_id' => $skuId,
                'sku_name' => $first->sku?->sku_name ?? $first->sku?->sku_code ?? '',
                'unit' => $first->sku?->unit ?? '',
                'total_quantity' => $totalQuantity,
                'picked_quantity' => $pickedQuantity,
                'status' => $status,
            ];
        })->values()->all();
    }

    /**
     * 按商家分组（按商户拆分明细）
     *
     * @return array<array{merchant_id: int, merchant_name: string, items: array}>
     */
    public function getMerchantGroups(): array
    {
        if (!$this->pickingTask) {
            return [];
        }

        $grouped = $this->pickingTask->items->groupBy('merchant_id');

        return $grouped->map(function ($items, $merchantId) {
            $first = $items->first();

            return [
                'merchant_id' => $merchantId,
                'merchant_name' => $first->merchant?->name ?? '',
                'items' => $items->map(fn($item) => [
                    'sku_id' => $item->sku_id,
                    'sku_name' => $item->sku?->sku_name ?? $item->sku?->sku_code ?? '',
                    'unit' => $item->sku?->unit ?? '',
                    'required_quantity' => $item->required_quantity,
                    'picked_quantity' => $item->picked_quantity,
                    'order_no' => $item->order?->order_no ?? '',
                    'status' => $item->status,
                ])->values()->all(),
            ];
        })->values()->all();
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
        $this->toastSuccess('已分配拣货员：' . $picker->name);
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
     * 完成拣货
     */
    public function completePicking(): void
    {
        if ($this->pickingTask->status !== PickingTask::STATUS_PICKING) {
            $this->toastError('仅拣货中状态可完成');
            return;
        }

        $this->pickingTask->update([
            'status' => PickingTask::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);
        $this->loadPickingTask();
        $this->toastSuccess('拣货已完成');
    }

    // ========== 视图切换 ==========

    public function switchViewMode(string $mode): void
    {
        $this->viewMode = $mode;
    }

    public function render()
    {
        $pickingTask = $this->pickingTask;
        $skuSummary = $this->getSkuSummary();
        $merchantGroups = $this->getMerchantGroups();
        $viewMode = $this->viewMode;

        return view('livewire.inventory.picking-task-detail', compact(
            'pickingTask', 'skuSummary', 'merchantGroups', 'viewMode'
        ))
            ->layout('components.app-layout')
            ->title('拣货总单详情 - ' . ($pickingTask->task_no ?? ''));
    }
}
