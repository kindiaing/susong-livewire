<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 商家账户表
        Schema::create('merchant_accounts', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('merchant_id')->unique()->comment('商家ID');
            $table->bigInteger('balance')->default(0)->comment('账户余额');
            $table->bigInteger('total_recharge')->default(0)->comment('总充值');
            $table->bigInteger('total_consumption')->default(0)->comment('总消费');
            $table->bigInteger('credit_limit')->default(0)->comment('信用额度');
            $table->tinyInteger('approval_status')->unsigned()->default(1)->comment('审核状态：1待审核，2已通过，3已拒绝（信用额度调整时有效）');
            $table->timestamps();

            $table->index('approval_status');
            $table->comment('商家账户表');
        });

        // 充值记录表
        Schema::create('recharges', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('merchant_id')->comment('商家ID');
            $table->bigInteger('amount')->default(0)->comment('充值金额');
            $table->tinyInteger('payment_method')->unsigned()->default(1)->comment('支付方式：1微信支付，2线下转账，3后台手工');
            $table->string('transaction_no', 100)->nullable()->comment('第三方交易号');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：1待确认，2成功，3失败');
            $table->tinyInteger('approval_status')->unsigned()->default(1)->comment('审核状态：1待审核，2已通过，3已拒绝');
            $table->unsignedBigInteger('operator_id')->nullable()->comment('操作人ID');
            $table->string('remark', 255)->nullable()->comment('备注');
            $table->timestamps();

            $table->index('merchant_id');
            $table->index('transaction_no');
            $table->index('status');
            $table->index('approval_status');
            $table->comment('充值记录表');
        });

        // 供应商结算单表
        Schema::create('supplier_settlements', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->string('settlement_no', 50)->unique()->comment('结算单号');
            $table->unsignedBigInteger('supplier_id')->comment('供应商ID');
            $table->date('start_date')->comment('结算周期开始');
            $table->date('end_date')->comment('结算周期结束');
            $table->bigInteger('total_amount')->default(0)->comment('汇总金额');
            $table->bigInteger('service_fee')->default(0)->comment('服务费');
            $table->bigInteger('payable_amount')->default(0)->comment('应付金额');
            $table->bigInteger('return_amount')->default(0)->comment('采购退货扣减金额');
            $table->bigInteger('paid_amount')->default(0)->comment('已付金额（多次付款累计）');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：1待结算，2部分付款，3已结清，4已办结');
            $table->timestamp('settled_at')->nullable()->comment('结算时间');
            $table->timestamp('closed_at')->nullable()->comment('办结时间');
            $table->unsignedBigInteger('closed_by')->nullable()->comment('办结操作人ID');
            $table->timestamps();

            $table->index('supplier_id');
            $table->index('status');
            $table->comment('供应商结算单表');
        });

        // 结算单明细表
        Schema::create('supplier_settlement_items', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('supplier_settlement_id')->comment('结算单ID');
            $table->unsignedBigInteger('purchase_order_id')->comment('采购单ID');
            $table->bigInteger('amount')->default(0)->comment('金额');
            $table->timestamps();

            $table->index('supplier_settlement_id');
            $table->index('purchase_order_id');
            $table->comment('结算单明细表');
        });

        // 付款记录表（多次付款）
        Schema::create('settlement_payments', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('settlement_id')->comment('供应商结算单ID');
            $table->bigInteger('amount')->default(0)->comment('本次付款金额');
            $table->tinyInteger('payment_method')->unsigned()->default(1)->comment('付款方式：1银行转账，2线下现金，3后台手工');
            $table->string('transaction_no', 100)->nullable()->comment('第三方交易号');
            $table->unsignedBigInteger('operator_id')->nullable()->comment('操作人ID');
            $table->tinyInteger('approval_status')->unsigned()->default(1)->comment('审核状态：1待审核，2已通过，3已拒绝');
            $table->json('evidence_urls')->nullable()->comment('付款凭证图片数组');
            $table->string('remark', 255)->nullable()->comment('备注');
            $table->timestamps();

            $table->index('settlement_id');
            $table->index('payment_method');
            $table->index('created_at');
            $table->index('approval_status');
            $table->comment('付款记录表');
        });

        // 应收账款表
        Schema::create('receivables', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->string('receivable_no', 50)->unique()->comment('应收单号');
            $table->unsignedBigInteger('order_id')->comment('订单ID');
            $table->unsignedBigInteger('merchant_id')->comment('商家ID');
            $table->bigInteger('original_amount')->default(0)->comment('原始金额');
            $table->bigInteger('adjusted_amount')->default(0)->comment('调整后金额');
            $table->bigInteger('discrepancy_amount')->default(0)->comment('差异金额');
            $table->bigInteger('return_amount')->default(0)->comment('售后退货扣减金额');
            $table->bigInteger('strategy_discount_amount')->default(0)->comment('改价/促销折扣金额');
            $table->bigInteger('received_amount')->default(0)->comment('已收金额（多次收款累计）');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：1未结算，2部分收款，3已结清，4争议中，5已办结');
            $table->tinyInteger('settlement_type')->unsigned()->default(1)->comment('结算方式：1现结，2账期，3预付款');
            $table->date('due_date')->nullable()->comment('到期日');
            $table->timestamp('settled_at')->nullable()->comment('结算时间');
            $table->timestamp('closed_at')->nullable()->comment('办结时间');
            $table->unsignedBigInteger('closed_by')->nullable()->comment('办结操作人ID');
            $table->tinyInteger('approval_status')->unsigned()->default(1)->comment('审核状态：1待审核，2已通过，3已拒绝（改价折扣调整时有效）');
            $table->timestamps();

            $table->index('order_id');
            $table->index('merchant_id');
            $table->index('status');
            $table->index('approval_status');
            $table->comment('应收账款表');
        });

        // 收款记录表（多次收款）
        Schema::create('receivable_payments', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('receivable_id')->comment('应收账款ID');
            $table->bigInteger('amount')->default(0)->comment('本次收款金额');
            $table->tinyInteger('payment_method')->unsigned()->default(1)->comment('收款方式：1余额扣款，2微信支付，3线下转账，4后台手工');
            $table->string('transaction_no', 100)->nullable()->comment('第三方交易号');
            $table->unsignedBigInteger('operator_id')->nullable()->comment('操作人ID');
            $table->tinyInteger('approval_status')->unsigned()->default(1)->comment('审核状态：1待审核，2已通过，3已拒绝');
            $table->json('evidence_urls')->nullable()->comment('收款凭证图片数组');
            $table->string('remark', 255)->nullable()->comment('备注');
            $table->timestamps();

            $table->index('receivable_id');
            $table->index('payment_method');
            $table->index('created_at');
            $table->index('approval_status');
            $table->comment('收款记录表');
        });

        // 发票表
        Schema::create('invoices', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->string('invoice_no', 50)->nullable()->comment('发票号');
            $table->tinyInteger('type')->unsigned()->default(1)->comment('类型：1客户发票，2供应商发票');
            $table->unsignedBigInteger('target_id')->comment('关联对象ID');
            $table->string('title', 100)->nullable()->comment('发票抬头');
            $table->bigInteger('amount')->default(0)->comment('金额');
            $table->string('file_url', 255)->nullable()->comment('发票文件地址');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：1待开具，2已开具，3已寄出');
            $table->timestamp('applied_at')->nullable()->comment('申请时间');
            $table->timestamp('issued_at')->nullable()->comment('开具时间');
            $table->timestamp('sent_at')->nullable()->comment('寄出时间');
            $table->timestamps();

            $table->index('target_id');
            $table->index('type');
            $table->index('status');
            $table->comment('发票表');
        });

        // 单据授权更正表
        Schema::create('correction_authorizations', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('order_id')->comment('订单ID');
            $table->unsignedBigInteger('operator_id')->comment('授权人ID');
            $table->string('reason', 255)->comment('更正原因');
            $table->json('before_data')->nullable()->comment('修改前数据');
            $table->json('after_data')->nullable()->comment('修改后数据');
            $table->timestamp('authorized_at')->nullable()->comment('授权时间');
            $table->timestamps();

            $table->index('order_id');
            $table->index('operator_id');
            $table->comment('单据授权更正表');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('correction_authorizations');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('receivable_payments');
        Schema::dropIfExists('receivables');
        Schema::dropIfExists('settlement_payments');
        Schema::dropIfExists('supplier_settlement_items');
        Schema::dropIfExists('supplier_settlements');
        Schema::dropIfExists('recharges');
        Schema::dropIfExists('merchant_accounts');
    }
};
