<?php

namespace App\Livewire\Merchant;

use App\Models\MerchantAddress;
use Livewire\Component;
use Livewire\WithPagination;

class MerchantAddressList extends Component
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
        MerchantAddress::findOrFail($this->deletingId)->delete();
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
        $query = MerchantAddress::orderBy('id', 'desc');

        if ($this->search) {
            $query->where('address', 'like', "%{$this->search}%");
        }

        $items = $query->paginate(20);

        return view('livewire.merchant.merchant-address-list', compact('items'))
            ->layout('components.app-layout')
            ->title('商家地址');
    }
}
