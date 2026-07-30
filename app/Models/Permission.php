<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

/**
 * 权限模型
 *
 * 继承 Spatie Permission，扩展 display_name / type / parent_id / route / sort / icon 字段。
 * roles() 由 Spatie 父类提供，不要重写。
 *
 * @property int $id
 * @property string $name 权限标识
 * @property string $guard_name 守卫名称
 * @property string $display_name 显示名称
 * @property int $type 类型：1模块，2页面，3按钮
 * @property int $parent_id 上级权限ID
 * @property string|null $route 路由
 * @property int $sort 排序
 * @property string|null $icon 图标
 */
class Permission extends SpatiePermission
{
    // 类型常量
    public const TYPE_MODULE = 1;
    public const TYPE_PAGE = 2;
    public const TYPE_BUTTON = 3;

    protected $fillable = [
        'name',
        'guard_name',
        'display_name',
        'type',
        'parent_id',
        'route',
        'sort',
        'icon',
    ];

    protected function casts(): array
    {
        return [
            'type' => 'integer',
            'parent_id' => 'integer',
            'sort' => 'integer',
        ];
    }

    /**
     * 类型映射
     */
    public static function typeMap(): array
    {
        return [
            self::TYPE_MODULE => '模块',
            self::TYPE_PAGE => '页面',
            self::TYPE_BUTTON => '按钮',
        ];
    }

    /**
     * 获取显示名称（优先 display_name，回退到 name）
     */
    public function getDisplayNameAttribute($value): string
    {
        return $value ?: $this->name;
    }

    /**
     * 上级权限
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * 子权限
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort');
    }

    /**
     * 作用域：按类型筛选
     */
    public function scopeByType($query, int $type)
    {
        return $query->where('type', $type);
    }

    /**
     * 作用域：顶级权限
     */
    public function scopeRoots($query)
    {
        return $query->where('parent_id', 0)->orderBy('sort');
    }

    /**
     * 作用域：按排序字段升序
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort')->orderBy('id');
    }
}
