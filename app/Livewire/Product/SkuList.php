<?php

namespace App\Livewire\Product;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Models\Sku;
use Livewire\Component;
use Livewire\WithPagination;

class SkuList extends Component
{
    use WithPagination;
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithToast;

    protected string $modelClass = Sku::class;

    public string $search = '';
    public int $filterStatus = -1;
    public int $filterApprovalStatus = -1;
    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    public int $formProductId = 0;
    public string $formSkuCode = '';
    public string $formSpecs = '';
    public int $formPurchasePrice = 0;
    public int $formWholesalePrice = 0;
    public int $formCostPrice = 0;
    public int $formStatus = 1;

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
        $sku = Sku::findOrFail($id);
        $this->editingId = $id;
        $this->formProductId = $sku->product_id;
        $this->formSkuCode = $sku->sku_code;
        $this->formSpecs = $sku->specs ? json_encode($sku->specs, JSON_UNESCAPED_UNICODE) : '';
        $this->formPurchasePrice = $sku->purchase_price;
        $this->formWholesalePrice = $sku->wholesale_price;
        $this->formCostPrice = $sku->cost_price;
        $this->formStatus = $sku->status;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'formProductId' => 'required|integer|min:1',
            'formSkuCode' => 'required|string|max:50',
            'formSpecs' => 'nullable|string',
            'formPurchasePrice' => 'required|integer|min:0',
            'formWholesalePrice' => 'required|integer|min:0',
            'formCostPrice' => 'required|integer|min:0',
            'formStatus' => 'required|in:0,1',
        ]);

        $specs = $validated['formSpecs'] ? json_decode($validated['formSpecs'], true) : null;

        $data = [
            'product_id' => $validated['formProductId'],
            'sku_code' => $validated['formSkuCode'],
            'specs' => $specs,
            'purchase_price' => $validated['formPurchasePrice'],
            'wholesale_price' => $validated['formWholesalePrice'],
            'cost_price' => $validated['formCostPrice'],
            'status' => $validated['formStatus'],
        ];

        if ($this->editingId) {
            Sku::findOrFail($this->editingId)->update($data);
            $this->toastSuccess('SKU已更新');
        } else {
            Sku::create($data);
            $this->toastSuccess('SKU已创建');
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
        Sku::findOrFail($this->deletingId)->delete();
        $this->toastSuccess('SKU已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterStatus = -1;
        $this->filterApprovalStatus = -1;
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
        $this->formProductId = 0;
        $this->formSkuCode = '';
        $this->formSpecs = '';
        $this->formPurchasePrice = 0;
        $this->formWholesalePrice = 0;
        $this->formCostPrice = 0;
        $this->formStatus = 1;
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'product_id', 'label' => '商品', 'sortable' => false, 'exportable' => true],
            ['key' => 'sku_code', 'label' => 'SKU编码', 'sortable' => true, 'exportable' => true],
            ['key' => 'price', 'label' => '售价', 'sortable' => false, 'exportable' => true],
            ['key' => 'cost_price', 'label' => '成本价', 'sortable' => false, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => false, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return Sku::with('product')
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('sku_code', 'like', "%{$this->search}%")
                        ->orWhereHas('product', function ($pq) {
                            $pq->where('name', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->filterStatus >= 0, function ($q) {
                $q->where('status', $this->filterStatus);
            })
            ->when($this->filterApprovalStatus > 0, function ($q) {
                $q->where('approval_status', $this->filterApprovalStatus);
            })
            ->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return 'SKU_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return Sku::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '商品ID' => 'product_id',
            'SKU编码' => 'sku_code',
            '采购价(分)' => 'purchase_price',
            '批发价(分)' => 'wholesale_price',
            '成本价(分)' => 'cost_price',
            '状态' => 'status',
        ];
    }

    public function getPageIds(): array
    {
        return $this->getExportQuery()->forPage($this->getPage(), 20)->pluck('id')->toArray();
    }

    public function render()
    {
        $query = Sku::with('product')->orderBy('id', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('sku_code', 'like', "%{$this->search}%")
                    ->orWhereHas('product', function ($pq) {
                        $pq->where('name', 'like', "%{$this->search}%");
                    });
            });
        }

        if ($this->filterStatus >= 0) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterApprovalStatus > 0) {
            $query->where('approval_status', $this->filterApprovalStatus);
        }

        $skus = $query->paginate(20);
        $allColumns = $this->getAllColumns();
        $selectedCount = count($this->selectedIds);

        return view('livewire.product.sku-list', compact('skus', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('SKU管理');
    }
}
