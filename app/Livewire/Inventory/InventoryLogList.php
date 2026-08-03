<?php

namespace App\Livewire\Inventory;

use App\Models\InventoryLog;
use App\Models\Warehouse;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithToast;
use App\Livewire\Traits\WithListCrud;
use Livewire\Component;
use Livewire\WithPagination;

class InventoryLogList extends Component
{
    use WithPagination;
    use WithRowSelection, WithColumnVisibility, WithExcelExport, WithExcelImport;
    use WithToast;
    use WithListCrud;

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

    public function getDefaultColumns(): array
    {
        return ['warehouse_id', 'sku_id', 'type', 'quantity', 'before_stock', 'after_stock', 'reason', 'operator_id', 'created_at'];
    }

    public function getExportRowCallback(): callable
    {
        return function ($row) {
            return [
                'id' => $row->id,
                'warehouse_id' => $row->warehouse?->name ?? '',
                'sku_id' => $row->sku?->sku_code ?? '',
                'type' => $row->type,
                'quantity' => $row->quantity,
                'before_stock' => $row->before_stock,
                'after_stock' => $row->after_stock,
                'reason' => $row->reason ?? '',
                'operator_id' => $row->operator?->name ?? '',
                'created_at' => $row->created_at?->format('Y-m-d H:i:s'),
            ];
        };
    }

    public function getImportModelClass(): string
    {
        return InventoryLog::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '仓库ID' => 'warehouse_id',
            'SKU ID' => 'sku_id',
            '变动类型' => 'type',
            '变动数量' => 'quantity',
            '变动前库存' => 'before_stock',
            '变动后库存' => 'after_stock',
            '原因' => 'reason',
        ];
    }

    public function getImportUniqueBy(): array
    {
        return ['id'];
    }

    public function getImportRequiredFields(): array
    {
        return ['仓库ID', 'SKU ID', '变动类型'];
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

        $items = $query->paginate(setting('per_page', 10));
        $warehouses = Warehouse::enabled()->orderBy('name')->get();
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();

        return view('livewire.inventory.inventory-log-list', compact('items', 'warehouses', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('库存日志');
    }
}
