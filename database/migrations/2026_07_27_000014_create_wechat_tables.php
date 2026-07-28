<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 微信用户绑定表
        Schema::create('wechat_users', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('user_id')->nullable()->comment('关联系统用户ID');
            $table->string('openid', 100)->unique()->comment('微信OpenID');
            $table->string('unionid', 100)->nullable()->comment('微信UnionID');
            $table->string('nickname', 50)->nullable()->comment('昵称');
            $table->string('avatar', 255)->nullable()->comment('头像');
            $table->tinyInteger('type')->unsigned()->default(1)->comment('类型：1商家端，2司机端');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态');
            $table->timestamps();

            $table->index('user_id');
            $table->index('type');
            $table->comment('微信用户绑定表');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wechat_users');
    }
};
