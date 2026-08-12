<?php

namespace Database\Seeders\Demo;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * 组织主体测试数据 Seeder
 *
 * 包含：供应商、配送路线、商家（含商家账户）、司机、车辆（含司机-车辆绑定）
 *
 * 注意：seeder 执行顺序为先路由再商家，商家线路关联通过 delivery_route_stops 表管理
 */
class OrganizationDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSuppliers();
        $this->seedDrivers();
        $this->seedVehicles();
        $this->seedDeliveryRoutes();
        $this->seedMerchants();
        $this->seedRouteStops();
        $this->seedMerchantSkuVisibility();
    }

    protected function seedSuppliers(): void
    {
        $now = now();
        $suppliers = [
            ['name' => '鲜源农业有限公司', 'contact_name' => '陈供应', 'contact_phone' => '13900000001', 'address' => '安徽省宿州市埇桥区农批市场A1', 'bank_name' => '中国工商银行宿州分行', 'bank_account' => '1302000109200100001', 'settlement_cycle' => 1, 'status' => 1, 'remark' => '蔬菜水果类主力供应商', 'created_at' => $now, 'updated_at' => $now],
            ['name' => '绿野蔬菜种植基地', 'contact_name' => '李蔬菜', 'contact_phone' => '13900000002', 'address' => '安徽省宿州市埇桥区农批市场B3', 'bank_name' => '中国农业银行宿州分行', 'bank_account' => '1302000109200100002', 'settlement_cycle' => 1, 'status' => 1, 'remark' => '叶菜类专供', 'created_at' => $now, 'updated_at' => $now],
            ['name' => '丰润肉业有限公司', 'contact_name' => '王肉业', 'contact_phone' => '13900000003', 'address' => '安徽省宿州市埇桥区肉联厂C2', 'bank_name' => '中国建设银行宿州分行', 'bank_account' => '1302000109200100003', 'settlement_cycle' => 2, 'status' => 1, 'remark' => '猪牛肉类供应商', 'created_at' => $now, 'updated_at' => $now],
            ['name' => '海滨水产批发部', 'contact_name' => '赵水产', 'contact_phone' => '13900000004', 'address' => '安徽省宿州市埇桥区水产市场D5', 'bank_name' => '中国银行宿州分行', 'bank_account' => '1302000109200100004', 'settlement_cycle' => 1, 'status' => 1, 'remark' => '水产海鲜类', 'created_at' => $now, 'updated_at' => $now],
            ['name' => '恒达粮油贸易公司', 'contact_name' => '钱粮油', 'contact_phone' => '13900000005', 'address' => '安徽省宿州市埇桥区粮批市场E1', 'bank_name' => '中国邮政储蓄银行宿州分行', 'bank_account' => '1302000109200100005', 'settlement_cycle' => 3, 'status' => 1, 'remark' => '粮油干货类', 'created_at' => $now, 'updated_at' => $now],
        ];

        foreach ($suppliers as $supplier) {
            if (! DB::table('suppliers')->where('name', $supplier['name'])->exists()) {
                DB::table('suppliers')->insert($supplier);
            }
        }
    }

    /**
     * 确保仓库数据已创建（warehouses 原由 InventoryDemoSeeder 创建，但执行顺序在 organization 之后）
     */
    protected function ensureWarehouses(): void
    {
        $now = now();
        $warehouses = [
            ['name' => '总仓-农批市场', 'type' => 1, 'is_cold_chain' => 0, 'address' => '安徽省宿州市埇桥区农批市场内', 'sort' => 1, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '分仓-肉联厂', 'type' => 2, 'is_cold_chain' => 1, 'address' => '安徽省宿州市埇桥区肉联厂内', 'sort' => 2, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
        ];

        foreach ($warehouses as $warehouse) {
            if (! DB::table('warehouses')->where('name', $warehouse['name'])->exists()) {
                DB::table('warehouses')->insert($warehouse);
            }
        }
    }

    protected function seedDeliveryRoutes(): void
    {
        $now = now();

        // 确保仓库数据已创建（warehouses 原由 InventoryDemoSeeder 创建，但执行顺序在 organization 之后）
        $this->ensureWarehouses();

        // 获取司机/车辆用于默认配置
        $driver1 = DB::table('drivers')->where('phone', '13700000001')->first();
        $driver2 = DB::table('drivers')->where('phone', '13700000002')->first();
        $vehicle1 = DB::table('vehicles')->where('plate_number', '皖LT0001')->first();
        $vehicle2 = DB::table('vehicles')->where('plate_number', '皖LT0002')->first();
        $warehouse1 = DB::table('warehouses')->where('name', '总仓-农批市场')->first();

        $routes = [
            ['name' => '城区北线', 'code' => 'E01', 'warehouse_id' => $warehouse1?->id, 'default_driver_id' => $driver1?->id, 'default_vehicle_id' => $vehicle1?->id, 'color' => '#3B82F6', 'departure_time' => '06:00:00', 'estimated_duration' => 120, 'estimated_distance' => 45.5, 'description' => '人民路-淮海路-汴河路北侧', 'sort' => 1, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '城区南线', 'code' => 'E02', 'warehouse_id' => $warehouse1?->id, 'default_driver_id' => $driver2?->id, 'default_vehicle_id' => $vehicle2?->id, 'color' => '#10B981', 'departure_time' => '06:30:00', 'estimated_duration' => 100, 'estimated_distance' => 38.2, 'description' => '银河路-胜利路-宿怀路南侧', 'sort' => 2, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
        ];

        foreach ($routes as $route) {
            if (! DB::table('delivery_routes')->where('name', $route['name'])->exists()) {
                DB::table('delivery_routes')->insert($route);
            } else {
                // 更新已有线路的新字段
                DB::table('delivery_routes')->where('name', $route['name'])->update([
                    'code' => $route['code'],
                    'warehouse_id' => $route['warehouse_id'],
                    'default_driver_id' => $route['default_driver_id'],
                    'default_vehicle_id' => $route['default_vehicle_id'],
                    'color' => $route['color'],
                    'departure_time' => $route['departure_time'],
                    'estimated_duration' => $route['estimated_duration'],
                    'estimated_distance' => $route['estimated_distance'],
                    'updated_at' => $now,
                ]);
            }
        }

        // 线路商家点位（delivery_route_stops）在 seedMerchants 之后单独调用
    }

    protected function seedRouteStops(): void
    {
        $now = now();
        $routeNorth = DB::table('delivery_routes')->where('code', 'E01')->first();
        $routeSouth = DB::table('delivery_routes')->where('code', 'E02')->first();

        // 北线商家
        $northMerchants = [
            ['name' => '味之初餐饮店', 'seq' => 1, 'address' => '安徽省宿州市埇桥区人民路88号'],
            ['name' => '鲜之味快餐店', 'seq' => 2, 'address' => '安徽省宿州市埇桥区淮海路120号'],
            ['name' => '家常菜馆', 'seq' => 3, 'address' => '安徽省宿州市埇桥区汴河路56号'],
        ];

        // 南线商家
        $southMerchants = [
            ['name' => '鑫鑫小吃店', 'seq' => 1, 'address' => '安徽省宿州市埇桥区银河一路32号'],
            ['name' => '老街坊饭店', 'seq' => 2, 'address' => '安徽省宿州市埇桥区胜利路18号'],
        ];

        if ($routeNorth) {
            foreach ($northMerchants as $m) {
                $merchant = DB::table('merchants')->where('name', $m['name'])->first();
                if (!$merchant) continue;
                if (!DB::table('delivery_route_stops')->where('route_id', $routeNorth->id)->where('merchant_id', $merchant->id)->exists()) {
                    DB::table('delivery_route_stops')->insert([
                        'route_id' => $routeNorth->id,
                        'merchant_id' => $merchant->id,
                        'sequence_no' => $m['seq'],
                        'address' => $m['address'],
                        'latitude' => null,
                        'longitude' => null,
                        'default_service_time' => 15,
                        'is_active' => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }

        if ($routeSouth) {
            foreach ($southMerchants as $m) {
                $merchant = DB::table('merchants')->where('name', $m['name'])->first();
                if (!$merchant) continue;
                if (!DB::table('delivery_route_stops')->where('route_id', $routeSouth->id)->where('merchant_id', $merchant->id)->exists()) {
                    DB::table('delivery_route_stops')->insert([
                        'route_id' => $routeSouth->id,
                        'merchant_id' => $merchant->id,
                        'sequence_no' => $m['seq'],
                        'address' => $m['address'],
                        'latitude' => null,
                        'longitude' => null,
                        'default_service_time' => 15,
                        'is_active' => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }
    }

    protected function seedMerchants(): void
    {
        $now = now();

        $merchants = [
            ['name' => '味之初餐饮店', 'contact_name' => '吴老板', 'contact_phone' => '15800000001', 'address' => '安徽省宿州市埇桥区人民路88号', 'min_order_amount' => 50000, 'settlement_type' => 1, 'credit_limit' => 5000000, 'status' => 1, 'remark' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '鲜之味快餐店', 'contact_name' => '郑老板', 'contact_phone' => '15800000002', 'address' => '安徽省宿州市埇桥区淮海路120号', 'min_order_amount' => 30000, 'settlement_type' => 2, 'credit_limit' => 10000000, 'status' => 1, 'remark' => '账期客户', 'created_at' => $now, 'updated_at' => $now],
            ['name' => '家常菜馆', 'contact_name' => '冯老板', 'contact_phone' => '15800000003', 'address' => '安徽省宿州市埇桥区汴河路56号', 'min_order_amount' => 0, 'settlement_type' => 1, 'credit_limit' => 0, 'status' => 1, 'remark' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '鑫鑫小吃店', 'contact_name' => '蒋老板', 'contact_phone' => '15800000004', 'address' => '安徽省宿州市埇桥区银河一路32号', 'min_order_amount' => 20000, 'settlement_type' => 3, 'credit_limit' => 3000000, 'status' => 1, 'remark' => '预付款客户', 'created_at' => $now, 'updated_at' => $now],
            ['name' => '老街坊饭店', 'contact_name' => '韩老板', 'contact_phone' => '15800000005', 'address' => '安徽省宿州市埇桥区胜利路18号', 'min_order_amount' => 0, 'settlement_type' => 1, 'credit_limit' => 0, 'status' => 1, 'remark' => null, 'created_at' => $now, 'updated_at' => $now],
        ];

        foreach ($merchants as $merchant) {
            if (! DB::table('merchants')->where('name', $merchant['name'])->exists()) {
                DB::table('merchants')->insert($merchant);
            }
        }

        // 创建商家账户
        foreach (DB::table('merchants')->get() as $merchant) {
            if (! DB::table('merchant_accounts')->where('merchant_id', $merchant->id)->exists()) {
                DB::table('merchant_accounts')->insert([
                    'merchant_id' => $merchant->id,
                    'balance' => 0,
                    'total_recharge' => 0,
                    'total_consumption' => 0,
                    'credit_limit' => $merchant->credit_limit ?: 5000000,
                    'approval_status' => 2,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    protected function seedDrivers(): void
    {
        $now = now();
        $drivers = [
            ['name' => '周师傅', 'phone' => '13700000001', 'id_card' => '342201199001011234', 'online_status' => 1, 'status' => 1, 'remark' => '北线司机，5年驾龄', 'created_at' => $now, 'updated_at' => $now],
            ['name' => '马师傅', 'phone' => '13700000002', 'id_card' => '342201199203025678', 'online_status' => 1, 'status' => 1, 'remark' => '南线司机，3年驾龄', 'created_at' => $now, 'updated_at' => $now],
        ];

        foreach ($drivers as $driver) {
            if (! DB::table('drivers')->where('phone', $driver['phone'])->exists()) {
                DB::table('drivers')->insert($driver);
            }
        }
    }

    protected function seedVehicles(): void
    {
        $now = now();
        $vehicles = [
            ['plate_number' => '皖LT0001', 'name' => '冷藏车1号', 'type' => 'refrigerated', 'is_cold_chain' => 1, 'capacity_kg' => 2000, 'capacity_volume' => 12.5, 'status' => 1, 'remark' => '北线专用冷藏车', 'created_at' => $now, 'updated_at' => $now],
            ['plate_number' => '皖LT0002', 'name' => '厢式货车1号', 'type' => 'van', 'is_cold_chain' => 0, 'capacity_kg' => 3000, 'capacity_volume' => 18.0, 'status' => 1, 'remark' => '南线专用厢式货车', 'created_at' => $now, 'updated_at' => $now],
        ];

        foreach ($vehicles as $vehicle) {
            if (! DB::table('vehicles')->where('plate_number', $vehicle['plate_number'])->exists()) {
                DB::table('vehicles')->insert($vehicle);
            } else {
                // 更新已有车辆的新字段
                DB::table('vehicles')->where('plate_number', $vehicle['plate_number'])->update([
                    'name' => $vehicle['name'],
                    'type' => $vehicle['type'],
                    'capacity_kg' => $vehicle['capacity_kg'],
                    'capacity_volume' => $vehicle['capacity_volume'],
                    'remark' => $vehicle['remark'],
                    'updated_at' => $now,
                ]);
            }
        }

        // 分配司机-车辆
        $driver1 = DB::table('drivers')->where('phone', '13700000001')->first();
        $driver2 = DB::table('drivers')->where('phone', '13700000002')->first();
        $vehicle1 = DB::table('vehicles')->where('plate_number', '皖LT0001')->first();
        $vehicle2 = DB::table('vehicles')->where('plate_number', '皖LT0002')->first();

        if ($driver1 && $vehicle1 && ! DB::table('driver_vehicles')->where('driver_id', $driver1->id)->where('vehicle_id', $vehicle1->id)->exists()) {
            DB::table('driver_vehicles')->insert(['driver_id' => $driver1->id, 'vehicle_id' => $vehicle1->id, 'is_default' => 1, 'bound_at' => $now, 'created_at' => $now, 'updated_at' => $now]);
        }
        if ($driver2 && $vehicle2 && ! DB::table('driver_vehicles')->where('driver_id', $driver2->id)->where('vehicle_id', $vehicle2->id)->exists()) {
            DB::table('driver_vehicles')->insert(['driver_id' => $driver2->id, 'vehicle_id' => $vehicle2->id, 'is_default' => 1, 'bound_at' => $now, 'created_at' => $now, 'updated_at' => $now]);
        }
    }

    protected function seedMerchantSkuVisibility(): void
    {
        $now = now();
        $merchant1 = DB::table('merchants')->where('name', '味之初餐饮店')->first();
        if (! $merchant1) return;

        $merchant2 = DB::table('merchants')->where('name', '鲜味轩小吃店')->first();

        // 商品级配置：味之初可见大白菜、五花肉
        $productVisible = ['大白菜', '五花肉'];
        foreach ($productVisible as $productName) {
            $product = DB::table('products')->where('name', $productName)->first();
            if (! $product) continue;

            if (! DB::table('merchant_sku_visibility')
                ->where('merchant_id', $merchant1->id)
                ->where('target_type', 'product')
                ->where('product_id', $product->id)
                ->exists()) {
                DB::table('merchant_sku_visibility')->insert([
                    'merchant_id' => $merchant1->id,
                    'target_type' => 'product',
                    'product_id' => $product->id,
                    'sku_id' => null,
                    'is_visible' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // SKU级配置：味之初可见鲜虾的特定SKU
        $product = DB::table('products')->where('name', '鲜虾')->first();
        if ($product) {
            $sku = DB::table('skus')->where('product_id', $product->id)->first();
            if ($sku && ! DB::table('merchant_sku_visibility')
                ->where('merchant_id', $merchant1->id)
                ->where('target_type', 'sku')
                ->where('sku_id', $sku->id)
                ->exists()) {
                DB::table('merchant_sku_visibility')->insert([
                    'merchant_id' => $merchant1->id,
                    'target_type' => 'sku',
                    'product_id' => $product->id,
                    'sku_id' => $sku->id,
                    'is_visible' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // 商品级配置：鲜味轩不可见金龙鱼大豆油
        if ($merchant2) {
            $hiddenProduct = DB::table('products')->where('name', '金龙鱼大豆油')->first();
            if ($hiddenProduct && ! DB::table('merchant_sku_visibility')
                ->where('merchant_id', $merchant2->id)
                ->where('target_type', 'product')
                ->where('product_id', $hiddenProduct->id)
                ->exists()) {
                DB::table('merchant_sku_visibility')->insert([
                    'merchant_id' => $merchant2->id,
                    'target_type' => 'product',
                    'product_id' => $hiddenProduct->id,
                    'sku_id' => null,
                    'is_visible' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
