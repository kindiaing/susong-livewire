<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Exceptions\UnauthorizedException;

/**
 * 路由权限中间件
 *
 * 根据路由名称自动匹配权限：
 * - 路由名 → 权限名映射（config 权限路由映射表）
 * - super_admin 角色自动放行
 * - 无匹配权限名的路由默认放行（待后续逐步收敛）
 */
class CheckPermission
{
    /**
     * 路由名 → 权限名映射表
     *
     * 格式：'路由名' => '权限名'
     * null 表示该路由仅需登录，不做权限校验
     */
    private const ROUTE_PERMISSION_MAP = [
        // 基础
        'dashboard'           => null, // 仅需登录
        'profile'             => null, // 仅需登录

        // ── 用户权限 ──
        'users'               => 'user.user.view',
        'roles'               => 'user.role.view',
        'permissions'          => 'user.permission.view',

        // ── 组织管理 ──
        'suppliers'           => 'org.supplier.view',
        'merchants'           => 'org.merchant.view',
        'delivery-routes'     => 'org.route.view',
        'drivers'             => 'org.driver.view',
        'vehicles'            => 'org.vehicle.view',

        // ── 商品管理 ──
        'categories'          => 'product.category.view',
        'products'            => 'product.product.view',
        'skus'                => 'product.product.view',
        'tags'                => 'product.tag.view',
        'keywords'            => 'product.keyword.view',
        'sku-barcodes'        => 'product.product.view',
        'sku-suppliers'       => 'product.product.view',
        'merchant-sku-visibility' => 'product.visibility.view',
        'restock-reminders'   => 'purchase.restock-reminder.view',

        // ── 采购管理 ──
        'purchase-items'      => 'purchase.purchase-order.view',
        'purchase-orders'     => 'purchase.purchase-order.view',
        'purchase-orders.detail' => 'purchase.purchase-order.view',
        'purchase-returns'    => 'purchase.purchase-return.view',

        // ── 订单管理 ──
        'orders'              => 'order.order.view',
        'carts'               => 'order.cart.view',
        'frequently-bought'   => 'order.order.view',
        'repurchase-templates' => 'order.order.view',
        'order-returns'       => 'order.order-return.view',

        // ── 库存管理 ──
        'warehouses'          => 'inventory.warehouse.view',
        'inventories'         => 'inventory.inventory.view',
        'inventory-logs'      => 'inventory.inventory-log.view',
        'picking-tasks'       => 'inventory.warehouse.view',

        // ── 配送管理 ──
        'delivery-tasks'      => 'delivery.delivery-task.view',
        'signatures'          => 'delivery.signature.view',
        'temperatures'        => 'delivery.temperature.view',
        'discrepancies'       => 'delivery.discrepancy.view',

        // ── 损耗管理 ──
        'loss-orders'         => 'loss.loss-order.view',

        // ── 财务管理 ──
        'merchant-accounts'   => 'finance.recharge.view',
        'recharges'           => 'finance.recharge.view',
        'supplier-settlements' => 'finance.supplier-settlement.view',
        'receivables'         => 'finance.receivable.view',
        'invoices'            => 'finance.invoice.view',
        'correction-authorizations' => 'finance.recharge.view',
        'price-strategies'    => 'price.price-strategy.view',
        'price-apportionments' => 'price.price-apportionment.view',
        'price-change-logs'   => 'price.price-change-log.view',
        'promotion-activities' => 'price.promotion.view',
        'pricing-config'      => 'price.pricing-config.view',

        // ── 商家扩展 ──
        'merchant-addresses'  => 'org.merchant.view',
        'merchant-favorites'  => 'org.merchant.view',

        // ── 系统管理 ──
        'finance-settings'    => 'system.system-config.view',
        'settings'            => 'system.system-config.view',
        'banners'             => 'system.banner.view',
        'approval-config'     => 'system.system-config.view',
        'approvals'           => 'system.system-config.view',
        'operation-logs'      => 'system.audit-log.view',
        'audit-logs'          => 'system.audit-log.view',
        'login-logs'          => 'system.login-log.view',
        'wechat-users'        => 'system.wechat-user.view',
    ];

    /**
     * super_admin 角色专有路由（仅超管可访问）
     */
    // 超管专属路由已废弃，权限由权限表统一控制
    // 如果需要限制某路由仅超管可访问，在权限树中只给 super_admin 分配即可
    private const SUPER_ADMIN_ONLY = [
    ];

    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        $routeName = $request->route()?->getName();

        // 无路由名的请求放行
        if (! $routeName) {
            return $next($request);
        }

        // 超管专属路由检查
        if (in_array($routeName, self::SUPER_ADMIN_ONLY, true)) {
            if (! $user->hasRole('super_admin')) {
                abort(403, '仅超级管理员可访问此页面');
            }

            return $next($request);
        }

        // 查找路由对应的权限名
        $permission = self::ROUTE_PERMISSION_MAP[$routeName] ?? null;

        // 未配置映射的路由：仅需登录即可访问（渐进式收敛）
        if ($permission === null) {
            return $next($request);
        }

        // super_admin 角色自动放行所有权限
        if ($user->hasRole('super_admin')) {
            return $next($request);
        }

        // 检查用户是否拥有对应权限
        if ($user->hasPermissionTo($permission)) {
            return $next($request);
        }

        abort(403, '您没有访问此页面的权限');
    }
}
