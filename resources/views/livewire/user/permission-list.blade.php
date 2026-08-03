<div class="">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">权限管理</h1>
            <p class="text-muted-foreground mt-1">管理系统权限节点（模块/页面/按钮）及角色分配</p>
        </div>
        @can('user.permission.create')
        <button type="button" wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            新增权限
        </button>
        @endcan
    </div>

    <div class="flex items-center gap-3 mb-4 flex-wrap">
        <div x-data class="relative">
            <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background pl-3 pr-8 text-sm" placeholder="搜索权限名称..." />
            @if($search)
                <button type="button" wire:click="resetFilters" class="absolute right-2 top-1/2 -translate-y-1/2 p-0.5 rounded-sm text-muted-foreground/50 hover:text-muted-foreground hover:bg-muted transition-colors">
                    <x-ui.icon name="x-mark" class="w-3.5 h-3.5" />
                </button>
            @endif
        </div>

        {{-- 类型筛选 --}}
        <select wire:model.live="filterType" class="flex h-9 rounded-md border border-input bg-background px-3 text-sm">
            <option value="0">全部类型</option>
            <option value="1">模块</option>
            <option value="2">页面</option>
            <option value="3">按钮</option>
        </select>

        {{-- 模块筛选 --}}
        <select wire:model.live="filterModule" class="flex h-9 rounded-md border border-input bg-background px-3 pr-8 text-sm max-w-48">
            <option value="0">全部模块</option>
            @foreach($moduleOptions as $mod)
                <option value="{{ $mod->id }}">{{ $mod->display_name }}</option>
            @endforeach
        </select>
    </div>

    <div class="rounded-lg border bg-card">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                    <th class="px-4 py-2 text-left w-16">ID</th>
                    <th class="px-4 py-2 text-left">权限标识</th>
                    <th class="px-4 py-2 text-left">显示名称</th>
                    <th class="px-4 py-2 text-left">类型</th>
                    <th class="px-4 py-2 text-left">关联角色数</th>
                    <th class="px-4 py-2 text-left">排序</th>
                    <th class="px-4 py-2 text-left w-24">操作</th>
                </tr>
            </thead>
            <tbody>
                @forelse($permissions as $perm)
                <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="perm-{{ $perm->id }}">
                    <td class="px-4 py-2 text-muted-foreground">{{ $perm->id }}</td>
                    <td class="px-4 py-2 font-medium text-foreground font-mono">{{ $perm->name }}</td>
                    <td class="px-4 py-2 text-foreground">{{ $perm->display_name }}</td>
                    <td class="px-4 py-2">
                        @php $typeLabel = \App\Models\Permission::typeMap()[$perm->type] ?? '未知'; @endphp
                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium {{ $perm->type === 1 ? 'bg-blue-100 text-blue-700' : ($perm->type === 2 ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700') }}">
                            {{ $typeLabel }}
                        </span>
                    </td>
                    <td class="px-4 py-2 text-muted-foreground">{{ $perm->roles_count }}</td>
                    <td class="px-4 py-2 text-muted-foreground">{{ $perm->sort }}</td>
                    <td class="px-4 py-2">
                        <div class="flex items-center gap-1">
                            {{-- 编辑 --}}
                            @can('user.permission.edit')
                            <button type="button" wire:click="openEditModal({{ $perm->id }})" class="p-1.5 rounded-md text-blue-600 hover:bg-blue-50 hover:text-blue-700 transition-colors" title="编辑">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                            </button>
                            @endcan
                            {{-- 角色分配 --}}
                            @can('user.permission.edit')
                            <button type="button" wire:click="openRoleModal({{ $perm->id }})" class="p-1.5 rounded-md text-indigo-600 hover:bg-indigo-50 hover:text-indigo-700 transition-colors" title="角色分配">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A9.75 9.75 0 016.75 5.087 9.75 9.75 0 0112 4.5c2.048 0 3.94.583 5.468 1.587A9.75 9.75 0 0120.25 9v.75c0 5.385-3.597 10.02-8.25 11.642a1.5 1.5 0 01-1 0C6.02 19.772 2.25 15.135 2.25 9.75V9A9.75 9.75 0 014.686 6.087z" />
                                </svg>
                            </button>
                            @endcan
                            {{-- 删除 --}}
                            @can('user.permission.delete')
                            <button type="button" wire:click="confirmDelete({{ $perm->id }})" class="p-1.5 rounded-md text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors" title="删除">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-12 text-center text-muted-foreground">暂无权限数据</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $permissions->links() }}</div>

    {{-- 新增/编辑弹窗 --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
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
                <button type="button" wire:click="closeModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="save" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">保存</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 删除确认弹窗 --}}
    @if($showDeleteConfirm)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-sm mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-2">确认删除</h2>
            <p class="text-sm text-muted-foreground mb-6">确定要删除该权限吗？已被角色引用的权限无法删除。</p>
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="closeDeleteConfirm" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="delete" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">删除</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 角色分配弹窗 --}}
    @if($showRoleModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
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
                    <button type="button" wire:click="closeRoleModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                    <button type="button" wire:click="saveRoles" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">保存</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @include('partials.column-modal')
    @include('partials.export-modal')
    @include('partials.import-modal')
</div>
