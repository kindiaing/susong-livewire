<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

    use SoftDeletes;
/**
 * 商家收货地址模型
 *
 * @property mixed $merchant_id 商家ID
 * @property mixed $contact_name 联系人
 * @property mixed $contact_phone 联系电话
 * @property mixed $address 收货地址
 * @property mixed $is_default 是否默认地址0否1是
 * @property mixed $sort 排序
 */
class MerchantAddress extends Model
{

    protected $fillable = [
        'merchant_id',
        'contact_name',
        'contact_phone',
        'address',
        'is_default',
        'sort',
    ];

    protected function casts(): array
    {
        return [
            'merchant_id' => 'integer',
            'is_default' => 'integer',
            'sort' => 'integer',
        ];
    }

}
