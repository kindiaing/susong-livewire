<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * 角色模型
 *
 * @property int $id
 * @property string $name 角色标识
 * @property string $guard_name 守卫名称
 * @property string $display_name 显示名称
 * @property string|null $description 描述
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Role extends SpatieRole
{
    protected $fillable = [
        'name',
        'guard_name',
        'display_name',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'display_name' => 'string',
        ];
    }

    /**
     * 获取显示名称（优先 display_name）
     */
    public function getDisplayNameAttribute($value): string
    {
        return $value ?? $this->name;
    }

    /**
     * 关联权限
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions');
    }

    /**
     * 关联用户
     */
    public function users()
    {
        return $this->morphedByMany(User::class, 'model', 'model_has_roles');
    }
}
