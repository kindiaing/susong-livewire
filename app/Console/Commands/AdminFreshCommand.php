<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\confirm;

class AdminFreshCommand extends Command
{
    protected $signature = 'admin:fresh
                            {--with-test-data : 重建后导入测试数据}
                            {--force : 强制执行，不确认}';

    protected $description = '清空并重建数据库（危险操作，需确认）';

    public function handle(): int
    {
        $force = $this->option('force');
        $withTestData = $this->option('with-test-data');

        if (! $force) {
            $this->warn('⚠ 此操作将删除所有数据！');

            if (! confirm('确定要清空并重建数据库吗？此操作不可逆！', default: false)) {
                $this->info('已取消。');

                return self::SUCCESS;
            }

            // 二次确认
            if (! confirm('请再次确认：输入 YES 将执行数据库清空重建？', default: false)) {
                $this->info('已取消。');

                return self::SUCCESS;
            }
        }

        $this->info('正在清空并重建数据库...');
        Artisan::call('migrate:fresh', ['--force' => true], $this->output);

        // 重新安装基础数据
        $installArgs = ['--force' => true];
        if ($withTestData) {
            $installArgs['--with-test-data'] = true;
        }

        Artisan::call('admin:install', $installArgs, $this->output);

        $this->info('数据库重建完成！');

        return self::SUCCESS;
    }
}
