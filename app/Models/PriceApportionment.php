<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 费用均摊模型
 *
 * @property mixed $target_type 单据类型：1订单2采购单
 * @property mixed $target_id 单据ID
 * @property mixed $target_item_id 单据明细ID
 * @property mixed $apportion_type 均摊类型：1整单改价2促销差价3费用4运费
 * @property mixed $amount 均摊金额
 * @property mixed $apportion_mode 均摊方式：1自动均摊2手动均摊
 * @property mixed $manual_amount 手动均摊金额
 * @property mixed $operator_id 操作人ID
 * @property mixed $approval_status 审核状态：1待审核2已通过3已拒绝
 */
class PriceApportionment extends Model
{

    protected $fillable = [
        'target_type',
        'target_id',
        'target_item_id',
        'apportion_type',
        'amount',
        'apportion_mode',
        'manual_amount',
        'operator_id',
        'approval_status',
    ];

    protected function casts(): array
    {
        return [
            'target_type' => 'integer',
            'target_id' => 'integer',
            'target_item_id' => 'integer',
            'apportion_type' => 'integer',
            'amount' => 'integer',
            'apportion_mode' => 'integer',
            'manual_amount' => 'integer',
            'operator_id' => 'integer',
            'approval_status' => 'integer',
        ];
    }

}
