<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * 单位主数据模型
 *
 * @property int $id
 * @property string $name 单位名称
 * @property string|null $symbol 单位简称/符号
 * @property int $status 状态：0禁用，1启用
 * @property int $sort 排序
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Unit extends Model
{
    public const STATUS_DISABLED = 0;
    public const STATUS_ENABLED = 1;

    protected $fillable = [
        'name',
        'symbol',
        'status',
        'sort',
    ];

    protected function casts(): array
    {
        return [
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
     * 关联：作为换算的大单位（from_unit）
     */
    public function fromConversions()
    {
        return $this->hasMany(UnitConversion::class, 'from_unit_id');
    }

    /**
     * 关联：作为换算的小单位（to_unit）
     */
    public function toConversions()
    {
        return $this->hasMany(UnitConversion::class, 'to_unit_id');
    }

    /**
     * 作用域：启用
     */
    public function scopeEnabled($query)
    {
        return $query->where('status', self::STATUS_ENABLED);
    }
}
