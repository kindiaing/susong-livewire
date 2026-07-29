<?php

namespace App\Livewire\Picking;

use App\Models\PickingTask;
use Livewire\Component;
use Livewire\WithPagination;

class PickingTaskList extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showDeleteConfirm = false;
    public ?int $deletingId = null;

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteConfirm = true;
    }

    public function delete(): void
    {
        PickingTask::findOrFail($this->deletingId)->delete();
        $this->dispatch('toast', message: '已删除', type: 'success');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    public function render()
    {
        $query = PickingTask::orderBy('id', 'desc');

        if ($this->search) {
            $query->where('task_no', 'like', "%{$this->search}%");
        }

        $items = $query->paginate(20);

        return view('livewire.picking.picking-task-list', compact('items'))
            ->layout('components.app-layout')
            ->title('拣货任务');
    }
}
