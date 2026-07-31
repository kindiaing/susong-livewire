<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\password;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class AdminCreateAdminCommand extends Command
{
    protected $signature = 'admin:create-admin
                            {--name= : 管理员用户名}
                            {--email= : 管理员邮箱}
                            {--password= : 管理员密码}
                            {--role=super_admin : 角色（super_admin / operator / operator_manager / finance / cashier / finance_manager）}
                            {--guard=web : 认证守卫}
                            {--force : 强制创建，不交互提示}';

    protected $description = '创建管理员账户（含后台管理角色）';

    public function handle(): int
    {
        $force = $this->option('force');

        $name = $this->option('name') ?: ($force ? 'Admin' : text(
            label: '请输入管理员用户名',
            placeholder: 'admin',
            required: true,
        ));

        $email = $this->option('email') ?: ($force ? 'admin@susong.com' : text(
            label: '请输入管理员邮箱',
            placeholder: 'admin@susong.com',
            required: true,
            validate: fn ($value) => filter_var($value, FILTER_VALIDATE_EMAIL) ? null : '请输入有效的邮箱地址',
        ));

        $plainPassword = $this->option('password') ?: ($force ? 'Password' : password(
            label: '请输入管理员密码',
            required: true,
            validate: fn ($value) => strlen($value) >= 8 ? null : '密码至少 8 位',
        ));

        $role = $this->option('role') ?: ($force ? 'super_admin' : select(
            label: '请选择管理角色',
            options: [
                'super_admin' => '超级管理员',
                'operator' => '运营管理员',
                'operator_manager' => '运营经理',
                'finance' => '财务人员',
                'cashier' => '出纳',
                'finance_manager' => '财务经理',
            ],
            default: 'super_admin',
        ));

        $guard = $this->option('guard');

        // 检查邮箱是否已存在
        if (User::where('email', $email)->exists()) {
            $this->error("邮箱 [{$email}] 已存在！");

            if (! $force && confirm('是否为该用户添加管理角色？')) {
                $user = User::where('email', $email)->first();
                $this->assignRole($user, $role, $guard);
                $this->info("已为用户 [{$user->name}] 分配 [{$role}] 角色。");

                return self::SUCCESS;
            }

            return self::FAILURE;
        }

        // 创建用户
        $user = User::create([
            'name' => $name,
            'username' => $name,
            'email' => $email,
            'password' => Hash::make($plainPassword),
            'email_verified_at' => now(),
            'status' => 1,
        ]);

        $this->assignRole($user, $role, $guard);

        $this->info('管理员创建成功！');
        $this->line("  用户名: <info>{$name}</info>");
        $this->line("  邮箱:   <info>{$email}</info>");
        $this->line("  角色:   <info>{$role}</info>");

        // 如果是 super_admin，自动分配所有权限
        if ($role === 'super_admin' && class_exists(\Spatie\Permission\Models\Permission::class)) {
            $user->syncPermissions(\Spatie\Permission\Models\Permission::all());
            $this->line('  权限:   <info>全部权限已分配</info>');
        }

        return self::SUCCESS;
    }

    protected function assignRole(User $user, string $role, string $guard = 'web'): void
    {
        $roleModel = Role::firstOrCreate(
            ['name' => $role, 'guard_name' => $guard],
        );
        $user->assignRole($roleModel);
    }
}
