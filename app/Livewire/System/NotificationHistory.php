<?php

namespace App\Livewire\System;

use App\Models\Notification;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

class NotificationHistory extends Component
{
    use WithPagination;

    public string $activeTab = '全部';

    protected $queryString = ['tab' => ['except' => '全部']];

    public function mount(string $tab = '全部'): void
    {
        $this->activeTab = $tab;
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function markRead(int $id): void
    {
        $notification = Notification::forUser(auth()->id())->find($id);
        if ($notification && $notification->is_read === 0) {
            $notification->markAsRead();
        }
    }

    public function markUnread(int $id): void
    {
        $notification = Notification::forUser(auth()->id())->find($id);
        if ($notification && $notification->is_read === 1) {
            $notification->markAsUnread();
        }
    }

    public function markAllRead(): void
    {
        Notification::forUser(auth()->id())
            ->visible()
            ->unread()
            ->update([
                'is_read' => 1,
                'read_at' => now(),
            ]);
    }

    public function deleteNotification(int $id): void
    {
        $notification = Notification::forUser(auth()->id())->find($id);
        if ($notification) {
            $notification->delete();
        }
    }

    public function restoreHidden(int $id): void
    {
        $notification = Notification::forUser(auth()->id())->find($id);
        if ($notification) {
            $notification->update(['hidden' => 0]);
        }
    }

    #[Computed]
    public function unreadCount(): int
    {
        return Notification::forUser(auth()->id())
            ->visible()
            ->unread()
            ->count();
    }

    public function render()
    {
        $query = Notification::forUser(auth()->id())->latestFirst();

        switch ($this->activeTab) {
            case '未读':
                $query->unread();
                break;
            case '已读':
                $query->where('is_read', 1);
                break;
            case '已隐藏':
                $query->where('hidden', 1);
                break;
            default:
                // 全部 = 不额外筛选
                break;
        }

        $notifications = $query->paginate(15)->withQueryString();

        return view('livewire.system.notification-history', [
            'notifications' => $notifications,
        ])
            ->layout('components.app-layout')
            ->title('历史通知');
    }
}
