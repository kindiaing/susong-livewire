<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 采购退货明细模型
 *
 * @property mixed $purchase_return_id 采购退货单ID
 * @property mixed $purchase_order_item_id 采购单明细ID
 * @property mixed $sku_id SKU ID
 * @property mixed $quantity 退货数量
 * @property mixed $price 退货单价
 * @property mixed $amount 退货金额
 * @property mixed $actual_quantity 实际出库数量
 * @property mixed $actual_price 实际出库单价
 * @property mixed $actual_amount 实际出库金额
 * @property mixed $reason 明细退货原因
 */
class PurchaseReturnItem extends Model
{

    protected $fillable = [
        'purchase_return_id',
        'purchase_order_item_id',
        'sku_id',
        'quantity',
        'price',
        'amount',
        'actual_quantity',
        'actual_price',
        'actual_amount',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'purchase_return_id' => 'integer',
            'purchase_order_item_id' => 'integer',
            'sku_id' => 'integer',
            'quantity' => 'integer',
            'price' => 'integer',
            'amount' => 'integer',
            'actual_quantity' => 'integer',
            'actual_price' => 'integer',
            'actual_amount' => 'integer',
        ];
    }

}
