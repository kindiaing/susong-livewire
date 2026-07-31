<?php

namespace Database\Seeders\Demo;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * 订单管理测试数据 Seeder
 *
 * 包含：购物车、订单（含明细）、常购清单、复购模板、商家收货地址、售后退货（含明细）
 */
class OrderDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedMerchantAddresses();
        $this->seedCarts();
        $this->seedOrders();
        $this->seedFrequentlyBought();
        $this->seedRepurchaseTemplates();
        $this->seedOrderReturns();
    }

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

    protected function seedCarts(): void
    {
        $now = now();
        $merchant1 = DB::table('merchants')->where('name', '味之初餐饮店')->first();
        $merchant2 = DB::table('merchants')->where('name', '鲜之味快餐店')->first();

        if ($merchant1 && ! DB::table('carts')->where('merchant_id', $merchant1->id)->exists()) {
            $cartId = DB::table('carts')->insertGetId(['merchant_id' => $merchant1->id, 'created_at' => $now, 'updated_at' => $now]);
            $sku1 = DB::table('skus')->where('sku_code', 'SKU-0001')->first();
            $sku2 = DB::table('skus')->where('sku_code', 'SKU-0002')->first();
            if ($sku1) DB::table('cart_items')->insert(['cart_id' => $cartId, 'sku_id' => $sku1->id, 'quantity' => 2000, 'price' => 9200, 'created_at' => $now, 'updated_at' => $now]);
            if ($sku2) DB::table('cart_items')->insert(['cart_id' => $cartId, 'sku_id' => $sku2->id, 'quantity' => 1000, 'price' => 13800, 'created_at' => $now, 'updated_at' => $now]);
        }

        if ($merchant2 && ! DB::table('carts')->where('merchant_id', $merchant2->id)->exists()) {
            $cartId = DB::table('carts')->insertGetId(['merchant_id' => $merchant2->id, 'created_at' => $now, 'updated_at' => $now]);
            $sku4 = DB::table('skus')->where('sku_code', 'SKU-0004')->first();
            if ($sku4) DB::table('cart_items')->insert(['cart_id' => $cartId, 'sku_id' => $sku4->id, 'quantity' => 500, 'price' => 149500, 'created_at' => $now, 'updated_at' => $now]);
        }
    }

    protected function seedOrders(): void
    {
        $now = now();
        $merchant1 = DB::table('merchants')->where('name', '味之初餐饮店')->first();
        $merchant2 = DB::table('merchants')->where('name', '家常菜馆')->first();
        $merchant3 = DB::table('merchants')->where('name', '鲜之味快餐店')->first();
        $route1 = DB::table('delivery_routes')->where('name', '城区北线')->first();

        $orders = [
            [
                'order_no' => 'ORD-20260728-001', 'merchant' => $merchant1, 'route' => $route1, 'batch' => 1,
                'delivery_address' => '安徽省宿州市埇桥区人民路88号', 'contact_name' => '吴老板', 'contact_phone' => '15800000001',
                'status' => 4, 'total_amount' => 23000, 'adjusted_amount' => 23000, 'final_amount' => 23000, 'payment_status' => 2, 'settlement_type' => 1,
                'items' => [
                    ['product' => '大白菜', 'quantity' => 2000, 'price' => 9200, 'actual_quantity' => 2100, 'actual_price' => 9200],
                    ['product' => '土豆',   'quantity' => 500,  'price' => 13800, 'actual_quantity' => 480, 'actual_price' => 13800],
                ],
            ],
            [
                'order_no' => 'ORD-20260728-002', 'merchant' => $merchant2, 'route' => $route1, 'batch' => 1,
                'delivery_address' => '安徽省宿州市埇桥区汴河路56号', 'contact_name' => '冯老板', 'contact_phone' => '15800000003',
                'status' => 2, 'total_amount' => 74500, 'adjusted_amount' => 74500, 'final_amount' => 0, 'payment_status' => 1, 'settlement_type' => 2,
                'items' => [
                    ['product' => '五花肉', 'quantity' => 500, 'price' => 149000, 'actual_quantity' => 0, 'actual_price' => 0],
                ],
            ],
            [
                'order_no' => 'ORD-20260729-001', 'merchant' => $merchant3, 'route' => $route1, 'batch' => 2,
                'delivery_address' => '安徽省宿州市埇桥区淮海路120号', 'contact_name' => '郑老板', 'contact_phone' => '15800000002',
                'status' => 1, 'total_amount' => 4025000, 'adjusted_amount' => 4025000, 'final_amount' => 0, 'payment_status' => 1, 'settlement_type' => 3,
                'items' => [
                    ['product' => '鲜虾', 'quantity' => 1000, 'price' => 4025000, 'actual_quantity' => 0, 'actual_price' => 0],
                ],
            ],
        ];

        foreach ($orders as $orderData) {
            if (! $orderData['merchant']) continue;
            if (DB::table('orders')->where('order_no', $orderData['order_no'])->exists()) continue;

            $orderId = DB::table('orders')->insertGetId([
                'order_no' => $orderData['order_no'], 'merchant_id' => $orderData['merchant']->id,
                'delivery_route_id' => $orderData['route']?->id, 'batch' => $orderData['batch'],
                'delivery_address' => $orderData['delivery_address'], 'contact_name' => $orderData['contact_name'], 'contact_phone' => $orderData['contact_phone'],
                'status' => $orderData['status'], 'total_amount' => $orderData['total_amount'],
                'adjusted_amount' => $orderData['adjusted_amount'], 'final_amount' => $orderData['final_amount'],
                'payment_status' => $orderData['payment_status'], 'settlement_type' => $orderData['settlement_type'],
                'is_locked' => 0, 'remark' => null, 'created_at' => $now, 'updated_at' => $now,
            ]);

            foreach ($orderData['items'] as $item) {
                $product = DB::table('products')->where('name', $item['product'])->first();
                if (! $product) continue;
                $sku = DB::table('skus')->where('product_id', $product->id)->first();
                if (! $sku) continue;
                $subtotal = $item['quantity'] * $item['price'];
                $actualSubtotal = $item['actual_quantity'] * $item['actual_price'];

                DB::table('order_items')->insert([
                    'order_id' => $orderId, 'sku_id' => $sku->id, 'product_name' => $item['product'],
                    'sku_specs' => $sku->specs, 'quantity' => $item['quantity'], 'price' => $item['price'],
                    'actual_quantity' => $item['actual_quantity'], 'actual_price' => $item['actual_price'],
                    'subtotal' => $subtotal, 'actual_subtotal' => $actualSubtotal,
                    'strategy_price' => 0, 'strategy_amount' => 0, 'discrepancy_amount' => abs($subtotal - $actualSubtotal),
                    'status' => 1, 'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }
    }

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
                    'merchant_id' => $merchant1->id, 'sku_id' => $sku->id,
                    'buy_count' => $item['buy_count'], 'last_buy_at' => $now,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }
    }

    protected function seedRepurchaseTemplates(): void
    {
        $now = now();
        $merchant1 = DB::table('merchants')->where('name', '味之初餐饮店')->first();
        if (! $merchant1) return;

        if (! DB::table('repurchase_templates')->where('merchant_id', $merchant1->id)->where('name', '日常蔬菜采购')->exists()) {
            $templateId = DB::table('repurchase_templates')->insertGetId([
                'merchant_id' => $merchant1->id, 'name' => '日常蔬菜采购',
                'status' => 1, 'created_at' => $now, 'updated_at' => $now,
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
                    'template_id' => $templateId, 'sku_id' => $sku->id,
                    'quantity' => $item['quantity'], 'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }
    }

    protected function seedOrderReturns(): void
    {
        $now = now();
        $order1 = DB::table('orders')->where('order_no', 'ORD-20260728-001')->first();

        if ($order1 && ! DB::table('order_returns')->where('return_no', 'OR-20260729-001')->exists()) {
            $returnId = DB::table('order_returns')->insertGetId([
                'return_no' => 'OR-20260729-001', 'order_id' => $order1->id, 'merchant_id' => $order1->merchant_id,
                'status' => 1, 'total_amount' => 6900, 'refund_amount' => 0,
                'reason' => '土豆质量问题退货', 'operator_id' => DB::table('users')->where('username', 'operator1')->value('id'),
                'created_at' => $now, 'updated_at' => $now,
            ]);

            $orderItem = DB::table('order_items')->where('order_id', $order1->id)->skip(1)->first();
            if ($orderItem) {
                DB::table('order_return_items')->insert([
                    'order_return_id' => $returnId, 'order_item_id' => $orderItem->id,
                    'sku_id' => $orderItem->sku_id, 'quantity' => 500, 'price' => 13800,
                    'amount' => 6900000, 'refund_amount' => 0, 'reason' => '品质不达标',
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }
    }
}
