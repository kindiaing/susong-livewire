<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * 配送任务模型
 *
 * @property int $id
 * @property string $task_no 任务编号
 * @property int $route_id 所属线路ID
 * @property Carbon $delivery_date 送达日期
 * @property Carbon|null $generated_at 任务生成时间
 * @property int|null $driver_id 司机ID
 * @property int|null $vehicle_id 车辆ID
 * @property int $batch 配送批次：1上午 2下午
 * @property int $status 状态
 * @property Carbon|null $planned_start_time 计划出发时间
 * @property Carbon|null $actual_start_time 实际出发时间
 * @property Carbon|null $actual_complete_time 实际完成时间
 * @property int $total_stops 总配送商家数
 * @property int $completed_stops 已完成商家数
 * @property int $skipped_stops 跳过商家数
 * @property int $total_orders 关联单据总数
 * @property int $has_urgent 是否包含加急
 * @property int $has_important 是否包含重要
 * @property string|null $remark 备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class DeliveryTask extends Model
{
    // 状态常量（tinyint）
    public const STATUS_PENDING = 1;       // 待配送
    public const STATUS_ASSIGNED = 2;      // 已分配
    public const STATUS_IN_PROGRESS = 3;   // 配送中
    public const STATUS_PAUSED = 4;        // 暂停
    public const STATUS_COMPLETED = 5;     // 已完成
    public const STATUS_CANCELLED = 6;     // 已作废

    // 配送批次常量
    public const BATCH_MORNING = 1;
    public const BATCH_AFTERNOON = 2;

    protected $fillable = [
        'task_no',
        'route_id',
        'delivery_date',
        'generated_at',
        'driver_id',
        'vehicle_id',
        'batch',
        'status',
        'planned_start_time',
        'actual_start_time',
        'actual_complete_time',
        'total_stops',
        'completed_stops',
        'skipped_stops',
        'total_orders',
        'has_urgent',
        'has_important',
        'remark',
    ];

    protected function casts(): array
    {
        return [
            'route_id' => 'integer',
            'delivery_date' => 'date',
            'generated_at' => 'datetime',
            'driver_id' => 'integer',
            'vehicle_id' => 'integer',
            'batch' => 'integer',
            'status' => 'integer',
            'planned_start_time' => 'datetime',
            'actual_start_time' => 'datetime',
            'actual_complete_time' => 'datetime',
            'total_stops' => 'integer',
            'completed_stops' => 'integer',
            'skipped_stops' => 'integer',
            'total_orders' => 'integer',
            'has_urgent' => 'integer',
            'has_important' => 'integer',
        ];
    }

    /**
     * 状态映射
     */
    public static function statusMap(): array
    {
        return [
            self::STATUS_PENDING => '待配送',
            self::STATUS_ASSIGNED => '已分配',
            self::STATUS_IN_PROGRESS => '配送中',
            self::STATUS_PAUSED => '暂停',
            self::STATUS_COMPLETED => '已完成',
            self::STATUS_CANCELLED => '已作废',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusMap()[$this->status] ?? '未知';
    }

    /**
     * 批次映射
     */
    public static function batchMap(): array
    {
        return [
            self::BATCH_MORNING => '上午',
            self::BATCH_AFTERNOON => '下午',
        ];
    }

    public function getBatchLabelAttribute(): string
    {
        return self::batchMap()[$this->batch] ?? '未知';
    }

    /**
     * 生成任务编号
     * 格式：T-{线路编码}-{日期}-{序号} 如 T-E01-20260810-001
     */
    public static function generateTaskNo(string $routeCode, ?string $date = null): string
    {
        $date = $date ?? now()->format('Ymd');
        $prefix = "T-{$routeCode}-{$date}-";

        $lastTask = static::where('task_no', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        $seq = 1;
        if ($lastTask) {
            $lastSeq = (int) substr($lastTask->task_no, -3);
            $seq = $lastSeq + 1;
        }

        return $prefix . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    /**
     * 判断是否可以转换到目标状态
     */
    public function canTransitionTo(int $status): bool
    {
        $transitions = [
            self::STATUS_PENDING => [self::STATUS_ASSIGNED, self::STATUS_CANCELLED],
            self::STATUS_ASSIGNED => [self::STATUS_IN_PROGRESS, self::STATUS_PENDING, self::STATUS_CANCELLED],
            self::STATUS_IN_PROGRESS => [self::STATUS_PAUSED, self::STATUS_COMPLETED],
            self::STATUS_PAUSED => [self::STATUS_IN_PROGRESS, self::STATUS_CANCELLED],
        ];

        return in_array($status, $transitions[$this->status] ?? []);
    }

    // ========== 关联 ==========

    /**
     * 关联配送线路
     */
    public function deliveryRoute()
    {
        return $this->belongsTo(DeliveryRoute::class, 'route_id');
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
     * 关联任务明细
     */
    public function details()
    {
        return $this->hasMany(DeliveryTaskDetail::class, 'task_id');
    }

    /**
     * 关联配送顺序表
     */
    public function sequences()
    {
        return $this->hasMany(DeliveryTaskSequence::class, 'task_id')->orderBy('sequence_no');
    }

    /**
     * 关联抵达流水
     */
    public function arrivalLogs()
    {
        return $this->hasMany(DeliveryArrivalLog::class, 'task_id');
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
     * 关联送货单
     */
    public function deliveryNotes()
    {
        return $this->hasMany(DeliveryNote::class, 'task_id');
    }

    // ========== 作用域 ==========

    public function scopeByStatus($query, int $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByBatch($query, int $batch)
    {
        return $query->where('batch', $batch);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeInDeliveryDate($query, string $date)
    {
        return $query->where('delivery_date', $date);
    }
}
