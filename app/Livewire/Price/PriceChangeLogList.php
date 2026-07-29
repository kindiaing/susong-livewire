<?php

namespace App\Livewire\Price;

use App\Models\PriceChangeLog;
use Livewire\Component;
use Livewire\WithPagination;

class PriceChangeLogList extends Component
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
        PriceChangeLog::findOrFail($this->deletingId)->delete();
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
        $query = PriceChangeLog::orderBy('id', 'desc');

        if ($this->search) {
            $query->where('reason', 'like', "%{$this->search}%");
        }

        $items = $query->paginate(20);

        return view('livewire.price.price-change-log-list', compact('items'))
            ->layout('components.app-layout')
            ->title('改价记录');
    }
}
