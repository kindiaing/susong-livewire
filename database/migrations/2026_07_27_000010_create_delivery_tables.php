<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ========== 配送任务表（大幅改造） ==========
        Schema::create('delivery_tasks', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->string('task_no', 50)->unique()->comment('任务编号，如：T-E01-20260810-001');
            $table->unsignedBigInteger('route_id')->comment('所属线路ID');

            // 日期信息
            $table->date('delivery_date')->comment('送达日期');
            $table->timestamp('generated_at')->nullable()->comment('任务生成时间');

            // 分配
            $table->unsignedBigInteger('driver_id')->nullable()->comment('分配司机ID');
            $table->unsignedBigInteger('vehicle_id')->nullable()->comment('分配车辆ID');

            // 配送批次
            $table->tinyInteger('batch')->unsigned()->default(1)->comment('配送批次：1上午，2下午');

            // 状态：1待配送 2已分配 3配送中 4暂停 5已完成 6已取消
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：1待配送 2已分配 3配送中 4暂停 5已完成 6已取消');

            // 时间
            $table->timestamp('planned_start_time')->nullable()->comment('计划出发时间');
            $table->timestamp('actual_start_time')->nullable()->comment('实际出发时间');
            $table->timestamp('actual_complete_time')->nullable()->comment('实际完成时间');

            // 统计
            $table->unsignedInteger('total_stops')->default(0)->comment('总配送商家数');
            $table->unsignedInteger('completed_stops')->default(0)->comment('已完成商家数');
            $table->unsignedInteger('skipped_stops')->default(0)->comment('跳过商家数');
            $table->unsignedInteger('total_orders')->default(0)->comment('关联单据总数');

            // 标记
            $table->tinyInteger('has_urgent')->unsigned()->default(0)->comment('是否包含加急：0否 1是');
            $table->tinyInteger('has_important')->unsigned()->default(0)->comment('是否包含重要：0否 1是');

            $table->string('remark', 500)->nullable()->comment('备注');
            $table->timestamps();
            $table->index('route_id');
            $table->index('delivery_date');
            $table->index(['route_id', 'delivery_date']);
            $table->index('driver_id');
            $table->index('status');
            $table->comment('配送任务表：运营按需勾选单据生成');
        });

        // ========== 配送任务明细表（原 delivery_task_orders 改名重建） ==========
        Schema::create('delivery_task_details', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('task_id')->comment('所属配送任务ID');
            $table->unsignedBigInteger('order_id')->nullable()->comment('关联的原始订单ID');

            // 商家信息（冗余，方便查询）
            $table->unsignedBigInteger('merchant_id')->comment('商家ID');
            $table->string('merchant_name', 100)->nullable()->comment('商家名称（冗余）');
            $table->string('merchant_address', 255)->nullable()->comment('配送地址（冗余）');

            // 日期信息
            $table->date('order_date')->nullable()->comment('下单日期');
            $table->date('delivery_date')->comment('送达日期');

            // 商品信息
            $table->string('product_summary', 500)->nullable()->comment('商品摘要');
            $table->decimal('total_quantity', 10, 2)->nullable()->comment('总数量');
            $table->decimal('total_weight', 10, 2)->nullable()->comment('总重量（kg）');

            // 单据来源
            $table->string('source_type', 20)->default('order')->comment('来源类型：order=订单 direct=直配单 merge=合并单');
            $table->unsignedBigInteger('source_id')->nullable()->comment('来源单据ID');

            // 状态：1待配送 2配送中 3已送达 4已取消
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：1待配送 2配送中 3已送达 4已取消');

            // 配送确认
            $table->timestamp('delivered_at')->nullable()->comment('实际送达时间');
            $table->string('delivery_method', 20)->nullable()->comment('配送方式：manual=手工 scan=扫码 photo=拍照 signature=签名');
            $table->json('delivery_photos')->nullable()->comment('配送照片[{url, taken_at}]');
            $table->string('delivery_remark', 500)->nullable()->comment('配送备注');

            $table->timestamps();
            $table->index('task_id');
            $table->index('merchant_id');
            $table->index('delivery_date');
            $table->index('order_date');
            $table->index('status');
            $table->comment('配送任务明细表：运营从单据池勾选的每张单据');
        });

        // ========== 配送顺序表（核心：按线路顺序 + 标记） ==========
        Schema::create('delivery_task_sequences', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('task_id')->comment('所属配送任务ID');
            $table->json('task_detail_ids')->comment('本商家在本任务中的所有明细ID数组');

            // 商家信息
            $table->unsignedBigInteger('merchant_id')->comment('商家ID');
            $table->string('merchant_name', 100)->nullable()->comment('商家名称（冗余）');
            $table->string('merchant_address', 255)->nullable()->comment('地址（冗余）');
            $table->decimal('latitude', 10, 8)->nullable()->comment('纬度');
            $table->decimal('longitude', 11, 8)->nullable()->comment('经度');

            // 核心顺序控制
            $table->unsignedInteger('base_sequence_no')->comment('来自线路的原始顺序号，永不变');
            $table->unsignedInteger('sequence_no')->comment('本次任务中的实际顺序号（1,2,3...）');

            // 预计时间
            $table->timestamp('estimated_arrival')->nullable()->comment('预计到达时间');
            $table->timestamp('estimated_departure')->nullable()->comment('预计离开时间');

            // 实际时间
            $table->timestamp('actual_arrival')->nullable()->comment('实际到达时间');
            $table->timestamp('actual_departure')->nullable()->comment('实际离开时间');
            $table->timestamp('actual_delivered_at')->nullable()->comment('实际送达/签收时间');

            // 加急/重要标记（不改变顺序，仅用于提醒）
            $table->tinyInteger('is_urgent')->unsigned()->default(0)->comment('是否加急：0否 1是');
            $table->string('urgent_reason', 255)->nullable()->comment('加急原因');
            $table->tinyInteger('is_important')->unsigned()->default(0)->comment('是否重要：0否 1是');
            $table->string('important_reason', 255)->nullable()->comment('重要原因');

            // 状态：1待配送 2配送中 3已到达 4已送达 5已跳过 6失败
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：1待配送 2配送中 3已到达 4已送达 5已跳过 6失败');

            // 配送确认信息
            $table->string('delivery_method', 20)->nullable()->comment('确认方式');
            $table->json('delivery_photos')->nullable()->comment('配送照片');
            $table->string('signature_image', 500)->nullable()->comment('签名图片URL');
            $table->decimal('gps_latitude', 10, 8)->nullable()->comment('送达时纬度');
            $table->decimal('gps_longitude', 11, 8)->nullable()->comment('送达时经度');

            $table->string('skip_reason', 255)->nullable()->comment('跳过原因');
            $table->string('fail_reason', 255)->nullable()->comment('失败原因');
            $table->string('remark', 500)->nullable()->comment('备注');

            $table->timestamps();
            $table->index('task_id');
            $table->index(['task_id', 'sequence_no']);
            $table->index(['task_id', 'base_sequence_no']);
            $table->index('status');
            $table->index('is_urgent');
            $table->index('is_important');
            $table->comment('配送顺序表：按线路 sequence_no 自动生成，加急/重要标记不改变顺序');
        });

        // ========== 线路明细表 — 商家列表（核心排序表） ==========
        Schema::create('delivery_route_stops', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('route_id')->comment('所属线路ID');
            $table->unsignedBigInteger('merchant_id')->comment('商家ID');
            $table->unsignedInteger('sequence_no')->comment('顺序号，拖拽排序即改此字段；1,2,3...连续');

            // 商家配送配置
            $table->string('address', 255)->nullable()->comment('配送地址（冗余）');
            $table->decimal('latitude', 10, 8)->nullable()->comment('纬度');
            $table->decimal('longitude', 11, 8)->nullable()->comment('经度');
            $table->unsignedInteger('default_service_time')->default(10)->comment('默认停留时间（分钟）');

            // 状态
            $table->tinyInteger('is_active')->unsigned()->default(1)->comment('是否启用：0停用 1启用');
            $table->string('remark', 500)->nullable()->comment('备注');

            $table->timestamps();
            $table->unique(['route_id', 'sequence_no']);
            $table->unique(['route_id', 'merchant_id']);
            $table->index('route_id');
            $table->index(['route_id', 'sequence_no']);
            $table->index('merchant_id');
            $table->comment('线路明细 — 商家列表。拖拽排序通过修改 sequence_no 实现');
        });

        // ========== 抵达时间流水表 ==========
        Schema::create('delivery_arrival_logs', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('task_id')->comment('配送任务ID');
            $table->unsignedBigInteger('sequence_id')->nullable()->comment('关联的配送顺序表ID');
            $table->unsignedBigInteger('merchant_id')->comment('商家ID');

            // 事件
            $table->string('event_type', 30)->comment('事件类型：arrival=到达 departure=离开 delivered=送达 skipped=跳过 gps_enter=进入围栏 gps_leave=离开围栏');
            $table->timestamp('event_time')->comment('事件发生时间');

            // GPS
            $table->decimal('gps_latitude', 10, 8)->nullable()->comment('纬度');
            $table->decimal('gps_longitude', 11, 8)->nullable()->comment('经度');
            $table->decimal('gps_accuracy', 8, 2)->nullable()->comment('精度（米）');

            // 来源
            $table->string('source', 20)->default('driver')->comment('来源：driver=司机 gps_auto=自动 system=系统 admin=后台');
            $table->unsignedBigInteger('operator_id')->nullable()->comment('操作人ID');

            $table->json('extra_data')->nullable()->comment('额外数据');
            $table->timestamp('created_at')->nullable();

            $table->index('task_id');
            $table->index('merchant_id');
            $table->index('event_time');
            $table->comment('配送抵达时间流水：每次到达、离开、送达的不可变记录');
        });

        // ========== 车辆故障记录表 ==========
        Schema::create('vehicle_issues', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('vehicle_id')->comment('车辆ID');
            $table->unsignedBigInteger('task_id')->nullable()->comment('关联任务ID');
            $table->string('issue_type', 30)->nullable()->comment('故障类型：breakdown=抛锚 accident=事故 tire=轮胎 battery=电瓶 engine=发动机 other=其他');
            $table->text('description')->comment('描述');
            $table->json('photos')->nullable()->comment('故障照片');
            $table->timestamp('reported_at')->nullable()->comment('上报时间');
            $table->unsignedBigInteger('reported_by')->nullable()->comment('上报人ID');
            $table->timestamp('resolved_at')->nullable()->comment('解决时间');
            $table->unsignedBigInteger('resolved_by')->nullable()->comment('处理人ID');
            $table->string('impact_type', 20)->nullable()->comment('影响类型');
            $table->string('impact_desc', 500)->nullable()->comment('影响描述');
            // 状态：1处理中 2已解决 3已关闭
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：1处理中 2已解决 3已关闭');
            $table->timestamps();
            $table->index('vehicle_id');
            $table->index('task_id');
            $table->comment('车辆故障记录表');
        });

        // ========== 保留的旧表 ==========

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
        Schema::dropIfExists('vehicle_issues');
        Schema::dropIfExists('delivery_arrival_logs');
        Schema::dropIfExists('delivery_route_stops');
        Schema::dropIfExists('delivery_task_sequences');
        Schema::dropIfExists('delivery_task_details');
        Schema::dropIfExists('delivery_tasks');
    }
};
