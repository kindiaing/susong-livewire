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
            $table->unsignedBigInteger('picker_id')->nullable()->comment('拣货员ID');
            $table->tinyInteger('batch')->unsigned()->default(1)->comment('配送批次：1上午，2下午');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：1待分配，2拣货中，3已完成');
            $table->timestamp('started_at')->nullable()->comment('开始时间');
            $table->timestamp('completed_at')->nullable()->comment('完成时间');
            $table->timestamps();
            $table->index('warehouse_id');
            $table->index('picker_id');
            $table->index('status');
            $table->comment('拣货任务表');
        });

        Schema::create('picking_task_items', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('picking_task_id')->comment('拣货任务ID');
            $table->unsignedBigInteger('order_id')->comment('订单ID');
            $table->unsignedBigInteger('order_item_id')->comment('订单明细ID');
            $table->unsignedBigInteger('sku_id')->comment('SKU ID');
            $table->bigInteger('required_quantity')->default(0)->comment('需求数量');
            $table->bigInteger('picked_quantity')->default(0)->comment('实际拣货数量');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：1待拣货，2已拣货，3差异');
            $table->timestamps();
            $table->index('picking_task_id');
            $table->index('order_id');
            $table->index('sku_id');
            $table->comment('拣货任务明细表');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('picking_task_items');
        Schema::dropIfExists('picking_tasks');
    }
};
