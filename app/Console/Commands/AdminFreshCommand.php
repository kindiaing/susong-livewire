<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

use function Laravel\Prompts\confirm;

class AdminFreshCommand extends Command
{
    protected $signature = 'admin:fresh
                            {--seed : 重建后导入测试数据}
                            {--force : 强制执行，不确认}';

    protected $description = '清空并重建数据库（危险操作，需确认）';

    public function handle(): int
    {
        $force = $this->option('force');
        $seed = $this->option('seed');

        if (! $force) {
            $this->warn('⚠ 此操作将删除所有数据！');

            if (! confirm('确定要清空并重建数据库吗？此操作不可逆！', default: false)) {
                $this->info('已取消。');
                return self::SUCCESS;
            }
        }

        $this->info('正在清空并重建数据库...');

        $installArgs = ['--reset' => true, '--force' => true];
        if ($seed) {
            $installArgs['--seed'] = true;
        }

        Artisan::call('admin:install', $installArgs, $this->output);

        $this->info('数据库重建完成！');

        return self::SUCCESS;
    }
}
