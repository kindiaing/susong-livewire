<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">待采清单</h1>
            <p class="text-muted-foreground mt-1">自动汇总待采商品，一键生成采购单</p>
        </div>
        @can('purchase.restock-reminder.create')
        <button type="button" wire:click="openCreateModal" type="button" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">新增待采项</button>
        @endcan
        <button type="button" wire:click="confirmGenerateOrders" type="button" class="inline-flex items-center gap-2 rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition-colors">批量生成采购单</button>
    </div>
    <div class="flex items-center gap-3 mb-4">
        <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background px-3 text-sm" placeholder="搜索SKU编码..." />
        <select wire:model.live="filterStatus" class="flex h-9 rounded-md border border-input bg-background px-3 text-sm">
            <option value="-1">全部状态</option>
            <option value="1">待生成</option>
            <option value="2">已生成</option>
        </select>
        <button type="button" wire:click="resetFilters" class="text-sm text-muted-foreground hover:text-foreground transition-colors">重置</button>
    </div>
    <div class="rounded-lg border bg-card">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                    <th class="px-4 py-2 text-left w-16">ID</th>
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
                    <td class="px-4 py-2 text-muted-foreground">{{ $item->id }}</td>
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
                        <label class="block text-sm font-medium text-foreground mb-1">SKU ID <span class="text-red-500">*</span></label>
                        <input type="number" wire:model="formSkuId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" min="1" />
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
                <button type="button" wire:click="closeModal" type="button" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="save" type="button" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">保存</button>
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
                <button type="button" wire:click="closeGenerateConfirm" type="button" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="generateOrders" type="button" class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition-colors">确认生成</button>
            </div>
        </div>
    </div>
    @endif
    @if($showDeleteConfirm)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" wire:click="closeDeleteConfirm"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-sm mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-2">确认删除</h2>
            <p class="text-sm text-muted-foreground mb-6">确定要删除该待采项吗？</p>
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="closeDeleteConfirm" type="button" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="delete" type="button" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">删除</button>
            </div>
        </div>
    </div>
    @endif
</div>
