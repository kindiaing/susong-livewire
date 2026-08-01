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
 * @property string|null $description 描述
 * @property int $sort 排序
 * @property int $status 状态：0禁用，1启用
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class DeliveryRoute extends Model
{
    use SoftDeletes;

    // 状态常量（统一：0禁用，1启用）
    public const STATUS_ENABLED = 1;
    public const STATUS_DISABLED = 0;

    protected $fillable = [
        'name',
        'description',
        'sort',
        'status',
    ];

    protected function casts(): array
    {
        return [
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
            self::STATUS_DISABLED => '禁用',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusMap()[$this->status] ?? '未知';
    }

    /**
     * 关联商家
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
        return $this->hasMany(DeliveryTask::class);
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
