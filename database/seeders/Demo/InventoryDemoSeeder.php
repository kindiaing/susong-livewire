<?php

namespace Database\Seeders\Demo;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * 库存管理测试数据 Seeder
 *
 * 包含：仓库、库存数据、库存变动日志
 */
class InventoryDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedWarehouses();
        $this->seedInventories();
        $this->seedInventoryLogs();
    }

    protected function seedWarehouses(): void
    {
        $now = now();
        $warehouses = [
            ['name' => '总仓-农批市场', 'type' => 1, 'is_cold_chain' => 0, 'address' => '安徽省宿州市埇桥区农批市场内', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '分仓-肉联厂', 'type' => 2, 'is_cold_chain' => 1, 'address' => '安徽省宿州市埇桥区肉联厂内', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
        ];

        foreach ($warehouses as $warehouse) {
            if (! DB::table('warehouses')->where('name', $warehouse['name'])->exists()) {
                DB::table('warehouses')->insert($warehouse);
            }
        }
    }

    protected function seedInventories(): void
    {
        $now = now();
        $warehouse1 = DB::table('warehouses')->where('name', '总仓-农批市场')->first();
        $warehouse2 = DB::table('warehouses')->where('name', '分仓-肉联厂')->first();
        if (! $warehouse1 || ! $warehouse2) return;

        $stockData = [
            ['大白菜',   $warehouse1->id, 50000,  5000, 'B20260701001'],
            ['土豆',     $warehouse1->id, 30000,  3000, 'B20260701002'],
            ['西红柿',   $warehouse1->id, 20000,  2000, 'B20260701003'],
            ['五花肉',   $warehouse2->id, 15000,  2000, 'B20260701004'],
            ['鲜虾',     $warehouse2->id, 8000,   1000, 'B20260701005'],
            ['金龙鱼大豆油', $warehouse1->id, 100, 10, 'B20260701006'],
        ];

        foreach ($stockData as [$productName, $warehouseId, $totalStock, $warningValue, $batchNo]) {
            $product = DB::table('products')->where('name', $productName)->first();
            if (! $product) continue;
            $sku = DB::table('skus')->where('product_id', $product->id)->first();
            if (! $sku) continue;

            if (! DB::table('inventory')->where('warehouse_id', $warehouseId)->where('sku_id', $sku->id)->where('batch_no', $batchNo)->exists()) {
                DB::table('inventory')->insert([
                    'warehouse_id' => $warehouseId, 'sku_id' => $sku->id,
                    'total_stock' => $totalStock, 'locked_stock' => 0, 'available_stock' => $totalStock,
                    'batch_no' => $batchNo, 'warning_value' => $warningValue,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }
    }

    protected function seedInventoryLogs(): void
    {
        $now = now();
        $warehouse1 = DB::table('warehouses')->where('name', '总仓-农批市场')->first();
        if (! $warehouse1) return;

        $product = DB::table('products')->where('name', '大白菜')->first();
        if (! $product) return;
        $sku = DB::table('skus')->where('product_id', $product->id)->first();
        if (! $sku) return;

        $logs = [
            ['warehouse_id' => $warehouse1->id, 'sku_id' => $sku->id, 'type' => 1, 'quantity' => 50000, 'before_stock' => 0, 'after_stock' => 50000, 'reason' => '采购入库', 'source_type' => 'purchase_order', 'source_id' => 1, 'created_at' => $now],
            ['warehouse_id' => $warehouse1->id, 'sku_id' => $sku->id, 'type' => 2, 'quantity' => -5000, 'before_stock' => 50000, 'after_stock' => 45000, 'reason' => '订单出库', 'source_type' => 'order', 'source_id' => 1, 'created_at' => $now],
        ];

        foreach ($logs as $log) {
            DB::table('inventory_logs')->insert($log);
        }
    }
}
