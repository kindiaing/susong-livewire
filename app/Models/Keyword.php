<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * 搜索关键词模型
 *
 * @property int $id
 * @property string $keyword 关键词
 * @property int|null $product_id 关联商品ID
 * @property int $search_count 搜索次数
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Keyword extends Model
{
    protected $fillable = [
        'keyword',
        'product_id',
        'search_count',
    ];

    protected function casts(): array
    {
        return [
            'product_id' => 'integer',
            'search_count' => 'integer',
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
     * 作用域：热门关键词（按搜索次数排序）
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->orderByDesc('search_count')->limit($limit);
    }
}
