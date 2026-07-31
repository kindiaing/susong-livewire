<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * SKU供应商关联模型（一品多供）
 *
 * @property int $id
 * @property int $sku_id SKU ID
 * @property int $supplier_id 供应商ID
 * @property int $is_default 是否默认供应商：0否，1是
 * @property int $purchase_price 该供应商采购参考价（厘）
 * @property int $status 是否启用：0禁用，1启用
 * @property int $sort 排序
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class SkuSupplier extends Model
{
    use SoftDeletes;

    // 状态常量
    public const STATUS_DISABLED = 0;
    public const STATUS_ENABLED = 1;

    protected $fillable = [
        'sku_id',
        'supplier_id',
        'is_default',
        'purchase_price',
        'status',
        'sort',
    ];

    protected function casts(): array
    {
        return [
            'sku_id' => 'integer',
            'supplier_id' => 'integer',
            'is_default' => 'integer',
            'purchase_price' => 'integer',
            'status' => 'integer',
            'sort' => 'integer',
        ];
    }

    /**
     * 模型启动方法
     *
     * 默认供应商互斥：同一 SKU 只能有一个 is_default=1
     */
    protected static function booted(): void
    {
        static::saved(function (SkuSupplier $model) {
            if ($model->is_default == 1) {
                static::where('sku_id', $model->sku_id)
                    ->where('is_default', 1)
                    ->where('id', '!=', $model->id)
                    ->update(['is_default' => 0]);
            }
        });
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

    public function getStatusLabelAttribute(): string
    {
        return self::statusMap()[$this->status] ?? '未知';
    }

    /**
     * 关联 SKU
     */
    public function sku()
    {
        return $this->belongsTo(Sku::class);
    }

    /**
     * 关联供应商
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * 作用域：启用
     */
    public function scopeEnabled($query)
    {
        return $query->where('status', self::STATUS_ENABLED);
    }
}
