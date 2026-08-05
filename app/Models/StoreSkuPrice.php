<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * 门店差异化价格模型
 *
 * @property int $id
 * @property int $store_id 门店ID
 * @property int $sku_id SKU ID
 * @property int $price_type 价格类型：1零售价上浮下调，2独立零售价，3会员价覆盖
 * @property int $adjust_mode 调整方式：1固定金额，2百分比，3直接覆盖
 * @property int $adjust_value 调整值（金额=厘，百分比=万分比）
 * @property int $member_level 会员等级：0不限定，1普通，2银卡，3金卡，4钻石
 * @property Carbon|null $effective_at 生效时间
 * @property Carbon|null $expire_at 失效时间
 * @property int $status 状态：0禁用，1启用
 */
class StoreSkuPrice extends Model
{
    public const PRICE_TYPE_ADJUST = 1;
    public const PRICE_TYPE_FIXED = 2;
    public const PRICE_TYPE_MEMBER = 3;

    public const ADJUST_FIXED_AMOUNT = 1;
    public const ADJUST_PERCENTAGE = 2;
    public const ADJUST_OVERRIDE = 3;

    public const STATUS_DISABLED = 0;
    public const STATUS_ENABLED = 1;

    protected $fillable = [
        'store_id',
        'sku_id',
        'price_type',
        'adjust_mode',
        'adjust_value',
        'member_level',
        'effective_at',
        'expire_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'store_id' => 'integer',
            'sku_id' => 'integer',
            'price_type' => 'integer',
            'adjust_mode' => 'integer',
            'adjust_value' => 'integer',
            'member_level' => 'integer',
            'effective_at' => 'datetime',
            'expire_at' => 'datetime',
            'status' => 'integer',
        ];
    }

    public static function priceTypeMap(): array
    {
        return [
            self::PRICE_TYPE_ADJUST => '零售价上浮/下调',
            self::PRICE_TYPE_FIXED => '独立零售价',
            self::PRICE_TYPE_MEMBER => '会员价覆盖',
        ];
    }

    public static function adjustModeMap(): array
    {
        return [
            self::ADJUST_FIXED_AMOUNT => '固定金额',
            self::ADJUST_PERCENTAGE => '百分比',
            self::ADJUST_OVERRIDE => '直接覆盖',
        ];
    }

    public static function statusMap(): array
    {
        return [
            self::STATUS_ENABLED => '启用',
            self::STATUS_DISABLED => '禁用',
        ];
    }

    public function sku()
    {
        return $this->belongsTo(Sku::class);
    }

    /**
     * 关联商家（store_id 对应 merchant_id）
     */
    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'store_id');
    }

    public function scopeEnabled($query)
    {
        return $query->where('status', self::STATUS_ENABLED);
    }

    public function scopeEffective($query)
    {
        return $query->where('status', self::STATUS_ENABLED)
            ->where('effective_at', '<=', now())
            ->where(function ($q) {
                $q->whereNull('expire_at')
                    ->orWhere('expire_at', '>=', now());
            });
    }
}
