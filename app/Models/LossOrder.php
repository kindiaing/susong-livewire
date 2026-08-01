<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * 损耗单模型
 *
 * @property int $id
 * @property string $loss_no 损耗单号
 * @property int $warehouse_id 仓库ID
 * @property int $total_amount 损耗总金额（厘）
 * @property int $loss_type 主要损耗类型：1存储腐坏，2称重失水，3过期报废，4加工损耗，5盘点差异，6其他
 * @property int $status 状态：1待审核，2已通过，3已执行，4已关闭，9已取消
 * @property int $approval_status 审核状态：1待审核，2已通过，3已拒绝
 * @property int|null $applicant_id 申请人ID
 * @property int|null $reviewer_id 审核人ID
 * @property Carbon|null $reviewed_at 审核时间
 * @property Carbon|null $executed_at 执行时间
 * @property Carbon|null $closed_at 关闭时间
 * @property string|null $reason 损耗原因
 * @property string|null $remark 备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class LossOrder extends Model
{
    use SoftDeletes;

    // 损耗类型常量
    public const LOSS_STORAGE = 1;
    public const LOSS_WEIGH = 2;
    public const LOSS_EXPIRED = 3;
    public const LOSS_PROCESS = 4;
    public const LOSS_INVENTORY = 5;
    public const LOSS_OTHER = 6;

    // 状态常量
    public const STATUS_PENDING = 1;
    public const STATUS_APPROVED = 2;
    public const STATUS_EXECUTED = 3;
    public const STATUS_CLOSED = 4;
    public const STATUS_CANCELLED = 9;

    // 审核状态常量
    public const APPROVAL_PENDING = 1;
    public const APPROVAL_APPROVED = 2;
    public const APPROVAL_REJECTED = 3;

    protected $fillable = [
        'loss_no',
        'warehouse_id',
        'total_amount',
        'loss_type',
        'status',
        'approval_status',
        'applicant_id',
        'reviewer_id',
        'reviewed_at',
        'executed_at',
        'closed_at',
        'reason',
        'remark',
    ];

    protected function casts(): array
    {
        return [
            'warehouse_id' => 'integer',
            'total_amount' => 'integer',
            'loss_type' => 'integer',
            'status' => 'integer',
            'approval_status' => 'integer',
            'applicant_id' => 'integer',
            'reviewer_id' => 'integer',
            'reviewed_at' => 'datetime',
            'executed_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    /**
     * 损耗类型映射
     */
    public static function typeMap(): array
    {
        return [
            self::LOSS_STORAGE => '存储腐坏',
            self::LOSS_WEIGH => '称重失水',
            self::LOSS_EXPIRED => '过期报废',
            self::LOSS_PROCESS => '加工损耗',
            self::LOSS_INVENTORY => '盘点差异',
            self::LOSS_OTHER => '其他',
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
            self::STATUS_EXECUTED => '已执行',
            self::STATUS_CLOSED => '已关闭',
            self::STATUS_CANCELLED => '已取消',
        ];
    }

    /**
     * 审核状态映射
     */
    public static function approvalStatusMap(): array
    {
        return [
            self::APPROVAL_PENDING => '待审核',
            self::APPROVAL_APPROVED => '已通过',
            self::APPROVAL_REJECTED => '已拒绝',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusMap()[$this->status] ?? '未知';
    }

    public function getApprovalStatusLabelAttribute(): string
    {
        return self::approvalStatusMap()[$this->approval_status] ?? '未知';
    }

    public function getLossTypeLabelAttribute(): string
    {
        return self::typeMap()[$this->loss_type] ?? '未知';
    }

    /**
     * 关联仓库
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * 关联损耗明细
     */
    public function items()
    {
        return $this->hasMany(LossOrderItem::class);
    }

    /**
     * 关联申请人
     */
    public function applicant()
    {
        return $this->belongsTo(User::class, 'applicant_id');
    }

    /**
     * 关联审核人
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * 作用域：待审核
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}
