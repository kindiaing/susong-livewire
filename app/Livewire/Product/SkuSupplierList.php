<?php

namespace App\Livewire\Product;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Models\SkuSupplier;
use App\Models\Supplier;
use App\Models\Sku;
use Livewire\Component;
use Livewire\WithPagination;

class SkuSupplierList extends Component
{
    use WithPagination;
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithToast;

    protected string $modelClass = SkuSupplier::class;

    public string $search = '';
    public int $filterStatus = -1;
    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    public int $formSkuId = 0;
    public int $formSupplierId = 0;
    public int $formIsDefault = 0;
    public int $formPurchasePrice = 0;
    public int $formStatus = 1;
    public int $formSort = 0;

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $skuSupplier = SkuSupplier::findOrFail($id);
        $this->editingId = $id;
        $this->formSkuId = $skuSupplier->sku_id;
        $this->formSupplierId = $skuSupplier->supplier_id;
        $this->formIsDefault = $skuSupplier->is_default;
        $this->formPurchasePrice = $skuSupplier->purchase_price;
        $this->formStatus = $skuSupplier->status;
        $this->formSort = $skuSupplier->sort;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'formSkuId' => 'required|integer|min:1',
            'formSupplierId' => 'required|integer|min:1',
            'formIsDefault' => 'required|in:0,1',
            'formPurchasePrice' => 'required|integer|min:0',
            'formStatus' => 'required|in:0,1',
            'formSort' => 'required|integer|min:0',
        ]);

        $data = [
            'sku_id' => $validated['formSkuId'],
            'supplier_id' => $validated['formSupplierId'],
            'is_default' => $validated['formIsDefault'],
            'purchase_price' => $validated['formPurchasePrice'],
            'status' => $validated['formStatus'],
            'sort' => $validated['formSort'],
        ];

        if ($this->editingId) {
            SkuSupplier::findOrFail($this->editingId)->update($data);
            $this->toastSuccess('供应商关联已更新');
        } else {
            SkuSupplier::create($data);
            $this->toastSuccess('供应商关联已创建');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteConfirm = true;
    }

    public function delete(): void
    {
        SkuSupplier::findOrFail($this->deletingId)->delete();
        $this->toastSuccess('供应商关联已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterStatus = -1;
        $this->resetPage();
        $this->clearSelection();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetErrorBag();
        $this->resetForm();
    }

    public function closeDeleteConfirm(): void
    {
        $this->showDeleteConfirm = false;
        $this->resetErrorBag();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->formSkuId = 0;
        $this->formSupplierId = 0;
        $this->formIsDefault = 0;
        $this->formPurchasePrice = 0;
        $this->formStatus = 1;
        $this->formSort = 0;
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'sku_id', 'label' => 'SKU', 'sortable' => false, 'exportable' => true],
            ['key' => 'supplier_id', 'label' => '供应商', 'sortable' => false, 'exportable' => true],
            ['key' => 'supply_price', 'label' => '供应价', 'sortable' => false, 'exportable' => true],
            ['key' => 'is_primary', 'label' => '主要供应商', 'sortable' => false, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return SkuSupplier::with(['sku', 'supplier'])
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->whereHas('sku', function ($sq) {
                        $sq->where('sku_code', 'like', "%{$this->search}%");
                    })->orWhereHas('supplier', function ($sq) {
                        $sq->where('name', 'like', "%{$this->search}%");
                    });
                });
            })
            ->when($this->filterStatus >= 0, function ($q) {
                $q->where('status', $this->filterStatus);
            })
            ->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '一品多供_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return SkuSupplier::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            'SKU ID' => 'sku_id',
            '供应商ID' => 'supplier_id',
            '采购价(分)' => 'purchase_price',
            '是否默认' => 'is_default',
            '排序' => 'sort',
            '状态' => 'status',
        ];
    }

    public function getPageIds(): array
    {
        return $this->getExportQuery()->forPage($this->getPage(), 20)->pluck('id')->toArray();
    }

    public function render()
    {
        $query = SkuSupplier::with(['sku', 'supplier'])->orderBy('id', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('sku', function ($sq) {
                    $sq->where('sku_code', 'like', "%{$this->search}%");
                })->orWhereHas('supplier', function ($sq) {
                    $sq->where('name', 'like', "%{$this->search}%");
                });
            });
        }

        if ($this->filterStatus >= 0) {
            $query->where('status', $this->filterStatus);
        }

        $skuSuppliers = $query->paginate(20);
        $suppliers = Supplier::orderBy('name')->get();
        $allColumns = $this->getAllColumns();
        $selectedCount = count($this->selectedIds);

        return view('livewire.product.sku-supplier-list', compact('skuSuppliers', 'suppliers', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('一品多供');
    }
}
