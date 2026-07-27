<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_tasks', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->string('task_no', 50)->unique()->comment('任务编号');
            $table->unsignedBigInteger('delivery_route_id')->comment('线路ID');
            $table->unsignedBigInteger('driver_id')->nullable()->comment('司机ID');
            $table->unsignedBigInteger('vehicle_id')->nullable()->comment('车辆ID');
            $table->tinyInteger('batch')->unsigned()->default(1)->comment('配送批次：1上午，2下午');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：1待配送，2配送中，3任务完成');
            $table->timestamp('planned_at')->nullable()->comment('计划配送时间');
            $table->timestamp('started_at')->nullable()->comment('开始时间');
            $table->timestamp('completed_at')->nullable()->comment('完成时间');
            $table->timestamps();
            $table->index('delivery_route_id');
            $table->index('driver_id');
            $table->index('status');
            $table->comment('配送任务表');
        });

        Schema::create('delivery_task_orders', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('delivery_task_id')->comment('配送任务ID');
            $table->unsignedBigInteger('order_id')->comment('订单ID');
            $table->unsignedInteger('delivery_sort')->default(0)->comment('配送顺序');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：1待配送，2已送达');
            $table->timestamps();
            $table->index('delivery_task_id');
            $table->index('order_id');
            $table->comment('配送任务订单关联表');
        });

        Schema::create('delivery_tracks', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('delivery_task_id')->comment('配送任务ID');
            $table->unsignedBigInteger('driver_id')->comment('司机ID');
            $table->integer('latitude')->default(0)->comment('纬度');
            $table->integer('longitude')->default(0)->comment('经度');
            $table->string('location_desc', 255)->nullable()->comment('位置描述');
            $table->timestamp('reported_at')->nullable()->comment('上报时间');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->index('delivery_task_id');
            $table->index('driver_id');
            $table->index('reported_at');
            $table->comment('配送轨迹表');
        });

        Schema::create('signatures', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('order_id')->comment('订单ID');
            $table->unsignedBigInteger('delivery_task_id')->comment('配送任务ID');
            $table->tinyInteger('type')->unsigned()->default(1)->comment('类型：1拍照签收，2电子签名，3质检照片');
            $table->string('image_url', 255)->nullable()->comment('图片/签名文件地址');
            $table->string('signer_name', 50)->nullable()->comment('签收人');
            $table->timestamp('signed_at')->nullable()->comment('签收时间');
            $table->timestamps();
            $table->index('order_id');
            $table->index('delivery_task_id');
            $table->index('type');
            $table->comment('签收存证表');
        });

        Schema::create('temperatures', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('delivery_task_id')->comment('配送任务ID');
            $table->integer('temperature')->default(0)->comment('温度值');
            $table->timestamp('recorded_at')->nullable()->comment('记录时间');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->index('delivery_task_id');
            $table->index('recorded_at');
            $table->comment('冷链温度记录表');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('temperatures');
        Schema::dropIfExists('signatures');
        Schema::dropIfExists('delivery_tracks');
        Schema::dropIfExists('delivery_task_orders');
        Schema::dropIfExists('delivery_tasks');
    }
};
