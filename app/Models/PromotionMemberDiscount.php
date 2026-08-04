<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 会员等级折扣模型
 *
 * @property int $id
 * @property int $promotion_id 促销活动ID（0=全局常驻规则）
 * @property int $member_level 会员等级：1普通，2银卡，3金卡，4钻石
 * @property int $discount_rate 折扣率（万分比，9500=95折）
 * @property int $is_permanent 是否常驻：0否，1是
 * @property int $status 状态：0禁用，1启用
 */
class PromotionMemberDiscount extends Model
{
    public const MEMBER_NORMAL = 1;
    public const MEMBER_SILVER = 2;
    public const MEMBER_GOLD = 3;
    public const MEMBER_DIAMOND = 4;

    public const STATUS_DISABLED = 0;
    public const STATUS_ENABLED = 1;

    protected $fillable = [
        'promotion_id',
        'member_level',
        'discount_rate',
        'is_permanent',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'promotion_id' => 'integer',
            'member_level' => 'integer',
            'discount_rate' => 'integer',
            'is_permanent' => 'integer',
            'status' => 'integer',
        ];
    }

    public static function memberLevelMap(): array
    {
        return [
            self::MEMBER_NORMAL => '普通',
            self::MEMBER_SILVER => '银卡',
            self::MEMBER_GOLD => '金卡',
            self::MEMBER_DIAMOND => '钻石',
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
