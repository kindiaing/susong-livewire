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

    /**
     * 作用域：根分类
     */
    public function scopeRoot($query)
    {
        return $query->where('parent_id', 0);
    }

    /**
     * 作用域：按排序字段排序
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort')->orderBy('id');
    }

    /**
     * 是否为根分类
     */
    public function isRoot(): bool
    {
        return $this->parent_id === 0;
    }

    /**
     * 是否有子分类
     */
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    /**
     * 获取所有后代分类ID（递归，含自身）
     */
    public function getAllChildrenIds(): array
    {
        $ids = [$this->id];
        $children = $this->children()->get();

        foreach ($children as $child) {
            $ids = array_merge($ids, $child->getAllChildrenIds());
        }

        return $ids;
    }

    /**
     * 获取树形结构（静态方法，一次性查询后在内存中组装）
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getTree(): \Illuminate\Database\Eloquent\Collection
    {
        $all = static::ordered()->get();
        $byParent = $all->groupBy('parent_id');

        $buildTree = function ($parentId) use (&$buildTree, &$byParent) {
            $nodes = $byParent->get($parentId, static::newCollection());
            foreach ($nodes as $node) {
                $node->setRelation('children', $buildTree($node->id));
            }
            return $nodes;
        };

        return $buildTree(0);
    }

    /**
     * 获取扁平化的分类选择项（用于 searchable-select 下拉）
     * 一级分类无前缀，二级/三级分类前加缩进前缀（全角空格）
     *
     * @return array<array{value: string, label: string}>
     */
    public static function toSelectOptions(): array
    {
        $tree = static::getTree();
        $options = [];

        $flatten = function ($nodes, int $depth) use (&$flatten, &$options) {
            foreach ($nodes as $node) {
                $prefix = str_repeat('　　', $depth); // 全角空格缩进
                $options[] = [
                    'value' => (string) $node->id,
                    'label' => $prefix . $node->name,
                ];
                if ($node->children->isNotEmpty()) {
                    $flatten($node->children, $depth + 1);
                }
            }
        };

        $flatten($tree, 0);
        return $options;
    }
}
