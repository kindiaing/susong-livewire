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
        <div class="grid grid-cols-[60px_1fr_1fr_80px_80px_140px] gap-3 border-b px-6 py-3 text-xs font-medium text-muted-foreground uppercase tracking-wider">
            <div>ID</div>
            <div>角色标识</div>
            <div>显示名称</div>
            <div>用户数</div>
            <div>权限数</div>
            <div>操作</div>
        </div>

        @forelse($roles as $role)
            <div class="grid grid-cols-[60px_1fr_1fr_80px_80px_140px] gap-3 border-b last:border-b-0 px-6 py-3 items-center hover:bg-muted/30 transition-colors"
                 wire:key="role-{{ $role->id }}">
                <div class="text-sm text-muted-foreground">{{ $role->id }}</div>
                <div class="text-sm font-medium text-foreground font-mono">{{ $role->name }}</div>
                <div class="text-sm text-foreground">{{ $role->display_name }}</div>
                <div class="text-sm text-muted-foreground">{{ $role->users_count }}</div>
                <div class="text-sm text-muted-foreground">{{ $role->permissions_count }}</div>
                <div class="flex items-center gap-2">
                    <button wire:click="openEditModal({{ $role->id }})" class="text-blue-600 hover:text-blue-700 text-sm">编辑</button>
                    <button wire:click="openPermissionModal({{ $role->id }})" class="text-indigo-600 hover:text-indigo-700 text-sm">权限</button>
                    <button wire:click="confirmDelete({{ $role->id }})" class="text-red-600 hover:text-red-700 text-sm">删除</button>
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
        <div class="fixed inset-0 bg-black/50" wire:click="closeModal"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-4">{{ $editingId ? '编辑角色' : '新增角色' }}</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">角色标识 <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="formName" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="如 super-admin" />
                    @error('formName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">显示名称 <span class="text-red-500">*</span></label>
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
            <p class="text-sm text-muted-foreground mb-6">确定要删除该角色吗？如果角色下有用户将无法删除。</p>
            <div class="flex justify-end gap-3">
                <button wire:click="closeDeleteConfirm" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button wire:click="delete" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">删除</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 权限分配弹窗（树形） --}}
    @if($showPermissionModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" wire:click="closePermissionModal"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-1">分配权限</h2>
            <p class="text-sm text-muted-foreground mb-4">角色：{{ $permissionRoleName }}</p>

            <div class="space-y-1 max-h-[400px] overflow-y-auto border rounded-md p-3">
                @foreach($permissionTree as $module)
                    {{-- 模块级 --}}
                    <div class="mb-2">
                        <label class="flex items-center gap-2 px-2 py-1.5 rounded bg-muted/30 cursor-pointer font-medium">
                            <input
                                type="checkbox"
                                value="{{ $module->id }}"
                                wire:model.live="formPermissionIds"
                                class="h-4 w-4 rounded border-input text-blue-600 focus:ring-blue-500"
                            />
                            <span class="text-sm text-foreground">{{ $module->display_name }}</span>
                            <span class="text-xs text-muted-foreground font-mono ml-1">{{ $module->name }}</span>
                        </label>

                        @if($module->children->isNotEmpty())
                            <div class="ml-6 mt-1 space-y-1">
                                @foreach($module->children as $page)
                                    {{-- 页面级 --}}
                                    <div>
                                        <label class="flex items-center gap-2 px-2 py-1 rounded hover:bg-muted/30 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                value="{{ $page->id }}"
                                                wire:model.live="formPermissionIds"
                                                class="h-4 w-4 rounded border-input text-green-600 focus:ring-green-500"
                                            />
                                            <span class="inline-flex items-center rounded px-1 py-0.5 text-[10px] font-medium bg-green-50 text-green-700">页面</span>
                                            <span class="text-sm text-foreground">{{ $page->display_name }}</span>
                                        </label>

                                        @if($page->children->isNotEmpty())
                                            <div class="ml-6 mt-1 space-y-0.5">
                                                @foreach($page->children as $btn)
                                                    <label class="flex items-center gap-2 px-2 py-0.5 rounded hover:bg-muted/30 cursor-pointer">
                                                        <input
                                                            type="checkbox"
                                                            value="{{ $btn->id }}"
                                                            wire:model.live="formPermissionIds"
                                                            class="h-3.5 w-3.5 rounded border-input text-orange-600 focus:ring-orange-500"
                                                        />
                                                        <span class="inline-flex items-center rounded px-1 py-0.5 text-[10px] font-medium bg-orange-50 text-orange-700">按钮</span>
                                                        <span class="text-sm text-foreground">{{ $btn->display_name }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach

                @if($permissionTree->isEmpty())
                    <div class="text-center text-sm text-muted-foreground py-4">暂无权限数据，请先在权限管理中创建</div>
                @endif
            </div>

            <div class="flex justify-between items-center mt-4">
                <div class="text-xs text-muted-foreground">已选 {{ count($formPermissionIds) }} 项权限</div>
                <div class="flex gap-3">
                    <button wire:click="closePermissionModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                    <button wire:click="savePermissions" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">保存</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
