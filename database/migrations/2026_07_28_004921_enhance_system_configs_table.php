<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. 增强字段
        Schema::table('system_configs', function (Blueprint $table) {
            $table->string('default_value', 255)->nullable()->after('config_value')->comment('默认值（重置用）');
            $table->string('config_type', 20)->default('string')->after('default_value')->comment('值类型：boolean/integer/decimal/string/enum/json');
            $table->string('config_group', 50)->default('basic')->after('config_type')->comment('分组：basic/order/delivery/finance/inventory/audit');
            $table->string('label', 100)->nullable()->after('config_group')->comment('中文显示名');
            $table->string('hint', 255)->nullable()->after('label')->comment('输入提示');
            $table->json('options')->nullable()->after('hint')->comment('枚举选项 [{"label":"选项名","value":"值"},...]');
            $table->string('validation_rules', 255)->nullable()->after('options')->comment('校验规则：min:0|max:999|required|integer');
            $table->integer('sort_order')->default(0)->after('validation_rules')->comment('组内排序');
            $table->boolean('is_public')->default(false)->after('sort_order')->comment('是否前端可读（无需登录）');
            $table->boolean('is_readonly')->default(false)->after('is_public')->comment('是否只读（代码写入，不允许管理后台改）');
        });

        // 2. 更新已有 6 条初始数据
        DB::table('system_configs')->where('config_key', 'site_name')->update([
            'default_value' => '本地速送服务平台',
            'config_type' => 'string',
            'config_group' => 'basic',
            'label' => '站点名称',
            'validation_rules' => 'required|max:50',
            'sort_order' => 1,
        ]);
        DB::table('system_configs')->where('config_key', 'contact_phone')->update([
            'default_value' => '15690631151',
            'config_type' => 'string',
            'config_group' => 'basic',
            'label' => '客服电话',
            'validation_rules' => 'required|max:20',
            'sort_order' => 2,
        ]);
        DB::table('system_configs')->where('config_key', 'default_delivery_batch')->update([
            'default_value' => '1',
            'config_type' => 'enum',
            'config_group' => 'delivery',
            'label' => '默认配送批次',
            'options' => json_encode([['label' => '上午', 'value' => '1'], ['label' => '下午', 'value' => '2']]),
            'sort_order' => 10,
        ]);
        DB::table('system_configs')->where('config_key', 'weighing_diff_threshold')->update([
            'default_value' => '20',
            'config_type' => 'integer',
            'config_group' => 'inventory',
            'label' => '称重差异阈值（%）',
            'hint' => '称重差异超过此百分比需人工确认',
            'validation_rules' => 'required|integer|min:1|max:100',
            'sort_order' => 20,
        ]);
        DB::table('system_configs')->where('config_key', 'audit_retention_days')->update([
            'default_value' => '90',
            'config_type' => 'integer',
            'config_group' => 'audit',
            'label' => '审计日志保留天数',
            'hint' => '0=永久保留，1-180天，到期每日定时清理',
            'validation_rules' => 'required|integer|min:0|max:180',
            'sort_order' => 50,
        ]);
        DB::table('system_configs')->where('config_key', 'loss_approval_threshold')->update([
            'default_value' => '200',
            'config_type' => 'integer',
            'config_group' => 'audit',
            'label' => '损耗审批阈值（元）',
            'hint' => '单张损耗单金额超过此值需运营经理审核，未超阈值直接执行',
            'validation_rules' => 'required|integer|min:0',
            'sort_order' => 51,
        ]);

        // 3. 新增业务配置项
        $now = now();
        $newConfigs = [
            // ── 订单配置 ──────────────────────────
            [
                'config_key' => 'order_auto_confirm_hours',
                'config_value' => '24',
                'default_value' => '24',
                'config_type' => 'integer',
                'config_group' => 'order',
                'label' => '自动确认收货时长（小时）',
                'hint' => '超过此时长未签收将自动确认',
                'options' => null,
                'validation_rules' => 'required|integer|min:1|max:168',
                'sort_order' => 3,
                'is_public' => false,
                'is_readonly' => false,
                'description' => '订单配送完成后的自动签收等待时长',
            ],
            [
                'config_key' => 'min_delivery_amount',
                'config_value' => '0',
                'default_value' => '0',
                'config_type' => 'integer',
                'config_group' => 'order',
                'label' => '最低起送金额（元）',
                'hint' => '0表示无限制',
                'options' => null,
                'validation_rules' => 'required|integer|min:0',
                'sort_order' => 4,
                'is_public' => true,
                'is_readonly' => false,
                'description' => '商家下单金额门槛',
            ],
            [
                'config_key' => 'allow_merchant_self_order',
                'config_value' => '1',
                'default_value' => '1',
                'config_type' => 'boolean',
                'config_group' => 'order',
                'label' => '允许商家自助下单',
                'hint' => '关闭后商家只能由运营代下单',
                'options' => null,
                'validation_rules' => 'required|boolean',
                'sort_order' => 5,
                'is_public' => true,
                'is_readonly' => false,
                'description' => '商家端小程序是否允许自主下单',
            ],

            // ── 配送配置 ──────────────────────────
            [
                'config_key' => 'delivery_timeout_minutes',
                'config_value' => '30',
                'default_value' => '30',
                'config_type' => 'integer',
                'config_group' => 'delivery',
                'label' => '配送超时标记时长（分钟）',
                'hint' => '超过此时长未完成配送将标记为异常',
                'options' => null,
                'validation_rules' => 'required|integer|min:10|max:180',
                'sort_order' => 11,
                'is_public' => false,
                'is_readonly' => false,
                'description' => '配送任务超时自动标记异常',
            ],
            [
                'config_key' => 'allow_driver_multi_task',
                'config_value' => '1',
                'default_value' => '1',
                'config_type' => 'boolean',
                'config_group' => 'delivery',
                'label' => '允许司机同时接多单',
                'hint' => '关闭后司机同时只能执行一个配送任务',
                'options' => null,
                'validation_rules' => 'required|boolean',
                'sort_order' => 12,
                'is_public' => false,
                'is_readonly' => false,
                'description' => '司机并发配送开关',
            ],

            // ── 财务风控 ──────────────────────────
            [
                'config_key' => 'max_daily_recharge_amount',
                'config_value' => '50000',
                'default_value' => '50000',
                'config_type' => 'integer',
                'config_group' => 'finance',
                'label' => '单日最大充值金额（元）',
                'hint' => '单商家每日充值累计上限',
                'options' => null,
                'validation_rules' => 'required|integer|min:1000',
                'sort_order' => 20,
                'is_public' => true,
                'is_readonly' => false,
                'description' => '商家充值风控限额',
            ],
            [
                'config_key' => 'credit_limit_default',
                'config_value' => '5000',
                'default_value' => '5000',
                'config_type' => 'integer',
                'config_group' => 'finance',
                'label' => '新商家默认信用额度（元）',
                'hint' => '新注册商家自动分配的信用额度',
                'options' => null,
                'validation_rules' => 'required|integer|min:0',
                'sort_order' => 21,
                'is_public' => false,
                'is_readonly' => false,
                'description' => '新商家初始信用额度',
            ],
            [
                'config_key' => 'enable_weighing_auto_debit',
                'config_value' => '0',
                'default_value' => '0',
                'config_type' => 'boolean',
                'config_group' => 'finance',
                'label' => '称重差异自动扣款',
                'hint' => '开启后称重差异在阈值内自动扣款，无需人工确认',
                'options' => null,
                'validation_rules' => 'required|boolean',
                'sort_order' => 22,
                'is_public' => false,
                'is_readonly' => false,
                'description' => '称重差异处理方式：自动扣款或人工确认',
            ],

            // ── 库存配置 ──────────────────────────
            [
                'config_key' => 'inventory_warning_enabled',
                'config_value' => '1',
                'default_value' => '1',
                'config_type' => 'boolean',
                'config_group' => 'inventory',
                'label' => '启用库存预警',
                'hint' => '开启后低于预警值触发通知',
                'options' => null,
                'validation_rules' => 'required|boolean',
                'sort_order' => 30,
                'is_public' => false,
                'is_readonly' => false,
                'description' => '库存预警检测开关',
            ],
            [
                'config_key' => 'inventory_warning_interval_minutes',
                'config_value' => '5',
                'default_value' => '5',
                'config_type' => 'integer',
                'config_group' => 'inventory',
                'label' => '库存预警检测频率（分钟）',
                'hint' => '定时任务检测间隔',
                'options' => null,
                'validation_rules' => 'required|integer|min:1|max:60',
                'sort_order' => 31,
                'is_public' => false,
                'is_readonly' => false,
                'description' => '库存预警定时检测周期',
            ],

            // ── 基础配置补充 ──────────────────────
            [
                'config_key' => 'max_upload_size_mb',
                'config_value' => '20',
                'default_value' => '20',
                'config_type' => 'integer',
                'config_group' => 'basic',
                'label' => '文件上传大小限制（MB）',
                'hint' => '单文件上传最大体积',
                'options' => null,
                'validation_rules' => 'required|integer|min:1|max:100',
                'sort_order' => 6,
                'is_public' => false,
                'is_readonly' => false,
                'description' => '管理后台和商家端文件上传限制',
            ],
        ];

        foreach ($newConfigs as &$config) {
            $config['created_at'] = $now;
            $config['updated_at'] = $now;
        }
        DB::table('system_configs')->insert($newConfigs);
    }

    public function down(): void
    {
        // 1. 删除新增的配置项
        DB::table('system_configs')->whereIn('config_key', [
            'order_auto_confirm_hours', 'min_delivery_amount', 'allow_merchant_self_order',
            'delivery_timeout_minutes', 'allow_driver_multi_task',
            'max_daily_recharge_amount', 'credit_limit_default', 'enable_weighing_auto_debit',
            'inventory_warning_enabled', 'inventory_warning_interval_minutes',
            'max_upload_size_mb',
        ])->delete();

        // 2. 还原已有数据的增强字段为 null
        DB::table('system_configs')->update([
            'default_value' => null,
            'config_type' => 'string',
            'config_group' => 'basic',
            'label' => null,
            'hint' => null,
            'options' => null,
            'validation_rules' => null,
            'sort_order' => 0,
            'is_public' => false,
            'is_readonly' => false,
        ]);

        // 3. 删除增强字段
        Schema::table('system_configs', function (Blueprint $table) {
            $table->dropColumn([
                'default_value', 'config_type', 'config_group', 'label', 'hint',
                'options', 'validation_rules', 'sort_order', 'is_public', 'is_readonly',
            ]);
        });
    }
};
