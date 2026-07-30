<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Login extends Component
{
    public string $username = '';
    public string $password = '';
    public bool $remember = false;

    public function mount(): void
    {
        if (auth()->check()) {
            $this->redirectIntended(default: route('dashboard'));
        }
    }

    public function login(): void
    {
        $this->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // 节流：每分钟最多5次尝试
        $key = 'login:' . strtolower($this->username);

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'username' => "尝试次数过多，请在 {$seconds} 秒后再试。",
            ]);
        }

        RateLimiter::hit($key);

        // 尝试查找用户（支持用户名/手机号/邮箱）
        $user = User::where('username', $this->username)
            ->orWhere('phone', $this->username)
            ->orWhere('email', $this->username)
            ->first();

        if (! $user || ! password_verify($this->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => '用户名/手机号/邮箱或密码不正确。',
            ]);
        }

        if ($user->status !== 1) {
            throw ValidationException::withMessages([
                'username' => '该账号已被禁用，请联系管理员。',
            ]);
        }

        // 登录成功，清除节流
        RateLimiter::clear($key);

        // 记录登录时间
        $user->update(['last_login_at' => now()]);

        // 记录登录日志
        \DB::table('login_logs')->insert([
            'user_id' => $user->id,
            'username' => $this->username,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'login_type' => 1,
            'status' => 1,
            'created_at' => now(),
        ]);

        Auth::login($user, $this->remember);

        $this->redirectIntended(default: route('dashboard'));
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('components.guest-layout');
    }
}
