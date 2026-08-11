<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 送货单主表（按商户维度，司机分货依据）
 *
 * @property int $id
 * @property string $note_no 送货单编号
 * @property int $task_id 所属配送任务ID
 * @property int $merchant_id 商家ID
 * @property string|null $merchant_name 商家名称（冗余）
 * @property string|null $merchant_address 配送地址（冗余）
 * @property \Carbon\Carbon $delivery_date 送达日期
 * @property array|null $order_ids 关联订单ID数组
 * @property array|null $order_nos 关联订单编号数组
 * @property string|null $product_summary 商品摘要
 * @property int $total_quantity 应送总数量
 * @property float|null $total_weight 总重量（kg）
 * @property int $status 状态：1待分货 2已分货 3已签收 4已取消
 * @property \Carbon\Carbon|null $delivered_at 实际分货时间
 * @property string|null $delivery_method 确认方式
 * @property string|null $remark 备注
 */
class DeliveryNote extends Model
{
    use SoftDeletes;

    // 状态常量
    public const STATUS_PENDING = 1;
    public const STATUS_DELIVERED = 2;
    public const STATUS_SIGNED = 3;
    public const STATUS_CANCELLED = 4;

    protected $fillable = [
        'note_no',
        'task_id',
        'merchant_id',
        'merchant_name',
        'merchant_address',
        'delivery_date',
        'order_ids',
        'order_nos',
        'product_summary',
        'total_quantity',
        'total_weight',
        'status',
        'delivered_at',
        'delivery_method',
        'remark',
    ];

    protected function casts(): array
    {
        return [
            'task_id' => 'integer',
            'merchant_id' => 'integer',
            'delivery_date' => 'date',
            'order_ids' => 'array',
            'order_nos' => 'array',
            'total_quantity' => 'integer',
            'total_weight' => 'decimal:2',
            'status' => 'integer',
            'delivered_at' => 'datetime',
        ];
    }

    public static function statusMap(): array
    {
        return [
            self::STATUS_PENDING => '待分货',
            self::STATUS_DELIVERED => '已分货',
            self::STATUS_SIGNED => '已签收',
            self::STATUS_CANCELLED => '已取消',
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
        return $this->belongsTo(DeliveryTask::class, 'task_id');
    }

    /**
     * 关联商家
     */
    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    /**
     * 关联送货单明细
     */
    public function items()
    {
        return $this->hasMany(DeliveryNoteItem::class);
    }
}