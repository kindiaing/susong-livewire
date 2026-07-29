<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">权限管理</h1>
            <p class="text-muted-foreground mt-1">管理系统权限节点（模块/页面/按钮）及角色分配</p>
        </div>
        <button wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            新增权限
        </button>
    </div>

    <div class="flex items-center gap-3 mb-4">
        <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background px-3 text-sm" placeholder="搜索权限名称..." />
        <button wire:click="resetFilters" class="text-sm text-muted-foreground hover:text-foreground transition-colors">重置</button>
    </div>

    <div class="rounded-lg border bg-card">
        <div class="grid grid-cols-[60px_1fr_1fr_80px_100px_80px_140px] gap-3 border-b px-6 py-3 text-xs font-medium text-muted-foreground uppercase tracking-wider">
            <div>ID</div>
            <div>权限标识</div>
            <div>显示名称</div>
            <div>类型</div>
            <div>关联角色数</div>
            <div>排序</div>
            <div>操作</div>
        </div>

        @forelse($permissions as $perm)
            <div class="grid grid-cols-[60px_1fr_1fr_80px_100px_80px_140px] gap-3 border-b last:border-b-0 px-6 py-3 items-center hover:bg-muted/30 transition-colors"
                 wire:key="perm-{{ $perm->id }}">
                <div class="text-sm text-muted-foreground">{{ $perm->id }}</div>
                <div class="text-sm font-medium text-foreground font-mono">{{ $perm->name }}</div>
                <div class="text-sm text-foreground">{{ $perm->display_name }}</div>
                <div>
                    @php $typeLabel = \App\Models\Permission::typeMap()[$perm->type] ?? '未知'; @endphp
                    <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium {{ $perm->type === 1 ? 'bg-blue-100 text-blue-700' : ($perm->type === 2 ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700') }}">
                        {{ $typeLabel }}
                    </span>
                </div>
                <div class="text-sm text-muted-foreground">{{ $perm->roles_count }}</div>
                <div class="text-sm text-muted-foreground">{{ $perm->sort }}</div>
                <div class="flex items-center gap-2">
                    <button wire:click="openEditModal({{ $perm->id }})" class="text-blue-600 hover:text-blue-700 text-sm">编辑</button>
                    <button wire:click="openRoleModal({{ $perm->id }})" class="text-indigo-600 hover:text-indigo-700 text-sm">角色</button>
                    <button wire:click="confirmDelete({{ $perm->id }})" class="text-red-600 hover:text-red-700 text-sm">删除</button>
                </div>
            </div>
        @empty
            <div class="px-6 py-12 text-center text-sm text-muted-foreground">暂无权限数据</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $permissions->links() }}</div>

    {{-- 新增/编辑弹窗 --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" wire:click="showModal = false"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-4">{{ $editingId ? '编辑权限' : '新增权限' }}</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">权限标识 <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="formName" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="如 user.manage" />
                    @error('formName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">显示名称 <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="formDisplayName" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="如 用户管理" />
                    @error('formDisplayName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">类型</label>
                        <select wire:model="formType" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="1">模块</option>
                            <option value="2">页面</option>
                            <option value="3">按钮</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">上级权限</label>
                        <select wire:model="formParentId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="0">顶级</option>
                            @foreach($parentOptions as $p)
                                <option value="{{ $p->id }}">{{ $p->display_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">路由</label>
                    <input type="text" wire:model="formRoute" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="可选" />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">排序</label>
                        <input type="number" wire:model="formSort" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">图标</label>
                        <input type="text" wire:model="formIcon" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="可选" />
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button wire:click="showModal = false" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button wire:click="save" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">保存</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 删除确认弹窗 --}}
    @if($showDeleteConfirm)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" wire:click="showDeleteConfirm = false"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-sm mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-2">确认删除</h2>
            <p class="text-sm text-muted-foreground mb-6">确定要删除该权限吗？已被角色引用的权限无法删除。</p>
            <div class="flex justify-end gap-3">
                <button wire:click="showDeleteConfirm = false" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button wire:click="delete" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">删除</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 角色分配弹窗 --}}
    @if($showRoleModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" wire:click="showRoleModal = false"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-1">分配角色</h2>
            <p class="text-sm text-muted-foreground mb-4">权限：{{ $rolePermissionName }}</p>
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

                @if(empty($allRoles))
                    <div class="text-center text-sm text-muted-foreground py-4">暂无角色数据，请先在角色管理中创建</div>
                @endif
            </div>
            <div class="flex justify-between items-center mt-4">
                <div class="text-xs text-muted-foreground">已选 {{ count($formRoleIds) }} 个角色</div>
                <div class="flex gap-3">
                    <button wire:click="showRoleModal = false" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                    <button wire:click="saveRoles" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">保存</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
