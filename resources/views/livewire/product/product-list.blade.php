<div class="">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">商品管理</h1>
            <p class="text-muted-foreground mt-1">管理商品基本信息及上下架状态</p>
        </div>
        @can('product.product.create')
        <button type="button" wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            <x-ui.icon name="plus" class="w-4 h-4" />
            新增商品
        </button>
        @endcan
    </div>

    <div class="flex items-center gap-3 mb-4">
        <div x-data class="relative">
            <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background pl-3 pr-8 text-sm" placeholder="搜索商品名称..." />
            @if($search)
                <button type="button" wire:click="resetFilters" class="absolute right-2 top-1/2 -translate-y-1/2 p-0.5 rounded-sm text-muted-foreground/50 hover:text-muted-foreground hover:bg-muted transition-colors">
                    <x-ui.icon name="x-mark" class="w-3.5 h-3.5" />
                </button>
            @endif
        </div>
        <div class="w-48">
            <x-ui.searchable-select
                placeholder="全部分类"
                :options="$categoryOptions"
                :clearable="true"
                :wireModel="'filterCategoryId'"
                :value="$filterCategoryId"
            />
        </div>
        <select wire:model.live="filterStatus" class="flex h-9 rounded-md border border-input bg-background px-3 text-sm">
            <option value="-1">全部状态</option>
            <option value="1">上架</option>
            <option value="0">下架</option>
        </select>
        <div class="flex-1"></div>
        <button type="button" wire:click="openColumnModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="adjustments" class="w-4 h-4" />列配置</button>
        <button type="button" wire:click="openImportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-up-tray" class="w-4 h-4" />导入</button>
        <button type="button" wire:click="openExportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-down-tray" class="w-4 h-4" />导出</button>
        @if($selectedCount > 0)
            <span class="text-sm text-muted-foreground">已选 {{ $selectedCount }} 项</span>
            @can('product.product.delete')
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
                    <th class="px-4 py-2 text-left">商品名称</th>
                    <th class="px-4 py-2 text-left">分类</th>
                    <th class="px-4 py-2 text-left">单位</th>
                    <th class="px-4 py-2 text-left">称重改价</th>
                    <th class="px-4 py-2 text-left">预警值</th>
                    <th class="px-4 py-2 text-left">状态</th>
                    <th class="px-4 py-2 text-left w-24">操作</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="product-{{ $product->id }}">
                    <td class="px-4 py-2"><input type="checkbox" value="{{ $product->id }}" wire:model.live="selectedIds" class="rounded" /></td>
                    <td class="px-4 py-2 text-muted-foreground">{{ $product->id }}</td>
                    <td class="px-4 py-2 font-medium text-foreground truncate">{{ $product->name }}</td>
                    <td class="px-4 py-2 text-foreground">{{ $product->category?->name ?? '-' }}</td>
                    <td class="px-4 py-2 text-foreground">{{ $product->unit }}</td>
                    <td class="px-4 py-2 text-foreground">{{ $product->is_weight_priced ? '是' : '否' }}</td>
                    <td class="px-4 py-2 text-foreground">{{ $product->stock_warning_value }}</td>
                    <td class="px-4 py-2">
                        {!! status_badge($product->status, 'active') !!}
                    </td>
                    <td class="px-4 py-2">
                        <div class="flex items-center gap-2">
                            @can('product.product.edit')
                            <button type="button" wire:click="openEditModal({{ $product->id }})" class="p-1 rounded text-blue-600 hover:bg-blue-50 hover:text-blue-700 transition-colors" title="编辑"><x-ui.icon name="pencil" class="w-3.5 h-3.5" /></button>
                            @endcan
                            @can('product.product.delete')
                            <button type="button" wire:click="confirmDelete({{ $product->id }})" class="p-1 rounded text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors" title="删除"><x-ui.icon name="trash" class="w-3.5 h-3.5" /></button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="px-6 py-12 text-center text-muted-foreground">暂无商品数据</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $products->links() }}</div>

    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4 p-6 max-h-[85vh] overflow-y-auto">
            <h2 class="text-lg font-semibold text-foreground mb-4">{{ $editingId ? '编辑商品' : '新增商品' }}</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">商品名称 <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="formName" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="请输入商品名称" />
                    @error('formName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">封面图</label>
                    <input type="text" wire:model="formCover" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="图片URL或路径" />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-ui.searchable-select
                            label="分类"
                            placeholder="请选择分类"
                            :options="$categoryOptions"
                            :clearable="true"
                            :wireModel="'formCategoryId'"
                            :value="$formCategoryId"
                            :error="$errors->first('formCategoryId')"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">默认供应商</label>
                        <select wire:model="formSupplierId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="0">请选择供应商</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">单位 <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="formUnit" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="如：斤、箱、份" />
                        @error('formUnit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
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
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">状态 <span class="text-red-500">*</span></label>
                    <select wire:model="formStatus" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="1">上架</option>
                        <option value="0">下架</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">商品详情</label>
                    <textarea wire:model="formDescription" rows="3" class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="可选"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="save" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">保存</button>
            </div>
        </div>
    </div>
    @endif


    @include('partials.column-modal')
    @include('partials.export-modal')
    @include('partials.import-modal')
    @include('partials.delete-confirm')
</div>
