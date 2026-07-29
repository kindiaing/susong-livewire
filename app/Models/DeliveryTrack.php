<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 配送轨迹模型
 *
 * @property mixed $delivery_task_id 配送任务ID
 * @property mixed $driver_id 司机ID
 * @property mixed $latitude 纬度
 * @property mixed $longitude 经度
 * @property mixed $location_desc 位置描述
 * @property mixed $reported_at 上报时间
 */
class DeliveryTrack extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'delivery_task_id',
        'driver_id',
        'latitude',
        'longitude',
        'location_desc',
        'reported_at',
    ];

    protected function casts(): array
    {
        return [
            'delivery_task_id' => 'integer',
            'driver_id' => 'integer',
            'latitude' => 'integer',
            'longitude' => 'integer',
            'reported_at' => 'datetime',
        ];
    }

}
