<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 应收收款模型
 *
 * @property mixed $receivable_id 应收账款ID
 * @property mixed $amount 本次收款金额
 * @property mixed $payment_method 收款方式：1余额扣款2微信支付3线下转账4后台手工
 * @property mixed $transaction_no 第三方交易号
 * @property mixed $operator_id 操作人ID
 * @property mixed $approval_status 审核状态：1待审核2已通过3已拒绝
 * @property mixed $evidence_urls 收款凭证图片数组
 * @property mixed $remark 备注
 */
class ReceivablePayment extends Model
{

    protected $fillable = [
        'receivable_id',
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
            'receivable_id' => 'integer',
            'amount' => 'integer',
            'payment_method' => 'integer',
            'operator_id' => 'integer',
            'approval_status' => 'integer',
            'evidence_urls' => 'array',
        ];
    }

}
