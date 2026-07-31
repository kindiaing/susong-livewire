<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\password;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class AdminCreateUserCommand extends Command
{
    protected $signature = 'admin:create-user
                            {--name= : 用户名}
                            {--email= : 邮箱}
                            {--password= : 密码}
                            {--role= : 角色}
                            {--guard=web : 认证守卫}
                            {--active=1 : 是否启用（1=启用，0=禁用）}';

    protected $description = '创建普通用户账户';

    public function handle(): int
    {
        $name = $this->option('name') ?: text(
            label: '请输入用户名',
            placeholder: '张三',
            required: true,
        );

        $email = $this->option('email') ?: text(
            label: '请输入邮箱',
            placeholder: 'user@susong.com',
            required: true,
            validate: fn ($value) => filter_var($value, FILTER_VALIDATE_EMAIL) ? null : '请输入有效的邮箱地址',
        );

        $plainPassword = $this->option('password') ?: password(
            label: '请输入密码',
            required: true,
            validate: fn ($value) => strlen($value) >= 8 ? null : '密码至少 8 位',
        );

        $role = $this->option('role') ?: select(
            label: '请选择角色',
            options: [
                'operator' => '运营管理员',
                'operator_manager' => '运营经理',
                'finance' => '财务人员',
                'cashier' => '出纳',
                'finance_manager' => '财务经理',
                'picker' => '拣货员',
                'driver' => '配送司机',
                'merchant' => '商家',
            ],
            default: 'operator',
        );

        $guard = $this->option('guard');
        $active = (int) $this->option('active');

        if (User::where('email', $email)->exists()) {
            $this->error("邮箱 [{$email}] 已存在！");

            return self::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'username' => $name,
            'email' => $email,
            'password' => Hash::make($plainPassword),
            'email_verified_at' => now(),
            'status' => $active,
        ]);

        // Spatie Permission 分配角色
        $roleModel = Role::firstOrCreate(
            ['name' => $role, 'guard_name' => $guard],
        );
        $user->assignRole($roleModel);

        $this->info('用户创建成功！');
        $this->line("  用户名: <info>{$name}</info>");
        $this->line("  邮箱:   <info>{$email}</info>");
        $this->line("  角色:   <info>{$role}</info>");
        $this->line("  状态:   <info>" . ($active ? '启用' : '禁用') . '</info>');

        return self::SUCCESS;
    }
}
