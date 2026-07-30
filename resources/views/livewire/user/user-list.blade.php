<div class="p-6">
    {{-- 页面标题 --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">用户管理</h1>
            <p class="text-muted-foreground mt-1">管理系统用户、角色分配与状态控制</p>
        </div>
        <button wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
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
        <button wire:click="resetFilters" class="text-sm text-muted-foreground hover:text-foreground transition-colors">重置</button>
    </div>

    {{-- 用户列表 --}}
    <div class="rounded-lg border bg-card">
        <div class="grid grid-cols-[60px_1fr_1fr_1fr_100px_1fr_180px] gap-3 border-b px-6 py-3 text-xs font-medium text-muted-foreground uppercase tracking-wider">
            <div>ID</div>
            <div>用户名</div>
            <div>姓名</div>
            <div>联系方式</div>
            <div>状态</div>
            <div>角色</div>
            <div>操作</div>
        </div>

        @forelse($users as $user)
            <div class="grid grid-cols-[60px_1fr_1fr_1fr_100px_1fr_180px] gap-3 border-b last:border-b-0 px-6 py-3 items-center hover:bg-muted/30 transition-colors"
                 wire:key="user-{{ $user->id }}">
                <div class="text-sm text-muted-foreground">{{ $user->id }}</div>
                <div class="text-sm font-medium text-foreground font-mono">{{ $user->username }}</div>
                <div class="text-sm text-foreground">{{ $user->name }}</div>
                <div class="text-sm text-muted-foreground">
                    @if($user->phone)<span>{{ $user->phone }}</span>@endif
                    @if($user->email)<span class="ml-1 text-xs">{{ $user->email }}</span>@endif
                    @if(!$user->phone && !$user->email)<span class="text-muted-foreground">-</span>@endif
                </div>
                <div>
                    @if($user->status === 1)
                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-green-100 text-green-700">启用</span>
                    @else
                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-gray-100 text-gray-600">禁用</span>
                    @endif
                </div>
                <div class="flex flex-wrap gap-1">
                    @forelse($user->roles as $role)
                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-blue-50 text-blue-700">{{ $role->display_name }}</span>
                    @empty
                        <span class="text-xs text-muted-foreground">未分配</span>
                    @endforelse
                </div>
                <div class="flex items-center gap-1.5 flex-wrap">
                    <button wire:click="openEditModal({{ $user->id }})" class="text-blue-600 hover:text-blue-700 text-sm">编辑</button>
                    <button wire:click="openRoleModal({{ $user->id }})" class="text-indigo-600 hover:text-indigo-700 text-sm">角色</button>
                    <button wire:click="toggleStatus({{ $user->id }})" class="text-orange-600 hover:text-orange-700 text-sm">
                        {{ $user->status === 1 ? '禁用' : '启用' }}
                    </button>
                    <button wire:click="confirmResetPassword({{ $user->id }})" class="text-amber-600 hover:text-amber-700 text-sm">重置密码</button>
                    <button wire:click="confirmDelete({{ $user->id }})" class="text-red-600 hover:text-red-700 text-sm">删除</button>
                </div>
            </div>
        @empty
            <div class="px-6 py-12 text-center text-sm text-muted-foreground">暂无用户数据</div>
        @endforelse
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
                <button wire:click="closeModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
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
                <button wire:click="closeDeleteConfirm" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button wire:click="delete" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">删除</button>
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
                <button wire:click="closeResetConfirm" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button wire:click="resetPassword" class="rounded-md bg-amber-600 px-4 py-2 text-sm font-medium text-white hover:bg-amber-700 transition-colors">确认重置</button>
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
                <button wire:click="closeRoleModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button wire:click="saveRoles" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">保存</button>
            </div>
        </div>
    </div>
    @endif
</div>
