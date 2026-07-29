<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

    use SoftDeletes;
/**
 * 价格策略模型
 *
 * @property mixed $name 策略名称
 * @property mixed $code 策略编码
 * @property mixed $type 类型：1促销2临时改价
 * @property mixed $target_type 作用对象：1供应商2商家3全部
 * @property mixed $scope 作用范围：1采购2销售3通用
 * @property mixed $status 状态：0禁用1启用
 * @property mixed $approval_status 审核状态：1待审核2已通过3已拒绝
 * @property mixed $start_at 生效开始时间
 * @property mixed $end_at 生效结束时间
 * @property mixed $created_by 创建人ID
 * @property mixed $remark 备注
 */
class PriceStrategy extends Model
{

    protected $fillable = [
        'name',
        'code',
        'type',
        'target_type',
        'scope',
        'status',
        'approval_status',
        'start_at',
        'end_at',
        'created_by',
        'remark',
    ];

    protected function casts(): array
    {
        return [
            'type' => 'integer',
            'target_type' => 'integer',
            'scope' => 'integer',
            'status' => 'integer',
            'approval_status' => 'integer',
            'created_by' => 'integer',
            'start_at' => 'datetime',
            'end_at' => 'datetime',
        ];
    }

}
