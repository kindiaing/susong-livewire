<?php

use App\Livewire\Auth\Login;
use App\Livewire\System\ApprovalConfig;
use App\Livewire\System\Approvals;
use App\Livewire\System\AuditLogs;
use App\Livewire\System\OperationLogs;
use App\Livewire\System\Settings;
use App\Livewire\User\Profile;
use Illuminate\Support\Facades\Route;

// 首页（公开，无需登录）
Route::view('/', 'welcome')->name('home');

// 认证路由
Route::get('/login', Login::class)->name('login');
Route::post('/logout', function () {
    auth()->logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('home');
})->name('logout');

// 需要登录的路由
Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'pages.dashboard')->name('dashboard');

    // 个人中心
    Route::get('/profile', Profile::class)->name('profile');

    // ── 系统管理 ──
    Route::get('/settings', Settings::class)->name('settings');
    Route::get('/approval-config', ApprovalConfig::class)->name('approval-config');
    Route::get('/approvals', Approvals::class)->name('approvals');
    Route::get('/operation-logs', OperationLogs::class)->name('operation-logs');
    Route::get('/audit-logs', AuditLogs::class)->name('audit-logs');
});

// 开发演示页（无需登录，生产环境应移除）
Route::view('/demo', 'pages.demo')->name('demo');
