<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * 商品模型
 *
 * @property int $id
 * @property int $category_id 分类ID
 * @property int|null $supplier_id 默认供应商ID
 * @property string $name 商品名称
 * @property string|null $cover 封面图
 * @property string $unit 单位
 * @property int $is_weight_priced 是否称重改价：0否，1是
 * @property int $stock_warning_value 库存预警值
 * @property int $status 状态：0下架，1上架
 * @property string|null $description 商品详情
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Product extends Model
{
    use SoftDeletes;

    // 状态常量
    public const STATUS_OFF = 0;
    public const STATUS_ON = 1;

    protected $fillable = [
        'category_id',
        'supplier_id',
        'name',
        'cover',
        'unit',
        'is_weight_priced',
        'stock_warning_value',
        'status',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'category_id' => 'integer',
            'supplier_id' => 'integer',
            'is_weight_priced' => 'integer',
            'stock_warning_value' => 'integer',
            'status' => 'integer',
        ];
    }

    /**
     * 状态映射
     */
    public static function statusMap(): array
    {
        return [
            self::STATUS_ON => '上架',
            self::STATUS_OFF => '下架',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusMap()[$this->status] ?? '未知';
    }

    /**
     * 关联分类
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * 关联默认供应商
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * 关联图片
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort');
    }

    /**
     * 关联 SKU
     */
    public function skus()
    {
        return $this->hasMany(Sku::class);
    }

    /**
     * 关联标签（多对多）
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tags', 'product_id', 'tag_id')
            ->withTimestamps();
    }

    /**
     * 关联关键词
     */
    public function keywords()
    {
        return $this->hasMany(Keyword::class);
    }

    /**
     * 作用域：上架
     */
    public function scopeOnSale($query)
    {
        return $query->where('status', self::STATUS_ON);
    }
}
