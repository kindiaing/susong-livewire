<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('merchant_id')->unique()->comment('商家ID');
            $table->timestamps();
            $table->comment('购物车表');
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('cart_id')->comment('购物车ID');
            $table->unsignedBigInteger('sku_id')->comment('SKU ID');
            $table->bigInteger('quantity')->default(0)->comment('数量（base_unit 最小单位）');
            $table->bigInteger('price')->default(0)->comment('加入时单价');
            $table->unsignedBigInteger('unit_id')->nullable()->comment('下单时所选单位ID');
            $table->bigInteger('unit_quantity')->default(0)->comment('下单时所选单位的数量');
            $table->timestamps();
            $table->index('cart_id');
            $table->index('sku_id');
            $table->comment('购物车明细表');
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->string('order_no', 50)->unique()->comment('订单号');
            $table->unsignedBigInteger('merchant_id')->comment('商家ID');
            $table->unsignedBigInteger('delivery_route_id')->nullable()->comment('配送线路ID（已废弃，线路信息通过配送任务获取）');
            $table->tinyInteger('batch')->unsigned()->default(1)->comment('配送批次：1上午，2下午');
            $table->string('delivery_address', 255)->nullable()->comment('配送地址');
            $table->string('contact_name', 50)->nullable()->comment('收货联系人');
            $table->string('contact_phone', 20)->nullable()->comment('收货电话');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：1待拣货，2拣货中，3配送中，4已签收，5已锁定，9已作废');
            $table->bigInteger('total_amount')->default(0)->comment('原始订单金额');
            $table->bigInteger('adjusted_amount')->default(0)->comment('调整后金额');
            $table->bigInteger('final_amount')->default(0)->comment('最终结算金额');
            $table->tinyInteger('payment_status')->unsigned()->default(1)->comment('支付状态：1未支付，2已支付，3账期');
            $table->tinyInteger('settlement_type')->unsigned()->default(1)->comment('结算方式：1现结，2账期，3预付款');
            $table->tinyInteger('is_locked')->unsigned()->default(0)->comment('是否锁定：0否，1是');
            $table->date('order_date')->nullable()->comment('单据日期');
            $table->date('delivery_date')->nullable()->comment('收货日期');
            $table->text('remark')->nullable()->comment('备注');
            $table->tinyInteger('is_supplement')->unsigned()->default(0)->comment('是否补单：0否，1是');
            $table->unsignedBigInteger('supplement_for')->nullable()->comment('补单关联的原订单ID');
            $table->timestamps();
            $table->softDeletes();
            $table->index('merchant_id');
            $table->index('delivery_route_id');
            $table->index('status');
            $table->index('batch');
            $table->index('order_date');
            $table->index('delivery_date');
            $table->comment('订单表');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('order_id')->comment('订单ID');
            $table->unsignedBigInteger('sku_id')->comment('SKU ID');
            $table->string('product_name', 100)->comment('商品名称快照');
            $table->json('sku_specs')->nullable()->comment('规格快照');
            $table->bigInteger('quantity')->default(0)->comment('下单数量（base_unit 最小单位）');
            $table->unsignedBigInteger('unit_id')->nullable()->comment('下单时选择的单位ID');
            $table->bigInteger('unit_quantity')->default(0)->comment('下单时选择的单位数量（如选"箱"输入2，此字段=2）');
            $table->bigInteger('price')->default(0)->comment('下单单价');
            $table->bigInteger('actual_quantity')->default(0)->comment('实际称重数量');
            $table->bigInteger('actual_price')->default(0)->comment('实际称重单价');
            $table->bigInteger('subtotal')->default(0)->comment('小计金额');
            $table->bigInteger('actual_subtotal')->default(0)->comment('实际小计金额');
            $table->bigInteger('strategy_price')->default(0)->comment('改价/促销单价');
            $table->bigInteger('strategy_amount')->default(0)->comment('改价/促销金额');
            $table->unsignedBigInteger('price_strategy_id')->nullable()->comment('价格策略ID');
            $table->unsignedBigInteger('price_strategy_item_id')->nullable()->comment('价格策略明细ID');
            $table->bigInteger('discrepancy_amount')->default(0)->comment('差异金额');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：1正常，2待审核，3已调整');
            $table->timestamps();
            $table->index('order_id');
            $table->index('sku_id');
            $table->comment('订单明细表');
        });

        Schema::create('frequently_bought', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('merchant_id')->comment('商家ID');
            $table->unsignedBigInteger('sku_id')->comment('SKU ID');
            $table->unsignedInteger('buy_count')->default(0)->comment('购买次数');
            $table->timestamp('last_buy_at')->nullable()->comment('最近购买时间');
            $table->timestamps();
            $table->unique(['merchant_id', 'sku_id']);
            $table->index('sku_id');
            $table->comment('常购清单表');
        });

        Schema::create('repurchase_templates', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('merchant_id')->comment('商家ID');
            $table->string('name', 50)->comment('模板名称');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态');
            $table->timestamps();
            $table->index('merchant_id');
            $table->comment('复购模板表');
        });

        Schema::create('repurchase_template_items', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('template_id')->comment('模板ID');
            $table->unsignedBigInteger('sku_id')->comment('SKU ID');
            $table->bigInteger('quantity')->default(0)->comment('数量');
            $table->timestamps();
            $table->index('template_id');
            $table->index('sku_id');
            $table->comment('复购模板明细表');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repurchase_template_items');
        Schema::dropIfExists('repurchase_templates');
        Schema::dropIfExists('frequently_bought');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
    }
};
