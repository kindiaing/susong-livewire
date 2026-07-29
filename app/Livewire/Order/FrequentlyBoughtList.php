<?php

namespace App\Livewire\Order;

use App\Models\FrequentlyBought;
use Livewire\Component;
use Livewire\WithPagination;

class FrequentlyBoughtList extends Component
{
    use WithPagination;

    public string $search = '';

    public function resetFilters(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    public function render()
    {
        $query = FrequentlyBought::with(['merchant', 'sku.product'])->orderBy('buy_count', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('merchant', function ($mq) {
                    $mq->where('name', 'like', "%{$this->search}%");
                })->orWhereHas('sku', function ($sq) {
                    $sq->where('sku_code', 'like', "%{$this->search}%");
                });
            });
        }

        $items = $query->paginate(20);

        return view('livewire.order.frequently-bought-list', compact('items'))
            ->layout('components.app-layout')
            ->title('常购清单');
    }
}
