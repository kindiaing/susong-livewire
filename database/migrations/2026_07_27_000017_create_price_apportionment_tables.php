<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 价格/费用均摊记录表
        Schema::create('price_apportionments', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->tinyInteger('target_type')->unsigned()->default(1)->comment('单据类型：1订单，2采购单');
            $table->unsignedBigInteger('target_id')->comment('单据ID');
            $table->unsignedBigInteger('target_item_id')->nullable()->comment('单据明细ID');
            $table->tinyInteger('apportion_type')->unsigned()->default(1)->comment('均摊类型：1整单改价，2促销差价，3费用，4运费');
            $table->bigInteger('amount')->default(0)->comment('均摊金额');
            $table->tinyInteger('apportion_mode')->unsigned()->default(1)->comment('均摊方式：1自动均摊，2手动均摊');
            $table->bigInteger('manual_amount')->default(0)->comment('手动均摊金额');
            $table->unsignedBigInteger('operator_id')->nullable()->comment('操作人ID');
            $table->tinyInteger('approval_status')->unsigned()->default(1)->comment('审核状态：1待审核，2已通过，3已拒绝（手动均摊时有效，自动均摊默认2）');
            $table->timestamps();
            $table->softDeletes();

            $table->index('target_type');
            $table->index('target_id');
            $table->index('target_item_id');
            $table->index('apportion_type');
            $table->index('approval_status');
            $table->comment('价格/费用均摊记录表');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_apportionments');
    }
};
