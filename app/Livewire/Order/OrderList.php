<?php

namespace App\Livewire\Order;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;

class OrderList extends Component
{
    use WithPagination;

    public string $search = '';
    public int $filterStatus = -1;
    public int $filterPaymentStatus = -1;
    public bool $showDeleteConfirm = false;
    public ?int $deletingId = null;

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteConfirm = true;
    }

    public function delete(): void
    {
        Order::findOrFail($this->deletingId)->delete();
        $this->dispatch('toast', message: '订单已删除', type: 'success');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterStatus = -1;
        $this->filterPaymentStatus = -1;
        $this->resetPage();
    }

    public function render()
    {
        $query = Order::with(['merchant', 'deliveryRoute'])->orderBy('id', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('order_no', 'like', "%{$this->search}%")
                    ->orWhereHas('merchant', function ($mq) {
                        $mq->where('name', 'like', "%{$this->search}%");
                    });
            });
        }

        if ($this->filterStatus > 0) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterPaymentStatus > 0) {
            $query->where('payment_status', $this->filterPaymentStatus);
        }

        $orders = $query->paginate(20);

        return view('livewire.order.order-list', compact('orders'))
            ->layout('components.app-layout')
            ->title('客户订单');
    }
}
