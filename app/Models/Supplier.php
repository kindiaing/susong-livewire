<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * 供应商模型
 *
 * @property int $id
 * @property string $name 供应商名称
 * @property string|null $contact_name 联系人
 * @property string|null $contact_phone 联系电话
 * @property string|null $address 地址
 * @property string|null $bank_name 开户银行
 * @property string|null $bank_account 银行账号
 * @property int $settlement_cycle 结算周期：1周结，2月结
 * @property int $status 状态：1启用，2禁用
 * @property string|null $remark 备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Supplier extends Model
{
    use SoftDeletes;

    // 结算周期常量
    public const CYCLE_WEEKLY = 1;

    public const CYCLE_MONTHLY = 2;

    public const CYCLE_IRREGULAR = 3;

    // 状态常量（与迁移一致：1启用，2禁用）
    public const STATUS_ENABLED = 1;

    public const STATUS_DISABLED = 2;

    protected $fillable = [
        'name',
        'contact_name',
        'contact_phone',
        'address',
        'bank_name',
        'bank_account',
        'settlement_cycle',
        'status',
        'remark',
    ];

    protected function casts(): array
    {
        return [
            'settlement_cycle' => 'integer',
            'status' => 'integer',
        ];
    }

    /**
     * 结算周期映射
     */
    public static function settlementCycleMap(): array
    {
        return [
            self::CYCLE_WEEKLY => '周结',
            self::CYCLE_MONTHLY => '月结',
            self::CYCLE_IRREGULAR => '不定期',
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

    /**
     * 关联采购单
     */
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    /**
     * 关联结算单
     */
    public function settlements()
    {
        return $this->hasMany(SupplierSettlement::class);
    }

    /**
     * 关联 SKU 供应商
     */
    public function skuSuppliers()
    {
        return $this->hasMany(SkuSupplier::class);
    }

    /**
     * 作用域：启用
     */
    public function scopeEnabled($query)
    {
        return $query->where('status', self::STATUS_ENABLED);
    }
}
