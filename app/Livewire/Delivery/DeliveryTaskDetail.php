<?php

namespace App\Livewire\Delivery;

use App\Models\DeliveryTask;
use Livewire\Component;

class DeliveryTaskDetail extends Component
{
    public int $taskId;
    public $task;

    public function mount(int $id): void
    {
        $this->taskId = $id;
        $this->task = DeliveryTask::with([
            'deliveryRoute',
            'driver',
            'vehicle',
            'details',
            'sequences',
        ])->findOrFail($id);
    }

    public function render()
    {
        $task = $this->task;
        $statusMap = DeliveryTaskList::$statusMap;
        $statusColorMap = DeliveryTaskList::$statusColorMap;
        $batchMap = DeliveryTaskList::$batchMap;

        return view('livewire.delivery.delivery-task-detail', compact(
            'task', 'statusMap', 'statusColorMap', 'batchMap'
        ))
            ->layout('components.app-layout')
            ->title('配送任务详情 - ' . $task->task_no);
    }
}
