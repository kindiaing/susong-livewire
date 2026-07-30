<?php

namespace App\Livewire\Picking;

use App\Models\PickingTask;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithToast;
use Livewire\Component;
use Livewire\WithPagination;

class PickingTaskList extends Component
{
    use WithPagination;
    use WithRowSelection, WithColumnVisibility, WithExcelExport, WithExcelImport;
    use WithToast;

    protected string $modelClass = PickingTask::class;

    public string $search = '';
    public bool $showDeleteConfirm = false;
    public ?int $deletingId = null;

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteConfirm = true;
    }

    public function delete(): void
    {
        PickingTask::findOrFail($this->deletingId)->delete();
        $this->toastSuccess('已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function closeDeleteConfirm(): void
    {
        $this->showDeleteConfirm = false;
        $this->resetErrorBag();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'task_no', 'label' => '任务编号', 'sortable' => true, 'exportable' => true],
            ['key' => 'type', 'label' => '任务类型', 'sortable' => true, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => true, 'exportable' => true],
            ['key' => 'picker_id', 'label' => '拣货员', 'sortable' => true, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return PickingTask::orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '拣货任务_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return PickingTask::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '任务编号' => 'task_no',
            '任务类型' => 'type',
        ];
    }

    public function getPageIds(): array
    {
        return PickingTask::orderBy('id', 'desc')->limit(20)->pluck('id')->toArray();
    }

    public function render()
    {
        $query = PickingTask::orderBy('id', 'desc');

        if ($this->search) {
            $query->where('task_no', 'like', "%{$this->search}%");
        }

        $items = $query->paginate(20);
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();

        return view('livewire.picking.picking-task-list', compact('items', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('拣货任务');
    }
}
