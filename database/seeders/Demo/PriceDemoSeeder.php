<?php

namespace Database\Seeders\Demo;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * 价格策略测试数据 Seeder
 *
 * 包含：价格策略（含明细）、改价记录、费用均摊
 */
class PriceDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedPriceStrategies();
        $this->seedPriceChangeLogs();
        $this->seedPriceApportionments();
    }

    protected function seedPriceStrategies(): void
    {
        $now = now();
        $merchant1 = DB::table('merchants')->where('name', '味之初餐饮店')->first();

        if (! DB::table('price_strategies')->where('code', 'PS-VIP-001')->exists()) {
            $strategyId = DB::table('price_strategies')->insertGetId([
                'name' => '老客户蔬菜优惠', 'code' => 'PS-VIP-001',
                'type' => 1, 'target_type' => 2, 'scope' => 2,
                'status' => 1, 'approval_status' => 2,
                'start_at' => $now, 'end_at' => now()->addMonths(3),
                'created_by' => DB::table('users')->where('username', 'operator1')->value('id'),
                'remark' => '老客户蔬菜类9折优惠',
                'created_at' => $now, 'updated_at' => $now,
            ]);

            $vegCategory = DB::table('categories')->where('name', '蔬菜')->first();
            if ($vegCategory) {
                DB::table('price_strategy_items')->insert([
                    'price_strategy_id' => $strategyId, 'target_id' => $merchant1?->id ?? 0,
                    'category_id' => $vegCategory->id, 'product_id' => null, 'sku_id' => null,
                    'price_type' => 2, 'price_value' => 0, 'discount_rate' => 9000,
                    'cost_weight_rate' => 10000, 'min_quantity' => 0, 'status' => 1,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }
    }

    protected function seedPriceChangeLogs(): void
    {
        $now = now();
        $order1 = DB::table('orders')->where('order_no', 'ORD-20260728-00001')->first();
        $operatorUser = DB::table('users')->where('username', 'operator1')->first();

        if ($order1 && ! DB::table('price_change_logs')->where('target_type', 1)->where('target_id', $order1->id)->exists()) {
            DB::table('price_change_logs')->insert([
                'source_type' => 3, 'source_id' => null,
                'target_type' => 1, 'target_id' => $order1->id, 'target_item_id' => null,
                'original_price' => 9200, 'new_price' => 9000, 'quantity' => 2000,
                'amount_diff' => -4000, 'operator_id' => $operatorUser?->id,
                'reason' => '老客户蔬菜优惠折扣', 'created_at' => $now,
            ]);
        }
    }

    protected function seedPriceApportionments(): void
    {
        $now = now();
        $order1 = DB::table('orders')->where('order_no', 'ORD-20260728-00001')->first();

        if ($order1 && ! DB::table('price_apportionments')->where('target_type', 1)->where('target_id', $order1->id)->exists()) {
            DB::table('price_apportionments')->insert([
                'target_type' => 1, 'target_id' => $order1->id, 'target_item_id' => null,
                'apportion_type' => 3, 'amount' => 5000, 'apportion_mode' => 1, 'manual_amount' => 0,
                'operator_id' => DB::table('users')->where('username', 'operator1')->value('id'),
                'approval_status' => 2, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }
}
