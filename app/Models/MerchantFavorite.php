<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 商家收藏模型
 *
 * @property mixed $merchant_id 商家ID
 * @property mixed $sku_id SKU ID
 */
class MerchantFavorite extends Model
{

    protected $fillable = [
        'merchant_id',
        'sku_id',
    ];

    protected function casts(): array
    {
        return [
            'merchant_id' => 'integer',
            'sku_id' => 'integer',
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
