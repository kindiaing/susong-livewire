<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * 审核类型配置模型
 *
 * @property int $id
 * @property string $type_code 审核类型编码（唯一）
 * @property string $type_name 审核类型名称
 * @property string|null $module_name 所属模块名称
 * @property string $risk_level 风险等级：P0/P1
 * @property int $enabled 是否启用：0关闭，1开启
 * @property int $applicant_role_id 申请人角色ID
 * @property int $reviewer_role_id 审核人角色ID
 * @property int $sort_order 显示排序
 * @property string|null $description 审核节点说明
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class ApprovalTypeConfig extends Model
{
    protected $table = 'approval_type_configs';

    protected $fillable = [
        'type_code',
        'type_name',
        'module_name',
        'risk_level',
        'enabled',
        'applicant_role_id',
        'reviewer_role_id',
        'sort_order',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'integer',
            'applicant_role_id' => 'integer',
            'reviewer_role_id' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    /**
     * 风险等级映射
     */
    public static function riskLevelMap(): array
    {
        return [
            'P0' => '核心资金',
            'P1' => '一般业务',
        ];
    }

    public function getRiskLevelLabelAttribute(): string
    {
        return self::riskLevelMap()[$this->risk_level] ?? $this->risk_level;
    }

    /**
     * 申请人角色
     */
    public function applicantRole()
    {
        return $this->belongsTo(\Spatie\Permission\Models\Role::class, 'applicant_role_id');
    }

    /**
     * 审核人角色
     */
    public function reviewerRole()
    {
        return $this->belongsTo(\Spatie\Permission\Models\Role::class, 'reviewer_role_id');
    }

    /**
     * 判断指定类型是否需要审核
     */
    public static function isApprovalRequired(string $typeCode): bool
    {
        try {
            $config = static::where('type_code', $typeCode)->first();
            return $config && $config->enabled === 1;
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * 获取审核人角色ID
     */
    public static function getReviewerRoleId(string $typeCode): ?int
    {
        try {
            $config = static::where('type_code', $typeCode)->first();
            return $config?->reviewer_role_id;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * 作用域：已启用
     */
    public function scopeEnabled($query)
    {
        return $query->where('enabled', 1);
    }

    /**
     * 作用域：按模块
     */
    public function scopeByModule($query, string $module)
    {
        return $query->where('module_name', $module);
    }

    /**
     * 作用域：按排序
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
