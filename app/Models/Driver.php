<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * 司机模型
 *
 * @property int $id
 * @property int|null $user_id 关联用户ID
 * @property string $name 姓名
 * @property string $phone 手机号
 * @property string|null $id_card 身份证号
 * @property int $online_status 在线状态：0离线，1在线
 * @property int $status 状态：1启用，2禁用
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Driver extends Model
{
    use SoftDeletes;

    // 在线状态常量
    public const ONLINE_OFFLINE = 0;
    public const ONLINE_ONLINE = 1;

    // 状态常量
    public const STATUS_ENABLED = 1;
    public const STATUS_DISABLED = 2;

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'id_card',
        'online_status',
        'status',
        'remark',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'online_status' => 'integer',
            'status' => 'integer',
        ];
    }

    /**
     * 在线状态映射
     */
    public static function onlineStatusMap(): array
    {
        return [
            self::ONLINE_OFFLINE => '离线',
            self::ONLINE_ONLINE => '在线',
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

    public function getOnlineStatusLabelAttribute(): string
    {
        return self::onlineStatusMap()[$this->online_status] ?? '未知';
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusMap()[$this->status] ?? '未知';
    }

    /**
     * 关联用户
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 关联车辆（多对多）
     */
    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class, 'driver_vehicles')
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
     * 获取默认车辆
     */
    public function defaultVehicle()
    {
        return $this->belongsToMany(Vehicle::class, 'driver_vehicles')
            ->wherePivot('is_default', 1)
            ->first();
    }

    /**
     * 作用域：启用
     */
    public function scopeEnabled($query)
    {
        return $query->where('status', self::STATUS_ENABLED);
    }

    /**
     * 作用域：在线
     */
    public function scopeOnline($query)
    {
        return $query->where('online_status', self::ONLINE_ONLINE);
    }
}
