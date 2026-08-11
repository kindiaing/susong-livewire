<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 送货单明细表
 *
 * @property int $id
 * @property int $delivery_note_id 送货单ID
 * @property int $sku_id SKU ID
 * @property string|null $sku_name SKU名称（冗余）
 * @property string|null $unit 单位
 * @property int $quantity 应送数量
 * @property int $picked_quantity 实际分货数量
 * @property int|null $order_id 来源订单ID
 * @property string|null $order_no 来源订单编号
 * @property int $status 状态：1待分货 2已分货 3差异
 */
class DeliveryNoteItem extends Model
{
    // 状态常量
    public const STATUS_PENDING = 1;
    public const STATUS_DELIVERED = 2;
    public const STATUS_DISCREPANCY = 3;

    protected $fillable = [
        'delivery_note_id',
        'sku_id',
        'sku_name',
        'unit',
        'quantity',
        'picked_quantity',
        'order_id',
        'order_no',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'delivery_note_id' => 'integer',
            'sku_id' => 'integer',
            'quantity' => 'integer',
            'picked_quantity' => 'integer',
            'order_id' => 'integer',
            'status' => 'integer',
        ];
    }

    public static function statusMap(): array
    {
        return [
            self::STATUS_PENDING => '待分货',
            self::STATUS_DELIVERED => '已分货',
            self::STATUS_DISCREPANCY => '差异',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusMap()[$this->status] ?? '未知';
    }

    /**
     * 关联送货单
     */
    public function deliveryNote()
    {
        return $this->belongsTo(DeliveryNote::class);
    }

    /**
     * 关联 SKU
     */
    public function sku()
    {
        return $this->belongsTo(Sku::class);
    }

    /**
     * 关联来源订单
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}