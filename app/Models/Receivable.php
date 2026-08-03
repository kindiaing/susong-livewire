<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 应收账款模型
 *
 * @property mixed $receivable_no 应收单号
 * @property mixed $order_id 订单ID
 * @property mixed $merchant_id 商家ID
 * @property mixed $original_amount 原始金额
 * @property mixed $adjusted_amount 调整后金额
 * @property mixed $discrepancy_amount 差异金额
 * @property mixed $return_amount 售后退货扣减金额
 * @property mixed $strategy_discount_amount 改价/促销折扣金额
 * @property mixed $received_amount 已收金额
 * @property mixed $status 状态：1未结算2部分收款3已结清4争议中5已办结
 * @property mixed $settlement_type 结算方式：1现结2账期3预付款
 * @property mixed $due_date 到期日
 * @property mixed $settled_at 结算时间
 * @property mixed $closed_at 办结时间
 * @property mixed $closed_by 办结操作人ID
 * @property mixed $approval_status 审核状态：1待审核2已通过3已拒绝
 */
class Receivable extends Model
{
    use SoftDeletes;

    // 状态常量
    public const STATUS_UNSETTLED = 1;
    public const STATUS_PARTIAL = 2;
    public const STATUS_SETTLED = 3;
    public const STATUS_DISPUTED = 4;
    public const STATUS_CLOSED = 5;

    // 结算方式常量
    public const SETTLEMENT_CASH = 1;
    public const SETTLEMENT_CREDIT = 2;
    public const SETTLEMENT_PREPAID = 3;

    // 审核状态常量
    public const APPROVAL_PENDING = 1;
    public const APPROVAL_APPROVED = 2;
    public const APPROVAL_REJECTED = 3;

    protected $fillable = [
        'receivable_no',
        'order_id',
        'merchant_id',
        'original_amount',
        'adjusted_amount',
        'discrepancy_amount',
        'return_amount',
        'strategy_discount_amount',
        'received_amount',
        'status',
        'settlement_type',
        'due_date',
        'settled_at',
        'closed_at',
        'closed_by',
        'approval_status',
    ];

    protected function casts(): array
    {
        return [
            'order_id' => 'integer',
            'merchant_id' => 'integer',
            'original_amount' => 'integer',
            'adjusted_amount' => 'integer',
            'discrepancy_amount' => 'integer',
            'return_amount' => 'integer',
            'strategy_discount_amount' => 'integer',
            'received_amount' => 'integer',
            'status' => 'integer',
            'settlement_type' => 'integer',
            'closed_by' => 'integer',
            'approval_status' => 'integer',
            'due_date' => 'date',
            'settled_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    /**
     * 状态映射
     */
    public static function statusMap(): array
    {
        return [
            self::STATUS_UNSETTLED => '未结算',
            self::STATUS_PARTIAL => '部分收款',
            self::STATUS_SETTLED => '已结清',
            self::STATUS_DISPUTED => '争议中',
            self::STATUS_CLOSED => '已办结',
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

    /**
     * 关联订单
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * 关联商家
     */
    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    /**
     * 关联收款记录
     */
    public function payments()
    {
        return $this->hasMany(ReceivablePayment::class);
    }

    /**
     * 关联办结操作人
     */
    public function closedByUser()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    /**
     * 作用域：未结算
     */
    public function scopeUnsettled($query)
    {
        return $query->where('status', self::STATUS_UNSETTLED);
    }
}
