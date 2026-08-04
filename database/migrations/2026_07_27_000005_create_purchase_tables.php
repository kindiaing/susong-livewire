<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('sku_id')->comment('SKU ID');
            $table->unsignedBigInteger('supplier_id')->nullable()->comment('供应商ID');
            $table->bigInteger('quantity')->default(0)->comment('待采数量');
            $table->bigInteger('expected_price')->default(0)->comment('预估成本价（厘）');
            $table->tinyInteger('source_type')->unsigned()->default(1)->comment('来源：1订单汇总，2手工添加');
            $table->unsignedBigInteger('source_id')->nullable()->comment('来源业务ID');
            $table->unsignedBigInteger('purchase_order_id')->nullable()->comment('关联采购单ID');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：1待生成采购单，2已生成采购单');
            $table->timestamps();
            $table->index('sku_id');
            $table->index('status');
            $table->comment('待采清单表');
        });

        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->string('order_no', 50)->unique()->comment('采购单号');
            $table->unsignedBigInteger('supplier_id')->comment('供应商ID');
            $table->unsignedBigInteger('warehouse_id')->nullable()->comment('入库目标仓库');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：1待接单，2备货中，3已发货，4已入库，5完成，9取消');
            $table->tinyInteger('return_status')->unsigned()->default(0)->comment('退货状态：0无退货，1部分退货，2全部退货');
            $table->bigInteger('total_amount')->default(0)->comment('总金额');
            $table->bigInteger('actual_amount')->default(0)->comment('实际入库金额');
            $table->unsignedBigInteger('operator_id')->nullable()->comment('经办人');
            $table->unsignedBigInteger('approved_by')->nullable()->comment('审核人ID');
            $table->timestamp('approved_at')->nullable()->comment('审核时间');
            $table->timestamp('ordered_at')->nullable()->comment('下单时间');
            $table->timestamp('shipped_at')->nullable()->comment('发货时间');
            $table->timestamp('stocked_at')->nullable()->comment('入库时间');
            $table->timestamp('completed_at')->nullable()->comment('完成时间');
            $table->timestamp('cancelled_at')->nullable()->comment('取消时间');
            $table->string('cancel_reason', 255)->nullable()->comment('取消原因');
            $table->text('remark')->nullable()->comment('备注');
            $table->timestamps();
            $table->softDeletes();
            $table->index('supplier_id');
            $table->index('status');
            $table->comment('采购单表');
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('purchase_order_id')->comment('采购单ID');
            $table->unsignedBigInteger('sku_id')->comment('SKU ID');
            $table->bigInteger('quantity')->default(0)->comment('采购数量');
            $table->bigInteger('price')->default(0)->comment('采购单价');
            $table->bigInteger('actual_quantity')->default(0)->comment('实际入库数量');
            $table->bigInteger('actual_price')->default(0)->comment('实际入库单价');
            $table->bigInteger('amount')->default(0)->comment('金额');
            $table->bigInteger('actual_amount')->default(0)->comment('实际金额');
            $table->bigInteger('strategy_price')->default(0)->comment('改价/促销单价');
            $table->bigInteger('strategy_amount')->default(0)->comment('改价/促销金额');
            $table->unsignedBigInteger('price_strategy_id')->nullable()->comment('价格策略ID');
            $table->unsignedBigInteger('price_strategy_item_id')->nullable()->comment('价格策略明细ID');
            $table->string('discrepancy_reason', 255)->nullable()->comment('入库差异原因');
            $table->bigInteger('discrepancy_quantity')->default(0)->comment('入库差异数量（采购数量-实际入库数量）');
            $table->bigInteger('returned_quantity')->default(0)->comment('已退货数量');
            $table->string('remark', 255)->nullable()->comment('明细备注');
            $table->unsignedBigInteger('loss_order_id')->nullable()->comment('关联损耗单ID');
            $table->timestamps();
            $table->index('purchase_order_id');
            $table->index('sku_id');
            $table->comment('采购单明细表');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('purchase_items');
    }
};
