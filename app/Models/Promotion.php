<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 运营主推模型
 *
 * @property mixed $type 类型：1主推商品2主推品类
 * @property mixed $target_id 目标ID
 * @property mixed $sort 排序
 * @property mixed $start_at 开始时间
 * @property mixed $end_at 结束时间
 * @property mixed $status 状态
 */
class Promotion extends Model
{
    // 类型常量
    public const TYPE_PRODUCT = 1;
    public const TYPE_CATEGORY = 2;

    // 状态常量
    public const STATUS_DISABLED = 0;
    public const STATUS_ENABLED = 1;

    protected $fillable = [
        'type',
        'target_id',
        'sort',
        'start_at',
        'end_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'type' => 'integer',
            'target_id' => 'integer',
            'sort' => 'integer',
            'status' => 'integer',
            'start_at' => 'datetime',
            'end_at' => 'datetime',
        ];
    }

    /**
     * 类型映射
     */
    public static function typeMap(): array
    {
        return [
            self::TYPE_PRODUCT => '主推商品',
            self::TYPE_CATEGORY => '主推品类',
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
     * 作用域：启用
     */
    public function scopeEnabled($query)
    {
        return $query->where('status', self::STATUS_ENABLED);
    }

    /**
     * 作用域：生效中
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ENABLED)
            ->where('start_at', '<=', now())
            ->where('end_at', '>=', now());
    }
}
