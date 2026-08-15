<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * 订单模型
 *
 * @property int $id
 * @property string $order_no 订单号
 * @property int $merchant_id 商家ID
 * @property int $batch 配送批次：1上午，2下午
 * @property string|null $delivery_address 配送地址
 * @property string|null $contact_name 收货联系人
 * @property string|null $contact_phone 收货电话
 * @property int $status 状态：1待拣货，2拣货中，3配送中，4已签收，5已锁定，9已作废
 * @property int $total_amount 原始订单金额（厘）
 * @property int $adjusted_amount 调整后金额（厘）
 * @property int $final_amount 最终结算金额（厘）
 * @property int $payment_status 支付状态：1未支付，2已支付，3账期
 * @property int $settlement_type 结算方式：1现结，2账期，3预付款
 * @property int $is_locked 是否锁定：0否，1是
 * @property int $is_supplement 是否补单：0否，1是
 * @property int|null $supplement_for 补单关联的原订单ID
 * @property string|null $remark 备注
 * @property \Carbon\Carbon|null $order_date 单据日期
 * @property \Carbon\Carbon|null $delivery_date 收货日期
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Order extends Model
{
    use SoftDeletes;

    public const STATUS_PICKING_WAIT = 1;
    public const STATUS_PICKING = 2;
    public const STATUS_DELIVERING = 3;
    public const STATUS_SIGNED = 4;
    public const STATUS_LOCKED = 5;
    public const STATUS_CANCELLED = 9;

    public const PAYMENT_UNPAID = 1;
    public const PAYMENT_PAID = 2;
    public const PAYMENT_CREDIT = 3;

    public const SETTLEMENT_CASH = 1;
    public const SETTLEMENT_CREDIT = 2;
    public const SETTLEMENT_PREPAID = 3;

    public const BATCH_MORNING = 1;
    public const BATCH_AFTERNOON = 2;

    protected $fillable = [
        'order_no',
        'merchant_id',
        'batch',
        'delivery_address',
        'contact_name',
        'contact_phone',
        'status',
        'total_amount',
        'adjusted_amount',
        'final_amount',
        'payment_status',
        'settlement_type',
        'is_locked',
        'is_supplement',
        'supplement_for',
        'remark',
        'order_date',
        'delivery_date',
    ];

    protected function casts(): array
    {
        return [
            'merchant_id' => 'integer',
            'batch' => 'integer',
            'status' => 'integer',
            'total_amount' => 'integer',
            'adjusted_amount' => 'integer',
            'final_amount' => 'integer',
            'payment_status' => 'integer',
            'settlement_type' => 'integer',
            'is_locked' => 'integer',
            'is_supplement' => 'integer',
            'supplement_for' => 'integer',
            'order_date' => 'date',
            'delivery_date' => 'date',
        ];
    }

    public static function statusMap(): array
    {
        return [
            self::STATUS_PICKING_WAIT => '待拣货',
            self::STATUS_PICKING => '拣货中',
            self::STATUS_DELIVERING => '配送中',
            self::STATUS_SIGNED => '已签收',
            self::STATUS_LOCKED => '已锁定',
            self::STATUS_CANCELLED => '已作废',
        ];
    }

    public static function paymentStatusMap(): array
    {
        return [
            self::PAYMENT_UNPAID => '未支付',
            self::PAYMENT_PAID => '已支付',
            self::PAYMENT_CREDIT => '账期',
        ];
    }

    public static function settlementTypeMap(): array
    {
        return [
            self::SETTLEMENT_CASH => '现结',
            self::SETTLEMENT_CREDIT => '账期',
            self::SETTLEMENT_PREPAID => '预付款',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusMap()[$this->status] ?? '未知';
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return self::paymentStatusMap()[$this->payment_status] ?? '未知';
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function deliveryRoute()
    {
        return $this->belongsTo(DeliveryRoute::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function signatures()
    {
        return $this->hasMany(Signature::class);
    }

    public function orderReturns()
    {
        return $this->hasMany(OrderReturn::class);
    }

    public function correctionAuthorizations()
    {
        return $this->hasMany(CorrectionAuthorization::class);
    }

    public function receivable()
    {
        return $this->hasOne(Receivable::class);
    }

    public function getSettlementTypeLabelAttribute(): string
    {
        return self::settlementTypeMap()[$this->settlement_type] ?? '未知';
    }

    public function getBatchLabelAttribute(): string
    {
        return match ($this->batch) {
            self::BATCH_MORNING => '上午',
            self::BATCH_AFTERNOON => '下午',
            default => '未知',
        };
    }

    /**
     * 关联补单原订单
     */
    public function supplementFor()
    {
        return $this->belongsTo(Order::class, 'supplement_for');
    }

    /**
     * 是否可编辑（拣货中不允许商户编辑）
     */
    public function isEditableByMerchant(): bool
    {
        return !in_array($this->status, [self::STATUS_PICKING, self::STATUS_DELIVERING]);
    }

    /**
     * 关联审计日志
     */
    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'model', 'model_type', 'model_id');
    }

    /**
     * 订单号生成
     * 格式：ORD-YYYYMMDD-5位序号
     */
    public static function generateOrderNo(): string
    {
        return generate_sequence_no('ORD', 'orders', 'order_no');
    }

    /**
     * 是否可流转到指定状态
     *
     * 核心规则：已签收(4)和已锁定(5)不可手动变更
     */
    public function canTransitionTo(int $status): bool
    {
        $flow = [
            self::STATUS_PICKING_WAIT => [self::STATUS_PICKING, self::STATUS_CANCELLED],
            self::STATUS_PICKING => [self::STATUS_DELIVERING, self::STATUS_CANCELLED],
            self::STATUS_DELIVERING => [self::STATUS_SIGNED, self::STATUS_CANCELLED],
        ];

        return in_array($status, $flow[$this->status] ?? []);
    }

    /**
     * 作用域：按状态
     */
    public function scopeByStatus($query, int $status)
    {
        return $query->where('status', $status);
    }

    /**
     * 作用域：按配送批次
     */
    public function scopeByBatch($query, int $batch)
    {
        return $query->where('batch', $batch);
    }

    /**
     * 作用域：按支付状态
     */
    public function scopeByPaymentStatus($query, int $paymentStatus)
    {
        return $query->where('payment_status', $paymentStatus);
    }
}
