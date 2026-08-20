<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * 通知创建事件 — 通过 Pusher 实时推送
 *
 * 实现 ShouldBroadcastNow：同步广播（不走队列），适合通知这种即时性要求高的场景。
 * 广播到两个频道：
 * 1. private-notifications.{userId}  — 指定用户的私有频道
 * 2. notifications                   — 全站公共频道（user_id=null 时使用）
 */
class NotificationCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Notification $notification
    ) {}

    /**
     * 广播频道
     */
    public function broadcastOn(): array
    {
        $channels = [];

        if ($this->notification->user_id) {
            // 指定用户 → 私有频道
            $channels[] = new PrivateChannel('notifications.' . $this->notification->user_id);
        }

        // 全站广播（user_id=null）→ 公共频道
        if ($this->notification->user_id === null) {
            $channels[] = new Channel('notifications');
        }

        // 商家通知 → 商家频道
        if ($this->notification->merchant_id) {
            $channels[] = new PrivateChannel('merchant-notifications.' . $this->notification->merchant_id);
        }

        return $channels;
    }

    /**
     * 广播事件名
     */
    public function broadcastAs(): string
    {
        return 'notification.created';
    }

    /**
     * 广播数据
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->notification->id,
            'type' => $this->notification->type,
            'type_label' => $this->notification->type_label,
            'type_color' => $this->notification->type_color,
            'title' => $this->notification->title,
            'content' => $this->notification->content,
            'data' => $this->notification->data,
            'is_read' => $this->notification->is_read,
            'created_at' => $this->notification->created_at?->toIso8601String(),
            'time_ago' => $this->notification->time_ago,
        ];
    }
}
