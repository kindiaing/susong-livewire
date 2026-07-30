<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

use function Laravel\Prompts\confirm;

class AdminSeedCommand extends Command
{
    protected $signature = 'admin:seed
                            {--fresh : 先清空再导入（migrate:fresh + seed）}
                            {--demo : 仅导入测试数据（DemoDataSeeder）}
                            {--system : 仅导入系统内置数据（SystemDataSeeder）}
                            {--force : 强制执行，不确认}';

    protected $description = '导入种子数据（默认导入全部：系统数据 + 测试数据）';

    public function handle(): int
    {
        $fresh = $this->option('fresh');
        $demo = $this->option('demo');
        $system = $this->option('system');
        $force = $this->option('force');

        // 确定模式
        if ($demo && $system) {
            $this->warn('--demo 和 --system 同时指定，等同于默认（导入全部）');
            $demo = false;
            $system = false;
        }

        $modeLabel = match (true) {
            $demo => '仅测试数据（DemoDataSeeder）',
            $system => '仅系统内置数据（SystemDataSeeder）',
            default => '全部（SystemDataSeeder + DemoDataSeeder）',
        };

        $this->info('╔══════════════════════════════════════╗');
        $this->info('║    速送服务平台 - 种子数据导入      ║');
        $this->info('╚══════════════════════════════════════╝');
        $this->newLine();

        if ($fresh) {
            $this->warn('⚠ --fresh 将先清空数据库再导入！');
        }

        if (! $force) {
            if (! confirm("即将导入 [{$modeLabel}]" . ($fresh ? '（含清空）' : '') . '，是否继续？', default: false)) {
                $this->info('已取消。');
                return self::SUCCESS;
            }
        }

        // 1. 清空（可选）
        if ($fresh) {
            $this->newLine();
            $this->info('清空并重建数据库...');
            Artisan::call('migrate:fresh', ['--force' => true], $this->output);
            $this->info('  ✓ 数据库已重建');
        }

        // 2. 系统内置数据
        if (! $demo) {
            $this->newLine();
            $this->info('导入系统内置数据（角色/权限/管理员/系统配置）...');
            Artisan::call('db:seed', ['--class' => 'SystemDataSeeder', '--force' => true], $this->output);
            $this->info('  ✓ 系统内置数据导入完成');
        }

        // 3. 测试数据
        if (! $system) {
            $this->newLine();
            $this->info('导入测试数据（商品/订单/示例业务数据）...');
            Artisan::call('db:seed', ['--class' => 'DemoDataSeeder', '--force' => true], $this->output);
            $this->info('  ✓ 测试数据导入完成');
        }

        $this->newLine();
        $this->info('╔══════════════════════════════════════╗');
        $this->info('║         导入完成！                    ║');
        $this->info('╚══════════════════════════════════════╝');

        if (! $demo) {
            $this->line('  超级管理员: <info>superadmin</info>  密码: <info>Password</info>');
        }
        if (! $system) {
            $this->line('  测试用户密码均为: <info>Password</info>');
        }
        $this->newLine();

        return self::SUCCESS;
    }
}
