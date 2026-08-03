<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 拼团活动模型
 *
 * @property int $id
 * @property int $promotion_id 促销活动ID
 * @property int $group_price 拼团价（厘）
 * @property int $min_group_size 最少成团人数
 * @property int $max_group_size 最多拼团人数
 * @property int $time_limit 拼团时限（分钟）
 * @property int $virtual_join 虚拟凑团：0否，1是
 * @property int $status 状态：0禁用，1启用
 */
class PromotionGroupBuy extends Model
{
    public const STATUS_DISABLED = 0;
    public const STATUS_ENABLED = 1;

    protected $fillable = [
        'promotion_id',
        'group_price',
        'min_group_size',
        'max_group_size',
        'time_limit',
        'virtual_join',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'promotion_id' => 'integer',
            'group_price' => 'integer',
            'min_group_size' => 'integer',
            'max_group_size' => 'integer',
            'time_limit' => 'integer',
            'virtual_join' => 'integer',
            'status' => 'integer',
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
