<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 改价记录模型
 *
 * @property mixed $source_type 来源：1促销2临时改价3手动改价
 * @property mixed $source_id 来源策略ID
 * @property mixed $target_type 作用单据类型：1订单2采购单3应收4应付
 * @property mixed $target_id 单据ID
 * @property mixed $target_item_id 单据明细ID
 * @property mixed $original_price 改价前单价
 * @property mixed $new_price 改价后单价
 * @property mixed $quantity 数量
 * @property mixed $amount_diff 金额差异
 * @property mixed $operator_id 操作人ID
 * @property mixed $role_ids 操作人角色ID数组
 * @property mixed $reason 改价原因
 * @property mixed $before_data 改价前数据快照
 * @property mixed $after_data 改价后数据快照
 */
class PriceChangeLog extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'source_type',
        'source_id',
        'target_type',
        'target_id',
        'target_item_id',
        'original_price',
        'new_price',
        'quantity',
        'amount_diff',
        'operator_id',
        'role_ids',
        'reason',
        'before_data',
        'after_data',
    ];

    protected function casts(): array
    {
        return [
            'source_type' => 'integer',
            'source_id' => 'integer',
            'target_type' => 'integer',
            'target_id' => 'integer',
            'target_item_id' => 'integer',
            'original_price' => 'integer',
            'new_price' => 'integer',
            'quantity' => 'integer',
            'amount_diff' => 'integer',
            'operator_id' => 'integer',
            'role_ids' => 'array',
            'before_data' => 'array',
            'after_data' => 'array',
        ];
    }

}
