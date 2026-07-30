<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * 实时库存模型
 *
 * @property int $id
 * @property int $warehouse_id 仓库ID
 * @property int $sku_id SKU ID
 * @property int $total_stock 总库存
 * @property int $locked_stock 锁定库存
 * @property int $available_stock 可用库存
 * @property string|null $batch_no 入库批次号
 * @property Carbon|null $expiry_date 效期
 * @property int $warning_value 预警值
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
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
     * 作用域：低于预警值
     */
    public function scopeBelowWarning($query)
    {
        return $query->whereColumn('available_stock', '<', 'warning_value');
    }
}
