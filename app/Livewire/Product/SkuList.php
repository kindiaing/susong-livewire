<?php

namespace App\Livewire\Product;

use App\Models\Sku;
use Livewire\Component;
use Livewire\WithPagination;

class SkuList extends Component
{
    use WithPagination;

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
            $this->dispatch('toast', message: 'SKU已更新', type: 'success');
        } else {
            Sku::create($data);
            $this->dispatch('toast', message: 'SKU已创建', type: 'success');
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
        $this->dispatch('toast', message: 'SKU已删除', type: 'success');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterStatus = -1;
        $this->filterApprovalStatus = -1;
        $this->resetPage();
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

        return view('livewire.product.sku-list', compact('skus'))
            ->layout('components.app-layout')
            ->title('SKU管理');
    }
}
