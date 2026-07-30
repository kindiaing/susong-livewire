<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * 商品标签关联模型
 *
 * @property int $id
 * @property int $product_id 商品ID
 * @property int $tag_id 标签ID
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class ProductTag extends Model
{
    protected $fillable = [
        'product_id',
        'tag_id',
    ];

    protected function casts(): array
    {
        return [
            'product_id' => 'integer',
            'tag_id' => 'integer',
        ];
    }

    /**
     * 关联商品
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * 关联标签
     */
    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }
}
