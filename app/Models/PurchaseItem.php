<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * 待采清单模型
 *
 * @property int $id
 * @property int $sku_id SKU ID
 * @property int $quantity 待采数量
 * @property int $source_type 来源：1订单汇总，2手工添加
 * @property int|null $source_id 来源业务ID
 * @property int $status 状态：1待生成采购单，2已生成采购单
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class PurchaseItem extends Model
{
    // 来源类型
    public const SOURCE_ORDER = 1;
    public const SOURCE_MANUAL = 2;

    // 状态常量
    public const STATUS_PENDING = 1;
    public const STATUS_ORDERED = 2;

    protected $fillable = [
        'sku_id',
        'quantity',
        'source_type',
        'source_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'sku_id' => 'integer',
            'quantity' => 'integer',
            'source_type' => 'integer',
            'source_id' => 'integer',
            'status' => 'integer',
        ];
    }

    public static function sourceTypeMap(): array
    {
        return [
            self::SOURCE_ORDER => '订单汇总',
            self::SOURCE_MANUAL => '手工添加',
        ];
    }

    public static function statusMap(): array
    {
        return [
            self::STATUS_PENDING => '待生成',
            self::STATUS_ORDERED => '已生成',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusMap()[$this->status] ?? '未知';
    }

    public function sku()
    {
        return $this->belongsTo(Sku::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * 作用域：按来源类型
     */
    public function scopeBySourceType($query, int $sourceType)
    {
        return $query->where('source_type', $sourceType);
    }

    /**
     * 作用域：订单汇总来源
     */
    public function scopeFromOrder($query)
    {
        return $query->where('source_type', self::SOURCE_ORDER);
    }

    /**
     * 作用域：手工添加来源
     */
    public function scopeFromManual($query)
    {
        return $query->where('source_type', self::SOURCE_MANUAL);
    }
}
