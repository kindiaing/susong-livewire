<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 价格策略明细模型
 *
 * @property mixed $price_strategy_id 价格策略ID
 * @property mixed $target_id 作用对象ID
 * @property mixed $category_id 商品分类ID
 * @property mixed $product_id 商品ID
 * @property mixed $sku_id SKU ID
 * @property mixed $price_type 价格类型：1固定价2折扣率3成本加权
 * @property mixed $price_value 固定价格
 * @property mixed $discount_rate 折扣率%
 * @property mixed $cost_weight_rate 成本加权率%
 * @property mixed $min_quantity 最小起量
 * @property mixed $effective_start_at 明细生效开始时间
 * @property mixed $effective_end_at 明细生效结束时间
 * @property mixed $status 状态：0禁用1启用
 */
class PriceStrategyItem extends Model
{

    protected $fillable = [
        'price_strategy_id',
        'target_id',
        'category_id',
        'product_id',
        'sku_id',
        'price_type',
        'price_value',
        'discount_rate',
        'cost_weight_rate',
        'min_quantity',
        'effective_start_at',
        'effective_end_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price_strategy_id' => 'integer',
            'target_id' => 'integer',
            'category_id' => 'integer',
            'product_id' => 'integer',
            'sku_id' => 'integer',
            'price_type' => 'integer',
            'price_value' => 'integer',
            'discount_rate' => 'integer',
            'cost_weight_rate' => 'integer',
            'min_quantity' => 'integer',
            'status' => 'integer',
            'effective_start_at' => 'datetime',
            'effective_end_at' => 'datetime',
        ];
    }

}
