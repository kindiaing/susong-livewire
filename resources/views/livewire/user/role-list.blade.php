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
        <div class="grid grid-cols-[60px_1fr_1fr_80px_80px_120px] gap-3 border-b px-6 py-3 text-xs font-medium text-muted-foreground uppercase tracking-wider">
            <div>ID</div>
            <div>角色标识</div>
            <div>显示名称</div>
            <div>用户数</div>
            <div>权限数</div>
            <div>操作</div>
        </div>

        @forelse($roles as $role)
            @php $isSuperAdmin = $role->name === 'super_admin' @endphp
            <div class="grid grid-cols-[60px_1fr_1fr_80px_80px_120px] gap-3 border-b last:border-b-0 px-6 py-3 items-center hover:bg-muted/30 transition-colors"
                 wire:key="role-{{ $role->id }}">
                <div class="text-sm text-muted-foreground">{{ $role->id }}</div>
                <div class="text-sm font-medium text-foreground font-mono">{{ $role->name }}</div>
                <div class="text-sm text-foreground">{{ $role->display_name }}</div>
                <div class="text-sm text-muted-foreground">{{ $role->users_count }}</div>
                <div class="text-sm text-muted-foreground">{{ $role->permissions_count }}</div>
                <div class="flex items-center gap-1">
                    {{-- 编辑（超级管理员不可编辑） --}}
                    @if(!$isSuperAdmin)
                        <button wire:click="openEditModal({{ $role->id }})" class="p-1.5 rounded-md text-blue-600 hover:bg-blue-50 hover:text-blue-700 transition-colors" title="编辑">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                        </button>
                    @endif
                    {{-- 权限分配 --}}
                    <button wire:click="openPermissionModal({{ $role->id }})" class="p-1.5 rounded-md text-indigo-600 hover:bg-indigo-50 hover:text-indigo-700 transition-colors" title="权限分配">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A9.75 9.75 0 016.75 5.087 9.75 9.75 0 0112 4.5c2.048 0 3.94.583 5.468 1.587A9.75 9.75 0 0120.25 9v.75c0 5.385-3.597 10.02-8.25 11.642a1.5 1.5 0 01-1 0C6.02 19.772 2.25 15.135 2.25 9.75V9A9.75 9.75 0 014.686 6.087z" />
                        </svg>
                    </button>
                    {{-- 删除（超级管理员不可删除） --}}
                    @if(!$isSuperAdmin)
                        <button wire:click="confirmDelete({{ $role->id }})" class="p-1.5 rounded-md text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors" title="删除">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                        </button>
                    @endif
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
