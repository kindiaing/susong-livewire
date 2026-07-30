<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 差异处理模型
 *
 * @property mixed $discrepancy_no 差异单号
 * @property mixed $order_id 关联订单ID
 * @property mixed $order_item_id 关联订单明细ID
 * @property mixed $stage 差异环节：1拣货2配送3实收
 * @property mixed $type 差异类型：1少收2拒收3残次4其他
 * @property mixed $expected_quantity 预期数量
 * @property mixed $actual_quantity 实际数量
 * @property mixed $quantity_diff 数量差异
 * @property mixed $amount_diff 金额差异
 * @property mixed $reason 差异原因
 * @property mixed $evidence_urls 凭证图片数组
 * @property mixed $responsible_party 责任方：1供应商2平台3司机4商家
 * @property mixed $decision 处理决策：1补货2退款3扣款4报损5不计
 * @property mixed $status 状态：1待处理2处理中3已处理4已关闭5争议中
 * @property mixed $handler_id 处理人ID
 * @property mixed $handled_at 处理时间
 * @property mixed $is_amount_adjusted 是否已调整金额
 * @property mixed $approval_status 审核状态：1待审核2已通过3已拒绝
 * @property mixed $remark 备注
 */
class Discrepancy extends Model
{

    protected $fillable = [
        'discrepancy_no',
        'order_id',
        'order_item_id',
        'stage',
        'type',
        'expected_quantity',
        'actual_quantity',
        'quantity_diff',
        'amount_diff',
        'reason',
        'evidence_urls',
        'responsible_party',
        'decision',
        'status',
        'handler_id',
        'handled_at',
        'is_amount_adjusted',
        'approval_status',
        'remark',
    ];

    protected function casts(): array
    {
        return [
            'order_id' => 'integer',
            'order_item_id' => 'integer',
            'stage' => 'integer',
            'type' => 'integer',
            'expected_quantity' => 'integer',
            'actual_quantity' => 'integer',
            'quantity_diff' => 'integer',
            'amount_diff' => 'integer',
            'responsible_party' => 'integer',
            'decision' => 'integer',
            'status' => 'integer',
            'handler_id' => 'integer',
            'handled_at' => 'datetime',
            'is_amount_adjusted' => 'integer',
            'approval_status' => 'integer',
            'evidence_urls' => 'array',
        ];
    }

}
