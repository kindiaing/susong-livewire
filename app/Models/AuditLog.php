<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * 审计日志模型
 *
 * @property int $id
 * @property string $model_type 模型类型
 * @property int $model_id 模型ID
 * @property string $action 操作动作
 * @property array|null $before_data 修改前数据
 * @property array|null $after_data 修改后数据
 * @property int|null $operator_id 操作人ID
 * @property string|null $ip 操作人IP地址
 * @property string|null $user_agent 浏览器/客户端UA
 * @property string|null $reason 操作原因
 * @property string|null $relation_type 关联类型
 * @property int|null $relation_id 关联ID
 * @property Carbon|null $created_at
 */
class AuditLog extends Model
{
    public $timestamps = false;

    protected $table = 'audit_logs';

    const CREATED_AT = 'created_at';

    protected $fillable = [
        'model_type',
        'model_id',
        'action',
        'before_data',
        'after_data',
        'operator_id',
        'ip',
        'user_agent',
        'reason',
        'relation_type',
        'relation_id',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'model_id' => 'integer',
            'operator_id' => 'integer',
            'relation_id' => 'integer',
            'before_data' => 'array',
            'after_data' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * 操作动作映射
     */
    public static function actionMap(): array
    {
        return [
            'create' => '创建',
            'update' => '修改',
            'delete' => '删除',
            'restore' => '恢复',
            'login' => '登录',
            'logout' => '登出',
            'approve' => '审核通过',
            'reject' => '审核拒绝',
            'export' => '导出',
            'import' => '导入',
        ];
    }

    public function getActionLabelAttribute(): string
    {
        return self::actionMap()[$this->action] ?? $this->action;
    }

    /**
     * 操作人
     */
    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    /**
     * 作用域：按模型类型
     */
    public function scopeByModel($query, string $modelType)
    {
        return $query->where('model_type', $modelType);
    }

    /**
     * 作用域：按操作人
     */
    public function scopeByOperator($query, int $operatorId)
    {
        return $query->where('operator_id', $operatorId);
    }

    /**
     * 作用域：按动作
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * 记录审计日志
     */
    public static function log(
        string $modelType,
        int $modelId,
        string $action,
        ?array $beforeData = null,
        ?array $afterData = null,
        ?string $reason = null,
        ?string $relationType = null,
        ?int $relationId = null,
    ): self {
        return static::create([
            'model_type' => $modelType,
            'model_id' => $modelId,
            'action' => $action,
            'before_data' => $beforeData,
            'after_data' => $afterData,
            'operator_id' => auth()->id(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'reason' => $reason,
            'relation_type' => $relationType,
            'relation_id' => $relationId,
            'created_at' => now(),
        ]);
    }
}
