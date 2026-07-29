<?php

use App\Livewire\Auth\Login;
use App\Livewire\Org\DriverList;
use App\Livewire\Org\MerchantList;
use App\Livewire\Org\RouteList;
use App\Livewire\Org\SupplierList;
use App\Livewire\Org\VehicleList;
use App\Livewire\System\ApprovalConfig;
use App\Livewire\System\Approvals;
use App\Livewire\System\AuditLogs;
use App\Livewire\System\OperationLogs;
use App\Livewire\System\Settings;
use App\Livewire\User\PermissionList;
use App\Livewire\User\Profile;
use App\Livewire\User\RoleList;
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

    // ── 用户权限 ──
    Route::get('/roles', RoleList::class)->name('roles');
    Route::get('/permissions', PermissionList::class)->name('permissions');

    // ── 组织主体 ──
    Route::get('/suppliers', SupplierList::class)->name('suppliers');
    Route::get('/merchants', MerchantList::class)->name('merchants');
    Route::get('/delivery-routes', RouteList::class)->name('delivery-routes');
    Route::get('/drivers', DriverList::class)->name('drivers');
    Route::get('/vehicles', VehicleList::class)->name('vehicles');

    // ── 系统管理 ──
    Route::get('/settings', Settings::class)->name('settings');
    Route::get('/approval-config', ApprovalConfig::class)->name('approval-config');
    Route::get('/approvals', Approvals::class)->name('approvals');
    Route::get('/operation-logs', OperationLogs::class)->name('operation-logs');
    Route::get('/audit-logs', AuditLogs::class)->name('audit-logs');
});

// 开发演示页（无需登录，生产环境应移除）
Route::view('/demo', 'pages.demo')->name('demo');
