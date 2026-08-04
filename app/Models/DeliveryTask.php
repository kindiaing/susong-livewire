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
    // 状态常量
    public const STATUS_PENDING = 1;
    public const STATUS_DELIVERING = 2;
    public const STATUS_COMPLETED = 3;

    // 配送批次常量
    public const BATCH_MORNING = 1;
    public const BATCH_AFTERNOON = 2;

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

    /**
     * 状态映射
     */
    public static function statusMap(): array
    {
        return [
            self::STATUS_PENDING => '待配送',
            self::STATUS_DELIVERING => '配送中',
            self::STATUS_COMPLETED => '任务完成',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusMap()[$this->status] ?? '未知';
    }

    /**
     * 关联配送线路
     */
    public function deliveryRoute()
    {
        return $this->belongsTo(DeliveryRoute::class);
    }

    /**
     * 关联司机
     */
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * 关联车辆
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * 关联配送任务订单
     */
    public function taskOrders()
    {
        return $this->hasMany(DeliveryTaskOrder::class);
    }

    /**
     * 关联签收记录
     */
    public function signatures()
    {
        return $this->hasMany(Signature::class);
    }

    /**
     * 关联温度记录
     */
    public function temperatures()
    {
        return $this->hasMany(Temperature::class);
    }

    /**
     * 关联配送轨迹
     */
    public function tracks()
    {
        return $this->hasMany(DeliveryTrack::class);
    }

    /**
     * 作用域：按状态
     */
    public function scopeByStatus($query, int $status)
    {
        return $query->where('status', $status);
    }

    /**
     * 作用域：按配送批次
     */
    public function scopeByBatch($query, int $batch)
    {
        return $query->where('batch', $batch);
    }

    /**
     * 作用域：待配送
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}
