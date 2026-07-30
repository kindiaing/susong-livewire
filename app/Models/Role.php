<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

/**
 * 角色模型
 *
 * 继承 Spatie Role，扩展 display_name / description 字段。
 * permissions() 和 users() 由 Spatie 父类提供，不要重写。
 *
 * @property int $id
 * @property string $name 角色标识
 * @property string $guard_name 守卫名称
 * @property string $display_name 显示名称
 * @property string|null $description 描述
 */
class Role extends SpatieRole
{
    protected $fillable = [
        'name',
        'guard_name',
        'display_name',
        'description',
    ];

    /**
     * 获取显示名称（优先 display_name，回退到 name）
     */
    public function getDisplayNameAttribute($value): string
    {
        return $value ?: $this->name;
    }
}
