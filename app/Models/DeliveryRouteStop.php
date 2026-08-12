<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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
     *
     * 两阶段更新避免 unique(route_id, sequence_no) 约束冲突：
     * 1) 先把所有 sequence_no 偏移到安全区间（+1000）
     * 2) 再按顺序从 1 重新赋值
     */
    public static function resequence(int $routeId): void
    {
        DB::transaction(function () use ($routeId) {
            // 阶段1：偏移到安全区间
            static::where('route_id', $routeId)->update(['sequence_no' => DB::raw('sequence_no + 1000')]);

            // 阶段2：按偏移后的顺序重新赋值
            $stops = static::where('route_id', $routeId)
                ->orderBy('sequence_no')
                ->get(['id']);

            $seq = 1;
            foreach ($stops as $stop) {
                $stop->update(['sequence_no' => $seq]);
                $seq++;
            }
        });
    }

    /**
     * 批量更新排序（拖拽排序）
     * 接收新的顺序数组 [0 => merchant_id, 1 => merchant_id, ...]
     *
     * 两阶段更新避免 unique(route_id, sequence_no) 约束冲突：
     * 1) 先把所有 sequence_no 偏移到安全区间（+1000）
     * 2) 再按新顺序从 1 重新赋值
     */
    public static function batchReorder(int $routeId, array $orderedMerchantIds): void
    {
        DB::transaction(function () use ($routeId, $orderedMerchantIds) {
            // 阶段1：偏移到安全区间，解除唯一约束
            static::where('route_id', $routeId)->update(['sequence_no' => DB::raw('sequence_no + 1000')]);

            // 阶段2：按新顺序赋值
            $seq = 1;
            foreach ($orderedMerchantIds as $merchantId) {
                static::where('route_id', $routeId)
                    ->where('merchant_id', $merchantId)
                    ->update(['sequence_no' => $seq]);
                $seq++;
            }
        });
    }
}
