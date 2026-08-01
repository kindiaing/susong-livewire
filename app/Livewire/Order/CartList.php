<?php

namespace App\Livewire\Order;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithMoneyConversion;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Models\Cart;
use App\Models\Merchant;
use App\Models\Sku;
use Livewire\Component;
use Livewire\WithPagination;

class CartList extends Component
{
    use WithPagination;
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithMoneyConversion;
    use WithToast;

    protected string $modelClass = Cart::class;

    public string $search = '';
    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    public int $formMerchantId = 0;
    public int $formSkuId = 0;
    public int $formQuantity = 1;
    public string $formUnitPrice = '';

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'merchant_id', 'label' => '商家', 'sortable' => false, 'exportable' => true],
            ['key' => 'sku_id', 'label' => 'SKU', 'sortable' => false, 'exportable' => true],
            ['key' => 'quantity', 'label' => '数量', 'sortable' => true, 'exportable' => true],
            ['key' => 'unit_price', 'label' => '单价', 'sortable' => true, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return $this->buildQuery();
    }

    public function getExportFileName(): string
    {
        return '购物车_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return Cart::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '商家ID' => 'merchant_id',
            'SKU ID' => 'sku_id',
            '数量' => 'quantity',
            '单价(元)' => 'unit_price',
        ];
    }

    public function getImportUniqueBy(): array
    {
        return ['merchant_id', 'sku_id'];
    }

    public function getImportMoneyFields(): array
    {
        return ['unit_price'];
    }

    public function getExportRowCallback(): callable
    {
        return function (Cart $row) {
            return [
                'id' => $row->id,
                'merchant_id' => $row->merchant?->name ?? '',
                'sku_id' => $row->sku?->sku_code ?? '',
                'quantity' => $row->quantity,
                'unit_price' => money_format($row->unit_price, false),
                'created_at' => $row->created_at?->format('Y-m-d H:i:s'),
            ];
        };
    }

    public function getPageIds(): array
    {
        return $this->buildQuery()->forPage($this->page, 20)->pluck('id')->toArray();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $cart = Cart::findOrFail($id);
        $this->editingId = $id;
        $this->formQuantity = $cart->quantity;
        $this->formUnitPrice = $this->centsToYuan($cart->unit_price);
        $this->showModal = true;
    }

    public function save(): void
    {
        if ($this->editingId) {
            $validated = $this->validate([
                'formQuantity' => 'required|integer|min:1',
            ]);
            Cart::findOrFail($this->editingId)->update([
                'quantity' => $validated['formQuantity'],
            ]);
            $this->toastSuccess('购物车已更新');
        } else {
            $validated = $this->validate([
                'formMerchantId' => 'required|integer|exists:merchants,id',
                'formSkuId' => 'required|integer|exists:skus,id',
                'formQuantity' => 'required|integer|min:1',
                'formUnitPrice' => 'required|numeric|min:0',
            ]);
            Cart::create([
                'merchant_id' => $validated['formMerchantId'],
                'sku_id' => $validated['formSkuId'],
                'quantity' => $validated['formQuantity'],
                'unit_price' => money_to_cents($validated['formUnitPrice']),
            ]);
            $this->toastSuccess('购物车已创建');
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
        Cart::findOrFail($this->deletingId)->delete();
        $this->toastSuccess('购物车项已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    public function getBatchActions(): array
    {
        return [
            ['label' => '批量删除', 'method' => 'batchDelete', 'color' => 'bg-red-600 hover:bg-red-700'],
        ];
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
        $this->formMerchantId = 0;
        $this->formSkuId = 0;
        $this->formQuantity = 1;
        $this->formUnitPrice = '';
    }

    private function buildQuery()
    {
        $query = Cart::with(['merchant', 'sku'])->orderBy('id', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('merchant', function ($mq) {
                    $mq->where('name', 'like', "%{$this->search}%");
                })->orWhereHas('sku', function ($sq) {
                    $sq->where('sku_code', 'like', "%{$this->search}%");
                });
            });
        }

        return $query;
    }

    public function render()
    {
        $carts = $this->buildQuery()->paginate(setting('per_page', 10));
        $merchants = Merchant::orderBy('name')->get();
        $skus = Sku::with('product')->orderBy('sku_code')->get();

        return view('livewire.order.cart-list', [
            'carts' => $carts,
            'merchants' => $merchants,
            'skus' => $skus,
            'allColumns' => $this->getAllColumns(),
            'selectedCount' => $this->getSelectedCount(),
            'batchActions' => $this->getBatchActions(),
        ])
            ->layout('components.app-layout')
            ->title('购物车');
    }
}
