<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 应收账款表结构调整：支持汇总单
 *
 * - order_id 改为可空：汇总单由多个订单合并生成，不挂靠单一订单
 * - remark 改为 text：汇总单备注自动拼接订单明细（单号+金额），255 长度不够
 *
 * 注意：->change() 需要 doctrine/dbal 支持（项目已安装）
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('receivables', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id')->nullable()->comment('订单ID（汇总单为空）')->change();
            $table->text('remark')->nullable()->comment('备注（汇总单自动拼接订单明细）')->change();
        });
    }

    public function down(): void
    {
        Schema::table('receivables', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id')->comment('订单ID')->change();
            $table->string('remark', 255)->nullable()->comment('备注')->change();
        });
    }
};
