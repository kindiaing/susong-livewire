<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('system_configs')->where('config_key', 'ui_show_footer')->delete();
    }

    public function down(): void
    {
        $now = now()->toDateTimeString();

        DB::table('system_configs')->insert([
            'config_key' => 'ui_show_footer',
            'config_value' => '1',
            'default_value' => '1',
            'config_type' => 'boolean',
            'config_group' => 'ui',
            'label' => '显示底部版权栏',
            'hint' => '关闭后页面底部不显示版权信息和技术栈信息',
            'description' => '控制管理后台页面底部版权栏的显示与隐藏。底部版权栏已移至首页，此配置不再使用。',
            'options' => null,
            'validation_rules' => null,
            'sort_order' => 2,
            'is_public' => 1,
            'is_readonly' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
};
