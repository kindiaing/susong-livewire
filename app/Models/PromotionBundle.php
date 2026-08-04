<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 组合捆绑套餐模型
 *
 * @property int $id
 * @property int $promotion_id 促销活动ID
 * @property string $bundle_name 套餐名称
 * @property int $bundle_price 套餐价（厘）
 * @property int $original_total 原价合计（厘）
 * @property int $bundle_quantity 每组最低件数
 * @property int $status 状态：0禁用，1启用
 */
class PromotionBundle extends Model
{
    public const STATUS_DISABLED = 0;
    public const STATUS_ENABLED = 1;

    protected $fillable = [
        'promotion_id',
        'bundle_name',
        'bundle_price',
        'original_total',
        'bundle_quantity',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'promotion_id' => 'integer',
            'bundle_price' => 'integer',
            'original_total' => 'integer',
            'bundle_quantity' => 'integer',
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

    public function items()
    {
        return $this->hasMany(PromotionBundleItem::class, 'bundle_id');
    }
}
