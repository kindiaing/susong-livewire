<?php

namespace App\Livewire\System;

use App\Models\Promotion;
use Livewire\Component;
use Livewire\WithPagination;

class PromotionList extends Component
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
        Promotion::findOrFail($this->deletingId)->delete();
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
        $query = Promotion::orderBy('id', 'desc');

        if ($this->search) {
            $query->where('type', 'like', "%{$this->search}%");
        }

        $items = $query->paginate(20);

        return view('livewire.system.promotion-list', compact('items'))
            ->layout('components.app-layout')
            ->title('运营主推');
    }
}
