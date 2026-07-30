<div class="p-6">
    {{-- 页面标题 --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">用户管理</h1>
            <p class="text-muted-foreground mt-1">管理系统用户、角色分配与状态控制</p>
        </div>
        <button type="button" wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            新增用户
        </button>
    </div>

    {{-- 搜索栏 --}}
    <div class="flex items-center gap-3 mb-4">
        <input
            type="text"
            wire:model.live="search"
            class="flex h-9 w-64 rounded-md border border-input bg-background px-3 text-sm"
            placeholder="搜索用户名/姓名/手机/邮箱..."
        />
        <button type="button" wire:click="resetFilters" class="text-sm text-muted-foreground hover:text-foreground transition-colors">重置</button>
    </div>

    {{-- 用户列表 --}}
    <div class="rounded-lg border bg-card overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                    <th class="px-4 py-2.5 text-left w-12">ID</th>
                    <th class="px-4 py-2.5 text-left">用户名</th>
                    <th class="px-4 py-2.5 text-left">姓名</th>
                    <th class="px-4 py-2.5 text-left">联系方式</th>
                    <th class="px-4 py-2.5 text-left w-16">状态</th>
                    <th class="px-4 py-2.5 text-left">角色</th>
                    <th class="px-4 py-2.5 text-right w-28">操作</th>
                </tr>
            </thead>
            <tbody>
            @forelse($users as $user)
                @php $isSuperAdmin = $user->roles->contains('name', 'super_admin') @endphp
                <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="user-{{ $user->id }}">
                    <td class="px-4 py-2 text-muted-foreground">{{ $user->id }}</td>
                    <td class="px-4 py-2 font-medium font-mono">{{ $user->username }}</td>
                    <td class="px-4 py-2">{{ $user->name }}</td>
                    <td class="px-4 py-2 text-muted-foreground">
                        @if($user->phone){{ $user->phone }}@endif
                        @if($user->email)<span class="ml-1 text-xs">{{ $user->email }}</span>@endif
                        @if(!$user->phone && !$user->email)—@endif
                    </td>
                    <td class="px-4 py-2">
                        @if($isSuperAdmin)
                            <span class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-green-500" title="已启用">
                                <svg class="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 13.5-13.5"/></svg>
                            </span>
                        @else
                            <button wire:click="toggleStatus({{ $user->id }})" type="button" title="{{ $user->status === 1 ? '点击禁用' : '点击启用' }}" class="inline-flex items-center justify-center">
                                @if($user->status === 1)
                                    <span class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-green-500 hover:bg-green-600 transition-colors">
                                        <svg class="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 13.5-13.5"/></svg>
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-gray-300 hover:bg-gray-400 transition-colors">
                                        <svg class="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </span>
                                @endif
                            </button>
                        @endif
                    </td>
                    <td class="px-4 py-2">
                        <div class="flex flex-wrap gap-1">
                        @forelse($user->roles as $role)
                            <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-blue-50 text-blue-700">{{ $role->display_name }}</span>
                        @empty
                            <span class="text-xs text-muted-foreground">未分配</span>
                        @endforelse
                        </div>
                    </td>
                    <td class="px-4 py-2 text-right">
                        <div class="inline-flex items-center gap-0.5">
                            {{-- 编辑 --}}
                            <button type="button" wire:click="openEditModal({{ $user->id }})" class="p-1 rounded text-blue-600 hover:bg-blue-50 hover:text-blue-700 transition-colors" title="编辑">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                            </button>
                            {{-- 角色分配 --}}
                            <button type="button" wire:click="openRoleModal({{ $user->id }})" class="p-1 rounded text-indigo-600 hover:bg-indigo-50 hover:text-indigo-700 transition-colors" title="角色分配">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A9.75 9.75 0 016.75 5.087 9.75 9.75 0 0112 4.5c2.048 0 3.94.583 5.468 1.587A9.75 9.75 0 0120.25 9v.75c0 5.385-3.597 10.02-8.25 11.642a1.5 1.5 0 01-1 0C6.02 19.772 2.25 15.135 2.25 9.75V9A9.75 9.75 0 014.686 6.087z" />
                                </svg>
                            </button>
                            {{-- 重置密码 --}}
                            <button type="button" wire:click="confirmResetPassword({{ $user->id }})" class="p-1 rounded text-amber-600 hover:bg-amber-50 hover:text-amber-700 transition-colors" title="重置密码">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                                </svg>
                            </button>
                            {{-- 删除（超级管理员不显示） --}}
                            @if(!$isSuperAdmin)
                                 <button type="button" wire:click="confirmDelete({{ $user->id }})" class="p-1 rounded text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors" title="删除">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-4 py-10 text-center text-muted-foreground">暂无用户数据</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>

    {{-- 新增/编辑弹窗 --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" wire:click="closeModal"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-4">{{ $editingId ? '编辑用户' : '新增用户' }}</h2>
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">用户名 <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="formUsername" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="登录用户名" />
                        @error('formUsername') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">姓名 <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="formName" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="真实姓名" />
                        @error('formName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">手机号</label>
                        <input type="text" wire:model="formPhone" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="可选" />
                        @error('formPhone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">邮箱</label>
                        <input type="email" wire:model="formEmail" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="可选" />
                        @error('formEmail') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">{{ $editingId ? '新密码（留空不修改）' : '密码' }}
                            @if(!$editingId)<span class="text-red-500">*</span>@endif
                        </label>
                        <input type="password" wire:model="formPassword" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="{{ $editingId ? '留空不修改' : '至少6位' }}" />
                        @error('formPassword') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">状态</label>
                        <select wire:model="formStatus" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="1">启用</option>
                            <option value="0">禁用</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button wire:click="save" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">保存</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 删除确认弹窗 --}}
    @if($showDeleteConfirm)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" wire:click="closeDeleteConfirm"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-sm mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-2">确认删除</h2>
            <p class="text-sm text-muted-foreground mb-6">确定要删除该用户吗？此操作不可恢复。</p>
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="closeDeleteConfirm" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="delete" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">删除</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 重置密码确认弹窗 --}}
    @if($showResetConfirm)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" wire:click="closeResetConfirm"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-sm mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-2">重置密码</h2>
            <p class="text-sm text-muted-foreground mb-6">确定要将该用户密码重置为 <span class="font-mono font-semibold text-foreground">Password</span> 吗？</p>
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="closeResetConfirm" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="resetPassword" class="rounded-md bg-amber-600 px-4 py-2 text-sm font-medium text-white hover:bg-amber-700 transition-colors">确认重置</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 角色分配弹窗 --}}
    @if($showRoleModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" wire:click="closeRoleModal"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-4">分配角色</h2>
            <div class="space-y-2 max-h-64 overflow-y-auto">
                @foreach($allRoles as $role)
                    <label class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-muted/30 cursor-pointer transition-colors">
                        <input
                            type="checkbox"
                            value="{{ $role['id'] }}"
                            wire:model.live="formRoleIds"
                            class="h-4 w-4 rounded border-input text-blue-600 focus:ring-blue-500"
                        />
                        <div>
                            <div class="text-sm font-medium text-foreground">{{ $role['display_name'] }}</div>
                            <div class="text-xs text-muted-foreground font-mono">{{ $role['name'] }}</div>
                        </div>
                    </label>
                @endforeach
            </div>
            <div class="flex justify-end gap-3 mt-6">
                    <button type="button" wire:click="closeRoleModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button wire:click="saveRoles" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">保存</button>
            </div>
        </div>
    </div>
    @endif
</div>
