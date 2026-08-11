<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * 线路明细模型 — 商家列表（核心排序表）
 *
 * @property int $id
 * @property int $route_id 所属线路ID
 * @property int $merchant_id 商家ID
 * @property int $sequence_no 顺序号
 * @property string|null $address 配送地址（冗余）
 * @property string|null $latitude 纬度
 * @property string|null $longitude 经度
 * @property int $default_service_time 默认停留时间（分钟）
 * @property int $is_active 是否启用：0停用 1启用
 * @property string|null $remark 备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class DeliveryRouteStop extends Model
{
    protected $fillable = [
        'route_id',
        'merchant_id',
        'sequence_no',
        'address',
        'latitude',
        'longitude',
        'default_service_time',
        'is_active',
        'remark',
    ];

    protected function casts(): array
    {
        return [
            'route_id' => 'integer',
            'merchant_id' => 'integer',
            'sequence_no' => 'integer',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'default_service_time' => 'integer',
            'is_active' => 'integer',
        ];
    }

    // ========== 关联 ==========

    /**
     * 关联线路
     */
    public function route()
    {
        return $this->belongsTo(DeliveryRoute::class, 'route_id');
    }

    /**
     * 关联商家
     */
    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    // ========== 作用域 ==========

    /**
     * 作用域：启用
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * 作用域：按顺序
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sequence_no');
    }

    /**
     * 重新排列某线路的所有 sequence_no，使其从 1 开始连续
     * 在删除/插入/拖拽后调用
     */
    public static function resequence(int $routeId): void
    {
        $stops = static::where('route_id', $routeId)
            ->orderBy('sequence_no')
            ->get();

        $seq = 1;
        foreach ($stops as $stop) {
            if ($stop->sequence_no !== $seq) {
                $stop->update(['sequence_no' => $seq]);
            }
            $seq++;
        }
    }

    /**
     * 批量更新排序（拖拽排序）
     * 接收新的顺序数组 [merchant_id => sequence_no, ...]
     */
    public static function batchReorder(int $routeId, array $orderedMerchantIds): void
    {
        foreach ($orderedMerchantIds as $seq => $merchantId) {
            static::where('route_id', $routeId)
                ->where('merchant_id', $merchantId)
                ->update(['sequence_no' => $seq + 1]);
        }
    }
}
