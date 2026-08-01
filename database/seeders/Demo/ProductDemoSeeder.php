<?php

namespace Database\Seeders\Demo;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * 商品管理测试数据 Seeder
 *
 * 包含：分类、商品（含SKU）、SKU条码、一品多供、关键词、标签、商家收藏
 */
class ProductDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedCategories();
        $this->seedProducts();
        $this->seedSkuSuppliers();
        $this->seedSkuBarcodes();
        $this->seedKeywords();
        $this->seedTags();
        $this->seedProductTags();
        $this->seedProductImages();
        $this->seedMerchantFavorites();
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

    protected function seedTags(): void
    {
        $now = now();
        $tags = [
            ['name' => '热销', 'sort' => 1, 'status' => 1],
            ['name' => '新品', 'sort' => 2, 'status' => 1],
            ['name' => '特价', 'sort' => 3, 'status' => 1],
            ['name' => '冷链', 'sort' => 4, 'status' => 1],
            ['name' => '应季', 'sort' => 5, 'status' => 1],
            ['name' => '已停用标签', 'sort' => 99, 'status' => 0],
        ];

        foreach ($tags as $tag) {
            if (! DB::table('tags')->where('name', $tag['name'])->exists()) {
                DB::table('tags')->insert([
                    'name' => $tag['name'],
                    'sort' => $tag['sort'],
                    'status' => $tag['status'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    protected function seedProductTags(): void
    {
        $now = now();
        $productNames = ['大白菜', '五花肉', '鲜虾', '金龙鱼大豆油'];
        $tagNames = ['热销', '新品', '特价', '应季'];

        foreach ($productNames as $productName) {
            $product = DB::table('products')->where('name', $productName)->first();
            if (! $product) continue;

            foreach ($tagNames as $tagName) {
                $tag = DB::table('tags')->where('name', $tagName)->first();
                if (! $tag) continue;

                if (! DB::table('product_tags')->where('product_id', $product->id)->where('tag_id', $tag->id)->exists()) {
                    DB::table('product_tags')->insert([
                        'product_id' => $product->id,
                        'tag_id' => $tag->id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }
    }

    protected function seedProductImages(): void
    {
        $now = now();
        $images = [
            ['product' => '大白菜', 'urls' => ['/uploads/products/baicai-1.jpg', '/uploads/products/baicai-2.jpg']],
            ['product' => '五花肉', 'urls' => ['/uploads/products/wuhua-1.jpg']],
            ['product' => '鲜虾', 'urls' => ['/uploads/products/xianxia-1.jpg', '/uploads/products/xianxia-2.jpg', '/uploads/products/xianxia-3.jpg']],
        ];

        foreach ($images as $item) {
            $product = DB::table('products')->where('name', $item['product'])->first();
            if (! $product) continue;

            foreach ($item['urls'] as $idx => $url) {
                if (! DB::table('product_images')->where('product_id', $product->id)->where('image_url', $url)->exists()) {
                    DB::table('product_images')->insert([
                        'product_id' => $product->id,
                        'image_url' => $url,
                        'sort' => $idx + 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }
    }
}
