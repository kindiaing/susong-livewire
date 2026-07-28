<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now()->toDateTimeString();

        DB::table('system_configs')->insert([
            // ── 界面设置 ──────────────────────────
            [
                'config_key' => 'ui_close_on_outside',
                'config_value' => '1',
                'default_value' => '1',
                'config_type' => 'boolean',
                'config_group' => 'ui',
                'label' => '点击旁边关闭通知',
                'hint' => '开启后，点击通知面板外的区域将自动关闭通知菜单',
                'description' => '控制点击通知 Drawer 外部区域时是否自动关闭面板。关闭此选项后，只能通过点击关闭按钮或按 ESC 键关闭通知面板。',
                'options' => null,
                'validation_rules' => null,
                'sort_order' => 1,
                'is_public' => 1,
                'is_readonly' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'config_key' => 'ui_show_footer',
                'config_value' => '1',
                'default_value' => '1',
                'config_type' => 'boolean',
                'config_group' => 'ui',
                'label' => '显示底部版权栏',
                'hint' => '关闭后页面底部不显示版权信息和技术栈信息',
                'description' => '控制管理后台页面底部版权栏的显示与隐藏。底部展示技术栈、备案号、开发者等信息。',
                'options' => null,
                'validation_rules' => null,
                'sort_order' => 2,
                'is_public' => 1,
                'is_readonly' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // ── 基础设置补充 ──────────────────────
            [
                'config_key' => 'site_icp_number',
                'config_value' => '',
                'default_value' => '',
                'config_type' => 'string',
                'config_group' => 'basic',
                'label' => 'ICP 备案号',
                'hint' => '网站 ICP 备案号，留空不显示，如：京ICP备2026XXXXX号',
                'description' => '显示在页面底部的 ICP 备案号。留空则不显示备案信息。',
                'options' => null,
                'validation_rules' => 'max:50',
                'sort_order' => 7,
                'is_public' => 1,
                'is_readonly' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('system_configs')->whereIn('config_key', [
            'ui_close_on_outside',
            'ui_show_footer',
            'site_icp_number',
        ])->delete();
    }
};
