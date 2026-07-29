<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 发票管理模型
 *
 * @property mixed $invoice_no 发票号
 * @property mixed $type 类型：1客户发票2供应商发票
 * @property mixed $target_id 关联对象ID
 * @property mixed $title 发票抬头
 * @property mixed $amount 金额
 * @property mixed $file_url 发票文件地址
 * @property mixed $status 状态：1待开具2已开具3已寄出
 * @property mixed $applied_at 申请时间
 * @property mixed $issued_at 开具时间
 * @property mixed $sent_at 寄出时间
 */
class Invoice extends Model
{

    protected $fillable = [
        'invoice_no',
        'type',
        'target_id',
        'title',
        'amount',
        'file_url',
        'status',
        'applied_at',
        'issued_at',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => 'integer',
            'target_id' => 'integer',
            'amount' => 'integer',
            'status' => 'integer',
            'applied_at' => 'datetime',
            'issued_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

}
