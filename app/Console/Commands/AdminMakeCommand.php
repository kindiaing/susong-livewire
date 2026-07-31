<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;
use function Laravel\Prompts\confirm;

/**
 * @deprecated 请使用 admin:create-admin 替代。此命令将在未来版本中移除。
 */
class AdminMakeCommand extends Command
{
    protected $signature = 'admin:make-user
                            {--name= : 管理员用户名}
                            {--email= : 管理员邮箱}
                            {--password= : 管理员密码}
                            {--role=super-admin : 角色（super-admin / admin）}
                            {--force : 强制创建，不询问}';

    protected $description = '[已废弃] 请使用 admin:create-admin 替代';

    public function handle(): int
    {
        $this->warn('⚠ admin:make-user 已废弃，请使用 admin:create-admin 替代。');
        $this->line('  示例: <info>php artisan admin:create-admin --name=Admin --email=admin@example.com --password=Secret123</info>');
        $this->newLine();

        $name = $this->option('name') ?: ($this->option('force') ? 'Admin' : text(
            label: '请输入管理员用户名',
            placeholder: 'admin',
            required: true,
        ));

        $email = $this->option('email') ?: ($this->option('force') ? 'admin@susong.com' : text(
            label: '请输入管理员邮箱',
            placeholder: 'admin@susong.com',
            required: true,
            validate: fn ($value) => filter_var($value, FILTER_VALIDATE_EMAIL) ? null : '请输入有效的邮箱地址',
        ));

        $plainPassword = $this->option('password') ?: ($this->option('force') ? 'admin123' : password(
            label: '请输入管理员密码',
            required: true,
            validate: fn ($value) => strlen($value) >= 8 ? null : '密码至少 8 位',
        ));

        $role = $this->option('role');

        // 检查邮箱是否已存在
        if (User::where('email', $email)->exists()) {
            $this->error("邮箱 [{$email}] 已存在！");

            if (! $this->option('force') && confirm('是否为该用户添加管理员角色？')) {
                $user = User::where('email', $email)->first();
                $this->assignRole($user, $role);
                $this->info("已为用户 [{$user->name}] 分配 [{$role}] 角色。");

                return self::SUCCESS;
            }

            return self::FAILURE;
        }

        // 创建用户
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($plainPassword),
            'email_verified_at' => now(),
        ]);

        $this->assignRole($user, $role);

        $this->info('管理员创建成功！');
        $this->line("  用户名: <info>{$name}</info>");
        $this->line("  邮箱:   <info>{$email}</info>");
        $this->line("  角色:   <info>{$role}</info>");

        return self::SUCCESS;
    }

    protected function assignRole(User $user, string $role): void
    {
        // Spatie Permission 已安装，角色不存在时自动创建
        if (class_exists(\Spatie\Permission\Models\Role::class)) {
            $roleModel = \Spatie\Permission\Models\Role::firstOrCreate(['name' => $role]);
            $user->assignRole($roleModel);
        }
    }
}
