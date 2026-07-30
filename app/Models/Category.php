<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * 商品分类模型
 *
 * @property int $id
 * @property int $parent_id 父级分类ID，0为根节点
 * @property string $name 分类名称
 * @property string|null $icon 图标
 * @property int $sort 排序
 * @property int $status 状态：0禁用，1启用
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Category extends Model
{
    use SoftDeletes;

    // 状态常量
    public const STATUS_DISABLED = 0;
    public const STATUS_ENABLED = 1;

    protected $fillable = [
        'parent_id',
        'name',
        'icon',
        'sort',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'parent_id' => 'integer',
            'sort' => 'integer',
            'status' => 'integer',
        ];
    }

    /**
     * 状态映射
     */
    public static function statusMap(): array
    {
        return [
            self::STATUS_ENABLED => '启用',
            self::STATUS_DISABLED => '禁用',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusMap()[$this->status] ?? '未知';
    }

    /**
     * 父级分类
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * 子分类
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * 关联商品
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * 作用域：启用
     */
    public function scopeEnabled($query)
    {
        return $query->where('status', self::STATUS_ENABLED);
    }
}
