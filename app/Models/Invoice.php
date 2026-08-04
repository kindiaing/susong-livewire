<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 发票管理模型
 *
 * @property mixed $invoice_no 发票号
 * @property mixed $type 类型：1客户发票2供应商发票
 * @property mixed $target_id 关联对象ID
 * @property mixed $title 发票抬头
 * @property mixed $amount 金额
 * @property mixed $file_url 发票文件地址
 * @property mixed $status 状态：1待开具2已开具3已寄出
 * @property mixed $applied_at 申请时间
 * @property mixed $issued_at 开具时间
 * @property mixed $sent_at 寄出时间
 */
class Invoice extends Model
{
    // 类型常量
    public const TYPE_CUSTOMER = 1;
    public const TYPE_SUPPLIER = 2;

    // 状态常量
    public const STATUS_PENDING = 1;
    public const STATUS_ISSUED = 2;
    public const STATUS_SENT = 3;

    protected $fillable = [
        'invoice_no',
        'type',
        'target_id',
        'title',
        'amount',
        'file_url',
        'status',
        'applied_at',
        'issued_at',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => 'integer',
            'target_id' => 'integer',
            'amount' => 'integer',
            'status' => 'integer',
            'applied_at' => 'datetime',
            'issued_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

    /**
     * 类型映射
     */
    public static function typeMap(): array
    {
        return [
            self::TYPE_CUSTOMER => '客户发票',
            self::TYPE_SUPPLIER => '供应商发票',
        ];
    }

    /**
     * 状态映射
     */
    public static function statusMap(): array
    {
        return [
            self::STATUS_PENDING => '待开具',
            self::STATUS_ISSUED => '已开具',
            self::STATUS_SENT => '已寄出',
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
     * 作用域：按类型
     */
    public function scopeByType($query, int $type)
    {
        return $query->where('type', $type);
    }

    /**
     * 作用域：待开具
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * 关联：客户发票 → 商家
     */
    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'target_id')->where('type', self::TYPE_CUSTOMER);
    }

    /**
     * 关联：供应商发票 → 供应商
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'target_id')->where('type', self::TYPE_SUPPLIER);
    }

    /**
     * 动态关联：根据 type 自动返回商家或供应商
     */
    public function target()
    {
        return $this->type === self::TYPE_CUSTOMER
            ? $this->belongsTo(Merchant::class, 'target_id')
            : $this->belongsTo(Supplier::class, 'target_id');
    }

    /**
     * 获取目标对象名称
     */
    public function getTargetNameAttribute(): string
    {
        if ($this->type === self::TYPE_CUSTOMER) {
            return Merchant::find($this->target_id)?->name ?? '-';
        }
        return Supplier::find($this->target_id)?->name ?? '-';
    }
}
