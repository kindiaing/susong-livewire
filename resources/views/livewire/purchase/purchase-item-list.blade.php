<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">待采清单</h1>
            <p class="text-muted-foreground mt-1">自动汇总待采商品，一键生成采购单</p>
        </div>
        @can('purchase.restock-reminder.create')
        <button type="button" wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">新增待采项</button>
        @endcan
        <button type="button" wire:click="confirmGenerateOrders" class="inline-flex items-center gap-2 rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition-colors">批量生成采购单</button>
    </div>
    <div class="flex items-center gap-3 mb-4">
        <div x-data class="relative">
            <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background pl-3 pr-8 text-sm" placeholder="搜索SKU编码..." />
            @if($search)
                <button type="button" wire:click="resetFilters" class="absolute right-2 top-1/2 -translate-y-1/2 p-0.5 rounded-sm text-muted-foreground/50 hover:text-muted-foreground hover:bg-muted transition-colors">
                    <x-ui.icon name="x-mark" class="w-3.5 h-3.5" />
                </button>
            @endif
        </div>
        <select wire:model.live="filterStatus" class="flex h-9 rounded-md border border-input bg-background px-3 text-sm">
            <option value="-1">全部状态</option>
            <option value="1">待生成</option>
            <option value="2">已生成</option>
        </select>
        <div class="flex-1"></div>
        <button type="button" wire:click="openColumnModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors">列配置</button>
        <button type="button" wire:click="openImportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors">导入</button>
        <button type="button" wire:click="openExportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors">导出</button>
            @if($selectedCount > 0)
                <span class="text-sm text-muted-foreground">已选 {{ $selectedCount }} 项</span>
                <button type="button" wire:click="batchDelete" class="inline-flex items-center gap-1 rounded-md bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700 transition-colors">批量删除</button>
                <button type="button" wire:click="clearSelection" class="text-sm text-muted-foreground hover:text-foreground transition-colors">取消选择</button>
            @endif
    </div>
    <div class="rounded-lg border bg-card">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                    <th class="px-4 py-2 text-left w-10"><input type="checkbox" wire:model.live="selectAllPage" class="rounded" /></th>
                    <th class="px-4 py-2 text-left">SKU编码</th>
                    <th class="px-4 py-2 text-left">商品名称</th>
                    <th class="px-4 py-2 text-left">待采数量</th>
                    <th class="px-4 py-2 text-left">来源</th>
                    <th class="px-4 py-2 text-left">状态</th>
                    <th class="px-4 py-2 text-left w-24">操作</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="pi-{{ $item->id }}">
                    <td class="px-4 py-2"><input type="checkbox" value="{{ $item->id }}" wire:model.live="selectedIds" class="rounded" /></td>
                    <td class="px-4 py-2 font-mono text-foreground">{{ $item->sku?->sku_code ?? '-' }}</td>
                    <td class="px-4 py-2 text-foreground truncate">{{ $item->sku?->product?->name ?? '-' }}</td>
                    <td class="px-4 py-2 text-foreground">{{ $item->quantity }}</td>
                    <td class="px-4 py-2 text-foreground">{{ \App\Models\PurchaseItem::sourceTypeMap()[$item->source_type] ?? '-' }}</td>
                    <td class="px-4 py-2">
                        @if($item->status === 1)<span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-yellow-100 text-yellow-700">待生成</span>
                        @else<span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-green-100 text-green-700">已生成</span>@endif
                    </td>
                    <td class="px-4 py-2">
                        <div class="flex items-center gap-2">
                            @can('purchase.restock-reminder.edit')
                            <button type="button" wire:click="openEditModal({{ $item->id }})" class="text-blue-600 hover:text-blue-700 text-sm">编辑</button>
                            @endcan
                            @can('purchase.restock-reminder.delete')
                            <button type="button" wire:click="confirmDelete({{ $item->id }})" class="text-red-600 hover:text-red-700 text-sm">删除</button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-12 text-center text-muted-foreground">暂无待采数据</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $items->links() }}</div>
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" wire:click="closeModal"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-4">{{ $editingId ? '编辑待采项' : '新增待采项' }}</h2>
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-ui.searchable-select label="SKU *" wire-model="formSkuId" :options="$skuOptions" placeholder="搜索SKU..." wireModel="formSkuId" />
                        @error('formSkuId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">数量 <span class="text-red-500">*</span></label>
                        <input type="number" wire:model="formQuantity" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" min="1" />
                        @error('formQuantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">来源</label>
                    <select wire:model="formSourceType" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="1">订单汇总</option>
                        <option value="2">手工添加</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="save" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">保存</button>
            </div>
        </div>
    </div>
    @endif
    @if($showGenerateConfirm)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" wire:click="closeGenerateConfirm"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-sm mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-2">确认生成采购单</h2>
            <p class="text-sm text-muted-foreground mb-6">将按供应商自动分组，每个供应商生成一个采购单。已勾选的待采项将标记为已生成。</p>
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="closeGenerateConfirm" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="generateOrders" class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition-colors">确认生成</button>
            </div>
        </div>
    </div>
    @endif
    @include('partials.column-modal')
    @include('partials.export-modal')
    @include('partials.import-modal')
    @include('partials.delete-confirm')
</div>
        </div>
    </div>
    @endif
</div>
