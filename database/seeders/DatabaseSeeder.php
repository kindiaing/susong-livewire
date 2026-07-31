<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * 双入口兼容设计：
     *   php artisan db:seed                  → 仅核心数据（生产环境）
     *   php artisan db:seed --class=DemoDataSeeder → 含全部测试数据（开发环境）
     *   php artisan db:seed --class=SystemDataSeeder → 仅核心数据（显式）
     *   php artisan migrate:fresh --seed      → 仅核心数据
     *   php artisan migrate:fresh --seed --seeder=DemoDataSeeder → 含全部测试数据
     *
     * 项目自定义入口（推荐）：
     *   php artisan admin:seed               → 全部（核心+测试）
     *   php artisan admin:seed --system       → 仅核心数据
     *   php artisan admin:seed --demo         → 全部测试数据
     *   php artisan admin:seed --demo=organization → 仅指定模块测试数据
     *   php artisan admin:seed --list         → 列出可用模块
     */
    public function run(): void
    {
        $this->call([
            SystemDataSeeder::class,
        ]);
    }
}
