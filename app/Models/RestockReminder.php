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
}
