<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->string('name', 50)->comment('仓库名称');
            $table->tinyInteger('type')->unsigned()->default(1)->comment('类型：1总仓，2前置仓');
            $table->tinyInteger('is_cold_chain')->unsigned()->default(0)->comment('是否冷链：0否，1是');
            $table->string('address', 255)->nullable()->comment('地址');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态');
            $table->timestamps();
            $table->softDeletes();
            $table->index('status');
            $table->comment('仓库表');
        });

        Schema::create('inventory', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('warehouse_id')->comment('仓库ID');
            $table->unsignedBigInteger('sku_id')->comment('SKU ID');
            $table->bigInteger('total_stock')->default(0)->comment('总库存');
            $table->bigInteger('locked_stock')->default(0)->comment('锁定库存');
            $table->bigInteger('available_stock')->default(0)->comment('可用库存');
            $table->string('batch_no', 50)->nullable()->comment('入库批次号');
            $table->date('expiry_date')->nullable()->comment('效期');
            $table->bigInteger('warning_value')->default(0)->comment('预警值');
            $table->timestamps();
            $table->unique(['warehouse_id', 'sku_id', 'batch_no']);
            $table->index('sku_id');
            $table->comment('实时库存表');
        });

        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('warehouse_id')->comment('仓库ID');
            $table->unsignedBigInteger('sku_id')->comment('SKU ID');
            $table->tinyInteger('type')->unsigned()->comment('变动类型：1入库，2出库，3调拨，4报损，5报溢，6调整');
            $table->bigInteger('quantity')->default(0)->comment('变动数量，正增负减');
            $table->bigInteger('before_stock')->default(0)->comment('变动前库存');
            $table->bigInteger('after_stock')->default(0)->comment('变动后库存');
            $table->string('reason', 255)->nullable()->comment('变动原因');
            $table->unsignedBigInteger('operator_id')->nullable()->comment('操作人ID');
            $table->string('source_type', 50)->nullable()->comment('业务来源类型');
            $table->unsignedBigInteger('source_id')->nullable()->comment('业务来源ID');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->index('warehouse_id');
            $table->index('sku_id');
            $table->index('type');
            $table->index('created_at');
            $table->comment('库存变动日志表');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_logs');
        Schema::dropIfExists('inventory');
        Schema::dropIfExists('warehouses');
    }
};
