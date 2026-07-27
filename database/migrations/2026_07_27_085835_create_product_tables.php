<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('parent_id')->default(0)->comment('父级分类ID，0为根节点');
            $table->string('name', 50)->comment('分类名称');
            $table->string('icon', 255)->nullable()->comment('图标');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：0禁用，1启用');
            $table->timestamps();
            $table->softDeletes();
            $table->index('parent_id');
            $table->index('status');
            $table->comment('商品分类表');
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('category_id')->comment('分类ID');
            $table->unsignedBigInteger('supplier_id')->nullable()->comment('默认供应商ID');
            $table->string('name', 100)->comment('商品名称');
            $table->string('cover', 255)->nullable()->comment('封面图');
            $table->string('unit', 20)->comment('单位：斤/箱/份等');
            $table->tinyInteger('is_weight_priced')->unsigned()->default(0)->comment('是否称重改价：0否，1是');
            $table->bigInteger('stock_warning_value')->default(0)->comment('库存预警值');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：0下架，1上架');
            $table->text('description')->nullable()->comment('商品详情');
            $table->timestamps();
            $table->softDeletes();
            $table->index('category_id');
            $table->index('supplier_id');
            $table->index('status');
            $table->comment('商品表');
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('product_id')->comment('商品ID');
            $table->string('image_url', 255)->comment('图片地址');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->timestamps();
            $table->index('product_id');
            $table->comment('商品图片表');
        });

        Schema::create('skus', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('product_id')->comment('商品ID');
            $table->string('sku_code', 50)->unique()->comment('SKU编码');
            $table->json('specs')->nullable()->comment('规格属性');
            $table->bigInteger('purchase_price')->default(0)->comment('采购参考价');
            $table->bigInteger('wholesale_price')->default(0)->comment('批发销售价');
            $table->bigInteger('cost_price')->default(0)->comment('财务成本价');
            $table->bigInteger('stock')->default(0)->comment('当前库存冗余字段');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：0禁用，1启用');
            $table->tinyInteger('approval_status')->unsigned()->default(1)->comment('审核状态：1待审核，2已通过，3已拒绝');
            $table->timestamps();
            $table->softDeletes();
            $table->index('product_id');
            $table->index('status');
            $table->index('approval_status');
            $table->comment('SKU规格表');
        });

        Schema::create('merchant_sku_visibility', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('merchant_id')->comment('商家ID');
            $table->unsignedBigInteger('sku_id')->comment('SKU ID');
            $table->tinyInteger('is_visible')->unsigned()->default(1)->comment('是否可见：0否，1是');
            $table->timestamps();
            $table->unique(['merchant_id', 'sku_id']);
            $table->index('sku_id');
            $table->comment('商家SKU可见性表');
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->string('name', 50)->unique()->comment('标签名称');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态');
            $table->timestamps();
            $table->comment('标签词库表');
        });

        Schema::create('product_tags', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('product_id')->comment('商品ID');
            $table->unsignedBigInteger('tag_id')->comment('标签ID');
            $table->timestamps();
            $table->unique(['product_id', 'tag_id']);
            $table->index('tag_id');
            $table->comment('商品标签关联表');
        });

        Schema::create('keywords', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->string('keyword', 50)->comment('关键词');
            $table->unsignedBigInteger('product_id')->nullable()->comment('关联商品ID');
            $table->unsignedInteger('search_count')->default(0)->comment('搜索次数');
            $table->timestamps();
            $table->index('product_id');
            $table->index('keyword');
            $table->comment('搜索关键词表');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keywords');
        Schema::dropIfExists('product_tags');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('merchant_sku_visibility');
        Schema::dropIfExists('skus');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
    }
};
