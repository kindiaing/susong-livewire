<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 商家账户模型
 *
 * @property mixed $merchant_id 商家ID
 * @property mixed $balance 账户余额
 * @property mixed $total_recharge 总充值
 * @property mixed $total_consumption 总消费
 * @property mixed $credit_limit 信用额度
 * @property mixed $approval_status 审核状态：1待审核2已通过3已拒绝
 */
class MerchantAccount extends Model
{
    use SoftDeletes;
    // 审核状态常量
    public const APPROVAL_PENDING = 1;
    public const APPROVAL_APPROVED = 2;
    public const APPROVAL_REJECTED = 3;

    protected $fillable = [
        'merchant_id',
        'balance',
        'frozen_amount',
        'total_recharge',
        'total_consumption',
        'credit_limit',
        'approval_status',
    ];

    protected function casts(): array
    {
        return [
            'merchant_id' => 'integer',
            'balance' => 'integer',
            'frozen_amount' => 'integer',
            'total_recharge' => 'integer',
            'total_consumption' => 'integer',
            'credit_limit' => 'integer',
            'approval_status' => 'integer',
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

    public function getApprovalStatusLabelAttribute(): string
    {
        return self::approvalStatusMap()[$this->approval_status] ?? '未知';
    }

    /**
     * 是否可编辑（待审核/已拒绝状态可编辑，已通过审核后账户基础数据锁定）
     */
    public function canEdit(): bool
    {
        return $this->approval_status !== self::APPROVAL_APPROVED;
    }

    /**
     * 是否可删除（同编辑规则）
     */
    public function canDelete(): bool
    {
        return $this->canEdit();
    }

    /**
     * 关联商家
     */
    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    /**
     * 关联审计日志
     */
    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'model', 'model_type', 'model_id');
    }
}
