<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 审批记录表
        Schema::create('approvals', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->string('approval_type', 50)->comment('审核类型编码，关联 approval_type_configs.type_code');
            $table->string('target_type', 50)->comment('关联单据类型');
            $table->unsignedBigInteger('target_id')->comment('关联单据ID');
            $table->unsignedBigInteger('applicant_id')->comment('申请人ID');
            $table->string('applicant_name', 50)->comment('申请人姓名');
            $table->json('before_data')->nullable()->comment('操作前数据快照');
            $table->json('after_data')->nullable()->comment('操作后数据快照');
            $table->bigInteger('amount')->nullable()->comment('涉及金额');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：1待审核，2已通过，3已拒绝，4已撤回');
            $table->unsignedBigInteger('reviewer_id')->nullable()->comment('审核人ID');
            $table->string('reviewer_name', 50)->nullable()->comment('审核人姓名');
            $table->string('review_remark', 255)->nullable()->comment('审核备注（拒绝原因等）');
            $table->timestamp('reviewed_at')->nullable()->comment('审核时间');
            $table->timestamps();

            $table->index('approval_type');
            $table->index(['target_type', 'target_id']);
            $table->index('applicant_id');
            $table->index('status');
            $table->index('reviewer_id');
            $table->index('created_at');
            $table->comment('审批记录表');
        });

        // 审核类型配置表
        Schema::create('approval_type_configs', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->string('type_code', 50)->unique()->comment('审核类型编码（唯一）');
            $table->string('type_name', 100)->comment('审核类型名称');
            $table->string('module_name', 50)->nullable()->comment('所属模块名称');
            $table->string('risk_level', 2)->default('P1')->comment('风险等级：P0/P1');
            $table->tinyInteger('enabled')->default(0)->comment('是否启用审核：0关闭，1开启');
            $table->unsignedBigInteger('applicant_role_id')->comment('申请人角色ID');
            $table->unsignedBigInteger('reviewer_role_id')->comment('审核人角色ID');
            $table->integer('sort_order')->default(0)->comment('显示排序');
            $table->string('description', 255)->nullable()->comment('审核节点说明');
            $table->timestamps();

            $table->index('enabled');
            $table->index('reviewer_role_id');
            $table->index('applicant_role_id');
            $table->comment('审核类型配置表');
        });

        // 初始化审核类型配置
        $now = now();
        $roles = DB::table('roles')->pluck('id', 'name');

        DB::table('approval_type_configs')->insert([
            // P0 — 前10个核心资金节点，默认开启
            ['type_code' => 'manual_recharge', 'type_name' => '后台手工充值', 'module_name' => '财务对账', 'risk_level' => 'P0', 'enabled' => 1, 'applicant_role_id' => $roles['operator'], 'reviewer_role_id' => $roles['finance_manager'], 'sort_order' => 1, 'description' => '运营管理员为商家手动充值', 'created_at' => $now, 'updated_at' => $now],
            ['type_code' => 'supplier_payment', 'type_name' => '供应商付款录入', 'module_name' => '财务对账', 'risk_level' => 'P0', 'enabled' => 1, 'applicant_role_id' => $roles['cashier'], 'reviewer_role_id' => $roles['finance_manager'], 'sort_order' => 2, 'description' => '出纳录入供应商付款记录', 'created_at' => $now, 'updated_at' => $now],
            ['type_code' => 'customer_receipt', 'type_name' => '客户收款录入', 'module_name' => '财务对账', 'risk_level' => 'P0', 'enabled' => 1, 'applicant_role_id' => $roles['cashier'], 'reviewer_role_id' => $roles['finance_manager'], 'sort_order' => 3, 'description' => '出纳录入客户收款记录', 'created_at' => $now, 'updated_at' => $now],
            ['type_code' => 'credit_limit', 'type_name' => '信用额度调整', 'module_name' => '商家管理', 'risk_level' => 'P0', 'enabled' => 1, 'applicant_role_id' => $roles['operator'], 'reviewer_role_id' => $roles['finance_manager'], 'sort_order' => 4, 'description' => '修改商家信用额度', 'created_at' => $now, 'updated_at' => $now],
            ['type_code' => 'price_strategy', 'type_name' => '价格策略创建/修改', 'module_name' => '价格策略', 'risk_level' => 'P0', 'enabled' => 1, 'applicant_role_id' => $roles['operator'], 'reviewer_role_id' => $roles['finance_manager'], 'sort_order' => 5, 'description' => '创建或修改促销/临时改价策略', 'created_at' => $now, 'updated_at' => $now],
            ['type_code' => 'manual_apportion', 'type_name' => '手动均摊调整', 'module_name' => '费用均摊', 'risk_level' => 'P0', 'enabled' => 1, 'applicant_role_id' => $roles['finance'], 'reviewer_role_id' => $roles['operator_manager'], 'sort_order' => 6, 'description' => '手动修改费用均摊金额', 'created_at' => $now, 'updated_at' => $now],
            ['type_code' => 'diff_refund_deduct', 'type_name' => '差异退款/扣款决策', 'module_name' => '差异处理', 'risk_level' => 'P0', 'enabled' => 1, 'applicant_role_id' => $roles['operator'], 'reviewer_role_id' => $roles['finance_manager'], 'sort_order' => 7, 'description' => '差异处理决策为退款或扣款', 'created_at' => $now, 'updated_at' => $now],
            ['type_code' => 'sku_price_change', 'type_name' => 'SKU 批发价修改(>15%)', 'module_name' => '商品管理', 'risk_level' => 'P1', 'enabled' => 1, 'applicant_role_id' => $roles['operator'], 'reviewer_role_id' => $roles['operator_manager'], 'sort_order' => 8, 'description' => '修改SKU批发销售价幅度>15%', 'created_at' => $now, 'updated_at' => $now],
            ['type_code' => 'receivable_adjust', 'type_name' => '应收改价折扣调整', 'module_name' => '财务对账', 'risk_level' => 'P0', 'enabled' => 1, 'applicant_role_id' => $roles['operator'], 'reviewer_role_id' => $roles['finance_manager'], 'sort_order' => 9, 'description' => '改价/促销导致应收金额调整', 'created_at' => $now, 'updated_at' => $now],
            ['type_code' => 'recharge_confirm', 'type_name' => '商家充值确认', 'module_name' => '财务对账', 'risk_level' => 'P0', 'enabled' => 1, 'applicant_role_id' => $roles['operator'], 'reviewer_role_id' => $roles['finance_manager'], 'sort_order' => 10, 'description' => '商家微信/线下充值待确认', 'created_at' => $now, 'updated_at' => $now],
            // P0 — 默认关闭（退货/授权更正）
            ['type_code' => 'purchase_return', 'type_name' => '采购退货', 'module_name' => '平台统采', 'risk_level' => 'P0', 'enabled' => 0, 'applicant_role_id' => $roles['operator'], 'reviewer_role_id' => $roles['finance_manager'], 'sort_order' => 11, 'description' => '采购退货审批', 'created_at' => $now, 'updated_at' => $now],
            ['type_code' => 'after_sale_return', 'type_name' => '售后退货退款', 'module_name' => '客户直采', 'risk_level' => 'P0', 'enabled' => 0, 'applicant_role_id' => $roles['operator'], 'reviewer_role_id' => $roles['finance_manager'], 'sort_order' => 12, 'description' => '售后退货退款审批', 'created_at' => $now, 'updated_at' => $now],
            ['type_code' => 'auth_correction', 'type_name' => '单据授权更正', 'module_name' => '财务对账', 'risk_level' => 'P0', 'enabled' => 0, 'applicant_role_id' => $roles['finance'], 'reviewer_role_id' => $roles['finance_manager'], 'sort_order' => 13, 'description' => '解锁已锁定数据允许更正', 'created_at' => $now, 'updated_at' => $now],
            // P1 — 默认关闭
            ['type_code' => 'weighing_price', 'type_name' => '称重改价(≤20%)', 'module_name' => '客户直采', 'risk_level' => 'P1', 'enabled' => 0, 'applicant_role_id' => $roles['picker'], 'reviewer_role_id' => $roles['operator_manager'], 'sort_order' => 14, 'description' => '称重改价金额生效', 'created_at' => $now, 'updated_at' => $now],
            ['type_code' => 'purchase_warehouse', 'type_name' => '采购入库确认', 'module_name' => '平台统采', 'risk_level' => 'P1', 'enabled' => 0, 'applicant_role_id' => $roles['operator'], 'reviewer_role_id' => $roles['operator_manager'], 'sort_order' => 15, 'description' => '入库确认触发库存联动', 'created_at' => $now, 'updated_at' => $now],
            ['type_code' => 'supplier_bank_edit', 'type_name' => '供应商银行信息修改', 'module_name' => '组织主体', 'risk_level' => 'P1', 'enabled' => 0, 'applicant_role_id' => $roles['operator'], 'reviewer_role_id' => $roles['finance_manager'], 'sort_order' => 16, 'description' => '银行收付款信息生效', 'created_at' => $now, 'updated_at' => $now],
            ['type_code' => 'manual_close', 'type_name' => '手动办结', 'module_name' => '财务对账', 'risk_level' => 'P1', 'enabled' => 0, 'applicant_role_id' => $roles['finance'], 'reviewer_role_id' => $roles['finance_manager'], 'sort_order' => 17, 'description' => '单据办结锁定', 'created_at' => $now, 'updated_at' => $now],
            ['type_code' => 'sku_price_minor', 'type_name' => 'SKU小幅改价(≤15%)', 'module_name' => '商品管理', 'risk_level' => 'P1', 'enabled' => 0, 'applicant_role_id' => $roles['operator'], 'reviewer_role_id' => $roles['operator_manager'], 'sort_order' => 18, 'description' => '小幅改价生效', 'created_at' => $now, 'updated_at' => $now],
            // P1 — 损耗管理节点，默认开启
            ['type_code' => 'loss_order', 'type_name' => '损耗单审批', 'module_name' => '损耗管理', 'risk_level' => 'P1', 'enabled' => 1, 'applicant_role_id' => $roles['operator'], 'reviewer_role_id' => $roles['operator_manager'], 'sort_order' => 19, 'description' => '损耗金额超过审批阈值时需运营经理审核', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_type_configs');
        Schema::dropIfExists('approvals');
    }
};
