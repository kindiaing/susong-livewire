<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * 配送线路模型
 *
 * @property int $id
 * @property string $name 线路名称
 * @property string $code 线路编码
 * @property int|null $warehouse_id 出发仓库ID
 * @property int|null $default_driver_id 默认司机ID
 * @property int|null $default_vehicle_id 默认车辆ID
 * @property string $color 地图显示颜色
 * @property Carbon $departure_time 默认出发时间
 * @property int|null $estimated_duration 预计总时长（分钟）
 * @property string|null $estimated_distance 预计总里程（公里）
 * @property string|null $description 描述
 * @property int $sort 排序
 * @property int $status 状态：0停用，1启用
 * @property string|null $remark 备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class DeliveryRoute extends Model
{
    use SoftDeletes;

    // 状态常量
    public const STATUS_ENABLED = 1;
    public const STATUS_DISABLED = 0;

    protected $fillable = [
        'name',
        'code',
        'warehouse_id',
        'default_driver_id',
        'default_vehicle_id',
        'color',
        'departure_time',
        'estimated_duration',
        'estimated_distance',
        'description',
        'sort',
        'status',
        'remark',
    ];

    protected function casts(): array
    {
        return [
            'warehouse_id' => 'integer',
            'default_driver_id' => 'integer',
            'default_vehicle_id' => 'integer',
            'departure_time' => 'datetime:H:i:s',
            'estimated_duration' => 'integer',
            'estimated_distance' => 'decimal:2',
            'sort' => 'integer',
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
            self::STATUS_DISABLED => '停用',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusMap()[$this->status] ?? '未知';
    }

    /**
     * 关联线路明细（商家列表）
     */
    public function stops()
    {
        return $this->hasMany(DeliveryRouteStop::class, 'route_id')->orderBy('sequence_no');
    }

    /**
     * 关联商家（旧方式，兼容）
     */
    public function merchants()
    {
        return $this->hasMany(Merchant::class);
    }

    /**
     * 关联配送任务
     */
    public function deliveryTasks()
    {
        return $this->hasMany(DeliveryTask::class, 'route_id');
    }

    /**
     * 关联默认司机
     */
    public function defaultDriver()
    {
        return $this->belongsTo(Driver::class, 'default_driver_id');
    }

    /**
     * 关联默认车辆
     */
    public function defaultVehicle()
    {
        return $this->belongsTo(Vehicle::class, 'default_vehicle_id');
    }

    /**
     * 作用域：启用
     */
    public function scopeEnabled($query)
    {
        return $query->where('status', self::STATUS_ENABLED);
    }

    /**
     * 作用域：按排序
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort')->orderBy('id');
    }
}
