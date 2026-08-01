<?php

namespace App\Livewire\Finance;

use App\Models\PriceApportionment;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithToast;
use Livewire\Component;
use Livewire\WithPagination;

class PriceApportionmentList extends Component
{
    use WithPagination;
    use WithRowSelection, WithColumnVisibility, WithExcelExport, WithExcelImport;
    use WithToast;

    protected string $modelClass = PriceApportionment::class;

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
        PriceApportionment::findOrFail($this->deletingId)->delete();
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
            ['key' => 'type', 'label' => '均摊类型', 'sortable' => true, 'exportable' => true],
            ['key' => 'amount', 'label' => '均摊金额', 'sortable' => true, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => true, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return PriceApportionment::orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '费用均摊_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return PriceApportionment::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '均摊类型' => 'type',
            '均摊金额' => 'amount',
        ];
    }

    public function getPageIds(): array
    {
        return PriceApportionment::orderBy('id', 'desc')->limit(20)->pluck('id')->toArray();
    }

    public function render()
    {
        $query = PriceApportionment::orderBy('id', 'desc');

        if ($this->search) {
            $query->where('amount', 'like', "%{$this->search}%");
        }

        $items = $query->paginate(setting('per_page', 10));
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();

        return view('livewire.finance.price-apportionment-list', compact('items', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('费用均摊');
    }
}
