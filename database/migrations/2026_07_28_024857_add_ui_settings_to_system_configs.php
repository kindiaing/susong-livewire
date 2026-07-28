<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now()->toDateTimeString();

        DB::table('system_configs')->insert([
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
        ]);
    }

    public function down(): void
    {
        DB::table('system_configs')->where('config_key', 'ui_close_on_outside')->delete();
    }
};
