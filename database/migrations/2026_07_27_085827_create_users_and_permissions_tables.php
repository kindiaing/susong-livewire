<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->string('username', 50)->unique()->comment('用户名');
            $table->string('password', 255)->comment('bcrypt加密密码');
            $table->string('name', 50)->comment('姓名');
            $table->string('phone', 20)->nullable()->unique()->comment('手机号');
            $table->string('email', 100)->nullable()->unique()->comment('邮箱');
            $table->string('avatar', 255)->nullable()->comment('头像');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：0禁用，1启用');
            $table->timestamp('last_login_at')->nullable()->comment('最后登录时间');
            $table->timestamp('email_verified_at')->nullable()->comment('邮箱验证时间');
            $table->string('remember_token', 100)->nullable()->comment('记住我令牌');
            $table->timestamps();
            $table->softDeletes();
            $table->comment('用户表');
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->string('name', 50)->comment('角色标识');
            $table->string('guard_name', 50)->default('web')->comment('守卫名称');
            $table->string('display_name', 50)->comment('角色显示名称');
            $table->string('description', 255)->nullable()->comment('描述');
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
            $table->comment('角色表');
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->string('name', 50)->comment('权限标识');
            $table->string('guard_name', 50)->default('web')->comment('守卫名称');
            $table->string('display_name', 50)->comment('权限显示名称');
            $table->tinyInteger('type')->unsigned()->default(1)->comment('类型：1菜单，2按钮，3接口');
            $table->unsignedBigInteger('parent_id')->default(0)->comment('父级权限ID');
            $table->string('route', 100)->nullable()->comment('路由/接口标识');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->string('icon', 50)->nullable()->comment('菜单图标');
            $table->timestamps();
            $table->index('parent_id');
            $table->unique(['name', 'guard_name']);
            $table->comment('权限表');
        });

        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')->comment('角色ID');
            $table->string('model_type', 255)->comment('模型类型');
            $table->unsignedBigInteger('model_id')->comment('模型ID');
            $table->primary(['role_id', 'model_id', 'model_type']);
            $table->index(['model_id', 'model_type'], 'idx_model');
            $table->comment('用户角色关联表');
        });

        Schema::create('model_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id')->comment('权限ID');
            $table->string('model_type', 255)->comment('模型类型');
            $table->unsignedBigInteger('model_id')->comment('模型ID');
            $table->primary(['permission_id', 'model_id', 'model_type']);
            $table->index(['model_id', 'model_type'], 'idx_model');
            $table->comment('用户权限关联表');
        });

        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id')->comment('权限ID');
            $table->unsignedBigInteger('role_id')->comment('角色ID');
            $table->primary(['permission_id', 'role_id']);
            $table->comment('角色权限关联表');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email', 255)->primary()->comment('邮箱');
            $table->string('token', 255)->comment('令牌');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->comment('密码重置令牌');
        });

        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->string('tokenable_type', 255)->comment('模型类型');
            $table->unsignedBigInteger('tokenable_id')->comment('模型ID');
            $table->string('name', 255)->comment('Token名称');
            $table->string('token', 64)->unique()->comment('Token值');
            $table->text('abilities')->nullable()->comment('能力列表');
            $table->timestamp('last_used_at')->nullable()->comment('最后使用时间');
            $table->timestamp('expires_at')->nullable()->comment('过期时间');
            $table->timestamps();
            $table->index(['tokenable_id', 'tokenable_type']);
            $table->comment('Sanctum Token表');
        });

        $now = now();
        DB::table('roles')->insert([
            ['name' => 'super_admin', 'guard_name' => 'web', 'display_name' => '超级管理员', 'description' => '全部功能、系统配置、账号管理', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'operator', 'guard_name' => 'web', 'display_name' => '运营管理员', 'description' => '商品、订单、商家、供应商管理', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'operator_manager', 'guard_name' => 'web', 'display_name' => '运营经理', 'description' => '运营审核、商品/订单/价格策略审核确认', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'finance', 'guard_name' => 'web', 'display_name' => '财务人员', 'description' => '应收、结算、发票、审计', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'cashier', 'guard_name' => 'web', 'display_name' => '出纳', 'description' => '付款录入、收款录入、资金操作执行', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'finance_manager', 'guard_name' => 'web', 'display_name' => '财务经理', 'description' => '财务审核、付款/收款/结算单据复核确认', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'picker', 'guard_name' => 'web', 'display_name' => '拣货员', 'description' => '拣货任务、称重改价', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'driver', 'guard_name' => 'web', 'display_name' => '配送司机', 'description' => '配送任务、轨迹、签收', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'merchant', 'guard_name' => 'web', 'display_name' => '商家', 'description' => '小程序商家端', 'created_at' => $now, 'updated_at' => $now],
        ]);

        $superAdminRoleId = DB::table('roles')->where('name', 'super_admin')->value('id');
        $userId = DB::table('users')->insertGetId([
            'username' => 'seeding',
            'password' => Hash::make('Password'),
            'name' => '系统管理员',
            'phone' => '15690631151',
            'email' => 'seeding@ihopeso.cn',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('model_has_roles')->insert([
            'role_id' => $superAdminRoleId,
            'model_type' => 'App\\Models\\User',
            'model_id' => $userId,
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('model_has_permissions');
        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('users');
    }
};
