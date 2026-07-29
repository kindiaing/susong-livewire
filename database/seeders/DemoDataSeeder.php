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

        // ---- V1.5.0 扩展示例数据 ----
        $this->seedSkuSuppliers();
        $this->seedSkuBarcodes();
        $this->seedMerchantAddresses();
        $this->seedInventories();
        $this->seedInventoryLogs();
        $this->seedPurchaseOrders();
        $this->seedPurchaseItems();
        $this->seedCarts();
        $this->seedOrders();
        $this->seedFrequentlyBought();
        $this->seedRepurchaseTemplates();
        $this->seedMerchantFavorites();
        $this->seedPickingTasks();
        $this->seedDeliveryTasks();
        $this->seedSignatures();
        $this->seedTemperatures();
        $this->seedDiscrepancies();
        $this->seedLossOrders();
        $this->seedRecharges();
        $this->seedSupplierSettlements();
        $this->seedReceivables();
        $this->seedInvoices();
        $this->seedPurchaseReturns();
        $this->seedOrderReturns();
        $this->seedPriceStrategies();
        $this->seedPriceChangeLogs();
        $this->seedPriceApportionments();
        $this->seedCorrectionAuthorizations();
        $this->seedBanners();
        $this->seedPromotions();
        $this->seedKeywords();
        $this->seedRestockReminders();
        $this->seedLoginLogs();
        $this->seedWechatUsers();
    }

    // ========== 原有基础数据 ==========

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

    // ========== V1.5.0 扩展示例数据 ==========

    /** 一品多供：为每个SKU绑定默认供应商 */
    protected function seedSkuSuppliers(): void
    {
        $now = now();
        $supplierGreen = DB::table('suppliers')->where('name', '绿野蔬菜种植基地')->first();
        $supplierMeat = DB::table('suppliers')->where('name', '丰润肉业有限公司')->first();
        $supplierSeafood = DB::table('suppliers')->where('name', '海滨水产批发部')->first();
        $supplierGrain = DB::table('suppliers')->where('name', '恒达粮油贸易公司')->first();
        $supplierXianyuan = DB::table('suppliers')->where('name', '鲜源农业有限公司')->first();

        $skuSupplierMap = [
            '大白菜' => [$supplierGreen, 8000],
            '土豆'   => [$supplierGreen, 12000],
            '西红柿' => [$supplierXianyuan, 25000],
            '五花肉' => [$supplierMeat, 130000],
            '鲜虾'   => [$supplierSeafood, 350000],
            '金龙鱼大豆油' => [$supplierGrain, 450000],
        ];

        foreach ($skuSupplierMap as $productName => [$supplier, $price]) {
            if (! $supplier) continue;
            $sku = DB::table('skus')->where('sku_code', 'like', 'SKU-%')->whereHas // 简化：通过product找sku
                ?? null;
            $product = DB::table('products')->where('name', $productName)->first();
            if (! $product) continue;
            $sku = DB::table('skus')->where('product_id', $product->id)->first();
            if (! $sku) continue;

            if (! DB::table('sku_suppliers')->where('sku_id', $sku->id)->where('supplier_id', $supplier->id)->exists()) {
                DB::table('sku_suppliers')->insert([
                    'sku_id' => $sku->id,
                    'supplier_id' => $supplier->id,
                    'is_default' => 1,
                    'purchase_price' => $price,
                    'status' => 1,
                    'sort' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    /** SKU条码 */
    protected function seedSkuBarcodes(): void
    {
        $now = now();
        $barcodes = [
            ['product' => '大白菜',   'supplier' => '绿野蔬菜种植基地', 'barcode_type' => 3, 'barcode_code' => '6901234500001'],
            ['product' => '土豆',     'supplier' => '绿野蔬菜种植基地', 'barcode_type' => 3, 'barcode_code' => '6901234500002'],
            ['product' => '西红柿',   'supplier' => '鲜源农业有限公司', 'barcode_type' => 1, 'barcode_code' => '6901234500003'],
            ['product' => '五花肉',   'supplier' => '丰润肉业有限公司', 'barcode_type' => 1, 'barcode_code' => '6901234500004'],
            ['product' => '鲜虾',     'supplier' => '海滨水产批发部',   'barcode_type' => 1, 'barcode_code' => '6901234500005'],
            ['product' => '金龙鱼大豆油', 'supplier' => '恒达粮油贸易公司', 'barcode_type' => 1, 'barcode_code' => '6901234500006'],
        ];

        foreach ($barcodes as $item) {
            $product = DB::table('products')->where('name', $item['product'])->first();
            $supplier = DB::table('suppliers')->where('name', $item['supplier'])->first();
            if (! $product || ! $supplier) continue;
            $sku = DB::table('skus')->where('product_id', $product->id)->first();
            if (! $sku) continue;

            if (! DB::table('sku_barcodes')->where('sku_id', $sku->id)->where('barcode_code', $item['barcode_code'])->exists()) {
                DB::table('sku_barcodes')->insert([
                    'sku_id' => $sku->id,
                    'supplier_id' => $supplier->id,
                    'barcode_type' => $item['barcode_type'],
                    'barcode_code' => $item['barcode_code'],
                    'is_default' => 1,
                    'is_enabled' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    /** 商家收货地址 */
    protected function seedMerchantAddresses(): void
    {
        $now = now();
        $addresses = [
            ['merchant' => '味之初餐饮店', 'contact_name' => '吴老板', 'contact_phone' => '15800000001', 'address' => '安徽省宿州市埇桥区人民路88号', 'is_default' => 1],
            ['merchant' => '鲜之味快餐店', 'contact_name' => '郑老板', 'contact_phone' => '15800000002', 'address' => '安徽省宿州市埇桥区淮海路120号', 'is_default' => 1],
            ['merchant' => '家常菜馆',   'contact_name' => '冯老板', 'contact_phone' => '15800000003', 'address' => '安徽省宿州市埇桥区汴河路56号', 'is_default' => 1],
            ['merchant' => '鑫鑫小吃店', 'contact_name' => '蒋老板', 'contact_phone' => '15800000004', 'address' => '安徽省宿州市埇桥区银河一路32号', 'is_default' => 1],
            ['merchant' => '老街坊饭店', 'contact_name' => '韩老板', 'contact_phone' => '15800000005', 'address' => '安徽省宿州市埇桥区胜利路18号', 'is_default' => 1],
        ];

        foreach ($addresses as $item) {
            $merchant = DB::table('merchants')->where('name', $item['merchant'])->first();
            if (! $merchant) continue;
            if (! DB::table('merchant_addresses')->where('merchant_id', $merchant->id)->where('address', $item['address'])->exists()) {
                DB::table('merchant_addresses')->insert([
                    'merchant_id' => $merchant->id,
                    'contact_name' => $item['contact_name'],
                    'contact_phone' => $item['contact_phone'],
                    'address' => $item['address'],
                    'is_default' => $item['is_default'],
                    'sort' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    /** 库存数据 */
    protected function seedInventories(): void
    {
        $now = now();
        $warehouse1 = DB::table('warehouses')->where('name', '总仓-农批市场')->first();
        $warehouse2 = DB::table('warehouses')->where('name', '分仓-肉联厂')->first();
        if (! $warehouse1 || ! $warehouse2) return;

        $stockData = [
            // [productName, warehouse, totalStock, warningValue, batchNo]
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
                    'warehouse_id' => $warehouseId,
                    'sku_id' => $sku->id,
                    'total_stock' => $totalStock,
                    'locked_stock' => 0,
                    'available_stock' => $totalStock,
                    'batch_no' => $batchNo,
                    'warning_value' => $warningValue,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    /** 库存变动日志 */
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

    /** 采购单 + 明细 */
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

            // 采购明细
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

    /** 待采清单 */
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

    /** 购物车 */
    protected function seedCarts(): void
    {
        $now = now();
        $merchant1 = DB::table('merchants')->where('name', '味之初餐饮店')->first();
        $merchant2 = DB::table('merchants')->where('name', '鲜之味快餐店')->first();

        // 商家1的购物车
        if ($merchant1 && ! DB::table('carts')->where('merchant_id', $merchant1->id)->exists()) {
            $cartId = DB::table('carts')->insertGetId([
                'merchant_id' => $merchant1->id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $sku1 = DB::table('skus')->where('sku_code', 'SKU-0001')->first();
            $sku2 = DB::table('skus')->where('sku_code', 'SKU-0002')->first();
            if ($sku1) DB::table('cart_items')->insert(['cart_id' => $cartId, 'sku_id' => $sku1->id, 'quantity' => 2000, 'price' => 9200, 'created_at' => $now, 'updated_at' => $now]);
            if ($sku2) DB::table('cart_items')->insert(['cart_id' => $cartId, 'sku_id' => $sku2->id, 'quantity' => 1000, 'price' => 13800, 'created_at' => $now, 'updated_at' => $now]);
        }

        // 商家2的购物车
        if ($merchant2 && ! DB::table('carts')->where('merchant_id', $merchant2->id)->exists()) {
            $cartId = DB::table('carts')->insertGetId([
                'merchant_id' => $merchant2->id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $sku4 = DB::table('skus')->where('sku_code', 'SKU-0004')->first();
            if ($sku4) DB::table('cart_items')->insert(['cart_id' => $cartId, 'sku_id' => $sku4->id, 'quantity' => 500, 'price' => 149500, 'created_at' => $now, 'updated_at' => $now]);
        }
    }

    /** 订单 + 订单明细 */
    protected function seedOrders(): void
    {
        $now = now();
        $merchant1 = DB::table('merchants')->where('name', '味之初餐饮店')->first();
        $merchant2 = DB::table('merchants')->where('name', '家常菜馆')->first();
        $merchant3 = DB::table('merchants')->where('name', '鲜之味快餐店')->first();
        $route1 = DB::table('delivery_routes')->where('name', '城区北线')->first();

        $orders = [
            [
                'order_no' => 'ORD-20260728-001',
                'merchant' => $merchant1,
                'route' => $route1,
                'batch' => 1,
                'delivery_address' => '安徽省宿州市埇桥区人民路88号',
                'contact_name' => '吴老板',
                'contact_phone' => '15800000001',
                'status' => 4,
                'total_amount' => 23000,
                'adjusted_amount' => 23000,
                'final_amount' => 23000,
                'payment_status' => 2,
                'settlement_type' => 1,
                'items' => [
                    ['product' => '大白菜', 'quantity' => 2000, 'price' => 9200, 'actual_quantity' => 2100, 'actual_price' => 9200],
                    ['product' => '土豆',   'quantity' => 500,  'price' => 13800, 'actual_quantity' => 480, 'actual_price' => 13800],
                ],
            ],
            [
                'order_no' => 'ORD-20260728-002',
                'merchant' => $merchant2,
                'route' => $route1,
                'batch' => 1,
                'delivery_address' => '安徽省宿州市埇桥区汴河路56号',
                'contact_name' => '冯老板',
                'contact_phone' => '15800000003',
                'status' => 2,
                'total_amount' => 74500,
                'adjusted_amount' => 74500,
                'final_amount' => 0,
                'payment_status' => 1,
                'settlement_type' => 2,
                'items' => [
                    ['product' => '五花肉', 'quantity' => 500, 'price' => 149000, 'actual_quantity' => 0, 'actual_price' => 0],
                ],
            ],
            [
                'order_no' => 'ORD-20260729-001',
                'merchant' => $merchant3,
                'route' => $route1,
                'batch' => 2,
                'delivery_address' => '安徽省宿州市埇桥区淮海路120号',
                'contact_name' => '郑老板',
                'contact_phone' => '15800000002',
                'status' => 1,
                'total_amount' => 4025000,
                'adjusted_amount' => 4025000,
                'final_amount' => 0,
                'payment_status' => 1,
                'settlement_type' => 3,
                'items' => [
                    ['product' => '鲜虾', 'quantity' => 1000, 'price' => 4025000, 'actual_quantity' => 0, 'actual_price' => 0],
                ],
            ],
        ];

        foreach ($orders as $orderData) {
            if (! $orderData['merchant']) continue;
            if (DB::table('orders')->where('order_no', $orderData['order_no'])->exists()) continue;

            $orderId = DB::table('orders')->insertGetId([
                'order_no' => $orderData['order_no'],
                'merchant_id' => $orderData['merchant']->id,
                'delivery_route_id' => $orderData['route']?->id,
                'batch' => $orderData['batch'],
                'delivery_address' => $orderData['delivery_address'],
                'contact_name' => $orderData['contact_name'],
                'contact_phone' => $orderData['contact_phone'],
                'status' => $orderData['status'],
                'total_amount' => $orderData['total_amount'],
                'adjusted_amount' => $orderData['adjusted_amount'],
                'final_amount' => $orderData['final_amount'],
                'payment_status' => $orderData['payment_status'],
                'settlement_type' => $orderData['settlement_type'],
                'is_locked' => 0,
                'remark' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($orderData['items'] as $item) {
                $product = DB::table('products')->where('name', $item['product'])->first();
                if (! $product) continue;
                $sku = DB::table('skus')->where('product_id', $product->id)->first();
                if (! $sku) continue;

                $subtotal = $item['quantity'] * $item['price'];
                $actualSubtotal = $item['actual_quantity'] * $item['actual_price'];

                DB::table('order_items')->insert([
                    'order_id' => $orderId,
                    'sku_id' => $sku->id,
                    'product_name' => $item['product'],
                    'sku_specs' => $sku->specs,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'actual_quantity' => $item['actual_quantity'],
                    'actual_price' => $item['actual_price'],
                    'subtotal' => $subtotal,
                    'actual_subtotal' => $actualSubtotal,
                    'strategy_price' => 0,
                    'strategy_amount' => 0,
                    'discrepancy_amount' => abs($subtotal - $actualSubtotal),
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    /** 常购清单 */
    protected function seedFrequentlyBought(): void
    {
        $now = now();
        $merchant1 = DB::table('merchants')->where('name', '味之初餐饮店')->first();
        if (! $merchant1) return;

        $frequentlyItems = [
            ['product' => '大白菜', 'buy_count' => 15],
            ['product' => '土豆',   'buy_count' => 12],
            ['product' => '西红柿', 'buy_count' => 8],
        ];

        foreach ($frequentlyItems as $item) {
            $product = DB::table('products')->where('name', $item['product'])->first();
            if (! $product) continue;
            $sku = DB::table('skus')->where('product_id', $product->id)->first();
            if (! $sku) continue;

            if (! DB::table('frequently_bought')->where('merchant_id', $merchant1->id)->where('sku_id', $sku->id)->exists()) {
                DB::table('frequently_bought')->insert([
                    'merchant_id' => $merchant1->id,
                    'sku_id' => $sku->id,
                    'buy_count' => $item['buy_count'],
                    'last_buy_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    /** 复购模板 */
    protected function seedRepurchaseTemplates(): void
    {
        $now = now();
        $merchant1 = DB::table('merchants')->where('name', '味之初餐饮店')->first();
        if (! $merchant1) return;

        if (! DB::table('repurchase_templates')->where('merchant_id', $merchant1->id)->where('name', '日常蔬菜采购')->exists()) {
            $templateId = DB::table('repurchase_templates')->insertGetId([
                'merchant_id' => $merchant1->id,
                'name' => '日常蔬菜采购',
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $items = [
                ['product' => '大白菜', 'quantity' => 2000],
                ['product' => '土豆',   'quantity' => 1000],
                ['product' => '西红柿', 'quantity' => 500],
            ];
            foreach ($items as $item) {
                $product = DB::table('products')->where('name', $item['product'])->first();
                if (! $product) continue;
                $sku = DB::table('skus')->where('product_id', $product->id)->first();
                if (! $sku) continue;

                DB::table('repurchase_template_items')->insert([
                    'template_id' => $templateId,
                    'sku_id' => $sku->id,
                    'quantity' => $item['quantity'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    /** 商家收藏 */
    protected function seedMerchantFavorites(): void
    {
        $now = now();
        $merchant1 = DB::table('merchants')->where('name', '味之初餐饮店')->first();
        if (! $merchant1) return;

        $favorites = ['大白菜', '五花肉', '鲜虾'];
        foreach ($favorites as $productName) {
            $product = DB::table('products')->where('name', $productName)->first();
            if (! $product) continue;
            $sku = DB::table('skus')->where('product_id', $product->id)->first();
            if (! $sku) continue;

            if (! DB::table('merchant_favorites')->where('merchant_id', $merchant1->id)->where('sku_id', $sku->id)->exists()) {
                DB::table('merchant_favorites')->insert([
                    'merchant_id' => $merchant1->id,
                    'sku_id' => $sku->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    /** 拣货任务 */
    protected function seedPickingTasks(): void
    {
        $now = now();
        $warehouse1 = DB::table('warehouses')->where('name', '总仓-农批市场')->first();
        $pickerUser = DB::table('users')->where('username', 'picker1')->first();
        $order1 = DB::table('orders')->where('order_no', 'ORD-20260728-001')->first();

        if ($warehouse1 && ! DB::table('picking_tasks')->where('task_no', 'PK-20260728-001')->exists()) {
            DB::table('picking_tasks')->insert([
                'task_no' => 'PK-20260728-001',
                'warehouse_id' => $warehouse1->id,
                'picker_id' => $pickerUser?->id,
                'batch' => 1,
                'status' => 3,
                'started_at' => $now,
                'completed_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /** 配送任务 + 配送订单关联 + 配送轨迹 */
    protected function seedDeliveryTasks(): void
    {
        $now = now();
        $route1 = DB::table('delivery_routes')->where('name', '城区北线')->first();
        $driver1 = DB::table('drivers')->where('phone', '13700000001')->first();
        $vehicle1 = DB::table('vehicles')->where('plate_number', '皖LT0001')->first();
        $order1 = DB::table('orders')->where('order_no', 'ORD-20260728-001')->first();

        if ($route1 && ! DB::table('delivery_tasks')->where('task_no', 'DT-20260728-001')->exists()) {
            $taskId = DB::table('delivery_tasks')->insertGetId([
                'task_no' => 'DT-20260728-001',
                'delivery_route_id' => $route1->id,
                'driver_id' => $driver1?->id,
                'vehicle_id' => $vehicle1?->id,
                'batch' => 1,
                'status' => 3,
                'planned_at' => $now,
                'started_at' => $now,
                'completed_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // 配送订单关联
            if ($order1 && ! DB::table('delivery_task_orders')->where('delivery_task_id', $taskId)->where('order_id', $order1->id)->exists()) {
                DB::table('delivery_task_orders')->insert([
                    'delivery_task_id' => $taskId,
                    'order_id' => $order1->id,
                    'delivery_sort' => 1,
                    'status' => 2,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            // 配送轨迹
            if ($driver1 && ! DB::table('delivery_tracks')->where('delivery_task_id', $taskId)->exists()) {
                DB::table('delivery_tracks')->insert([
                    ['delivery_task_id' => $taskId, 'driver_id' => $driver1->id, 'latitude' => 33720000, 'longitude' => 116970000, 'location_desc' => '农批市场出发', 'reported_at' => $now, 'created_at' => $now],
                    ['delivery_task_id' => $taskId, 'driver_id' => $driver1->id, 'latitude' => 33721000, 'longitude' => 116975000, 'location_desc' => '人民路中段', 'reported_at' => $now, 'created_at' => $now],
                ]);
            }
        }
    }

    /** 签收存证 */
    protected function seedSignatures(): void
    {
        $now = now();
        $order1 = DB::table('orders')->where('order_no', 'ORD-20260728-001')->first();
        $task = DB::table('delivery_tasks')->where('task_no', 'DT-20260728-001')->first();

        if ($order1 && $task && ! DB::table('signatures')->where('order_id', $order1->id)->exists()) {
            DB::table('signatures')->insert([
                'order_id' => $order1->id,
                'delivery_task_id' => $task->id,
                'type' => 1,
                'image_url' => '/uploads/signatures/demo-sign-001.jpg',
                'signer_name' => '吴老板',
                'signed_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /** 冷链温度记录 */
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

    /** 差异单 */
    protected function seedDiscrepancies(): void
    {
        $now = now();
        $order1 = DB::table('orders')->where('order_no', 'ORD-20260728-001')->first();

        if ($order1 && ! DB::table('discrepancies')->where('order_id', $order1->id)->exists()) {
            $orderItem = DB::table('order_items')->where('order_id', $order1->id)->first();

            DB::table('discrepancies')->insert([
                'discrepancy_no' => 'DIS-20260728-001',
                'order_id' => $order1->id,
                'order_item_id' => $orderItem?->id,
                'stage' => 2,
                'type' => 1,
                'expected_quantity' => 500,
                'actual_quantity' => 480,
                'quantity_diff' => -20,
                'amount_diff' => -13800,
                'reason' => '运输途中少件',
                'responsible_party' => 1,
                'decision' => 2,
                'status' => 3,
                'handled_at' => $now,
                'is_amount_adjusted' => 1,
                'approval_status' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /** 损耗单 + 明细 */
    protected function seedLossOrders(): void
    {
        $now = now();
        $warehouse1 = DB::table('warehouses')->where('name', '总仓-农批市场')->first();
        $operatorUser = DB::table('users')->where('username', 'operator1')->first();

        if ($warehouse1 && ! DB::table('loss_orders')->where('loss_no', 'LOSS-20260728-001')->exists()) {
            $lossId = DB::table('loss_orders')->insertGetId([
                'loss_no' => 'LOSS-20260728-001',
                'warehouse_id' => $warehouse1->id,
                'total_amount' => 6400,
                'loss_type' => 2,
                'status' => 3,
                'approval_status' => 2,
                'applicant_id' => $operatorUser?->id,
                'reviewer_id' => $operatorUser?->id,
                'reviewed_at' => $now,
                'executed_at' => $now,
                'reason' => '蔬菜称重失水损耗',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $product = DB::table('products')->where('name', '大白菜')->first();
            if ($product) {
                $sku = DB::table('skus')->where('product_id', $product->id)->first();
                if ($sku) DB::table('loss_order_items')->insert([
                    'loss_order_id' => $lossId,
                    'sku_id' => $sku->id,
                    'loss_type' => 2,
                    'quantity' => 800,
                    'cost_price' => 8000,
                    'loss_amount' => 6400,
                    'responsible_party' => 1,
                    'reason' => '失水减重',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    /** 充值记录 */
    protected function seedRecharges(): void
    {
        $now = now();
        $operatorUser = DB::table('users')->where('username', 'operator1')->first();

        $recharges = [
            ['merchant' => '味之初餐饮店', 'amount' => 5000000, 'payment_method' => 3, 'transaction_no' => 'RCH-20260720-001', 'status' => 2, 'approval_status' => 2],
            ['merchant' => '家常菜馆',    'amount' => 3000000, 'payment_method' => 2, 'transaction_no' => 'RCH-20260722-001', 'status' => 2, 'approval_status' => 2],
            ['merchant' => '鲜之味快餐店', 'amount' => 10000000, 'payment_method' => 3, 'transaction_no' => 'RCH-20260725-001', 'status' => 1, 'approval_status' => 1],
        ];

        foreach ($recharges as $item) {
            $merchant = DB::table('merchants')->where('name', $item['merchant'])->first();
            if (! $merchant) continue;
            if (DB::table('recharges')->where('transaction_no', $item['transaction_no'])->exists()) continue;

            DB::table('recharges')->insert([
                'merchant_id' => $merchant->id,
                'amount' => $item['amount'],
                'payment_method' => $item['payment_method'],
                'transaction_no' => $item['transaction_no'],
                'status' => $item['status'],
                'approval_status' => $item['approval_status'],
                'operator_id' => $operatorUser?->id,
                'remark' => '示例充值',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /** 供应商结算 + 明细 + 付款 */
    protected function seedSupplierSettlements(): void
    {
        $now = now();
        $supplierGreen = DB::table('suppliers')->where('name', '绿野蔬菜种植基地')->first();
        $supplierMeat = DB::table('suppliers')->where('name', '丰润肉业有限公司')->first();
        $po1 = DB::table('purchase_orders')->where('order_no', 'PO-20260725-001')->first();

        // 结算单1：绿野蔬菜 - 已结清
        if ($supplierGreen && ! DB::table('supplier_settlements')->where('settlement_no', 'SS-20260728-001')->exists()) {
            $settlementId = DB::table('supplier_settlements')->insertGetId([
                'settlement_no' => 'SS-20260728-001',
                'supplier_id' => $supplierGreen->id,
                'start_date' => '2026-07-01',
                'end_date' => '2026-07-28',
                'total_amount' => 440000,
                'service_fee' => 5000,
                'payable_amount' => 435000,
                'return_amount' => 0,
                'paid_amount' => 435000,
                'status' => 3,
                'settled_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            if ($po1) {
                DB::table('supplier_settlement_items')->insert([
                    'supplier_settlement_id' => $settlementId,
                    'purchase_order_id' => $po1->id,
                    'amount' => 440000,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                // 付款记录
                DB::table('settlement_payments')->insert([
                    'settlement_id' => $settlementId,
                    'amount' => 435000,
                    'payment_method' => 1,
                    'transaction_no' => 'PAY-20260728-001',
                    'operator_id' => DB::table('users')->where('username', 'cashier1')->value('id'),
                    'approval_status' => 2,
                    'remark' => '银行转账付款',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // 结算单2：丰润肉业 - 待结算
        if ($supplierMeat && ! DB::table('supplier_settlements')->where('settlement_no', 'SS-20260728-002')->exists()) {
            DB::table('supplier_settlements')->insert([
                'settlement_no' => 'SS-20260728-002',
                'supplier_id' => $supplierMeat->id,
                'start_date' => '2026-07-01',
                'end_date' => '2026-07-28',
                'total_amount' => 260000,
                'service_fee' => 3000,
                'payable_amount' => 257000,
                'return_amount' => 0,
                'paid_amount' => 0,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /** 应收账款 + 收款记录 */
    protected function seedReceivables(): void
    {
        $now = now();
        $order1 = DB::table('orders')->where('order_no', 'ORD-20260728-001')->first();
        $order2 = DB::table('orders')->where('order_no', 'ORD-20260728-002')->first();

        if ($order1 && ! DB::table('receivables')->where('receivable_no', 'RCV-20260728-001')->exists()) {
            $rcvId = DB::table('receivables')->insertGetId([
                'receivable_no' => 'RCV-20260728-001',
                'order_id' => $order1->id,
                'merchant_id' => $order1->merchant_id,
                'original_amount' => 23000,
                'adjusted_amount' => 23000,
                'discrepancy_amount' => 0,
                'return_amount' => 0,
                'strategy_discount_amount' => 0,
                'received_amount' => 23000,
                'status' => 3,
                'settlement_type' => 1,
                'settled_at' => $now,
                'approval_status' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // 收款记录
            DB::table('receivable_payments')->insert([
                'receivable_id' => $rcvId,
                'amount' => 23000,
                'payment_method' => 1,
                'transaction_no' => 'RP-20260728-001',
                'operator_id' => DB::table('users')->where('username', 'cashier1')->value('id'),
                'approval_status' => 2,
                'remark' => '余额扣款',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        if ($order2 && ! DB::table('receivables')->where('receivable_no', 'RCV-20260728-002')->exists()) {
            DB::table('receivables')->insert([
                'receivable_no' => 'RCV-20260728-002',
                'order_id' => $order2->id,
                'merchant_id' => $order2->merchant_id,
                'original_amount' => 74500,
                'adjusted_amount' => 74500,
                'discrepancy_amount' => 0,
                'return_amount' => 0,
                'strategy_discount_amount' => 0,
                'received_amount' => 0,
                'status' => 1,
                'settlement_type' => 2,
                'due_date' => now()->addDays(15)->toDateString(),
                'approval_status' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /** 发票管理 */
    protected function seedInvoices(): void
    {
        $now = now();
        $merchant1 = DB::table('merchants')->where('name', '味之初餐饮店')->first();

        if ($merchant1 && ! DB::table('invoices')->where('invoice_no', 'INV-20260728-001')->exists()) {
            DB::table('invoices')->insert([
                'invoice_no' => 'INV-20260728-001',
                'type' => 1,
                'target_id' => $merchant1->id,
                'title' => '味之初餐饮店',
                'amount' => 23000,
                'file_url' => '/uploads/invoices/demo-inv-001.pdf',
                'status' => 2,
                'applied_at' => $now,
                'issued_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /** 采购退货 + 明细 */
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

    /** 售后退货 + 明细 */
    protected function seedOrderReturns(): void
    {
        $now = now();
        $order1 = DB::table('orders')->where('order_no', 'ORD-20260728-001')->first();

        if ($order1 && ! DB::table('order_returns')->where('return_no', 'OR-20260729-001')->exists()) {
            $returnId = DB::table('order_returns')->insertGetId([
                'return_no' => 'OR-20260729-001',
                'order_id' => $order1->id,
                'merchant_id' => $order1->merchant_id,
                'status' => 1,
                'total_amount' => 6900,
                'refund_amount' => 0,
                'reason' => '土豆质量问题退货',
                'operator_id' => DB::table('users')->where('username', 'operator1')->value('id'),
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $orderItem = DB::table('order_items')->where('order_id', $order1->id)->skip(1)->first();
            if ($orderItem) {
                DB::table('order_return_items')->insert([
                    'order_return_id' => $returnId,
                    'order_item_id' => $orderItem->id,
                    'sku_id' => $orderItem->sku_id,
                    'quantity' => 500,
                    'price' => 13800,
                    'amount' => 6900000,
                    'refund_amount' => 0,
                    'reason' => '品质不达标',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    /** 价格策略 + 明细 */
    protected function seedPriceStrategies(): void
    {
        $now = now();
        $merchant1 = DB::table('merchants')->where('name', '味之初餐饮店')->first();

        if (! DB::table('price_strategies')->where('code', 'PS-VIP-001')->exists()) {
            $strategyId = DB::table('price_strategies')->insertGetId([
                'name' => '老客户蔬菜优惠',
                'code' => 'PS-VIP-001',
                'type' => 1,
                'target_type' => 2,
                'scope' => 2,
                'status' => 1,
                'approval_status' => 2,
                'start_at' => $now,
                'end_at' => now()->addMonths(3),
                'created_by' => DB::table('users')->where('username', 'operator1')->value('id'),
                'remark' => '老客户蔬菜类9折优惠',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $vegCategory = DB::table('categories')->where('name', '蔬菜')->first();
            if ($vegCategory) {
                DB::table('price_strategy_items')->insert([
                    'price_strategy_id' => $strategyId,
                    'target_id' => $merchant1?->id ?? 0,
                    'category_id' => $vegCategory->id,
                    'product_id' => null,
                    'sku_id' => null,
                    'price_type' => 2,
                    'price_value' => 0,
                    'discount_rate' => 9000,
                    'cost_weight_rate' => 10000,
                    'min_quantity' => 0,
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    /** 改价记录 */
    protected function seedPriceChangeLogs(): void
    {
        $now = now();
        $order1 = DB::table('orders')->where('order_no', 'ORD-20260728-001')->first();
        $operatorUser = DB::table('users')->where('username', 'operator1')->first();

        if ($order1 && ! DB::table('price_change_logs')->where('target_type', 1)->where('target_id', $order1->id)->exists()) {
            DB::table('price_change_logs')->insert([
                'source_type' => 3,
                'source_id' => null,
                'target_type' => 1,
                'target_id' => $order1->id,
                'target_item_id' => null,
                'original_price' => 9200,
                'new_price' => 9000,
                'quantity' => 2000,
                'amount_diff' => -4000,
                'operator_id' => $operatorUser?->id,
                'reason' => '老客户蔬菜优惠折扣',
                'created_at' => $now,
            ]);
        }
    }

    /** 费用均摊 */
    protected function seedPriceApportionments(): void
    {
        $now = now();
        $order1 = DB::table('orders')->where('order_no', 'ORD-20260728-001')->first();

        if ($order1 && ! DB::table('price_apportionments')->where('target_type', 1)->where('target_id', $order1->id)->exists()) {
            DB::table('price_apportionments')->insert([
                'target_type' => 1,
                'target_id' => $order1->id,
                'target_item_id' => null,
                'apportion_type' => 3,
                'amount' => 5000,
                'apportion_mode' => 1,
                'manual_amount' => 0,
                'operator_id' => DB::table('users')->where('username', 'operator1')->value('id'),
                'approval_status' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /** 授权更正 */
    protected function seedCorrectionAuthorizations(): void
    {
        $now = now();
        $order1 = DB::table('orders')->where('order_no', 'ORD-20260728-001')->first();
        $operatorUser = DB::table('users')->where('username', 'operator1')->first();

        if ($order1 && ! DB::table('correction_authorizations')->where('order_id', $order1->id)->exists()) {
            DB::table('correction_authorizations')->insert([
                'order_id' => $order1->id,
                'operator_id' => $operatorUser?->id ?? 1,
                'reason' => '订单金额需要调整',
                'before_data' => json_encode(['final_amount' => 23000]),
                'after_data' => json_encode(['final_amount' => 19000]),
                'authorized_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /** 轮播广告 */
    protected function seedBanners(): void
    {
        $now = now();
        $banners = [
            ['title' => '新鲜蔬菜每日直达', 'image_url' => '/uploads/banners/veg-fresh.jpg', 'link_url' => '/products?category=1', 'sort' => 1, 'status' => 1],
            ['title' => '海鲜专区限时特惠', 'image_url' => '/uploads/banners/seafood-sale.jpg', 'link_url' => '/products?category=4', 'sort' => 2, 'status' => 1],
            ['title' => '新用户充值满赠', 'image_url' => '/uploads/banners/recharge-gift.jpg', 'link_url' => '/recharges', 'sort' => 3, 'status' => 1],
        ];

        foreach ($banners as $banner) {
            if (! DB::table('banners')->where('title', $banner['title'])->exists()) {
                DB::table('banners')->insert(array_merge($banner, ['created_at' => $now, 'updated_at' => $now]));
            }
        }
    }

    /** 运营主推 */
    protected function seedPromotions(): void
    {
        $now = now();
        $product1 = DB::table('products')->where('name', '大白菜')->first();
        $vegCategory = DB::table('categories')->where('name', '蔬菜')->first();

        $promotions = [
            ['type' => 1, 'target_id' => $product1?->id ?? 0, 'sort' => 1, 'start_at' => $now, 'end_at' => now()->addMonths(1), 'status' => 1],
            ['type' => 2, 'target_id' => $vegCategory?->id ?? 0, 'sort' => 2, 'start_at' => $now, 'end_at' => now()->addMonths(1), 'status' => 1],
        ];

        foreach ($promotions as $promo) {
            if ($promo['target_id'] && ! DB::table('promotions')->where('type', $promo['type'])->where('target_id', $promo['target_id'])->exists()) {
                DB::table('promotions')->insert(array_merge($promo, ['created_at' => $now, 'updated_at' => $now]));
            }
        }
    }

    /** 搜索关键词 */
    protected function seedKeywords(): void
    {
        $now = now();
        $keywords = [
            ['keyword' => '白菜', 'search_count' => 128],
            ['keyword' => '土豆', 'search_count' => 95],
            ['keyword' => '五花肉', 'search_count' => 67],
            ['keyword' => '大豆油', 'search_count' => 42],
        ];

        foreach ($keywords as $item) {
            if (! DB::table('keywords')->where('keyword', $item['keyword'])->exists()) {
                DB::table('keywords')->insert([
                    'keyword' => $item['keyword'],
                    'product_id' => null,
                    'search_count' => $item['search_count'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    /** 补货提醒 */
    protected function seedRestockReminders(): void
    {
        $now = now();
        $merchant1 = DB::table('merchants')->where('name', '味之初餐饮店')->first();
        if (! $merchant1) return;

        $reminders = [
            ['product' => '大白菜', 'threshold_quantity' => 1000, 'remind_cycle' => 1],
            ['product' => '五花肉', 'threshold_quantity' => 200, 'remind_cycle' => 2],
            ['product' => '鲜虾',   'threshold_quantity' => 100, 'remind_cycle' => 1],
        ];

        foreach ($reminders as $item) {
            $product = DB::table('products')->where('name', $item['product'])->first();
            if (! $product) continue;
            $sku = DB::table('skus')->where('product_id', $product->id)->first();
            if (! $sku) continue;

            if (! DB::table('restock_reminders')->where('merchant_id', $merchant1->id)->where('sku_id', $sku->id)->exists()) {
                DB::table('restock_reminders')->insert([
                    'merchant_id' => $merchant1->id,
                    'sku_id' => $sku->id,
                    'threshold_quantity' => $item['threshold_quantity'],
                    'remind_cycle' => $item['remind_cycle'],
                    'last_reminded_at' => null,
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    /** 登录日志 */
    protected function seedLoginLogs(): void
    {
        $now = now();
        $users = DB::table('users')->limit(5)->get();

        $logs = [];
        foreach ($users as $user) {
            $logs[] = [
                'user_id' => $user->id,
                'username' => $user->username,
                'ip' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/126.0',
                'login_type' => 1,
                'status' => 1,
                'fail_reason' => null,
                'created_at' => $now,
            ];
        }
        // 一条失败日志
        $logs[] = [
            'user_id' => null,
            'username' => 'unknown',
            'ip' => '192.168.1.100',
            'user_agent' => 'Mozilla/5.0',
            'login_type' => 1,
            'status' => 0,
            'fail_reason' => '用户不存在',
            'created_at' => $now,
        ];

        DB::table('login_logs')->insert($logs);
    }

    /** 微信用户 */
    protected function seedWechatUsers(): void
    {
        $now = now();
        $merchantUser = DB::table('users')->where('username', 'merchant1')->first();
        $driverUser = DB::table('users')->where('username', 'driver1')->first();

        $wechatUsers = [
            ['user_id' => $merchantUser?->id, 'openid' => 'o_demo_merchant_001', 'unionid' => null, 'nickname' => '吴商家', 'avatar' => null, 'type' => 1, 'status' => 1],
            ['user_id' => $driverUser?->id, 'openid' => 'o_demo_driver_001', 'unionid' => null, 'nickname' => '周司机', 'avatar' => null, 'type' => 2, 'status' => 1],
        ];

        foreach ($wechatUsers as $item) {
            if (DB::table('wechat_users')->where('openid', $item['openid'])->exists()) continue;
            DB::table('wechat_users')->insert(array_merge($item, ['created_at' => $now, 'updated_at' => $now]));
        }
    }
}
