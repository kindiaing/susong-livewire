<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">购物车</h1>
            <p class="text-muted-foreground mt-1">查看各商家购物车数据</p>
        </div>
    </div>
    <div class="flex items-center gap-3 mb-4">
        <div x-data class="relative">
            <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background pl-3 pr-8 text-sm" placeholder="搜索商家名称..." />
            @if($search)
                <button type="button" wire:click="resetFilters" class="absolute right-2 top-1/2 -translate-y-1/2 p-0.5 rounded-sm text-muted-foreground/50 hover:text-muted-foreground hover:bg-muted transition-colors">
                    <x-ui.icon name="x-mark" class="w-3.5 h-3.5" />
                </button>
            @endif
        </div>
    </div>
    <div class="rounded-lg border bg-card">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                    <th class="px-4 py-2 text-left w-16">ID</th>
                    <th class="px-4 py-2 text-left">商家</th>
                    <th class="px-4 py-2 text-left">商品</th>
                    <th class="px-4 py-2 text-left">SKU</th>
                    <th class="px-4 py-2 text-left">数量</th>
                    <th class="px-4 py-2 text-left">单价（元）</th>
                    <th class="px-4 py-2 text-left w-24">操作</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cartItems as $item)
                <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="cart-{{ $item->id }}">
                    <td class="px-4 py-2 text-muted-foreground">{{ $item->id }}</td>
                    <td class="px-4 py-2 text-foreground">{{ $item->cart?->merchant?->name ?? '-' }}</td>
                    <td class="px-4 py-2 text-foreground truncate">{{ $item->sku?->product?->name ?? '-' }}</td>
                    <td class="px-4 py-2 font-mono text-foreground">{{ $item->sku?->sku_code ?? '-' }}</td>
                    <td class="px-4 py-2 text-foreground">{{ $item->quantity }}</td>
                    <td class="px-4 py-2 text-foreground">{{ $item->price }}</td>
                    <td class="px-4 py-2">@can('order.cart.delete')<button type="button" wire:click="confirmDelete({{ $item->id }})" class="text-red-600 hover:text-red-700 text-sm">删除</button>@endcan</td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-12 text-center text-muted-foreground">暂无购物车数据</td></tr>
                @endforelse
            </tbody>
        </table>
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
