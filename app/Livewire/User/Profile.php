<?php

namespace App\Livewire\User;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
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
        ]);

        $user = auth()->user();

        // 手机号唯一性校验（排除自身）
        if ($validated['phone'] && $validated['phone'] !== $user->phone) {
            if (\App\Models\User::where('phone', $validated['phone'])->where('id', '!=', $user->id)->exists()) {
                $this->addError('phone', '该手机号已被其他用户使用');
                return;
            }
        }

        // 邮箱唯一性校验（排除自身）
        if ($validated['email'] && $validated['email'] !== $user->email) {
            if (\App\Models\User::where('email', $validated['email'])->where('id', '!=', $user->id)->exists()) {
                $this->addError('email', '该邮箱已被其他用户使用');
                return;
            }
        }

        $user->update($validated);

        $this->js("window.\$toast && window.\$toast.success('保存成功', '个人资料已更新')");
    }

    public function changePassword(): void
    {
        $validated = $this->validate([
            'current_password'      => ['required', 'string'],
            'password'             => ['required', 'string', Password::defaults(), 'confirmed'],
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
