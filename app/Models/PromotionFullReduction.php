<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 满减活动模型
 *
 * @property int $id
 * @property int $promotion_id 促销活动ID
 * @property int $threshold_type 门槛类型：1按金额，2按件数
 * @property int $threshold_amount 门槛金额/件数
 * @property int $reduction_type 减免方式：1固定减，2折扣率，3赠品
 * @property int $reduction_amount 减免金额（厘）
 * @property int $discount_rate 折扣率（万分比）
 * @property int|null $gift_sku_id 赠品SKU ID
 * @property int $gift_quantity 赠品数量
 * @property int $is_stacked 是否可叠加：0否，1是
 * @property int $sort 排序
 */
class PromotionFullReduction extends Model
{
    public const THRESHOLD_AMOUNT = 1;
    public const THRESHOLD_QUANTITY = 2;

    public const REDUCTION_FIXED = 1;
    public const REDUCTION_DISCOUNT = 2;
    public const REDUCTION_GIFT = 3;

    protected $fillable = [
        'promotion_id',
        'threshold_type',
        'threshold_amount',
        'reduction_type',
        'reduction_amount',
        'discount_rate',
        'gift_sku_id',
        'gift_quantity',
        'is_stacked',
        'sort',
    ];

    protected function casts(): array
    {
        return [
            'promotion_id' => 'integer',
            'threshold_type' => 'integer',
            'threshold_amount' => 'integer',
            'reduction_type' => 'integer',
            'reduction_amount' => 'integer',
            'discount_rate' => 'integer',
            'gift_sku_id' => 'integer',
            'gift_quantity' => 'integer',
            'is_stacked' => 'integer',
            'sort' => 'integer',
        ];
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    public function giftSku()
    {
        return $this->belongsTo(Sku::class, 'gift_sku_id');
    }
}
