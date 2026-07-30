<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * 智能补货提醒规则模型
 *
 * @property int $id
 * @property int $merchant_id 商家ID
 * @property int $sku_id SKU ID
 * @property int $threshold_quantity 触发提醒的库存阈值
 * @property int $remind_cycle 提醒周期：1每日，2每周，3仅一次
 * @property Carbon|null $last_reminded_at 上次提醒时间
 * @property int $status 状态：0禁用，1启用
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class RestockReminder extends Model
{
    protected $table = 'restock_reminders';

    // 提醒周期常量
    public const CYCLE_DAILY = 1;
    public const CYCLE_WEEKLY = 2;
    public const CYCLE_ONCE = 3;

    // 状态常量
    public const STATUS_DISABLED = 0;
    public const STATUS_ENABLED = 1;

    protected $fillable = [
        'merchant_id',
        'sku_id',
        'threshold_quantity',
        'remind_cycle',
        'last_reminded_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'merchant_id' => 'integer',
            'sku_id' => 'integer',
            'threshold_quantity' => 'integer',
            'remind_cycle' => 'integer',
            'last_reminded_at' => 'datetime',
            'status' => 'integer',
        ];
    }

    /**
     * 提醒周期映射
     */
    public static function remindCycleMap(): array
    {
        return [
            self::CYCLE_DAILY => '每日',
            self::CYCLE_WEEKLY => '每周',
            self::CYCLE_ONCE => '仅一次',
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

    public function getRemindCycleLabelAttribute(): string
    {
        return self::remindCycleMap()[$this->remind_cycle] ?? '未知';
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusMap()[$this->status] ?? '未知';
    }

    /**
     * 关联商家
     */
    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    /**
     * 关联 SKU
     */
    public function sku()
    {
        return $this->belongsTo(Sku::class);
    }

    /**
     * 作用域：启用
     */
    public function scopeEnabled($query)
    {
        return $query->where('status', self::STATUS_ENABLED);
    }
}
