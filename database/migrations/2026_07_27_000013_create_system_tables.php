<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 系统配置表（含增强字段）
        Schema::create('system_configs', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->string('config_key', 100)->unique()->comment('配置键');
            $table->text('config_value')->nullable()->comment('配置值');
            $table->string('default_value', 255)->nullable()->comment('默认值（重置用）');
            $table->string('config_type', 20)->default('string')->comment('值类型：boolean/integer/decimal/string/enum/json');
            $table->string('config_group', 50)->default('basic')->comment('分组：basic/order/delivery/finance/inventory/audit/ui');
            $table->string('label', 100)->nullable()->comment('中文显示名');
            $table->string('hint', 255)->nullable()->comment('输入提示');
            $table->json('options')->nullable()->comment('枚举选项 [{"label":"选项名","value":"值"},...]');
            $table->string('validation_rules', 255)->nullable()->comment('校验规则：min:0|max:999|required|integer');
            $table->integer('sort_order')->default(0)->comment('组内排序');
            $table->boolean('is_public')->default(false)->comment('是否前端可读（无需登录）');
            $table->boolean('is_readonly')->default(false)->comment('是否只读（代码写入，不允许管理后台改）');
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

        // 内置系统配置（23 条，含分组、类型、排序等完整字段）
        $now = now();
        DB::table('system_configs')->insert([
            // ── 基础配置 ──────────────────────────
            ['config_key' => 'site_name', 'config_value' => '本地速送服务平台', 'default_value' => '本地速送服务平台', 'config_type' => 'string', 'config_group' => 'basic', 'label' => '站点名称', 'hint' => null, 'options' => null, 'validation_rules' => 'required|max:50', 'sort_order' => 1, 'is_public' => 0, 'is_readonly' => 0, 'description' => '站点名称', 'created_at' => $now, 'updated_at' => $now],
            ['config_key' => 'contact_phone', 'config_value' => '15690631151', 'default_value' => '15690631151', 'config_type' => 'string', 'config_group' => 'basic', 'label' => '客服电话', 'hint' => null, 'options' => null, 'validation_rules' => 'required|max:20', 'sort_order' => 2, 'is_public' => 0, 'is_readonly' => 0, 'description' => '客服电话', 'created_at' => $now, 'updated_at' => $now],
            ['config_key' => 'max_upload_size_mb', 'config_value' => '20', 'default_value' => '20', 'config_type' => 'integer', 'config_group' => 'basic', 'label' => '文件上传大小限制（MB）', 'hint' => '单文件上传最大体积', 'options' => null, 'validation_rules' => 'required|integer|min:1|max:100', 'sort_order' => 6, 'is_public' => 0, 'is_readonly' => 0, 'description' => '管理后台和商家端文件上传限制', 'created_at' => $now, 'updated_at' => $now],
            ['config_key' => 'site_icp_number', 'config_value' => '', 'default_value' => '', 'config_type' => 'string', 'config_group' => 'basic', 'label' => 'ICP 备案号', 'hint' => '网站 ICP 备案号，留空不显示', 'options' => null, 'validation_rules' => 'max:50', 'sort_order' => 7, 'is_public' => 1, 'is_readonly' => 0, 'description' => '显示在页面底部的 ICP 备案号', 'created_at' => $now, 'updated_at' => $now],
            ['config_key' => 'site_tech_stack_url', 'config_value' => 'https://laravel.com', 'default_value' => 'https://laravel.com', 'config_type' => 'string', 'config_group' => 'basic', 'label' => '技术栈链接', 'hint' => '底部版权栏"技术栈"文字的跳转链接', 'options' => null, 'validation_rules' => 'url|max:255', 'sort_order' => 8, 'is_public' => 1, 'is_readonly' => 0, 'description' => '点击底部版权栏中的技术栈文字时跳转的 URL', 'created_at' => $now, 'updated_at' => $now],
            ['config_key' => 'site_developer_name', 'config_value' => 'Seeding', 'default_value' => 'Seeding', 'config_type' => 'string', 'config_group' => 'basic', 'label' => '开发者名称', 'hint' => '底部版权栏显示的开发者名称', 'options' => null, 'validation_rules' => 'max:50', 'sort_order' => 9, 'is_public' => 1, 'is_readonly' => 0, 'description' => '显示在页面底部版权栏中的开发者名称', 'created_at' => $now, 'updated_at' => $now],
            ['config_key' => 'site_developer_url', 'config_value' => '', 'default_value' => '', 'config_type' => 'string', 'config_group' => 'basic', 'label' => '开发者链接', 'hint' => '底部版权栏"开发者名称"的跳转链接，留空不可点击', 'options' => null, 'validation_rules' => 'nullable|url|max:255', 'sort_order' => 10, 'is_public' => 1, 'is_readonly' => 0, 'description' => '点击底部版权栏中的开发者名称时跳转的 URL', 'created_at' => $now, 'updated_at' => $now],
            ['config_key' => 'site_icp_url', 'config_value' => 'https://beian.miit.gov.cn/', 'default_value' => 'https://beian.miit.gov.cn/', 'config_type' => 'string', 'config_group' => 'basic', 'label' => '备案号链接', 'hint' => '底部版权栏"ICP备案号"的跳转链接', 'options' => null, 'validation_rules' => 'url|max:255', 'sort_order' => 11, 'is_public' => 1, 'is_readonly' => 0, 'description' => '点击底部版权栏中的备案号时跳转的 URL', 'created_at' => $now, 'updated_at' => $now],

            // ── 订单配置 ──────────────────────────
            ['config_key' => 'order_auto_confirm_hours', 'config_value' => '24', 'default_value' => '24', 'config_type' => 'integer', 'config_group' => 'order', 'label' => '自动确认收货时长（小时）', 'hint' => '超过此时长未签收将自动确认', 'options' => null, 'validation_rules' => 'required|integer|min:1|max:168', 'sort_order' => 3, 'is_public' => 0, 'is_readonly' => 0, 'description' => '订单配送完成后的自动签收等待时长', 'created_at' => $now, 'updated_at' => $now],
            ['config_key' => 'min_delivery_amount', 'config_value' => '0', 'default_value' => '0', 'config_type' => 'integer', 'config_group' => 'order', 'label' => '最低起送金额（元）', 'hint' => '0表示无限制', 'options' => null, 'validation_rules' => 'required|integer|min:0', 'sort_order' => 4, 'is_public' => 1, 'is_readonly' => 0, 'description' => '商家下单金额门槛', 'created_at' => $now, 'updated_at' => $now],
            ['config_key' => 'allow_merchant_self_order', 'config_value' => '1', 'default_value' => '1', 'config_type' => 'boolean', 'config_group' => 'order', 'label' => '允许商家自助下单', 'hint' => '关闭后商家只能由运营代下单', 'options' => null, 'validation_rules' => 'required|boolean', 'sort_order' => 5, 'is_public' => 1, 'is_readonly' => 0, 'description' => '商家端小程序是否允许自主下单', 'created_at' => $now, 'updated_at' => $now],

            // ── 配送配置 ──────────────────────────
            ['config_key' => 'default_delivery_batch', 'config_value' => '1', 'default_value' => '1', 'config_type' => 'enum', 'config_group' => 'delivery', 'label' => '默认配送批次', 'hint' => null, 'options' => json_encode([['label' => '上午', 'value' => '1'], ['label' => '下午', 'value' => '2']]), 'validation_rules' => null, 'sort_order' => 10, 'is_public' => 0, 'is_readonly' => 0, 'description' => '默认配送批次：1上午，2下午', 'created_at' => $now, 'updated_at' => $now],
            ['config_key' => 'delivery_timeout_minutes', 'config_value' => '30', 'default_value' => '30', 'config_type' => 'integer', 'config_group' => 'delivery', 'label' => '配送超时标记时长（分钟）', 'hint' => '超过此时长未完成配送将标记为异常', 'options' => null, 'validation_rules' => 'required|integer|min:10|max:180', 'sort_order' => 11, 'is_public' => 0, 'is_readonly' => 0, 'description' => '配送任务超时自动标记异常', 'created_at' => $now, 'updated_at' => $now],
            ['config_key' => 'allow_driver_multi_task', 'config_value' => '1', 'default_value' => '1', 'config_type' => 'boolean', 'config_group' => 'delivery', 'label' => '允许司机同时接多单', 'hint' => '关闭后司机同时只能执行一个配送任务', 'options' => null, 'validation_rules' => 'required|boolean', 'sort_order' => 12, 'is_public' => 0, 'is_readonly' => 0, 'description' => '司机并发配送开关', 'created_at' => $now, 'updated_at' => $now],

            // ── 财务风控 ──────────────────────────
            ['config_key' => 'max_daily_recharge_amount', 'config_value' => '50000', 'default_value' => '50000', 'config_type' => 'integer', 'config_group' => 'finance', 'label' => '单日最大充值金额（元）', 'hint' => '单商家每日充值累计上限', 'options' => null, 'validation_rules' => 'required|integer|min:1000', 'sort_order' => 20, 'is_public' => 1, 'is_readonly' => 0, 'description' => '商家充值风控限额', 'created_at' => $now, 'updated_at' => $now],
            ['config_key' => 'credit_limit_default', 'config_value' => '5000', 'default_value' => '5000', 'config_type' => 'integer', 'config_group' => 'finance', 'label' => '新商家默认信用额度（元）', 'hint' => '新注册商家自动分配的信用额度', 'options' => null, 'validation_rules' => 'required|integer|min:0', 'sort_order' => 21, 'is_public' => 0, 'is_readonly' => 0, 'description' => '新商家初始信用额度', 'created_at' => $now, 'updated_at' => $now],
            ['config_key' => 'enable_weighing_auto_debit', 'config_value' => '0', 'default_value' => '0', 'config_type' => 'boolean', 'config_group' => 'finance', 'label' => '称重差异自动扣款', 'hint' => '开启后称重差异在阈值内自动扣款，无需人工确认', 'options' => null, 'validation_rules' => 'required|boolean', 'sort_order' => 22, 'is_public' => 0, 'is_readonly' => 0, 'description' => '称重差异处理方式', 'created_at' => $now, 'updated_at' => $now],

            // ── 库存配置 ──────────────────────────
            ['config_key' => 'weighing_diff_threshold', 'config_value' => '20', 'default_value' => '20', 'config_type' => 'integer', 'config_group' => 'inventory', 'label' => '称重差异阈值（%）', 'hint' => '称重差异超过此百分比需人工确认', 'options' => null, 'validation_rules' => 'required|integer|min:1|max:100', 'sort_order' => 20, 'is_public' => 0, 'is_readonly' => 0, 'description' => '称重差异阈值（百分比）', 'created_at' => $now, 'updated_at' => $now],
            ['config_key' => 'inventory_warning_enabled', 'config_value' => '1', 'default_value' => '1', 'config_type' => 'boolean', 'config_group' => 'inventory', 'label' => '启用库存预警', 'hint' => '开启后低于预警值触发通知', 'options' => null, 'validation_rules' => 'required|boolean', 'sort_order' => 30, 'is_public' => 0, 'is_readonly' => 0, 'description' => '库存预警检测开关', 'created_at' => $now, 'updated_at' => $now],
            ['config_key' => 'inventory_warning_interval_minutes', 'config_value' => '5', 'default_value' => '5', 'config_type' => 'integer', 'config_group' => 'inventory', 'label' => '库存预警检测频率（分钟）', 'hint' => '定时任务检测间隔', 'options' => null, 'validation_rules' => 'required|integer|min:1|max:60', 'sort_order' => 31, 'is_public' => 0, 'is_readonly' => 0, 'description' => '库存预警定时检测周期', 'created_at' => $now, 'updated_at' => $now],

            // ── 审计配置 ──────────────────────────
            ['config_key' => 'audit_retention_days', 'config_value' => '90', 'default_value' => '90', 'config_type' => 'integer', 'config_group' => 'audit', 'label' => '审计日志保留天数', 'hint' => '0=永久保留，1-180天，到期每日定时清理', 'options' => null, 'validation_rules' => 'required|integer|min:0|max:180', 'sort_order' => 50, 'is_public' => 0, 'is_readonly' => 0, 'description' => '审计/日志保留天数', 'created_at' => $now, 'updated_at' => $now],
            ['config_key' => 'loss_approval_threshold', 'config_value' => '200', 'default_value' => '200', 'config_type' => 'integer', 'config_group' => 'audit', 'label' => '损耗审批阈值（元）', 'hint' => '单张损耗单金额超过此值需运营经理审核', 'options' => null, 'validation_rules' => 'required|integer|min:0', 'sort_order' => 51, 'is_public' => 0, 'is_readonly' => 0, 'description' => '损耗审批阈值（元）', 'created_at' => $now, 'updated_at' => $now],

            // ── 界面配置 ──────────────────────────
            ['config_key' => 'ui_close_on_outside', 'config_value' => '1', 'default_value' => '1', 'config_type' => 'boolean', 'config_group' => 'ui', 'label' => '点击旁边关闭通知', 'hint' => '开启后，点击通知面板外的区域将自动关闭通知菜单', 'options' => null, 'validation_rules' => null, 'sort_order' => 1, 'is_public' => 1, 'is_readonly' => 0, 'description' => '控制点击通知 Drawer 外部区域时是否自动关闭面板', 'created_at' => $now, 'updated_at' => $now],
            ['config_key' => 'per_page', 'config_value' => '10', 'default_value' => '10', 'config_type' => 'integer', 'config_group' => 'ui', 'label' => '列表每页条数', 'hint' => '管理后台列表页默认每页显示条数', 'options' => json_encode([['label' => '10条/页', 'value' => '10'], ['label' => '15条/页', 'value' => '15'], ['label' => '20条/页', 'value' => '20'], ['label' => '50条/页', 'value' => '50']]), 'validation_rules' => 'required|integer|in:10,15,20,50', 'sort_order' => 2, 'is_public' => 0, 'is_readonly' => 0, 'description' => '列表页分页条数，全局生效', 'created_at' => $now, 'updated_at' => $now],
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
