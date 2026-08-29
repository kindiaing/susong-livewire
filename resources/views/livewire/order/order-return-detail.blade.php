<div class="" x-data="{ activeTab: 'items' }">
    {{-- 顶部：返回 + 单号 + 状态 + 操作按钮 --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('order-returns') }}" class="text-muted-foreground hover:text-foreground transition-colors" title="返回列表">
                <x-ui.icon name="arrow-left" class="w-5 h-5" />
            </a>
            <h1 class="text-xl font-bold text-foreground">{{ $return->return_no }}</h1>
            {!! status_badge($return->status, 'order_return') !!}
        </div>
        <div class="flex items-center gap-2">
            @if($return->status === 1)
                <button type="button" wire:click="confirmApprove" class="rounded-md bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700 transition-colors">审核</button>
            @elseif($return->status === 2)
                <button type="button" wire:click="confirmReturn" class="rounded-md bg-orange-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-orange-700 transition-colors">确认退货</button>
            @elseif($return->status === 3)
                <button type="button" wire:click="confirmRefund" class="rounded-md bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700 transition-colors">完成退款</button>
            @endif

            @if(in_array($return->status, [1, 2]))
                <button type="button" wire:click="confirmCancel" class="rounded-md border border-red-300 px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">作废</button>
            @endif

            {{-- 超管状态回退 --}}
            @if($isSuperAdmin && !in_array($return->status, [1, 9]))
                <div class="relative" x-data="{open: false}">
                    <button type="button" @click="open = !open" class="rounded-md border border-purple-300 px-3 py-1.5 text-sm font-medium text-purple-600 hover:bg-purple-50 transition-colors">回退</button>
                    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-1 w-36 rounded-md border bg-background shadow-lg z-10 py-1">
                        @if($return->status > 1)<button type="button" wire:click="confirmRollback(1)" class="block w-full text-left px-3 py-1.5 text-sm hover:bg-accent transition-colors" @click="open = false">回退到待审核</button>@endif
                        @if($return->status > 2)<button type="button" wire:click="confirmRollback(2)" class="block w-full text-left px-3 py-1.5 text-sm hover:bg-accent transition-colors" @click="open = false">回退到已审核</button>@endif
                        @if($return->status > 3)<button type="button" wire:click="confirmRollback(3)" class="block w-full text-left px-3 py-1.5 text-sm hover:bg-accent transition-colors" @click="open = false">回退到已退货</button>@endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- 单据摘要信息 --}}
    <div class="flex flex-wrap items-center gap-x-6 gap-y-1 text-sm text-muted-foreground mb-4">
        <span>订单：<b class="text-foreground"><a href="{{ route('orders.detail', $return->order_id) }}" class="text-blue-600 hover:text-blue-700">{{ $return->order?->order_no ?? '-' }}</a></b></span>
        <span>商家：<b class="text-foreground">{{ $return->merchant?->name ?? '-' }}</b></span>
        @if($return->operator)<span>经办人：<b class="text-foreground">{{ $return->operator->name }}</b></span>@endif
        <span>退货金额：<b class="text-foreground">{{ money_format($return->total_amount) }}</b></span>
        <span>退款金额：<b class="text-foreground">{{ money_format($return->refund_amount) }}</b></span>
        @if($return->reason)<span>原因：<b class="text-foreground">{{ $return->reason }}</b></span>@endif
        @if($return->remark)<span>备注：<b class="text-foreground">{{ $return->remark }}</b></span>@endif
    </div>

    {{-- Tab 切换 --}}
    <div class="border-b mb-4">
        <nav class="flex gap-6">
            <button type="button" @click="activeTab = 'items'" :class="activeTab === 'items' ? 'border-blue-600 text-foreground' : 'border-transparent text-muted-foreground hover:text-foreground'" class="pb-2 text-sm font-medium border-b-2 transition-colors">退货明细</button>
            <button type="button" @click="activeTab = 'logs'" :class="activeTab === 'logs' ? 'border-blue-600 text-foreground' : 'border-transparent text-muted-foreground hover:text-foreground'" class="pb-2 text-sm font-medium border-b-2 transition-colors">
                状态变更记录
                @if($return->auditLogs->isNotEmpty())
                    <span class="ml-1.5 inline-flex items-center justify-center rounded-full bg-muted text-muted-foreground text-[10px] font-medium px-1.5 py-0.5 leading-none">{{ $return->auditLogs->count() }}</span>
                @endif
            </button>
        </nav>
    </div>

    {{-- Tab 内容：退货明细 --}}
    <div x-show="activeTab === 'items'" x-transition>
    <div class="rounded-lg border bg-card">
        <div class="px-4 py-2 border-b flex items-center justify-between">
            <h2 class="text-sm font-semibold text-foreground">退货明细</h2>
            @if($return->status === 1)
            <div class="flex items-center gap-2">
                <button type="button" wire:click="addItemRow" class="rounded-md border border-input px-2 py-1 text-xs hover:bg-accent transition-colors">+ 添加行</button>
                @if(count($formItems) > 0)
                <button type="button" wire:click="exportItems" class="rounded-md border border-input px-2 py-1 text-xs hover:bg-accent transition-colors">导出明细</button>
                @endif
                <button type="button" wire:click="saveItems" class="rounded-md bg-blue-600 px-3 py-1 text-xs font-medium text-white hover:bg-blue-700 transition-colors">保存明细</button>
            </div>
            @endif
        </div>

        @if($return->status === 1)
        {{-- 待审核：可编辑明细 --}}
        @if(count($formItems) > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[720px]">
                <thead>
                    <tr class="border-b text-[11px] font-medium text-muted-foreground bg-muted/30">
                        <th class="px-3 py-1.5 text-left">订单明细</th>
                        <th class="px-3 py-1.5 text-right w-[80px]">可退数量</th>
                        <th class="px-3 py-1.5 text-right w-[110px]">退货数量</th>
                        <th class="px-3 py-1.5 text-right w-[90px]">单价（元）</th>
                        <th class="px-3 py-1.5 text-left w-[160px]">原因</th>
                        <th class="px-3 py-1.5 text-center w-[50px]"></th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($formItems as $i => $row)
                    <tr wire:key="order-return-item-{{ $i }}">
                        <td class="px-3 py-1.5">
                            <select wire:change="pickOrderItem({{ $i }}, $event.target.value)" class="w-full h-8 rounded border border-input bg-background px-1.5 text-xs">
                                <option value="0">选择订单明细</option>
                                @foreach($orderItems as $oi)
                                <option value="{{ $oi->id }}" @selected((int) ($row['order_item_id'] ?? 0) === $oi->id)>
                                    {{ $oi->sku?->product?->name ?? $oi->product_name ?? 'SKU' }}（{{ $oi->sku?->sku_code }}）实收 {{ $oi->actual_quantity ?: $oi->quantity }}
                                </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-3 py-1.5 text-right text-muted-foreground tabular-nums">{{ $row['max_quantity'] ?: '-' }}</td>
                        <td class="px-3 py-1.5">
                            <input type="number" wire:model="formItems.{{ $i }}.quantity" min="0" max="{{ $row['max_quantity'] }}" class="w-full h-8 rounded border border-input bg-background px-1.5 text-xs text-right" />
                        </td>
                        <td class="px-3 py-1.5 text-right text-muted-foreground tabular-nums">{{ number_format(($row['price'] ?? 0) / 1000, 2) }}</td>
                        <td class="px-3 py-1.5">
                            <input type="text" wire:model="formItems.{{ $i }}.reason" placeholder="可选" class="w-full h-8 rounded border border-input bg-background px-1.5 text-xs" />
                        </td>
                        <td class="px-3 py-1.5 text-center">
                            <button type="button" wire:click="removeItemRow({{ $i }})" class="text-red-500 hover:text-red-700" title="删除行"><x-ui.icon name="trash" class="w-3.5 h-3.5" /></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="px-4 py-2 text-xs text-muted-foreground border-t">退货数量不能超过客户实收数量；数量填 0 或删除行表示该明细不退货。编辑后请点击「保存明细」生效。</p>
        @else
        <div class="px-4 py-8 text-center text-sm text-muted-foreground">
            暂无明细。点击右上角「+ 添加行」录入退货商品，保存明细后方可审核。
        </div>
        @endif
        @else
        {{-- 非待审核：只读明细 --}}
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-[11px] font-medium text-muted-foreground bg-muted/30">
                    <th class="px-3 py-1.5 text-left">SKU</th>
                    <th class="px-3 py-1.5 text-right w-[70px]">退货数量</th>
                    <th class="px-3 py-1.5 text-right w-[80px]">退货单价</th>
                    <th class="px-3 py-1.5 text-right w-[80px]">退货金额</th>
                    <th class="px-3 py-1.5 text-right w-[80px]">退款金额</th>
                    <th class="px-3 py-1.5 text-left w-[120px]">原因</th>
                </tr>
            </thead>
            <tbody>
                @forelse($return->items as $item)
                <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="ori-{{ $item->id }}">
                    <td class="px-3 py-1.5 truncate">
                        <span class="font-mono text-foreground">{{ $item->sku?->sku_code }}</span>
                        <span class="text-muted-foreground ml-1">{{ $item->sku?->product?->name }}</span>
                    </td>
                    <td class="px-3 py-1.5 text-right tabular-nums">{!! format_quantity($item->sku_id, $item->quantity, $item->sku?->baseUnit?->name) !!}</td>
                    <td class="px-3 py-1.5 text-right tabular-nums">{{ money_format($item->price) }}</td>
                    <td class="px-3 py-1.5 text-right tabular-nums font-medium">{{ money_format($item->amount) }}</td>
                    <td class="px-3 py-1.5 text-right tabular-nums">{{ $item->refund_amount ? money_format($item->refund_amount) : '-' }}</td>
                    <td class="px-3 py-1.5 text-muted-foreground truncate">{{ $item->reason ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-3 py-6 text-center text-muted-foreground">暂无明细</td></tr>
                @endforelse
            </tbody>
        </table>
        @endif
    </div>

    </div>
    </div>

    {{-- Tab 内容：状态变更记录 --}}
    <div x-show="activeTab === 'logs'" x-transition>
        @if($return->auditLogs->isNotEmpty())
        <div class="rounded-lg border bg-card">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b text-[11px] font-medium text-muted-foreground bg-muted/30">
                        <th class="px-4 py-2 text-left w-[180px]">时间</th>
                        <th class="px-4 py-2 text-left w-[120px]">操作</th>
                        <th class="px-4 py-2 text-left">状态变更</th>
                        <th class="px-4 py-2 text-left w-[120px]">操作人</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($return->auditLogs->sortByDesc('created_at') as $log)
                    <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors">
                        <td class="px-4 py-2 text-muted-foreground tabular-nums">{{ $log->created_at?->format('Y-m-d H:i:s') }}</td>
                        <td class="px-4 py-2">
                            <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium
                                @if($log->action === 'cancel') bg-red-100 text-red-700
                                @elseif($log->action === 'rollback') bg-purple-100 text-purple-700
                                @elseif($log->action === 'status_change') bg-blue-100 text-blue-700
                                @elseif($log->action === 'create') bg-green-100 text-green-700
                                @else bg-muted text-muted-foreground
                                @endif
                            ">{{ $log->action_label }}</span>
                        </td>
                        <td class="px-4 py-2">
                            @php
                                $before = $log->before_data ?? [];
                                $after = $log->after_data ?? [];
                                $beforeStatus = $before['status'] ?? null;
                                $afterStatus = $after['status'] ?? null;
                            @endphp
                            @if($beforeStatus && $afterStatus)
                                {!! status_badge($beforeStatus, 'order_return') !!}
                                <x-ui.icon name="arrow-right" class="w-3.5 h-3.5 inline text-muted-foreground mx-1" />
                                {!! status_badge($afterStatus, 'order_return') !!}
                            @else
                                <span class="text-muted-foreground">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-foreground">{{ $log->operator?->name ?? '系统' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="rounded-lg border bg-card px-6 py-12 text-center text-sm text-muted-foreground">暂无状态变更记录</div>
        @endif
    </div>

    {{-- 状态确认弹窗 --}}
    @if($showConfirmModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-sm mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">确认操作</h2>
                <button type="button" wire:click="closeConfirmModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <p class="text-sm text-muted-foreground mb-6">{{ $confirmTitle }}</p>
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="closeConfirmModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="executeConfirm" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">确认</button>
            </div>
        </div>
    </div>
    @endif
</div>
