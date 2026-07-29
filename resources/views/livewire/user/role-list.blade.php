<div class="p-6">
    {{-- 页面标题 --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">角色管理</h1>
            <p class="text-muted-foreground mt-1">管理系统角色及权限分配</p>
        </div>
        <button wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            新增角色
        </button>
    </div>

    {{-- 搜索栏 --}}
    <div class="flex items-center gap-3 mb-4">
        <input
            type="text"
            wire:model.live="search"
            class="flex h-9 w-64 rounded-md border border-input bg-background px-3 text-sm"
            placeholder="搜索角色名称..."
        />
        <button wire:click="resetFilters" class="text-sm text-muted-foreground hover:text-foreground transition-colors">重置</button>
    </div>

    {{-- 角色列表 --}}
    <div class="rounded-lg border bg-card">
        <div class="grid grid-cols-[60px_1fr_1fr_100px_100px_120px] gap-3 border-b px-6 py-3 text-xs font-medium text-muted-foreground uppercase tracking-wider">
            <div>ID</div>
            <div>角色标识</div>
            <div>显示名称</div>
            <div>用户数</div>
            <div>守卫</div>
            <div>操作</div>
        </div>

        @forelse($roles as $role)
            <div class="grid grid-cols-[60px_1fr_1fr_100px_100px_120px] gap-3 border-b last:border-b-0 px-6 py-3 items-center hover:bg-muted/30 transition-colors"
                 wire:key="role-{{ $role->id }}">
                <div class="text-sm text-muted-foreground">{{ $role->id }}</div>
                <div class="text-sm font-medium text-foreground font-mono">{{ $role->name }}</div>
                <div class="text-sm text-foreground">{{ $role->display_name }}</div>
                <div class="text-sm text-muted-foreground">{{ $role->users_count }}</div>
                <div class="text-sm text-muted-foreground">{{ $role->guard_name }}</div>
                <div class="flex items-center gap-2">
                    <button wire:click="openEditModal({{ $role->id }})" class="text-blue-600 hover:text-blue-700 text-sm">编辑</button>
                    <button wire:click="delete({{ $role->id }})" class="text-red-600 hover:text-red-700 text-sm" onclick="return confirm('确定删除该角色？')">删除</button>
                </div>
            </div>
        @empty
            <div class="px-6 py-12 text-center text-sm text-muted-foreground">暂无角色数据</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $roles->links() }}</div>

    {{-- 新增/编辑弹窗 --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" wire:click="showModal = false"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-4">{{ $editingId ? '编辑角色' : '新增角色' }}</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">角色标识</label>
                    <input type="text" wire:model="formName" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="如 super-admin" />
                    @error('formName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">显示名称</label>
                    <input type="text" wire:model="formDisplayName" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="如 超级管理员" />
                    @error('formDisplayName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">守卫名称</label>
                    <input type="text" wire:model="formGuardName" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">描述</label>
                    <textarea wire:model="formDescription" rows="2" class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="可选"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button wire:click="showModal = false" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button wire:click="save" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">保存</button>
            </div>
        </div>
    </div>
    @endif
</div>
