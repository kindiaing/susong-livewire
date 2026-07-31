<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 用户偏好模型
 *
 * @property int $id
 * @property int $user_id
 * @property string $pref_key
 * @property array|null $pref_value
 */
class UserPreference extends Model
{
    protected $fillable = [
        'user_id',
        'pref_key',
        'pref_value',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'pref_value' => 'array',
        ];
    }

    /**
     * 获取用户偏好值
     */
    public static function getPreference(int $userId, string $key, $default = null)
    {
        $pref = static::where('user_id', $userId)
            ->where('pref_key', $key)
            ->value('pref_value');

        return $pref ?? $default;
    }

    /**
     * 设置用户偏好值
     */
    public static function setPreference(int $userId, string $key, $value): void
    {
        static::updateOrCreate(
            ['user_id' => $userId, 'pref_key' => $key],
            ['pref_value' => $value],
        );
    }

    /**
     * 关联用户
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
