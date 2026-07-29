<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 实时库存模型
 *
 * @property mixed $warehouse_id 仓库ID
 * @property mixed $sku_id SKU ID
 * @property mixed $total_stock 总库存
 * @property mixed $locked_stock 锁定库存
 * @property mixed $available_stock 可用库存
 * @property mixed $batch_no 入库批次号
 * @property mixed $expiry_date 效期
 * @property mixed $warning_value 预警值
 */
class Inventory extends Model
{
    protected $table = 'inventory';

    protected $fillable = [
        'warehouse_id',
        'sku_id',
        'total_stock',
        'locked_stock',
        'available_stock',
        'batch_no',
        'expiry_date',
        'warning_value',
    ];

    protected function casts(): array
    {
        return [
            'warehouse_id' => 'integer',
            'sku_id' => 'integer',
            'total_stock' => 'integer',
            'locked_stock' => 'integer',
            'available_stock' => 'integer',
            'warning_value' => 'integer',
            'expiry_date' => 'date',
        ];
    }

}
