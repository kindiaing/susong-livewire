<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 角色已在 Migration 8.1 中初始化，此处仅补充权限
        $now = now();

        // 创建基础菜单权限
        $permissions = [
            ['name' => 'dashboard', 'guard_name' => 'web', 'display_name' => '仪表盘', 'type' => 1, 'parent_id' => 0, 'route' => 'dashboard', 'sort' => 0, 'icon' => 'layout-dashboard', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'product.menu', 'guard_name' => 'web', 'display_name' => '商品管理', 'type' => 1, 'parent_id' => 0, 'route' => null, 'sort' => 1, 'icon' => 'package', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'product.index', 'guard_name' => 'web', 'display_name' => '商品列表', 'type' => 1, 'parent_id' => 0, 'route' => 'products.index', 'sort' => 10, 'icon' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'order.menu', 'guard_name' => 'web', 'display_name' => '订单管理', 'type' => 1, 'parent_id' => 0, 'route' => null, 'sort' => 2, 'icon' => 'shopping-cart', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'purchase.menu', 'guard_name' => 'web', 'display_name' => '采购管理', 'type' => 1, 'parent_id' => 0, 'route' => null, 'sort' => 3, 'icon' => 'truck', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'finance.menu', 'guard_name' => 'web', 'display_name' => '财务管理', 'type' => 1, 'parent_id' => 0, 'route' => null, 'sort' => 4, 'icon' => 'banknote', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'inventory.menu', 'guard_name' => 'web', 'display_name' => '库存管理', 'type' => 1, 'parent_id' => 0, 'route' => null, 'sort' => 5, 'icon' => 'warehouse', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'delivery.menu', 'guard_name' => 'web', 'display_name' => '物流配送', 'type' => 1, 'parent_id' => 0, 'route' => null, 'sort' => 6, 'icon' => 'delivery-truck', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'organization.menu', 'guard_name' => 'web', 'display_name' => '组织管理', 'type' => 1, 'parent_id' => 0, 'route' => null, 'sort' => 7, 'icon' => 'building', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'system.menu', 'guard_name' => 'web', 'display_name' => '系统管理', 'type' => 1, 'parent_id' => 0, 'route' => null, 'sort' => 8, 'icon' => 'settings', 'created_at' => $now, 'updated_at' => $now],
        ];

        foreach ($permissions as $perm) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $perm['name'], 'guard_name' => $perm['guard_name']],
                $perm
            );
        }

        // 超级管理员获得所有权限
        $superAdmin = DB::table('roles')->where('name', 'super_admin')->first();
        if ($superAdmin) {
            $allPermIds = DB::table('permissions')->pluck('id');
            foreach ($allPermIds as $permId) {
                DB::table('role_has_permissions')->updateOrInsert(
                    ['permission_id' => $permId, 'role_id' => $superAdmin->id],
                    ['permission_id' => $permId, 'role_id' => $superAdmin->id]
                );
            }
        }
    }
}
