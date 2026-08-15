<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 售后退货模型
 *
 * @property mixed $return_no 退货单号
 * @property mixed $order_id 关联订单ID
 * @property mixed $merchant_id 商家ID
 * @property mixed $status 状态：1待审核2已审核3已退货4退款完成9取消
 * @property mixed $total_amount 退货总金额
 * @property mixed $refund_amount 实际退款金额
 * @property mixed $reason 退货原因
 * @property mixed $operator_id 经办人ID
 * @property mixed $audited_by 审核人ID
 * @property mixed $audited_at 审核时间
 * @property mixed $remark 备注
 */
class OrderReturn extends Model
{
    use SoftDeletes;

    // 状态常量
    public const STATUS_PENDING = 1;
    public const STATUS_APPROVED = 2;
    public const STATUS_RETURNED = 3;
    public const STATUS_REFUNDED = 4;
    public const STATUS_CANCELLED = 9;

    protected $fillable = [
        'return_no',
        'order_id',
        'merchant_id',
        'status',
        'total_amount',
        'refund_amount',
        'reason',
        'operator_id',
        'audited_by',
        'audited_at',
        'remark',
    ];

    protected function casts(): array
    {
        return [
            'order_id' => 'integer',
            'merchant_id' => 'integer',
            'status' => 'integer',
            'total_amount' => 'integer',
            'refund_amount' => 'integer',
            'operator_id' => 'integer',
            'audited_by' => 'integer',
            'audited_at' => 'datetime',
        ];
    }

    /**
     * 状态映射
     */
    public static function statusMap(): array
    {
        return [
            self::STATUS_PENDING => '待审核',
            self::STATUS_APPROVED => '已审核',
            self::STATUS_RETURNED => '已退货',
            self::STATUS_REFUNDED => '退款完成',
            self::STATUS_CANCELLED => '已作废',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusMap()[$this->status] ?? '未知';
    }

    /**
     * 关联订单
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * 关联商家
     */
    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    /**
     * 关联退货明细
     */
    public function items()
    {
        return $this->hasMany(OrderReturnItem::class);
    }

    /**
     * 关联经办人
     */
    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    /**
     * 关联审核人
     */
    public function auditor()
    {
        return $this->belongsTo(User::class, 'audited_by');
    }

    /**
     * 作用域：待审核
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}
