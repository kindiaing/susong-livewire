<?php

namespace App\Livewire\System;

use App\Models\WechatUser;
use Livewire\Component;
use Livewire\WithPagination;

class WechatUserList extends Component
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
        WechatUser::findOrFail($this->deletingId)->delete();
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
        $query = WechatUser::orderBy('id', 'desc');

        if ($this->search) {
            $query->where('nickname', 'like', "%{$this->search}%");
        }

        $items = $query->paginate(20);

        return view('livewire.system.wechat-user-list', compact('items'))
            ->layout('components.app-layout')
            ->title('微信用户');
    }
}
