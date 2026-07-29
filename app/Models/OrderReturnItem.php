<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 售后退货明细模型
 *
 * @property mixed $order_return_id 售后退货单ID
 * @property mixed $order_item_id 订单明细ID
 * @property mixed $sku_id SKU ID
 * @property mixed $quantity 退货数量
 * @property mixed $price 退货单价
 * @property mixed $amount 退货金额
 * @property mixed $refund_amount 实际退款金额
 * @property mixed $reason 明细退货原因
 */
class OrderReturnItem extends Model
{

    protected $fillable = [
        'order_return_id',
        'order_item_id',
        'sku_id',
        'quantity',
        'price',
        'amount',
        'refund_amount',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'order_return_id' => 'integer',
            'order_item_id' => 'integer',
            'sku_id' => 'integer',
            'quantity' => 'integer',
            'price' => 'integer',
            'amount' => 'integer',
            'refund_amount' => 'integer',
        ];
    }

}
