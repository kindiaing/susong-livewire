<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 价格策略主表
        Schema::create('price_strategies', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->string('name', 100)->comment('策略名称');
            $table->string('code', 50)->nullable()->unique()->comment('策略编码');
            $table->tinyInteger('type')->unsigned()->default(1)->comment('类型：1促销，2临时改价');
            $table->tinyInteger('target_type')->unsigned()->default(1)->comment('作用对象：1供应商，2商家，3全部');
            $table->tinyInteger('scope')->unsigned()->default(3)->comment('作用范围：1采购，2销售，3通用');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：0禁用，1启用');
            $table->tinyInteger('approval_status')->unsigned()->default(1)->comment('审核状态：1待审核，2已通过，3已拒绝');
            $table->timestamp('start_at')->nullable()->comment('生效开始时间');
            $table->timestamp('end_at')->nullable()->comment('生效结束时间');
            $table->unsignedBigInteger('created_by')->nullable()->comment('创建人ID');
            $table->text('remark')->nullable()->comment('备注');
            $table->timestamps();
            $table->softDeletes();

            $table->index('type');
            $table->index('target_type');
            $table->index('status');
            $table->index('approval_status');
            $table->index(['start_at', 'end_at']);
            $table->comment('价格策略主表');
        });

        // 价格策略明细表
        Schema::create('price_strategy_items', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('price_strategy_id')->comment('价格策略ID');
            $table->unsignedBigInteger('target_id')->default(0)->comment('作用对象ID：supplier_id/merchant_id，0表示全部');
            $table->unsignedBigInteger('category_id')->nullable()->comment('商品分类ID');
            $table->unsignedBigInteger('product_id')->nullable()->comment('商品ID');
            $table->unsignedBigInteger('sku_id')->nullable()->comment('SKU ID');
            $table->tinyInteger('price_type')->unsigned()->default(1)->comment('价格类型：1固定价，2折扣率，3成本加权');
            $table->bigInteger('price_value')->default(0)->comment('固定价格');
            $table->integer('discount_rate')->default(10000)->comment('折扣率%');
            $table->integer('cost_weight_rate')->default(10000)->comment('成本加权率%');
            $table->bigInteger('min_quantity')->default(0)->comment('最小起量');
            $table->timestamp('effective_start_at')->nullable()->comment('明细生效开始时间');
            $table->timestamp('effective_end_at')->nullable()->comment('明细生效结束时间');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：0禁用，1启用');
            $table->timestamps();

            $table->index('price_strategy_id');
            $table->index('target_id');
            $table->index('category_id');
            $table->index('product_id');
            $table->index('sku_id');
            $table->index(['effective_start_at', 'effective_end_at']);
            $table->comment('价格策略明细表');
        });

        // 改价/促销记录表
        Schema::create('price_change_logs', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->tinyInteger('source_type')->unsigned()->default(1)->comment('来源：1促销，2临时改价，3手动改价');
            $table->unsignedBigInteger('source_id')->nullable()->comment('来源策略ID');
            $table->tinyInteger('target_type')->unsigned()->default(1)->comment('作用单据：1订单，2采购单，3应收，4应付');
            $table->unsignedBigInteger('target_id')->comment('单据ID');
            $table->unsignedBigInteger('target_item_id')->nullable()->comment('单据明细ID');
            $table->bigInteger('original_price')->default(0)->comment('改价前单价');
            $table->bigInteger('new_price')->default(0)->comment('改价后单价');
            $table->bigInteger('quantity')->default(0)->comment('数量');
            $table->bigInteger('amount_diff')->default(0)->comment('金额差异');
            $table->unsignedBigInteger('operator_id')->nullable()->comment('操作人ID');
            $table->json('role_ids')->nullable()->comment('操作人角色ID数组');
            $table->string('reason', 255)->nullable()->comment('改价原因');
            $table->json('before_data')->nullable()->comment('改价前数据');
            $table->json('after_data')->nullable()->comment('改价后数据');
            $table->timestamp('created_at')->nullable()->comment('创建时间');

            $table->index('source_type');
            $table->index('source_id');
            $table->index('target_type');
            $table->index('target_id');
            $table->index('target_item_id');
            $table->index('operator_id');
            $table->comment('改价/促销记录表');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_change_logs');
        Schema::dropIfExists('price_strategy_items');
        Schema::dropIfExists('price_strategies');
    }
};
