<?php

namespace App\Livewire\Delivery;

use App\Models\DeliveryTask;
use App\Models\DeliveryTaskSequence;
use App\Livewire\Traits\WithToast;
use Livewire\Component;

class DeliveryTaskDetail extends Component
{
    use WithToast;

    public int $taskId;
    public $task;

    public static array $statusMap = [
        1 => '待配送', 2 => '已分配', 3 => '配送中',
        4 => '暂停', 5 => '已完成', 6 => '已作废',
    ];

    public static array $statusColorMap = [
        1 => 'yellow', 2 => 'blue', 3 => 'orange',
        4 => 'gray', 5 => 'green', 6 => 'red',
    ];

    public static array $batchMap = [
        1 => '上午', 2 => '下午',
    ];

    public function mount(int $id): void
    {
        $this->taskId = $id;
        $this->loadTask();
    }

    private function loadTask(): void
    {
        $this->task = DeliveryTask::with([
            'deliveryRoute',
            'driver',
            'vehicle',
            'details.order',
            'sequences' => fn($q) => $q->orderBy('sequence_no'),
        ])->findOrFail($this->taskId);
    }

    // ========== 状态操作 ==========

    public function startDelivery(): void
    {
        $task = DeliveryTask::findOrFail($this->taskId);
        if (!in_array($task->status, [DeliveryTask::STATUS_PENDING, DeliveryTask::STATUS_ASSIGNED])) {
            $this->toastError('仅待配送/已分配任务可开始');
            return;
        }
        $task->update([
            'status' => DeliveryTask::STATUS_IN_PROGRESS,
            'actual_start_time' => now(),
        ]);
        $this->loadTask();
        $this->toastSuccess('已开始配送');
    }

    public function pauseDelivery(): void
    {
        $task = DeliveryTask::findOrFail($this->taskId);
        if ($task->status !== DeliveryTask::STATUS_IN_PROGRESS) {
            $this->toastError('仅配送中任务可暂停');
            return;
        }
        $task->update(['status' => DeliveryTask::STATUS_PAUSED]);
        $this->loadTask();
        $this->toastSuccess('配送已暂停');
    }

    public function resumeDelivery(): void
    {
        $task = DeliveryTask::findOrFail($this->taskId);
        if ($task->status !== DeliveryTask::STATUS_PAUSED) {
            $this->toastError('仅暂停任务可继续');
            return;
        }
        $task->update(['status' => DeliveryTask::STATUS_IN_PROGRESS]);
        $this->loadTask();
        $this->toastSuccess('配送已继续');
    }

    public function completeDelivery(): void
    {
        $task = DeliveryTask::findOrFail($this->taskId);
        if ($task->status !== DeliveryTask::STATUS_IN_PROGRESS) {
            $this->toastError('仅配送中任务可完成');
            return;
        }
        $task->update([
            'status' => DeliveryTask::STATUS_COMPLETED,
            'actual_complete_time' => now(),
        ]);
        $this->loadTask();
        $this->toastSuccess('配送已完成');
    }

    public function cancelDelivery(): void
    {
        $task = DeliveryTask::findOrFail($this->taskId);
        if (!$task->canTransitionTo(DeliveryTask::STATUS_CANCELLED)) {
            $this->toastError('当前状态不允许作废');
            return;
        }
        $task->update(['status' => DeliveryTask::STATUS_CANCELLED]);
        $this->loadTask();
        $this->toastSuccess('配送任务已作废');
    }

    // ========== 顺序表操作 ==========

    public function toggleUrgent(int $sequenceId): void
    {
        $seq = DeliveryTaskSequence::findOrFail($sequenceId);
        if ($seq->is_urgent) {
            $seq->unmarkUrgent();
            $this->toastSuccess('已取消加急');
        } else {
            $seq->markUrgent();
            $this->toastSuccess('已标记加急');
        }
        $this->loadTask();
    }

    public function toggleImportant(int $sequenceId): void
    {
        $seq = DeliveryTaskSequence::findOrFail($sequenceId);
        if ($seq->is_important) {
            $seq->unmarkImportant();
            $this->toastSuccess('已取消重要标记');
        } else {
            $seq->markImportant();
            $this->toastSuccess('已标记重要');
        }
        $this->loadTask();
    }

    public function render()
    {
        $task = $this->task;
        $statusMap = self::$statusMap;
        $statusColorMap = self::$statusColorMap;
        $batchMap = self::$batchMap;

        return view('livewire.delivery.delivery-task-detail', compact(
            'task', 'statusMap', 'statusColorMap', 'batchMap'
        ))
            ->layout('components.app-layout')
            ->title('配送任务详情 - ' . $task->task_no);
    }
}
