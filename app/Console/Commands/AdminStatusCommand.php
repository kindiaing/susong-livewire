<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminStatusCommand extends Command
{
    protected $signature = 'admin:status';

    protected $description = '检查系统状态（数据库/Redis/Reverb/队列）';

    public function handle(): int
    {
        $this->info('╔══════════════════════════════════════╗');
        $this->info('║      速送服务平台 - 系统状态检查      ║');
        $this->info('╚══════════════════════════════════════╝');
        $this->newLine();

        // 1. 数据库
        $this->checkDatabase();

        // 2. Redis
        $this->checkRedis();

        // 3. Reverb
        $this->checkReverb();

        // 4. 队列
        $this->checkQueue();

        // 5. 用户统计
        $this->checkUsers();

        $this->newLine();
        $this->info('状态检查完成。');

        return self::SUCCESS;
    }

    protected function checkDatabase(): void
    {
        $this->line('  <info>[数据库]</info>');

        try {
            $connection = DB::connection();
            $connection->getPdo();
            $database = config('database.connections.'.config('database.default').'.database');
            $this->line("    连接:    <info>正常</info>");
            $this->line("    数据库:  <info>{$database}</info>");

            if (Schema::hasTable('users')) {
                $count = DB::table('users')->count();
                $this->line("    用户数:  <info>{$count}</info>");
            } else {
                $this->line('    用户表:  <comment>未创建（请执行 admin:install）</comment>');
            }

            if (Schema::hasTable('migrations')) {
                $migrations = DB::table('migrations')->count();
                $this->line("    迁移数:  <info>{$migrations}</info>");
            }
        } catch (\Throwable $e) {
            $this->line('    连接:    <error>失败</error>');
            $this->line('    错误:    <error>'.$e->getMessage().'</error>');
        }

        $this->newLine();
    }

    protected function checkRedis(): void
    {
        $this->line('  <info>[Redis]</info>');

        try {
            $redis = app('redis');
            $redis->ping();
            $this->line('    连接:    <info>正常</info>');
            $this->line('    驱动:    <info>'.config('database.redis.client', 'phpredis').'</info>');
        } catch (\Throwable $e) {
            $this->line('    连接:    <error>失败</error>');
            $this->line('    错误:    <error>'.$e->getMessage().'</error>');
        }

        $this->newLine();
    }

    protected function checkReverb(): void
    {
        $this->line('  <info>[Reverb WebSocket]</info>');

        $host = config('reverb.servers.reverb.host', '0.0.0.0');
        $port = config('reverb.servers.reverb.port', 8080);
        $this->line("    地址:    <info>{$host}:{$port}</info>");
        $this->line("    广播驱动: <info>".config('broadcasting.default', '未配置').'</info>');

        // 尝试连接
        $connection = @fsockopen($host === '0.0.0.0' ? '127.0.0.1' : $host, $port, $errno, $errstr, 2);
        if ($connection) {
            fclose($connection);
            $this->line('    状态:    <info>运行中</info>');
        } else {
            $this->line('    状态:    <comment>未启动（php artisan reverb:start）</comment>');
        }

        $this->newLine();
    }

    protected function checkQueue(): void
    {
        $this->line('  <info>[队列]</info>');

        $connection = config('queue.default', 'sync');
        $this->line("    驱动:    <info>{$connection}</info>");

        if ($connection === 'redis') {
            try {
                $size = app('redis')->llen('queues:default');
                $this->line("    待处理:  <info>{$size}</info>");
            } catch (\Throwable $e) {
                $this->line('    待处理:  <comment>无法获取</comment>');
            }
        }

        $this->newLine();
    }

    protected function checkUsers(): void
    {
        $this->line('  <info>[用户统计]</info>');

        if (! Schema::hasTable('users')) {
            $this->line('    用户表未创建');

            return;
        }

        $total = DB::table('users')->count();
        $this->line("    总用户:  <info>{$total}</info>");

        if (class_exists(\Spatie\Permission\Models\Role::class)) {
            $roles = \Spatie\Permission\Models\Role::withCount('users')->get();
            foreach ($roles as $role) {
                if ($role->users_count > 0) {
                    $this->line("    {$role->name}: <info>{$role->users_count}</info>");
                }
            }
        }

        $this->newLine();
    }
}
