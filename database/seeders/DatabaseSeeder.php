<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * 使用方式：
     *   php artisan db:seed                  → 仅内置数据（生产环境）
     *   php artisan db:seed --class=DemoDataSeeder → 含测试数据（开发环境）
     *   php artisan migrate:fresh --seed      → 仅内置数据
     *   php artisan migrate:fresh --seed --seeder=DemoDataSeeder → 含测试数据
     */
    public function run(): void
    {
        $this->call([
            SystemDataSeeder::class,
        ]);
    }
}
