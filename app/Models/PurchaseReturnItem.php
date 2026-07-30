<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * 采购退货明细模型
 *
 * @property int $id
 * @property int $purchase_return_id 采购退货单ID
 * @property int $purchase_order_item_id 采购单明细ID
 * @property int $sku_id SKU ID
 * @property int $quantity 退货数量
 * @property int $price 退货单价（厘）
 * @property int $amount 退货金额（厘）
 * @property int $actual_quantity 实际出库数量
 * @property int $actual_price 实际出库单价（厘）
 * @property int $actual_amount 实际出库金额（厘）
 * @property string|null $reason 明细退货原因
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
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

    /**
     * 关联退货单
     */
    public function purchaseReturn()
    {
        return $this->belongsTo(PurchaseReturn::class);
    }

    /**
     * 关联 SKU
     */
    public function sku()
    {
        return $this->belongsTo(Sku::class);
    }
}
