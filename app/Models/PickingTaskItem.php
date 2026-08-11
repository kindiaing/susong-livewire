<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 拣货任务明细模型
 *
 * @property mixed $picking_task_id 拣货任务ID
 * @property mixed $order_id 订单ID
 * @property mixed $order_item_id 订单明细ID
 * @property mixed $sku_id SKU ID
 * @property mixed $merchant_id 商家ID
 * @property mixed $required_quantity 需求数量
 * @property mixed $picked_quantity 实际拣货数量
 * @property mixed $status 状态：1待拣货2已拣货3差异
 */
class PickingTaskItem extends Model
{
    // 状态常量
    public const STATUS_PENDING = 1;
    public const STATUS_PICKED = 2;
    public const STATUS_DISCREPANCY = 3;

    protected $fillable = [
        'picking_task_id',
        'order_id',
        'order_item_id',
        'sku_id',
        'merchant_id',
        'required_quantity',
        'picked_quantity',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'picking_task_id' => 'integer',
            'order_id' => 'integer',
            'order_item_id' => 'integer',
            'sku_id' => 'integer',
            'merchant_id' => 'integer',
            'required_quantity' => 'integer',
            'picked_quantity' => 'integer',
            'status' => 'integer',
        ];
    }

    /**
     * 状态映射
     */
    public static function statusMap(): array
    {
        return [
            self::STATUS_PENDING => '待拣货',
            self::STATUS_PICKED => '已拣货',
            self::STATUS_DISCREPANCY => '差异',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusMap()[$this->status] ?? '未知';
    }

    /**
     * 关联拣货任务
     */
    public function pickingTask()
    {
        return $this->belongsTo(PickingTask::class);
    }

    /**
     * 关联订单
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * 关联订单明细
     */
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    /**
     * 关联 SKU
     */
    public function sku()
    {
        return $this->belongsTo(Sku::class);
    }

    /**
     * 关联商家
     */
    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}
