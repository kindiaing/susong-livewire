<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 冷链温度记录模型
 *
 * @property mixed $delivery_task_id 配送任务ID
 * @property mixed $temperature 温度值
 * @property mixed $recorded_at 记录时间
 */
class Temperature extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'delivery_task_id',
        'temperature',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'delivery_task_id' => 'integer',
            'temperature' => 'integer',
            'recorded_at' => 'datetime',
        ];
    }

}
