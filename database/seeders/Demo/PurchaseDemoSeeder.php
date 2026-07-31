<?php

namespace Database\Seeders\Demo;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * 采购管理测试数据 Seeder
 *
 * 包含：采购单（含明细）、待采清单、采购退货（含明细）
 */
class PurchaseDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedPurchaseOrders();
        $this->seedPurchaseItems();
        $this->seedPurchaseReturns();
    }

    protected function seedPurchaseOrders(): void
    {
        $now = now();
        $supplierGreen = DB::table('suppliers')->where('name', '绿野蔬菜种植基地')->first();
        $supplierMeat = DB::table('suppliers')->where('name', '丰润肉业有限公司')->first();

        // 采购单1：绿野蔬菜 - 已完成
        $po1Id = null;
        if ($supplierGreen && ! DB::table('purchase_orders')->where('order_no', 'PO-20260725-001')->exists()) {
            $po1Id = DB::table('purchase_orders')->insertGetId([
                'order_no' => 'PO-20260725-001',
                'supplier_id' => $supplierGreen->id,
                'status' => 5,
                'total_amount' => 450000,
                'actual_amount' => 440000,
                'remark' => '日常蔬菜采购',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $product1 = DB::table('products')->where('name', '大白菜')->first();
            $product2 = DB::table('products')->where('name', '土豆')->first();
            $product3 = DB::table('products')->where('name', '西红柿')->first();

            if ($product1) {
                $sku1 = DB::table('skus')->where('product_id', $product1->id)->first();
                if ($sku1) DB::table('purchase_order_items')->insert([
                    'purchase_order_id' => $po1Id, 'sku_id' => $sku1->id, 'quantity' => 50000, 'price' => 8000, 'actual_quantity' => 50000, 'actual_price' => 8000, 'amount' => 400000, 'actual_amount' => 400000, 'strategy_price' => 0, 'strategy_amount' => 0, 'created_at' => $now, 'updated_at' => $now,
                ]);
            }
            if ($product2) {
                $sku2 = DB::table('skus')->where('product_id', $product2->id)->first();
                if ($sku2) DB::table('purchase_order_items')->insert([
                    'purchase_order_id' => $po1Id, 'sku_id' => $sku2->id, 'quantity' => 3000, 'price' => 12000, 'actual_quantity' => 2800, 'actual_price' => 12000, 'amount' => 36000, 'actual_amount' => 33600, 'strategy_price' => 0, 'strategy_amount' => 0, 'discrepancy_reason' => '运输损耗5%', 'created_at' => $now, 'updated_at' => $now,
                ]);
            }
            if ($product3) {
                $sku3 = DB::table('skus')->where('product_id', $product3->id)->first();
                if ($sku3) DB::table('purchase_order_items')->insert([
                    'purchase_order_id' => $po1Id, 'sku_id' => $sku3->id, 'quantity' => 2000, 'price' => 25000, 'actual_quantity' => 2000, 'actual_price' => 25000, 'amount' => 50000, 'actual_amount' => 50000, 'strategy_price' => 0, 'strategy_amount' => 0, 'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }

        // 采购单2：丰润肉业 - 备货中
        if ($supplierMeat && ! DB::table('purchase_orders')->where('order_no', 'PO-20260728-002')->exists()) {
            $po2Id = DB::table('purchase_orders')->insertGetId([
                'order_no' => 'PO-20260728-002',
                'supplier_id' => $supplierMeat->id,
                'status' => 2,
                'total_amount' => 260000,
                'actual_amount' => 0,
                'remark' => '肉类补货',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $product4 = DB::table('products')->where('name', '五花肉')->first();
            if ($product4) {
                $sku4 = DB::table('skus')->where('product_id', $product4->id)->first();
                if ($sku4) DB::table('purchase_order_items')->insert([
                    'purchase_order_id' => $po2Id, 'sku_id' => $sku4->id, 'quantity' => 2000, 'price' => 130000, 'actual_quantity' => 0, 'actual_price' => 0, 'amount' => 260000, 'actual_amount' => 0, 'strategy_price' => 0, 'strategy_amount' => 0, 'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }
    }

    protected function seedPurchaseItems(): void
    {
        $now = now();
        $product = DB::table('products')->where('name', '鲜虾')->first();
        if (! $product) return;
        $sku = DB::table('skus')->where('product_id', $product->id)->first();
        if (! $sku) return;

        if (! DB::table('purchase_items')->where('sku_id', $sku->id)->exists()) {
            DB::table('purchase_items')->insert([
                'sku_id' => $sku->id,
                'quantity' => 1000,
                'source_type' => 1,
                'source_id' => null,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    protected function seedPurchaseReturns(): void
    {
        $now = now();
        $po1 = DB::table('purchase_orders')->where('order_no', 'PO-20260725-001')->first();
        $supplierGreen = DB::table('suppliers')->where('name', '绿野蔬菜种植基地')->first();
        $warehouse1 = DB::table('warehouses')->where('name', '总仓-农批市场')->first();

        if ($po1 && $supplierGreen && $warehouse1 && ! DB::table('purchase_returns')->where('return_no', 'PR-20260728-001')->exists()) {
            $returnId = DB::table('purchase_returns')->insertGetId([
                'return_no' => 'PR-20260728-001',
                'purchase_order_id' => $po1->id,
                'supplier_id' => $supplierGreen->id,
                'warehouse_id' => $warehouse1->id,
                'status' => 2,
                'total_amount' => 33600,
                'actual_amount' => 0,
                'reason' => '土豆运输损耗退货',
                'operator_id' => DB::table('users')->where('username', 'operator1')->value('id'),
                'audited_by' => DB::table('users')->where('username', 'ops_manager')->value('id'),
                'audited_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $poItem = DB::table('purchase_order_items')->where('purchase_order_id', $po1->id)->skip(1)->first();
            if ($poItem) {
                DB::table('purchase_return_items')->insert([
                    'purchase_return_id' => $returnId,
                    'purchase_order_item_id' => $poItem->id,
                    'sku_id' => $poItem->sku_id,
                    'quantity' => 200,
                    'price' => 12000,
                    'amount' => 2400000,
                    'actual_quantity' => 0,
                    'actual_price' => 0,
                    'actual_amount' => 0,
                    'reason' => '运输损耗5%',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
