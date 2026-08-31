<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\confirm;

class AdminInstallCommand extends Command
{
    protected $signature = 'admin:install
                            {--seed : 同时导入测试数据}
                            {--force : 强制执行，不确认}';

    protected $description = '安装/初始化数据库（迁移 + 种子 + 权限 + 管理员）';

    public function handle(): int
    {
        $this->info('╔══════════════════════════════════════╗');
        $this->info('║    速送服务平台 - 数据库安装向导     ║');
        $this->info('╚══════════════════════════════════════╝');
        $this->newLine();

        $seed = $this->option('seed');
        $force = $this->option('force');

        // 确认
        if (! $force) {
            $mode = $seed ? '安装 + 测试数据' : '仅安装（不含测试数据）';

            $this->warn('⚠ 此操作将清空并重建数据库！');

            if (! confirm("即将执行 [{$mode}]，是否继续？", default: false)) {
                $this->info('已取消。');
                return self::SUCCESS;
            }
        }

        // 1. 迁移
        // 本地开发：migrate:fresh 清空并重建（admin:install 默认行为）
        // Cloud 环境：migrate:fresh 被静默拦截（prohibited），不抛异常但也不建表，
        //              因此必须检测表是否真正创建，失败则自动降级为 migrate（增量迁移）
        $this->newLine();
        $this->info('[1/3] 执行数据库迁移...');

        Artisan::call('migrate:fresh', ['--force' => true], $this->output);

        // migrate:fresh 在某些托管环境（Laravel Cloud 等）会被静默跳过，
        // 不抛异常也不建表，需要检测关键表是否真实存在
        if (! Schema::hasTable('roles')) {
            $this->warn('  ⚠ migrate:fresh 未生效（当前环境可能禁止此命令）');
            $this->info('  → 自动降级为 migrate（增量迁移，不清空已有数据）');
            Artisan::call('migrate', ['--force' => true], $this->output);

            if (! Schema::hasTable('roles')) {
                $this->error('  ✗ 迁移失败：roles 表仍不存在，终止安装');
                return self::FAILURE;
            }
            $this->info('  ✓ migrate 完成（增量迁移）');
        } else {
            $this->info('  ✓ migrate:fresh 完成（已清空并重建）');
        }

        $this->info('  ✓ 迁移完成（角色、审核配置、系统配置已由 Migration 自动初始化）');

        // 清理 Spatie 权限缓存（migrate:fresh 重建表后旧缓存会干扰 Seeder）
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. 运行 Seeder（系统内置数据：角色权限 + 系统配置）
        $this->newLine();
        $this->info('[2/3] 初始化系统内置数据...');
        Artisan::call('db:seed', ['--class' => 'SystemDataSeeder', '--force' => true], $this->output);
        $this->info('  ✓ 系统内置数据初始化完成');

        // 3. 测试数据
        if ($seed) {
            $this->newLine();
            $this->info('[3/3] 导入测试数据...');
            Artisan::call('db:seed', ['--class' => 'DemoDataSeeder', '--force' => true], $this->output);
            $this->info('  ✓ 测试数据导入完成');
        } else {
            $this->newLine();
            $this->info('[3/3] 跳过测试数据（使用 --seed 可导入）');
        }

        $this->newLine();
        $this->info('╔══════════════════════════════════════╗');
        $this->info('║         安装完成！                    ║');
        $this->info('╚══════════════════════════════════════╝');
        $this->newLine();

        $this->line('  超管账号已自动创建：');
        $this->line('    用户名: <info>admin</info>');
        $this->line('    邮箱:   <info>admin@ihopeso.cn</info>');
        $this->line('    密码:   <info>Password</info>');
        $this->newLine();
        $this->line('  如需创建个人超管账号，请执行: <info>php artisan admin:create-admin --force</info>');
        $this->newLine();

        if ($seed) {
            $this->line('  测试用户密码均为: <info>Password</info>');
            $this->newLine();
        }

        $this->warn('  ⚠ 生产环境部署前，请修改默认超管密码！');

        return self::SUCCESS;
    }
}
