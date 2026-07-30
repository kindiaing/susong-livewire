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
}
