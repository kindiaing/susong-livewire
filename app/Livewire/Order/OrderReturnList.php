<?php

namespace App\Livewire\Order;

use App\Models\OrderReturn;
use Livewire\Component;
use Livewire\WithPagination;

class OrderReturnList extends Component
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
        OrderReturn::findOrFail($this->deletingId)->delete();
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
        $query = OrderReturn::orderBy('id', 'desc');

        if ($this->search) {
            $query->where('return_no', 'like', "%{$this->search}%");
        }

        $items = $query->paginate(20);

        return view('livewire.order.order-return-list', compact('items'))
            ->layout('components.app-layout')
            ->title('售后退货');
    }
}
