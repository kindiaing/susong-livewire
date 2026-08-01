<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 签收存证模型
 *
 * @property mixed $order_id 订单ID
 * @property mixed $delivery_task_id 配送任务ID
 * @property mixed $type 类型：1拍照签收2电子签名3质检照片
 * @property mixed $image_url 图片地址
 * @property mixed $signer_name 签收人
 * @property mixed $signed_at 签收时间
 */
class Signature extends Model
{
    use SoftDeletes;

    // 类型常量
    public const TYPE_PHOTO = 1;
    public const TYPE_ELECTRONIC = 2;
    public const TYPE_QC = 3;

    protected $fillable = [
        'order_id',
        'delivery_task_id',
        'type',
        'image_url',
        'signer_name',
        'signed_at',
    ];

    protected function casts(): array
    {
        return [
            'order_id' => 'integer',
            'delivery_task_id' => 'integer',
            'type' => 'integer',
            'signed_at' => 'datetime',
        ];
    }

    /**
     * 类型映射
     */
    public static function typeMap(): array
    {
        return [
            self::TYPE_PHOTO => '拍照签收',
            self::TYPE_ELECTRONIC => '电子签名',
            self::TYPE_QC => '质检照片',
        ];
    }

    public function getTypeLabelAttribute(): string
    {
        return self::typeMap()[$this->type] ?? '未知';
    }

    /**
     * 关联订单
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * 关联配送任务
     */
    public function deliveryTask()
    {
        return $this->belongsTo(DeliveryTask::class);
    }
}
