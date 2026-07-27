<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class AdminListRolesCommand extends Command
{
    protected $signature = 'admin:roles
                            {--with-users : 同时显示每个角色下的用户}';

    protected $description = '列出所有角色与权限';

    public function handle(): int
    {
        if (! class_exists(Role::class)) {
            $this->error('Spatie Permission 未安装！');

            return self::FAILURE;
        }

        $roles = Role::with('permissions', 'users')->get();

        if ($roles->isEmpty()) {
            $this->warn('暂无角色数据，请先执行 admin:install');

            return self::SUCCESS;
        }

        $this->info('角色列表：');
        $this->newLine();

        foreach ($roles as $role) {
            $userCount = $role->users->count();
            $permCount = $role->permissions->count();
            $this->line("  <info>{$role->name}</info>  — 用户 {$userCount} 人 / 权限 {$permCount} 项");

            if ($this->option('with-users') && $userCount > 0) {
                foreach ($role->users as $user) {
                    $this->line("      - {$user->name} ({$user->email})");
                }
            }
        }

        $this->newLine();
        $this->line("共 <info>{$roles->count()}</info> 个角色");

        return self::SUCCESS;
    }
}
