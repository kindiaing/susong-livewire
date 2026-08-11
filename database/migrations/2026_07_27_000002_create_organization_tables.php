<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->string('name', 100)->comment('供应商名称');
            $table->string('contact_name', 50)->nullable()->comment('联系人');
            $table->string('contact_phone', 20)->nullable()->comment('联系电话');
            $table->string('address', 255)->nullable()->comment('地址');
            $table->string('bank_name', 100)->nullable()->comment('开户行');
            $table->string('bank_account', 50)->nullable()->comment('银行账号');
            $table->tinyInteger('settlement_cycle')->unsigned()->default(1)->comment('结算周期：1周结，2月结，3不定期');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：0禁用，1启用');
            $table->text('remark')->nullable()->comment('备注');
            $table->timestamps();
            $table->softDeletes();
            $table->index('status');
            $table->comment('供应商表');
        });

        Schema::create('delivery_routes', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->string('name', 100)->comment('线路名称，如：城东1号线');
            $table->string('code', 50)->unique()->comment('线路编码，如：E01');
            $table->unsignedBigInteger('warehouse_id')->nullable()->comment('出发仓库ID');
            $table->unsignedBigInteger('default_driver_id')->nullable()->comment('默认司机（用户ID）');
            $table->unsignedBigInteger('default_vehicle_id')->nullable()->comment('默认车辆ID');
            $table->string('color', 20)->default('#3B82F6')->comment('地图显示颜色');
            $table->time('departure_time')->default('06:00:00')->comment('默认出发时间');
            $table->unsignedInteger('estimated_duration')->nullable()->comment('预计总时长（分钟）');
            $table->decimal('estimated_distance', 8, 2)->nullable()->comment('预计总里程（公里）');
            $table->string('description', 255)->nullable()->comment('描述');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：0停用，1启用');
            $table->string('remark', 500)->nullable()->comment('备注');
            $table->timestamps();
            $table->softDeletes();
            $table->index('status');
            $table->comment('配送线路定义表');
        });

        Schema::create('merchants', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('user_id')->nullable()->comment('关联登录用户ID');
            $table->string('name', 100)->comment('商家名称');
            $table->string('contact_name', 50)->nullable()->comment('联系人');
            $table->string('contact_phone', 20)->nullable()->comment('联系电话');
            $table->string('address', 255)->nullable()->comment('默认配送地址');
            $table->unsignedBigInteger('delivery_route_id')->nullable()->comment('所属配送线路ID');
            $table->unsignedInteger('delivery_sort')->default(0)->comment('配送顺序');
            $table->bigInteger('min_order_amount')->default(0)->comment('起送价');
            $table->tinyInteger('settlement_type')->unsigned()->default(1)->comment('结算方式：1现结，2账期，3预付款');
            $table->bigInteger('credit_limit')->default(0)->comment('信用额度');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：0禁用，1启用');
            $table->text('remark')->nullable()->comment('备注');
            $table->timestamps();
            $table->softDeletes();
            $table->index('user_id');
            $table->index('delivery_route_id');
            $table->index('status');
            $table->comment('商家表');
        });

        Schema::create('drivers', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('user_id')->nullable()->comment('关联登录用户ID');
            $table->string('name', 50)->comment('姓名');
            $table->string('phone', 20)->unique()->comment('手机号');
            $table->string('id_card', 18)->nullable()->comment('身份证号');
            $table->tinyInteger('online_status')->unsigned()->default(0)->comment('在线状态：0离线，1在线');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：0禁用，1启用');
            $table->text('remark')->nullable()->comment('备注');
            $table->timestamps();
            $table->softDeletes();
            $table->index('user_id');
            $table->index('status');
            $table->comment('司机表');
        });

        Schema::create('vehicles', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->string('plate_number', 20)->unique()->comment('车牌号');
            $table->string('name', 50)->nullable()->comment('车辆名称');
            $table->string('type', 20)->default('van')->comment('类型：van=厢式货车 truck=卡车 refrigerated=冷藏车 motorcycle=三轮摩托车');
            $table->decimal('capacity_kg', 10, 2)->nullable()->comment('载重（公斤）');
            $table->decimal('capacity_volume', 8, 2)->nullable()->comment('容积（立方米）');
            $table->tinyInteger('is_cold_chain')->unsigned()->default(0)->comment('是否冷链：0否，1是');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：1可用，2维修中，3报废');
            $table->date('last_maintenance_date')->nullable()->comment('上次保养日期');
            $table->date('next_maintenance_date')->nullable()->comment('下次保养日期');
            $table->string('remark', 500)->nullable()->comment('备注');
            $table->timestamps();
            $table->softDeletes();
            $table->index('status');
            $table->comment('车辆表');
        });

        Schema::create('driver_vehicles', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('driver_id')->comment('司机ID');
            $table->unsignedBigInteger('vehicle_id')->comment('车辆ID');
            $table->tinyInteger('is_default')->unsigned()->default(0)->comment('是否默认车辆');
            $table->timestamp('bound_at')->nullable()->comment('绑定时间');
            $table->timestamp('unbound_at')->nullable()->comment('解绑时间');
            $table->timestamps();
            $table->index('driver_id');
            $table->index('vehicle_id');
            $table->comment('司机车辆绑定表');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_vehicles');
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('drivers');
        Schema::dropIfExists('merchants');
        Schema::dropIfExists('delivery_routes');
        Schema::dropIfExists('suppliers');
    }
};
