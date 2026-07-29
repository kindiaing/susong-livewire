<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 微信用户模型
 *
 * @property mixed $user_id 关联系统用户ID
 * @property mixed $openid 微信OpenID
 * @property mixed $unionid 微信UnionID
 * @property mixed $nickname 昵称
 * @property mixed $avatar 头像
 * @property mixed $type 类型：1商家端2司机端
 * @property mixed $status 状态
 */
class WechatUser extends Model
{

    protected $fillable = [
        'user_id',
        'openid',
        'unionid',
        'nickname',
        'avatar',
        'type',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'type' => 'integer',
            'status' => 'integer',
        ];
    }

}
