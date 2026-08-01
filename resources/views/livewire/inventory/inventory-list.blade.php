<div class="p-6">
    {{-- 页面标题 --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">实时库存</h1>
            <p class="text-muted-foreground mt-1">查看和管理各仓库SKU库存</p>
        </div>
        @can('inventory.inventory.create')
        <button wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            新增库存
        </button>
        @endcan
            新增库存
        </button>
    </div>

    {{-- 搜索栏 --}}
    <div class="flex items-center gap-3 mb-4">
        <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background px-3 text-sm" placeholder="搜索SKU编码/商品名称..." />
        <select wire:model.live="filterWarehouseId" class="flex h-9 w-40 rounded-md border border-input bg-background px-3 text-sm">
            <option value="0">全部仓库</option>
            @foreach($warehouses as $w)
                <option value="{{ $w->id }}">{{ $w->name }}</option>
            @endforeach
        </select>
        <button wire:click="resetFilters" class="text-sm text-muted-foreground hover:text-foreground transition-colors">重置</button>
    </div>

    {{-- 列表 --}}
    <div class="rounded-lg border bg-card overflow-x-auto">
        <div class="grid grid-cols-[60px_1fr_1fr_100px_100px_100px_120px_100px] gap-3 border-b px-6 py-3 text-xs font-medium text-muted-foreground uppercase tracking-wider min-w-[900px]">
            <div>ID</div>
            <div>仓库</div>
            <div>SKU</div>
            <div>总库存</div>
            <div>锁定</div>
            <div>可用</div>
            <div>效期</div>
            <div>操作</div>
        </div>

        @forelse($items as $item)
            <div class="grid grid-cols-[60px_1fr_1fr_100px_100px_100px_120px_100px] gap-3 border-b last:border-b-0 px-6 py-3 items-center hover:bg-muted/30 transition-colors min-w-[900px]"
                 wire:key="inventory-{{ $item->id }}">
                <div class="text-sm text-muted-foreground">{{ $item->id }}</div>
                <div class="text-sm text-foreground">{{ $item->warehouse?->name ?? '-' }}</div>
                <div class="text-sm text-foreground">
                    {{ $item->sku?->sku_code ?? '-' }}
                    <span class="text-muted-foreground text-xs ml-1">{{ $item->sku?->product?->name ?? '' }}</span>
                </div>
                <div class="text-sm text-foreground">{{ $item->total_stock }}</div>
                <div class="text-sm text-foreground">{{ $item->locked_stock }}</div>
                <div class="text-sm {{ $item->available_stock <= $item->warning_value ? 'text-red-600 font-medium' : 'text-foreground' }}">{{ $item->available_stock }}</div>
                <div class="text-sm text-muted-foreground">{{ $item->expiry_date ? $item->expiry_date->format('Y-m-d') : '-' }}</div>
                <div class="flex items-center gap-2">
                    @can('inventory.inventory.edit')
                    <button wire:click="openEditModal({{ $item->id }})" class="text-blue-600 hover:text-blue-700 text-sm">编辑</button>
                    @endcan
                    @can('inventory.inventory.delete')
                    <button wire:click="confirmDelete({{ $item->id }})" class="text-red-600 hover:text-red-700 text-sm">删除</button>
                    @endcan
                </div>
            </div>
        @empty
            <div class="px-6 py-12 text-center text-sm text-muted-foreground">暂无库存数据</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $items->links() }}</div>

    {{-- 新增/编辑弹窗 --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" wire:click="closeModal"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4 p-6 max-h-[85vh] overflow-y-auto">
            <h2 class="text-lg font-semibold text-foreground mb-4">{{ $editingId ? '编辑库存' : '新增库存' }}</h2>
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">仓库 <span class="text-red-500">*</span></label>
                        <select wire:model="formWarehouseId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="0">请选择</option>
                            @foreach($warehouses as $w)
                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                            @endforeach
                        </select>
                        @error('formWarehouseId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">SKU <span class="text-red-500">*</span></label>
                        <select wire:model="formSkuId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="0">请选择</option>
                            @foreach($skus as $s)
                                <option value="{{ $s->id }}">{{ $s->sku_code }}</option>
                            @endforeach
                        </select>
                        @error('formSkuId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">总库存 <span class="text-red-500">*</span></label>
                        <input type="number" wire:model="formTotalStock" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" min="0" />
                        @error('formTotalStock') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">锁定库存 <span class="text-red-500">*</span></label>
                        <input type="number" wire:model="formLockedStock" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" min="0" />
                        @error('formLockedStock') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">可用库存 <span class="text-red-500">*</span></label>
                        <input type="number" wire:model="formAvailableStock" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" min="0" />
                        @error('formAvailableStock') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">批次号</label>
                        <input type="text" wire:model="formBatchNo" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="可选" />
                        @error('formBatchNo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">效期</label>
                        <input type="date" wire:model="formExpiryDate" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                        @error('formExpiryDate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">预警值 <span class="text-red-500">*</span></label>
                        <input type="number" wire:model="formWarningValue" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" min="0" />
                        @error('formWarningValue') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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
            <p class="text-sm text-muted-foreground mb-6">确定要删除该库存记录吗？此操作不可恢复。</p>
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="closeDeleteConfirm" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button wire:click="delete" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">删除</button>
            </div>
        </div>
    </div>
    @endif
</div>
