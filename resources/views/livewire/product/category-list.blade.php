<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">分类管理</h1>
            <p class="text-muted-foreground mt-1">商品分类，支持无限级树形结构</p>
        </div>
        @can('product.category.create')
        <button type="button" wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            新增分类
        </button>
        @endcan
    </div>

    <div class="flex items-center gap-3 mb-4">
        <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background px-3 text-sm" placeholder="搜索分类名称..." />
        <button type="button" wire:click="resetFilters" class="text-sm text-muted-foreground hover:text-foreground transition-colors">重置</button>
        <div class="flex-1"></div>
        <button type="button" wire:click="openColumnModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors">列配置</button>
        <button type="button" wire:click="openImportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors">导入</button>
        <button type="button" wire:click="openExportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors">导出</button>
        @if($selectedCount > 0)
            <span class="text-sm text-muted-foreground">已选 {{ $selectedCount }} 项</span>
            @can('product.category.delete')
            <button type="button" wire:click="batchDelete" class="inline-flex items-center gap-1 rounded-md bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700 transition-colors">批量删除</button>
            @endcan
            <button type="button" wire:click="clearSelection" class="text-sm text-muted-foreground hover:text-foreground transition-colors">取消选择</button>
        @endif
    </div>

    <div class="rounded-lg border bg-card">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                    <th class="px-4 py-2 text-left w-10"><input type="checkbox" wire:model.live="selectAll" class="rounded" /></th>
                    <th class="px-4 py-2 text-left w-16">ID</th>
                    <th class="px-4 py-2 text-left">分类名称</th>
                    <th class="px-4 py-2 text-left">父级分类</th>
                    <th class="px-4 py-2 text-left">排序</th>
                    <th class="px-4 py-2 text-left">状态</th>
                    <th class="px-4 py-2 text-left w-24">操作</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="category-{{ $category->id }}">
                    <td class="px-4 py-2"><input type="checkbox" value="{{ $category->id }}" wire:model.live="selectedIds" class="rounded" /></td>
                    <td class="px-4 py-2 text-muted-foreground">{{ $category->id }}</td>
                    <td class="px-4 py-2 font-medium text-foreground">{{ $category->name }}</td>
                    <td class="px-4 py-2 text-foreground">{{ $category->parent_id == 0 ? '根节点' : ($category->parent?->name ?? '-') }}</td>
                    <td class="px-4 py-2 text-foreground">{{ $category->sort }}</td>
                    <td class="px-4 py-2">
                        @if($category->status === 1)
                            <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-green-100 text-green-700">启用</span>
                        @else
                            <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-gray-100 text-gray-600">禁用</span>
                        @endif
                    </td>
                    <td class="px-4 py-2">
                        <div class="flex items-center gap-2">
                            @can('product.category.edit')
                            <button type="button" wire:click="openEditModal({{ $category->id }})" class="text-blue-600 hover:text-blue-700 text-sm">编辑</button>
                            @endcan
                            @can('product.category.delete')
                            <button type="button" wire:click="confirmDelete({{ $category->id }})" class="text-red-600 hover:text-red-700 text-sm">删除</button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-12 text-center text-muted-foreground">暂无分类数据</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $categories->links() }}</div>

    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" wire:click="closeModal"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4 p-6 max-h-[85vh] overflow-y-auto">
            <h2 class="text-lg font-semibold text-foreground mb-4">{{ $editingId ? '编辑分类' : '新增分类' }}</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">分类名称 <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="formName" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="请输入分类名称" />
                    @error('formName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">父级分类</label>
                    <select wire:model="formParentId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="0">根节点</option>
                        @foreach($parentOptions as $opt)
                            <option value="{{ $opt->id }}">{{ $opt->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">排序</label>
                        <input type="number" wire:model="formSort" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" min="0" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">状态 <span class="text-red-500">*</span></label>
                        <select wire:model="formStatus" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="1">启用</option>
                            <option value="0">禁用</option>
                        </select>
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

    @if($showDeleteConfirm)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" wire:click="closeDeleteConfirm"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-sm mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-2">确认删除</h2>
            <p class="text-sm text-muted-foreground mb-6">确定要删除该分类吗？此操作不可恢复。</p>
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="closeDeleteConfirm" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="delete" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">删除</button>
            </div>
        </div>
    </div>
    @endif

    @include('partials.column-modal')
    @include('partials.export-modal')
    @include('partials.import-modal')
    @include('partials.delete-confirm')
</div>
