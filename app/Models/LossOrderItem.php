<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * 损耗单明细模型
 *
 * @property int $id
 * @property int $loss_order_id 损耗单ID
 * @property int $sku_id SKU ID
 * @property int $loss_type 损耗类型
 * @property int $quantity 损耗数量
 * @property int $cost_price SKU成本价快照（厘）
 * @property int $loss_amount 损耗金额（厘）
 * @property int $responsible_party 责任方：1平台，2供应商
 * @property int|null $supplier_id 供应商ID
 * @property string|null $reason 明细损耗原因
 * @property array|null $evidence_urls 凭证图片数组
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class LossOrderItem extends Model
{
    // 责任方常量
    public const PARTY_PLATFORM = 1;
    public const PARTY_SUPPLIER = 2;

    protected $fillable = [
        'loss_order_id',
        'sku_id',
        'loss_type',
        'quantity',
        'cost_price',
        'loss_amount',
        'responsible_party',
        'supplier_id',
        'reason',
        'evidence_urls',
    ];

    protected function casts(): array
    {
        return [
            'loss_order_id' => 'integer',
            'sku_id' => 'integer',
            'loss_type' => 'integer',
            'quantity' => 'integer',
            'cost_price' => 'integer',
            'loss_amount' => 'integer',
            'responsible_party' => 'integer',
            'supplier_id' => 'integer',
            'evidence_urls' => 'array',
        ];
    }

    /**
     * 责任方映射
     */
    public static function responsiblePartyMap(): array
    {
        return [
            self::PARTY_PLATFORM => '平台',
            self::PARTY_SUPPLIER => '供应商',
        ];
    }

    public function getResponsiblePartyLabelAttribute(): string
    {
        return self::responsiblePartyMap()[$this->responsible_party] ?? '未知';
    }

    /**
     * 关联损耗单
     */
    public function lossOrder()
    {
        return $this->belongsTo(LossOrder::class);
    }

    /**
     * 关联 SKU
     */
    public function sku()
    {
        return $this->belongsTo(Sku::class);
    }

    /**
     * 关联供应商
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
