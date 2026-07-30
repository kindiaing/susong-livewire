<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * 司机车辆绑定模型
 *
 * @property int $id
 * @property int $driver_id 司机ID
 * @property int $vehicle_id 车辆ID
 * @property int $is_default 是否默认：0否，1是
 * @property Carbon|null $bound_at 绑定时间
 * @property Carbon|null $unbound_at 解绑时间
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class DriverVehicle extends Model
{
    protected $table = 'driver_vehicles';

    protected $fillable = [
        'driver_id',
        'vehicle_id',
        'is_default',
        'bound_at',
        'unbound_at',
    ];

    protected function casts(): array
    {
        return [
            'driver_id' => 'integer',
            'vehicle_id' => 'integer',
            'is_default' => 'integer',
            'bound_at' => 'datetime',
            'unbound_at' => 'datetime',
        ];
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
     * 作用域：当前有效绑定（未解绑）
     */
    public function scopeActive($query)
    {
        return $query->whereNull('unbound_at');
    }

    /**
     * 作用域：默认车辆
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', 1);
    }
}
