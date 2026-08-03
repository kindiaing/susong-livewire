<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 供应商结算明细模型
 *
 * @property mixed $supplier_settlement_id 结算单ID
 * @property mixed $purchase_order_id 采购单ID
 * @property mixed $amount 金额
 */
class SupplierSettlementItem extends Model
{

    protected $fillable = [
        'supplier_settlement_id',
        'purchase_order_id',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'supplier_settlement_id' => 'integer',
            'purchase_order_id' => 'integer',
            'amount' => 'integer',
        ];
    }

    /**
     * 关联结算单
     */
    public function settlement()
    {
        return $this->belongsTo(SupplierSettlement::class, 'supplier_settlement_id');
    }

    /**
     * 关联采购单
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
}
