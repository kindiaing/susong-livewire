<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 充值记录模型
 *
 * @property mixed $merchant_id 商家ID
 * @property mixed $amount 充值金额
 * @property mixed $payment_method 支付方式：1微信支付2线下转账3后台手工
 * @property mixed $transaction_no 第三方交易号
 * @property mixed $status 状态：1待确认2成功3失败
 * @property mixed $approval_status 审核状态：1待审核2已通过3已拒绝
 * @property mixed $operator_id 操作人ID
 * @property mixed $remark 备注
 */
class Recharge extends Model
{
    // 支付方式常量
    public const METHOD_WECHAT = 1;
    public const METHOD_OFFLINE = 2;
    public const METHOD_MANUAL = 3;

    // 状态常量
    public const STATUS_PENDING = 1;
    public const STATUS_SUCCESS = 2;
    public const STATUS_FAILED = 3;

    // 审核状态常量
    public const APPROVAL_PENDING = 1;
    public const APPROVAL_APPROVED = 2;
    public const APPROVAL_REJECTED = 3;

    protected $fillable = [
        'merchant_id',
        'amount',
        'payment_method',
        'transaction_no',
        'status',
        'approval_status',
        'operator_id',
        'remark',
    ];

    protected function casts(): array
    {
        return [
            'merchant_id' => 'integer',
            'amount' => 'integer',
            'payment_method' => 'integer',
            'status' => 'integer',
            'approval_status' => 'integer',
            'operator_id' => 'integer',
        ];
    }

    /**
     * 支付方式映射
     */
    public static function paymentMethodMap(): array
    {
        return [
            self::METHOD_WECHAT => '微信支付',
            self::METHOD_OFFLINE => '线下转账',
            self::METHOD_MANUAL => '后台手工',
        ];
    }

    /**
     * 状态映射
     */
    public static function statusMap(): array
    {
        return [
            self::STATUS_PENDING => '待确认',
            self::STATUS_SUCCESS => '成功',
            self::STATUS_FAILED => '失败',
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

    public function getPaymentMethodLabelAttribute(): string
    {
        return self::paymentMethodMap()[$this->payment_method] ?? '未知';
    }

    /**
     * 关联商家
     */
    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    /**
     * 关联操作人
     */
    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    /**
     * 作用域：待确认
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * 作用域：按状态
     */
    public function scopeByStatus($query, int $status)
    {
        return $query->where('status', $status);
    }
}
