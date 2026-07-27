<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class AdminResetPasswordCommand extends Command
{
    protected $signature = 'admin:reset-password
                            {--email= : 用户邮箱}
                            {--password= : 新密码}';

    protected $description = '重置用户密码';

    public function handle(): int
    {
        $email = $this->option('email') ?: text(
            label: '请输入要重置密码的邮箱',
            placeholder: 'admin@susong.com',
            required: true,
        );

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("用户 [{$email}] 不存在！");

            return self::FAILURE;
        }

        $plainPassword = $this->option('password') ?: password(
            label: '请输入新密码',
            required: true,
            validate: fn ($value) => strlen($value) >= 8 ? null : '密码至少 8 位',
        );

        $user->update(['password' => Hash::make($plainPassword)]);

        $this->info("用户 [{$user->name}] 的密码已重置！");

        return self::SUCCESS;
    }
}
