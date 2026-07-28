<?php

namespace App\Livewire\User;

use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Profile extends Component
{
    // ---- 个人资料 ----
    public string $name = '';
    public string $phone = '';
    public string $email = '';

    // ---- 修改密码 ----
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(): void
    {
        $user = auth()->user();
        $this->name = $user->name ?? '';
        $this->phone = $user->phone ?? '';
        $this->email = $user->email ?? '';
    }

    public function saveProfile(): void
    {
        $validated = $this->validate([
            'name'  => 'required|string|max:50',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
        ], [
            'name.required'  => '姓名不能为空',
            'name.string'    => '姓名格式不正确',
            'name.max'       => '姓名不能超过50个字符',
            'phone.string'   => '手机号格式不正确',
            'phone.max'      => '手机号不能超过20个字符',
            'email.email'    => '请输入有效的邮箱地址',
            'email.max'      => '邮箱不能超过100个字符',
        ]);

        // 从数据库重新读取原始值，对比是否修改（Livewire private 属性在请求间不持久化）
        $user = auth()->user();
        $original = [
            'name'  => $user->name ?? '',
            'phone' => $user->phone ?? '',
            'email' => $user->email ?? '',
        ];

        $changed = [];
        foreach (['name', 'phone', 'email'] as $field) {
            if ($validated[$field] !== ($original[$field] ?? '')) {
                $changed[$field] = $validated[$field];
            }
        }

        if (empty($changed)) {
            $this->js("window.\$toast && window.\$toast.info('未修改', '没有任何内容被修改')");
            return;
        }

        // 手机号唯一性校验（排除自身）
        if (isset($changed['phone']) && $changed['phone'] !== $user->phone) {
            if (\App\Models\User::where('phone', $changed['phone'])->where('id', '!=', $user->id)->exists()) {
                $this->addError('phone', '该手机号已被其他用户使用');
                return;
            }
        }

        // 邮箱唯一性校验（排除自身）
        if (isset($changed['email']) && $changed['email'] !== $user->email) {
            if (\App\Models\User::where('email', $changed['email'])->where('id', '!=', $user->id)->exists()) {
                $this->addError('email', '该邮箱已被其他用户使用');
                return;
            }
        }

        $user->update($changed);

        $this->js("window.\$toast && window.\$toast.success('保存成功', '个人资料已更新')");
    }

    public function changePassword(): void
    {
        $validated = $this->validate([
            'current_password'      => ['required', 'string'],
            'password'             => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => '请输入当前密码',
            'current_password.string'   => '当前密码格式不正确',
            'password.required'         => '请输入新密码',
            'password.string'           => '新密码格式不正确',
            'password.min'              => '新密码至少需要8个字符',
            'password.confirmed'        => '两次输入的密码不一致',
        ], [
            'current_password' => '当前密码',
            'password'         => '新密码',
        ]);

        $user = auth()->user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            $this->addError('current_password', '当前密码不正确');
            return;
        }

        $user->update(['password' => Hash::make($validated['password'])]);

        // 清空密码字段
        $this->reset(['current_password', 'password', 'password_confirmation']);

        $this->js("window.\$toast && window.\$toast.success('密码已修改', '新密码已生效，下次登录请使用新密码')");
    }

    public function render()
    {
        return view('livewire.user.profile')
            ->layout('components.app-layout')
            ->title('个人中心');
    }
}
