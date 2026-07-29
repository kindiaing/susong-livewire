<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

    use SoftDeletes;
/**
 * 仓库模型
 *
 * @property mixed $name 仓库名称
 * @property mixed $type 类型：1总仓2前置仓
 * @property mixed $is_cold_chain 是否冷链0否1是
 * @property mixed $address 地址
 * @property mixed $status 状态
 */
class Warehouse extends Model
{

    protected $fillable = [
        'name',
        'type',
        'is_cold_chain',
        'address',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'type' => 'integer',
            'is_cold_chain' => 'integer',
            'status' => 'integer',
        ];
    }

    public const TYPE_GENERAL = 1;
    public const TYPE_FRONT = 2;

    public static function typeMap(): array
    {
        return [
            self::TYPE_GENERAL => '总仓',
            self::TYPE_FRONT => '前置仓',
        ];
    }
}
