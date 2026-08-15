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
        $this->seedNotifications();
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
            if ($promo['target_id'] && ! DB::table('featured_promotions')->where('type', $promo['type'])->where('target_id', $promo['target_id'])->exists()) {
                DB::table('featured_promotions')->insert(array_merge($promo, ['created_at' => $now, 'updated_at' => $now]));
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

    protected function seedNotifications(): void
    {
        $now = now();
        $merchant1 = DB::table('merchants')->where('name', '味之初餐饮店')->first();
        $operator = DB::table('users')->where('username', 'operator1')->first();
        $finance = DB::table('users')->where('username', 'finance1')->first();
        $merchantUser = DB::table('users')->where('username', 'merchant1')->first();

        $notifications = [
            // 系统通知
            ['user_id' => null, 'merchant_id' => null, 'type' => 1, 'title' => '系统维护通知', 'content' => '系统将于今晚 22:00-23:00 进行例行维护，届时服务将短暂中断', 'data' => null, 'is_read' => 0, 'read_at' => null, 'created_at' => $now->copy()->subHours(2), 'updated_at' => $now->copy()->subHours(2)],

            // 订单状态变更（商家维度）
            ['user_id' => null, 'merchant_id' => $merchant1?->id, 'type' => 2, 'title' => '订单状态变更', 'content' => '订单 ORD-20260815-00001 状态从「待拣货」变更为「拣货中」', 'data' => json_encode(['order_no' => 'ORD-20260815-00001', 'from_status' => '待拣货', 'to_status' => '拣货中']), 'is_read' => 0, 'read_at' => null, 'created_at' => $now->copy()->subHour(), 'updated_at' => $now->copy()->subHour()],

            // 补货提醒
            ['user_id' => $operator?->id, 'merchant_id' => null, 'type' => 3, 'title' => '补货提醒', 'content' => '大白菜 当前库存 800，已低于预警阈值 1000', 'data' => json_encode(['sku_name' => '大白菜', 'current_stock' => 800, 'threshold' => 1000]), 'is_read' => 0, 'read_at' => null, 'created_at' => $now->copy()->subMinutes(30), 'updated_at' => $now->copy()->subMinutes(30)],

            // 库存预警
            ['user_id' => $operator?->id, 'merchant_id' => null, 'type' => 4, 'title' => '库存预警', 'content' => '五花肉 当前库存 50，已低于预警阈值 200', 'data' => json_encode(['sku_name' => '五花肉', 'current_stock' => 50, 'threshold' => 200]), 'is_read' => 1, 'read_at' => $now->copy()->subMinutes(10), 'created_at' => $now->copy()->subHours(5), 'updated_at' => $now->copy()->subMinutes(10)],

            // 账户变动 — 充值到账
            ['user_id' => null, 'merchant_id' => $merchant1?->id, 'type' => 5, 'title' => '充值到账', 'content' => '充值单 RC2026081500001 已审核通过，到账金额 ¥5,000.00', 'data' => json_encode(['recharge_no' => 'RC2026081500001', 'amount' => '5,000.00']), 'is_read' => 0, 'read_at' => null, 'created_at' => $now->copy()->subMinutes(15), 'updated_at' => $now->copy()->subMinutes(15)],

            // 账户变动 — 结算完成
            ['user_id' => $finance?->id, 'merchant_id' => null, 'type' => 5, 'title' => '结算完成', 'content' => '供应商结算单 SS2026081500001 已结清，金额 ¥12,800.00', 'data' => json_encode(['settlement_no' => 'SS2026081500001', 'amount' => '12,800.00']), 'is_read' => 0, 'read_at' => null, 'created_at' => $now->copy()->subMinutes(8), 'updated_at' => $now->copy()->subMinutes(8)],

            // 账户变动 — 收款确认
            ['user_id' => $finance?->id, 'merchant_id' => null, 'type' => 5, 'title' => '收款确认', 'content' => '应收单 RC2026081500002 已收款，金额 ¥3,200.00', 'data' => json_encode(['receivable_no' => 'RC2026081500002', 'amount' => '3,200.00']), 'is_read' => 1, 'read_at' => $now->copy()->subMinutes(3), 'created_at' => $now->copy()->subMinutes(20), 'updated_at' => $now->copy()->subMinutes(3)],

            // 系统通知 — 采购单待审核
            ['user_id' => $operator?->id, 'merchant_id' => null, 'type' => 1, 'title' => '采购单待审核', 'content' => '采购单 PO-20260815-00001 已提交，请及时审核', 'data' => json_encode(['purchase_no' => 'PO-20260815-00001']), 'is_read' => 0, 'read_at' => null, 'created_at' => $now->copy()->subMinutes(5), 'updated_at' => $now->copy()->subMinutes(5)],

            // 商家维度 — 订单签收通知
            ['user_id' => $merchantUser?->id, 'merchant_id' => null, 'type' => 2, 'title' => '订单已签收', 'content' => '订单 ORD-20260814-00005 已完成签收', 'data' => json_encode(['order_no' => 'ORD-20260814-00005']), 'is_read' => 0, 'read_at' => null, 'created_at' => $now->copy()->subMinutes(45), 'updated_at' => $now->copy()->subMinutes(45)],

            // 库存预警 — 另一个
            ['user_id' => $operator?->id, 'merchant_id' => null, 'type' => 4, 'title' => '库存预警', 'content' => '鲜虾 当前库存 30，已低于预警阈值 100', 'data' => json_encode(['sku_name' => '鲜虾', 'current_stock' => 30, 'threshold' => 100]), 'is_read' => 0, 'read_at' => null, 'created_at' => $now->copy()->subMinutes(2), 'updated_at' => $now->copy()->subMinutes(2)],
        ];

        foreach ($notifications as $n) {
            DB::table('notifications')->insert($n);
        }
    }
}
