<?php

namespace App\Livewire\Delivery;

use App\Models\Temperature;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithToast;
use Livewire\Component;
use Livewire\WithPagination;

class TemperatureList extends Component
{
    use WithPagination;
    use WithRowSelection, WithColumnVisibility, WithExcelExport;
    use WithToast;

    protected string $modelClass = Temperature::class;

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
        Temperature::findOrFail($this->deletingId)->delete();
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
            ['key' => 'delivery_id', 'label' => '配送单ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'temperature', 'label' => '温度', 'sortable' => true, 'exportable' => true],
            ['key' => 'recorded_at', 'label' => '记录时间', 'sortable' => true, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return Temperature::orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '温度记录_' . now()->format('Ymd_His');
    }

    public function getPageIds(): array
    {
        return Temperature::orderBy('id', 'desc')->limit(20)->pluck('id')->toArray();
    }

    public function render()
    {
        $query = Temperature::orderBy('id', 'desc');

        if ($this->search) {
            $query->where('temperature', 'like', "%{$this->search}%");
        }

        $items = $query->paginate(20);
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();

        return view('livewire.delivery.temperature-list', compact('items', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('温度记录');
    }
}
