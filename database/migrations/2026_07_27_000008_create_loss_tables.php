<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loss_orders', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->string('loss_no', 50)->unique()->comment('损耗单号');
            $table->string('source_type', 50)->nullable()->comment('来源类型：purchase_order=采购入库差异');
            $table->unsignedBigInteger('source_id')->nullable()->comment('来源业务ID（如采购单ID）');
            $table->unsignedBigInteger('warehouse_id')->comment('仓库ID');
            $table->bigInteger('total_amount')->default(0)->comment('损耗总金额');
            $table->tinyInteger('loss_type')->unsigned()->default(1)->comment('主要损耗类型：1存储腐坏，2称重失水，3过期报废，4加工损耗，5盘点差异，6其他');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：1待审核，2已通过，3已执行，4已关闭，9已作废');
            $table->tinyInteger('approval_status')->unsigned()->default(1)->comment('审核状态：1待审核，2已通过，3已拒绝');
            $table->unsignedBigInteger('applicant_id')->nullable()->comment('申请人ID');
            $table->unsignedBigInteger('reviewer_id')->nullable()->comment('审核人ID');
            $table->timestamp('reviewed_at')->nullable()->comment('审核时间');
            $table->timestamp('executed_at')->nullable()->comment('执行时间');
            $table->timestamp('closed_at')->nullable()->comment('关闭时间');
            $table->string('reason', 255)->nullable()->comment('损耗原因');
            $table->text('remark')->nullable()->comment('备注');
            $table->timestamps();
            $table->softDeletes();
            $table->index('warehouse_id');
            $table->index('loss_type');
            $table->index('status');
            $table->index('approval_status');
            $table->index(['source_type', 'source_id']);
            $table->comment('损耗单主表');
        });

        Schema::create('loss_order_items', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('loss_order_id')->comment('损耗单ID');
            $table->unsignedBigInteger('purchase_order_item_id')->nullable()->comment('采购单明细ID（入库差异来源）');
            $table->unsignedBigInteger('purchase_order_id')->nullable()->comment('采购单ID（入库差异来源）');
            $table->unsignedBigInteger('sku_id')->comment('SKU ID');
            $table->tinyInteger('loss_type')->unsigned()->default(1)->comment('损耗类型：1存储腐坏，2称重失水，3过期报废，4加工损耗，5盘点差异，6其他');
            $table->bigInteger('quantity')->default(0)->comment('损耗数量');
            $table->bigInteger('cost_price')->default(0)->comment('SKU成本价快照');
            $table->bigInteger('loss_amount')->default(0)->comment('损耗金额');
            $table->tinyInteger('responsible_party')->unsigned()->default(1)->comment('责任方：1平台，2供应商');
            $table->unsignedBigInteger('supplier_id')->nullable()->comment('供应商ID');
            $table->string('reason', 255)->nullable()->comment('明细损耗原因');
            $table->json('evidence_urls')->nullable()->comment('凭证图片数组');
            $table->timestamps();
            $table->index('loss_order_id');
            $table->index('sku_id');
            $table->index('loss_type');
            $table->index('supplier_id');
            $table->index('purchase_order_item_id');
            $table->index('purchase_order_id');
            $table->comment('损耗单明细表');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loss_order_items');
        Schema::dropIfExists('loss_orders');
    }
};
