<div class="">
    {{-- 页面标题 --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">库存日志</h1>
            <p class="text-muted-foreground mt-1">查看库存变动记录（只读）</p>
        </div>
    </div>

    {{-- 搜索栏 --}}
    <div class="flex items-center gap-3 mb-4">
        <div x-data class="relative">
            <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background pl-3 pr-8 text-sm" placeholder="搜索SKU编码/变动原因..." />
            @if($search)
                <button type="button" wire:click="resetFilters" class="absolute right-2 top-1/2 -translate-y-1/2 p-0.5 rounded-sm text-muted-foreground/50 hover:text-muted-foreground hover:bg-muted transition-colors">
                    <x-ui.icon name="x-mark" class="w-3.5 h-3.5" />
                </button>
            @endif
        </div>
        <select wire:model.live="filterType" class="flex h-9 w-32 rounded-md border border-input bg-background px-3 text-sm">
            <option value="-1">全部类型</option>
            <option value="1">入库</option>
            <option value="2">出库</option>
            <option value="3">调拨</option>
            <option value="4">报损</option>
            <option value="5">报溢</option>
            <option value="6">调整</option>
        </select>
        <select wire:model.live="filterWarehouseId" class="flex h-9 w-40 rounded-md border border-input bg-background px-3 text-sm">
            <option value="0">全部仓库</option>
            @foreach($warehouses as $w)
                <option value="{{ $w->id }}">{{ $w->name }}</option>
            @endforeach
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

    {{-- 列表 --}}
    <div class="rounded-lg border bg-card overflow-x-auto">
        <table class="w-full text-sm min-w-[1000px]">
            <thead>
                <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                    <th class="px-4 py-2 text-left w-10"><input type="checkbox" wire:model.live="selectAllPage" class="rounded" /></th>
                    <th class="px-4 py-2 text-left">仓库</th>
                    <th class="px-4 py-2 text-left">SKU</th>
                    <th class="px-4 py-2 text-left">类型</th>
                    <th class="px-4 py-2 text-left">变动数量</th>
                    <th class="px-4 py-2 text-left">变动前</th>
                    <th class="px-4 py-2 text-left">变动后</th>
                    <th class="px-4 py-2 text-left">原因</th>
                    <th class="px-4 py-2 text-left">时间</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="invlog-{{ $item->id }}">
                    <td class="px-4 py-2"><input type="checkbox" value="{{ $item->id }}" wire:model.live="selectedIds" class="rounded" /></td>
                    <td class="px-4 py-2 text-foreground">{{ $item->warehouse?->name ?? '-' }}</td>
                    <td class="px-4 py-2 text-foreground">
                        {{ $item->sku?->sku_code ?? '-' }}
                        <span class="text-muted-foreground text-xs ml-1">{{ $item->sku?->product?->name ?? '' }}</span>
                    </td>
                    <td class="px-4 py-2">
                        @php($typeLabel = \App\Models\InventoryLog::typeMap()[$item->type] ?? '未知')
                        {!! status_badge($item->type, 'inventory_log', $typeLabel) !!}
                    </td>
                    <td class="px-4 py-2 {{ $item->quantity >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ $item->quantity >= 0 ? '+' : '' }}{{ $item->quantity }}</td>
                    <td class="px-4 py-2 text-muted-foreground">{{ $item->before_stock }}</td>
                    <td class="px-4 py-2 text-foreground">{{ $item->after_stock }}</td>
                    <td class="px-4 py-2 text-muted-foreground truncate">{{ $item->reason ?: '-' }}</td>
                    <td class="px-4 py-2 text-muted-foreground">{{ $item->created_at ? $item->created_at->format('Y-m-d H:i') : '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="9" class="px-6 py-12 text-center text-muted-foreground">暂无库存变动记录</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $items->links() }}</div>

    @include('partials.column-modal')
    @include('partials.export-modal')
    @include('partials.import-modal')
    @include('partials.delete-confirm')
</div>
