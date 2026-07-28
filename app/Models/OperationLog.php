<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * 操作日志模型
 *
 * @property int $id
 * @property int|null $user_id 操作人ID
 * @property string|null $username 操作人用户名
 * @property string|null $method 请求方法
 * @property string|null $path 请求路径
 * @property string|null $ip IP地址
 * @property string|null $content 操作内容
 * @property Carbon|null $created_at
 */
class OperationLog extends Model
{
    public $timestamps = false;

    protected $table = 'operation_logs';

    const CREATED_AT = 'created_at';

    protected $fillable = [
        'user_id',
        'username',
        'method',
        'path',
        'ip',
        'content',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    /**
     * 操作人
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 请求方法颜色映射
     */
    public static function methodColorMap(): array
    {
        return [
            'GET' => 'blue',
            'POST' => 'green',
            'PUT' => 'orange',
            'PATCH' => 'orange',
            'DELETE' => 'red',
        ];
    }

    public function getMethodColorAttribute(): string
    {
        return self::methodColorMap()[$this->method] ?? 'gray';
    }

    /**
     * 作用域：按用户
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * 作用域：按路径
     */
    public function scopeByPath($query, string $path)
    {
        return $query->where('path', 'like', "%{$path}%");
    }

    /**
     * 作用域：按方法
     */
    public function scopeByMethod($query, string $method)
    {
        return $query->where('method', strtoupper($method));
    }

    /**
     * 记录操作日志
     */
    public static function log(string $content, ?string $method = null, ?string $path = null): self
    {
        return static::create([
            'user_id' => auth()->id(),
            'username' => auth()->user()?->username,
            'method' => $method ?? request()->method(),
            'path' => $path ?? request()->path(),
            'ip' => request()->ip(),
            'content' => $content,
            'created_at' => now(),
        ]);
    }
}
