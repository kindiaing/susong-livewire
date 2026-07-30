<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 运营主推模型
 *
 * @property mixed $type 类型：1主推商品2主推品类
 * @property mixed $target_id 目标ID
 * @property mixed $sort 排序
 * @property mixed $start_at 开始时间
 * @property mixed $end_at 结束时间
 * @property mixed $status 状态
 */
class Promotion extends Model
{

    protected $fillable = [
        'type',
        'target_id',
        'sort',
        'start_at',
        'end_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'type' => 'integer',
            'target_id' => 'integer',
            'sort' => 'integer',
            'status' => 'integer',
            'start_at' => 'datetime',
            'end_at' => 'datetime',
        ];
    }

}
