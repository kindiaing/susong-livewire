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

    protected $fillable = [
        'merchant_id',
        'balance',
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
            'total_recharge' => 'integer',
            'total_consumption' => 'integer',
            'credit_limit' => 'integer',
            'approval_status' => 'integer',
        ];
    }

}
