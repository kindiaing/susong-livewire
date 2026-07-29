<?php

namespace App\Livewire\Purchase;

use App\Models\PurchaseItem;
use Livewire\Component;
use Livewire\WithPagination;

class PurchaseItemList extends Component
{
    use WithPagination;

    public string $search = '';
    public int $filterStatus = -1;
    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    public int $formSkuId = 0;
    public int $formQuantity = 0;
    public int $formSourceType = 1;

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $item = PurchaseItem::findOrFail($id);
        $this->editingId = $id;
        $this->formSkuId = $item->sku_id;
        $this->formQuantity = $item->quantity;
        $this->formSourceType = $item->source_type;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'formSkuId' => 'required|integer|min:1',
            'formQuantity' => 'required|integer|min:1',
            'formSourceType' => 'required|in:1,2',
        ]);

        $data = [
            'sku_id' => $validated['formSkuId'],
            'quantity' => $validated['formQuantity'],
            'source_type' => $validated['formSourceType'],
        ];

        if ($this->editingId) {
            PurchaseItem::findOrFail($this->editingId)->update($data);
            $this->dispatch('toast', message: '待采项已更新', type: 'success');
        } else {
            $data['status'] = PurchaseItem::STATUS_PENDING;
            PurchaseItem::create($data);
            $this->dispatch('toast', message: '待采项已创建', type: 'success');
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
        PurchaseItem::findOrFail($this->deletingId)->delete();
        $this->dispatch('toast', message: '待采项已删除', type: 'success');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterStatus = -1;
        $this->resetPage();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->formSkuId = 0;
        $this->formQuantity = 0;
        $this->formSourceType = 1;
    }

    public function render()
    {
        $query = PurchaseItem::with('sku.product')->orderBy('id', 'desc');

        if ($this->search) {
            $query->whereHas('sku', function ($q) {
                $q->where('sku_code', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterStatus > 0) {
            $query->where('status', $this->filterStatus);
        }

        $items = $query->paginate(20);

        return view('livewire.purchase.purchase-item-list', compact('items'))
            ->layout('components.app-layout')
            ->title('待采清单');
    }
}
