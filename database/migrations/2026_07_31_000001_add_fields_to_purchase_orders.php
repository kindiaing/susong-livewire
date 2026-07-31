<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('warehouse_id')->nullable()->after('supplier_id')->comment('入库目标仓库');
            $table->unsignedBigInteger('operator_id')->nullable()->after('actual_amount')->comment('经办人');
            $table->timestamp('ordered_at')->nullable()->after('operator_id')->comment('下单时间');
            $table->timestamp('shipped_at')->nullable()->after('ordered_at')->comment('发货时间');
            $table->timestamp('stocked_at')->nullable()->after('shipped_at')->comment('入库时间');

            $table->foreign('warehouse_id')->references('id')->on('warehouses')->nullOnDelete();
            $table->foreign('operator_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['warehouse_id']);
            $table->dropForeign(['operator_id']);
            $table->dropColumn(['warehouse_id', 'operator_id', 'ordered_at', 'shipped_at', 'stocked_at']);
        });
    }
};
