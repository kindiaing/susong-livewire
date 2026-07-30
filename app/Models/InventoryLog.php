<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * 库存变动日志模型
 *
 * @property int $id
 * @property int $warehouse_id 仓库ID
 * @property int $sku_id SKU ID
 * @property int $type 变动类型：1入库，2出库，3调拨，4报损，5报溢，6调整
 * @property int $quantity 变动数量（正增负减）
 * @property int $before_stock 变动前库存
 * @property int $after_stock 变动后库存
 * @property string|null $reason 变动原因
 * @property int|null $operator_id 操作人ID
 * @property string|null $source_type 业务来源类型
 * @property int|null $source_id 业务来源ID
 * @property Carbon|null $created_at
 */
class InventoryLog extends Model
{
    public const UPDATED_AT = null;

    // 变动类型常量
    public const TYPE_IN = 1;
    public const TYPE_OUT = 2;
    public const TYPE_TRANSFER = 3;
    public const TYPE_LOSS = 4;
    public const TYPE_OVER = 5;
    public const TYPE_ADJUST = 6;

    protected $fillable = [
        'warehouse_id',
        'sku_id',
        'type',
        'quantity',
        'before_stock',
        'after_stock',
        'reason',
        'operator_id',
        'source_type',
        'source_id',
    ];

    protected function casts(): array
    {
        return [
            'warehouse_id' => 'integer',
            'sku_id' => 'integer',
            'type' => 'integer',
            'quantity' => 'integer',
            'before_stock' => 'integer',
            'after_stock' => 'integer',
            'operator_id' => 'integer',
            'source_id' => 'integer',
        ];
    }

    /**
     * 变动类型映射
     */
    public static function typeMap(): array
    {
        return [
            self::TYPE_IN => '入库',
            self::TYPE_OUT => '出库',
            self::TYPE_TRANSFER => '调拨',
            self::TYPE_LOSS => '报损',
            self::TYPE_OVER => '报溢',
            self::TYPE_ADJUST => '调整',
        ];
    }

    public function getTypeLabelAttribute(): string
    {
        return self::typeMap()[$this->type] ?? '未知';
    }

    /**
     * 关联仓库
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * 关联 SKU
     */
    public function sku()
    {
        return $this->belongsTo(Sku::class);
    }

    /**
     * 关联操作人
     */
    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }
}
