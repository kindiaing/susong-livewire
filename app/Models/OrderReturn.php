<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

    use SoftDeletes;
/**
 * 售后退货模型
 *
 * @property mixed $return_no 退货单号
 * @property mixed $order_id 关联订单ID
 * @property mixed $merchant_id 商家ID
 * @property mixed $status 状态：1待审核2已审核3已退货4退款完成9取消
 * @property mixed $total_amount 退货总金额
 * @property mixed $refund_amount 实际退款金额
 * @property mixed $reason 退货原因
 * @property mixed $operator_id 经办人ID
 * @property mixed $audited_by 审核人ID
 * @property mixed $audited_at 审核时间
 * @property mixed $remark 备注
 */
class OrderReturn extends Model
{

    protected $fillable = [
        'return_no',
        'order_id',
        'merchant_id',
        'status',
        'total_amount',
        'refund_amount',
        'reason',
        'operator_id',
        'audited_by',
        'audited_at',
        'remark',
    ];

    protected function casts(): array
    {
        return [
            'order_id' => 'integer',
            'merchant_id' => 'integer',
            'status' => 'integer',
            'total_amount' => 'integer',
            'refund_amount' => 'integer',
            'operator_id' => 'integer',
            'audited_by' => 'integer',
            'audited_at' => 'datetime',
        ];
    }

}
