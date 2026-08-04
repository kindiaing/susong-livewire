<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * 采购单明细模型
 *
 * @property int $id
 * @property int $purchase_order_id 采购单ID
 * @property int $sku_id SKU ID
 * @property int $quantity 采购数量
 * @property int $price 采购单价（厘）
 * @property int $actual_quantity 实际入库数量
 * @property int $actual_price 实际入库单价（厘）
 * @property int $amount 金额（厘）
 * @property int $actual_amount 实际金额（厘）
 * @property int $strategy_price 改价/促销单价（厘）
 * @property int $strategy_amount 改价/促销金额（厘）
 * @property int|null $price_strategy_id 价格策略ID
 * @property int|null $price_strategy_item_id 价格策略明细ID
 * @property string|null $discrepancy_reason 入库差异原因
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'sku_id',
        'quantity',
        'price',
        'actual_quantity',
        'actual_price',
        'amount',
        'actual_amount',
        'strategy_price',
        'strategy_amount',
        'price_strategy_id',
        'price_strategy_item_id',
        'discrepancy_reason',
        'discrepancy_quantity',
        'loss_order_id',
    ];

    protected function casts(): array
    {
        return [
            'purchase_order_id' => 'integer',
            'sku_id' => 'integer',
            'quantity' => 'integer',
            'price' => 'integer',
            'actual_quantity' => 'integer',
            'actual_price' => 'integer',
            'amount' => 'integer',
            'actual_amount' => 'integer',
            'strategy_price' => 'integer',
            'strategy_amount' => 'integer',
            'price_strategy_id' => 'integer',
            'price_strategy_item_id' => 'integer',
            'discrepancy_quantity' => 'integer',
            'loss_order_id' => 'integer',
        ];
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function sku()
    {
        return $this->belongsTo(Sku::class);
    }

    /**
     * 关联价格策略
     */
    public function priceStrategy()
    {
        return $this->belongsTo(PriceStrategy::class);
    }

    /**
     * 关联损耗单
     */
    public function lossOrder()
    {
        return $this->belongsTo(LossOrder::class);
    }

    /**
     * 关联退货明细
     */
    public function returnItems()
    {
        return $this->hasMany(PurchaseReturnItem::class);
    }
}
