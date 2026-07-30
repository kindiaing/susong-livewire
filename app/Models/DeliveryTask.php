<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 配送任务模型
 *
 * @property mixed $task_no 任务编号
 * @property mixed $delivery_route_id 线路ID
 * @property mixed $driver_id 司机ID
 * @property mixed $vehicle_id 车辆ID
 * @property mixed $batch 配送批次：1上午2下午
 * @property mixed $status 状态：1待配送2配送中3任务完成
 * @property mixed $planned_at 计划配送时间
 * @property mixed $started_at 开始时间
 * @property mixed $completed_at 完成时间
 */
class DeliveryTask extends Model
{

    protected $fillable = [
        'task_no',
        'delivery_route_id',
        'driver_id',
        'vehicle_id',
        'batch',
        'status',
        'planned_at',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'delivery_route_id' => 'integer',
            'driver_id' => 'integer',
            'vehicle_id' => 'integer',
            'batch' => 'integer',
            'status' => 'integer',
            'planned_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

}
