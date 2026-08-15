<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('picking_tasks', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->string('task_no', 50)->unique()->comment('任务编号');
            $table->unsignedBigInteger('warehouse_id')->comment('仓库ID');
            $table->unsignedBigInteger('route_id')->nullable()->comment('所属配送线路ID');
            $table->date('delivery_date')->nullable()->comment('送达日期');
            $table->unsignedBigInteger('picker_id')->nullable()->comment('拣货员ID');
            $table->tinyInteger('batch')->unsigned()->default(1)->comment('配送批次：1上午，2下午');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：1待分配，2拣货中，3已完成');
            $table->unsignedInteger('total_skus')->default(0)->comment('SKU种数汇总');
            $table->bigInteger('total_quantity')->default(0)->comment('总数量汇总');
            $table->timestamp('started_at')->nullable()->comment('开始时间');
            $table->timestamp('completed_at')->nullable()->comment('完成时间');
            $table->timestamps();
            $table->softDeletes();
            $table->index('warehouse_id');
            $table->index('route_id');
            $table->index('delivery_date');
            $table->index(['route_id', 'delivery_date']);
            $table->index('picker_id');
            $table->index('status');
            $table->comment('拣货任务表（按线路生成，含SKU汇总）');
        });

        Schema::create('picking_task_items', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('picking_task_id')->comment('拣货任务ID');
            $table->unsignedBigInteger('order_id')->comment('订单ID');
            $table->unsignedBigInteger('order_item_id')->comment('订单明细ID');
            $table->unsignedBigInteger('sku_id')->comment('SKU ID');
            $table->unsignedBigInteger('merchant_id')->nullable()->comment('商家ID（方便按商家分组汇总）');
            $table->bigInteger('required_quantity')->default(0)->comment('需求数量');
            $table->bigInteger('picked_quantity')->default(0)->comment('实际拣货数量');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：1待拣货，2已拣货，3差异');
            $table->timestamps();
            $table->index('picking_task_id');
            $table->index('order_id');
            $table->index('sku_id');
            $table->index('merchant_id');
            $table->comment('拣货任务明细表');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('picking_task_items');
        Schema::dropIfExists('picking_tasks');
    }
};
