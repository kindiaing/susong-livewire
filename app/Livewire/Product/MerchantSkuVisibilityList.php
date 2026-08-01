<?php

namespace App\Livewire\Product;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Models\Merchant;
use App\Models\MerchantSkuVisibility;
use App\Models\Sku;
use Livewire\Component;
use Livewire\WithPagination;

class MerchantSkuVisibilityList extends Component
{
    use WithColumnVisibility;
    use WithExcelExport;
    use WithPagination;
    use WithRowSelection;
    use WithToast;

    protected string $modelClass = MerchantSkuVisibility::class;

    public string $searchMerchant = '';

    public string $searchSku = '';

    public ?int $filterMerchantId = null;

    public bool $showCreateModal = false;

    public bool $showDeleteConfirm = false;

    public ?int $deletingId = null;

    // 创建表单
    public int $formMerchantId = 0;

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
        $validated = $this->validate([
            'formMerchantId' => 'required|integer|exists:merchants,id',
            'formSkuId' => 'required|integer|exists:skus,id',
            'formIsVisible' => 'required|in:0,1',
        ]);

        // upsert: 同一商家+SKU 只保留一条记录
        MerchantSkuVisibility::updateOrCreate(
            [
                'merchant_id' => $validated['formMerchantId'],
                'sku_id' => $validated['formSkuId'],
            ],
            [
                'is_visible' => $validated['formIsVisible'],
            ]
        );

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

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteConfirm = true;
    }

    public function delete(): void
    {
        MerchantSkuVisibility::findOrFail($this->deletingId)->delete();
        $this->toastSuccess('记录已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
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
        $this->searchSku = '';
        $this->filterMerchantId = null;
        $this->resetPage();
        $this->clearSelection();
    }

    private function resetForm(): void
    {
        $this->formMerchantId = 0;
        $this->formSkuId = 0;
        $this->formIsVisible = 1;
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'merchant_id', 'label' => '商家', 'sortable' => true, 'exportable' => true],
            ['key' => 'sku_id', 'label' => 'SKU', 'sortable' => true, 'exportable' => true],
            ['key' => 'is_visible', 'label' => '是否可见', 'sortable' => false, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return $this->getBaseQuery()->orderBy('id');
    }

    public function getExportFileName(): string
    {
        return 'SKU可见性配置_'.now()->format('Ymd_His');
    }

    public function getPageIds(): array
    {
        return $this->getBaseQuery()->forPage($this->getPage(), 10)->pluck('id')->toArray();
    }

    private function getBaseQuery()
    {
        return MerchantSkuVisibility::with(['merchant', 'sku'])
            ->when($this->filterMerchantId, function ($q) {
                $q->where('merchant_id', $this->filterMerchantId);
            })
            ->when($this->searchMerchant, function ($q) {
                $q->whereHas('merchant', function ($q) {
                    $q->where('name', 'like', "%{$this->searchMerchant}%");
                });
            })
            ->when($this->searchSku, function ($q) {
                $q->whereHas('sku', function ($q) {
                    $q->where('name', 'like', "%{$this->searchSku}%")
                        ->orWhere('sku_code', 'like', "%{$this->searchSku}%");
                });
            });
    }

    public function render()
    {
        $records = $this->getBaseQuery()
            ->orderByDesc('id')
            ->paginate(setting('per_page', 10));

        $merchants = Merchant::where('status', 1)->orderBy('name')->get();

        $allColumns = $this->getAllColumns();
        $selectedCount = count($this->selectedIds);

        return view('livewire.product.merchant-sku-visibility-list', compact('records', 'merchants', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('可见性配置');
    }
}
