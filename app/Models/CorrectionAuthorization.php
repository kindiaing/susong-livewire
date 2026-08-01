<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 授权更正模型
 *
 * @property mixed $order_id 订单ID
 * @property mixed $operator_id 授权人ID
 * @property mixed $reason 更正原因
 * @property mixed $before_data 修改前数据
 * @property mixed $after_data 修改后数据
 * @property mixed $authorized_at 授权时间
 */
class CorrectionAuthorization extends Model
{

    protected $fillable = [
        'order_id',
        'operator_id',
        'reason',
        'before_data',
        'after_data',
        'authorized_at',
    ];

    protected function casts(): array
    {
        return [
            'order_id' => 'integer',
            'operator_id' => 'integer',
            'before_data' => 'array',
            'after_data' => 'array',
            'authorized_at' => 'datetime',
        ];
    }

    /**
     * 关联订单
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * 关联授权人
     */
    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }
}
