<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * 配送顺序表模型 — 按线路顺序 + 标记
 *
 * @property int $id
 * @property int $task_id 所属配送任务ID
 * @property array $task_detail_ids 本商家在本任务中的所有明细ID数组
 * @property int $merchant_id 商家ID
 * @property string|null $merchant_name 商家名称（冗余）
 * @property string|null $merchant_address 地址（冗余）
 * @property string|null $latitude 纬度
 * @property string|null $longitude 经度
 * @property int $base_sequence_no 来自线路的原始顺序号（永不变）
 * @property int $sequence_no 本次任务中的实际顺序号
 * @property Carbon|null $estimated_arrival 预计到达时间
 * @property Carbon|null $estimated_departure 预计离开时间
 * @property Carbon|null $actual_arrival 实际到达时间
 * @property Carbon|null $actual_departure 实际离开时间
 * @property Carbon|null $actual_delivered_at 实际送达/签收时间
 * @property int $is_urgent 是否加急
 * @property string|null $urgent_reason 加急原因
 * @property int $is_important 是否重要
 * @property string|null $important_reason 重要原因
 * @property int $status 状态
 * @property string|null $delivery_method 确认方式
 * @property array|null $delivery_photos 配送照片
 * @property string|null $signature_image 签名图片URL
 * @property string|null $gps_latitude 送达时纬度
 * @property string|null $gps_longitude 送达时经度
 * @property string|null $skip_reason 跳过原因
 * @property string|null $fail_reason 失败原因
 * @property string|null $remark 备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class DeliveryTaskSequence extends Model
{
    // 状态常量
    public const STATUS_PENDING = 1;      // 待配送
    public const STATUS_IN_PROGRESS = 2;  // 配送中
    public const STATUS_ARRIVED = 3;      // 已到达
    public const STATUS_DELIVERED = 4;    // 已送达
    public const STATUS_SKIPPED = 5;      // 已跳过
    public const STATUS_FAILED = 6;       // 失败

    protected $fillable = [
        'task_id',
        'task_detail_ids',
        'merchant_id',
        'merchant_name',
        'merchant_address',
        'latitude',
        'longitude',
        'base_sequence_no',
        'sequence_no',
        'estimated_arrival',
        'estimated_departure',
        'actual_arrival',
        'actual_departure',
        'actual_delivered_at',
        'is_urgent',
        'urgent_reason',
        'is_important',
        'important_reason',
        'status',
        'delivery_method',
        'delivery_photos',
        'signature_image',
        'gps_latitude',
        'gps_longitude',
        'skip_reason',
        'fail_reason',
        'remark',
    ];

    protected function casts(): array
    {
        return [
            'task_id' => 'integer',
            'task_detail_ids' => 'array',
            'merchant_id' => 'integer',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'base_sequence_no' => 'integer',
            'sequence_no' => 'integer',
            'estimated_arrival' => 'datetime',
            'estimated_departure' => 'datetime',
            'actual_arrival' => 'datetime',
            'actual_departure' => 'datetime',
            'actual_delivered_at' => 'datetime',
            'is_urgent' => 'integer',
            'is_important' => 'integer',
            'status' => 'integer',
            'delivery_photos' => 'array',
            'gps_latitude' => 'decimal:8',
            'gps_longitude' => 'decimal:8',
        ];
    }

    /**
     * 状态映射
     */
    public static function statusMap(): array
    {
        return [
            self::STATUS_PENDING => '待配送',
            self::STATUS_IN_PROGRESS => '配送中',
            self::STATUS_ARRIVED => '已到达',
            self::STATUS_DELIVERED => '已送达',
            self::STATUS_SKIPPED => '已跳过',
            self::STATUS_FAILED => '失败',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusMap()[$this->status] ?? '未知';
    }

    // ========== 关联 ==========

    /**
     * 关联配送任务
     */
    public function task()
    {
        return $this->belongsTo(DeliveryTask::class, 'task_id');
    }

    /**
     * 关联商家
     */
    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    /**
     * 获取关联的任务明细（通过 task_detail_ids）
     */
    public function taskDetails()
    {
        return DeliveryTaskDetail::whereIn('id', $this->task_detail_ids ?? [])->get();
    }

    // ========== 业务方法 ==========

    /**
     * 标记加急
     */
    public function markUrgent(string $reason = ''): void
    {
        $this->update([
            'is_urgent' => 1,
            'urgent_reason' => $reason,
        ]);

        // 同步更新任务的 has_urgent 标记
        $this->task->update(['has_urgent' => 1]);
    }

    /**
     * 取消加急
     */
    public function unmarkUrgent(): void
    {
        $this->update([
            'is_urgent' => 0,
            'urgent_reason' => null,
        ]);

        // 检查任务中是否还有其他加急商家
        $hasOtherUrgent = static::where('task_id', $this->task_id)
            ->where('id', '!=', $this->id)
            ->where('is_urgent', 1)
            ->exists();

        if (!$hasOtherUrgent) {
            $this->task->update(['has_urgent' => 0]);
        }
    }

    /**
     * 标记重要
     */
    public function markImportant(string $reason = ''): void
    {
        $this->update([
            'is_important' => 1,
            'important_reason' => $reason,
        ]);

        $this->task->update(['has_important' => 1]);
    }

    /**
     * 取消重要
     */
    public function unmarkImportant(): void
    {
        $this->update([
            'is_important' => 0,
            'important_reason' => null,
        ]);

        $hasOtherImportant = static::where('task_id', $this->task_id)
            ->where('id', '!=', $this->id)
            ->where('is_important', 1)
            ->exists();

        if (!$hasOtherImportant) {
            $this->task->update(['has_important' => 0]);
        }
    }

    // ========== 作用域 ==========

    public function scopeByStatus($query, int $status)
    {
        return $query->where('status', $status);
    }

    public function scopeUrgent($query)
    {
        return $query->where('is_urgent', 1);
    }

    public function scopeImportant($query)
    {
        return $query->where('is_important', 1);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sequence_no');
    }
}
