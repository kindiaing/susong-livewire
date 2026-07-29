<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 拣货任务明细模型
 *
 * @property mixed $picking_task_id 拣货任务ID
 * @property mixed $order_id 订单ID
 * @property mixed $order_item_id 订单明细ID
 * @property mixed $sku_id SKU ID
 * @property mixed $required_quantity 需求数量
 * @property mixed $picked_quantity 实际拣货数量
 * @property mixed $status 状态：1待拣货2已拣货3差异
 */
class PickingTaskItem extends Model
{

    protected $fillable = [
        'picking_task_id',
        'order_id',
        'order_item_id',
        'sku_id',
        'required_quantity',
        'picked_quantity',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'picking_task_id' => 'integer',
            'order_id' => 'integer',
            'order_item_id' => 'integer',
            'sku_id' => 'integer',
            'required_quantity' => 'integer',
            'picked_quantity' => 'integer',
            'status' => 'integer',
        ];
    }

}
