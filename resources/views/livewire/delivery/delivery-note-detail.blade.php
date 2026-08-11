<div class="">
    {{-- 页面标题 + 状态操作 --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('delivery-notes') }}" class="text-muted-foreground hover:text-foreground transition-colors">
                <x-ui.icon name="arrow-left" class="w-5 h-5" />
            </a>
            <div>
                <h1 class="text-2xl font-bold text-foreground">
                    {{ $note->note_no }}
                </h1>
                <p class="text-muted-foreground mt-1">送货单详情</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @php $c = $statusColorMap[$note->status] ?? 'gray'; @endphp
            <span class="inline-flex items-center rounded px-2 py-1 text-sm font-medium bg-{{ $c }}-100 text-{{ $c }}-700">
                {{ $statusMap[$note->status] ?? '-' }}
            </span>
            @if($note->status === 1)
            <button type="button" wire:click="markDelivered" class="rounded-md bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700 transition-colors">确认分货</button>
            @endif
            @if($note->status === 2)
            <button type="button" wire:click="markSigned" class="rounded-md bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700 transition-colors">确认签收</button>
            @endif
            @if(in_array($note->status, [1, 2]))
            <button type="button" wire:click="cancelNote" class="rounded-md border border-orange-300 px-3 py-1.5 text-sm text-orange-600 hover:bg-orange-50 transition-colors">取消</button>
            @endif
        </div>
    </div>

    {{-- 基本信息卡片 --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <div class="rounded-lg border bg-card p-4">
            <h3 class="text-xs font-medium text-muted-foreground uppercase tracking-wider mb-3">单据信息</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-muted-foreground">商户名称</span>
                    <span class="text-foreground font-medium">{{ $note->merchant?->name ?? $note->merchant_name ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">配送地址</span>
                    <span class="text-foreground truncate max-w-[200px]" title="{{ $note->merchant_address ?? '-' }}">{{ $note->merchant_address ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">送达日期</span>
                    <span class="text-foreground">{{ $note->delivery_date?->format('Y-m-d') ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">关联订单</span>
                    <span class="text-foreground">{{ is_array($note->order_nos) ? implode(', ', $note->order_nos) : '-' }}</span>
                </div>
            </div>
        </div>

        <div class="rounded-lg border bg-card p-4">
            <h3 class="text-xs font-medium text-muted-foreground uppercase tracking-wider mb-3">配送信息</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-muted-foreground">配送任务编号</span>
                    <span class="text-foreground font-medium">{{ $note->deliveryTask?->task_no ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">分货时间</span>
                    <span class="text-foreground">{{ $note->delivered_at?->format('Y-m-d H:i:s') ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">确认方式</span>
                    <span class="text-foreground">{{ $note->delivery_method ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">备注</span>
                    <span class="text-foreground">{{ $note->remark ?: '-' }}</span>
                </div>
            </div>
        </div>

        <div class="rounded-lg border bg-card p-4">
            <h3 class="text-xs font-medium text-muted-foreground uppercase tracking-wider mb-3">商品统计</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-muted-foreground">商品项数</span>
                    <span class="text-foreground font-medium">{{ $note->items->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">应送总数</span>
                    <span class="text-foreground">{{ $note->total_quantity }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">已分货数</span>
                    <span class="text-foreground">{{ $note->items->where('status', 2)->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">差异数</span>
                    <span class="text-foreground text-red-600">{{ $note->items->where('status', 3)->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- 送货单明细表 --}}
    <div class="rounded-lg border bg-card">
        <div class="px-6 py-4 border-b">
            <h3 class="text-sm font-semibold text-foreground">送货单明细（{{ $note->items->count() }} 条）</h3>
        </div>
        @if($note->items->count() > 0)
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-muted/20">
                <tr class="text-xs text-muted-foreground uppercase tracking-wider">
                    <th class="px-4 py-2 text-left">SKU名称</th>
                    <th class="px-4 py-2 text-left">单位</th>
                    <th class="px-4 py-2 text-right">应送数量</th>
                    <th class="px-4 py-2 text-right">实际分货数量</th>
                    <th class="px-4 py-2 text-left">来源订单</th>
                    <th class="px-4 py-2 text-center">状态</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($note->items as $item)
                <tr class="hover:bg-muted/30 transition-colors">
                    <td class="px-4 py-2 text-foreground">{{ $item->sku_name ?? $item->sku?->name ?? '-' }}</td>
                    <td class="px-4 py-2 text-muted-foreground">{{ $item->unit ?? '-' }}</td>
                    <td class="px-4 py-2 text-right text-foreground">{{ $item->quantity }}</td>
                    <td class="px-4 py-2 text-right">
                        @if($note->status === 2)
                            <input type="number"
                                wire:model.blur="pickedQty_{{ $item->id }}"
                                value="{{ $item->picked_quantity ?? 0 }}"
                                min="0"
                                max="{{ $item->quantity }}"
                                class="w-20 rounded-md border border-input bg-background px-2 py-1 text-sm text-right focus:outline-none focus:ring-2 focus:ring-ring"
                                wire:change="confirmItemDelivery({{ $item->id }}, $event.target.value)"
                            />
                        @else
                            <span class="text-foreground">{{ $item->picked_quantity ?? '-' }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-muted-foreground font-mono text-xs">{{ $item->order_no ?? '-' }}</td>
                    <td class="px-4 py-2 text-center">
                        @php $ic = $itemStatusColorMap[$item->status] ?? 'gray'; @endphp
                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-{{ $ic }}-100 text-{{ $ic }}-700">{{ $itemStatusMap[$item->status] ?? '-' }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        @else
        <div class="px-6 py-8 text-center text-sm text-muted-foreground">暂无送货单明细数据</div>
        @endif
    </div>
</div>