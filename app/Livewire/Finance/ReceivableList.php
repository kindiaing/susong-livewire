<?php

namespace App\Livewire\Finance;

use App\Models\Receivable;
use Livewire\Component;
use Livewire\WithPagination;

class ReceivableList extends Component
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
        Receivable::findOrFail($this->deletingId)->delete();
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
        $query = Receivable::orderBy('id', 'desc');

        if ($this->search) {
            $query->where('receivable_no', 'like', "%{$this->search}%");
        }

        $items = $query->paginate(20);

        return view('livewire.finance.receivable-list', compact('items'))
            ->layout('components.app-layout')
            ->title('应收账款');
    }
}
