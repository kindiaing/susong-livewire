<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 先确保内置数据已就位
        $this->call(SystemDataSeeder::class);

        // 创建各角色测试用户
        $this->seedTestUsers();

        // 创建基础业务数据
        $this->seedSuppliers();
        $this->seedMerchants();
        $this->seedDeliveryRoutes();
        $this->seedDrivers();
        $this->seedVehicles();
        $this->seedCategories();
        $this->seedProducts();
        $this->seedWarehouses();
    }

    protected function seedTestUsers(): void
    {
        $now = now();
        $users = [
            ['username' => 'operator1', 'password' => Hash::make('Password'), 'name' => '张运营', 'phone' => '13800000001', 'email' => 'operator@susong.test', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['username' => 'ops_manager', 'password' => Hash::make('Password'), 'name' => '李运营经理', 'phone' => '13800000002', 'email' => 'ops_manager@susong.test', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['username' => 'finance1', 'password' => Hash::make('Password'), 'name' => '王财务', 'phone' => '13800000003', 'email' => 'finance@susong.test', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['username' => 'cashier1', 'password' => Hash::make('Password'), 'name' => '赵出纳', 'phone' => '13800000004', 'email' => 'cashier@susong.test', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['username' => 'fin_manager', 'password' => Hash::make('Password'), 'name' => '钱财务经理', 'phone' => '13800000005', 'email' => 'finance_manager@susong.test', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['username' => 'picker1', 'password' => Hash::make('Password'), 'name' => '孙拣货员', 'phone' => '13800000006', 'email' => 'picker@susong.test', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['username' => 'driver1', 'password' => Hash::make('Password'), 'name' => '周司机', 'phone' => '13800000007', 'email' => 'driver@susong.test', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['username' => 'merchant1', 'password' => Hash::make('Password'), 'name' => '吴商家', 'phone' => '13800000008', 'email' => 'merchant@susong.test', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
        ];

        foreach ($users as $userData) {
            if (DB::table('users')->where('username', $userData['username'])->exists()) {
                continue;
            }
            DB::table('users')->insert($userData);
        }

        // 分配角色
        $roleMappings = [
            'operator1' => 'operator',
            'ops_manager' => 'operator_manager',
            'finance1' => 'finance',
            'cashier1' => 'cashier',
            'fin_manager' => 'finance_manager',
            'picker1' => 'picker',
            'driver1' => 'driver',
            'merchant1' => 'merchant',
        ];

        foreach ($roleMappings as $username => $roleName) {
            $user = DB::table('users')->where('username', $username)->first();
            $role = DB::table('roles')->where('name', $roleName)->first();
            if ($user && $role) {
                DB::table('model_has_roles')->updateOrInsert(
                    ['role_id' => $role->id, 'model_type' => 'App\\Models\\User', 'model_id' => $user->id],
                    ['role_id' => $role->id, 'model_type' => 'App\\Models\\User', 'model_id' => $user->id]
                );
            }
        }
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
                    'credit_limit' => 5000000, // 500元
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

    protected function seedCategories(): void
    {
        $now = now();
        $categories = [
            ['name' => '蔬菜', 'sort' => 1, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '水果', 'sort' => 2, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '肉类', 'sort' => 3, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '水产', 'sort' => 4, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '粮油', 'sort' => 5, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '调料', 'sort' => 6, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '豆制品', 'sort' => 7, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '冷冻食品', 'sort' => 8, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
        ];

        foreach ($categories as $category) {
            if (! DB::table('categories')->where('name', $category['name'])->exists()) {
                DB::table('categories')->insert($category);
            }
        }
    }

    protected function seedProducts(): void
    {
        $now = now();

        // 简单商品示例（products表无type字段，unit是商品级别）
        $products = [
            ['category_id' => DB::table('categories')->where('name', '蔬菜')->value('id'), 'name' => '大白菜', 'unit' => '斤', 'is_weight_priced' => 1, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => DB::table('categories')->where('name', '蔬菜')->value('id'), 'name' => '土豆', 'unit' => '斤', 'is_weight_priced' => 1, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => DB::table('categories')->where('name', '蔬菜')->value('id'), 'name' => '西红柿', 'unit' => '斤', 'is_weight_priced' => 1, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => DB::table('categories')->where('name', '肉类')->value('id'), 'name' => '五花肉', 'unit' => '斤', 'is_weight_priced' => 1, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => DB::table('categories')->where('name', '水产')->value('id'), 'name' => '鲜虾', 'unit' => '斤', 'is_weight_priced' => 1, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => DB::table('categories')->where('name', '粮油')->value('id'), 'name' => '金龙鱼大豆油', 'unit' => '桶', 'is_weight_priced' => 0, 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
        ];

        foreach ($products as $product) {
            if (! empty($product['category_id']) && ! DB::table('products')->where('name', $product['name'])->exists()) {
                $productId = DB::table('products')->insertGetId($product);

                // 为每个商品创建一个默认SKU（skus表无name/unit字段）
                $name = $product['name'];
                $purchasePrice = match($name) {
                    '大白菜' => 8000, '土豆' => 12000, '西红柿' => 25000,
                    '五花肉' => 130000, '鲜虾' => 350000,
                    '金龙鱼大豆油' => 450000,
                    default => 10000,
                };
                $wholesalePrice = (int)($purchasePrice * 1.15);

                DB::table('skus')->insert([
                    'product_id' => $productId,
                    'sku_code' => 'SKU-' . str_pad($productId, 4, '0', STR_PAD_LEFT),
                    'specs' => json_encode([['label' => '规格', 'value' => $product['unit']]]),
                    'purchase_price' => $purchasePrice,
                    'wholesale_price' => $wholesalePrice,
                    'cost_price' => 0,
                    'stock' => 0,
                    'status' => 1,
                    'approval_status' => 2,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
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
}
