<?php

namespace App\Livewire\Order;

use App\Models\Cart;
use App\Models\CartItem;
use Livewire\Component;
use Livewire\WithPagination;

class CartList extends Component
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
        CartItem::findOrFail($this->deletingId)->delete();
        $this->dispatch('toast', message: '购物车项已删除', type: 'success');
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
        $query = CartItem::with(['cart.merchant', 'sku.product'])->orderBy('id', 'desc');

        if ($this->search) {
            $query->whereHas('cart.merchant', function ($q) {
                $q->where('name', 'like', "%{$this->search}%");
            });
        }

        $cartItems = $query->paginate(20);

        return view('livewire.order.cart-list', compact('cartItems'))
            ->layout('components.app-layout')
            ->title('购物车');
    }
}
