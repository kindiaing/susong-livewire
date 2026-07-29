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
    </div>
    <div class="rounded-lg border bg-card">
        <div class="grid grid-cols-[60px_1fr_1fr_100px_100px] gap-3 border-b px-6 py-3 text-xs font-medium text-muted-foreground uppercase tracking-wider">
            <div>ID</div><div>商家</div><div>商品</div><div>购买次数</div><div>最近购买</div>
        </div>
        @forelse($items as $item)
            <div class="grid grid-cols-[60px_1fr_1fr_100px_100px] gap-3 border-b last:border-b-0 px-6 py-3 items-center hover:bg-muted/30 transition-colors" wire:key="fb-{{ $item->id }}">
                <div class="text-sm text-muted-foreground">{{ $item->id }}</div>
                <div class="text-sm text-foreground">{{ $item->merchant?->name ?? '-' }}</div>
                <div class="text-sm text-foreground truncate">{{ $item->sku?->product?->name ?? '-' }}</div>
                <div class="text-sm font-medium text-foreground">{{ $item->buy_count }}</div>
                <div class="text-sm text-muted-foreground">{{ $item->last_buy_at?->format('m-d H:i') ?? '-' }}</div>
            </div>
        @empty
            <div class="px-6 py-12 text-center text-sm text-muted-foreground">暂无常购数据</div>
        @endforelse
    </div>
    <div class="mt-4">{{ $items->links() }}</div>
</div>
