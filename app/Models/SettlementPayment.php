<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 结算付款模型
 *
 * @property mixed $settlement_id 供应商结算单ID
 * @property mixed $amount 本次付款金额
 * @property mixed $payment_method 付款方式：1银行转账2线下现金3后台手工
 * @property mixed $transaction_no 第三方交易号
 * @property mixed $operator_id 操作人ID
 * @property mixed $approval_status 审核状态：1待审核2已通过3已拒绝
 * @property mixed $evidence_urls 付款凭证图片数组
 * @property mixed $remark 备注
 */
class SettlementPayment extends Model
{
    use SoftDeletes;
    // 付款方式常量
    public const METHOD_BANK = 1;
    public const METHOD_CASH = 2;
    public const METHOD_MANUAL = 3;

    // 审核状态常量
    public const APPROVAL_PENDING = 1;
    public const APPROVAL_APPROVED = 2;
    public const APPROVAL_REJECTED = 3;

    protected $fillable = [
        'settlement_id',
        'amount',
        'payment_method',
        'transaction_no',
        'operator_id',
        'approval_status',
        'evidence_urls',
        'remark',
    ];

    protected function casts(): array
    {
        return [
            'settlement_id' => 'integer',
            'amount' => 'integer',
            'payment_method' => 'integer',
            'operator_id' => 'integer',
            'approval_status' => 'integer',
            'evidence_urls' => 'array',
        ];
    }

    /**
     * 付款方式映射
     */
    public static function paymentMethodMap(): array
    {
        return [
            self::METHOD_BANK => '银行转账',
            self::METHOD_CASH => '线下现金',
            self::METHOD_MANUAL => '后台手工',
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

    public function getPaymentMethodLabelAttribute(): string
    {
        return self::paymentMethodMap()[$this->payment_method] ?? '未知';
    }

    public function getApprovalStatusLabelAttribute(): string
    {
        return self::approvalStatusMap()[$this->approval_status] ?? '未知';
    }

    /**
     * 关联结算单
     */
    public function settlement()
    {
        return $this->belongsTo(SupplierSettlement::class, 'settlement_id');
    }

    /**
     * 关联操作人
     */
    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    /**
     * 作用域：待审核
     */
    public function scopePending($query)
    {
        return $query->where('approval_status', self::APPROVAL_PENDING);
    }
}
