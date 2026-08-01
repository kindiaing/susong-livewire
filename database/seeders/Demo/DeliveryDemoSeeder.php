<?php

namespace Database\Seeders\Demo;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * 配送管理测试数据 Seeder
 *
 * 包含：配送任务（含配送订单关联+配送轨迹）、签收存证、冷链温度记录、差异单
 */
class DeliveryDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedPickingTasks();
        $this->seedPickingTaskItems();
        $this->seedDeliveryTasks();
        $this->seedSignatures();
        $this->seedTemperatures();
        $this->seedDiscrepancies();
    }

    protected function seedPickingTasks(): void
    {
        $now = now();
        $warehouse1 = DB::table('warehouses')->where('name', '总仓-农批市场')->first();
        $pickerUser = DB::table('users')->where('username', 'picker1')->first();
        $order1 = DB::table('orders')->where('order_no', 'ORD-20260728-001')->first();

        if ($warehouse1 && ! DB::table('picking_tasks')->where('task_no', 'PK-20260728-001')->exists()) {
            DB::table('picking_tasks')->insert([
                'task_no' => 'PK-20260728-001', 'warehouse_id' => $warehouse1->id,
                'picker_id' => $pickerUser?->id, 'batch' => 1, 'status' => 3,
                'started_at' => $now, 'completed_at' => $now, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }

    protected function seedPickingTaskItems(): void
    {
        $now = now();
        $pickingTask = DB::table('picking_tasks')->where('task_no', 'PK-20260728-001')->first();
        $order1 = DB::table('orders')->where('order_no', 'ORD-20260728-001')->first();

        if (! $pickingTask || ! $order1) return;

        $productNames = ['大白菜', '五花肉', '鲜虾'];
        foreach ($productNames as $productName) {
            $product = DB::table('products')->where('name', $productName)->first();
            if (! $product) continue;
            $sku = DB::table('skus')->where('product_id', $product->id)->first();
            if (! $sku) continue;
            $orderItem = DB::table('order_items')->where('order_id', $order1->id)->where('sku_id', $sku->id)->first();

            if (! DB::table('picking_task_items')->where('picking_task_id', $pickingTask->id)->where('sku_id', $sku->id)->exists()) {
                DB::table('picking_task_items')->insert([
                    'picking_task_id' => $pickingTask->id,
                    'order_id' => $order1->id,
                    'order_item_id' => $orderItem?->id,
                    'sku_id' => $sku->id,
                    'required_quantity' => $orderItem?->quantity ?? 1,
                    'picked_quantity' => $orderItem?->quantity ?? 1,
                    'status' => 2,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    protected function seedDeliveryTasks(): void
    {
        $now = now();
        $route1 = DB::table('delivery_routes')->where('name', '城区北线')->first();
        $driver1 = DB::table('drivers')->where('phone', '13700000001')->first();
        $vehicle1 = DB::table('vehicles')->where('plate_number', '皖LT0001')->first();
        $order1 = DB::table('orders')->where('order_no', 'ORD-20260728-001')->first();

        if ($route1 && ! DB::table('delivery_tasks')->where('task_no', 'DT-20260728-001')->exists()) {
            $taskId = DB::table('delivery_tasks')->insertGetId([
                'task_no' => 'DT-20260728-001', 'delivery_route_id' => $route1->id,
                'driver_id' => $driver1?->id, 'vehicle_id' => $vehicle1?->id,
                'batch' => 1, 'status' => 3, 'planned_at' => $now, 'started_at' => $now, 'completed_at' => $now,
                'created_at' => $now, 'updated_at' => $now,
            ]);

            if ($order1 && ! DB::table('delivery_task_orders')->where('delivery_task_id', $taskId)->where('order_id', $order1->id)->exists()) {
                DB::table('delivery_task_orders')->insert([
                    'delivery_task_id' => $taskId, 'order_id' => $order1->id,
                    'delivery_sort' => 1, 'status' => 2, 'created_at' => $now, 'updated_at' => $now,
                ]);
            }

            if ($driver1 && ! DB::table('delivery_tracks')->where('delivery_task_id', $taskId)->exists()) {
                DB::table('delivery_tracks')->insert([
                    ['delivery_task_id' => $taskId, 'driver_id' => $driver1->id, 'latitude' => 33720000, 'longitude' => 116970000, 'location_desc' => '农批市场出发', 'reported_at' => $now, 'created_at' => $now],
                    ['delivery_task_id' => $taskId, 'driver_id' => $driver1->id, 'latitude' => 33721000, 'longitude' => 116975000, 'location_desc' => '人民路中段', 'reported_at' => $now, 'created_at' => $now],
                ]);
            }
        }
    }

    protected function seedSignatures(): void
    {
        $now = now();
        $order1 = DB::table('orders')->where('order_no', 'ORD-20260728-001')->first();
        $task = DB::table('delivery_tasks')->where('task_no', 'DT-20260728-001')->first();

        if ($order1 && $task && ! DB::table('signatures')->where('order_id', $order1->id)->exists()) {
            DB::table('signatures')->insert([
                'order_id' => $order1->id, 'delivery_task_id' => $task->id,
                'type' => 1, 'image_url' => '/uploads/signatures/demo-sign-001.jpg',
                'signer_name' => '吴老板', 'signed_at' => $now, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }

    protected function seedTemperatures(): void
    {
        $now = now();
        $task = DB::table('delivery_tasks')->where('task_no', 'DT-20260728-001')->first();

        if ($task && ! DB::table('temperatures')->where('delivery_task_id', $task->id)->exists()) {
            DB::table('temperatures')->insert([
                ['delivery_task_id' => $task->id, 'temperature' => -180, 'recorded_at' => $now, 'created_at' => $now],
                ['delivery_task_id' => $task->id, 'temperature' => -150, 'recorded_at' => $now, 'created_at' => $now],
            ]);
        }
    }

    protected function seedDiscrepancies(): void
    {
        $now = now();
        $order1 = DB::table('orders')->where('order_no', 'ORD-20260728-001')->first();

        if ($order1 && ! DB::table('discrepancies')->where('order_id', $order1->id)->exists()) {
            $orderItem = DB::table('order_items')->where('order_id', $order1->id)->first();

            DB::table('discrepancies')->insert([
                'discrepancy_no' => 'DIS-20260728-001', 'order_id' => $order1->id,
                'order_item_id' => $orderItem?->id, 'stage' => 2, 'type' => 1,
                'expected_quantity' => 500, 'actual_quantity' => 480,
                'quantity_diff' => -20, 'amount_diff' => -13800,
                'reason' => '运输途中少件', 'responsible_party' => 1,
                'decision' => 2, 'status' => 3, 'handled_at' => $now,
                'is_amount_adjusted' => 1, 'approval_status' => 2,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }
}
