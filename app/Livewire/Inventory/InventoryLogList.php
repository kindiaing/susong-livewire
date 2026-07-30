<?php

namespace App\Livewire\Inventory;

use App\Models\InventoryLog;
use App\Models\Warehouse;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use Livewire\Component;
use Livewire\WithPagination;

class InventoryLogList extends Component
{
    use WithPagination;
    use WithRowSelection, WithColumnVisibility, WithExcelExport;

    protected string $modelClass = InventoryLog::class;

    public string $search = '';
    public int $filterType = -1;
    public int $filterWarehouseId = 0;

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterType = -1;
        $this->filterWarehouseId = 0;
        $this->resetPage();
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'warehouse_id', 'label' => '仓库', 'sortable' => true, 'exportable' => true],
            ['key' => 'sku_id', 'label' => 'SKU', 'sortable' => true, 'exportable' => true],
            ['key' => 'type', 'label' => '变动类型', 'sortable' => true, 'exportable' => true],
            ['key' => 'quantity', 'label' => '变动数量', 'sortable' => true, 'exportable' => true],
            ['key' => 'before_stock', 'label' => '变动前库存', 'sortable' => true, 'exportable' => true],
            ['key' => 'after_stock', 'label' => '变动后库存', 'sortable' => true, 'exportable' => true],
            ['key' => 'reason', 'label' => '原因', 'sortable' => false, 'exportable' => true],
            ['key' => 'operator_id', 'label' => '操作人', 'sortable' => true, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return InventoryLog::with(['warehouse', 'sku.product', 'operator'])->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '库存日志_' . now()->format('Ymd_His');
    }

    public function getPageIds(): array
    {
        return InventoryLog::orderBy('id', 'desc')->limit(20)->pluck('id')->toArray();
    }

    public function render()
    {
        $query = InventoryLog::with(['warehouse', 'sku.product', 'operator'])->orderBy('id', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('reason', 'like', "%{$this->search}%")
                    ->orWhereHas('sku', fn($sq) => $sq->where('sku_code', 'like', "%{$this->search}%"));
            });
        }

        if ($this->filterType >= 0) {
            $query->where('type', $this->filterType);
        }

        if ($this->filterWarehouseId > 0) {
            $query->where('warehouse_id', $this->filterWarehouseId);
        }

        $items = $query->paginate(20);
        $warehouses = Warehouse::enabled()->orderBy('name')->get();
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();

        return view('livewire.inventory.inventory-log-list', compact('items', 'warehouses', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('库存日志');
    }
}
