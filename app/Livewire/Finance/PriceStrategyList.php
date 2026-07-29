<?php

namespace App\Livewire\Finance;

use App\Models\PriceStrategy;
use Livewire\Component;
use Livewire\WithPagination;

class PriceStrategyList extends Component
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
        PriceStrategy::findOrFail($this->deletingId)->delete();
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
        $query = PriceStrategy::orderBy('id', 'desc');

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
        }

        $items = $query->paginate(20);

        return view('livewire.finance.price-strategy-list', compact('items'))
            ->layout('components.app-layout')
            ->title('价格策略');
    }
}
