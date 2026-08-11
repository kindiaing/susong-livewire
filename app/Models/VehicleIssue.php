<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * 车辆故障记录模型
 *
 * @property int $id
 * @property int $vehicle_id 车辆ID
 * @property int|null $task_id 关联任务ID
 * @property string|null $issue_type 故障类型
 * @property string $description 描述
 * @property array|null $photos 故障照片
 * @property Carbon|null $reported_at 上报时间
 * @property int|null $reported_by 上报人ID
 * @property Carbon|null $resolved_at 解决时间
 * @property int|null $resolved_by 处理人ID
 * @property string|null $impact_type 影响类型
 * @property string|null $impact_desc 影响描述
 * @property int $status 状态：1处理中 2已解决 3已关闭
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class VehicleIssue extends Model
{
    // 状态常量
    public const STATUS_OPEN = 1;       // 处理中
    public const STATUS_RESOLVED = 2;   // 已解决
    public const STATUS_CLOSED = 3;      // 已关闭

    // 故障类型常量
    public const TYPE_BREAKDOWN = 'breakdown';
    public const TYPE_ACCIDENT = 'accident';
    public const TYPE_TIRE = 'tire';
    public const TYPE_BATTERY = 'battery';
    public const TYPE_ENGINE = 'engine';
    public const TYPE_OTHER = 'other';

    protected $fillable = [
        'vehicle_id',
        'task_id',
        'issue_type',
        'description',
        'photos',
        'reported_at',
        'reported_by',
        'resolved_at',
        'resolved_by',
        'impact_type',
        'impact_desc',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'vehicle_id' => 'integer',
            'task_id' => 'integer',
            'photos' => 'array',
            'reported_at' => 'datetime',
            'reported_by' => 'integer',
            'resolved_at' => 'datetime',
            'resolved_by' => 'integer',
            'status' => 'integer',
        ];
    }

    /**
     * 状态映射
     */
    public static function statusMap(): array
    {
        return [
            self::STATUS_OPEN => '处理中',
            self::STATUS_RESOLVED => '已解决',
            self::STATUS_CLOSED => '已关闭',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusMap()[$this->status] ?? '未知';
    }

    /**
     * 故障类型映射
     */
    public static function issueTypeMap(): array
    {
        return [
            self::TYPE_BREAKDOWN => '抛锚',
            self::TYPE_ACCIDENT => '事故',
            self::TYPE_TIRE => '轮胎',
            self::TYPE_BATTERY => '电瓶',
            self::TYPE_ENGINE => '发动机',
            self::TYPE_OTHER => '其他',
        ];
    }

    public function getIssueTypeLabelAttribute(): string
    {
        return self::issueTypeMap()[$this->issue_type] ?? '未知';
    }

    // ========== 关联 ==========

    /**
     * 关联车辆
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * 关联配送任务
     */
    public function deliveryTask()
    {
        return $this->belongsTo(DeliveryTask::class, 'task_id');
    }

    /**
     * 关联上报人
     */
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    /**
     * 关联处理人
     */
    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    // ========== 作用域 ==========

    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }
}
