<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * 采购退货模型
 *
 * @property int $id
 * @property string $return_no 退货单号
 * @property int $purchase_order_id 关联采购单ID
 * @property int $supplier_id 供应商ID
 * @property int $warehouse_id 出库仓库ID
 * @property int $status 状态：1待审核，2已审核，3已出库，4完成，9取消
 * @property int $total_amount 退货总金额（厘）
 * @property int $actual_amount 实际出库金额（厘）
 * @property string|null $reason 退货原因
 * @property int|null $operator_id 经办人ID
 * @property int|null $audited_by 审核人ID
 * @property Carbon|null $audited_at 审核时间
 * @property string|null $remark 备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class PurchaseReturn extends Model
{
    use SoftDeletes;

    // 状态常量
    public const STATUS_PENDING = 1;
    public const STATUS_APPROVED = 2;
    public const STATUS_SHIPPED = 3;
    public const STATUS_COMPLETED = 4;
    public const STATUS_CANCELLED = 9;

    protected $fillable = [
        'return_no',
        'purchase_order_id',
        'supplier_id',
        'warehouse_id',
        'status',
        'total_amount',
        'actual_amount',
        'reason',
        'operator_id',
        'audited_by',
        'audited_at',
        'shipped_at',
        'completed_at',
        'cancelled_at',
        'remark',
    ];

    protected function casts(): array
    {
        return [
            'purchase_order_id' => 'integer',
            'supplier_id' => 'integer',
            'warehouse_id' => 'integer',
            'status' => 'integer',
            'total_amount' => 'integer',
            'actual_amount' => 'integer',
            'operator_id' => 'integer',
            'audited_by' => 'integer',
            'audited_at' => 'datetime',
            'shipped_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
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
            self::STATUS_SHIPPED => '已出库',
            self::STATUS_COMPLETED => '完成',
            self::STATUS_CANCELLED => '取消',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusMap()[$this->status] ?? '未知';
    }

    /**
     * 关联采购单
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    /**
     * 关联供应商
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
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
     * 关联仓库
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * 关联退货明细
     */
    public function items()
    {
        return $this->hasMany(PurchaseReturnItem::class);
    }

    /**
     * 作用域：待审核
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}
