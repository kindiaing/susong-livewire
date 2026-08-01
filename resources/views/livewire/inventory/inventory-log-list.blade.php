<div class="p-6">
    {{-- 页面标题 --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">库存日志</h1>
            <p class="text-muted-foreground mt-1">查看库存变动记录（只读）</p>
        </div>
    </div>

    {{-- 搜索栏 --}}
    <div class="flex items-center gap-3 mb-4">
        <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background px-3 text-sm" placeholder="搜索SKU编码/变动原因..." />
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
        <button type="button" wire:click="resetFilters" class="text-sm text-muted-foreground hover:text-foreground transition-colors">重置</button>
    </div>

    {{-- 列表 --}}
    <div class="rounded-lg border bg-card overflow-x-auto">
        <table class="w-full text-sm min-w-[1000px]">
            <thead>
                <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                    <th class="px-4 py-2 text-left w-16">ID</th>
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
                    <td class="px-4 py-2 text-muted-foreground">{{ $item->id }}</td>
                    <td class="px-4 py-2 text-foreground">{{ $item->warehouse?->name ?? '-' }}</td>
                    <td class="px-4 py-2 text-foreground">
                        {{ $item->sku?->sku_code ?? '-' }}
                        <span class="text-muted-foreground text-xs ml-1">{{ $item->sku?->product?->name ?? '' }}</span>
                    </td>
                    <td class="px-4 py-2">
                        @php($typeLabel = \App\Models\InventoryLog::typeMap()[$item->type] ?? '未知')
                        @if(in_array($item->type, [1, 5, 6]))
                            <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-green-100 text-green-700">{{ $typeLabel }}</span>
                        @else
                            <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-red-100 text-red-700">{{ $typeLabel }}</span>
                        @endif
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
</div>
