<?php

namespace App\Livewire\Loss;

use App\Models\LossOrder;
use Livewire\Component;
use Livewire\WithPagination;

class LossOrderList extends Component
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
        LossOrder::findOrFail($this->deletingId)->delete();
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
        $query = LossOrder::orderBy('id', 'desc');

        if ($this->search) {
            $query->where('loss_no', 'like', "%{$this->search}%");
        }

        $items = $query->paginate(20);

        return view('livewire.loss.loss-order-list', compact('items'))
            ->layout('components.app-layout')
            ->title('损耗管理');
    }
}
