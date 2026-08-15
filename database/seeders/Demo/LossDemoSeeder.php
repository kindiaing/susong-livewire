<?php

namespace Database\Seeders\Demo;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * 损耗管理测试数据 Seeder
 *
 * 包含：损耗单（含明细）
 */
class LossDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedLossOrders();
    }

    protected function seedLossOrders(): void
    {
        $now = now();
        $warehouse1 = DB::table('warehouses')->where('name', '总仓-农批市场')->first();
        $operatorUser = DB::table('users')->where('username', 'operator1')->first();

        if ($warehouse1 && ! DB::table('loss_orders')->where('loss_no', 'LO-20260728-00001')->exists()) {
            $lossId = DB::table('loss_orders')->insertGetId([
                'loss_no' => 'LO-20260728-00001', 'warehouse_id' => $warehouse1->id,
                'total_amount' => 6400, 'loss_type' => 2, 'status' => 3, 'approval_status' => 2,
                'applicant_id' => $operatorUser?->id, 'reviewer_id' => $operatorUser?->id,
                'reviewed_at' => $now, 'executed_at' => $now,
                'reason' => '蔬菜称重失水损耗', 'created_at' => $now, 'updated_at' => $now,
            ]);

            $product = DB::table('products')->where('name', '大白菜')->first();
            if ($product) {
                $sku = DB::table('skus')->where('product_id', $product->id)->first();
                if ($sku) DB::table('loss_order_items')->insert([
                    'loss_order_id' => $lossId, 'sku_id' => $sku->id,
                    'loss_type' => 2, 'quantity' => 800, 'cost_price' => 8000,
                    'loss_amount' => 6400, 'responsible_party' => 1, 'reason' => '失水减重',
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }
    }
}
