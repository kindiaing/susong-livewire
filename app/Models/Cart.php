<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * 购物车模型
 *
 * @property int $id
 * @property int $merchant_id 商家ID
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Cart extends Model
{
    protected $fillable = [
        'merchant_id',
    ];

    protected function casts(): array
    {
        return [
            'merchant_id' => 'integer',
        ];
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
}
