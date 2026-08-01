<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 改价记录模型
 *
 * @property mixed $source_type 来源：1促销2临时改价3手动改价
 * @property mixed $source_id 来源策略ID
 * @property mixed $target_type 作用单据类型：1订单2采购单3应收4应付
 * @property mixed $target_id 单据ID
 * @property mixed $target_item_id 单据明细ID
 * @property mixed $original_price 改价前单价
 * @property mixed $new_price 改价后单价
 * @property mixed $quantity 数量
 * @property mixed $amount_diff 金额差异
 * @property mixed $operator_id 操作人ID
 * @property mixed $role_ids 操作人角色ID数组
 * @property mixed $reason 改价原因
 * @property mixed $before_data 改价前数据快照
 * @property mixed $after_data 改价后数据快照
 */
class PriceChangeLog extends Model
{
    public const UPDATED_AT = null;

    // 来源类型常量
    public const SOURCE_PROMOTION = 1;
    public const SOURCE_TEMP_PRICE = 2;
    public const SOURCE_MANUAL = 3;

    // 作用单据类型常量
    public const TARGET_ORDER = 1;
    public const TARGET_PURCHASE = 2;
    public const TARGET_RECEIVABLE = 3;
    public const TARGET_PAYABLE = 4;

    protected $fillable = [
        'source_type',
        'source_id',
        'target_type',
        'target_id',
        'target_item_id',
        'original_price',
        'new_price',
        'quantity',
        'amount_diff',
        'operator_id',
        'role_ids',
        'reason',
        'before_data',
        'after_data',
    ];

    protected function casts(): array
    {
        return [
            'source_type' => 'integer',
            'source_id' => 'integer',
            'target_type' => 'integer',
            'target_id' => 'integer',
            'target_item_id' => 'integer',
            'original_price' => 'integer',
            'new_price' => 'integer',
            'quantity' => 'integer',
            'amount_diff' => 'integer',
            'operator_id' => 'integer',
            'role_ids' => 'array',
            'before_data' => 'array',
            'after_data' => 'array',
        ];
    }

    /**
     * 来源类型映射
     */
    public static function sourceTypeMap(): array
    {
        return [
            self::SOURCE_PROMOTION => '促销',
            self::SOURCE_TEMP_PRICE => '临时改价',
            self::SOURCE_MANUAL => '手动改价',
        ];
    }

    /**
     * 作用单据类型映射
     */
    public static function targetTypeMap(): array
    {
        return [
            self::TARGET_ORDER => '订单',
            self::TARGET_PURCHASE => '采购单',
            self::TARGET_RECEIVABLE => '应收',
            self::TARGET_PAYABLE => '应付',
        ];
    }

    public function getSourceTypeLabelAttribute(): string
    {
        return self::sourceTypeMap()[$this->source_type] ?? '未知';
    }

    public function getTargetTypeLabelAttribute(): string
    {
        return self::targetTypeMap()[$this->target_type] ?? '未知';
    }

    /**
     * 关联操作人
     */
    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    /**
     * 作用域：按来源类型
     */
    public function scopeBySourceType($query, int $type)
    {
        return $query->where('source_type', $type);
    }

    /**
     * 作用域：按单据类型
     */
    public function scopeByTargetType($query, int $type)
    {
        return $query->where('target_type', $type);
    }
}
