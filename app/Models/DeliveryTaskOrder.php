<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 配送任务订单关联模型
 *
 * @property mixed $delivery_task_id 配送任务ID
 * @property mixed $order_id 订单ID
 * @property mixed $delivery_sort 配送顺序
 * @property mixed $status 状态：1待配送2已送达
 */
class DeliveryTaskOrder extends Model
{
    // 状态常量
    public const STATUS_PENDING = 1;
    public const STATUS_DELIVERED = 2;

    protected $fillable = [
        'delivery_task_id',
        'order_id',
        'delivery_sort',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'delivery_task_id' => 'integer',
            'order_id' => 'integer',
            'delivery_sort' => 'integer',
            'status' => 'integer',
        ];
    }

    /**
     * 状态映射
     */
    public static function statusMap(): array
    {
        return [
            self::STATUS_PENDING => '待配送',
            self::STATUS_DELIVERED => '已送达',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusMap()[$this->status] ?? '未知';
    }

    /**
     * 关联配送任务
     */
    public function deliveryTask()
    {
        return $this->belongsTo(DeliveryTask::class);
    }

    /**
     * 关联订单
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
