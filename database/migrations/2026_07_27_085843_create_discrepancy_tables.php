<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discrepancies', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->string('discrepancy_no', 50)->unique()->comment('差异单号');
            $table->unsignedBigInteger('order_id')->comment('关联订单ID');
            $table->unsignedBigInteger('order_item_id')->nullable()->comment('关联订单明细ID');
            $table->tinyInteger('stage')->unsigned()->comment('差异环节：1拣货，2配送，3实收');
            $table->tinyInteger('type')->unsigned()->default(1)->comment('差异类型：1少收，2拒收，3残次，4其他');
            $table->bigInteger('expected_quantity')->default(0)->comment('预期数量');
            $table->bigInteger('actual_quantity')->default(0)->comment('实际数量');
            $table->bigInteger('quantity_diff')->default(0)->comment('数量差异');
            $table->bigInteger('amount_diff')->default(0)->comment('金额差异');
            $table->string('reason', 255)->nullable()->comment('差异原因');
            $table->json('evidence_urls')->nullable()->comment('凭证图片数组');
            $table->tinyInteger('responsible_party')->unsigned()->nullable()->comment('责任方：1供应商，2平台，3司机，4商家');
            $table->tinyInteger('decision')->unsigned()->nullable()->comment('处理决策：1补货，2退款，3扣款，4报损，5不计');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：1待处理，2处理中，3已处理，4已关闭，5争议中');
            $table->unsignedBigInteger('handler_id')->nullable()->comment('处理人ID');
            $table->timestamp('handled_at')->nullable()->comment('处理时间');
            $table->tinyInteger('is_amount_adjusted')->unsigned()->default(0)->comment('是否已调整金额');
            $table->tinyInteger('approval_status')->unsigned()->default(1)->comment('审核状态：1待审核，2已通过，3已拒绝（决策为退款/扣款时有效）');
            $table->text('remark')->nullable()->comment('备注');
            $table->timestamps();
            $table->softDeletes();

            $table->index('order_id');
            $table->index('order_item_id');
            $table->index('stage');
            $table->index('status');
            $table->index('approval_status');
            $table->comment('差异单表');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discrepancies');
    }
};
