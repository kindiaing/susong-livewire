<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 采购单明细补充价格策略字段
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->bigInteger('strategy_price')->default(0)->after('actual_amount')->comment('改价/促销单价');
            $table->bigInteger('strategy_amount')->default(0)->after('strategy_price')->comment('改价/促销金额');
            $table->unsignedBigInteger('price_strategy_id')->nullable()->after('strategy_amount')->comment('价格策略ID');
            $table->unsignedBigInteger('price_strategy_item_id')->nullable()->after('price_strategy_id')->comment('价格策略明细ID');
        });

        // 订单明细补充价格策略字段
        Schema::table('order_items', function (Blueprint $table) {
            $table->bigInteger('strategy_price')->default(0)->after('actual_subtotal')->comment('改价/促销单价');
            $table->bigInteger('strategy_amount')->default(0)->after('strategy_price')->comment('改价/促销金额');
            $table->unsignedBigInteger('price_strategy_id')->nullable()->after('strategy_amount')->comment('价格策略ID');
            $table->unsignedBigInteger('price_strategy_item_id')->nullable()->after('price_strategy_id')->comment('价格策略明细ID');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->dropColumn(['strategy_price', 'strategy_amount', 'price_strategy_id', 'price_strategy_item_id']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['strategy_price', 'strategy_amount', 'price_strategy_id', 'price_strategy_item_id']);
        });
    }
};
