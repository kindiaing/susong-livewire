<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 优惠券模型
 *
 * @property int $id
 * @property int $promotion_id 促销活动ID
 * @property string $coupon_code 优惠券编码
 * @property string $name 优惠券名称
 * @property int $coupon_type 类型：1满减券，2折扣券，3抵扣券，4运费券
 * @property int $threshold_amount 使用门槛金额（厘）
 * @property int $reduction_amount 抵扣金额（厘）
 * @property int $discount_rate 折扣率（万分比）
 * @property int $max_discount 最大优惠上限（厘）
 * @property int $total_quantity 发放总量
 * @property int $claimed_quantity 已领取数量
 * @property int $used_quantity 已使用数量
 * @property int $per_user_limit 每人限领
 * @property int $valid_days 领取后有效天数
 * @property int $status 状态：0禁用，1启用
 */
class PromotionCoupon extends Model
{
    use SoftDeletes;

    public const COUPON_REDUCTION = 1;
    public const COUPON_DISCOUNT = 2;
    public const COUPON_DEDUCTION = 3;
    public const COUPON_SHIPPING = 4;

    public const STATUS_DISABLED = 0;
    public const STATUS_ENABLED = 1;

    protected $fillable = [
        'promotion_id',
        'coupon_code',
        'name',
        'coupon_type',
        'threshold_amount',
        'reduction_amount',
        'discount_rate',
        'max_discount',
        'total_quantity',
        'claimed_quantity',
        'used_quantity',
        'per_user_limit',
        'valid_days',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'promotion_id' => 'integer',
            'coupon_type' => 'integer',
            'threshold_amount' => 'integer',
            'reduction_amount' => 'integer',
            'discount_rate' => 'integer',
            'max_discount' => 'integer',
            'total_quantity' => 'integer',
            'claimed_quantity' => 'integer',
            'used_quantity' => 'integer',
            'per_user_limit' => 'integer',
            'valid_days' => 'integer',
            'status' => 'integer',
        ];
    }

    public static function couponTypeMap(): array
    {
        return [
            self::COUPON_REDUCTION => '满减券',
            self::COUPON_DISCOUNT => '折扣券',
            self::COUPON_DEDUCTION => '抵扣券',
            self::COUPON_SHIPPING => '运费券',
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
}
