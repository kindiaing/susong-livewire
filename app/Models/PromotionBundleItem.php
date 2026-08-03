<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 套餐明细模型
 *
 * @property int $id
 * @property int $bundle_id 套餐ID
 * @property int $sku_id SKU ID
 * @property int $quantity 数量
 * @property int $sort 排序
 */
class PromotionBundleItem extends Model
{
    protected $fillable = [
        'bundle_id',
        'sku_id',
        'quantity',
        'sort',
    ];

    protected function casts(): array
    {
        return [
            'bundle_id' => 'integer',
            'sku_id' => 'integer',
            'quantity' => 'integer',
            'sort' => 'integer',
        ];
    }

    public function bundle()
    {
        return $this->belongsTo(PromotionBundle::class, 'bundle_id');
    }

    public function sku()
    {
        return $this->belongsTo(Sku::class);
    }
}
