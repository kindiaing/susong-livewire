<?php

namespace App\Livewire\Delivery;

use App\Models\Discrepancy;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithToast;
use App\Livewire\Traits\WithListCrud;
use Livewire\Component;
use Livewire\WithPagination;

class DiscrepancyList extends Component
{
    use WithPagination;
    use WithRowSelection, WithColumnVisibility, WithExcelExport, WithExcelImport;
    use WithToast;
    use WithListCrud;

    protected string $modelClass = Discrepancy::class;

    public string $search = '';

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function delete(): void
    {
        Discrepancy::findOrFail($this->deletingId)->delete();
        $this->toastSuccess('已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'discrepancy_no', 'label' => '差异编号', 'sortable' => true, 'exportable' => true],
            ['key' => 'type', 'label' => '差异类型', 'sortable' => true, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => true, 'exportable' => true],
            ['key' => 'amount', 'label' => '差异金额', 'sortable' => true, 'exportable' => true],
            ['key' => 'reason', 'label' => '原因', 'sortable' => false, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getDefaultColumns(): array
    {
        return ['discrepancy_no', 'type', 'status', 'amount', 'reason', 'created_at'];
    }

    public function getExportQuery()
    {
        return Discrepancy::orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '差异处理_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return Discrepancy::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '差异编号' => 'discrepancy_no',
            '差异类型' => 'type',
            '原因' => 'reason',
        ];
    }

    public function getPageIds(): array
    {
        return Discrepancy::orderBy('id', 'desc')->limit(20)->pluck('id')->toArray();
    }

    public function render()
    {
        $query = Discrepancy::orderBy('id', 'desc');

        if ($this->search) {
            $query->where('discrepancy_no', 'like', "%{$this->search}%");
        }

        $items = $query->paginate(setting('per_page', 10));
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();

        return view('livewire.delivery.discrepancy-list', compact('items', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('差异处理');
    }
}
