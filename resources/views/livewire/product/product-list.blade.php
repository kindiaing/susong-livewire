<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">商品管理</h1>
            <p class="text-muted-foreground mt-1">管理商品基本信息及上下架状态</p>
        </div>
        <button wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            新增商品
        </button>
    </div>

    <div class="flex items-center gap-3 mb-4">
        <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background px-3 text-sm" placeholder="搜索商品名称..." />
        <select wire:model.live="filterCategoryId" class="flex h-9 rounded-md border border-input bg-background px-3 text-sm">
            <option value="0">全部分类</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
        </select>
        <select wire:model.live="filterStatus" class="flex h-9 rounded-md border border-input bg-background px-3 text-sm">
            <option value="-1">全部状态</option>
            <option value="1">上架</option>
            <option value="0">下架</option>
        </select>
        <button wire:click="resetFilters" class="text-sm text-muted-foreground hover:text-foreground transition-colors">重置</button>
    </div>

    <div class="rounded-lg border bg-card">
        <div class="grid grid-cols-[60px_1fr_120px_60px_80px_80px_80px_100px] gap-3 border-b px-6 py-3 text-xs font-medium text-muted-foreground uppercase tracking-wider">
            <div>ID</div>
            <div>商品名称</div>
            <div>分类</div>
            <div>单位</div>
            <div>称重改价</div>
            <div>预警值</div>
            <div>状态</div>
            <div>操作</div>
        </div>
        @forelse($products as $product)
            <div class="grid grid-cols-[60px_1fr_120px_60px_80px_80px_80px_100px] gap-3 border-b last:border-b-0 px-6 py-3 items-center hover:bg-muted/30 transition-colors" wire:key="product-{{ $product->id }}">
                <div class="text-sm text-muted-foreground">{{ $product->id }}</div>
                <div class="text-sm font-medium text-foreground truncate">{{ $product->name }}</div>
                <div class="text-sm text-foreground">{{ $product->category?->name ?? '-' }}</div>
                <div class="text-sm text-foreground">{{ $product->unit }}</div>
                <div class="text-sm text-foreground">{{ $product->is_weight_priced ? '是' : '否' }}</div>
                <div class="text-sm text-foreground">{{ $product->stock_warning_value }}</div>
                <div>
                    @if($product->status === 1)
                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-green-100 text-green-700">上架</span>
                    @else
                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-gray-100 text-gray-600">下架</span>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <button wire:click="openEditModal({{ $product->id }})" class="text-blue-600 hover:text-blue-700 text-sm">编辑</button>
                    <button wire:click="confirmDelete({{ $product->id }})" class="text-red-600 hover:text-red-700 text-sm">删除</button>
                </div>
            </div>
        @empty
            <div class="px-6 py-12 text-center text-sm text-muted-foreground">暂无商品数据</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $products->links() }}</div>

    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" wire:click="showModal = false"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4 p-6 max-h-[85vh] overflow-y-auto">
            <h2 class="text-lg font-semibold text-foreground mb-4">{{ $editingId ? '编辑商品' : '新增商品' }}</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">商品名称 <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="formName" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="请输入商品名称" />
                    @error('formName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">分类 <span class="text-red-500">*</span></label>
                        <select wire:model="formCategoryId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="0">请选择分类</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('formCategoryId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">单位 <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="formUnit" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="如：斤、箱、份" />
                        @error('formUnit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">称重改价</label>
                        <select wire:model="formIsWeightPriced" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="0">否</option>
                            <option value="1">是</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">库存预警值</label>
                        <input type="number" wire:model="formStockWarningValue" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" min="0" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">状态 <span class="text-red-500">*</span></label>
                        <select wire:model="formStatus" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="1">上架</option>
                            <option value="0">下架</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">商品详情</label>
                    <textarea wire:model="formDescription" rows="3" class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="可选"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button wire:click="showModal = false" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button wire:click="save" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">保存</button>
            </div>
        </div>
    </div>
    @endif

    @if($showDeleteConfirm)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" wire:click="showDeleteConfirm = false"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-sm mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-2">确认删除</h2>
            <p class="text-sm text-muted-foreground mb-6">确定要删除该商品吗？此操作不可恢复。</p>
            <div class="flex justify-end gap-3">
                <button wire:click="showDeleteConfirm = false" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button wire:click="delete" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">删除</button>
            </div>
        </div>
    </div>
    @endif
</div>
