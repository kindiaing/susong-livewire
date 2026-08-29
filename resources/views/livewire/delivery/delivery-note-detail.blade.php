<div class="" x-data="{ activeTab: 'items' }">
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
            @can('delivery.delivery-note.deliver')
            @if($note->status === 1)
            <button type="button" wire:click="markDelivered" class="rounded-md bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700 transition-colors">确认分货</button>
            @endif
            @endcan
            @can('delivery.delivery-note.sign')
            @if($note->status === 2)
            <button type="button" wire:click="markSigned" class="rounded-md bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700 transition-colors">确认签收</button>
            @endif
            @endcan
            @can('delivery.delivery-note.cancel')
            @if(in_array($note->status, [1, 2]))
            <button type="button" wire:click="cancelNote" class="rounded-md border border-orange-300 px-3 py-1.5 text-sm text-orange-600 hover:bg-orange-50 transition-colors">作废</button>
            @endif
            @endcan
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
                    <span class="text-foreground">{{ number_format($note->total_quantity) }}</span>
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

    {{-- Tab 切换 --}}
    <div class="border-b mb-4">
        <nav class="flex gap-6">
            <button type="button" @click="activeTab = 'items'" :class="activeTab === 'items' ? 'border-blue-600 text-foreground' : 'border-transparent text-muted-foreground hover:text-foreground'" class="pb-2 text-sm font-medium border-b-2 transition-colors">送货单明细</button>
            <button type="button" @click="activeTab = 'logs'" :class="activeTab === 'logs' ? 'border-blue-600 text-foreground' : 'border-transparent text-muted-foreground hover:text-foreground'" class="pb-2 text-sm font-medium border-b-2 transition-colors">
                状态变更记录
                @if($note->auditLogs->isNotEmpty())
                    <span class="ml-1.5 inline-flex items-center justify-center rounded-full bg-muted text-muted-foreground text-[10px] font-medium px-1.5 py-0.5 leading-none">{{ $note->auditLogs->count() }}</span>
                @endif
            </button>
        </nav>
    </div>

    {{-- Tab 内容：送货单明细 --}}
    <div x-show="activeTab === 'items'" x-transition>
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
                    <td class="px-4 py-2 text-right text-foreground">{!! format_quantity($item->sku_id, $item->quantity, $item->unit) !!}</td>
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
                            <span class="text-foreground">{!! format_quantity($item->sku_id, $item->picked_quantity ?? 0, $item->unit) !!}</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-muted-foreground font-mono text-xs">{{ $item->order_no ?? '-' }}</td>
                    <td class="px-4 py-2 text-center">
                        @php $ic = $itemStatusColorMap[$item->status] ?? 'gray'; @endphp
                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-{{ $ic }}-100 text-{{ $ic }}-700">{{ $itemStatusMap[$item->status] ?? '-' }}</span>
                        @if(isset($itemDiscrepancies[$item->id]))
                        @php $disc = $itemDiscrepancies[$item->id]; @endphp
                        <a href="{{ route('discrepancies') }}" class="block mt-1 text-[11px] text-blue-600 hover:text-blue-700 transition-colors" title="差异原因：{{ $disc->reason ?? '-' }}">
                            {{ $disc->discrepancy_no }} · {{ $disc->status_label }}
                        </a>
                        @endif
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

    {{-- Tab 内容：状态变更记录 --}}
    <div x-show="activeTab === 'logs'" x-transition>
        @if($note->auditLogs->isNotEmpty())
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
                    @foreach($note->auditLogs->sortByDesc('created_at') as $log)
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
                                <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-{{ $statusColorMap[$beforeStatus] ?? 'gray' }}-100 text-{{ $statusColorMap[$beforeStatus] ?? 'gray' }}-700">{{ $statusMap[$beforeStatus] ?? '-' }}</span>
                                <x-ui.icon name="arrow-right" class="w-3.5 h-3.5 inline text-muted-foreground mx-1" />
                                <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-{{ $statusColorMap[$afterStatus] ?? 'gray' }}-100 text-{{ $statusColorMap[$afterStatus] ?? 'gray' }}-700">{{ $statusMap[$afterStatus] ?? '-' }}</span>
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

    {{-- 实收差异登记弹窗（遮罩不可点击关闭） --}}
    @if($showDiffModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">登记实收差异</h2>
                <button type="button" wire:click="closeDiffModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-4">
                <div class="rounded-md bg-muted p-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">SKU</span>
                        <span class="text-foreground font-medium">{{ $diffSkuName }}</span>
                    </div>
                    <div class="flex justify-between mt-1">
                        <span class="text-muted-foreground">应送 / 实收</span>
                        <span class="text-foreground font-medium">{!! format_quantity($diffSkuId, $diffExpectedQty) !!} / {!! format_quantity($diffSkuId, $diffActualQty) !!}</span>
                    </div>
                    <div class="flex justify-between mt-1">
                        <span class="text-muted-foreground">差异数量</span>
                        <span class="text-red-600 font-medium">{!! format_quantity($diffSkuId, $diffExpectedQty - $diffActualQty) !!}</span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">差异类型</label>
                    <select wire:model="diffType" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="1">少收</option>
                        <option value="2">拒收</option>
                        <option value="3">残次</option>
                        <option value="4">其他</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">差异原因 <span class="text-red-500">*</span></label>
                    <textarea wire:model="diffReason" rows="2" maxlength="255" class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="请说明差异原因，将生成差异单由处理台统一跟进"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeDiffModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">暂不登记</button>
                <button type="button" wire:click="saveItemDiscrepancy" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">登记差异</button>
            </div>
        </div>
    </div>
    @endif
</div>