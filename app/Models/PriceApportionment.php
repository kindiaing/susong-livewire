<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 费用均摊模型
 *
 * @property mixed $target_type 单据类型：1订单2采购单
 * @property mixed $target_id 单据ID
 * @property mixed $target_item_id 单据明细ID
 * @property mixed $apportion_type 均摊类型：1整单改价2促销差价3费用4运费
 * @property mixed $amount 均摊金额
 * @property mixed $apportion_mode 均摊方式：1自动均摊2手动均摊
 * @property mixed $manual_amount 手动均摊金额
 * @property mixed $operator_id 操作人ID
 * @property mixed $approval_status 审核状态：1待审核2已通过3已拒绝
 */
class PriceApportionment extends Model
{
    use SoftDeletes;
    // 单据类型常量
    public const TARGET_ORDER = 1;
    public const TARGET_PURCHASE = 2;

    // 均摊类型常量
    public const APPORTION_PRICE_CHANGE = 1;
    public const APPORTION_PROMOTION = 2;
    public const APPORTION_FEE = 3;
    public const APPORTION_FREIGHT = 4;

    // 均摊方式常量
    public const MODE_AUTO = 1;
    public const MODE_MANUAL = 2;

    // 审核状态常量
    public const APPROVAL_PENDING = 1;
    public const APPROVAL_APPROVED = 2;
    public const APPROVAL_REJECTED = 3;

    protected $fillable = [
        'target_type',
        'target_id',
        'target_item_id',
        'apportion_type',
        'amount',
        'apportion_mode',
        'manual_amount',
        'operator_id',
        'approval_status',
    ];

    protected function casts(): array
    {
        return [
            'target_type' => 'integer',
            'target_id' => 'integer',
            'target_item_id' => 'integer',
            'apportion_type' => 'integer',
            'amount' => 'integer',
            'apportion_mode' => 'integer',
            'manual_amount' => 'integer',
            'operator_id' => 'integer',
            'approval_status' => 'integer',
        ];
    }

    /**
     * 单据类型映射
     */
    public static function targetTypeMap(): array
    {
        return [
            self::TARGET_ORDER => '订单',
            self::TARGET_PURCHASE => '采购单',
        ];
    }

    /**
     * 均摊类型映射
     */
    public static function apportionTypeMap(): array
    {
        return [
            self::APPORTION_PRICE_CHANGE => '整单改价',
            self::APPORTION_PROMOTION => '促销差价',
            self::APPORTION_FEE => '费用',
            self::APPORTION_FREIGHT => '运费',
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

    public function getTargetTypeLabelAttribute(): string
    {
        return self::targetTypeMap()[$this->target_type] ?? '未知';
    }

    public function getApportionTypeLabelAttribute(): string
    {
        return self::apportionTypeMap()[$this->apportion_type] ?? '未知';
    }

    public function getApprovalStatusLabelAttribute(): string
    {
        return self::approvalStatusMap()[$this->approval_status] ?? '未知';
    }

    /**
     * 关联操作人
     */
    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    /**
     * 作用域：按单据类型
     */
    public function scopeByTargetType($query, int $type)
    {
        return $query->where('target_type', $type);
    }
}
