<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * 车辆模型
 *
 * @property int $id
 * @property string $plate_number 车牌号
 * @property string|null $name 车辆名称
 * @property string $type 类型：van/truck/refrigerated/motorcycle
 * @property string|null $capacity_kg 载重（公斤）
 * @property string|null $capacity_volume 容积（立方米）
 * @property int $is_cold_chain 是否冷链：0否 1是
 * @property int $status 状态：1可用 2维修中 3报废
 * @property Carbon|null $last_maintenance_date 上次保养日期
 * @property Carbon|null $next_maintenance_date 下次保养日期
 * @property string|null $remark 备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Vehicle extends Model
{
    use SoftDeletes;

    // 状态常量
    public const STATUS_ACTIVE = 1;       // 可用
    public const STATUS_MAINTENANCE = 2;  // 维修中
    public const STATUS_RETIRED = 3;      // 报废

    // 车辆类型常量
    public const TYPE_VAN = 'van';
    public const TYPE_TRUCK = 'truck';
    public const TYPE_REFRIGERATED = 'refrigerated';
    public const TYPE_MOTORCYCLE = 'motorcycle';

    protected $fillable = [
        'plate_number',
        'name',
        'type',
        'capacity_kg',
        'capacity_volume',
        'is_cold_chain',
        'status',
        'last_maintenance_date',
        'next_maintenance_date',
        'remark',
    ];

    protected function casts(): array
    {
        return [
            'capacity_kg' => 'decimal:2',
            'capacity_volume' => 'decimal:2',
            'is_cold_chain' => 'integer',
            'status' => 'integer',
            'last_maintenance_date' => 'date',
            'next_maintenance_date' => 'date',
        ];
    }

    /**
     * 状态映射
     */
    public static function statusMap(): array
    {
        return [
            self::STATUS_ACTIVE => '可用',
            self::STATUS_MAINTENANCE => '维修中',
            self::STATUS_RETIRED => '报废',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusMap()[$this->status] ?? '未知';
    }

    /**
     * 车辆类型映射
     */
    public static function typeMap(): array
    {
        return [
            self::TYPE_VAN => '厢式货车',
            self::TYPE_TRUCK => '卡车',
            self::TYPE_REFRIGERATED => '冷藏车',
            self::TYPE_MOTORCYCLE => '三轮摩托车',
        ];
    }

    public function getTypeLabelAttribute(): string
    {
        return self::typeMap()[$this->type] ?? '未知';
    }

    public function getColdChainLabelAttribute(): string
    {
        return $this->is_cold_chain ? '冷链' : '非冷链';
    }

    // ========== 关联 ==========

    /**
     * 关联司机（多对多）
     */
    public function drivers()
    {
        return $this->belongsToMany(Driver::class, 'driver_vehicles')
            ->withPivot(['is_default', 'bound_at', 'unbound_at'])
            ->withTimestamps();
    }

    /**
     * 关联配送任务
     */
    public function deliveryTasks()
    {
        return $this->hasMany(DeliveryTask::class);
    }

    /**
     * 关联故障记录
     */
    public function issues()
    {
        return $this->hasMany(VehicleIssue::class);
    }

    // ========== 作用域 ==========

    /**
     * 作用域：可用
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
}
