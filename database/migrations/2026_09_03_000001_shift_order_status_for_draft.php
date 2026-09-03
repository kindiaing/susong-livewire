<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 订单状态整体后移：新增「草稿（待提交）」状态
     *
     * 旧链：1待拣货 → 2拣货中 → 3配送中 → 4已签收 → 5已锁定，9已作废
     * 新链：1草稿 → 2待拣货 → 3拣货中 → 4配送中 → 5已签收 → 6已锁定，9已作废
     *
     * 存量数据从大到小依次更新，避免链式覆盖（5→6 先于 4→5 …最后 1→2）
     */
    public function up(): void
    {
        // 从大到小更新，避免链式覆盖
        DB::statement('UPDATE orders SET status = 6 WHERE status = 5');
        DB::statement('UPDATE orders SET status = 5 WHERE status = 4');
        DB::statement('UPDATE orders SET status = 4 WHERE status = 3');
        DB::statement('UPDATE orders SET status = 3 WHERE status = 2');
        DB::statement('UPDATE orders SET status = 2 WHERE status = 1');

        Schema::table('orders', function (Blueprint $table) {
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：1草稿（待提交），2待拣货，3拣货中，4配送中，5已签收，6已锁定，9已作废')->change();
        });
    }

    public function down(): void
    {
        // 逆向：从小到大更新
        DB::statement('UPDATE orders SET status = 1 WHERE status = 2');
        DB::statement('UPDATE orders SET status = 2 WHERE status = 3');
        DB::statement('UPDATE orders SET status = 3 WHERE status = 4');
        DB::statement('UPDATE orders SET status = 4 WHERE status = 5');
        DB::statement('UPDATE orders SET status = 5 WHERE status = 6');

        Schema::table('orders', function (Blueprint $table) {
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：1待拣货，2拣货中，3配送中，4已签收，5已锁定，9已作废')->change();
        });
    }
};
