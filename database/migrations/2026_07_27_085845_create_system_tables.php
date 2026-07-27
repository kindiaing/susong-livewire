<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 系统配置表
        Schema::create('system_configs', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->string('config_key', 100)->unique()->comment('配置键');
            $table->text('config_value')->nullable()->comment('配置值');
            $table->string('description', 255)->nullable()->comment('说明');
            $table->timestamps();

            $table->comment('系统配置表');
        });

        // 轮播广告表
        Schema::create('banners', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->string('title', 100)->comment('标题');
            $table->string('image_url', 255)->comment('图片地址');
            $table->string('link_url', 255)->nullable()->comment('跳转链接');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：0禁用，1启用');
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('sort');
            $table->comment('轮播广告表');
        });

        // 运营主推表
        Schema::create('promotions', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->tinyInteger('type')->unsigned()->default(1)->comment('类型：1主推商品，2主推品类');
            $table->unsignedBigInteger('target_id')->comment('目标ID');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->timestamp('start_at')->nullable()->comment('开始时间');
            $table->timestamp('end_at')->nullable()->comment('结束时间');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态');
            $table->timestamps();

            $table->index('type');
            $table->index('target_id');
            $table->index('status');
            $table->comment('运营主推表');
        });

        // 操作日志表
        Schema::create('operation_logs', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('user_id')->nullable()->comment('操作人ID');
            $table->string('username', 50)->nullable()->comment('操作人用户名');
            $table->string('method', 10)->nullable()->comment('请求方法');
            $table->string('path', 255)->nullable()->comment('请求路径');
            $table->string('ip', 50)->nullable()->comment('IP地址');
            $table->text('content')->nullable()->comment('操作内容');
            $table->timestamp('created_at')->nullable()->comment('创建时间');

            $table->index('user_id');
            $table->index('created_at');
            $table->comment('操作日志表');
        });

        // 审计日志表
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->string('model_type', 100)->comment('模型类型');
            $table->unsignedBigInteger('model_id')->comment('模型ID');
            $table->string('action', 50)->comment('操作动作');
            $table->json('before_data')->nullable()->comment('修改前数据');
            $table->json('after_data')->nullable()->comment('修改后数据');
            $table->unsignedBigInteger('operator_id')->nullable()->comment('操作人ID');
            $table->string('ip', 50)->nullable()->comment('操作人IP地址');
            $table->string('user_agent', 255)->nullable()->comment('浏览器/客户端UA');
            $table->string('reason', 255)->nullable()->comment('操作原因');
            $table->string('relation_type', 50)->nullable()->comment('关联类型');
            $table->unsignedBigInteger('relation_id')->nullable()->comment('关联ID');
            $table->timestamp('created_at')->nullable()->comment('创建时间');

            $table->index(['model_type', 'model_id']);
            $table->index('operator_id');
            $table->index('created_at');
            $table->comment('审计日志表');
        });

        // 登录日志表
        Schema::create('login_logs', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('user_id')->nullable()->comment('用户ID');
            $table->string('username', 50)->comment('登录账号');
            $table->string('ip', 50)->nullable()->comment('IP地址');
            $table->string('user_agent', 255)->nullable()->comment('浏览器/客户端UA');
            $table->tinyInteger('login_type')->unsigned()->default(1)->comment('类型：1管理后台，2商家小程序，3司机小程序');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('结果：1成功，0失败');
            $table->string('fail_reason', 100)->nullable()->comment('失败原因');
            $table->timestamp('created_at')->nullable()->comment('登录时间');

            $table->index('user_id');
            $table->index('username');
            $table->index('ip');
            $table->index('created_at');
            $table->comment('登录日志表');
        });

        // 默认系统配置
        DB::table('system_configs')->insert([
            ['config_key' => 'site_name', 'config_value' => '本地速送服务平台', 'description' => '站点名称', 'created_at' => now(), 'updated_at' => now()],
            ['config_key' => 'contact_phone', 'config_value' => '15690631151', 'description' => '客服电话', 'created_at' => now(), 'updated_at' => now()],
            ['config_key' => 'default_delivery_batch', 'config_value' => '1', 'description' => '默认配送批次：1上午，2下午', 'created_at' => now(), 'updated_at' => now()],
            ['config_key' => 'weighing_diff_threshold', 'config_value' => '20', 'description' => '称重差异阈值（百分比）', 'created_at' => now(), 'updated_at' => now()],
            ['config_key' => 'audit_retention_days', 'config_value' => '90', 'description' => '审计/日志保留天数：0=永久保留，1-180天，到期每日定时清理', 'created_at' => now(), 'updated_at' => now()],
            ['config_key' => 'loss_approval_threshold', 'config_value' => '200', 'description' => '损耗审批阈值（元）：单张损耗单金额超过此值需运营经理审核，未超阈值直接执行', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('login_logs');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('operation_logs');
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('banners');
        Schema::dropIfExists('system_configs');
    }
};
