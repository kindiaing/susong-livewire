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

}
