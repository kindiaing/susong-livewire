<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * 断链修复：退货出库→库存扣减 + 入库差异→损耗单
 *
 * 变更内容：
 * 1. purchase_orders 新增 return_status（退货状态）
 * 2. purchase_order_items 新增 discrepancy_quantity（差异数量）、loss_order_id（关联损耗单）
 * 3. loss_orders 新增 source_type（来源类型）、source_id（来源业务ID）
 * 4. loss_order_items 新增 purchase_order_item_id、purchase_order_id
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. purchase_orders 新增 return_status
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->tinyInteger('return_status')->unsigned()->default(0)->after('status')
                ->comment('退货状态：0无退货，1部分退货，2全部退货');
        });

        // 2. purchase_order_items 新增字段
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->bigInteger('discrepancy_quantity')->default(0)->after('discrepancy_reason')
                ->comment('入库差异数量（采购数量-实际入库数量）');
            $table->unsignedBigInteger('loss_order_id')->nullable()->after('discrepancy_quantity')
                ->comment('关联损耗单ID');

            $table->foreign('loss_order_id')->references('id')->on('loss_orders')->nullOnDelete();
        });

        // 3. loss_orders 新增来源追踪字段
        Schema::table('loss_orders', function (Blueprint $table) {
            $table->string('source_type', 50)->nullable()->after('loss_no')
                ->comment('来源类型：purchase_order=采购入库差异');
            $table->unsignedBigInteger('source_id')->nullable()->after('source_type')
                ->comment('来源业务ID（如采购单ID）');

            $table->index(['source_type', 'source_id']);
        });

        // 4. loss_order_items 新增采购关联字段
        Schema::table('loss_order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_order_item_id')->nullable()->after('loss_order_id')
                ->comment('采购单明细ID（入库差异来源）');
            $table->unsignedBigInteger('purchase_order_id')->nullable()->after('purchase_order_item_id')
                ->comment('采购单ID（入库差异来源）');

            $table->index('purchase_order_item_id');
            $table->index('purchase_order_id');
        });

        // 5. 新增系统配置：入库差异自动创建损耗单
        $now = now();
        DB::table('system_configs')->insert([
            'config_key' => 'stockin_auto_create_loss',
            'config_value' => '1',
            'default_value' => '1',
            'config_type' => 'boolean',
            'config_group' => 'inventory',
            'label' => '入库差异自动创建损耗单',
            'hint' => '开启后采购入库差异自动生成损耗单，关闭则需手动创建',
            'options' => null,
            'validation_rules' => 'required|boolean',
            'sort_order' => 32,
            'is_public' => false,
            'is_readonly' => false,
            'description' => '采购入库数量少于采购数量时，自动创建损耗单扣减差异库存',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 清除系统配置缓存
        Cache::forget('system_configs_all');
    }

    public function down(): void
    {
        // 5 删除系统配置
        DB::table('system_configs')->where('config_key', 'stockin_auto_create_loss')->delete();
        Cache::forget('system_configs_all');

        // 4
        Schema::table('loss_order_items', function (Blueprint $table) {
            $table->dropIndex(['purchase_order_item_id']);
            $table->dropIndex(['purchase_order_id']);
            $table->dropColumn(['purchase_order_item_id', 'purchase_order_id']);
        });

        // 3
        Schema::table('loss_orders', function (Blueprint $table) {
            $table->dropIndex(['source_type', 'source_id']);
            $table->dropColumn(['source_type', 'source_id']);
        });

        // 2
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->dropForeign(['loss_order_id']);
            $table->dropColumn(['discrepancy_quantity', 'loss_order_id']);
        });

        // 1
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['return_status']);
        });
    }
};
