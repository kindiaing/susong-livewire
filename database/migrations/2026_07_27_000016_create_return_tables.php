<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 采购退货单表
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->string('return_no', 50)->unique()->comment('退货单号');
            $table->unsignedBigInteger('purchase_order_id')->comment('关联采购单ID');
            $table->unsignedBigInteger('supplier_id')->comment('供应商ID');
            $table->unsignedBigInteger('warehouse_id')->comment('出库仓库ID');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：1待审核，2已审核，3已出库，4完成，9取消');
            $table->bigInteger('total_amount')->default(0)->comment('退货总金额');
            $table->bigInteger('actual_amount')->default(0)->comment('实际出库金额');
            $table->string('reason', 255)->nullable()->comment('退货原因');
            $table->unsignedBigInteger('operator_id')->nullable()->comment('经办人ID');
            $table->unsignedBigInteger('audited_by')->nullable()->comment('审核人ID');
            $table->timestamp('audited_at')->nullable()->comment('审核时间');
            $table->timestamp('shipped_at')->nullable()->comment('出库时间');
            $table->timestamp('completed_at')->nullable()->comment('完成时间');
            $table->timestamp('cancelled_at')->nullable()->comment('取消时间');
            $table->text('remark')->nullable()->comment('备注');
            $table->timestamps();
            $table->softDeletes();

            $table->index('purchase_order_id');
            $table->index('supplier_id');
            $table->index('status');
            $table->comment('采购退货单表');
        });

        // 采购退货明细表
        Schema::create('purchase_return_items', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('purchase_return_id')->comment('采购退货单ID');
            $table->unsignedBigInteger('purchase_order_item_id')->comment('采购单明细ID');
            $table->unsignedBigInteger('sku_id')->comment('SKU ID');
            $table->bigInteger('quantity')->default(0)->comment('退货数量');
            $table->bigInteger('price')->default(0)->comment('退货单价');
            $table->bigInteger('amount')->default(0)->comment('退货金额');
            $table->bigInteger('actual_quantity')->default(0)->comment('实际出库数量');
            $table->bigInteger('actual_price')->default(0)->comment('实际出库单价');
            $table->bigInteger('actual_amount')->default(0)->comment('实际出库金额');
            $table->string('reason', 255)->nullable()->comment('明细原因');
            $table->timestamps();

            $table->index('purchase_return_id');
            $table->index('purchase_order_item_id');
            $table->index('sku_id');
            $table->comment('采购退货明细表');
        });

        // 售后退货单表
        Schema::create('order_returns', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->string('return_no', 50)->unique()->comment('退货单号');
            $table->unsignedBigInteger('order_id')->comment('关联订单ID');
            $table->unsignedBigInteger('merchant_id')->comment('商家ID');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：1待审核，2已审核，3已退货，4退款完成，9取消');
            $table->bigInteger('total_amount')->default(0)->comment('退货总金额');
            $table->bigInteger('refund_amount')->default(0)->comment('实际退款金额');
            $table->string('reason', 255)->nullable()->comment('退货原因');
            $table->unsignedBigInteger('operator_id')->nullable()->comment('经办人ID');
            $table->unsignedBigInteger('audited_by')->nullable()->comment('审核人ID');
            $table->timestamp('audited_at')->nullable()->comment('审核时间');
            $table->text('remark')->nullable()->comment('备注');
            $table->timestamps();
            $table->softDeletes();

            $table->index('order_id');
            $table->index('merchant_id');
            $table->index('status');
            $table->comment('售后退货单表');
        });

        // 售后退货明细表
        Schema::create('order_return_items', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('order_return_id')->comment('售后退货单ID');
            $table->unsignedBigInteger('order_item_id')->comment('订单明细ID');
            $table->unsignedBigInteger('sku_id')->comment('SKU ID');
            $table->bigInteger('quantity')->default(0)->comment('退货数量');
            $table->bigInteger('price')->default(0)->comment('退货单价');
            $table->bigInteger('amount')->default(0)->comment('退货金额');
            $table->bigInteger('refund_amount')->default(0)->comment('实际退款金额');
            $table->string('reason', 255)->nullable()->comment('明细原因');
            $table->timestamps();

            $table->index('order_return_id');
            $table->index('order_item_id');
            $table->index('sku_id');
            $table->comment('售后退货明细表');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_return_items');
        Schema::dropIfExists('order_returns');
        Schema::dropIfExists('purchase_return_items');
        Schema::dropIfExists('purchase_returns');
    }
};
