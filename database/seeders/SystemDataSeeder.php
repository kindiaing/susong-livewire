<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

/**
 * 核心数据 Seeder
 *
 * - 确保 9 个系统角色存在
 * - 创建权限树（模块 → 页面 → 按钮），三级格式命名（模块.页面.动作）
 * - 每个页面级权限自动生成对应的 .view 页面访问权限
 * - 为 seeding 账户分配 super_admin 角色 + 全部权限
 *
 * 此 Seeder 由 DatabaseSeeder 自动调用，适用于生产环境。
 */
class SystemDataSeeder extends Seeder
{
    protected array $systemRoles = [
        ['name' => 'super_admin', 'display_name' => '超级管理员', 'description' => '全部功能、系统配置、账号管理'],
        ['name' => 'operator', 'display_name' => '运营专员', 'description' => '商品、订单、商家、供应商管理'],
        ['name' => 'operator_manager', 'display_name' => '运营经理', 'description' => '运营审核、商品/订单/价格策略审核确认'],
        ['name' => 'finance', 'display_name' => '财务专员', 'description' => '应收、结算、发票、审计'],
        ['name' => 'cashier', 'display_name' => '出纳', 'description' => '付款录入、收款录入、资金操作执行'],
        ['name' => 'finance_manager', 'display_name' => '财务经理', 'description' => '财务审核、付款/收款/结算单据复核确认'],
        ['name' => 'picker', 'display_name' => '拣货员', 'description' => '拣货任务、称重改价'],
        ['name' => 'driver', 'display_name' => '配送司机', 'description' => '配送任务、轨迹、签收'],
        ['name' => 'merchant', 'display_name' => '商家', 'description' => '小程序商家端'],
    ];

    /**
     * 权限树定义（三级格式：模块.页面.动作）
     */
    protected array $permissionTree = [
        'dashboard' => [
            'display_name' => '仪表盘',
            'icon' => 'layout-dashboard',
            'pages' => [],
        ],
        'user' => [
            'display_name' => '用户权限',
            'icon' => 'users',
            'pages' => [
                'user.user' => ['display_name' => '用户管理', 'buttons' => ['user.user.create', 'user.user.edit', 'user.user.delete', 'user.user.toggle', 'user.user.reset-password', 'user.user.assign-role']],
                'user.role' => ['display_name' => '角色管理', 'buttons' => ['user.role.create', 'user.role.edit', 'user.role.delete', 'user.role.assign-permission']],
                'user.permission' => ['display_name' => '权限管理', 'buttons' => ['user.permission.create', 'user.permission.edit', 'user.permission.delete', 'user.permission.assign-role']],
            ],
        ],
        'org' => [
            'display_name' => '组织管理',
            'icon' => 'building',
            'pages' => [
                'org.supplier' => ['display_name' => '供应商管理', 'buttons' => ['org.supplier.create', 'org.supplier.edit', 'org.supplier.delete']],
                'org.merchant' => ['display_name' => '商家管理', 'buttons' => ['org.merchant.create', 'org.merchant.edit', 'org.merchant.delete']],
                'org.route' => ['display_name' => '配送路线', 'buttons' => ['org.route.create', 'org.route.edit', 'org.route.delete']],
                'org.driver' => ['display_name' => '司机管理', 'buttons' => ['org.driver.create', 'org.driver.edit', 'org.driver.delete']],
                'org.vehicle' => ['display_name' => '车辆管理', 'buttons' => ['org.vehicle.create', 'org.vehicle.edit', 'org.vehicle.delete']],
            ],
        ],
        'product' => [
            'display_name' => '商品管理',
            'icon' => 'package',
            'pages' => [
                'product.product' => ['display_name' => '商品列表', 'buttons' => ['product.product.create', 'product.product.edit', 'product.product.delete']],
                'product.category' => ['display_name' => '分类管理', 'buttons' => ['product.category.create', 'product.category.edit', 'product.category.delete']],
                'product.tag' => ['display_name' => '标签管理', 'buttons' => ['product.tag.create', 'product.tag.edit', 'product.tag.delete']],
                'product.keyword' => ['display_name' => '关键词管理', 'buttons' => ['product.keyword.create', 'product.keyword.edit', 'product.keyword.delete']],
                'product.visibility' => ['display_name' => '可见性配置', 'buttons' => ['product.visibility.create', 'product.visibility.edit', 'product.visibility.delete']],
            ],
        ],
        'purchase' => [
            'display_name' => '采购管理',
            'icon' => 'truck',
            'pages' => [
                'purchase.purchase-order' => ['display_name' => '采购单', 'buttons' => ['purchase.purchase-order.create', 'purchase.purchase-order.edit', 'purchase.purchase-order.delete', 'purchase.purchase-order.submit', 'purchase.purchase-order.approve']],
                'purchase.purchase-return' => ['display_name' => '采购退货', 'buttons' => ['purchase.purchase-return.create', 'purchase.purchase-return.edit', 'purchase.purchase-return.delete']],
                'purchase.restock-reminder' => ['display_name' => '补货提醒', 'buttons' => ['purchase.restock-reminder.create', 'purchase.restock-reminder.edit', 'purchase.restock-reminder.delete']],
            ],
        ],
        'order' => [
            'display_name' => '订单管理',
            'icon' => 'shopping-cart',
            'pages' => [
                'order.order' => ['display_name' => '订单列表', 'buttons' => ['order.order.create', 'order.order.edit', 'order.order.cancel', 'order.order.lock', 'order.order.change-price', 'order.order.delete']],
                'order.cart' => ['display_name' => '购物车', 'buttons' => []],
                'order.order-return' => ['display_name' => '退货管理', 'buttons' => ['order.order-return.create', 'order.order-return.edit', 'order.order-return.delete']],
            ],
        ],
        'inventory' => [
            'display_name' => '库存管理',
            'icon' => 'warehouse',
            'pages' => [
                'inventory.warehouse' => ['display_name' => '仓库管理', 'buttons' => ['inventory.warehouse.create', 'inventory.warehouse.edit', 'inventory.warehouse.delete']],
                'inventory.inventory' => ['display_name' => '库存列表', 'buttons' => []],
                'inventory.inventory-log' => ['display_name' => '库存日志', 'buttons' => []],
            ],
        ],
        'delivery' => [
            'display_name' => '物流配送',
            'icon' => 'delivery-truck',
            'pages' => [
                'delivery.delivery-task' => ['display_name' => '配送任务', 'buttons' => ['delivery.delivery-task.assign', 'delivery.delivery-task.update']],
                'delivery.signature' => ['display_name' => '签收管理', 'buttons' => []],
                'delivery.discrepancy' => ['display_name' => '差异处理', 'buttons' => ['delivery.discrepancy.restock', 'delivery.discrepancy.refund', 'delivery.discrepancy.writeoff']],
                'delivery.temperature' => ['display_name' => '温度记录', 'buttons' => []],
            ],
        ],
        'loss' => [
            'display_name' => '损耗管理',
            'icon' => 'exclamation-triangle',
            'pages' => [
                'loss.loss-order' => ['display_name' => '损耗单', 'buttons' => ['loss.loss-order.create', 'loss.loss-order.edit', 'loss.loss-order.approve', 'loss.loss-order.execute', 'loss.loss-order.close']],
            ],
        ],
        'finance' => [
            'display_name' => '财务管理',
            'icon' => 'banknote',
            'pages' => [
                'finance.recharge' => ['display_name' => '充值管理', 'buttons' => ['finance.recharge.create', 'finance.recharge.approve']],
                'finance.supplier-settlement' => ['display_name' => '供应商结算', 'buttons' => ['finance.supplier-settlement.create', 'finance.supplier-settlement.pay']],
                'finance.receivable' => ['display_name' => '应收管理', 'buttons' => ['finance.receivable.collect', 'finance.receivable.approve']],
                'finance.invoice' => ['display_name' => '发票管理', 'buttons' => ['finance.invoice.create', 'finance.invoice.issue', 'finance.invoice.send']],
            ],
        ],
        'price' => [
            'display_name' => '价格管理',
            'icon' => 'calculator',
            'pages' => [
                'price.promotion' => ['display_name' => '促销活动', 'buttons' => ['price.promotion.create', 'price.promotion.edit', 'price.promotion.approve', 'price.promotion.toggle']],
                'price.pricing-config' => ['display_name' => '取价配置', 'buttons' => ['price.pricing-config.edit']],
                'price.price-change-log' => ['display_name' => '改价记录', 'buttons' => []],
                'price.price-apportionment' => ['display_name' => '费用均摊', 'buttons' => []],
            ],
        ],
        'system' => [
            'display_name' => '系统管理',
            'icon' => 'settings',
            'pages' => [
                'system.system-config' => ['display_name' => '系统配置', 'buttons' => ['system.system-config.edit']],
                'system.audit-log' => ['display_name' => '审计日志', 'buttons' => []],
                'system.login-log' => ['display_name' => '登录日志', 'buttons' => []],
                'system.banner' => ['display_name' => '轮播管理', 'buttons' => ['system.banner.create', 'system.banner.edit', 'system.banner.delete']],
                'system.wechat-user' => ['display_name' => '微信用户', 'buttons' => []],
            ],
        ],
    ];

    /**
     * 按钮权限显示名称映射
     */
    protected array $buttonDisplayNames = [
        // 用户权限
        'user.user.create' => '创建用户', 'user.user.edit' => '编辑用户', 'user.user.delete' => '删除用户',
        'user.user.toggle' => '启用/禁用', 'user.user.reset-password' => '重置密码', 'user.user.assign-role' => '分配角色',
        'user.role.create' => '创建角色', 'user.role.edit' => '编辑角色', 'user.role.delete' => '删除角色', 'user.role.assign-permission' => '分配权限',
        'user.permission.create' => '创建权限', 'user.permission.edit' => '编辑权限', 'user.permission.delete' => '删除权限', 'user.permission.assign-role' => '分配角色',
        // 组织管理
        'org.supplier.create' => '创建供应商', 'org.supplier.edit' => '编辑供应商', 'org.supplier.delete' => '删除供应商',
        'org.merchant.create' => '创建商家', 'org.merchant.edit' => '编辑商家', 'org.merchant.delete' => '删除商家',
        'org.route.create' => '创建路线', 'org.route.edit' => '编辑路线', 'org.route.delete' => '删除路线',
        'org.driver.create' => '创建司机', 'org.driver.edit' => '编辑司机', 'org.driver.delete' => '删除司机',
        'org.vehicle.create' => '创建车辆', 'org.vehicle.edit' => '编辑车辆', 'org.vehicle.delete' => '删除车辆',
        // 商品管理
        'product.product.create' => '创建商品', 'product.product.edit' => '编辑商品', 'product.product.delete' => '删除商品',
        'product.category.create' => '创建分类', 'product.category.edit' => '编辑分类', 'product.category.delete' => '删除分类',
        'product.tag.create' => '创建标签', 'product.tag.edit' => '编辑标签', 'product.tag.delete' => '删除标签',
        'product.keyword.create' => '创建关键词', 'product.keyword.edit' => '编辑关键词', 'product.keyword.delete' => '删除关键词',
        'product.visibility.create' => '创建可见性配置', 'product.visibility.edit' => '编辑可见性配置', 'product.visibility.delete' => '删除可见性配置',
        // 采购管理
        'purchase.purchase-order.create' => '创建采购单', 'purchase.purchase-order.edit' => '编辑采购单', 'purchase.purchase-order.delete' => '删除采购单',
        'purchase.purchase-order.submit' => '提交审核', 'purchase.purchase-order.approve' => '审核采购单',
        'purchase.purchase-return.create' => '创建退货', 'purchase.purchase-return.edit' => '编辑退货', 'purchase.purchase-return.delete' => '删除退货',
        'purchase.restock-reminder.create' => '创建提醒', 'purchase.restock-reminder.edit' => '编辑提醒', 'purchase.restock-reminder.delete' => '删除提醒',
        // 订单管理
        'order.order.create' => '创建订单', 'order.order.edit' => '编辑订单', 'order.order.cancel' => '取消订单', 'order.order.lock' => '锁定订单', 'order.order.change-price' => '改价', 'order.order.delete' => '删除订单',
        'order.order-return.create' => '创建退货', 'order.order-return.edit' => '编辑退货', 'order.order-return.delete' => '删除退货',
        // 库存管理
        'inventory.warehouse.create' => '创建仓库', 'inventory.warehouse.edit' => '编辑仓库', 'inventory.warehouse.delete' => '删除仓库',
        // 配送管理
        'delivery.delivery-task.assign' => '分配任务', 'delivery.delivery-task.update' => '更新状态',
        'delivery.discrepancy.restock' => '补货', 'delivery.discrepancy.refund' => '退款', 'delivery.discrepancy.writeoff' => '报损',
        // 损耗管理
        'loss.loss-order.create' => '创建损耗单', 'loss.loss-order.edit' => '编辑损耗单', 'loss.loss-order.approve' => '审核损耗单',
        'loss.loss-order.execute' => '执行损耗', 'loss.loss-order.close' => '关闭损耗单',
        // 财务管理
        'finance.recharge.create' => '创建充值', 'finance.recharge.approve' => '审核充值',
        'finance.supplier-settlement.create' => '创建结算', 'finance.supplier-settlement.pay' => '付款',
        'finance.receivable.collect' => '收款', 'finance.receivable.approve' => '审核应收',
        'finance.invoice.create' => '创建发票', 'finance.invoice.issue' => '开具发票', 'finance.invoice.send' => '寄出发票',
        // 价格管理
        'price.promotion.create' => '创建活动', 'price.promotion.edit' => '编辑活动',
        'price.promotion.approve' => '审核活动', 'price.promotion.toggle' => '启用/禁用',
        'price.pricing-config.edit' => '编辑取价配置',
        // 系统管理
        'system.system-config.edit' => '编辑配置',
        'system.banner.create' => '创建轮播', 'system.banner.edit' => '编辑轮播', 'system.banner.delete' => '删除轮播',
    ];

    public function run(): void
    {
        $this->ensureRoles();

        // 先清空旧权限，避免 firstOrCreate 匹配到旧名称
        Permission::query()->delete();

        $this->createPermissionTree();
        $this->assignSeedingSuperAdmin();
        $this->ensureDefaultSupplier();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    protected function ensureRoles(): void
    {
        foreach ($this->systemRoles as $roleDef) {
            Role::firstOrCreate(
                ['name' => $roleDef['name'], 'guard_name' => 'web'],
                ['display_name' => $roleDef['display_name'], 'description' => $roleDef['description']]
            );
        }
    }

    protected function createPermissionTree(): void
    {
        foreach ($this->permissionTree as $moduleName => $moduleDef) {
            // 创建模块级权限
            $module = Permission::create([
                'name' => $moduleName,
                'guard_name' => 'web',
                'display_name' => $moduleDef['display_name'],
                'type' => Permission::TYPE_MODULE,
                'parent_id' => 0,
                'route' => $moduleName === 'dashboard' ? 'dashboard' : null,
                'sort' => array_search($moduleName, array_keys($this->permissionTree)),
                'icon' => $moduleDef['icon'] ?? null,
            ]);

            // 创建页面级 + .view + 按钮级权限
            $pageSort = 0;
            foreach ($moduleDef['pages'] as $pageName => $pageDef) {
                // 创建页面级权限
                $page = Permission::create([
                    'name' => $pageName,
                    'guard_name' => 'web',
                    'display_name' => $pageDef['display_name'],
                    'type' => Permission::TYPE_PAGE,
                    'parent_id' => $module->id,
                    'sort' => $pageSort++,
                ]);

                // 创建 .view 页面访问权限（sort=0，挂在页面下方）
                Permission::create([
                    'name' => $pageName . '.view',
                    'guard_name' => 'web',
                    'display_name' => $pageDef['display_name'] . '（查看）',
                    'type' => Permission::TYPE_BUTTON,
                    'parent_id' => $page->id,
                    'sort' => 0,
                ]);

                // 创建按钮级权限（sort 从1开始）
                $btnSort = 1;
                foreach ($pageDef['buttons'] as $btnName) {
                    Permission::create([
                        'name' => $btnName,
                        'guard_name' => 'web',
                        'display_name' => $this->buttonDisplayNames[$btnName] ?? $btnName,
                        'type' => Permission::TYPE_BUTTON,
                        'parent_id' => $page->id,
                        'sort' => $btnSort++,
                    ]);
                }
            }
        }
    }

    protected function assignSeedingSuperAdmin(): void
    {
        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            $superAdminRole->syncPermissions(Permission::all());
        }

        // 为各角色分配合适的权限
        $rolePermissions = [
            // 运营专员：组织/商品/订单/采购查看/库存查看/配送查看/损耗查看/促销查看/轮播
            'operator' => [
                'dashboard',
                'user.user', 'user.user.view',
                'org.supplier', 'org.supplier.view', 'org.supplier.create', 'org.supplier.edit',
                'org.merchant', 'org.merchant.view', 'org.merchant.create', 'org.merchant.edit',
                'org.route', 'org.route.view', 'org.route.create', 'org.route.edit',
                'org.driver', 'org.driver.view', 'org.driver.create', 'org.driver.edit',
                'org.vehicle', 'org.vehicle.view', 'org.vehicle.create', 'org.vehicle.edit',
                'product.product', 'product.product.view', 'product.product.create', 'product.product.edit',
                'product.category', 'product.category.view', 'product.category.create', 'product.category.edit',
                'product.tag', 'product.tag.view', 'product.tag.create', 'product.tag.edit',
                'product.keyword', 'product.keyword.view', 'product.keyword.create', 'product.keyword.edit',
                'product.visibility', 'product.visibility.view',
                'purchase.purchase-order', 'purchase.purchase-order.view',
                'purchase.purchase-return', 'purchase.purchase-return.view',
                'purchase.restock-reminder', 'purchase.restock-reminder.view',
                'order.order', 'order.order.view', 'order.order.create', 'order.order.edit', 'order.order.cancel', 'order.order.lock', 'order.order.change-price', 'order.order.delete',
                'order.cart',
                'order.order-return', 'order.order-return.view', 'order.order-return.create', 'order.order-return.edit',
                'inventory.warehouse', 'inventory.warehouse.view',
                'inventory.inventory', 'inventory.inventory.view',
                'inventory.inventory-log', 'inventory.inventory-log.view',
                'delivery.delivery-task', 'delivery.delivery-task.view',
                'delivery.signature', 'delivery.signature.view',
                'delivery.discrepancy', 'delivery.discrepancy.view',
                'delivery.temperature', 'delivery.temperature.view',
                'loss.loss-order', 'loss.loss-order.view', 'loss.loss-order.create',
                'price.promotion', 'price.promotion.view', 'price.promotion.create', 'price.promotion.edit',
                'price.price-change-log', 'price.price-change-log.view',
                'system.banner', 'system.banner.view', 'system.banner.create', 'system.banner.edit',
            ],
            // 运营经理：运营专员权限 + 审核权限
            'operator_manager' => [
                'dashboard',
                'user.user', 'user.user.view',
                'org.supplier', 'org.supplier.view', 'org.supplier.create', 'org.supplier.edit',
                'org.merchant', 'org.merchant.view', 'org.merchant.create', 'org.merchant.edit',
                'org.route', 'org.route.view', 'org.route.create', 'org.route.edit',
                'org.driver', 'org.driver.view', 'org.driver.create', 'org.driver.edit',
                'org.vehicle', 'org.vehicle.view', 'org.vehicle.create', 'org.vehicle.edit',
                'product.product', 'product.product.view', 'product.product.create', 'product.product.edit',
                'product.category', 'product.category.view', 'product.category.create', 'product.category.edit',
                'product.tag', 'product.tag.view', 'product.tag.create', 'product.tag.edit',
                'product.keyword', 'product.keyword.view', 'product.keyword.create', 'product.keyword.edit',
                'product.visibility', 'product.visibility.view',
                'purchase.purchase-order', 'purchase.purchase-order.view', 'purchase.purchase-order.approve',
                'purchase.purchase-return', 'purchase.purchase-return.view',
                'purchase.restock-reminder', 'purchase.restock-reminder.view',
                'order.order', 'order.order.view', 'order.order.create', 'order.order.edit', 'order.order.cancel', 'order.order.lock', 'order.order.change-price', 'order.order.delete',
                'order.cart',
                'order.order-return', 'order.order-return.view', 'order.order-return.create', 'order.order-return.edit',
                'inventory.warehouse', 'inventory.warehouse.view',
                'inventory.inventory', 'inventory.inventory.view',
                'inventory.inventory-log', 'inventory.inventory-log.view',
                'delivery.delivery-task', 'delivery.delivery-task.view', 'delivery.delivery-task.assign', 'delivery.delivery-task.update',
                'delivery.signature', 'delivery.signature.view',
                'delivery.discrepancy', 'delivery.discrepancy.view', 'delivery.discrepancy.restock', 'delivery.discrepancy.refund', 'delivery.discrepancy.writeoff',
                'delivery.temperature', 'delivery.temperature.view',
                'loss.loss-order', 'loss.loss-order.view', 'loss.loss-order.create', 'loss.loss-order.approve', 'loss.loss-order.execute', 'loss.loss-order.close',
                'price.promotion', 'price.promotion.view', 'price.promotion.create', 'price.promotion.edit', 'price.promotion.approve',
                'price.pricing-config', 'price.pricing-config.view',
                'price.price-change-log', 'price.price-change-log.view',
                'price.price-apportionment', 'price.price-apportionment.view',
                'system.banner', 'system.banner.view', 'system.banner.create', 'system.banner.edit',
                'system.audit-log', 'system.audit-log.view',
            ],
            // 财务专员：财务/价格记录/审计日志
            'finance' => [
                'dashboard',
                'finance.recharge', 'finance.recharge.view', 'finance.recharge.create',
                'finance.supplier-settlement', 'finance.supplier-settlement.view', 'finance.supplier-settlement.create',
                'finance.receivable', 'finance.receivable.view', 'finance.receivable.collect',
                'finance.invoice', 'finance.invoice.view', 'finance.invoice.create', 'finance.invoice.issue',
                'price.price-change-log', 'price.price-change-log.view',
                'price.price-apportionment', 'price.price-apportionment.view',
                'system.audit-log', 'system.audit-log.view',
            ],
            // 出纳：付款/收款/资金操作执行
            'cashier' => [
                'dashboard',
                'finance.recharge', 'finance.recharge.view', 'finance.recharge.create',
                'finance.supplier-settlement', 'finance.supplier-settlement.view', 'finance.supplier-settlement.pay',
                'finance.receivable', 'finance.receivable.view', 'finance.receivable.collect',
                'finance.invoice', 'finance.invoice.view',
            ],
            // 财务经理：财务全部 + 审核权限
            'finance_manager' => [
                'dashboard',
                'finance.recharge', 'finance.recharge.view', 'finance.recharge.create', 'finance.recharge.approve',
                'finance.supplier-settlement', 'finance.supplier-settlement.view', 'finance.supplier-settlement.create', 'finance.supplier-settlement.pay',
                'finance.receivable', 'finance.receivable.view', 'finance.receivable.collect', 'finance.receivable.approve',
                'finance.invoice', 'finance.invoice.view', 'finance.invoice.create', 'finance.invoice.issue', 'finance.invoice.send',
                'price.price-change-log', 'price.price-change-log.view',
                'price.price-apportionment', 'price.price-apportionment.view',
                'system.audit-log', 'system.audit-log.view',
                'system.login-log', 'system.login-log.view',
            ],
            // 拣货员：库存/采购查看/损耗查看
            'picker' => [
                'dashboard',
                'inventory.warehouse', 'inventory.warehouse.view',
                'inventory.inventory', 'inventory.inventory.view',
                'inventory.inventory-log', 'inventory.inventory-log.view',
                'purchase.purchase-order', 'purchase.purchase-order.view',
                'loss.loss-order', 'loss.loss-order.view',
            ],
            // 配送司机：配送任务/签收/温度/订单查看
            'driver' => [
                'dashboard',
                'delivery.delivery-task', 'delivery.delivery-task.view', 'delivery.delivery-task.update',
                'delivery.signature', 'delivery.signature.view',
                'delivery.discrepancy', 'delivery.discrepancy.view',
                'delivery.temperature', 'delivery.temperature.view',
                'order.order', 'order.order.view',
            ],
            // 商家：订单/购物车/签收查看/充值查看
            'merchant' => [
                'dashboard',
                'order.order', 'order.order.view',
                'order.cart',
                'order.order-return', 'order.order-return.view',
                'delivery.signature', 'delivery.signature.view',
                'finance.recharge', 'finance.recharge.view',
                'finance.invoice', 'finance.invoice.view',
            ],
        ];

        foreach ($rolePermissions as $roleName => $permissionNames) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $permissions = Permission::whereIn('name', $permissionNames)->get();
                $role->syncPermissions($permissions);
            }
        }

        $seeding = \App\Models\User::where('username', 'seeding')->first();
        if ($seeding) {
            $seeding->assignRole('super_admin');
            $seeding->syncPermissions(Permission::all());
        }
    }

    /**
     * 创建默认供应商（核心数据，确保采购流程可用）
     */
    protected function ensureDefaultSupplier(): void
    {
        Supplier::firstOrCreate(
            ['name' => '默认供应商'],
            [
                'contact_name' => '-',
                'contact_phone' => '-',
                'settlement_cycle' => Supplier::CYCLE_MONTHLY,
                'status' => Supplier::STATUS_ENABLED,
            ]
        );
    }
}
