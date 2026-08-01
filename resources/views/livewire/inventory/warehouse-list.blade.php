<div class="p-6">
    {{-- 页面标题 --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">仓库管理</h1>
            <p class="text-muted-foreground mt-1">管理仓库信息及冷链配置</p>
        </div>
        @can('inventory.warehouse.create')
        <button wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            新增仓库
        </button>
        @endcan
    </div>

    {{-- 搜索栏 --}}
    <div class="flex items-center gap-3 mb-4">
        <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background px-3 text-sm" placeholder="搜索仓库名称..." />
        <select wire:model.live="filterType" class="flex h-9 w-32 rounded-md border border-input bg-background px-3 text-sm">
            <option value="-1">全部类型</option>
            <option value="1">总仓</option>
            <option value="2">前置仓</option>
        </select>
        <select wire:model.live="filterStatus" class="flex h-9 w-32 rounded-md border border-input bg-background px-3 text-sm">
            <option value="-1">全部状态</option>
            <option value="1">启用</option>
            <option value="0">禁用</option>
        </select>
        <button wire:click="resetFilters" class="text-sm text-muted-foreground hover:text-foreground transition-colors">重置</button>
    </div>

    {{-- 列表 --}}
    <div class="rounded-lg border bg-card">
        <div class="grid grid-cols-[60px_1fr_100px_100px_1fr_80px_100px] gap-3 border-b px-6 py-3 text-xs font-medium text-muted-foreground uppercase tracking-wider">
            <div>ID</div>
            <div>仓库名称</div>
            <div>类型</div>
            <div>冷链</div>
            <div>地址</div>
            <div>状态</div>
            <div>操作</div>
        </div>

        @forelse($items as $item)
            <div class="grid grid-cols-[60px_1fr_100px_100px_1fr_80px_100px] gap-3 border-b last:border-b-0 px-6 py-3 items-center hover:bg-muted/30 transition-colors"
                 wire:key="warehouse-{{ $item->id }}">
                <div class="text-sm text-muted-foreground">{{ $item->id }}</div>
                <div class="text-sm font-medium text-foreground truncate">{{ $item->name }}</div>
                <div class="text-sm text-foreground">{{ \App\Models\Warehouse::typeMap()[$item->type] ?? '-' }}</div>
                <div class="text-sm text-foreground">{{ $item->is_cold_chain ? '是' : '否' }}</div>
                <div class="text-sm text-muted-foreground truncate">{{ $item->address ?: '-' }}</div>
                <div>
                    @if($item->status === 1)
                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-green-100 text-green-700">启用</span>
                    @else
                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-gray-100 text-gray-600">禁用</span>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    @can('inventory.warehouse.edit')
                    <button wire:click="openEditModal({{ $item->id }})" class="text-blue-600 hover:text-blue-700 text-sm">编辑</button>
                    @endcan
                    @can('inventory.warehouse.delete')
                    <button wire:click="confirmDelete({{ $item->id }})" class="text-red-600 hover:text-red-700 text-sm">删除</button>
                    @endcan
                </div>
            </div>
        @empty
            <div class="px-6 py-12 text-center text-sm text-muted-foreground">暂无仓库数据</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $items->links() }}</div>

    {{-- 新增/编辑弹窗 --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" wire:click="closeModal"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4 p-6 max-h-[85vh] overflow-y-auto">
            <h2 class="text-lg font-semibold text-foreground mb-4">{{ $editingId ? '编辑仓库' : '新增仓库' }}</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">仓库名称 <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="formName" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="请输入仓库名称" />
                    @error('formName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">类型 <span class="text-red-500">*</span></label>
                        <select wire:model="formType" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="1">总仓</option>
                            <option value="2">前置仓</option>
                        </select>
                        @error('formType') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">是否冷链 <span class="text-red-500">*</span></label>
                        <select wire:model="formIsColdChain" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="0">否</option>
                            <option value="1">是</option>
                        </select>
                        @error('formIsColdChain') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">地址</label>
                    <input type="text" wire:model="formAddress" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="详细地址" />
                    @error('formAddress') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">状态 <span class="text-red-500">*</span></label>
                    <select wire:model="formStatus" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="1">启用</option>
                        <option value="0">禁用</option>
                    </select>
                    @error('formStatus') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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
            <p class="text-sm text-muted-foreground mb-6">确定要删除该仓库吗？此操作不可恢复。</p>
            <div class="flex justify-end gap-3">
                <button wire:click="closeDeleteConfirm" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button wire:click="delete" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">删除</button>
            </div>
        </div>
    </div>
    @endif
</div>
