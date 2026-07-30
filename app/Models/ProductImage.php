<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * 商品图片模型
 *
 * @property int $id
 * @property int $product_id 商品ID
 * @property string $image_url 图片地址
 * @property int $sort 排序
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class ProductImage extends Model
{
    protected $fillable = [
        'product_id',
        'image_url',
        'sort',
    ];

    protected function casts(): array
    {
        return [
            'product_id' => 'integer',
            'sort' => 'integer',
        ];
    }

    /**
     * 关联商品
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
