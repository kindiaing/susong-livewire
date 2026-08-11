<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * 配送抵达时间流水模型
 *
 * @property int $id
 * @property int $task_id 配送任务ID
 * @property int|null $sequence_id 关联的配送顺序表ID
 * @property int $merchant_id 商家ID
 * @property string $event_type 事件类型
 * @property Carbon $event_time 事件发生时间
 * @property string|null $gps_latitude 纬度
 * @property string|null $gps_longitude 经度
 * @property string|null $gps_accuracy 精度（米）
 * @property string $source 来源
 * @property int|null $operator_id 操作人ID
 * @property array|null $extra_data 额外数据
 * @property Carbon|null $created_at
 */
class DeliveryArrivalLog extends Model
{
    public const UPDATED_AT = null;

    // 事件类型常量
    public const EVENT_ARRIVAL = 'arrival';
    public const EVENT_DEPARTURE = 'departure';
    public const EVENT_DELIVERED = 'delivered';
    public const EVENT_SKIPPED = 'skipped';
    public const EVENT_GPS_ENTER = 'gps_enter';
    public const EVENT_GPS_LEAVE = 'gps_leave';

    // 来源常量
    public const SOURCE_DRIVER = 'driver';
    public const SOURCE_GPS_AUTO = 'gps_auto';
    public const SOURCE_SYSTEM = 'system';
    public const SOURCE_ADMIN = 'admin';

    protected $fillable = [
        'task_id',
        'sequence_id',
        'merchant_id',
        'event_type',
        'event_time',
        'gps_latitude',
        'gps_longitude',
        'gps_accuracy',
        'source',
        'operator_id',
        'extra_data',
    ];

    protected function casts(): array
    {
        return [
            'task_id' => 'integer',
            'sequence_id' => 'integer',
            'merchant_id' => 'integer',
            'event_time' => 'datetime',
            'gps_latitude' => 'decimal:8',
            'gps_longitude' => 'decimal:8',
            'gps_accuracy' => 'decimal:2',
            'operator_id' => 'integer',
            'extra_data' => 'array',
        ];
    }

    /**
     * 事件类型映射
     */
    public static function eventTypeMap(): array
    {
        return [
            self::EVENT_ARRIVAL => '到达',
            self::EVENT_DEPARTURE => '离开',
            self::EVENT_DELIVERED => '送达',
            self::EVENT_SKIPPED => '跳过',
            self::EVENT_GPS_ENTER => '进入围栏',
            self::EVENT_GPS_LEAVE => '离开围栏',
        ];
    }

    public function getEventTypeLabelAttribute(): string
    {
        return self::eventTypeMap()[$this->event_type] ?? '未知';
    }

    /**
     * 来源映射
     */
    public static function sourceMap(): array
    {
        return [
            self::SOURCE_DRIVER => '司机',
            self::SOURCE_GPS_AUTO => '自动',
            self::SOURCE_SYSTEM => '系统',
            self::SOURCE_ADMIN => '后台',
        ];
    }

    public function getSourceLabelAttribute(): string
    {
        return self::sourceMap()[$this->source] ?? '未知';
    }

    // ========== 关联 ==========

    /**
     * 关联配送任务
     */
    public function task()
    {
        return $this->belongsTo(DeliveryTask::class, 'task_id');
    }

    /**
     * 关联配送顺序
     */
    public function sequence()
    {
        return $this->belongsTo(DeliveryTaskSequence::class, 'sequence_id');
    }

    /**
     * 关联商家
     */
    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}
