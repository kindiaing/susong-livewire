<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * 标签词库模型
 *
 * @property int $id
 * @property string $name 标签名称
 * @property int $sort 排序
 * @property int $status 状态：0禁用，1启用
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Tag extends Model
{
    // 状态常量
    public const STATUS_DISABLED = 0;
    public const STATUS_ENABLED = 1;

    protected $fillable = [
        'name',
        'sort',
        'status',
    ];

    protected function casts(): array
    {
        return [
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
     * 关联商品（多对多）
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_tags', 'tag_id', 'product_id')
            ->withTimestamps();
    }

    /**
     * 作用域：启用
     */
    public function scopeEnabled($query)
    {
        return $query->where('status', self::STATUS_ENABLED);
    }
}
