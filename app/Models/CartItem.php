<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * 购物车明细模型
 *
 * @property int $id
 * @property int $cart_id 购物车ID
 * @property int $sku_id SKU ID
 * @property int $quantity 数量
 * @property int $price 加入时单价（厘）
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'sku_id',
        'quantity',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'cart_id' => 'integer',
            'sku_id' => 'integer',
            'quantity' => 'integer',
            'price' => 'integer',
        ];
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function sku()
    {
        return $this->belongsTo(Sku::class);
    }

    /**
     * 关联商品（通过 SKU）
     */
    public function product()
    {
        return $this->hasOneThrough(Product::class, Sku::class, 'id', 'id', 'sku_id', 'product_id');
    }
}
