<div class="p-6">
    {{-- 页面标题 --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-foreground">个人中心</h1>
        <p class="text-muted-foreground mt-1">管理您的个人资料和账户安全</p>
    </div>

    <x-ui.tabs defaultTab="profile">
        {{-- 标签栏 --}}
        <div class="flex border-b border-border mb-6">
            <x-ui.tabs-trigger value="profile">个人资料</x-ui.tabs-trigger>
            <x-ui.tabs-trigger value="password">修改密码</x-ui.tabs-trigger>
        </div>

        {{-- ==================== 个人资料 ==================== --}}
        <x-ui.tabs-content value="profile">
            <x-ui.card class="max-w-xl">
                <x-ui.card-header>
                    <x-ui.card-title>基本信息</x-ui.card-title>
                </x-ui.card-header>
                <x-ui.card-content>
                    <form wire:submit="saveProfile" class="space-y-4">

                        {{-- 用户名（只读） --}}
                        <x-ui.input label="用户名"
                                    type="text"
                                    :value="auth()->user()->username"
                                    disabled
                                    hint="用户名不可修改" />

                        {{-- 姓名 --}}
                        <x-ui.input label="姓名"
                                    wire:model="name"
                                    type="text"
                                    placeholder="请输入姓名" />
                        @error('name')
                            <p class="text-xs text-destructive -mt-3">{{ $message }}</p>
                        @enderror

                        {{-- 手机号 --}}
                        <x-ui.input label="手机号"
                                    wire:model="phone"
                                    type="tel"
                                    placeholder="请输入手机号" />
                        @error('phone')
                            <p class="text-xs text-destructive -mt-3">{{ $message }}</p>
                        @enderror

                        {{-- 邮箱 --}}
                        <x-ui.input label="邮箱"
                                    wire:model="email"
                                    type="email"
                                    placeholder="请输入邮箱" />
                        @error('email')
                            <p class="text-xs text-destructive -mt-3">{{ $message }}</p>
                        @enderror

                        {{-- 角色标签（只读） --}}
                        <div class="grid gap-1.5">
                            <x-ui.label>角色</x-ui.label>
                            <div class="flex gap-1.5">
                                @foreach(auth()->user()->roles as $role)
                                    <span class="inline-flex items-center rounded-md bg-primary/10 px-2 py-1 text-xs font-medium text-primary">
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                                @if(auth()->user()->roles->isEmpty())
                                    <span class="text-sm text-muted-foreground">未分配角色</span>
                                @endif
                            </div>
                        </div>

                        {{-- 保存按钮 --}}
                        <div class="flex items-center gap-3 pt-2">
                            <x-ui.button type="submit" variant="blue" wire:loading.attr="disabled">
                                <span wire:loading.remove>保存修改</span>
                                <span wire:loading>保存中...</span>
                            </x-ui.button>
                        </div>
                    </form>
                </x-ui.card-content>
            </x-ui.card>
        </x-ui.tabs-content>

        {{-- ==================== 修改密码 ==================== --}}
        <x-ui.tabs-content value="password">
            <x-ui.card class="max-w-xl">
                <x-ui.card-header>
                    <x-ui.card-title>修改密码</x-ui.card-title>
                </x-ui.card-header>
                <x-ui.card-content>
                    <form wire:submit="changePassword" class="space-y-4">

                        {{-- 当前密码 --}}
                        <x-ui.input label="当前密码"
                                    wire:model="current_password"
                                    type="password"
                                    placeholder="请输入当前密码"
                                    autocomplete="current-password" />
                        @error('current_password')
                            <p class="text-xs text-destructive -mt-3">{{ $message }}</p>
                        @enderror

                        {{-- 新密码 --}}
                        <x-ui.input label="新密码"
                                    wire:model="password"
                                    type="password"
                                    placeholder="请输入新密码"
                                    autocomplete="new-password" />
                        @error('password')
                            <p class="text-xs text-destructive -mt-3">{{ $message }}</p>
                        @enderror

                        {{-- 确认密码 --}}
                        <x-ui.input label="确认新密码"
                                    wire:model="password_confirmation"
                                    type="password"
                                    placeholder="请再次输入新密码"
                                    autocomplete="new-password" />
                        @error('password_confirmation')
                            <p class="text-xs text-destructive -mt-3">{{ $message }}</p>
                        @enderror

                        {{-- 修改按钮 --}}
                        <div class="flex items-center gap-3 pt-2">
                            <x-ui.button type="submit" variant="blue" wire:loading.attr="disabled">
                                <span wire:loading.remove>修改密码</span>
                                <span wire:loading>修改中...</span>
                            </x-ui.button>
                        </div>
                    </form>
                </x-ui.card-content>
            </x-ui.card>
        </x-ui.tabs-content>
    </x-ui.tabs>
</div>
