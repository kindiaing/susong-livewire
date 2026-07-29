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

}
