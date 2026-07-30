<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 登录日志模型
 *
 * @property mixed $user_id 用户ID
 * @property mixed $username 登录账号
 * @property mixed $ip IP地址
 * @property mixed $user_agent 浏览器UA
 * @property mixed $login_type 类型：1管理后台2商家小程序3司机小程序
 * @property mixed $status 结果：1成功0失败
 * @property mixed $fail_reason 失败原因
 */
class LoginLog extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'username',
        'ip',
        'user_agent',
        'login_type',
        'status',
        'fail_reason',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'login_type' => 'integer',
            'status' => 'integer',
        ];
    }

}
