<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * 配送任务明细模型
 *
 * @property int $id
 * @property int $task_id 所属配送任务ID
 * @property int|null $order_id 关联的原始订单ID
 * @property int $merchant_id 商家ID
 * @property string|null $merchant_name 商家名称（冗余）
 * @property string|null $merchant_address 配送地址（冗余）
 * @property Carbon|null $order_date 下单日期
 * @property Carbon $delivery_date 送达日期
 * @property string|null $product_summary 商品摘要
 * @property string|null $total_quantity 总数量
 * @property string|null $total_weight 总重量（kg）
 * @property string $source_type 来源类型
 * @property int|null $source_id 来源单据ID
 * @property int $status 状态
 * @property Carbon|null $delivered_at 实际送达时间
 * @property string|null $delivery_method 配送方式
 * @property array|null $delivery_photos 配送照片
 * @property string|null $delivery_remark 配送备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class DeliveryTaskDetail extends Model
{
    // 状态常量
    public const STATUS_PENDING = 1;     // 待配送
    public const STATUS_IN_PROGRESS = 2; // 配送中
    public const STATUS_DELIVERED = 3;   // 已送达
    public const STATUS_CANCELLED = 4;   // 已取消

    // 来源类型常量
    public const SOURCE_ORDER = 'order';
    public const SOURCE_DIRECT = 'direct';
    public const SOURCE_MERGE = 'merge';

    protected $fillable = [
        'task_id',
        'order_id',
        'merchant_id',
        'merchant_name',
        'merchant_address',
        'order_date',
        'delivery_date',
        'product_summary',
        'total_quantity',
        'total_weight',
        'source_type',
        'source_id',
        'status',
        'delivered_at',
        'delivery_method',
        'delivery_photos',
        'delivery_remark',
    ];

    protected function casts(): array
    {
        return [
            'task_id' => 'integer',
            'order_id' => 'integer',
            'merchant_id' => 'integer',
            'order_date' => 'date',
            'delivery_date' => 'date',
            'total_quantity' => 'decimal:2',
            'total_weight' => 'decimal:2',
            'source_id' => 'integer',
            'status' => 'integer',
            'delivered_at' => 'datetime',
            'delivery_photos' => 'array',
        ];
    }

    /**
     * 状态映射
     */
    public static function statusMap(): array
    {
        return [
            self::STATUS_PENDING => '待配送',
            self::STATUS_IN_PROGRESS => '配送中',
            self::STATUS_DELIVERED => '已送达',
            self::STATUS_CANCELLED => '已取消',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusMap()[$this->status] ?? '未知';
    }

    /**
     * 来源类型映射
     */
    public static function sourceTypeMap(): array
    {
        return [
            self::SOURCE_ORDER => '订单',
            self::SOURCE_DIRECT => '直配单',
            self::SOURCE_MERGE => '合并单',
        ];
    }

    public function getSourceTypeLabelAttribute(): string
    {
        return self::sourceTypeMap()[$this->source_type] ?? '未知';
    }

    // ========== 关联 ==========

    /**
     * 关联配送任务
     */
    public function deliveryTask()
    {
        return $this->belongsTo(DeliveryTask::class, 'task_id');
    }

    /**
     * 关联订单
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * 关联商家
     */
    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}
