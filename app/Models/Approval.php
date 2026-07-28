<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * 审批记录模型
 *
 * @property int $id
 * @property string $approval_type 审核类型编码
 * @property string $target_type 关联单据类型
 * @property int $target_id 关联单据ID
 * @property int $applicant_id 申请人ID
 * @property string $applicant_name 申请人姓名
 * @property array|null $before_data 操作前数据快照
 * @property array|null $after_data 操作后数据快照
 * @property int|null $amount 涉及金额
 * @property int $status 状态：1待审核，2已通过，3已拒绝，4已撤回
 * @property int|null $reviewer_id 审核人ID
 * @property string|null $reviewer_name 审核人姓名
 * @property string|null $review_remark 审核备注
 * @property Carbon|null $reviewed_at 审核时间
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Approval extends Model
{
    // 状态常量
    public const STATUS_PENDING = 1;
    public const STATUS_APPROVED = 2;
    public const STATUS_REJECTED = 3;
    public const STATUS_WITHDRAWN = 4;

    protected $table = 'approvals';

    protected $fillable = [
        'approval_type',
        'target_type',
        'target_id',
        'applicant_id',
        'applicant_name',
        'before_data',
        'after_data',
        'amount',
        'status',
        'reviewer_id',
        'reviewer_name',
        'review_remark',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'applicant_id' => 'integer',
            'target_id' => 'integer',
            'amount' => 'integer',
            'status' => 'integer',
            'before_data' => 'array',
            'after_data' => 'array',
            'reviewer_id' => 'integer',
            'reviewed_at' => 'datetime',
        ];
    }

    /**
     * 状态映射
     */
    public static function statusMap(): array
    {
        return [
            self::STATUS_PENDING => '待审核',
            self::STATUS_APPROVED => '已通过',
            self::STATUS_REJECTED => '已拒绝',
            self::STATUS_WITHDRAWN => '已撤回',
        ];
    }

    /**
     * 状态颜色映射
     */
    public static function statusColorMap(): array
    {
        return [
            self::STATUS_PENDING => 'orange',
            self::STATUS_APPROVED => 'green',
            self::STATUS_REJECTED => 'red',
            self::STATUS_WITHDRAWN => 'gray',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusMap()[$this->status] ?? '未知';
    }

    public function getStatusColorAttribute(): string
    {
        return self::statusColorMap()[$this->status] ?? 'gray';
    }

    /**
     * 申请人
     */
    public function applicant()
    {
        return $this->belongsTo(User::class, 'applicant_id');
    }

    /**
     * 审核人
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * 审核类型配置
     */
    public function typeConfig()
    {
        return $this->belongsTo(ApprovalTypeConfig::class, 'approval_type', 'type_code');
    }

    /**
     * 作用域：待审核
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * 作用域：按类型筛选
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('approval_type', $type);
    }
}
