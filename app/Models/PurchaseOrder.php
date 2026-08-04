<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * 采购单模型
 *
 * @property int $id
 * @property string $order_no 采购单号
 * @property int $supplier_id 供应商ID
 * @property int|null $warehouse_id 入库目标仓库
 * @property int $status 状态：1待接单，2备货中，3已发货，4已入库，5完成，9已作废
 * @property int $total_amount 总金额（厘）
 * @property int $actual_amount 实际入库金额（厘）
 * @property int|null $operator_id 经办人
 * @property \Illuminate\Support\Carbon|null $ordered_at 下单时间
 * @property \Illuminate\Support\Carbon|null $shipped_at 发货时间
 * @property \Illuminate\Support\Carbon|null $stocked_at 入库时间
 * @property string|null $remark 备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class PurchaseOrder extends Model
{
    use SoftDeletes;

    // 退货状态常量
    public const RETURN_STATUS_NONE = 0;
    public const RETURN_STATUS_PARTIAL = 1;
    public const RETURN_STATUS_FULL = 2;

    public const STATUS_PENDING = 1;
    public const STATUS_PREPARING = 2;
    public const STATUS_SHIPPED = 3;
    public const STATUS_STOCKED = 4;
    public const STATUS_COMPLETED = 5;
    public const STATUS_CANCELLED = 9;

    protected $fillable = [
        'order_no',
        'supplier_id',
        'warehouse_id',
        'status',
        'total_amount',
        'actual_amount',
        'operator_id',
        'approved_by',
        'approved_at',
        'ordered_at',
        'shipped_at',
        'stocked_at',
        'completed_at',
        'cancelled_at',
        'cancel_reason',
        'remark',
        'return_status',
    ];

    protected function casts(): array
    {
        return [
            'supplier_id' => 'integer',
            'warehouse_id' => 'integer',
            'status' => 'integer',
            'total_amount' => 'integer',
            'actual_amount' => 'integer',
            'operator_id' => 'integer',
            'approved_by' => 'integer',
            'approved_at' => 'datetime',
            'ordered_at' => 'datetime',
            'shipped_at' => 'datetime',
            'stocked_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'return_status' => 'integer',
        ];
    }

    public static function statusMap(): array
    {
        return [
            self::STATUS_PENDING => '待接单',
            self::STATUS_PREPARING => '备货中',
            self::STATUS_SHIPPED => '已发货',
            self::STATUS_STOCKED => '已入库',
            self::STATUS_COMPLETED => '完成',
            self::STATUS_CANCELLED => '已作废',
        ];
    }

    /**
     * 退货状态映射
     */
    public static function returnStatusMap(): array
    {
        return [
            self::RETURN_STATUS_NONE => '无退货',
            self::RETURN_STATUS_PARTIAL => '部分退货',
            self::RETURN_STATUS_FULL => '全部退货',
        ];
    }

    public function getReturnStatusLabelAttribute(): string
    {
        return self::returnStatusMap()[$this->return_status] ?? '未知';
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusMap()[$this->status] ?? '未知';
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    /**
     * 关联采购退货
     */
    public function purchaseReturns()
    {
        return $this->hasMany(PurchaseReturn::class);
    }

    /**
     * 采购单号生成
     */
    public static function generateOrderNo(): string
    {
        return 'PO' . date('YmdHis') . str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
    }

    /**
     * 是否可流转到下一状态
     *
     * 核心规则：已入库(4)只能→完成(5)，禁止直接作废
     * 已入库的采购单如需作废，必须走退货流程
     */
    public function canTransitionTo(int $status): bool
    {
        $flow = [
            self::STATUS_PENDING => [self::STATUS_PREPARING, self::STATUS_CANCELLED],
            self::STATUS_PREPARING => [self::STATUS_SHIPPED, self::STATUS_CANCELLED],
            self::STATUS_SHIPPED => [self::STATUS_STOCKED, self::STATUS_CANCELLED],
            self::STATUS_STOCKED => [self::STATUS_COMPLETED],
        ];

        return in_array($status, $flow[$this->status] ?? []);
    }

    /**
     * 是否有进行中的退货
     */
    public function hasActiveReturn(): bool
    {
        return $this->purchaseReturns()
            ->whereNotIn('status', [PurchaseReturn::STATUS_COMPLETED, PurchaseReturn::STATUS_CANCELLED])
            ->exists();
    }

    /**
     * 更新退货状态
     */
    public function updateReturnStatus(): void
    {
        $returns = $this->purchaseReturns()
            ->where('status', '!=', PurchaseReturn::STATUS_CANCELLED)
            ->get();

        if ($returns->isEmpty()) {
            $this->return_status = self::RETURN_STATUS_NONE;
        } else {
            // 检查所有明细是否全部退货
            $orderedQuantities = $this->items()->pluck('quantity', 'id');
            $returnedQuantities = [];
            foreach ($returns as $return) {
                foreach ($return->items as $item) {
                    $poiId = $item->purchase_order_item_id;
                    $returnedQuantities[$poiId] = ($returnedQuantities[$poiId] ?? 0) + $item->quantity;
                }
            }

            $allFullyReturned = true;
            foreach ($orderedQuantities as $poiId => $qty) {
                if (($returnedQuantities[$poiId] ?? 0) < $qty) {
                    $allFullyReturned = false;
                    break;
                }
            }

            $this->return_status = $allFullyReturned
                ? self::RETURN_STATUS_FULL
                : self::RETURN_STATUS_PARTIAL;
        }

        $this->save();
    }

    /**
     * 重算总金额
     */
    public function recalculateAmounts(): void
    {
        $this->total_amount = $this->items()->sum('amount');
        $this->actual_amount = $this->items()->sum('actual_amount');
        $this->save();
    }
}
