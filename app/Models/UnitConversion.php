<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * 单位换算关系模型
 *
 * 每条记录表示一个 SKU 的一级换算关系，如：1箱 = 6件
 * 多级换算通过 parent_conversion_id 链路串联：箱→件(ratio=6), 件→包(ratio=10)
 * 严格单链路：同一 SKU 的 from_unit 只能出现一次，构成唯一链路
 *
 * @property int $id
 * @property int $sku_id SKU ID
 * @property int $from_unit_id 大单位ID
 * @property int $to_unit_id 小单位ID
 * @property int $ratio 换算系数：1 from_unit = ratio to_unit
 * @property int|null $parent_conversion_id 上级换算ID（链路串联）
 * @property int $status 状态：0禁用，1启用
 * @property int $sort 排序
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class UnitConversion extends Model
{
    public const STATUS_DISABLED = 0;
    public const STATUS_ENABLED = 1;

    protected $fillable = [
        'sku_id',
        'from_unit_id',
        'to_unit_id',
        'ratio',
        'parent_conversion_id',
        'status',
        'sort',
    ];

    protected function casts(): array
    {
        return [
            'sku_id' => 'integer',
            'from_unit_id' => 'integer',
            'to_unit_id' => 'integer',
            'ratio' => 'integer',
            'parent_conversion_id' => 'integer',
            'status' => 'integer',
            'sort' => 'integer',
        ];
    }

    public static function statusMap(): array
    {
        return [
            self::STATUS_ENABLED => '启用',
            self::STATUS_DISABLED => '禁用',
        ];
    }

    /**
     * 关联 SKU
     */
    public function sku()
    {
        return $this->belongsTo(Sku::class);
    }

    /**
     * 关联大单位
     */
    public function fromUnit()
    {
        return $this->belongsTo(Unit::class, 'from_unit_id');
    }

    /**
     * 关联小单位
     */
    public function toUnit()
    {
        return $this->belongsTo(Unit::class, 'to_unit_id');
    }

    /**
     * 关联上级换算
     */
    public function parentConversion()
    {
        return $this->belongsTo(UnitConversion::class, 'parent_conversion_id');
    }

    /**
     * 关联下级换算
     */
    public function childConversion()
    {
        return $this->hasOne(UnitConversion::class, 'parent_conversion_id');
    }

    /**
     * 作用域：启用
     */
    public function scopeEnabled($query)
    {
        return $query->where('status', self::STATUS_ENABLED);
    }

    /**
     * 作用域：按 SKU
     */
    public function scopeBySku($query, int $skuId)
    {
        return $query->where('sku_id', $skuId);
    }
}
