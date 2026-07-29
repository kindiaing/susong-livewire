<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * 常购清单模型
 *
 * @property int $id
 * @property int $merchant_id 商家ID
 * @property int $sku_id SKU ID
 * @property int $buy_count 购买次数
 * @property Carbon|null $last_buy_at 最近购买时间
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class FrequentlyBought extends Model
{
    protected $fillable = [
        'merchant_id',
        'sku_id',
        'buy_count',
        'last_buy_at',
    ];

    protected function casts(): array
    {
        return [
            'merchant_id' => 'integer',
            'sku_id' => 'integer',
            'buy_count' => 'integer',
            'last_buy_at' => 'datetime',
        ];
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function sku()
    {
        return $this->belongsTo(Sku::class);
    }
}
