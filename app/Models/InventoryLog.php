<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 库存变动日志模型
 *
 * @property mixed $warehouse_id 仓库ID
 * @property mixed $sku_id SKU ID
 * @property mixed $type 变动类型：1入库2出库3调拨4报损5报溢6调整
 * @property mixed $quantity 变动数量
 * @property mixed $before_stock 变动前库存
 * @property mixed $after_stock 变动后库存
 * @property mixed $reason 变动原因
 * @property mixed $operator_id 操作人ID
 * @property mixed $source_type 业务来源类型
 * @property mixed $source_id 业务来源ID
 */
class InventoryLog extends Model
{
    public const UPDATED_AT = null;

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

    public const TYPE_IN = 1;
    public const TYPE_OUT = 2;
    public const TYPE_TRANSFER = 3;
    public const TYPE_LOSS = 4;
    public const TYPE_OVER = 5;
    public const TYPE_ADJUST = 6;

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
}
