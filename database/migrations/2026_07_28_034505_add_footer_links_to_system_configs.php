<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now()->toDateTimeString();

        DB::table('system_configs')->insert([
            // ── 底部版权栏链接配置 ──────────────────
            [
                'config_key' => 'site_tech_stack_url',
                'config_value' => 'https://laravel.com',
                'default_value' => 'https://laravel.com',
                'config_type' => 'string',
                'config_group' => 'basic',
                'label' => '技术栈链接',
                'hint' => '底部版权栏"技术栈"文字的跳转链接',
                'description' => '点击底部版权栏中的技术栈文字时跳转的 URL。可指向项目介绍页或框架官网。',
                'options' => null,
                'validation_rules' => 'url|max:255',
                'sort_order' => 8,
                'is_public' => 1,
                'is_readonly' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'config_key' => 'site_developer_name',
                'config_value' => 'Seeding',
                'default_value' => 'Seeding',
                'config_type' => 'string',
                'config_group' => 'basic',
                'label' => '开发者名称',
                'hint' => '底部版权栏显示的开发者名称',
                'description' => '显示在页面底部版权栏中的开发者名称，如"Seeding"。留空则不显示开发者信息。',
                'options' => null,
                'validation_rules' => 'max:50',
                'sort_order' => 9,
                'is_public' => 1,
                'is_readonly' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'config_key' => 'site_developer_url',
                'config_value' => '',
                'default_value' => '',
                'config_type' => 'string',
                'config_group' => 'basic',
                'label' => '开发者链接',
                'hint' => '底部版权栏"开发者名称"的跳转链接，留空则只显示文字不可点击',
                'description' => '点击底部版权栏中的开发者名称时跳转的 URL。留空则开发者名称仅显示文字，不可点击。',
                'options' => null,
                'validation_rules' => 'nullable|url|max:255',
                'sort_order' => 10,
                'is_public' => 1,
                'is_readonly' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'config_key' => 'site_icp_url',
                'config_value' => 'https://beian.miit.gov.cn/',
                'default_value' => 'https://beian.miit.gov.cn/',
                'config_type' => 'string',
                'config_group' => 'basic',
                'label' => '备案号链接',
                'hint' => '底部版权栏"ICP备案号"的跳转链接',
                'description' => '点击底部版权栏中的备案号时跳转的 URL，默认指向工信部备案查询页。',
                'options' => null,
                'validation_rules' => 'url|max:255',
                'sort_order' => 11,
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
            'site_tech_stack_url',
            'site_developer_name',
            'site_developer_url',
            'site_icp_url',
        ])->delete();
    }
};
