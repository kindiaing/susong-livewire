<?php

namespace App\Livewire\Product;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithMoneyConversion;
use App\Livewire\Traits\WithToast;
use App\Livewire\Traits\WithListCrud;
use App\Models\Product;
use App\Models\Sku;
use Livewire\Component;
use Livewire\WithPagination;

class SkuList extends Component
{
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithMoneyConversion;
    use WithPagination;
    use WithRowSelection;
    use WithToast;
    use WithListCrud;

    protected string $modelClass = Sku::class;

    public string $search = '';

    public int $filterStatus = -1;

    public int $filterApprovalStatus = -1;

    public int $formProductId = 0;

    public string $formSkuCode = '';

    public string $formSpecs = '';

    public string $formPurchasePrice = '';

    public string $formWholesalePrice = '';

    public string $formCostPrice = '';

    public int $formStatus = 1;

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function openEditModal(int $id): void
    {
        $sku = Sku::findOrFail($id);
        $this->editingId = $id;
        $this->formProductId = $sku->product_id;
        $this->formSkuCode = $sku->sku_code;
        $this->formSpecs = $sku->specs ? json_encode($sku->specs, JSON_UNESCAPED_UNICODE) : '';
        $this->formPurchasePrice = $this->centsToYuan($sku->purchase_price);
        $this->formWholesalePrice = $this->centsToYuan($sku->wholesale_price);
        $this->formCostPrice = $this->centsToYuan($sku->cost_price);
        $this->formStatus = $sku->status;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'formProductId' => 'required|integer|min:1|exists:products,id',
            'formSkuCode' => 'required|string|max:50',
            'formSpecs' => 'nullable|string',
            'formPurchasePrice' => 'required|numeric|min:0',
            'formWholesalePrice' => 'required|numeric|min:0',
            'formCostPrice' => 'required|numeric|min:0',
            'formStatus' => 'required|in:0,1',
        ]);

        $specs = $validated['formSpecs'] ? json_decode($validated['formSpecs'], true) : null;

        $data = [
            'product_id' => $validated['formProductId'],
            'sku_code' => $validated['formSkuCode'],
            'specs' => $specs,
            'purchase_price' => money_to_cents($validated['formPurchasePrice']),
            'wholesale_price' => money_to_cents($validated['formWholesalePrice']),
            'cost_price' => money_to_cents($validated['formCostPrice']),
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

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->formProductId = 0;
        $this->formSkuCode = '';
        $this->formSpecs = '';
        $this->formPurchasePrice = '';
        $this->formWholesalePrice = '';
        $this->formCostPrice = '';
        $this->formStatus = 1;
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'product_id', 'label' => '商品', 'sortable' => false, 'exportable' => true],
            ['key' => 'sku_code', 'label' => 'SKU编码', 'sortable' => true, 'exportable' => true],
            ['key' => 'purchase_price', 'label' => '采购价', 'sortable' => false, 'exportable' => true, 'type' => 'money'],
            ['key' => 'wholesale_price', 'label' => '批发价', 'sortable' => false, 'exportable' => true, 'type' => 'money'],
            ['key' => 'cost_price', 'label' => '成本价', 'sortable' => false, 'exportable' => true, 'type' => 'money'],
            ['key' => 'stock', 'label' => '库存', 'sortable' => true, 'exportable' => true],
            ['key' => 'approval_status', 'label' => '审核状态', 'sortable' => false, 'exportable' => true],
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
        return 'SKU_'.now()->format('Ymd_His');
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
            '采购价(元)' => 'purchase_price',
            '批发价(元)' => 'wholesale_price',
            '成本价(元)' => 'cost_price',
            '状态' => 'status',
        ];
    }

    public function getPageIds(): array
    {
        return $this->getExportQuery()->forPage($this->getPage(), 20)->pluck('id')->toArray();
    }

    public function getImportMoneyFields(): array
    {
        return ['purchase_price', 'wholesale_price', 'cost_price'];
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

        $skus = $query->paginate(setting('per_page', 10));
        $allColumns = $this->getAllColumns();
        $selectedCount = count($this->selectedIds);

        $productOptions = Product::orderBy('name')->get()->map(fn($p) => ['value' => $p->id, 'label' => $p->name])->toArray();

        return view('livewire.product.sku-list', compact('skus', 'allColumns', 'selectedCount', 'productOptions'))
            ->layout('components.app-layout')
            ->title('SKU管理');
    }
}
