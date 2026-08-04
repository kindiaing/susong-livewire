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
    // 类型常量
    public const TYPE_MERCHANT = 1;
    public const TYPE_DRIVER = 2;

    // 状态常量
    public const STATUS_DISABLED = 0;
    public const STATUS_ENABLED = 1;

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

    /**
     * 类型映射
     */
    public static function typeMap(): array
    {
        return [
            self::TYPE_MERCHANT => '商家端',
            self::TYPE_DRIVER => '司机端',
        ];
    }

    /**
     * 状态映射
     */
    public static function statusMap(): array
    {
        return [
            self::STATUS_ENABLED => '启用',
            self::STATUS_DISABLED => '禁用',
        ];
    }

    public function getTypeLabelAttribute(): string
    {
        return self::typeMap()[$this->type] ?? '未知';
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusMap()[$this->status] ?? '未知';
    }

    /**
     * 关联系统用户
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 作用域：启用
     */
    public function scopeEnabled($query)
    {
        return $query->where('status', self::STATUS_ENABLED);
    }
}
