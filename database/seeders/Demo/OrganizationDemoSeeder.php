<?php

namespace Database\Seeders\Demo;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * 组织主体测试数据 Seeder
 *
 * 包含：供应商、商家（含商家账户）、配送路线、司机、车辆（含司机-车辆绑定）
 */
class OrganizationDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSuppliers();
        $this->seedMerchants();
        $this->seedDeliveryRoutes();
        $this->seedDrivers();
        $this->seedVehicles();
    }

    protected function seedSuppliers(): void
    {
        $now = now();
        $suppliers = [
            ['name' => '鲜源农业有限公司', 'contact_name' => '陈供应', 'contact_phone' => '13900000001', 'address' => '安徽省宿州市埇桥区农批市场A1', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '绿野蔬菜种植基地', 'contact_name' => '李蔬菜', 'contact_phone' => '13900000002', 'address' => '安徽省宿州市埇桥区农批市场B3', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '丰润肉业有限公司', 'contact_name' => '王肉业', 'contact_phone' => '13900000003', 'address' => '安徽省宿州市埇桥区肉联厂C2', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '海滨水产批发部', 'contact_name' => '赵水产', 'contact_phone' => '13900000004', 'address' => '安徽省宿州市埇桥区水产市场D5', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '恒达粮油贸易公司', 'contact_name' => '钱粮油', 'contact_phone' => '13900000005', 'address' => '安徽省宿州市埇桥区粮批市场E1', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
        ];

        foreach ($suppliers as $supplier) {
            if (! DB::table('suppliers')->where('name', $supplier['name'])->exists()) {
                DB::table('suppliers')->insert($supplier);
            }
        }
    }

    protected function seedMerchants(): void
    {
        $now = now();
        $merchants = [
            ['name' => '味之初餐饮店', 'contact_name' => '吴老板', 'contact_phone' => '15800000001', 'address' => '安徽省宿州市埇桥区人民路88号', 'min_order_amount' => 0, 'settlement_type' => 1, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '鲜之味快餐店', 'contact_name' => '郑老板', 'contact_phone' => '15800000002', 'address' => '安徽省宿州市埇桥区淮海路120号', 'min_order_amount' => 0, 'settlement_type' => 1, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '家常菜馆', 'contact_name' => '冯老板', 'contact_phone' => '15800000003', 'address' => '安徽省宿州市埇桥区汴河路56号', 'min_order_amount' => 0, 'settlement_type' => 1, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '鑫鑫小吃店', 'contact_name' => '蒋老板', 'contact_phone' => '15800000004', 'address' => '安徽省宿州市埇桥区银河一路32号', 'min_order_amount' => 0, 'settlement_type' => 1, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '老街坊饭店', 'contact_name' => '韩老板', 'contact_phone' => '15800000005', 'address' => '安徽省宿州市埇桥区胜利路18号', 'min_order_amount' => 0, 'settlement_type' => 1, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
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
                    'credit_limit' => 5000000,
                    'approval_status' => 2,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    protected function seedDeliveryRoutes(): void
    {
        $now = now();
        $routes = [
            ['name' => '城区北线', 'description' => '人民路-淮海路-汴河路北侧', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '城区南线', 'description' => '银河路-胜利路-宿怀路南侧', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
        ];

        foreach ($routes as $route) {
            if (! DB::table('delivery_routes')->where('name', $route['name'])->exists()) {
                DB::table('delivery_routes')->insert($route);
            }
        }
    }

    protected function seedDrivers(): void
    {
        $now = now();
        $drivers = [
            ['name' => '周师傅', 'phone' => '13700000001', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '马师傅', 'phone' => '13700000002', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
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
            ['plate_number' => '皖LT0001', 'vehicle_type' => 1, 'is_cold_chain' => 0, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['plate_number' => '皖LT0002', 'vehicle_type' => 1, 'is_cold_chain' => 0, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
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
            DB::table('driver_vehicles')->insert(['driver_id' => $driver1->id, 'vehicle_id' => $vehicle1->id, 'created_at' => $now, 'updated_at' => $now]);
        }
        if ($driver2 && $vehicle2 && ! DB::table('driver_vehicles')->where('driver_id', $driver2->id)->where('vehicle_id', $vehicle2->id)->exists()) {
            DB::table('driver_vehicles')->insert(['driver_id' => $driver2->id, 'vehicle_id' => $vehicle2->id, 'created_at' => $now, 'updated_at' => $now]);
        }
    }
}
