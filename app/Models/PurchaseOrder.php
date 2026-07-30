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
 * @property int $status 状态：1待接单，2备货中，3已发货，4已入库，5完成，9取消
 * @property int $total_amount 总金额（厘）
 * @property int $actual_amount 实际入库金额（厘）
 * @property string|null $remark 备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class PurchaseOrder extends Model
{
    use SoftDeletes;

    public const STATUS_PENDING = 1;
    public const STATUS_PREPARING = 2;
    public const STATUS_SHIPPED = 3;
    public const STATUS_STOCKED = 4;
    public const STATUS_COMPLETED = 5;
    public const STATUS_CANCELLED = 9;

    protected $fillable = [
        'order_no',
        'supplier_id',
        'status',
        'total_amount',
        'actual_amount',
        'remark',
    ];

    protected function casts(): array
    {
        return [
            'supplier_id' => 'integer',
            'status' => 'integer',
            'total_amount' => 'integer',
            'actual_amount' => 'integer',
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
            self::STATUS_CANCELLED => '取消',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusMap()[$this->status] ?? '未知';
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }
}
