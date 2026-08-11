<?php

namespace Database\Seeders\Demo;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * 配送管理测试数据 Seeder（v2）
 *
 * 包含：拣货任务（v2新字段route_id/delivery_date/total_skus/total_quantity + items.merchant_id）、
 * 送货单（delivery_notes + delivery_note_items）、配送任务、签收存证、冷链温度、差异单
 */
class DeliveryDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedPickingTasks();
        $this->seedPickingTaskItems();
        $this->seedDeliveryNotes();
        $this->seedDeliveryNoteItems();
        $this->seedDeliveryTasks();
        $this->seedSignatures();
        $this->seedTemperatures();
        $this->seedDiscrepancies();
    }

    // ========== 拣货任务 ==========

    protected function seedPickingTasks(): void
    {
        $now = now();
        $warehouse1 = DB::table('warehouses')->where('name', '总仓-农批市场')->first();
        $pickerUser = DB::table('users')->where('username', 'picker1')->first();
        $route1 = DB::table('delivery_routes')->where('code', 'E01')->first();

        if (!$warehouse1) return;

        // 拣货任务1：已完成（北线，昨天）
        if (!DB::table('picking_tasks')->where('task_no', 'PK-E01-20260810-001')->exists()) {
            DB::table('picking_tasks')->insert([
                'task_no' => 'PK-E01-20260810-001',
                'warehouse_id' => $warehouse1->id,
                'route_id' => $route1?->id,
                'delivery_date' => '2026-08-10',
                'picker_id' => $pickerUser?->id,
                'batch' => 1,
                'status' => 3, // 已完成
                'total_skus' => 2,
                'total_quantity' => 2500,
                'started_at' => $now->copy()->subDay()->setTime(5, 0, 0),
                'completed_at' => $now->copy()->subDay()->setTime(5, 45, 0),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 拣货任务2：拣货中（北线，今天）
        if (!DB::table('picking_tasks')->where('task_no', 'PK-E01-20260811-001')->exists()) {
            DB::table('picking_tasks')->insert([
                'task_no' => 'PK-E01-20260811-001',
                'warehouse_id' => $warehouse1->id,
                'route_id' => $route1?->id,
                'delivery_date' => '2026-08-11',
                'picker_id' => $pickerUser?->id,
                'batch' => 1,
                'status' => 2, // 拣货中
                'total_skus' => 1,
                'total_quantity' => 500,
                'started_at' => $now->copy()->setTime(5, 0, 0),
                'completed_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 拣货任务3：待分配（南线，今天）
        $route2 = DB::table('delivery_routes')->where('code', 'E02')->first();
        if ($route2 && !DB::table('picking_tasks')->where('task_no', 'PK-E02-20260811-001')->exists()) {
            DB::table('picking_tasks')->insert([
                'task_no' => 'PK-E02-20260811-001',
                'warehouse_id' => $warehouse1->id,
                'route_id' => $route2->id,
                'delivery_date' => '2026-08-11',
                'picker_id' => null,
                'batch' => 1,
                'status' => 1, // 待分配
                'total_skus' => 0,
                'total_quantity' => 0,
                'started_at' => null,
                'completed_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    protected function seedPickingTaskItems(): void
    {
        $now = now();

        // ===== 拣货任务1（已完成）明细 =====
        $pickingTask1 = DB::table('picking_tasks')->where('task_no', 'PK-E01-20260810-001')->first();
        $order1 = DB::table('orders')->where('order_no', 'ORD-20260728-001')->first();

        if ($pickingTask1 && $order1) {
            $merchant1 = DB::table('merchants')->where('id', $order1->merchant_id)->first();
            $productData = [
                ['product' => '大白菜', 'quantity' => 2000],
                ['product' => '土豆', 'quantity' => 500],
            ];

            foreach ($productData as $pd) {
                $product = DB::table('products')->where('name', $pd['product'])->first();
                if (!$product) continue;
                $sku = DB::table('skus')->where('product_id', $product->id)->first();
                if (!$sku) continue;
                $orderItem = DB::table('order_items')->where('order_id', $order1->id)->where('sku_id', $sku->id)->first();
                if (!$orderItem) continue;

                if (!DB::table('picking_task_items')->where('picking_task_id', $pickingTask1->id)->where('sku_id', $sku->id)->exists()) {
                    DB::table('picking_task_items')->insert([
                        'picking_task_id' => $pickingTask1->id,
                        'order_id' => $order1->id,
                        'order_item_id' => $orderItem->id,
                        'sku_id' => $sku->id,
                        'merchant_id' => $order1->merchant_id,
                        'required_quantity' => $pd['quantity'],
                        'picked_quantity' => $pd['quantity'],
                        'status' => 2, // 已拣货
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }

        // ===== 拣货任务2（拣货中）明细 =====
        $pickingTask2 = DB::table('picking_tasks')->where('task_no', 'PK-E01-20260811-001')->first();
        $order2 = DB::table('orders')->where('order_no', 'ORD-20260728-002')->first();

        if ($pickingTask2 && $order2) {
            $product = DB::table('products')->where('name', '五花肉')->first();
            if ($product) {
                $sku = DB::table('skus')->where('product_id', $product->id)->first();
                $orderItem = $sku ? DB::table('order_items')->where('order_id', $order2->id)->where('sku_id', $sku->id)->first() : null;

                if ($sku && $orderItem && !DB::table('picking_task_items')->where('picking_task_id', $pickingTask2->id)->where('sku_id', $sku->id)->exists()) {
                    DB::table('picking_task_items')->insert([
                        'picking_task_id' => $pickingTask2->id,
                        'order_id' => $order2->id,
                        'order_item_id' => $orderItem->id,
                        'sku_id' => $sku->id,
                        'merchant_id' => $order2->merchant_id,
                        'required_quantity' => 500,
                        'picked_quantity' => 300, // 部分拣货
                        'status' => 3, // 差异
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }
    }

    // ========== 送货单 ==========

    protected function seedDeliveryNotes(): void
    {
        $now = now();

        // 送货单1：已签收（味之初，北线，昨天）
        $order1 = DB::table('orders')->where('order_no', 'ORD-20260728-001')->first();
        $merchant1 = $order1 ? DB::table('merchants')->where('id', $order1->merchant_id)->first() : null;

        if ($order1 && $merchant1 && !DB::table('delivery_notes')->where('note_no', 'DN-E01-20260810-001')->exists()) {
            DB::table('delivery_notes')->insert([
                'note_no' => 'DN-E01-20260810-001',
                'task_id' => 0,
                'merchant_id' => $merchant1->id,
                'merchant_name' => $merchant1->name,
                'merchant_address' => $merchant1->address,
                'delivery_date' => '2026-08-10',
                'order_ids' => json_encode([$order1->id]),
                'order_nos' => json_encode([$order1->order_no]),
                'product_summary' => '大白菜、土豆',
                'total_quantity' => 2500,
                'total_weight' => null,
                'status' => 3, // 已签收
                'delivered_at' => $now->copy()->subDay()->setTime(7, 30, 0),
                'delivery_method' => 'signature',
                'remark' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 送货单2：已分货（家常菜馆，北线，昨天）
        $order2 = DB::table('orders')->where('order_no', 'ORD-20260728-002')->first();
        $merchant2 = $order2 ? DB::table('merchants')->where('id', $order2->merchant_id)->first() : null;

        if ($order2 && $merchant2 && !DB::table('delivery_notes')->where('note_no', 'DN-E01-20260810-002')->exists()) {
            DB::table('delivery_notes')->insert([
                'note_no' => 'DN-E01-20260810-002',
                'task_id' => 0,
                'merchant_id' => $merchant2->id,
                'merchant_name' => $merchant2->name,
                'merchant_address' => $merchant2->address,
                'delivery_date' => '2026-08-10',
                'order_ids' => json_encode([$order2->id]),
                'order_nos' => json_encode([$order2->order_no]),
                'product_summary' => '五花肉',
                'total_quantity' => 500,
                'total_weight' => null,
                'status' => 2, // 已分货
                'delivered_at' => $now->copy()->subDay()->setTime(8, 0, 0),
                'delivery_method' => 'manual',
                'remark' => '等待签收确认',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 送货单3：待分货（鲜之味快餐店，北线，今天）
        $merchant3 = DB::table('merchants')->where('name', '鲜之味快餐店')->first();
        if ($merchant3 && !DB::table('delivery_notes')->where('note_no', 'DN-E01-20260811-001')->exists()) {
            DB::table('delivery_notes')->insert([
                'note_no' => 'DN-E01-20260811-001',
                'task_id' => 0,
                'merchant_id' => $merchant3->id,
                'merchant_name' => $merchant3->name,
                'merchant_address' => $merchant3->address,
                'delivery_date' => '2026-08-11',
                'order_ids' => json_encode([]),
                'order_nos' => json_encode([]),
                'product_summary' => '鲜虾',
                'total_quantity' => 1000,
                'total_weight' => null,
                'status' => 1, // 待分货
                'delivered_at' => null,
                'delivery_method' => null,
                'remark' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 送货单4：待分货（鑫鑫小吃店，南线，今天）
        $merchant4 = DB::table('merchants')->where('name', '鑫鑫小吃店')->first();
        if ($merchant4 && !DB::table('delivery_notes')->where('note_no', 'DN-E02-20260811-001')->exists()) {
            DB::table('delivery_notes')->insert([
                'note_no' => 'DN-E02-20260811-001',
                'task_id' => 0,
                'merchant_id' => $merchant4->id,
                'merchant_name' => $merchant4->name,
                'merchant_address' => $merchant4->address,
                'delivery_date' => '2026-08-11',
                'order_ids' => json_encode([]),
                'order_nos' => json_encode([]),
                'product_summary' => '土豆',
                'total_quantity' => 800,
                'total_weight' => null,
                'status' => 1, // 待分货
                'delivered_at' => null,
                'delivery_method' => null,
                'remark' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    protected function seedDeliveryNoteItems(): void
    {
        $now = now();

        // 送货单1 明细（已签收）
        $note1 = DB::table('delivery_notes')->where('note_no', 'DN-E01-20260810-001')->first();
        $order1 = DB::table('orders')->where('order_no', 'ORD-20260728-001')->first();

        if ($note1) {
            $items1 = [
                ['product' => '大白菜', 'quantity' => 2000, 'picked' => 2000],
                ['product' => '土豆', 'quantity' => 500, 'picked' => 500],
            ];
            foreach ($items1 as $item) {
                $product = DB::table('products')->where('name', $item['product'])->first();
                if (!$product) continue;
                $sku = DB::table('skus')->where('product_id', $product->id)->first();
                if (!$sku) continue;

                if (!DB::table('delivery_note_items')->where('delivery_note_id', $note1->id)->where('sku_id', $sku->id)->exists()) {
                    DB::table('delivery_note_items')->insert([
                        'delivery_note_id' => $note1->id,
                        'sku_id' => $sku->id,
                        'sku_name' => $item['product'],
                        'unit' => '斤',
                        'quantity' => $item['quantity'],
                        'picked_quantity' => $item['picked'],
                        'order_id' => $order1?->id,
                        'order_no' => $order1?->order_no,
                        'status' => 2, // 已分货
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }

        // 送货单2 明细（已分货）
        $note2 = DB::table('delivery_notes')->where('note_no', 'DN-E01-20260810-002')->first();
        $order2 = DB::table('orders')->where('order_no', 'ORD-20260728-002')->first();

        if ($note2) {
            $product = DB::table('products')->where('name', '五花肉')->first();
            if ($product) {
                $sku = DB::table('skus')->where('product_id', $product->id)->first();
                if ($sku && !DB::table('delivery_note_items')->where('delivery_note_id', $note2->id)->where('sku_id', $sku->id)->exists()) {
                    DB::table('delivery_note_items')->insert([
                        'delivery_note_id' => $note2->id,
                        'sku_id' => $sku->id,
                        'sku_name' => '五花肉',
                        'unit' => '斤',
                        'quantity' => 500,
                        'picked_quantity' => 500,
                        'order_id' => $order2?->id,
                        'order_no' => $order2?->order_no,
                        'status' => 2, // 已分货
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }

        // 送货单3 明细（待分货）
        $note3 = DB::table('delivery_notes')->where('note_no', 'DN-E01-20260811-001')->first();
        if ($note3) {
            $product = DB::table('products')->where('name', '鲜虾')->first();
            if ($product) {
                $sku = DB::table('skus')->where('product_id', $product->id)->first();
                if ($sku && !DB::table('delivery_note_items')->where('delivery_note_id', $note3->id)->where('sku_id', $sku->id)->exists()) {
                    DB::table('delivery_note_items')->insert([
                        'delivery_note_id' => $note3->id,
                        'sku_id' => $sku->id,
                        'sku_name' => '鲜虾',
                        'unit' => '斤',
                        'quantity' => 1000,
                        'picked_quantity' => 0,
                        'order_id' => null,
                        'order_no' => null,
                        'status' => 1, // 待分货
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }

        // 送货单4 明细（待分货）
        $note4 = DB::table('delivery_notes')->where('note_no', 'DN-E02-20260811-001')->first();
        if ($note4) {
            $product = DB::table('products')->where('name', '土豆')->first();
            if ($product) {
                $sku = DB::table('skus')->where('product_id', $product->id)->first();
                if ($sku && !DB::table('delivery_note_items')->where('delivery_note_id', $note4->id)->where('sku_id', $sku->id)->exists()) {
                    DB::table('delivery_note_items')->insert([
                        'delivery_note_id' => $note4->id,
                        'sku_id' => $sku->id,
                        'sku_name' => '土豆',
                        'unit' => '斤',
                        'quantity' => 800,
                        'picked_quantity' => 0,
                        'order_id' => null,
                        'order_no' => null,
                        'status' => 1, // 待分货
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }
    }

    // ========== 配送任务 ==========

    protected function seedDeliveryTasks(): void
    {
        $now = now();
        $route1 = DB::table('delivery_routes')->where('code', 'E01')->first();
        $driver1 = DB::table('drivers')->where('phone', '13700000001')->first();
        $vehicle1 = DB::table('vehicles')->where('plate_number', '皖LT0001')->first();
        $order1 = DB::table('orders')->where('order_no', 'ORD-20260728-001')->first();

        if (!$route1) return;

        $taskNo = 'T-E01-20260810-001';

        if (!DB::table('delivery_tasks')->where('task_no', $taskNo)->exists()) {
            $taskId = DB::table('delivery_tasks')->insertGetId([
                'task_no' => $taskNo,
                'route_id' => $route1->id,
                'delivery_date' => '2026-08-10',
                'generated_at' => $now,
                'driver_id' => $driver1?->id,
                'vehicle_id' => $vehicle1?->id,
                'batch' => 1,
                'status' => 5, // 已完成
                'planned_start_time' => $now->copy()->subDay()->setTime(6, 0, 0),
                'actual_start_time' => $now->copy()->subDay()->setTime(6, 5, 0),
                'actual_complete_time' => $now->copy()->subDay()->setTime(8, 30, 0),
                'total_stops' => 2,
                'completed_stops' => 2,
                'skipped_stops' => 0,
                'total_orders' => 2,
                'has_urgent' => 0,
                'has_important' => 0,
                'remark' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // 创建 delivery_task_details
            if ($order1) {
                $merchant = DB::table('merchants')->where('id', $order1->merchant_id)->first();

                if (!DB::table('delivery_task_details')->where('task_id', $taskId)->where('order_id', $order1->id)->exists()) {
                    $detailId1 = DB::table('delivery_task_details')->insertGetId([
                        'task_id' => $taskId,
                        'order_id' => $order1->id,
                        'merchant_id' => $order1->merchant_id,
                        'merchant_name' => $merchant?->name,
                        'merchant_address' => $merchant?->address,
                        'order_date' => $order1->order_date,
                        'delivery_date' => $order1->delivery_date,
                        'product_summary' => '大白菜、土豆',
                        'total_quantity' => 2500,
                        'total_weight' => null,
                        'source_type' => 'order',
                        'source_id' => $order1->id,
                        'status' => 3, // 已送达
                        'delivered_at' => $now->copy()->subDay()->setTime(7, 25, 0),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    // 顺序表 - 味之初
                    $routeStop = DB::table('delivery_route_stops')
                        ->where('route_id', $route1->id)
                        ->where('merchant_id', $order1->merchant_id)
                        ->first();

                    if (!DB::table('delivery_task_sequences')->where('task_id', $taskId)->where('merchant_id', $order1->merchant_id)->exists()) {
                        DB::table('delivery_task_sequences')->insert([
                            'task_id' => $taskId,
                            'task_detail_ids' => json_encode([$detailId1]),
                            'merchant_id' => $order1->merchant_id,
                            'merchant_name' => $merchant?->name,
                            'merchant_address' => $merchant?->address ?? $routeStop?->address,
                            'latitude' => $routeStop?->latitude,
                            'longitude' => $routeStop?->longitude,
                            'base_sequence_no' => $routeStop?->sequence_no ?? 1,
                            'sequence_no' => 1,
                            'is_urgent' => 0,
                            'is_important' => 0,
                            'status' => 4, // 已送达
                            'actual_arrival' => $now->copy()->subDay()->setTime(7, 15, 0),
                            'actual_departure' => $now->copy()->subDay()->setTime(7, 30, 0),
                            'actual_delivered_at' => $now->copy()->subDay()->setTime(7, 25, 0),
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }
                }
            }

            // 家常菜馆的配送明细
            $order2 = DB::table('orders')->where('order_no', 'ORD-20260728-002')->first();
            if ($order2) {
                $merchant2 = DB::table('merchants')->where('id', $order2->merchant_id)->first();

                if (!DB::table('delivery_task_details')->where('task_id', $taskId)->where('order_id', $order2->id)->exists()) {
                    $detailId2 = DB::table('delivery_task_details')->insertGetId([
                        'task_id' => $taskId,
                        'order_id' => $order2->id,
                        'merchant_id' => $order2->merchant_id,
                        'merchant_name' => $merchant2?->name,
                        'merchant_address' => $merchant2?->address,
                        'order_date' => $order2->order_date,
                        'delivery_date' => $order2->delivery_date,
                        'product_summary' => '五花肉',
                        'total_quantity' => 500,
                        'total_weight' => null,
                        'source_type' => 'order',
                        'source_id' => $order2->id,
                        'status' => 3, // 已送达
                        'delivered_at' => $now->copy()->subDay()->setTime(7, 50, 0),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    // 顺序表 - 家常菜馆
                    $routeStop2 = DB::table('delivery_route_stops')
                        ->where('route_id', $route1->id)
                        ->where('merchant_id', $order2->merchant_id)
                        ->first();

                    if (!DB::table('delivery_task_sequences')->where('task_id', $taskId)->where('merchant_id', $order2->merchant_id)->exists()) {
                        DB::table('delivery_task_sequences')->insert([
                            'task_id' => $taskId,
                            'task_detail_ids' => json_encode([$detailId2]),
                            'merchant_id' => $order2->merchant_id,
                            'merchant_name' => $merchant2?->name,
                            'merchant_address' => $merchant2?->address ?? $routeStop2?->address,
                            'latitude' => $routeStop2?->latitude,
                            'longitude' => $routeStop2?->longitude,
                            'base_sequence_no' => $routeStop2?->sequence_no ?? 2,
                            'sequence_no' => 2,
                            'is_urgent' => 0,
                            'is_important' => 0,
                            'status' => 4, // 已送达
                            'actual_arrival' => $now->copy()->subDay()->setTime(7, 40, 0),
                            'actual_departure' => $now->copy()->subDay()->setTime(7, 55, 0),
                            'actual_delivered_at' => $now->copy()->subDay()->setTime(7, 50, 0),
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }
                }
            }

            // 配送轨迹
            if ($driver1 && !DB::table('delivery_tracks')->where('delivery_task_id', $taskId)->exists()) {
                DB::table('delivery_tracks')->insert([
                    ['delivery_task_id' => $taskId, 'driver_id' => $driver1->id, 'latitude' => 33720000, 'longitude' => 116970000, 'location_desc' => '农批市场出发', 'reported_at' => $now->copy()->subDay()->setTime(6, 5, 0), 'created_at' => $now],
                    ['delivery_task_id' => $taskId, 'driver_id' => $driver1->id, 'latitude' => 33721000, 'longitude' => 116975000, 'location_desc' => '人民路中段', 'reported_at' => $now->copy()->subDay()->setTime(6, 30, 0), 'created_at' => $now],
                ]);
            }
        }
    }

    protected function seedSignatures(): void
    {
        $now = now();
        $order1 = DB::table('orders')->where('order_no', 'ORD-20260728-001')->first();
        $task = DB::table('delivery_tasks')->where('task_no', 'T-E01-20260810-001')->first();

        if ($order1 && $task && !DB::table('signatures')->where('order_id', $order1->id)->exists()) {
            DB::table('signatures')->insert([
                'order_id' => $order1->id, 'delivery_task_id' => $task->id,
                'type' => 1, 'image_url' => '/uploads/signatures/demo-sign-001.jpg',
                'signer_name' => '吴老板', 'signed_at' => $now->copy()->subDay()->setTime(7, 20, 0), 'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }

    protected function seedTemperatures(): void
    {
        $now = now();
        $task = DB::table('delivery_tasks')->where('task_no', 'T-E01-20260810-001')->first();

        if ($task && !DB::table('temperatures')->where('delivery_task_id', $task->id)->exists()) {
            DB::table('temperatures')->insert([
                ['delivery_task_id' => $task->id, 'temperature' => -180, 'recorded_at' => $now->copy()->subDay()->setTime(6, 10, 0), 'created_at' => $now],
                ['delivery_task_id' => $task->id, 'temperature' => -150, 'recorded_at' => $now->copy()->subDay()->setTime(7, 0, 0), 'created_at' => $now],
            ]);
        }
    }

    protected function seedDiscrepancies(): void
    {
        $now = now();
        $order1 = DB::table('orders')->where('order_no', 'ORD-20260728-001')->first();

        if ($order1 && !DB::table('discrepancies')->where('order_id', $order1->id)->exists()) {
            $orderItem = DB::table('order_items')->where('order_id', $order1->id)->first();

            DB::table('discrepancies')->insert([
                'discrepancy_no' => 'DIS-20260810-001', 'order_id' => $order1->id,
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
