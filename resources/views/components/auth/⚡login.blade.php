<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

new class extends Livewire\Component
{
    public string $login = '';
    public string $password = '';
    public bool $remember = false;

    public function login(): void
    {
        $this->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        // 节流：每分钟最多5次尝试
        $key = 'login:' . strtolower($this->login);

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'login' => "尝试次数过多，请在 {$seconds} 秒后再试。",
            ]);
        }

        RateLimiter::hit($key);

        // 尝试查找用户（支持用户名/手机号/邮箱）
        $user = User::where('username', $this->login)
            ->orWhere('phone', $this->login)
            ->orWhere('email', $this->login)
            ->first();

        if (! $user || ! password_verify($this->password, $user->password)) {
            throw ValidationException::withMessages([
                'login' => '用户名/手机号/邮箱或密码不正确。',
            ]);
        }

        if ($user->status !== 1) {
            throw ValidationException::withMessages([
                'login' => '该账号已被禁用，请联系管理员。',
            ]);
        }

        // 登录成功，清除节流
        RateLimiter::clear($key);

        // 记录登录时间
        $user->update(['last_login_at' => now()]);

        // 记录登录日志
        \DB::table('login_logs')->insert([
            'user_id' => $user->id,
            'username' => $this->login,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'login_type' => 1,
            'status' => 1,
            'created_at' => now(),
        ]);

        Auth::login($user, $this->remember);

        $this->redirectIntended(default: route('dashboard'));
    }
}; ?>

<x-guest-layout>
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-50 px-4 py-12">
    <div class="w-full max-w-md">
        <!-- Logo & Title -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-primary text-primary-foreground mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-foreground">速送服务平台</h1>
            <p class="text-muted-foreground mt-1">管理后台登录</p>
        </div>

        <!-- Login Card -->
        <x-ui.card class="shadow-lg">
            <x-ui.card-content class="p-6">
                <form wire:submit="login" class="space-y-4">
                    <!-- Login Field -->
                    <div class="space-y-2">
                        <x-ui.input
                            wire:model="login"
                            type="text"
                            placeholder="用户名 / 手机号 / 邮箱"
                            autofocus
                            required
                        />
                        @error('login')
                            <p class="text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div class="space-y-2">
                        <x-ui.input
                            wire:model="password"
                            type="password"
                            placeholder="密码"
                            required
                        />
                        @error('password')
                            <p class="text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center gap-2">
                        <x-ui.checkbox wire:model="remember" id="remember" />
                        <label for="remember" class="text-sm text-muted-foreground cursor-pointer select-none">
                            记住登录状态 7 天
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <x-ui.button type="submit" variant="default" class="w-full" wire:loading.attr="disabled">
                        <x-ui.spinner class="mr-2" wire:loading />
                        <span wire:loading.remove>登 录</span>
                        <span wire:loading>登录中...</span>
                    </x-ui.button>
                </form>
            </x-ui.card-content>
        </x-ui.card>

        <!-- Footer -->
        <p class="text-center text-xs text-muted-foreground mt-6">
            &copy; {{ now()->year }} 速送服务平台 · 本地速送服务管理后台
        </p>
    </div>
</div>
</x-guest-layout>
