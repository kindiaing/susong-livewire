<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * 商家模型
 *
 * @property int $id
 * @property int|null $user_id 关联用户ID
 * @property string $name 商家名称
 * @property string|null $contact_name 联系人
 * @property string|null $contact_phone 联系电话
 * @property string|null $address 地址
 * @property int|null $delivery_route_id 配送线路ID
 * @property int $delivery_sort 配送顺序
 * @property int $min_order_amount 起送金额（厘）
 * @property int $settlement_type 结算方式：1现结，2账期，3预付款
 * @property int $credit_limit 信用额度（厘）
 * @property int $status 状态：1启用，2禁用
 * @property string|null $remark 备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Merchant extends Model
{
    use SoftDeletes;

    // 结算方式常量
    public const SETTLEMENT_CASH = 1;
    public const SETTLEMENT_CREDIT = 2;
    public const SETTLEMENT_PREPAID = 3;

    // 状态常量
    public const STATUS_ENABLED = 1;
    public const STATUS_DISABLED = 2;

    protected $fillable = [
        'user_id',
        'name',
        'contact_name',
        'contact_phone',
        'address',
        'delivery_route_id',
        'delivery_sort',
        'min_order_amount',
        'settlement_type',
        'credit_limit',
        'status',
        'remark',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'delivery_route_id' => 'integer',
            'delivery_sort' => 'integer',
            'min_order_amount' => 'integer',
            'settlement_type' => 'integer',
            'credit_limit' => 'integer',
            'status' => 'integer',
        ];
    }

    /**
     * 结算方式映射
     */
    public static function settlementTypeMap(): array
    {
        return [
            self::SETTLEMENT_CASH => '现结',
            self::SETTLEMENT_CREDIT => '账期',
            self::SETTLEMENT_PREPAID => '预付款',
        ];
    }

    /**
     * 状态映射
     */
    public static function statusMap(): array
    {
        return [
            self::STATUS_ENABLED => '启用',
            self::STATUS_DISABLED => '禁用',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusMap()[$this->status] ?? '未知';
    }

    public function getSettlementTypeLabelAttribute(): string
    {
        return self::settlementTypeMap()[$this->settlement_type] ?? '未知';
    }

    /**
     * 关联用户
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 关联配送线路
     */
    public function deliveryRoute()
    {
        return $this->belongsTo(DeliveryRoute::class);
    }

    /**
     * 关联商家账户
     */
    public function account()
    {
        return $this->hasOne(MerchantAccount::class);
    }

    /**
     * 关联订单
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * 关联收货地址
     */
    public function addresses()
    {
        return $this->hasMany(MerchantAddress::class);
    }

    /**
     * 作用域：启用
     */
    public function scopeEnabled($query)
    {
        return $query->where('status', self::STATUS_ENABLED);
    }
}
