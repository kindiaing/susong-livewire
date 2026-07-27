<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;

class AdminInstallCommand extends Command
{
    protected $signature = 'admin:install
                            {--with-test-data : 同时导入测试数据}
                            {--force : 强制执行，不确认}
                            {--fresh : 先清空数据库（migrate:fresh）}';

    protected $description = '安装/初始化数据库（迁移 + 种子 + 权限 + 管理员）';

    public function handle(): int
    {
        $this->info('╔══════════════════════════════════════╗');
        $this->info('║    速送服务平台 - 数据库安装向导     ║');
        $this->info('╚══════════════════════════════════════╝');
        $this->newLine();

        $withTestData = $this->option('with-test-data');
        $force = $this->option('force');
        $fresh = $this->option('fresh');

        // 确认
        if (! $force) {
            $mode = $withTestData ? '安装 + 测试数据' : '仅安装（不含测试数据）';
            if (! confirm("即将执行 [{$mode}]，是否继续？", default: false)) {
                $this->info('已取消。');

                return self::SUCCESS;
            }
        }

        // 1. 迁移
        $this->newLine();
        $this->info('[1/6] 执行数据库迁移...');

        if ($fresh) {
            $this->warn('  ⚠ 使用 migrate:fresh，将清空所有数据！');
            Artisan::call('migrate:fresh', [], $this->output);
        } else {
            Artisan::call('migrate', ['--force' => true], $this->output);
        }

        $this->info('  ✓ 迁移完成');

        // 2. 基础种子（角色 + 权限）
        $this->newLine();
        $this->info('[2/6] 初始化角色与权限...');

        if (class_exists(\Spatie\Permission\Models\Role::class)) {
            $this->seedRoles();
            $this->info('  ✓ 角色与权限初始化完成');
        } else {
            $this->warn('  ⚠ Spatie Permission 未就绪，跳过');
        }

        // 3. 审核配置种子
        $this->newLine();
        $this->info('[3/6] 初始化审核节点配置...');
        $this->seedApprovalConfig();
        $this->info('  ✓ 审核节点配置完成');

        // 4. 系统配置种子
        $this->newLine();
        $this->info('[4/6] 初始化系统配置...');
        $this->seedSystemConfig();
        $this->info('  ✓ 系统配置完成');

        // 5. 创建默认管理员
        $this->newLine();
        $this->info('[5/6] 创建默认管理员账户...');
        $this->createDefaultAdmin();
        $this->info('  ✓ 默认管理员创建完成');

        // 6. 测试数据
        if ($withTestData) {
            $this->newLine();
            $this->info('[6/6] 导入测试数据...');
            $this->seedTestData();
            $this->info('  ✓ 测试数据导入完成');
        } else {
            $this->newLine();
            $this->info('[6/6] 跳过测试数据（使用 --with-test-data 可导入）');
        }

        $this->newLine();
        $this->info('╔══════════════════════════════════════╗');
        $this->info('║         安装完成！                    ║');
        $this->info('╚══════════════════════════════════════╝');
        $this->line('  管理员邮箱: <info>admin@susong.com</info>');
        $this->line('  默认密码:   <info>admin123</info>');
        $this->newLine();
        $this->warn('  ⚠ 请立即修改默认密码！');

        return self::SUCCESS;
    }

    protected function seedRoles(): void
    {
        $roles = [
            'super-admin' => '超级管理员',
            'admin' => '运营管理员',
            'finance' => '财务人员',
            'picker' => '拣货员',
            'driver' => '配送司机',
            'merchant' => '商家',
            'cashier' => '出纳',
            'finance-manager' => '财务经理',
            'ops-manager' => '运营经理',
        ];

        foreach ($roles as $name => $description) {
            \Spatie\Permission\Models\Role::firstOrCreate(
                ['name' => $name],
                ['guard_name' => 'web']
            );
        }
    }

    protected function seedApprovalConfig(): void
    {
        // 19个审核节点种子，默认开启前10+第19条损耗审核
        if (! Schema::hasTable('approval_type_configs')) {
            $this->warn('    approval_type_configs 表不存在，跳过');

            return;
        }

        $configs = [
            ['type_code' => 'purchase_order_create', 'name' => '采购单创建', 'enabled' => true],
            ['type_code' => 'purchase_order_confirm', 'name' => '采购单确认', 'enabled' => true],
            ['type_code' => 'purchase_return', 'name' => '采购退货', 'enabled' => true],
            ['type_code' => 'order_create', 'name' => '订单创建', 'enabled' => true],
            ['type_code' => 'order_lock', 'name' => '订单锁定', 'enabled' => true],
            ['type_code' => 'order_weighing', 'name' => '称重改价', 'enabled' => true],
            ['type_code' => 'order_return', 'name' => '售后退货', 'enabled' => true],
            ['type_code' => 'recharge_apply', 'name' => '充值申请', 'enabled' => true],
            ['type_code' => 'recharge_audit', 'name' => '充值审核', 'enabled' => true],
            ['type_code' => 'settlement_payment', 'name' => '结算付款', 'enabled' => true],
            ['type_code' => 'inventory_adjust', 'name' => '库存调整', 'enabled' => false],
            ['type_code' => 'loss_order', 'name' => '损耗单', 'enabled' => false],
            ['type_code' => 'picking_assign', 'name' => '拣货分配', 'enabled' => false],
            ['type_code' => 'delivery_assign', 'name' => '配送分配', 'enabled' => false],
            ['type_code' => 'discrepancy_handle', 'name' => '差异处理', 'enabled' => false],
            ['type_code' => 'receivable_payment', 'name' => '应收收款', 'enabled' => false],
            ['type_code' => 'price_strategy', 'name' => '价格策略', 'enabled' => false],
            ['type_code' => 'invoice_create', 'name' => '发票开具', 'enabled' => false],
            ['type_code' => 'loss_audit', 'name' => '损耗审核', 'enabled' => true],
        ];

        foreach ($configs as $config) {
            DB::table('approval_type_configs')->updateOrInsert(
                ['type_code' => $config['type_code']],
                array_merge($config, [
                    'applicant_role' => 'admin',
                    'auditor_role' => 'super-admin',
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }

    protected function seedSystemConfig(): void
    {
        if (! Schema::hasTable('system_configs')) {
            $this->warn('    system_configs 表不存在，跳过');

            return;
        }

        $configs = [
            ['key' => 'app.name', 'value' => '速送服务平台', 'description' => '系统名称'],
            ['key' => 'order.auto_lock_minutes', 'value' => '30', 'description' => '订单自动锁定时间（分钟）'],
            ['key' => 'inventory.warning_threshold', 'value' => '10', 'description' => '库存预警阈值'],
            ['key' => 'delivery.max_orders_per_task', 'value' => '20', 'description' => '单次配送最大订单数'],
            ['key' => 'loss.auto_execute_hours', 'value' => '24', 'description' => '损耗单自动执行时间（小时）'],
        ];

        foreach ($configs as $config) {
            DB::table('system_configs')->updateOrInsert(
                ['key' => $config['key']],
                array_merge($config, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }

    protected function createDefaultAdmin(): void
    {
        if (User::where('email', 'admin@susong.com')->exists()) {
            return;
        }

        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@susong.com',
            'password' => bcrypt('admin123'),
            'email_verified_at' => now(),
        ]);

        if (class_exists(\Spatie\Permission\Models\Role::class)) {
            $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super-admin']);
            $user->assignRole($role);
        }
    }

    protected function seedTestData(): void
    {
        // 运行 DatabaseSeeder
        Artisan::call('db:seed', ['--force' => true], $this->output);

        // 创建测试用户
        $testUsers = [
            ['name' => '运营经理', 'email' => 'ops@susong.com', 'role' => 'ops-manager'],
            ['name' => '财务经理', 'email' => 'finance@susong.com', 'role' => 'finance-manager'],
            ['name' => '出纳', 'email' => 'cashier@susong.com', 'role' => 'cashier'],
            ['name' => '拣货员', 'email' => 'picker@susong.com', 'role' => 'picker'],
            ['name' => '司机', 'email' => 'driver@susong.com', 'role' => 'driver'],
        ];

        foreach ($testUsers as $data) {
            if (User::where('email', $data['email'])->exists()) {
                continue;
            }
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);

            if (class_exists(\Spatie\Permission\Models\Role::class)) {
                $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => $data['role']]);
                $user->assignRole($role);
            }
        }

        $this->line('    测试账户密码均为: <info>password</info>');
    }
}
