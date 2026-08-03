<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 差异处理模型
 *
 * @property mixed $discrepancy_no 差异单号
 * @property mixed $order_id 关联订单ID
 * @property mixed $order_item_id 关联订单明细ID
 * @property mixed $stage 差异环节：1拣货2配送3实收
 * @property mixed $type 差异类型：1少收2拒收3残次4其他
 * @property mixed $expected_quantity 预期数量
 * @property mixed $actual_quantity 实际数量
 * @property mixed $quantity_diff 数量差异
 * @property mixed $amount_diff 金额差异
 * @property mixed $reason 差异原因
 * @property mixed $evidence_urls 凭证图片数组
 * @property mixed $responsible_party 责任方：1供应商2平台3司机4商家
 * @property mixed $decision 处理决策：1补货2退款3扣款4报损5不计
 * @property mixed $status 状态：1待处理2处理中3已处理4已关闭5争议中
 * @property mixed $handler_id 处理人ID
 * @property mixed $handled_at 处理时间
 * @property mixed $is_amount_adjusted 是否已调整金额
 * @property mixed $approval_status 审核状态：1待审核2已通过3已拒绝
 * @property mixed $remark 备注
 */
class Discrepancy extends Model
{
    use SoftDeletes;

    // 差异环节常量
    public const STAGE_PICKING = 1;
    public const STAGE_DELIVERY = 2;
    public const STAGE_RECEIVING = 3;

    // 差异类型常量
    public const TYPE_SHORT = 1;
    public const TYPE_REJECT = 2;
    public const TYPE_DEFECT = 3;
    public const TYPE_OTHER = 4;

    // 责任方常量
    public const PARTY_SUPPLIER = 1;
    public const PARTY_PLATFORM = 2;
    public const PARTY_DRIVER = 3;
    public const PARTY_MERCHANT = 4;

    // 处理决策常量
    public const DECISION_RESTOCK = 1;
    public const DECISION_REFUND = 2;
    public const DECISION_DEDUCT = 3;
    public const DECISION_LOSS = 4;
    public const DECISION_IGNORE = 5;

    // 状态常量
    public const STATUS_PENDING = 1;
    public const STATUS_PROCESSING = 2;
    public const STATUS_RESOLVED = 3;
    public const STATUS_CLOSED = 4;
    public const STATUS_DISPUTED = 5;

    // 审核状态常量
    public const APPROVAL_PENDING = 1;
    public const APPROVAL_APPROVED = 2;
    public const APPROVAL_REJECTED = 3;

    protected $fillable = [
        'discrepancy_no',
        'order_id',
        'order_item_id',
        'stage',
        'type',
        'expected_quantity',
        'actual_quantity',
        'quantity_diff',
        'amount_diff',
        'reason',
        'evidence_urls',
        'responsible_party',
        'decision',
        'status',
        'handler_id',
        'handled_at',
        'is_amount_adjusted',
        'approval_status',
        'remark',
    ];

    protected function casts(): array
    {
        return [
            'order_id' => 'integer',
            'order_item_id' => 'integer',
            'stage' => 'integer',
            'type' => 'integer',
            'expected_quantity' => 'integer',
            'actual_quantity' => 'integer',
            'quantity_diff' => 'integer',
            'amount_diff' => 'integer',
            'responsible_party' => 'integer',
            'decision' => 'integer',
            'status' => 'integer',
            'handler_id' => 'integer',
            'handled_at' => 'datetime',
            'is_amount_adjusted' => 'integer',
            'approval_status' => 'integer',
            'evidence_urls' => 'array',
        ];
    }

    /**
     * 状态映射
     */
    public static function statusMap(): array
    {
        return [
            self::STATUS_PENDING => '待处理',
            self::STATUS_PROCESSING => '处理中',
            self::STATUS_RESOLVED => '已处理',
            self::STATUS_CLOSED => '已关闭',
            self::STATUS_DISPUTED => '争议中',
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

    /**
     * 差异环节映射
     */
    public static function stageMap(): array
    {
        return [
            self::STAGE_PICKING => '拣货',
            self::STAGE_DELIVERY => '配送',
            self::STAGE_RECEIVING => '实收',
        ];
    }

    /**
     * 差异类型映射
     */
    public static function typeMap(): array
    {
        return [
            self::TYPE_SHORT => '少收',
            self::TYPE_REJECT => '拒收',
            self::TYPE_DEFECT => '残次',
            self::TYPE_OTHER => '其他',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusMap()[$this->status] ?? '未知';
    }

    public function getApprovalStatusLabelAttribute(): string
    {
        return self::approvalStatusMap()[$this->approval_status] ?? '未知';
    }

    public function getStageLabelAttribute(): string
    {
        return self::stageMap()[$this->stage] ?? '未知';
    }

    public function getTypeLabelAttribute(): string
    {
        return self::typeMap()[$this->type] ?? '未知';
    }

    /**
     * 关联订单
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * 关联订单明细
     */
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    /**
     * 关联处理人
     */
    public function handler()
    {
        return $this->belongsTo(User::class, 'handler_id');
    }

    /**
     * 作用域：待处理
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * 作用域：按差异环节
     */
    public function scopeByStage($query, int $stage)
    {
        return $query->where('stage', $stage);
    }
}
