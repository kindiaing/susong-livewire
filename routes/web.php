<?php

use App\Livewire\Auth\Login;
use App\Livewire\Delivery\DeliveryRouteDetail;
use App\Livewire\Delivery\DeliveryRouteList;
use App\Livewire\Delivery\DeliveryTaskDetail;
use App\Livewire\Delivery\DeliveryTaskList;
use App\Livewire\Delivery\VehicleIssueList;
use App\Livewire\Delivery\DiscrepancyList;
use App\Livewire\Delivery\SignatureList;
use App\Livewire\Delivery\TemperatureList;
use App\Livewire\Finance\CorrectionAuthorizationList;
use App\Livewire\Finance\InvoiceList;
use App\Livewire\Finance\MerchantAccountList;
use App\Livewire\Finance\PriceApportionmentList;
use App\Livewire\Finance\PromotionActivityList;
use App\Livewire\Finance\ReceivableList;
use App\Livewire\Finance\RechargeList;
use App\Livewire\Finance\SupplierSettlementList;
use App\Livewire\Inventory\InventoryList;
use App\Livewire\Inventory\InventoryLogList;
use App\Livewire\Inventory\WarehouseList;
use App\Livewire\Loss\LossOrderList;
use App\Livewire\Merchant\MerchantAddressList;
use App\Livewire\Merchant\MerchantFavoriteList;
use App\Livewire\Org\DriverList;
use App\Livewire\Org\SupplierList;
use App\Livewire\Org\VehicleList;
use App\Livewire\Order\CartList;
use App\Livewire\Order\FrequentlyBoughtList;
use App\Livewire\Order\OrderDetail;
use App\Livewire\Order\OrderList;
use App\Livewire\Order\OrderReturnList;
use App\Livewire\Order\RepurchaseTemplateList;
use App\Livewire\Picking\PickingTaskList;
use App\Livewire\Price\PriceChangeLogList;
use App\Livewire\Price\PricingConfig;
use App\Livewire\Price\PromotionSettings;
use App\Livewire\Product\CategoryList;
use App\Livewire\Product\KeywordList;
use App\Livewire\Product\ProductList;
use App\Livewire\Product\SkuBarcodeList;
use App\Livewire\Product\SkuList;
use App\Livewire\Product\RestockReminderList;
use App\Livewire\Product\SkuSupplierList;
use App\Livewire\Product\MerchantSkuVisibilityList;
use App\Livewire\Product\TagList;
use App\Livewire\Purchase\PurchaseItemList;
use App\Livewire\Purchase\PurchaseOrderDetail;
use App\Livewire\Purchase\PurchaseOrderList;
use App\Livewire\Purchase\PurchaseReturnDetail;
use App\Livewire\Purchase\PurchaseReturnList;
use App\Livewire\System\ApprovalConfig;
use App\Livewire\System\Approvals;
use App\Livewire\System\AuditLogs;
use App\Livewire\System\AuditSettings;
use App\Livewire\System\BannerList;
use App\Livewire\System\LoginLogList;
use App\Livewire\System\OperationLogs;
use App\Livewire\System\FinanceSettings;
use App\Livewire\System\Settings;
use App\Livewire\System\WechatUserList;
use App\Livewire\User\PermissionList;
use App\Livewire\User\Profile;
use App\Livewire\User\RoleList;
use App\Livewire\User\UserList;
use Illuminate\Support\Facades\Route;

// 首页（公开，无需登录）
Route::view('/', 'welcome')->name('home');

// 认证路由
Route::get('/login', Login::class)->name('login');

// 退出登录（仅需 auth，无需权限校验）
Route::middleware(['auth'])->post('/logout', function () {
    auth()->logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('home');
})->name('logout');

// 需要登录 + 权限校验的路由
Route::middleware(['auth', 'permission'])->group(function () {
    Route::view('/dashboard', 'pages.dashboard')->name('dashboard');

    // 个人中心
    Route::get('/profile', Profile::class)->name('profile');

    // ── 用户权限 ──
    Route::get('/users', UserList::class)->name('users');
    Route::get('/roles', RoleList::class)->name('roles');
    Route::get('/permissions', PermissionList::class)->name('permissions');

    // ── 组织主体 ──
    Route::get('/suppliers', SupplierList::class)->name('suppliers');
    Route::get('/merchants', \App\Livewire\Org\MerchantList::class)->name('merchants');
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
    Route::get('/merchant-sku-visibility', MerchantSkuVisibilityList::class)->name('merchant-sku-visibility');
    Route::get('/restock-reminders', RestockReminderList::class)->name('restock-reminders');

    // ── 采购管理 ──
    Route::get('/purchase-items', PurchaseItemList::class)->name('purchase-items');
    Route::get('/purchase-orders', PurchaseOrderList::class)->name('purchase-orders');
    Route::get('/purchase-orders/{id}', PurchaseOrderDetail::class)->name('purchase-orders.detail');
    Route::get('/purchase-returns', PurchaseReturnList::class)->name('purchase-returns');
    Route::get('/purchase-returns/{id}', PurchaseReturnDetail::class)->name('purchase-returns.detail');

    // ── 订单配送 ──
    Route::get('/orders', OrderList::class)->name('orders');
    Route::get('/orders/{id}', OrderDetail::class)->name('orders.detail');
    Route::get('/carts', CartList::class)->name('carts');
    Route::get('/frequently-bought', FrequentlyBoughtList::class)->name('frequently-bought');
    Route::get('/repurchase-templates', RepurchaseTemplateList::class)->name('repurchase-templates');
    Route::get('/order-returns', OrderReturnList::class)->name('order-returns');

    // ── 库存拣货 ──
    Route::get('/warehouses', WarehouseList::class)->name('warehouses');
    Route::get('/inventories', InventoryList::class)->name('inventories');
    Route::get('/inventory-logs', InventoryLogList::class)->name('inventory-logs');
    Route::get('/picking-tasks', PickingTaskList::class)->name('picking-tasks');

    // ── 配送管理 ──
    Route::get('/delivery-routes', DeliveryRouteList::class)->name('delivery-routes');
    Route::get('/delivery-routes/{id}', DeliveryRouteDetail::class)->name('delivery-routes.detail');
    Route::get('/delivery-tasks', DeliveryTaskList::class)->name('delivery-tasks');
    Route::get('/delivery-tasks/{id}', DeliveryTaskDetail::class)->name('delivery-tasks.detail');
    Route::get('/signatures', SignatureList::class)->name('signatures');
    Route::get('/temperatures', TemperatureList::class)->name('temperatures');
    Route::get('/discrepancies', DiscrepancyList::class)->name('discrepancies');
    Route::get('/vehicle-issues', VehicleIssueList::class)->name('vehicle-issues');

    // ── 损耗管理 ──
    Route::get('/loss-orders', LossOrderList::class)->name('loss-orders');

    // ── 财务对账 ──
    Route::get('/merchant-accounts', MerchantAccountList::class)->name('merchant-accounts');
    Route::get('/recharges', RechargeList::class)->name('recharges');
    Route::get('/supplier-settlements', SupplierSettlementList::class)->name('supplier-settlements');
    Route::get('/receivables', ReceivableList::class)->name('receivables');
    Route::get('/invoices', InvoiceList::class)->name('invoices');
    Route::get('/correction-authorizations', CorrectionAuthorizationList::class)->name('correction-authorizations');
    Route::get('/promotion-activities', PromotionActivityList::class)->name('promotion-activities');
    Route::get('/price-apportionments', PriceApportionmentList::class)->name('price-apportionments');
    Route::get('/promotion-settings', PromotionSettings::class)->name('promotion-settings');
    Route::get('/price-change-logs', PriceChangeLogList::class)->name('price-change-logs');

    // ── 商家扩展 ──
    Route::get('/merchant-addresses', MerchantAddressList::class)->name('merchant-addresses');
    Route::get('/merchant-favorites', MerchantFavoriteList::class)->name('merchant-favorites');

    // ── 系统管理 ──
    Route::get('/finance-settings', FinanceSettings::class)->name('finance-settings');
    Route::get('/settings', Settings::class)->name('settings');
    Route::get('/banners', BannerList::class)->name('banners');
    Route::get('/approval-config', ApprovalConfig::class)->name('approval-config');
    Route::get('/approvals', Approvals::class)->name('approvals');
    Route::get('/operation-logs', OperationLogs::class)->name('operation-logs');
    Route::get('/audit-settings', AuditSettings::class)->name('audit-settings');
    Route::get('/audit-logs', AuditLogs::class)->name('audit-logs');
    Route::get('/login-logs', LoginLogList::class)->name('login-logs');
    Route::get('/wechat-users', WechatUserList::class)->name('wechat-users');
});

// 开发演示页（无需登录，生产环境应移除）
Route::view('/demo', 'pages.demo')->name('demo');
