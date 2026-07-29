<?php

namespace App\Livewire\Finance;

use App\Models\SupplierSettlement;
use Livewire\Component;
use Livewire\WithPagination;

class SupplierSettlementList extends Component
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
        SupplierSettlement::findOrFail($this->deletingId)->delete();
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
        $query = SupplierSettlement::orderBy('id', 'desc');

        if ($this->search) {
            $query->where('settlement_no', 'like', "%{$this->search}%");
        }

        $items = $query->paginate(20);

        return view('livewire.finance.supplier-settlement-list', compact('items'))
            ->layout('components.app-layout')
            ->title('供应商结算');
    }
}
