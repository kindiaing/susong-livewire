<?php

namespace App\Livewire\Purchase;

use App\Models\PurchaseReturn;
use Livewire\Component;
use Livewire\WithPagination;

class PurchaseReturnList extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showDeleteConfirm = false;
    public ?int $deletingId = null;

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteConfirm = true;
    }

    public function delete(): void
    {
        PurchaseReturn::findOrFail($this->deletingId)->delete();
        $this->dispatch('toast', message: '已删除', type: 'success');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    public function render()
    {
        $query = PurchaseReturn::orderBy('id', 'desc');

        if ($this->search) {
            $query->where('return_no', 'like', "%{$this->search}%");
        }

        $items = $query->paginate(20);

        return view('livewire.purchase.purchase-return-list', compact('items'))
            ->layout('components.app-layout')
            ->title('采购退货');
    }
}
