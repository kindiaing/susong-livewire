<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

/**
 * 核心数据 Seeder
 *
 * 包含生产环境必需的基础数据：
 * - 确保 9 个系统角色存在（已在 Migration 中初始化，此处确保存在）
 * - 创建 140 条权限树（模块 → 页面 → 按钮）
 * - 为 Migration 创建的 seeding 账户分配 super_admin 角色 + 全部 140 权限
 * - 不再创建 superadmin 测试用户（测试用户由 DemoDataSeeder 负责）
 *
 * 此 Seeder 由 DatabaseSeeder 自动调用，适用于生产环境。
 */
class SystemDataSeeder extends Seeder
{
    /**
     * 9 个系统角色定义
     */
    protected array $systemRoles = [
        ['name' => 'super_admin', 'display_name' => '超级管理员', 'description' => '全部功能、系统配置、账号管理'],
        ['name' => 'operator', 'display_name' => '运营管理员', 'description' => '商品、订单、商家、供应商管理'],
        ['name' => 'operator_manager', 'display_name' => '运营经理', 'description' => '运营审核、商品/订单/价格策略审核确认'],
        ['name' => 'finance', 'display_name' => '财务人员', 'description' => '应收、结算、发票、审计'],
        ['name' => 'cashier', 'display_name' => '出纳', 'description' => '付款录入、收款录入、资金操作执行'],
        ['name' => 'finance_manager', 'display_name' => '财务经理', 'description' => '财务审核、付款/收款/结算单据复核确认'],
        ['name' => 'picker', 'display_name' => '拣货员', 'description' => '拣货任务、称重改价'],
        ['name' => 'driver', 'display_name' => '配送司机', 'description' => '配送任务、轨迹、签收'],
        ['name' => 'merchant', 'display_name' => '商家', 'description' => '小程序商家端'],
    ];

    /**
     * 权限树定义（模块 → 页面 → 按钮）
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
                'user.manage' => ['显示名称' => '用户管理', 'buttons' => ['user.create', 'user.edit', 'user.delete', 'user.toggle', 'user.reset-password', 'user.assign-role']],
                'role.manage' => ['显示名称' => '角色管理', 'buttons' => ['role.create', 'role.edit', 'role.delete', 'role.assign-permission']],
                'permission.manage' => ['显示名称' => '权限管理', 'buttons' => ['permission.create', 'permission.edit', 'permission.delete', 'permission.assign-role']],
            ],
        ],
        'product' => [
            'display_name' => '商品管理',
            'icon' => 'package',
            'pages' => [
                'product.index' => ['显示名称' => '商品列表', 'buttons' => ['product.create', 'product.edit', 'product.delete']],
                'category.index' => ['显示名称' => '分类管理', 'buttons' => ['category.create', 'category.edit', 'category.delete']],
                'tag.index' => ['显示名称' => '标签管理', 'buttons' => ['tag.create', 'tag.edit', 'tag.delete']],
                'keyword.index' => ['显示名称' => '关键词管理', 'buttons' => ['keyword.create', 'keyword.edit', 'keyword.delete']],
            ],
        ],
        'order' => [
            'display_name' => '订单管理',
            'icon' => 'shopping-cart',
            'pages' => [
                'order.index' => ['显示名称' => '订单列表', 'buttons' => ['order.create', 'order.edit', 'order.cancel', 'order.lock', 'order.change-price']],
                'cart.index' => ['显示名称' => '购物车', 'buttons' => []],
                'order-return.index' => ['显示名称' => '退货管理', 'buttons' => ['order-return.create', 'order-return.edit', 'order-return.delete']],
            ],
        ],
        'purchase' => [
            'display_name' => '采购管理',
            'icon' => 'truck',
            'pages' => [
                'purchase-order.index' => ['显示名称' => '采购单', 'buttons' => ['purchase-order.create', 'purchase-order.edit', 'purchase-order.delete', 'purchase-order.submit', 'purchase-order.approve']],
                'purchase-return.index' => ['显示名称' => '采购退货', 'buttons' => ['purchase-return.create', 'purchase-return.edit', 'purchase-return.delete']],
                'restock-reminder.index' => ['显示名称' => '补货提醒', 'buttons' => ['restock-reminder.create', 'restock-reminder.edit', 'restock-reminder.delete']],
            ],
        ],
        'finance' => [
            'display_name' => '财务管理',
            'icon' => 'banknote',
            'pages' => [
                'recharge.index' => ['显示名称' => '充值管理', 'buttons' => ['recharge.create', 'recharge.approve']],
                'supplier-settlement.index' => ['显示名称' => '供应商结算', 'buttons' => ['supplier-settlement.create', 'supplier-settlement.pay']],
                'receivable.index' => ['显示名称' => '应收管理', 'buttons' => ['receivable.collect', 'receivable.approve']],
                'invoice.index' => ['显示名称' => '发票管理', 'buttons' => ['invoice.create', 'invoice.issue', 'invoice.send']],
            ],
        ],
        'inventory' => [
            'display_name' => '库存管理',
            'icon' => 'warehouse',
            'pages' => [
                'warehouse.index' => ['显示名称' => '仓库管理', 'buttons' => ['warehouse.create', 'warehouse.edit', 'warehouse.delete']],
                'inventory.index' => ['显示名称' => '库存列表', 'buttons' => []],
                'inventory-log.index' => ['显示名称' => '库存日志', 'buttons' => []],
            ],
        ],
        'delivery' => [
            'display_name' => '物流配送',
            'icon' => 'delivery-truck',
            'pages' => [
                'delivery-task.index' => ['显示名称' => '配送任务', 'buttons' => ['delivery-task.assign', 'delivery-task.update']],
                'signature.index' => ['显示名称' => '签收管理', 'buttons' => []],
                'discrepancy.index' => ['显示名称' => '差异处理', 'buttons' => ['discrepancy.restock', 'discrepancy.refund', 'discrepancy.writeoff']],
                'temperature.index' => ['显示名称' => '温度记录', 'buttons' => []],
            ],
        ],
        'loss' => [
            'display_name' => '损耗管理',
            'icon' => 'exclamation-triangle',
            'pages' => [
                'loss-order.index' => ['显示名称' => '损耗单', 'buttons' => ['loss-order.create', 'loss-order.edit', 'loss-order.approve', 'loss-order.execute', 'loss-order.close']],
            ],
        ],
        'organization' => [
            'display_name' => '组织管理',
            'icon' => 'building',
            'pages' => [
                'supplier.index' => ['显示名称' => '供应商管理', 'buttons' => ['supplier.create', 'supplier.edit', 'supplier.delete']],
                'merchant.index' => ['显示名称' => '商家管理', 'buttons' => ['merchant.create', 'merchant.edit', 'merchant.delete']],
                'route.index' => ['显示名称' => '配送路线', 'buttons' => ['route.create', 'route.edit', 'route.delete']],
                'driver.index' => ['显示名称' => '司机管理', 'buttons' => ['driver.create', 'driver.edit', 'driver.delete']],
                'vehicle.index' => ['显示名称' => '车辆管理', 'buttons' => ['vehicle.create', 'vehicle.edit', 'vehicle.delete']],
            ],
        ],
        'price' => [
            'display_name' => '价格管理',
            'icon' => 'calculator',
            'pages' => [
                'price-strategy.index' => ['显示名称' => '价格策略', 'buttons' => ['price-strategy.create', 'price-strategy.edit', 'price-strategy.approve', 'price-strategy.toggle']],
                'price-change-log.index' => ['显示名称' => '改价记录', 'buttons' => []],
                'price-apportionment.index' => ['显示名称' => '费用均摊', 'buttons' => []],
            ],
        ],
        'system' => [
            'display_name' => '系统管理',
            'icon' => 'settings',
            'pages' => [
                'system-config.index' => ['显示名称' => '系统配置', 'buttons' => ['system-config.edit']],
                'audit-log.index' => ['显示名称' => '审计日志', 'buttons' => []],
                'login-log.index' => ['显示名称' => '登录日志', 'buttons' => []],
                'banner.index' => ['显示名称' => '轮播管理', 'buttons' => ['banner.create', 'banner.edit', 'banner.delete']],
                'wechat-user.index' => ['显示名称' => '微信用户', 'buttons' => []],
            ],
        ],
    ];

    public function run(): void
    {
        // 1. 确保角色存在
        $this->ensureRoles();

        // 2. 创建权限树
        $this->createPermissionTree();

        // 3. 为 seeding 账户分配 super_admin 角色 + 全部权限
        $this->assignSeedingSuperAdmin();

        // 4. 清除权限缓存
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
            $module = Permission::firstOrCreate(
                ['name' => $moduleName, 'guard_name' => 'web'],
                [
                    'display_name' => $moduleDef['display_name'],
                    'type' => Permission::TYPE_MODULE,
                    'parent_id' => 0,
                    'route' => $moduleName === 'dashboard' ? 'dashboard' : null,
                    'sort' => array_search($moduleName, array_keys($this->permissionTree)),
                    'icon' => $moduleDef['icon'] ?? null,
                ]
            );

            // 创建页面级和按钮级权限
            $pageSort = 0;
            foreach ($moduleDef['pages'] as $pageName => $pageDef) {
                $page = Permission::firstOrCreate(
                    ['name' => $pageName, 'guard_name' => 'web'],
                    [
                        'display_name' => $pageDef['显示名称'],
                        'type' => Permission::TYPE_PAGE,
                        'parent_id' => $module->id,
                        'sort' => $pageSort++,
                    ]
                );

                // 创建按钮级权限
                $btnSort = 0;
                foreach ($pageDef['buttons'] as $btnName) {
                    Permission::firstOrCreate(
                        ['name' => $btnName, 'guard_name' => 'web'],
                        [
                            'display_name' => $this->buttonDisplayName($btnName),
                            'type' => Permission::TYPE_BUTTON,
                            'parent_id' => $page->id,
                            'sort' => $btnSort++,
                        ]
                    );
                }
            }
        }
    }

    protected function buttonDisplayName(string $name): string
    {
        $map = [
            'user.create' => '创建用户', 'user.edit' => '编辑用户', 'user.delete' => '删除用户',
            'user.toggle' => '启用/禁用', 'user.reset-password' => '重置密码', 'user.assign-role' => '分配角色',
            'role.create' => '创建角色', 'role.edit' => '编辑角色', 'role.delete' => '删除角色', 'role.assign-permission' => '分配权限',
            'permission.create' => '创建权限', 'permission.edit' => '编辑权限', 'permission.delete' => '删除权限', 'permission.assign-role' => '分配角色',
            'product.create' => '创建商品', 'product.edit' => '编辑商品', 'product.delete' => '删除商品',
            'category.create' => '创建分类', 'category.edit' => '编辑分类', 'category.delete' => '删除分类',
            'tag.create' => '创建标签', 'tag.edit' => '编辑标签', 'tag.delete' => '删除标签',
            'keyword.create' => '创建关键词', 'keyword.edit' => '编辑关键词', 'keyword.delete' => '删除关键词',
            'order.create' => '创建订单', 'order.edit' => '编辑订单', 'order.cancel' => '取消订单', 'order.lock' => '锁定订单', 'order.change-price' => '改价',
            'order-return.create' => '创建退货', 'order-return.edit' => '编辑退货', 'order-return.delete' => '删除退货',
            'purchase-order.create' => '创建采购单', 'purchase-order.edit' => '编辑采购单', 'purchase-order.delete' => '删除采购单',
            'purchase-order.submit' => '提交审核', 'purchase-order.approve' => '审核采购单',
            'purchase-return.create' => '创建退货', 'purchase-return.edit' => '编辑退货', 'purchase-return.delete' => '删除退货',
            'restock-reminder.create' => '创建提醒', 'restock-reminder.edit' => '编辑提醒', 'restock-reminder.delete' => '删除提醒',
            'recharge.create' => '创建充值', 'recharge.approve' => '审核充值',
            'supplier-settlement.create' => '创建结算', 'supplier-settlement.pay' => '付款',
            'receivable.collect' => '收款', 'receivable.approve' => '审核应收',
            'invoice.create' => '创建发票', 'invoice.issue' => '开具发票', 'invoice.send' => '寄出发票',
            'warehouse.create' => '创建仓库', 'warehouse.edit' => '编辑仓库', 'warehouse.delete' => '删除仓库',
            'delivery-task.assign' => '分配任务', 'delivery-task.update' => '更新状态',
            'discrepancy.restock' => '补货', 'discrepancy.refund' => '退款', 'discrepancy.writeoff' => '报损',
            'loss-order.create' => '创建损耗单', 'loss-order.edit' => '编辑损耗单', 'loss-order.approve' => '审核损耗单',
            'loss-order.execute' => '执行损耗', 'loss-order.close' => '关闭损耗单',
            'supplier.create' => '创建供应商', 'supplier.edit' => '编辑供应商', 'supplier.delete' => '删除供应商',
            'merchant.create' => '创建商家', 'merchant.edit' => '编辑商家', 'merchant.delete' => '删除商家',
            'route.create' => '创建路线', 'route.edit' => '编辑路线', 'route.delete' => '删除路线',
            'driver.create' => '创建司机', 'driver.edit' => '编辑司机', 'driver.delete' => '删除司机',
            'vehicle.create' => '创建车辆', 'vehicle.edit' => '编辑车辆', 'vehicle.delete' => '删除车辆',
            'price-strategy.create' => '创建策略', 'price-strategy.edit' => '编辑策略',
            'price-strategy.approve' => '审核策略', 'price-strategy.toggle' => '启用/禁用',
            'system-config.edit' => '编辑配置',
            'banner.create' => '创建轮播', 'banner.edit' => '编辑轮播', 'banner.delete' => '删除轮播',
        ];
        return $map[$name] ?? $name;
    }

    /**
     * 为 Migration 创建的 seeding 账户分配 super_admin 角色 + 全部 140 权限
     */
    protected function assignSeedingSuperAdmin(): void
    {
        // 确保 super_admin 角色拥有所有权限
        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            $superAdminRole->syncPermissions(Permission::all());
        }

        // 为 seeding 账户分配 super_admin 角色
        $seeding = \App\Models\User::where('username', 'seeding')->first();
        if ($seeding) {
            $seeding->assignRole('super_admin');
            $seeding->syncPermissions(Permission::all());
        }
    }
}
