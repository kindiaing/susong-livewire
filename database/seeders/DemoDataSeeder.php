<?php

namespace Database\Seeders;

use Database\Seeders\Demo\DeliveryDemoSeeder;
use Database\Seeders\Demo\FinanceDemoSeeder;
use Database\Seeders\Demo\InventoryDemoSeeder;
use Database\Seeders\Demo\LossDemoSeeder;
use Database\Seeders\Demo\OrderDemoSeeder;
use Database\Seeders\Demo\OrganizationDemoSeeder;
use Database\Seeders\Demo\PriceDemoSeeder;
use Database\Seeders\Demo\ProductDemoSeeder;
use Database\Seeders\Demo\PurchaseDemoSeeder;
use Database\Seeders\Demo\SystemDemoSeeder;
use Database\Seeders\Demo\TestUsersDemoSeeder;
use Illuminate\Database\Seeder;

/**
 * 测试数据总入口 Seeder
 *
 * 按模块依赖顺序调用 Demo/ 下 10 个分模块 Seeder。
 * 不再硬调用 SystemDataSeeder（核心数据需由调用方自行确保已导入）。
 *
 * 支持两种调用方式：
 * 1. Laravel 原生：php artisan db:seed --class=DemoDataSeeder
 * 2. 项目自定义：php artisan admin:seed --demo
 *    分模块：php artisan admin:seed --demo=organization
 */
class DemoDataSeeder extends Seeder
{
    /**
     * 模块注册表：key = 模块名（命令行参数值），value = Seeder 类名
     *
     * 顺序即为依赖顺序，不可随意调整
     */
    public static array $modules = [
        'test-users'   => ['class' => TestUsersDemoSeeder::class,   'label' => '测试用户（9 个角色各 1 个测试用户）'],
        'organization' => ['class' => OrganizationDemoSeeder::class, 'label' => '组织主体（供应商/商家/司机/车辆/线路）'],
        'product'      => ['class' => ProductDemoSeeder::class,      'label' => '商品管理（分类/商品/SKU/条码/关键词）'],
        'purchase'     => ['class' => PurchaseDemoSeeder::class,     'label' => '采购管理（采购单/待采清单/采购退货）'],
        'order'        => ['class' => OrderDemoSeeder::class,        'label' => '订单管理（购物车/订单/常购/复购/退货）'],
        'inventory'    => ['class' => InventoryDemoSeeder::class,    'label' => '库存管理（仓库/库存/库存日志）'],
        'delivery'     => ['class' => DeliveryDemoSeeder::class,     'label' => '配送管理（拣货/配送/签收/温度/差异）'],
        'finance'      => ['class' => FinanceDemoSeeder::class,      'label' => '财务对账（充值/结算/应收/发票/授权更正）'],
        'price'        => ['class' => PriceDemoSeeder::class,       'label' => '价格策略（策略/改价记录/费用均摊）'],
        'loss'         => ['class' => LossDemoSeeder::class,         'label' => '损耗管理（损耗单/损耗明细）'],
        'system-demo'  => ['class' => SystemDemoSeeder::class,       'label' => '系统支撑（轮播/主推/补货/登录日志/微信用户）'],
    ];

    public function run(): void
    {
        foreach (self::$modules as $key => $module) {
            $this->call($module['class']);
        }
    }

    /**
     * 运行指定模块（仅运行一个或多个分模块 Seeder）
     *
     * @param  string|array  $module  模块名或模块名数组
     */
    public function runModule(string|array $module): void
    {
        $modules = is_array($module) ? $module : [$module];

        foreach ($modules as $key) {
            if (isset(self::$modules[$key])) {
                $this->call(self::$modules[$key]['class']);
            }
        }
    }
}
