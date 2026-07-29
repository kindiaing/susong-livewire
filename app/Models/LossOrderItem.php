<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 损耗单明细模型
 *
 * @property mixed $loss_order_id 损耗单ID
 * @property mixed $sku_id SKU ID
 * @property mixed $loss_type 损耗类型
 * @property mixed $quantity 损耗数量
 * @property mixed $cost_price SKU成本价快照
 * @property mixed $loss_amount 损耗金额
 * @property mixed $responsible_party 责任方：1平台2供应商
 * @property mixed $supplier_id 供应商ID
 * @property mixed $reason 明细损耗原因
 * @property mixed $evidence_urls 凭证图片数组
 */
class LossOrderItem extends Model
{

    protected $fillable = [
        'loss_order_id',
        'sku_id',
        'loss_type',
        'quantity',
        'cost_price',
        'loss_amount',
        'responsible_party',
        'supplier_id',
        'reason',
        'evidence_urls',
    ];

    protected function casts(): array
    {
        return [
            'loss_order_id' => 'integer',
            'sku_id' => 'integer',
            'loss_type' => 'integer',
            'quantity' => 'integer',
            'cost_price' => 'integer',
            'loss_amount' => 'integer',
            'responsible_party' => 'integer',
            'supplier_id' => 'integer',
            'evidence_urls' => 'array',
        ];
    }

}
