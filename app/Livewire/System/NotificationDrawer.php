<?php

namespace App\Livewire\System;

use App\Models\Notification;
use Livewire\Component;

class NotificationDrawer extends Component
{
    public string $activeTab = '全部';
    public int $unreadCount = 0;

    public function mount(): void
    {
        $this->updateUnreadCount();
    }

    public function updateUnreadCount(): void
    {
        $this->unreadCount = Notification::forUser(auth()->id())
            ->unread()
            ->count();
    }

    public function getNotificationsProperty()
    {
        $query = Notification::forUser(auth()->id())
            ->latestFirst()
            ->limit(50);

        if ($this->activeTab === '未读') {
            $query->unread();
        }

        return $query->get();
    }

    public function markRead(int $id): void
    {
        $notification = Notification::forUser(auth()->id())->find($id);
        if ($notification && $notification->is_read === 0) {
            $notification->markAsRead();
            $this->updateUnreadCount();
        }
    }

    public function markAllRead(): void
    {
        Notification::forUser(auth()->id())
            ->unread()
            ->update([
                'is_read' => 1,
                'read_at' => now(),
            ]);

        $this->updateUnreadCount();
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.system.notification-drawer');
    }
}
