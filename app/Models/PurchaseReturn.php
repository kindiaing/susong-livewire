<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

    use SoftDeletes;
/**
 * 采购退货模型
 *
 * @property mixed $return_no 退货单号
 * @property mixed $purchase_order_id 关联采购单ID
 * @property mixed $supplier_id 供应商ID
 * @property mixed $warehouse_id 出库仓库ID
 * @property mixed $status 状态：1待审核2已审核3已出库4完成9取消
 * @property mixed $total_amount 退货总金额
 * @property mixed $actual_amount 实际出库金额
 * @property mixed $reason 退货原因
 * @property mixed $operator_id 经办人ID
 * @property mixed $audited_by 审核人ID
 * @property mixed $audited_at 审核时间
 * @property mixed $remark 备注
 */
class PurchaseReturn extends Model
{

    protected $fillable = [
        'return_no',
        'purchase_order_id',
        'supplier_id',
        'warehouse_id',
        'status',
        'total_amount',
        'actual_amount',
        'reason',
        'operator_id',
        'audited_by',
        'audited_at',
        'remark',
    ];

    protected function casts(): array
    {
        return [
            'purchase_order_id' => 'integer',
            'supplier_id' => 'integer',
            'warehouse_id' => 'integer',
            'status' => 'integer',
            'total_amount' => 'integer',
            'actual_amount' => 'integer',
            'operator_id' => 'integer',
            'audited_by' => 'integer',
            'audited_at' => 'datetime',
        ];
    }

}
