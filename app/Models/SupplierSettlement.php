<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 供应商结算模型
 *
 * @property mixed $settlement_no 结算单号
 * @property mixed $supplier_id 供应商ID
 * @property mixed $start_date 结算周期开始
 * @property mixed $end_date 结算周期结束
 * @property mixed $total_amount 汇总金额
 * @property mixed $service_fee 服务费
 * @property mixed $payable_amount 应付金额
 * @property mixed $paid_amount 已付金额
 * @property mixed $return_amount 采购退货扣减金额
 * @property mixed $status 状态：1待结算2部分付款3已结清4已办结
 * @property mixed $settled_at 结算时间
 * @property mixed $closed_at 办结时间
 * @property mixed $closed_by 办结操作人ID
 */
class SupplierSettlement extends Model
{
    // 状态常量
    public const STATUS_PENDING = 1;
    public const STATUS_PARTIAL = 2;
    public const STATUS_SETTLED = 3;
    public const STATUS_CLOSED = 4;

    protected $fillable = [
        'settlement_no',
        'supplier_id',
        'start_date',
        'end_date',
        'total_amount',
        'service_fee',
        'payable_amount',
        'paid_amount',
        'return_amount',
        'status',
        'settled_at',
        'closed_at',
        'closed_by',
    ];

    protected function casts(): array
    {
        return [
            'supplier_id' => 'integer',
            'total_amount' => 'integer',
            'service_fee' => 'integer',
            'payable_amount' => 'integer',
            'paid_amount' => 'integer',
            'return_amount' => 'integer',
            'status' => 'integer',
            'settled_at' => 'datetime',
            'closed_at' => 'datetime',
            'closed_by' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    /**
     * 状态映射
     */
    public static function statusMap(): array
    {
        return [
            self::STATUS_PENDING => '待结算',
            self::STATUS_PARTIAL => '部分付款',
            self::STATUS_SETTLED => '已结清',
            self::STATUS_CLOSED => '已办结',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusMap()[$this->status] ?? '未知';
    }

    /**
     * 关联供应商
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * 关联结算明细
     */
    public function items()
    {
        return $this->hasMany(SupplierSettlementItem::class);
    }

    /**
     * 关联付款记录
     */
    public function payments()
    {
        return $this->hasMany(SettlementPayment::class, 'settlement_id');
    }

    /**
     * 关联办结操作人
     */
    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    /**
     * 作用域：待结算
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}
