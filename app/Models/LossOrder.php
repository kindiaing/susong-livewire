<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

    use SoftDeletes;
/**
 * 损耗单模型
 *
 * @property mixed $loss_no 损耗单号
 * @property mixed $warehouse_id 仓库ID
 * @property mixed $total_amount 损耗总金额
 * @property mixed $loss_type 主要损耗类型：1存储腐坏2称重失水3过期报废4加工损耗5盘点差异6其他
 * @property mixed $status 状态：1待审核2已通过3已执行4已关闭9已取消
 * @property mixed $approval_status 审核状态：1待审核2已通过3已拒绝
 * @property mixed $applicant_id 申请人ID
 * @property mixed $reviewer_id 审核人ID
 * @property mixed $reviewed_at 审核时间
 * @property mixed $executed_at 执行时间
 * @property mixed $closed_at 关闭时间
 * @property mixed $reason 损耗原因
 * @property mixed $remark 备注
 */
class LossOrder extends Model
{

    protected $fillable = [
        'loss_no',
        'warehouse_id',
        'total_amount',
        'loss_type',
        'status',
        'approval_status',
        'applicant_id',
        'reviewer_id',
        'reviewed_at',
        'executed_at',
        'closed_at',
        'reason',
        'remark',
    ];

    protected function casts(): array
    {
        return [
            'warehouse_id' => 'integer',
            'total_amount' => 'integer',
            'loss_type' => 'integer',
            'status' => 'integer',
            'approval_status' => 'integer',
            'applicant_id' => 'integer',
            'reviewer_id' => 'integer',
            'reviewed_at' => 'datetime',
            'executed_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public const LOSS_STORAGE = 1;
    public const LOSS_WEIGH = 2;
    public const LOSS_EXPIRED = 3;
    public const LOSS_PROCESS = 4;
    public const LOSS_INVENTORY = 5;
    public const LOSS_OTHER = 6;

    public static function typeMap(): array
    {
        return [
            self::LOSS_STORAGE => '存储腐坏',
            self::LOSS_WEIGH => '称重失水',
            self::LOSS_EXPIRED => '过期报废',
            self::LOSS_PROCESS => '加工损耗',
            self::LOSS_INVENTORY => '盘点差异',
            self::LOSS_OTHER => '其他',
        ];
    }
}
