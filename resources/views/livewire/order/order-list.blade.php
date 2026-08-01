<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">客户订单</h1>
            <p class="text-muted-foreground mt-1">管理客户订单及支付状态</p>
        </div>
    </div>
    <div class="flex items-center gap-3 mb-4">
        <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background px-3 text-sm" placeholder="搜索订单号/商家..." />
        <select wire:model.live="filterStatus" class="flex h-9 rounded-md border border-input bg-background px-3 text-sm">
            <option value="-1">全部状态</option>
            <option value="1">待拣货</option><option value="2">拣货中</option><option value="3">配送中</option>
            <option value="4">已签收</option><option value="5">已锁定</option><option value="9">已取消</option>
        </select>
        <select wire:model.live="filterPaymentStatus" class="flex h-9 rounded-md border border-input bg-background px-3 text-sm">
            <option value="-1">全部支付</option>
            <option value="1">未支付</option><option value="2">已支付</option><option value="3">账期</option>
        </select>
        <button type="button" wire:click="resetFilters" class="text-sm text-muted-foreground hover:text-foreground transition-colors">重置</button>
    </div>
    <div class="rounded-lg border bg-card">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                    <th class="px-4 py-2 text-left w-16">ID</th>
                    <th class="px-4 py-2 text-left">订单号</th>
                    <th class="px-4 py-2 text-left">商家</th>
                    <th class="px-4 py-2 text-left">总金额</th>
                    <th class="px-4 py-2 text-left">批次</th>
                    <th class="px-4 py-2 text-left">最终金额</th>
                    <th class="px-4 py-2 text-left">状态</th>
                    <th class="px-4 py-2 text-left">支付</th>
                    <th class="px-4 py-2 text-left w-24">操作</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="order-{{ $order->id }}">
                    <td class="px-4 py-2 text-muted-foreground">{{ $order->id }}</td>
                    <td class="px-4 py-2 font-mono text-foreground">{{ $order->order_no }}</td>
                    <td class="px-4 py-2 text-foreground truncate">{{ $order->merchant?->name ?? '-' }}</td>
                    <td class="px-4 py-2 text-foreground">{{ $order->total_amount }}</td>
                    <td class="px-4 py-2 text-foreground">{{ $order->batch === 1 ? '上午' : '下午' }}</td>
                    <td class="px-4 py-2 text-foreground">{{ $order->final_amount }}</td>
                    <td class="px-4 py-2">
                        @php $sc = ['1'=>'yellow','2'=>'blue','3'=>'orange','4'=>'green','5'=>'gray','9'=>'red']; $c = $sc[$order->status] ?? 'gray'; @endphp
                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-{{ $c }}-100 text-{{ $c }}-700">{{ $order->status_label }}</span>
                    </td>
                    <td class="px-4 py-2">
                        @php $pc = ['1'=>'yellow','2'=>'green','3'=>'blue']; $cp = $pc[$order->payment_status] ?? 'gray'; @endphp
                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-{{ $cp }}-100 text-{{ $cp }}-700">{{ $order->payment_status_label }}</span>
                    </td>
                    <td class="px-4 py-2">
                        @can('order.order.delete')
                        <button type="button" wire:click="confirmDelete({{ $order->id }})" class="text-red-600 hover:text-red-700 text-sm">删除</button>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="px-6 py-12 text-center text-muted-foreground">暂无订单数据</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $orders->links() }}</div>
    @if($showDeleteConfirm)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" wire:click="closeDeleteConfirm"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-sm mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-2">确认删除</h2>
            <p class="text-sm text-muted-foreground mb-6">确定要删除该订单吗？</p>
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="closeDeleteConfirm" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="delete" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">删除</button>
            </div>
        </div>
    </div>
    @endif
</div>
