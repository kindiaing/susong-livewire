<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * 秒杀活动模型
 *
 * @property int $id
 * @property int $promotion_id 促销活动ID
 * @property int $flash_price 秒杀价（厘）
 * @property int $total_stock 秒杀总库存
 * @property int $sold_stock 已售库存
 * @property int $per_user_limit 每人限购
 * @property Carbon|null $flash_start_at 秒杀开始时间
 * @property Carbon|null $flash_end_at 秒杀结束时间
 * @property int $status 状态：0禁用，1启用
 */
class PromotionFlashSale extends Model
{
    public const STATUS_DISABLED = 0;
    public const STATUS_ENABLED = 1;

    protected $fillable = [
        'promotion_id',
        'flash_price',
        'total_stock',
        'sold_stock',
        'per_user_limit',
        'flash_start_at',
        'flash_end_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'promotion_id' => 'integer',
            'flash_price' => 'integer',
            'total_stock' => 'integer',
            'sold_stock' => 'integer',
            'per_user_limit' => 'integer',
            'flash_start_at' => 'datetime',
            'flash_end_at' => 'datetime',
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
