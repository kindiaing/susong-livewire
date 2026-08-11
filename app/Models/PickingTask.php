<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 拣货任务模型
 *
 * @property mixed $task_no 任务编号
 * @property mixed $warehouse_id 仓库ID
 * @property mixed $route_id 所属配送线路ID
 * @property mixed $delivery_date 送达日期
 * @property mixed $picker_id 拣货员ID
 * @property mixed $batch 配送批次：1上午2下午
 * @property mixed $status 状态：1待分配2拣货中3已完成
 * @property mixed $total_skus SKU种数汇总
 * @property mixed $total_quantity 总数量汇总
 * @property mixed $started_at 开始时间
 * @property mixed $completed_at 完成时间
 */
class PickingTask extends Model
{

    protected $fillable = [
        'task_no',
        'warehouse_id',
        'route_id',
        'delivery_date',
        'picker_id',
        'batch',
        'status',
        'total_skus',
        'total_quantity',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'warehouse_id' => 'integer',
            'route_id' => 'integer',
            'delivery_date' => 'date',
            'picker_id' => 'integer',
            'batch' => 'integer',
            'status' => 'integer',
            'total_skus' => 'integer',
            'total_quantity' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public const STATUS_PENDING = 1;
    public const STATUS_PICKING = 2;
    public const STATUS_COMPLETED = 3;

    public static function statusMap(): array
    {
        return [
            self::STATUS_PENDING => '待分配',
            self::STATUS_PICKING => '拣货中',
            self::STATUS_COMPLETED => '已完成',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusMap()[$this->status] ?? '未知';
    }

    /**
     * 关联仓库
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * 关联配送线路
     */
    public function deliveryRoute()
    {
        return $this->belongsTo(DeliveryRoute::class, 'route_id');
    }

    /**
     * 关联拣货员
     */
    public function picker()
    {
        return $this->belongsTo(User::class, 'picker_id');
    }

    /**
     * 关联拣货明细
     */
    public function items()
    {
        return $this->hasMany(PickingTaskItem::class);
    }

    /**
     * 作用域：按状态
     */
    public function scopeByStatus($query, int $status)
    {
        return $query->where('status', $status);
    }

    /**
     * 作用域：按线路+日期
     */
    public function scopeByRouteAndDate($query, int $routeId, string $date)
    {
        return $query->where('route_id', $routeId)->where('delivery_date', $date);
    }

    /**
     * 作用域：待分配
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}
