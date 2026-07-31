<?php

namespace Database\Seeders\Demo;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * 系统支撑测试数据 Seeder
 *
 * 包含：轮播广告、运营主推、补货提醒、登录日志、微信用户
 */
class SystemDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedBanners();
        $this->seedPromotions();
        $this->seedRestockReminders();
        $this->seedLoginLogs();
        $this->seedWechatUsers();
    }

    protected function seedBanners(): void
    {
        $now = now();
        $banners = [
            ['title' => '新鲜蔬菜每日直达', 'image_url' => '/uploads/banners/veg-fresh.jpg', 'link_url' => '/products?category=1', 'sort' => 1, 'status' => 1],
            ['title' => '海鲜专区限时特惠', 'image_url' => '/uploads/banners/seafood-sale.jpg', 'link_url' => '/products?category=4', 'sort' => 2, 'status' => 1],
            ['title' => '新用户充值满赠', 'image_url' => '/uploads/banners/recharge-gift.jpg', 'link_url' => '/recharges', 'sort' => 3, 'status' => 1],
        ];

        foreach ($banners as $banner) {
            if (! DB::table('banners')->where('title', $banner['title'])->exists()) {
                DB::table('banners')->insert(array_merge($banner, ['created_at' => $now, 'updated_at' => $now]));
            }
        }
    }

    protected function seedPromotions(): void
    {
        $now = now();
        $product1 = DB::table('products')->where('name', '大白菜')->first();
        $vegCategory = DB::table('categories')->where('name', '蔬菜')->first();

        $promotions = [
            ['type' => 1, 'target_id' => $product1?->id ?? 0, 'sort' => 1, 'start_at' => $now, 'end_at' => now()->addMonths(1), 'status' => 1],
            ['type' => 2, 'target_id' => $vegCategory?->id ?? 0, 'sort' => 2, 'start_at' => $now, 'end_at' => now()->addMonths(1), 'status' => 1],
        ];

        foreach ($promotions as $promo) {
            if ($promo['target_id'] && ! DB::table('promotions')->where('type', $promo['type'])->where('target_id', $promo['target_id'])->exists()) {
                DB::table('promotions')->insert(array_merge($promo, ['created_at' => $now, 'updated_at' => $now]));
            }
        }
    }

    protected function seedRestockReminders(): void
    {
        $now = now();
        $merchant1 = DB::table('merchants')->where('name', '味之初餐饮店')->first();
        if (! $merchant1) return;

        $reminders = [
            ['product' => '大白菜', 'threshold_quantity' => 1000, 'remind_cycle' => 1],
            ['product' => '五花肉', 'threshold_quantity' => 200, 'remind_cycle' => 2],
            ['product' => '鲜虾',   'threshold_quantity' => 100, 'remind_cycle' => 1],
        ];

        foreach ($reminders as $item) {
            $product = DB::table('products')->where('name', $item['product'])->first();
            if (! $product) continue;
            $sku = DB::table('skus')->where('product_id', $product->id)->first();
            if (! $sku) continue;

            if (! DB::table('restock_reminders')->where('merchant_id', $merchant1->id)->where('sku_id', $sku->id)->exists()) {
                DB::table('restock_reminders')->insert([
                    'merchant_id' => $merchant1->id, 'sku_id' => $sku->id,
                    'threshold_quantity' => $item['threshold_quantity'],
                    'remind_cycle' => $item['remind_cycle'],
                    'last_reminded_at' => null, 'status' => 1,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }
    }

    protected function seedLoginLogs(): void
    {
        $now = now();
        $users = DB::table('users')->limit(5)->get();

        $logs = [];
        foreach ($users as $user) {
            $logs[] = [
                'user_id' => $user->id, 'username' => $user->username,
                'ip' => '127.0.0.1', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/126.0',
                'login_type' => 1, 'status' => 1, 'fail_reason' => null, 'created_at' => $now,
            ];
        }
        $logs[] = [
            'user_id' => null, 'username' => 'unknown',
            'ip' => '192.168.1.100', 'user_agent' => 'Mozilla/5.0',
            'login_type' => 1, 'status' => 0, 'fail_reason' => '用户不存在', 'created_at' => $now,
        ];

        DB::table('login_logs')->insert($logs);
    }

    protected function seedWechatUsers(): void
    {
        $now = now();
        $merchantUser = DB::table('users')->where('username', 'merchant1')->first();
        $driverUser = DB::table('users')->where('username', 'driver1')->first();

        $wechatUsers = [
            ['user_id' => $merchantUser?->id, 'openid' => 'o_demo_merchant_001', 'unionid' => null, 'nickname' => '吴商家', 'avatar' => null, 'type' => 1, 'status' => 1],
            ['user_id' => $driverUser?->id, 'openid' => 'o_demo_driver_001', 'unionid' => null, 'nickname' => '周司机', 'avatar' => null, 'type' => 2, 'status' => 1],
        ];

        foreach ($wechatUsers as $item) {
            if (DB::table('wechat_users')->where('openid', $item['openid'])->exists()) continue;
            DB::table('wechat_users')->insert(array_merge($item, ['created_at' => $now, 'updated_at' => $now]));
        }
    }
}
