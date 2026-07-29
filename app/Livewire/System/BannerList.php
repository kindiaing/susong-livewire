<?php

namespace App\Livewire\System;

use App\Models\Banner;
use Livewire\Component;
use Livewire\WithPagination;

class BannerList extends Component
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
        Banner::findOrFail($this->deletingId)->delete();
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
        $query = Banner::orderBy('id', 'desc');

        if ($this->search) {
            $query->where('title', 'like', "%{$this->search}%");
        }

        $items = $query->paginate(20);

        return view('livewire.system.banner-list', compact('items'))
            ->layout('components.app-layout')
            ->title('轮播广告');
    }
}
