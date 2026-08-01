<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">可见性配置</h1>
            <p class="text-muted-foreground mt-1">管理商家可见SKU配置</p>
        </div>
        @can('product.visibility.create')
        <button type="button" wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            新增配置
        </button>
        @endcan
    </div>

    {{-- 筛选区 --}}
    <div class="flex items-center gap-3 mb-4">
        <div x-data class="relative">
            <input type="text" wire:model.live="searchMerchant" class="flex h-9 w-56 rounded-md border border-input bg-background pl-3 pr-8 text-sm" placeholder="搜索商家名称..." />
            @if($searchMerchant)
                <button type="button" wire:click="resetFilters" class="absolute right-2 top-1/2 -translate-y-1/2 p-0.5 rounded-sm text-muted-foreground/50 hover:text-muted-foreground hover:bg-muted transition-colors">
                    <x-ui.icon name="x-mark" class="w-3.5 h-3.5" />
                </button>
            @endif
        </div>
        <div x-data class="relative">
            <input type="text" wire:model.live="searchSku" class="flex h-9 w-56 rounded-md border border-input bg-background pl-3 pr-8 text-sm" placeholder="搜索SKU名称/编码..." />
            @if($searchSku)
                <button type="button" wire:click="resetFilters" class="absolute right-2 top-1/2 -translate-y-1/2 p-0.5 rounded-sm text-muted-foreground/50 hover:text-muted-foreground hover:bg-muted transition-colors">
                    <x-ui.icon name="x-mark" class="w-3.5 h-3.5" />
                </button>
            @endif
        </div>
        <select wire:model.live="filterMerchantId" class="flex h-9 w-56 rounded-md border border-input bg-background px-3 text-sm">
            <option value="">全部商家</option>
            @foreach($merchants as $merchant)
                <option value="{{ $merchant->id }}">{{ $merchant->name }}</option>
            @endforeach
        </select>
        <div class="flex-1"></div>
        <button type="button" wire:click="openColumnModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors">列配置</button>
        <button type="button" wire:click="openExportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors">导出</button>
        @if($selectedCount > 0)
            <span class="text-sm text-muted-foreground">已选 {{ $selectedCount }} 项</span>
            @can('product.visibility.delete')
            <button type="button" wire:click="batchDelete" class="inline-flex items-center gap-1 rounded-md bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700 transition-colors">批量删除</button>
            @endcan
            <button type="button" wire:click="clearSelection" class="text-sm text-muted-foreground hover:text-foreground transition-colors">取消选择</button>
        @endif
    </div>

    {{-- 列表 --}}
    <div class="rounded-lg border bg-card">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                    <th class="px-4 py-2 text-left w-10"><input type="checkbox" wire:model.live="selectAll" class="rounded" /></th>
                    <th class="px-4 py-2 text-left w-16">ID</th>
                    <th class="px-4 py-2 text-left">商家</th>
                    <th class="px-4 py-2 text-left">SKU名称</th>
                    <th class="px-4 py-2 text-left w-32">SKU编码</th>
                    <th class="px-4 py-2 text-left w-24">是否可见</th>
                    <th class="px-4 py-2 text-left w-24">操作</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $record)
                <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="visibility-{{ $record->id }}">
                    <td class="px-4 py-2"><input type="checkbox" value="{{ $record->id }}" wire:model.live="selectedIds" class="rounded" /></td>
                    <td class="px-4 py-2 text-muted-foreground">{{ $record->id }}</td>
                    <td class="px-4 py-2 font-medium text-foreground">{{ $record->merchant?->name ?? '-' }}</td>
                    <td class="px-4 py-2 text-foreground">{{ $record->sku?->name ?? '-' }}</td>
                    <td class="px-4 py-2 text-muted-foreground">{{ $record->sku?->sku_code ?? '-' }}</td>
                    <td class="px-4 py-2">
                        <button type="button" wire:click="toggleVisibility({{ $record->id }})" class="inline-flex items-center gap-1.5 cursor-pointer">
                            @if($record->is_visible)
                                <span class="w-4 h-4 rounded-full bg-green-500 inline-block"></span>
                                <span class="text-xs text-green-700">可见</span>
                            @else
                                <span class="w-4 h-4 rounded-full bg-gray-300 inline-block"></span>
                                <span class="text-xs text-gray-500">不可见</span>
                            @endif
                        </button>
                    </td>
                    <td class="px-4 py-2">
                        @can('product.visibility.delete')
                        <button type="button" wire:click="confirmDelete({{ $record->id }})" class="text-red-600 hover:text-red-700 text-sm">删除</button>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-muted-foreground">暂无可见性配置数据</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $records->links() }}</div>

    {{-- 新增/编辑弹窗 --}}
    @if($showCreateModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" wire:click="closeCreateModal"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-4">新增可见性配置</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">商家 <span class="text-red-500">*</span></label>
                    <select wire:model="formMerchantId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="0">请选择商家</option>
                        @foreach($merchants as $merchant)
                            <option value="{{ $merchant->id }}">{{ $merchant->name }}</option>
                        @endforeach
                    </select>
                    @error('formMerchantId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <x-ui.searchable-select label="SKU *" wire-model="formSkuId" :options="$skuOptions" placeholder="搜索SKU..." wireModel="formSkuId" />
                    @error('formSkuId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">可见性</label>
                    <select wire:model="formIsVisible" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="1">可见</option>
                        <option value="0">不可见</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeCreateModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="save" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">保存</button>
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
            <p class="text-sm text-muted-foreground mb-6">确定要删除该可见性配置吗？</p>
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="closeDeleteConfirm" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="delete" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">删除</button>
            </div>
        </div>
    </div>
    @endif

    @include('partials.column-modal')
    @include('partials.export-modal')
    @include('partials.delete-confirm')
</div>
