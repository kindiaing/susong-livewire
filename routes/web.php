<?php

use App\Livewire\Auth\Login;
use App\Livewire\Org\DriverList;
use App\Livewire\Org\MerchantList;
use App\Livewire\Org\RouteList;
use App\Livewire\Org\SupplierList;
use App\Livewire\Org\VehicleList;
use App\Livewire\Order\CartList;
use App\Livewire\Order\FrequentlyBoughtList;
use App\Livewire\Order\OrderList;
use App\Livewire\Order\RepurchaseTemplateList;
use App\Livewire\Product\CategoryList;
use App\Livewire\Product\KeywordList;
use App\Livewire\Product\ProductList;
use App\Livewire\Product\SkuBarcodeList;
use App\Livewire\Product\SkuList;
use App\Livewire\Product\SkuSupplierList;
use App\Livewire\Product\TagList;
use App\Livewire\Purchase\PurchaseItemList;
use App\Livewire\Purchase\PurchaseOrderList;
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

    // ── 商品管理 ──
    Route::get('/categories', CategoryList::class)->name('categories');
    Route::get('/products', ProductList::class)->name('products');
    Route::get('/skus', SkuList::class)->name('skus');
    Route::get('/tags', TagList::class)->name('tags');
    Route::get('/keywords', KeywordList::class)->name('keywords');
    Route::get('/sku-barcodes', SkuBarcodeList::class)->name('sku-barcodes');
    Route::get('/sku-suppliers', SkuSupplierList::class)->name('sku-suppliers');

    // ── 采购管理 ──
    Route::get('/purchase-items', PurchaseItemList::class)->name('purchase-items');
    Route::get('/purchase-orders', PurchaseOrderList::class)->name('purchase-orders');

    // ── 订单配送 ──
    Route::get('/orders', OrderList::class)->name('orders');
    Route::get('/carts', CartList::class)->name('carts');
    Route::get('/frequently-bought', FrequentlyBoughtList::class)->name('frequently-bought');
    Route::get('/repurchase-templates', RepurchaseTemplateList::class)->name('repurchase-templates');

    // ── 系统管理 ──
    Route::get('/settings', Settings::class)->name('settings');
    Route::get('/approval-config', ApprovalConfig::class)->name('approval-config');
    Route::get('/approvals', Approvals::class)->name('approvals');
    Route::get('/operation-logs', OperationLogs::class)->name('operation-logs');
    Route::get('/audit-logs', AuditLogs::class)->name('audit-logs');
});

// 开发演示页（无需登录，生产环境应移除）
Route::view('/demo', 'pages.demo')->name('demo');
