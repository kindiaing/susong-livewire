<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">常购清单</h1>
            <p class="text-muted-foreground mt-1">商家常购商品排行</p>
        </div>
    </div>
    <div class="flex items-center gap-3 mb-4">
        <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background px-3 text-sm" placeholder="搜索商家/SKU..." />
        <button wire:click="resetFilters" class="text-sm text-muted-foreground hover:text-foreground transition-colors">重置</button>
        <div class="flex-1"></div>
        @if($selectedCount > 0)
        <span class="text-sm text-muted-foreground">已选 {{ $selectedCount }} 项</span>
        <button wire:click="batchDelete" class="rounded-md bg-red-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-red-700 transition-colors">批量删除</button>
        <button wire:click="clearSelection" class="text-xs text-muted-foreground hover:text-foreground">取消选择</button>
        @endif
        <button wire:click="openColumnModal" class="rounded-md border border-input px-3 py-1.5 text-xs font-medium text-foreground hover:bg-accent transition-colors">列配置</button>
        <button wire:click="openExportModal" class="rounded-md border border-input px-3 py-1.5 text-xs font-medium text-foreground hover:bg-accent transition-colors">导出</button>
        <button wire:click="openImportModal" class="rounded-md border border-input px-3 py-1.5 text-xs font-medium text-foreground hover:bg-accent transition-colors">导入</button>
    </div>
    <div class="rounded-lg border bg-card">
        <div class="grid grid-cols-[40px_60px_1fr_1fr_100px_100px] gap-2 border-b px-6 py-3 text-xs font-medium text-muted-foreground uppercase tracking-wider">
            <div><input type="checkbox" wire:model.live="selectAllPage" class="h-4 w-4 rounded border-input text-blue-600 focus:ring-blue-500" /></div>
            <div>ID</div><div>商家</div><div>商品</div><div>购买次数</div><div>创建时间</div>
        </div>
        @forelse($items as $item)
            <div class="grid grid-cols-[40px_60px_1fr_1fr_100px_100px] gap-2 border-b last:border-b-0 px-6 py-3 items-center hover:bg-muted/30 transition-colors" wire:key="fb-{{ $item->id }}">
                <div><input type="checkbox" value="{{ $item->id }}" wire:model.live="selectedIds" class="h-4 w-4 rounded border-input text-blue-600 focus:ring-blue-500" /></div>
                <div class="text-sm text-muted-foreground">{{ $item->id }}</div>
                <div class="text-sm text-foreground">{{ $item->merchant?->name ?? '-' }}</div>
                <div class="text-sm text-foreground truncate">{{ $item->sku?->product?->name ?? '-' }}</div>
                <div class="text-sm font-medium text-foreground">{{ $item->buy_count }}</div>
                <div class="text-sm text-muted-foreground">{{ $item->created_at?->format('Y-m-d H:i') }}</div>
            </div>
        @empty
            <div class="px-6 py-12 text-center text-sm text-muted-foreground">暂无常购数据</div>
        @endforelse
    </div>
    <div class="mt-4">{{ $items->links() }}</div>
    @include('partials.column-modal')
    @include('partials.export-modal')
    @include('partials.import-modal')
</div>
