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
                            {--reset : 先清空数据库（migrate:fresh）}
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
        $reset = $this->option('reset');

        // 确认
        if (! $force) {
            $mode = $reset
                ? ($seed ? '重置数据库 + 测试数据' : '重置数据库（仅基础数据）')
                : ($seed ? '安装 + 测试数据' : '仅安装（不含测试数据）');

            if ($reset) {
                $this->warn('⚠ --reset 将删除所有数据！');
            }

            if (! confirm("即将执行 [{$mode}]，是否继续？", default: false)) {
                $this->info('已取消。');
                return self::SUCCESS;
            }
        }

        // 1. 迁移
        $this->newLine();
        $this->info('[1/3] 执行数据库迁移...');

        if ($reset) {
            $this->warn('  ⚠ 使用 migrate:fresh，将清空所有数据！');
            Artisan::call('migrate:fresh', ['--force' => true], $this->output);
        } else {
            Artisan::call('migrate', ['--force' => true], $this->output);
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

        $this->warn('  尚未创建管理员账号，请执行以下命令创建：');
        $this->line('    <info>php artisan admin:create-admin</info>          # 交互式创建超管/管理员');
        $this->line('    <info>php artisan admin:create-admin --force</info>  # 快速创建默认超管');
        $this->newLine();

        if ($seed) {
            $this->line('  测试用户密码均为: <info>Password</info>');
            $this->newLine();
        }

        $this->warn('  ⚠ 生产环境部署前，请修改默认管理员密码！');

        return self::SUCCESS;
    }
}
