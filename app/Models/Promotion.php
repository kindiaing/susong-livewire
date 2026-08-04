<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 促销活动主表模型
 *
 * @property int $id
 * @property string $name 活动名称
 * @property int $promo_type 促销类型
 * @property string|null $promo_code 活动编码
 * @property string|null $description 活动描述
 * @property int $scope_type 适用范围
 * @property mixed $start_at 开始时间
 * @property mixed $end_at 结束时间
 * @property int $status 状态
 * @property int|null $created_by 创建人ID
 */
class Promotion extends Model
{
    use SoftDeletes;

    // 促销类型常量
    public const TYPE_PROMOTION = 1;
    public const TYPE_FULL_REDUCTION = 2;
    public const TYPE_COUPON = 3;
    public const TYPE_BUNDLE = 4;
    public const TYPE_CLEARANCE = 5;
    public const TYPE_GROUP_BUY = 6;
    public const TYPE_FLASH_SALE = 7;
    public const TYPE_MEMBER_DISCOUNT = 8;

    // 状态常量
    public const STATUS_DISABLED = 0;
    public const STATUS_ENABLED = 1;

    protected $fillable = [
        'name',
        'promo_type',
        'promo_code',
        'description',
        'scope_type',
        'start_at',
        'end_at',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'promo_type' => 'integer',
            'scope_type' => 'integer',
            'status' => 'integer',
            'start_at' => 'datetime',
            'end_at' => 'datetime',
        ];
    }

    /**
     * 促销类型映射
     */
    public static function typeMap(): array
    {
        return [
            self::TYPE_PROMOTION => '普通促销',
            self::TYPE_FULL_REDUCTION => '满减',
            self::TYPE_COUPON => '优惠券',
            self::TYPE_BUNDLE => '组合套餐',
            self::TYPE_CLEARANCE => '清仓临期',
            self::TYPE_GROUP_BUY => '拼团',
            self::TYPE_FLASH_SALE => '秒杀',
            self::TYPE_MEMBER_DISCOUNT => '会员折扣',
        ];
    }

    /**
     * 状态映射
     */
    public static function statusMap(): array
    {
        return [
            self::STATUS_ENABLED => '启用',
            self::STATUS_DISABLED => '禁用',
        ];
    }

    public function getTypeLabelAttribute(): string
    {
        return self::typeMap()[$this->promo_type] ?? '未知';
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusMap()[$this->status] ?? '未知';
    }

    /**
     * 作用域：启用
     */
    public function scopeEnabled($query)
    {
        return $query->where('status', self::STATUS_ENABLED);
    }

    /**
     * 作用域：生效中
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ENABLED)
            ->where('start_at', '<=', now())
            ->where('end_at', '>=', now());
    }

    // ── 子表关联 ──────────────────────────

    public function skus()
    {
        return $this->hasMany(PromotionSku::class);
    }

    public function fullReductions()
    {
        return $this->hasMany(PromotionFullReduction::class);
    }

    public function coupons()
    {
        return $this->hasMany(PromotionCoupon::class);
    }

    public function bundles()
    {
        return $this->hasMany(PromotionBundle::class);
    }

    public function bundleItems()
    {
        return $this->hasManyThrough(PromotionBundleItem::class, PromotionBundle::class);
    }

    public function clearances()
    {
        return $this->hasMany(PromotionClearance::class);
    }

    public function groupBuys()
    {
        return $this->hasMany(PromotionGroupBuy::class);
    }

    public function flashSales()
    {
        return $this->hasMany(PromotionFlashSale::class);
    }

    public function memberDiscounts()
    {
        return $this->hasMany(PromotionMemberDiscount::class);
    }

    /**
     * 创建人
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
