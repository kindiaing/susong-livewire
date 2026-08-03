<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 清仓临期活动模型
 *
 * @property int $id
 * @property int $promotion_id 促销活动ID
 * @property int $clearance_type 类型：1清仓，2临期
 * @property string|null $expiry_date 临期截止日期
 * @property int $discount_rate 折扣率（万分比）
 * @property int $fixed_price 清仓固定价（厘）
 * @property int $status 状态：0禁用，1启用
 */
class PromotionClearance extends Model
{
    public const CLEARANCE_TYPE_NORMAL = 1;
    public const CLEARANCE_TYPE_EXPIRING = 2;

    public const STATUS_DISABLED = 0;
    public const STATUS_ENABLED = 1;

    protected $fillable = [
        'promotion_id',
        'clearance_type',
        'expiry_date',
        'discount_rate',
        'fixed_price',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'promotion_id' => 'integer',
            'clearance_type' => 'integer',
            'expiry_date' => 'date',
            'discount_rate' => 'integer',
            'fixed_price' => 'integer',
            'status' => 'integer',
        ];
    }

    public static function clearanceTypeMap(): array
    {
        return [
            self::CLEARANCE_TYPE_NORMAL => '清仓',
            self::CLEARANCE_TYPE_EXPIRING => '临期',
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
