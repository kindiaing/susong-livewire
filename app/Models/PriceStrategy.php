<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 价格策略模型（已弃用 — V1.10.0 起由 PricingService + Promotion 体系替代）
 *
 * @deprecated 保留仅供 PurchaseOrderItem 旧关联兼容，新代码请勿使用
 *
 * @property mixed $name 策略名称
 * @property mixed $code 策略编码
 * @property mixed $type 类型：1促销2临时改价
 * @property mixed $target_type 作用对象：1供应商2商家3全部
 * @property mixed $scope 作用范围：1采购2销售3通用
 * @property mixed $status 状态：0禁用1启用
 * @property mixed $approval_status 审核状态：1待审核2已通过3已拒绝
 * @property mixed $start_at 生效开始时间
 * @property mixed $end_at 生效结束时间
 * @property mixed $created_by 创建人ID
 * @property mixed $remark 备注
 */
class PriceStrategy extends Model
{
    use SoftDeletes;

    // 类型常量
    public const TYPE_PROMOTION = 1;
    public const TYPE_TEMP_PRICE = 2;

    // 作用对象常量
    public const TARGET_SUPPLIER = 1;
    public const TARGET_MERCHANT = 2;
    public const TARGET_ALL = 3;

    // 作用范围常量
    public const SCOPE_PURCHASE = 1;
    public const SCOPE_SALE = 2;
    public const SCOPE_GENERAL = 3;

    // 状态常量
    public const STATUS_DISABLED = 0;
    public const STATUS_ENABLED = 1;

    // 审核状态常量
    public const APPROVAL_PENDING = 1;
    public const APPROVAL_APPROVED = 2;
    public const APPROVAL_REJECTED = 3;

    protected $fillable = [
        'name',
        'code',
        'type',
        'target_type',
        'scope',
        'status',
        'approval_status',
        'start_at',
        'end_at',
        'created_by',
        'remark',
    ];

    protected function casts(): array
    {
        return [
            'type' => 'integer',
            'target_type' => 'integer',
            'scope' => 'integer',
            'status' => 'integer',
            'approval_status' => 'integer',
            'created_by' => 'integer',
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
            self::TYPE_PROMOTION => '促销',
            self::TYPE_TEMP_PRICE => '临时改价',
        ];
    }

    /**
     * 作用对象映射
     */
    public static function targetTypeMap(): array
    {
        return [
            self::TARGET_SUPPLIER => '供应商',
            self::TARGET_MERCHANT => '商家',
            self::TARGET_ALL => '全部',
        ];
    }

    /**
     * 作用范围映射
     */
    public static function scopeMap(): array
    {
        return [
            self::SCOPE_PURCHASE => '采购',
            self::SCOPE_SALE => '销售',
            self::SCOPE_GENERAL => '通用',
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

    /**
     * 审核状态映射
     */
    public static function approvalStatusMap(): array
    {
        return [
            self::APPROVAL_PENDING => '待审核',
            self::APPROVAL_APPROVED => '已通过',
            self::APPROVAL_REJECTED => '已拒绝',
        ];
    }

    public function getTypeLabelAttribute(): string
    {
        return self::typeMap()[$this->type] ?? '未知';
    }

    public function getTargetTypeLabelAttribute(): string
    {
        return self::targetTypeMap()[$this->target_type] ?? '未知';
    }

    public function getScopeLabelAttribute(): string
    {
        return self::scopeMap()[$this->scope] ?? '未知';
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusMap()[$this->status] ?? '未知';
    }

    public function getApprovalStatusLabelAttribute(): string
    {
        return self::approvalStatusMap()[$this->approval_status] ?? '未知';
    }

    /**
     * 关联策略明细
     */
    public function items()
    {
        return $this->hasMany(PriceStrategyItem::class);
    }

    /**
     * 关联创建人
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * 作用域：启用
     */
    public function scopeEnabled($query)
    {
        return $query->where('status', self::STATUS_ENABLED);
    }

    /**
     * 作用域：按类型
     */
    public function scopeByType($query, int $type)
    {
        return $query->where('type', $type);
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
