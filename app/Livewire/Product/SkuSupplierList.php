<?php

namespace App\Livewire\Product;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithMoneyConversion;
use App\Livewire\Traits\WithToast;
use App\Livewire\Traits\WithListCrud;
use App\Models\SkuSupplier;
use App\Models\Supplier;
use App\Models\Sku;
use Livewire\Component;
use Livewire\WithPagination;

class SkuSupplierList extends Component
{
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithMoneyConversion;
    use WithPagination;
    use WithRowSelection;
    use WithToast;
    use WithListCrud;

    protected string $modelClass = SkuSupplier::class;

    public string $search = '';

    public int $filterStatus = -1;

    public int $formSkuId = 0;

    public int $formSupplierId = 0;

    public int $formIsDefault = 0;

    public string $formPurchasePrice = '';

    public int $formStatus = 1;

    public int $formSort = 0;

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function openEditModal(int $id): void
    {
        $skuSupplier = SkuSupplier::findOrFail($id);
        $this->editingId = $id;
        $this->formSkuId = $skuSupplier->sku_id;
        $this->formSupplierId = $skuSupplier->supplier_id;
        $this->formIsDefault = $skuSupplier->is_default;
        $this->formPurchasePrice = $this->centsToYuan($skuSupplier->purchase_price);
        $this->formStatus = $skuSupplier->status;
        $this->formSort = $skuSupplier->sort;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'formSkuId' => 'required|integer|min:1|exists:skus,id',
            'formSupplierId' => 'required|integer|min:1',
            'formIsDefault' => 'required|in:0,1',
            'formPurchasePrice' => 'required|numeric|min:0',
            'formStatus' => 'required|in:0,1',
            'formSort' => 'required|integer|min:0',
        ]);

        $data = [
            'sku_id' => $validated['formSkuId'],
            'supplier_id' => $validated['formSupplierId'],
            'is_default' => $validated['formIsDefault'],
            'purchase_price' => money_to_cents($validated['formPurchasePrice']),
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

        // 注意：默认供应商互斥已由 SkuSupplier Model booted() 事件自动处理，此处无需重复

        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $skuSupplier = SkuSupplier::findOrFail($id);
        $warnings = [];

        // 检查是否为默认供应商
        if ($skuSupplier->is_default) {
            // 检查该SKU是否有其他供应商可切换
            $otherCount = SkuSupplier::where('sku_id', $skuSupplier->sku_id)
                ->where('id', '!=', $id)
                ->count();
            if ($otherCount === 0) {
                $warnings[] = '这是该SKU的唯一供应商，删除后SKU将无供应商';
            } else {
                $warnings[] = '这是默认供应商，请先指定其他供应商为默认后再删除';
            }
        }

        if (count($warnings) > 0) {
            $this->deleteWarning = implode('，', $warnings) . '。';
            $this->canDelete = false;
        } else {
            $this->deleteWarning = '确定要删除该供应商关联吗？此操作不可恢复。';
            $this->canDelete = true;
        }

        $this->deletingId = $id;
        $this->showDeleteConfirm = true;
    }

    public function delete(): void
    {
        if (!$this->canDelete) {
            $this->toastWarning('无法删除，请先处理关联问题');
            return;
        }

        SkuSupplier::findOrFail($this->deletingId)->delete();
        $this->toastSuccess('供应商关联已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
        $this->deleteWarning = '';
        $this->canDelete = true;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterStatus = -1;
        $this->resetPage();
        $this->clearSelection();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->formSkuId = 0;
        $this->formSupplierId = 0;
        $this->formIsDefault = 0;
        $this->formPurchasePrice = '';
        $this->formStatus = 1;
        $this->formSort = 0;
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'sku_id', 'label' => 'SKU', 'sortable' => false, 'exportable' => true],
            ['key' => 'supplier_id', 'label' => '供应商', 'sortable' => false, 'exportable' => true],
            ['key' => 'purchase_price', 'label' => '采购价', 'sortable' => false, 'exportable' => true, 'type' => 'money'],
            ['key' => 'is_default', 'label' => '默认供应商', 'sortable' => false, 'exportable' => true],
            ['key' => 'sort', 'label' => '排序', 'sortable' => true, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => false, 'exportable' => true],
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
        return '一品多供_'.now()->format('Ymd_His');
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
            '采购价(元)' => 'purchase_price',
            '是否默认' => 'is_default',
            '排序' => 'sort',
            '状态' => 'status',
        ];
    }

    public function getPageIds(): array
    {
        return $this->getExportQuery()->forPage($this->getPage(), 20)->pluck('id')->toArray();
    }

    public function getImportMoneyFields(): array
    {
        return ['purchase_price'];
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

        $skuSuppliers = $query->paginate(setting('per_page', 10));
        $suppliers = Supplier::orderBy('name')->get();
        $allColumns = $this->getAllColumns();
        $selectedCount = count($this->selectedIds);

        $skuOptions = Sku::with('product')->orderBy('sku_code')->get()->map(fn($s) => ['value' => $s->id, 'label' => $s->sku_code . ' - ' . ($s->product?->name ?? '')])->toArray();

        return view('livewire.product.sku-supplier-list', compact('skuSuppliers', 'suppliers', 'allColumns', 'selectedCount', 'skuOptions'))
            ->layout('components.app-layout')
            ->title('一品多供');
    }
}
