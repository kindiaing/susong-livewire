<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 商家收货地址表
        Schema::create('merchant_addresses', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('merchant_id')->comment('商家ID');
            $table->string('contact_name', 50)->nullable()->comment('联系人');
            $table->string('contact_phone', 20)->nullable()->comment('联系电话');
            $table->string('address', 255)->comment('收货地址');
            $table->tinyInteger('is_default')->unsigned()->default(0)->comment('是否默认地址：0否，1是');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->timestamps();
            $table->softDeletes();

            $table->index('merchant_id');
            $table->index('is_default');
            $table->comment('商家收货地址表');
        });

        // 商家收藏商品表
        Schema::create('merchant_favorites', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('merchant_id')->comment('商家ID');
            $table->unsignedBigInteger('sku_id')->comment('SKU ID');
            $table->timestamps();

            $table->unique(['merchant_id', 'sku_id']);
            $table->index('sku_id');
            $table->comment('商家收藏商品表');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merchant_favorites');
        Schema::dropIfExists('merchant_addresses');
    }
};
