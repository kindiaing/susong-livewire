<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * 仓库模型
 *
 * @property int $id
 * @property string $name 仓库名称
 * @property int $type 类型：1总仓，2前置仓
 * @property int $is_cold_chain 是否冷链：0否，1是
 * @property string|null $address 地址
 * @property int $status 状态：0禁用，1启用
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Warehouse extends Model
{
    use SoftDeletes;

    // 类型常量
    public const TYPE_GENERAL = 1;
    public const TYPE_FRONT = 2;

    // 状态常量
    public const STATUS_DISABLED = 0;
    public const STATUS_ENABLED = 1;

    protected $fillable = [
        'name',
        'type',
        'is_cold_chain',
        'address',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'type' => 'integer',
            'is_cold_chain' => 'integer',
            'status' => 'integer',
        ];
    }

    /**
     * 类型映射
     */
    public static function typeMap(): array
    {
        return [
            self::TYPE_GENERAL => '总仓',
            self::TYPE_FRONT => '前置仓',
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

    public function getTypeLabelAttribute(): string
    {
        return self::typeMap()[$this->type] ?? '未知';
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusMap()[$this->status] ?? '未知';
    }

    /**
     * 关联库存
     */
    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    /**
     * 关联库存日志
     */
    public function inventoryLogs()
    {
        return $this->hasMany(InventoryLog::class);
    }

    /**
     * 关联拣货任务
     */
    public function pickingTasks()
    {
        return $this->hasMany(PickingTask::class);
    }

    /**
     * 关联损耗单
     */
    public function lossOrders()
    {
        return $this->hasMany(LossOrder::class);
    }

    /**
     * 关联采购退货
     */
    public function purchaseReturns()
    {
        return $this->hasMany(PurchaseReturn::class);
    }

    /**
     * 作用域：启用
     */
    public function scopeEnabled($query)
    {
        return $query->where('status', self::STATUS_ENABLED);
    }

    /**
     * 作用域：冷链仓库
     */
    public function scopeColdChain($query)
    {
        return $query->where('is_cold_chain', 1);
    }
}
