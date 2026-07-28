<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * 通知/消息模型
 *
 * @property int $id
 * @property int|null $user_id 目标用户ID，NULL表示全站广播
 * @property int|null $merchant_id 目标商家ID
 * @property int $type 类型：1系统通知，2订单状态变更，3补货提醒，4库存预警，5账户变动
 * @property string $title 标题
 * @property string|null $content 内容
 * @property array|null $data 扩展数据
 * @property int $is_read 是否已读：0未读，1已读
 * @property Carbon|null $read_at 已读时间
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Notification extends Model
{
    // 类型常量
    public const TYPE_SYSTEM = 1;
    public const TYPE_ORDER = 2;
    public const TYPE_RESTOCK = 3;
    public const TYPE_INVENTORY = 4;
    public const TYPE_ACCOUNT = 5;

    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'merchant_id',
        'type',
        'title',
        'content',
        'data',
        'is_read',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'merchant_id' => 'integer',
            'type' => 'integer',
            'data' => 'array',
            'is_read' => 'integer',
            'read_at' => 'datetime',
        ];
    }

    /**
     * 类型映射：类型值 → 中文标签
     */
    public static function typeMap(): array
    {
        return [
            self::TYPE_SYSTEM => '系统通知',
            self::TYPE_ORDER => '订单状态变更',
            self::TYPE_RESTOCK => '补货提醒',
            self::TYPE_INVENTORY => '库存预警',
            self::TYPE_ACCOUNT => '账户变动',
        ];
    }

    /**
     * 类型映射：类型值 → 颜色类名
     */
    public static function typeColorMap(): array
    {
        return [
            self::TYPE_SYSTEM => 'blue',
            self::TYPE_ORDER => 'green',
            self::TYPE_RESTOCK => 'orange',
            self::TYPE_INVENTORY => 'red',
            self::TYPE_ACCOUNT => 'purple',
        ];
    }

    /**
     * 获取类型标签
     */
    public function getTypeLabelAttribute(): string
    {
        return self::typeMap()[$this->type] ?? '未知';
    }

    /**
     * 获取类型颜色
     */
    public function getTypeColorAttribute(): string
    {
        return self::typeColorMap()[$this->type] ?? 'blue';
    }

    /**
     * 获取相对时间描述
     */
    public function getTimeAgoAttribute(): string
    {
        if (!$this->created_at) {
            return '';
        }

        $diff = now()->diffInSeconds($this->created_at);

        if ($diff < 60) {
            return '刚刚';
        }
        if ($diff < 3600) {
            return intval($diff / 60) . ' 分钟前';
        }
        if ($diff < 86400) {
            return intval($diff / 3600) . ' 小时前';
        }
        if ($diff < 172800) {
            return '昨天 ' . $this->created_at->format('H:i');
        }
        if ($diff < 604800) {
            return intval($diff / 86400) . ' 天前';
        }

        return $this->created_at->format('Y-m-d H:i');
    }

    /**
     * 标记为已读
     */
    public function markAsRead(): void
    {
        if ($this->is_read === 0) {
            $this->update([
                'is_read' => 1,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * 作用域：指定用户的通知（含全站广播）
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
              ->orWhereNull('user_id');
        });
    }

    /**
     * 作用域：未读
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', 0);
    }

    /**
     * 作用域：按创建时间倒序
     */
    public function scopeLatestFirst($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
