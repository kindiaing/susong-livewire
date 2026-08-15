<?php

namespace App\Console\Commands;

use Database\Seeders\DemoDataSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

use function Laravel\Prompts\confirm;

class AdminSeedCommand extends Command
{
    protected $signature = 'admin:seed
                            {--fresh : 先清空再导入（migrate:fresh + seed）}
                            {--system : 仅导入核心数据（SystemDataSeeder）}
                            {--demo= : 导入测试数据（不传值=全部模块，传值=指定模块，如 --demo=organization）}
                            {--list : 列出所有可用的 Seeder 模块}
                            {--force : 强制执行，不确认}';

    protected $description = '导入种子数据（默认导入全部：核心数据 + 测试数据）';

    public function handle(): int
    {
        // --list 模式：列出可用模块后退出
        if ($this->option('list')) {
            return $this->listModules();
        }

        $fresh = $this->option('fresh');
        $system = $this->option('system');
        $demo = $this->option('demo');
        $force = $this->option('force');

        // 确定 demo 模式：null=未传, false(字符串)=无值, 字符串=指定模块
        $demoSpecified = $demo !== null;   // --demo 选项是否被指定
        $demoModule = is_string($demo) && $demo !== '' ? $demo : null;  // 指定的模块名

        // 验证模块名
        if ($demoModule && ! isset(DemoDataSeeder::$modules[$demoModule])) {
            $this->error("模块 [{$demoModule}] 不存在！");
            $this->line('可用模块：' . implode(', ', array_keys(DemoDataSeeder::$modules)));
            $this->line('使用 <info>php artisan admin:seed --list</info> 查看详情');
            return self::FAILURE;
        }

        // 确定模式标签
        $modeLabel = match (true) {
            $system && ! $demoSpecified => '仅核心数据（SystemDataSeeder）',
            $demoSpecified && ! $system => $demoModule
                ? "仅测试数据 [{$demoModule}]（" . DemoDataSeeder::$modules[$demoModule]['label'] . '）'
                : '全部测试数据（DemoDataSeeder 10 个模块）',
            default => '全部（核心数据 + 测试数据）',
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

        // 2. 核心数据（SystemDataSeeder）
        if (! $demoSpecified) {
            $this->newLine();
            $this->info('导入核心数据（角色/权限/seeding 角色分配）...');
            Artisan::call('db:seed', ['--class' => 'SystemDataSeeder', '--force' => true], $this->output);
            $this->info('  ✓ 核心数据导入完成');
        }

        // 3. 测试数据
        if (! $system) {
            $this->newLine();

            if ($demoModule) {
                // 指定模块
                $this->info("导入测试数据 [{$demoModule}]（" . DemoDataSeeder::$modules[$demoModule]['label'] . '）...');
                $seeder = new DemoDataSeeder;
                $seeder->setContainer(app());
                $seeder->setCommand($this);
                $seeder->runModule($demoModule);
            } else {
                // 全部测试数据
                $this->info('导入全部测试数据（10 个模块）...');
                Artisan::call('db:seed', ['--class' => 'DemoDataSeeder', '--force' => true], $this->output);
            }

            $this->info('  ✓ 测试数据导入完成');
        }

        $this->newLine();
        $this->info('╔══════════════════════════════════════╗');
        $this->info('║         导入完成！                    ║');
        $this->info('╚══════════════════════════════════════╝');

        if (! $demoSpecified) {
            $this->line('  超级管理员: <info>seeding</info>  密码: <info>Password</info>');
        }
        if (! $system) {
            $this->line('  测试用户密码均为: <info>Password</info>');
        }
        $this->newLine();

        return self::SUCCESS;
    }

    /**
     * 列出所有可用的 Seeder 模块
     */
    protected function listModules(): int
    {
        $this->info('╔══════════════════════════════════════╗');
        $this->info('║      可用 Seeder 模块列表            ║');
        $this->info('╚══════════════════════════════════════╝');
        $this->newLine();

        $this->line('  <info>核心数据</info>');
        $this->line('    system       SystemDataSeeder（9角色+140权限+首个用户角色分配）');
        $this->newLine();

        $this->line('  <info>测试数据（10 个模块）</info>');
        foreach (DemoDataSeeder::$modules as $key => $module) {
            $this->line("    <comment>" . str_pad($key, 14) . "</comment>  {$module['label']}");
        }

        $this->newLine();
        $this->line('  <info>用法示例</info>');
        $this->line('    php artisan admin:seed --demo                    # 全部测试数据');
        $this->line('    php artisan admin:seed --demo=organization       # 仅组织主体');
        $this->line('    php artisan admin:seed --system                   # 仅核心数据');
        $this->line('    php artisan admin:seed                           # 全部（核心+测试）');
        $this->newLine();

        return self::SUCCESS;
    }
}
