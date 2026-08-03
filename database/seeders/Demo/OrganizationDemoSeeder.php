<?php

namespace Database\Seeders\Demo;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * 组织主体测试数据 Seeder
 *
 * 包含：供应商、配送路线、商家（含商家账户）、司机、车辆（含司机-车辆绑定）
 *
 * 注意：seeder 执行顺序改为先路由再商家，因为商家需要关联 delivery_route_id
 */
class OrganizationDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedDeliveryRoutes();
        $this->seedSuppliers();
        $this->seedMerchants();
        $this->seedDrivers();
        $this->seedVehicles();
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

    protected function seedDeliveryRoutes(): void
    {
        $now = now();
        $routes = [
            ['name' => '城区北线', 'description' => '人民路-淮海路-汴河路北侧', 'sort' => 1, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '城区南线', 'description' => '银河路-胜利路-宿怀路南侧', 'sort' => 2, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
        ];

        foreach ($routes as $route) {
            if (! DB::table('delivery_routes')->where('name', $route['name'])->exists()) {
                DB::table('delivery_routes')->insert($route);
            }
        }
    }

    protected function seedMerchants(): void
    {
        $now = now();

        // 获取线路ID
        $routeNorth = DB::table('delivery_routes')->where('name', '城区北线')->first();
        $routeSouth = DB::table('delivery_routes')->where('name', '城区南线')->first();
        $routeNorthId = $routeNorth?->id;
        $routeSouthId = $routeSouth?->id;

        $merchants = [
            ['name' => '味之初餐饮店', 'contact_name' => '吴老板', 'contact_phone' => '15800000001', 'address' => '安徽省宿州市埇桥区人民路88号', 'delivery_route_id' => $routeNorthId, 'delivery_sort' => 1, 'min_order_amount' => 50000, 'settlement_type' => 1, 'credit_limit' => 5000000, 'status' => 1, 'remark' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '鲜之味快餐店', 'contact_name' => '郑老板', 'contact_phone' => '15800000002', 'address' => '安徽省宿州市埇桥区淮海路120号', 'delivery_route_id' => $routeNorthId, 'delivery_sort' => 2, 'min_order_amount' => 30000, 'settlement_type' => 2, 'credit_limit' => 10000000, 'status' => 1, 'remark' => '账期客户', 'created_at' => $now, 'updated_at' => $now],
            ['name' => '家常菜馆', 'contact_name' => '冯老板', 'contact_phone' => '15800000003', 'address' => '安徽省宿州市埇桥区汴河路56号', 'delivery_route_id' => $routeNorthId, 'delivery_sort' => 3, 'min_order_amount' => 0, 'settlement_type' => 1, 'credit_limit' => 0, 'status' => 1, 'remark' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '鑫鑫小吃店', 'contact_name' => '蒋老板', 'contact_phone' => '15800000004', 'address' => '安徽省宿州市埇桥区银河一路32号', 'delivery_route_id' => $routeSouthId, 'delivery_sort' => 1, 'min_order_amount' => 20000, 'settlement_type' => 3, 'credit_limit' => 3000000, 'status' => 1, 'remark' => '预付款客户', 'created_at' => $now, 'updated_at' => $now],
            ['name' => '老街坊饭店', 'contact_name' => '韩老板', 'contact_phone' => '15800000005', 'address' => '安徽省宿州市埇桥区胜利路18号', 'delivery_route_id' => $routeSouthId, 'delivery_sort' => 2, 'min_order_amount' => 0, 'settlement_type' => 1, 'credit_limit' => 0, 'status' => 1, 'remark' => null, 'created_at' => $now, 'updated_at' => $now],
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
            ['plate_number' => '皖LT0001', 'vehicle_type' => '冷藏车', 'is_cold_chain' => 1, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['plate_number' => '皖LT0002', 'vehicle_type' => '厢式货车', 'is_cold_chain' => 0, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
        ];

        foreach ($vehicles as $vehicle) {
            if (! DB::table('vehicles')->where('plate_number', $vehicle['plate_number'])->exists()) {
                DB::table('vehicles')->insert($vehicle);
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
