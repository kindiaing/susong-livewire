<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">购物车</h1>
            <p class="text-muted-foreground mt-1">查看各商家购物车数据</p>
        </div>
    </div>
    <div class="flex items-center gap-3 mb-4">
        <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background px-3 text-sm" placeholder="搜索商家名称..." />
        <button type="button" wire:click="resetFilters" class="text-sm text-muted-foreground hover:text-foreground transition-colors">重置</button>
    </div>
    <div class="rounded-lg border bg-card">
        <div class="grid grid-cols-[60px_1fr_1fr_80px_80px_100px_80px] gap-3 border-b px-6 py-3 text-xs font-medium text-muted-foreground uppercase tracking-wider">
            <div>ID</div><div>商家</div><div>商品</div><div>SKU</div><div>数量</div><div>单价（元）</div><div>操作</div>
        </div>
        @forelse($cartItems as $item)
            <div class="grid grid-cols-[60px_1fr_1fr_80px_80px_100px_80px] gap-3 border-b last:border-b-0 px-6 py-3 items-center hover:bg-muted/30 transition-colors" wire:key="cart-{{ $item->id }}">
                <div class="text-sm text-muted-foreground">{{ $item->id }}</div>
                <div class="text-sm text-foreground">{{ $item->cart?->merchant?->name ?? '-' }}</div>
                <div class="text-sm text-foreground truncate">{{ $item->sku?->product?->name ?? '-' }}</div>
                <div class="text-sm font-mono text-foreground">{{ $item->sku?->sku_code ?? '-' }}</div>
                <div class="text-sm text-foreground">{{ $item->quantity }}</div>
                <div class="text-sm text-foreground">{{ $item->price }}</div>
                <div>@can('order.cart.delete')<button type="button" wire:click="confirmDelete({{ $item->id }})" class="text-red-600 hover:text-red-700 text-sm">删除</button>@endcan</div>
            </div>
        @empty
            <div class="px-6 py-12 text-center text-sm text-muted-foreground">暂无购物车数据</div>
        @endforelse
    </div>
    <div class="mt-4">{{ $cartItems->links() }}</div>
    @if($showDeleteConfirm)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" wire:click="closeDeleteConfirm"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-sm mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-2">确认删除</h2>
            <p class="text-sm text-muted-foreground mb-6">确定要删除该购物车项吗？</p>
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="closeDeleteConfirm" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="delete" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">删除</button>
            </div>
        </div>
    </div>
    @endif
</div>
