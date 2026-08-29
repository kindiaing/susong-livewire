<?php

namespace Database\Seeders\Demo;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * 测试用户 Seeder
 *
 * 为运营/财务/拣货/配送/商家等角色创建测试用户。
 * 超管和管理员账号请使用 admin:create-admin 命令创建。
 * 依赖 SystemDataSeeder 已运行（角色和权限已就位） */
class TestUsersDemoSeeder extends Seeder
{
    public function run(): void
    {
        $testUsers = [
            ['role' => 'operator', 'username' => 'operator1', 'name' => '张运营', 'phone' => '13800000001', 'email' => 'operator@susong.test'],
            ['role' => 'operator_manager', 'username' => 'ops_manager', 'name' => '李运营经理', 'phone' => '13800000002', 'email' => 'ops_manager@susong.test'],
            ['role' => 'finance', 'username' => 'finance1', 'name' => '王财务', 'phone' => '13800000003', 'email' => 'finance@susong.test'],
            ['role' => 'cashier', 'username' => 'cashier1', 'name' => '赵出纳', 'phone' => '13800000004', 'email' => 'cashier@susong.test'],
            ['role' => 'finance_manager', 'username' => 'fin_manager', 'name' => '钱财务经理', 'phone' => '13800000005', 'email' => 'finance_manager@susong.test'],
            ['role' => 'picker', 'username' => 'picker1', 'name' => '孙拣货员', 'phone' => '13800000006', 'email' => 'picker@susong.test'],
            ['role' => 'driver', 'username' => 'driver1', 'name' => '周司机', 'phone' => '13800000007', 'email' => 'driver@susong.test'],
            ['role' => 'merchant', 'username' => 'merchant1', 'name' => '吴商家', 'phone' => '13800000008', 'email' => 'merchant@susong.test'],
        ];

        foreach ($testUsers as $item) {
            $user = User::firstOrCreate(
                ['username' => $item['username']],
                [
                    'name' => $item['name'],
                    'phone' => $item['phone'],
                    'email' => $item['email'],
                    'password' => bcrypt('Password'),
                    'status' => 1,
                ]
            );
            $user->assignRole($item['role']);
        }
    }
}
