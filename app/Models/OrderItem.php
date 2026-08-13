<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * 订单明细模型
 *
 * @property int $id
 * @property int $order_id 订单ID
 * @property int $sku_id SKU ID
 * @property string $product_name 商品名称快照
 * @property array|null $sku_specs 规格快照
 * @property int $quantity 下单数量（base_unit 最小单位）
 * @property int|null $unit_id 下单时选择的单位ID
 * @property int $unit_quantity 下单时选择的单位数量
 * @property int $price 下单单价（厘）
 * @property int $actual_quantity 实际称重数量
 * @property int $actual_price 实际称重单价（厘）
 * @property int $subtotal 小计金额（厘）
 * @property int $actual_subtotal 实际小计金额（厘）
 * @property int $strategy_price 改价/促销单价（厘）
 * @property int $strategy_amount 改价/促销金额（厘）
 * @property int|null $price_strategy_id 价格策略ID
 * @property int|null $price_strategy_item_id 价格策略明细ID
 * @property int $discrepancy_amount 差异金额（厘）
 * @property int $status 状态：1正常，2待审核，3已调整
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class OrderItem extends Model
{
    public const STATUS_NORMAL = 1;
    public const STATUS_PENDING_REVIEW = 2;
    public const STATUS_ADJUSTED = 3;

    protected $fillable = [
        'order_id',
        'sku_id',
        'product_name',
        'sku_specs',
        'quantity',
        'unit_id',
        'unit_quantity',
        'price',
        'actual_quantity',
        'actual_price',
        'subtotal',
        'actual_subtotal',
        'strategy_price',
        'strategy_amount',
        'price_strategy_id',
        'price_strategy_item_id',
        'discrepancy_amount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'order_id' => 'integer',
            'sku_id' => 'integer',
            'sku_specs' => 'array',
            'quantity' => 'integer',
            'unit_id' => 'integer',
            'unit_quantity' => 'integer',
            'price' => 'integer',
            'actual_quantity' => 'integer',
            'actual_price' => 'integer',
            'subtotal' => 'integer',
            'actual_subtotal' => 'integer',
            'strategy_price' => 'integer',
            'strategy_amount' => 'integer',
            'price_strategy_id' => 'integer',
            'price_strategy_item_id' => 'integer',
            'discrepancy_amount' => 'integer',
            'status' => 'integer',
        ];
    }

    public static function statusMap(): array
    {
        return [
            self::STATUS_NORMAL => '正常',
            self::STATUS_PENDING_REVIEW => '待审核',
            self::STATUS_ADJUSTED => '已调整',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function sku()
    {
        return $this->belongsTo(Sku::class);
    }

    /**
     * 关联下单单位
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * 关联价格策略
     */
    public function priceStrategy()
    {
        return $this->belongsTo(PriceStrategy::class, 'price_strategy_id');
    }

    /**
     * 关联价格策略明细
     */
    public function priceStrategyItem()
    {
        return $this->belongsTo(PriceStrategyItem::class, 'price_strategy_item_id');
    }

    /**
     * 作用域：正常
     */
    public function scopeNormal($query)
    {
        return $query->where('status', self::STATUS_NORMAL);
    }

    /**
     * 作用域：待审核
     */
    public function scopePendingReview($query)
    {
        return $query->where('status', self::STATUS_PENDING_REVIEW);
    }
}
