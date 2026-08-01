<?php

namespace App\Livewire\Price;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Models\PriceChangeLog;
use Livewire\Component;
use Livewire\WithPagination;

class PriceChangeLogList extends Component
{
    use WithPagination;
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithToast;

    protected string $modelClass = PriceChangeLog::class;

    public string $search = '';
    public bool $showDeleteConfirm = false;
    public ?int $deletingId = null;

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function getDefaultColumns(): array
    {
        return ['sku', 'field_name', 'before_value', 'after_value', 'operator_id', 'created_at'];
    }

    public function getExportRowCallback(): callable
    {
        return function ($row) {
            return [
                'id' => $row->id,
                'sku' => $row->sku?->sku_code ?? '',
                'field_name' => $row->field_name ?? '',
                'before_value' => $row->before_value ?? '',
                'after_value' => $row->after_value ?? '',
                'operator_id' => $row->operator?->name ?? '',
                'created_at' => $row->created_at?->format('Y-m-d H:i:s'),
            ];
        };
    }

    public function getImportModelClass(): string
    {
        return PriceChangeLog::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            'SKU ID' => 'sku_id',
            '变更字段' => 'field_name',
            '修改前' => 'before_value',
            '修改后' => 'after_value',
        ];
    }

    public function getImportUniqueBy(): array
    {
        return ['id'];
    }

    public function getImportRequiredFields(): array
    {
        return ['SKU ID', '变更字段'];
    }

    public function getImportValueMap(): array
    {
        return [];
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'sku', 'label' => 'SKU', 'sortable' => false, 'exportable' => true],
            ['key' => 'field_name', 'label' => '变更字段', 'sortable' => false, 'exportable' => true],
            ['key' => 'before_value', 'label' => '修改前', 'sortable' => false, 'exportable' => true],
            ['key' => 'after_value', 'label' => '修改后', 'sortable' => false, 'exportable' => true],
            ['key' => 'operator_id', 'label' => '操作人', 'sortable' => true, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return PriceChangeLog::with('sku')
            ->when($this->search, function ($q) {
                $q->where('field_name', 'like', "%{$this->search}%");
            })->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '改价记录_' . now()->format('Ymd_His');
    }

    public function getPageIds(): array
    {
        return $this->getExportQuery()->forPage($this->page, 20)->pluck('id')->toArray();
    }

    public function closeColumnModal(): void
    {
        $this->showColumnModal = false;
    }

    public function closeExportModal(): void
    {
        $this->showExportModal = false;
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteConfirm = true;
    }

    public function delete(): void
    {
        PriceChangeLog::findOrFail($this->deletingId)->delete();
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
        $this->clearSelection();
    }

    public function render()
    {
        $query = PriceChangeLog::with('sku')->orderBy('id', 'desc');

        if ($this->search) {
            $query->where('field_name', 'like', "%{$this->search}%");
        }

        $items = $query->paginate(setting('per_page', 10));

        return view('livewire.price.price-change-log-list', [
            'items' => $items,
            'allColumns' => $this->getAllColumns(),
            'selectedCount' => $this->getSelectedCount(),
        ])
            ->layout('components.app-layout')
            ->title('改价记录');
    }
}
