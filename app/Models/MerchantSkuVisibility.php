<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * 商家SKU可见性模型
 *
 * @property int $id
 * @property int $merchant_id 商家ID
 * @property int $sku_id SKU ID
 * @property int $is_visible 是否可见：0否，1是
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class MerchantSkuVisibility extends Model
{
    protected $table = 'merchant_sku_visibility';

    protected $fillable = [
        'merchant_id',
        'sku_id',
        'is_visible',
    ];

    protected function casts(): array
    {
        return [
            'merchant_id' => 'integer',
            'sku_id' => 'integer',
            'is_visible' => 'integer',
        ];
    }

    /**
     * 关联商家
     */
    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    /**
     * 关联 SKU
     */
    public function sku()
    {
        return $this->belongsTo(Sku::class);
    }

    /**
     * 作用域：可见
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', 1);
    }

    /**
     * 作用域：按商家筛选
     */
    public function scopeByMerchant($query, int $merchantId)
    {
        return $query->where('merchant_id', $merchantId);
    }

    /**
     * 作用域：按SKU筛选
     */
    public function scopeBySku($query, int $skuId)
    {
        return $query->where('sku_id', $skuId);
    }
}
