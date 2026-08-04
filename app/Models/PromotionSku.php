<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * 活动商品明细模型
 *
 * @property int $id
 * @property int $promotion_id 促销活动ID
 * @property int $sku_id SKU ID
 * @property int $price_type 定价方式：1固定价，2折扣率
 * @property int $fixed_price 促销固定单价（厘）
 * @property int $discount_rate 折扣率（万分比）
 * @property int $second_price_type 第二件定价方式：1无，2固定价，3折扣率
 * @property int $second_fixed_price 第二件固定单价（厘）
 * @property int $second_discount_rate 第二件折扣率（万分比）
 * @property int $max_quantity 限购数量
 * @property int $max_per_customer 每人限购
 * @property int $stock_limit 活动库存限量
 * @property int $sort 排序
 * @property int $status 状态：0禁用，1启用
 */
class PromotionSku extends Model
{
    public const PRICE_TYPE_FIXED = 1;
    public const PRICE_TYPE_DISCOUNT = 2;

    public const SECOND_PRICE_NONE = 1;
    public const SECOND_PRICE_FIXED = 2;
    public const SECOND_PRICE_DISCOUNT = 3;

    public const STATUS_DISABLED = 0;
    public const STATUS_ENABLED = 1;

    protected $fillable = [
        'promotion_id',
        'sku_id',
        'price_type',
        'fixed_price',
        'discount_rate',
        'second_price_type',
        'second_fixed_price',
        'second_discount_rate',
        'max_quantity',
        'max_per_customer',
        'stock_limit',
        'sort',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'promotion_id' => 'integer',
            'sku_id' => 'integer',
            'price_type' => 'integer',
            'fixed_price' => 'integer',
            'discount_rate' => 'integer',
            'second_price_type' => 'integer',
            'second_fixed_price' => 'integer',
            'second_discount_rate' => 'integer',
            'max_quantity' => 'integer',
            'max_per_customer' => 'integer',
            'stock_limit' => 'integer',
            'sort' => 'integer',
            'status' => 'integer',
        ];
    }

    public static function priceTypeMap(): array
    {
        return [
            self::PRICE_TYPE_FIXED => '固定价',
            self::PRICE_TYPE_DISCOUNT => '折扣率',
        ];
    }

    public static function secondPriceTypeMap(): array
    {
        return [
            self::SECOND_PRICE_NONE => '无',
            self::SECOND_PRICE_FIXED => '固定价',
            self::SECOND_PRICE_DISCOUNT => '折扣率',
        ];
    }

    public static function statusMap(): array
    {
        return [
            self::STATUS_ENABLED => '启用',
            self::STATUS_DISABLED => '禁用',
        ];
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    public function sku()
    {
        return $this->belongsTo(Sku::class);
    }

    public function scopeEnabled($query)
    {
        return $query->where('status', self::STATUS_ENABLED);
    }
}
