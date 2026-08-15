<div class="">
    {{-- 页面标题 --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">送货单</h1>
            <p class="text-muted-foreground mt-1">管理送货单及分货确认</p>
        </div>
    </div>

    {{-- 搜索栏 + 筛选 + 工具按钮 --}}
    <div class="flex items-center gap-3 mb-4">
        <div x-data class="relative">
            <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background pl-3 pr-8 text-sm" placeholder="搜索送货单号/商户名称..." />
            @if($search)
                <button type="button" wire:click="$set('search','')" class="absolute right-2 top-1/2 -translate-y-1/2 p-0.5 rounded-sm text-muted-foreground/50 hover:text-muted-foreground hover:bg-muted transition-colors">
                    <x-ui.icon name="x-mark" class="w-3.5 h-3.5" />
                </button>
            @endif
        </div>
        <select wire:model.live="filterStatus" class="flex h-9 w-32 rounded-md border border-input bg-background px-3 text-sm">
            <option value="0">全部状态</option>
            <option value="1">待分货</option>
            <option value="2">已分货</option>
            <option value="3">已签收</option>
            <option value="4">已作废</option>
        </select>
        <select wire:model.live="filterMerchantId" class="flex h-9 w-40 rounded-md border border-input bg-background px-3 text-sm">
            <option value="0">全部商户</option>
            @foreach($merchantOptions as $id => $name)
            <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </select>
        <input type="date" wire:model.live="filterDeliveryDate" class="flex h-9 w-36 rounded-md border border-input bg-background px-3 text-sm" />
        <div class="flex-1"></div>
        <button type="button" wire:click="openColumnModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="adjustments" class="w-4 h-4" />列配置</button>
        <button type="button" wire:click="openExportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-up-tray" class="w-4 h-4" />导出</button>
        @if($selectedCount > 0)
            <span class="text-sm text-muted-foreground">已选 {{ $selectedCount }} 项</span>
            @can('delivery.delivery-note.cancel')
            <button type="button" wire:click="batchDelete" class="inline-flex items-center gap-1 rounded-md bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700 transition-colors">批量作废</button>
            @endcan
            <button type="button" wire:click="clearSelection" class="text-sm text-muted-foreground hover:text-foreground transition-colors">取消选择</button>
        @endif
    </div>

    {{-- 列表 --}}
    @php
        $statusMap = \App\Livewire\Delivery\DeliveryNoteList::$statusMap;
        $statusColorMap = \App\Livewire\Delivery\DeliveryNoteList::$statusColorMap;
        $allCols = collect($this->getAllColumns())
            ->filter(fn($col) => !in_array($col['key'], ['note_no']))
            ->values();
        $visibleCols = $allCols->filter(fn($col) => $this->isColumnVisible($col['key']));
        $gridCols = '40px 160px';
        foreach ($visibleCols as $col) {
            $width = $col['width'] ?? '120px';
            $gridCols .= ' ' . $width;
        }
        $gridCols .= ' 180px';
    @endphp

    <div class="rounded-lg border bg-card">
        <div class="grid gap-3 border-b px-6 py-3 text-xs font-medium text-muted-foreground uppercase tracking-wider" style="grid-template-columns: {{ $gridCols }}">
            <div><input type="checkbox" wire:model.live="selectAllPage" class="rounded" /></div>
            <div>送货单号</div>
            @foreach($visibleCols as $col)
                <div>{{ $col['label'] }}</div>
            @endforeach
            <div>操作</div>
        </div>

        @forelse($items as $item)
            <div class="grid gap-3 border-b last:border-b-0 px-6 py-2 items-center hover:bg-muted/30 transition-colors"
                 style="grid-template-columns: {{ $gridCols }}"
                 wire:key="delivery-note-{{ $item->id }}">
                <div><input type="checkbox" value="{{ $item->id }}" wire:model.live="selectedIds" class="rounded" /></div>
                <div class="text-sm font-mono font-medium truncate">
                    <a href="{{ route('delivery-notes.detail', $item->id) }}" class="text-blue-600 hover:underline">{{ $item->note_no }}</a>
                </div>
                @foreach($visibleCols as $col)
                    @switch($col['key'])
                        @case('id')
                            <div class="text-sm text-muted-foreground">{{ $item->id }}</div>
                            @break
                        @case('merchant_name')
                            <div class="text-sm text-foreground truncate max-w-[160px]">{{ $item->merchant_name ?: ($item->merchant?->name ?? '-') }}</div>
                            @break
                        @case('merchant_address')
                            <div class="text-sm text-muted-foreground truncate max-w-[200px]">{{ $item->merchant_address ?: '-' }}</div>
                            @break
                        @case('delivery_date')
                            <div class="text-sm text-foreground">{{ $item->delivery_date?->format('Y-m-d') ?? '-' }}</div>
                            @break
                        @case('status')
                            <div>
                                @php $c = $statusColorMap[$item->status] ?? 'gray'; @endphp
                                <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-{{ $c }}-100 text-{{ $c }}-700">{{ $statusMap[$item->status] ?? '-' }}</span>
                            </div>
                            @break
                        @case('total_quantity')
                            <div class="text-sm text-foreground">{{ $item->total_quantity ?? 0 }}</div>
                            @break
                        @case('order_nos')
                            <div class="text-sm text-muted-foreground truncate max-w-[200px]">{{ is_array($item->order_nos) ? implode(', ', $item->order_nos) : '-' }}</div>
                            @break
                        @case('product_summary')
                            <div class="text-sm text-muted-foreground truncate max-w-[200px]">{{ $item->product_summary ?: '-' }}</div>
                            @break
                        @case('delivered_at')
                            <div class="text-sm text-foreground">{{ $item->delivered_at?->format('Y-m-d H:i') ?? '-' }}</div>
                            @break
                        @case('remark')
                            <div class="text-sm text-foreground truncate max-w-[200px]">{{ $item->remark ?: '-' }}</div>
                            @break
                        @default
                            <div class="text-sm text-foreground truncate">{{ $item->{$col['key']} ?? '-' }}</div>
                    @endswitch
                @endforeach
                <div class="flex items-center gap-2">
                    @can('delivery.delivery-note.deliver')
                    @if($item->status === 1)
                    <button type="button" wire:click="markDelivered({{ $item->id }})" class="text-blue-600 hover:text-blue-700 text-sm">已分货</button>
                    @endif
                    @endcan
                    @can('delivery.delivery-note.sign')
                    @if($item->status === 2)
                    <button type="button" wire:click="markSigned({{ $item->id }})" class="text-green-600 hover:text-green-700 text-sm">已签收</button>
                    @endif
                    @endcan
                    @can('delivery.delivery-note.cancel')
                    @if($item->status === 1)
                    <button type="button" wire:click="confirmDelete({{ $item->id }})" class="p-1 rounded text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors" title="作废"><x-ui.icon name="trash" class="w-3.5 h-3.5" /></button>
                    @endif
                    @endcan
                </div>
            </div>
        @empty
            <div class="px-6 py-12 text-center text-sm text-muted-foreground">暂无送货单数据</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $items->links() }}</div>

    {{-- 删除确认弹窗（作废） --}}
    @include('partials.column-modal')
    @include('partials.export-modal')
    @include('partials.delete-confirm')
</div>