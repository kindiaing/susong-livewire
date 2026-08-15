<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 授权更正模型
 *
 * @property mixed $order_id 订单ID
 * @property mixed $operator_id 授权人ID
 * @property mixed $type 更正类型
 * @property mixed $reason 更正原因
 * @property mixed $amount 更正金额（厘）
 * @property mixed $status 状态：1待审核2已通过3已拒绝
 * @property mixed $before_data 修改前数据
 * @property mixed $after_data 修改后数据
 * @property mixed $authorized_at 授权时间
 */
class CorrectionAuthorization extends Model
{
    use SoftDeletes;

    // 状态常量
    public const STATUS_PENDING = 1;
    public const STATUS_APPROVED = 2;
    public const STATUS_REJECTED = 3;

    // 类型常量
    public const TYPE_BALANCE = 'balance';
    public const TYPE_CREDIT = 'credit';
    public const TYPE_ORDER = 'order';
    public const TYPE_OTHER = 'other';

    protected $fillable = [
        'order_id',
        'operator_id',
        'type',
        'reason',
        'amount',
        'status',
        'before_data',
        'after_data',
        'authorized_at',
    ];

    protected function casts(): array
    {
        return [
            'order_id' => 'integer',
            'operator_id' => 'integer',
            'amount' => 'integer',
            'status' => 'integer',
            'before_data' => 'array',
            'after_data' => 'array',
            'authorized_at' => 'datetime',
        ];
    }

    /**
     * 状态映射
     */
    public static function statusMap(): array
    {
        return [
            self::STATUS_PENDING => '待审核',
            self::STATUS_APPROVED => '已通过',
            self::STATUS_REJECTED => '已拒绝',
        ];
    }

    /**
     * 类型映射
     */
    public static function typeMap(): array
    {
        return [
            self::TYPE_BALANCE => '余额更正',
            self::TYPE_CREDIT => '信用更正',
            self::TYPE_ORDER => '订单更正',
            self::TYPE_OTHER => '其他',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusMap()[$this->status] ?? '未知';
    }

    public function getTypeLabelAttribute(): string
    {
        return self::typeMap()[$this->type] ?? '其他';
    }

    /**
     * 关联订单
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * 关联授权人
     */
    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    /**
     * 作用域：待审核
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}
