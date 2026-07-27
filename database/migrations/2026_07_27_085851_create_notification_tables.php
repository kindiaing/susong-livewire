<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 通知/消息表
        Schema::create('notifications', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('user_id')->nullable()->comment('目标用户ID，NULL表示全站广播');
            $table->unsignedBigInteger('merchant_id')->nullable()->comment('目标商家ID');
            $table->tinyInteger('type')->unsigned()->default(1)->comment('类型：1系统通知，2订单状态变更，3补货提醒，4库存预警，5账户变动');
            $table->string('title', 100)->comment('标题');
            $table->text('content')->nullable()->comment('内容');
            $table->json('data')->nullable()->comment('扩展数据');
            $table->tinyInteger('is_read')->unsigned()->default(0)->comment('是否已读：0未读，1已读');
            $table->timestamp('read_at')->nullable()->comment('已读时间');
            $table->timestamps();

            $table->index('user_id');
            $table->index('merchant_id');
            $table->index('type');
            $table->index('is_read');
            $table->index('created_at');
            $table->comment('通知/消息表');
        });

        // 智能补货提醒规则表
        Schema::create('restock_reminders', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('merchant_id')->comment('商家ID');
            $table->unsignedBigInteger('sku_id')->comment('SKU ID');
            $table->bigInteger('threshold_quantity')->default(0)->comment('触发提醒的库存阈值');
            $table->tinyInteger('remind_cycle')->unsigned()->default(1)->comment('提醒周期：1每日，2每周，3仅一次');
            $table->timestamp('last_reminded_at')->nullable()->comment('上次提醒时间');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：0禁用，1启用');
            $table->timestamps();

            $table->unique(['merchant_id', 'sku_id']);
            $table->index('status');
            $table->comment('智能补货提醒规则表');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restock_reminders');
        Schema::dropIfExists('notifications');
    }
};
