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
        'dashboard'           => 'dashboard',
        'profile'             => null, // 仅需登录

        // ── 用户权限 ──
        'users'               => 'user.manage',
        'roles'               => 'role.manage',
        'permissions'          => 'permission.manage',

        // ── 组织管理 ──
        'suppliers'           => 'supplier.index',
        'merchants'           => 'merchant.index',
        'delivery-routes'     => 'route.index',
        'drivers'             => 'driver.index',
        'vehicles'            => 'vehicle.index',

        // ── 商品管理 ──
        'categories'          => 'category.index',
        'products'            => 'product.index',
        'skus'                => 'product.index',
        'tags'                => 'tag.index',
        'keywords'            => 'keyword.index',
        'sku-barcodes'        => 'product.index',
        'sku-suppliers'       => 'product.index',
        'restock-reminders'   => 'restock-reminder.index',

        // ── 采购管理 ──
        'purchase-items'      => 'purchase-order.index',
        'purchase-orders'     => 'purchase-order.index',
        'purchase-returns'    => 'purchase-return.index',

        // ── 订单管理 ──
        'orders'              => 'order.index',
        'carts'               => 'cart.index',
        'frequently-bought'   => 'order.index',
        'repurchase-templates' => 'order.index',
        'order-returns'       => 'order-return.index',

        // ── 库存管理 ──
        'warehouses'          => 'warehouse.index',
        'inventories'         => 'inventory.index',
        'inventory-logs'      => 'inventory-log.index',
        'picking-tasks'       => 'inventory.index',

        // ── 配送管理 ──
        'delivery-tasks'      => 'delivery-task.index',
        'signatures'          => 'signature.index',
        'temperatures'        => 'temperature.index',
        'discrepancies'       => 'discrepancy.index',

        // ── 损耗管理 ──
        'loss-orders'         => 'loss-order.index',

        // ── 财务管理 ──
        'merchant-accounts'   => 'recharge.index',
        'recharges'           => 'recharge.index',
        'supplier-settlements' => 'supplier-settlement.index',
        'receivables'         => 'receivable.index',
        'invoices'            => 'invoice.index',
        'correction-authorizations' => 'recharge.index',
        'price-strategies'    => 'price-strategy.index',
        'price-apportionments' => 'price-apportionment.index',
        'price-change-logs'   => 'price-change-log.index',

        // ── 商家扩展 ──
        'merchant-addresses'  => 'merchant.index',
        'merchant-favorites'  => 'merchant.index',

        // ── 系统管理 ──
        'settings'            => 'system-config.index',
        'banners'             => 'banner.index',
        'promotions'          => 'system-config.index',
        'approval-config'     => 'system-config.index',
        'approvals'           => 'system-config.index',
        'operation-logs'      => 'audit-log.index',
        'audit-logs'          => 'audit-log.index',
        'login-logs'          => 'login-log.index',
        'wechat-users'        => 'wechat-user.index',
    ];

    /**
     * super_admin 角色专有路由（仅超管可访问）
     */
    private const SUPER_ADMIN_ONLY = [
        'roles',
        'permissions',
        'approval-config',
        'settings',
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
