<?php

namespace App\Livewire\Delivery;

use App\Models\Discrepancy;
use Livewire\Component;
use Livewire\WithPagination;

class DiscrepancyList extends Component
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
        Discrepancy::findOrFail($this->deletingId)->delete();
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
        $query = Discrepancy::orderBy('id', 'desc');

        if ($this->search) {
            $query->where('discrepancy_no', 'like', "%{$this->search}%");
        }

        $items = $query->paginate(20);

        return view('livewire.delivery.discrepancy-list', compact('items'))
            ->layout('components.app-layout')
            ->title('差异处理');
    }
}
