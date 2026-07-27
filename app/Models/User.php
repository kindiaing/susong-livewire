<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $username
 * @property string $name
 * @property string|null $phone
 * @property string|null $email
 * @property string $password
 * @property string|null $avatar
 * @property int $status
 * @property Carbon|null $last_login_at
 * @property Carbon|null $email_verified_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $guard_name = 'web';

    protected $fillable = [
        'username',
        'name',
        'phone',
        'email',
        'password',
        'avatar',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'integer',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * 查找用于登录的用户名/手机号/邮箱
     */
    public function findForPassport(string $username): ?self
    {
        return static::where('username', $username)
            ->orWhere('phone', $username)
            ->orWhere('email', $username)
            ->first();
    }
}
