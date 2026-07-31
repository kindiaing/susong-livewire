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
 * @property string|null $vehicle_type 车辆类型
 * @property int $is_cold_chain 是否冷链：0否，1是
 * @property int $status 状态：0禁用，1启用
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Vehicle extends Model
{
    use SoftDeletes;

    // 状态常量（统一：0禁用，1启用）
    public const STATUS_ENABLED = 1;
    public const STATUS_DISABLED = 0;

    protected $fillable = [
        'plate_number',
        'vehicle_type',
        'is_cold_chain',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'is_cold_chain' => 'integer',
            'status' => 'integer',
        ];
    }

    /**
     * 状态映射
     */
    public static function statusMap(): array
    {
        return [
            self::STATUS_ENABLED => '启用',
            self::STATUS_DISABLED => '禁用',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusMap()[$this->status] ?? '未知';
    }

    public function getColdChainLabelAttribute(): string
    {
        return $this->is_cold_chain ? '冷链' : '非冷链';
    }

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
     * 作用域：启用
     */
    public function scopeEnabled($query)
    {
        return $query->where('status', self::STATUS_ENABLED);
    }
}
