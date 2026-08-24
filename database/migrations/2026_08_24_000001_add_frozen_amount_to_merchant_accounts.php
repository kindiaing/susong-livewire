<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // merchant_accounts 增加 frozen_amount 字段
        // 用于预付款抵扣时冻结金额，签收确认后扣减解冻
        Schema::table('merchant_accounts', function (Blueprint $table) {
            $table->bigInteger('frozen_amount')->default(0)->after('balance')->comment('冻结金额（预付款签收时冻结，确认后扣减）');
        });
    }

    public function down(): void
    {
        Schema::table('merchant_accounts', function (Blueprint $table) {
            $table->dropColumn('frozen_amount');
        });
    }
};
