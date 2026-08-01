<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * SKU条码模型
 *
 * @property int $id
 * @property int $sku_id SKU ID
 * @property int|null $supplier_id 供应商ID
 * @property int $barcode_type 条码类型：1厂家条码，2供应商条码，3内部条码，4备用条码
 * @property string $barcode_code 条码值
 * @property int $is_default 是否默认条码：0否，1是
 * @property int $is_enabled 是否启用：0禁用，1启用
 * @property string|null $remark 备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class SkuBarcode extends Model
{
    use SoftDeletes;

    // 条码类型常量
    public const TYPE_FACTORY = 1;
    public const TYPE_SUPPLIER = 2;
    public const TYPE_INTERNAL = 3;
    public const TYPE_BACKUP = 4;

    protected $fillable = [
        'sku_id',
        'supplier_id',
        'barcode_type',
        'barcode_code',
        'is_default',
        'is_enabled',
        'remark',
    ];

    protected function casts(): array
    {
        return [
            'sku_id' => 'integer',
            'supplier_id' => 'integer',
            'barcode_type' => 'integer',
            'is_default' => 'integer',
            'is_enabled' => 'integer',
        ];
    }

    /**
     * 条码类型映射
     */
    public static function barcodeTypeMap(): array
    {
        return [
            self::TYPE_FACTORY => '厂家条码',
            self::TYPE_SUPPLIER => '供应商条码',
            self::TYPE_INTERNAL => '内部条码',
            self::TYPE_BACKUP => '备用条码',
        ];
    }

    public function getBarcodeTypeLabelAttribute(): string
    {
        return self::barcodeTypeMap()[$this->barcode_type] ?? '未知';
    }

    /**
     * 状态映射（is_enabled 字段）
     */
    public static function statusMap(): array
    {
        return [
            1 => '启用',
            0 => '禁用',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusMap()[$this->is_enabled] ?? '未知';
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
        return $query->where('is_enabled', 1);
    }
}
