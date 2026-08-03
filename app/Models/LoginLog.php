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

    // 登录类型常量
    public const LOGIN_ADMIN = 1;
    public const LOGIN_MERCHANT = 2;
    public const LOGIN_DRIVER = 3;

    // 结果常量
    public const RESULT_FAIL = 0;
    public const RESULT_SUCCESS = 1;

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

    /**
     * 登录类型映射
     */
    public static function loginTypeMap(): array
    {
        return [
            self::LOGIN_ADMIN => '管理后台',
            self::LOGIN_MERCHANT => '商家小程序',
            self::LOGIN_DRIVER => '司机小程序',
        ];
    }

    /**
     * 结果映射
     */
    public static function statusMap(): array
    {
        return [
            self::RESULT_SUCCESS => '成功',
            self::RESULT_FAIL => '失败',
        ];
    }

    public function getLoginTypeLabelAttribute(): string
    {
        return self::loginTypeMap()[$this->login_type] ?? '未知';
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusMap()[$this->status] ?? '未知';
    }

    /**
     * 关联用户
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 作用域：按登录类型
     */
    public function scopeByLoginType($query, int $type)
    {
        return $query->where('login_type', $type);
    }

    /**
     * 作用域：成功
     */
    public function scopeSuccess($query)
    {
        return $query->where('status', self::RESULT_SUCCESS);
    }

    /**
     * 作用域：失败
     */
    public function scopeFailed($query)
    {
        return $query->where('status', self::RESULT_FAIL);
    }
}
