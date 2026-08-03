<?php

namespace App\Livewire\Inventory;

use App\Models\Inventory;
use App\Models\Warehouse;
use App\Models\Sku;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithToast;
use App\Livewire\Traits\WithListCrud;
use Livewire\Component;
use Livewire\WithPagination;

class InventoryList extends Component
{
    use WithPagination;
    use WithRowSelection, WithColumnVisibility, WithExcelExport, WithExcelImport;
    use WithToast;
    use WithListCrud;

    protected string $modelClass = Inventory::class;

    public string $search = '';
    public int $filterWarehouseId = 0;

    public int $formWarehouseId = 0;
    public int $formSkuId = 0;
    public int $formTotalStock = 0;
    public int $formLockedStock = 0;
    public int $formAvailableStock = 0;
    public string $formBatchNo = '';
    public string $formExpiryDate = '';
    public int $formWarningValue = 0;

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function openEditModal(int $id): void
    {
        $item = Inventory::findOrFail($id);
        $this->editingId = $id;
        $this->formWarehouseId = $item->warehouse_id;
        $this->formSkuId = $item->sku_id;
        $this->formTotalStock = $item->total_stock;
        $this->formLockedStock = $item->locked_stock;
        $this->formAvailableStock = $item->available_stock;
        $this->formBatchNo = $item->batch_no ?? '';
        $this->formExpiryDate = $item->expiry_date ? $item->expiry_date->format('Y-m-d') : '';
        $this->formWarningValue = $item->warning_value;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'formWarehouseId' => 'required|integer|exists:warehouses,id',
            'formSkuId' => 'required|integer|exists:skus,id',
            'formTotalStock' => 'required|integer|min:0',
            'formLockedStock' => 'required|integer|min:0',
            'formAvailableStock' => 'required|integer|min:0',
            'formBatchNo' => 'nullable|string|max:50',
            'formExpiryDate' => 'nullable|date',
            'formWarningValue' => 'required|integer|min:0',
        ]);

        $data = [
            'warehouse_id' => $validated['formWarehouseId'],
            'sku_id' => $validated['formSkuId'],
            'total_stock' => $validated['formTotalStock'],
            'locked_stock' => $validated['formLockedStock'],
            'available_stock' => $validated['formAvailableStock'],
            'batch_no' => $validated['formBatchNo'] ?: null,
            'expiry_date' => $validated['formExpiryDate'] ?: null,
            'warning_value' => $validated['formWarningValue'],
        ];

        if ($this->editingId) {
            $item = Inventory::findOrFail($this->editingId);
            $item->update($data);
            $this->toastSuccess('库存已更新');
        } else {
            Inventory::create($data);
            $this->toastSuccess('库存已创建');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(): void
    {
        $item = Inventory::findOrFail($this->deletingId);
        $item->delete();
        $this->toastSuccess('库存记录已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterWarehouseId = 0;
        $this->resetPage();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->formWarehouseId = 0;
        $this->formSkuId = 0;
        $this->formTotalStock = 0;
        $this->formLockedStock = 0;
        $this->formAvailableStock = 0;
        $this->formBatchNo = '';
        $this->formExpiryDate = '';
        $this->formWarningValue = 0;
    }

    public function getDefaultColumns(): array
    {
        return ['warehouse_id', 'sku_id', 'total_stock', 'locked_stock', 'available_stock', 'batch_no', 'expiry_date', 'warning_value', 'created_at'];
    }

    public function getExportRowCallback(): callable
    {
        return function ($row) {
            return [
                'id' => $row->id,
                'warehouse_id' => $row->warehouse?->name ?? '',
                'sku_id' => $row->sku?->sku_code ?? '',
                'total_stock' => $row->total_stock,
                'locked_stock' => $row->locked_stock,
                'available_stock' => $row->available_stock,
                'batch_no' => $row->batch_no ?? '',
                'expiry_date' => $row->expiry_date ? $row->expiry_date->format('Y-m-d') : '',
                'warning_value' => $row->warning_value,
                'created_at' => $row->created_at?->format('Y-m-d H:i:s'),
            ];
        };
    }

    public function getImportUniqueBy(): array
    {
        return ['sku_id'];
    }

    public function getImportRequiredFields(): array
    {
        return ['仓库ID', 'SKU ID'];
    }

    public function getImportValueMap(): array
    {
        return [];
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'warehouse_id', 'label' => '仓库', 'sortable' => true, 'exportable' => true],
            ['key' => 'sku_id', 'label' => 'SKU', 'sortable' => true, 'exportable' => true],
            ['key' => 'total_stock', 'label' => '总库存', 'sortable' => true, 'exportable' => true],
            ['key' => 'locked_stock', 'label' => '锁定库存', 'sortable' => true, 'exportable' => true],
            ['key' => 'available_stock', 'label' => '可用库存', 'sortable' => true, 'exportable' => true],
            ['key' => 'batch_no', 'label' => '批次号', 'sortable' => true, 'exportable' => true],
            ['key' => 'expiry_date', 'label' => '有效期', 'sortable' => true, 'exportable' => true],
            ['key' => 'warning_value', 'label' => '预警值', 'sortable' => true, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return Inventory::with(['warehouse', 'sku.product'])->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '实时库存_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return Inventory::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '仓库ID' => 'warehouse_id',
            'SKU ID' => 'sku_id',
            '总库存' => 'total_stock',
            '锁定库存' => 'locked_stock',
            '可用库存' => 'available_stock',
            '批次号' => 'batch_no',
            '预警值' => 'warning_value',
        ];
    }

    public function getPageIds(): array
    {
        return Inventory::orderBy('id', 'desc')->limit(20)->pluck('id')->toArray();
    }

    public function render()
    {
        $query = Inventory::with(['warehouse', 'sku.product'])->orderBy('id', 'desc');

        if ($this->search) {
            $query->whereHas('sku', function ($q) {
                $q->where('sku_code', 'like', "%{$this->search}%")
                    ->orWhereHas('product', fn($pq) => $pq->where('name', 'like', "%{$this->search}%"));
            });
        }

        if ($this->filterWarehouseId > 0) {
            $query->where('warehouse_id', $this->filterWarehouseId);
        }

        $items = $query->paginate(setting('per_page', 10));
        $warehouses = Warehouse::enabled()->orderBy('name')->get();
        $skus = Sku::with('product')->orderBy('sku_code')->get();
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();

        return view('livewire.inventory.inventory-list', compact('items', 'warehouses', 'skus', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('实时库存');
    }
}
