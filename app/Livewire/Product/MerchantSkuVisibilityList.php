<?php

namespace App\Livewire\Product;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Livewire\Traits\WithListCrud;
use App\Models\Merchant;
use App\Models\MerchantSkuVisibility;
use App\Models\Product;
use App\Models\Sku;
use Livewire\Component;
use Livewire\WithPagination;

class MerchantSkuVisibilityList extends Component
{
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithPagination;
    use WithRowSelection;
    use WithToast;
    use WithListCrud;

    protected string $modelClass = MerchantSkuVisibility::class;

    // 筛选
    public string $searchMerchant = '';

    public string $searchTarget = '';

    public ?int $filterMerchantId = null;

    public string $filterTargetType = '';

    // 弹窗属性名覆盖
    public bool $showCreateModal = false;

    protected function getModalPropertyName(): string
    {
        return 'showCreateModal';
    }

    // 创建表单
    public int $formMerchantId = 0;

    public string $formTargetType = 'product';

    public int $formProductId = 0;

    public int $formSkuId = 0;

    public int $formIsVisible = 1;

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function save(): void
    {
        $rules = [
            'formMerchantId' => 'required|integer|exists:merchants,id',
            'formTargetType' => 'required|in:product,sku',
            'formIsVisible' => 'required|in:0,1',
        ];

        if ($this->formTargetType === 'product') {
            $rules['formProductId'] = 'required|integer|exists:products,id';
            $validated = $this->validate($rules);
            MerchantSkuVisibility::updateOrCreate(
                [
                    'merchant_id' => $validated['formMerchantId'],
                    'target_type' => 'product',
                    'product_id' => $validated['formProductId'],
                    'sku_id' => null,
                ],
                [
                    'is_visible' => $validated['formIsVisible'],
                ]
            );
        } else {
            $rules['formSkuId'] = 'required|integer|exists:skus,id';
            $validated = $this->validate($rules);
            // 获取SKU对应的product_id
            $sku = Sku::find($validated['formSkuId']);
            MerchantSkuVisibility::updateOrCreate(
                [
                    'merchant_id' => $validated['formMerchantId'],
                    'target_type' => 'sku',
                    'product_id' => $sku->product_id,
                    'sku_id' => $validated['formSkuId'],
                ],
                [
                    'is_visible' => $validated['formIsVisible'],
                ]
            );
        }

        $this->toastSuccess('可见性配置已保存');
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function toggleVisibility(int $id): void
    {
        $record = MerchantSkuVisibility::findOrFail($id);
        $record->update(['is_visible' => $record->is_visible ? 0 : 1]);
        $this->toastSuccess($record->is_visible ? '已设为可见' : '已设为不可见');
    }

    public function delete(): void
    {
        MerchantSkuVisibility::findOrFail($this->deletingId)->delete();
        $this->toastSuccess('记录已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function batchDelete(): void
    {
        $count = count($this->selectedIds);
        if ($count === 0) {
            $this->toastWarning('请先选择要删除的记录');
            return;
        }
        MerchantSkuVisibility::whereIn('id', $this->selectedIds)->delete();
        $this->toastSuccess("已删除 {$count} 条记录");
        $this->clearSelection();
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->resetErrorBag();
        $this->resetForm();
    }

    public function closeDeleteConfirm(): void
    {
        $this->showDeleteConfirm = false;
        $this->resetErrorBag();
    }

    public function resetFilters(): void
    {
        $this->searchMerchant = '';
        $this->searchTarget = '';
        $this->filterMerchantId = null;
        $this->filterTargetType = '';
        $this->resetPage();
        $this->clearSelection();
    }

    private function resetForm(): void
    {
        $this->formMerchantId = 0;
        $this->formTargetType = 'product';
        $this->formProductId = 0;
        $this->formSkuId = 0;
        $this->formIsVisible = 1;
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'merchant_id', 'label' => '商家', 'sortable' => false, 'exportable' => true],
            ['key' => 'target_type', 'label' => '配置类型', 'sortable' => false, 'exportable' => true],
            ['key' => 'product_id', 'label' => '商品', 'sortable' => false, 'exportable' => true],
            ['key' => 'sku_id', 'label' => 'SKU', 'sortable' => false, 'exportable' => true],
            ['key' => 'is_visible', 'label' => '是否可见', 'sortable' => false, 'exportable' => true],
        ];
    }

    public function getDefaultColumns(): array
    {
        return ['merchant_id', 'target_type', 'product_id', 'sku_id', 'is_visible'];
    }

    public function getExportQuery()
    {
        return $this->getBaseQuery()->orderBy('id');
    }

    public function getExportFileName(): string
    {
        return '可见性配置_'.now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return MerchantSkuVisibility::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '商家ID' => 'merchant_id',
            '配置类型' => 'target_type',
            '商品ID' => 'product_id',
            'SKU ID' => 'sku_id',
            '是否可见' => 'is_visible',
        ];
    }

    public function getImportUniqueBy(): array
    {
        return ['merchant_id', 'target_type', 'product_id', 'sku_id'];
    }

    public function getPageIds(): array
    {
        return $this->getBaseQuery()->forPage($this->getPage(), 10)->pluck('id')->toArray();
    }

    private function getBaseQuery()
    {
        return MerchantSkuVisibility::with(['merchant', 'product', 'sku.product'])
            ->when($this->filterMerchantId, function ($q) {
                $q->where('merchant_id', $this->filterMerchantId);
            })
            ->when($this->filterTargetType, function ($q) {
                $q->where('target_type', $this->filterTargetType);
            })
            ->when($this->searchMerchant, function ($q) {
                $q->whereHas('merchant', function ($q) {
                    $q->where('name', 'like', "%{$this->searchMerchant}%");
                });
            })
            ->when($this->searchTarget, function ($q) {
                $q->where(function ($q) {
                    // 搜索商品名
                    $q->whereHas('product', function ($pq) {
                        $pq->where('name', 'like', "%{$this->searchTarget}%");
                    })
                    // 或搜索SKU编码/商品名
                    ->orWhereHas('sku', function ($sq) {
                        $sq->where('sku_code', 'like', "%{$this->searchTarget}%")
                            ->orWhereHas('product', function ($pq) {
                                $pq->where('name', 'like', "%{$this->searchTarget}%");
                            });
                    });
                });
            });
    }

    public function render()
    {
        $records = $this->getBaseQuery()
            ->orderByDesc('id')
            ->paginate(setting('per_page', 10));

        $merchants = Merchant::where('status', 1)->orderBy('name')->get();
        $products = Product::where('status', 1)->orderBy('name')->get();
        $skuOptions = Sku::with('product')->orderBy('sku_code')->get()->map(fn($s) => ['value' => $s->id, 'label' => $s->sku_code . ' - ' . ($s->product?->name ?? '')])->toArray();

        $allColumns = $this->getAllColumns();
        $selectedCount = count($this->selectedIds);

        return view('livewire.product.merchant-sku-visibility-list', compact('records', 'merchants', 'products', 'skuOptions', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('可见性配置');
    }
}
