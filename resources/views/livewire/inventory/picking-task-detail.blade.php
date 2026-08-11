<div class="">
    {{-- 页面标题 + 状态操作 --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('picking-tasks') }}" class="text-muted-foreground hover:text-foreground transition-colors">
                <x-ui.icon name="arrow-left" class="w-5 h-5" />
            </a>
            <div>
                <h1 class="text-2xl font-bold text-foreground">
                    {{ $pickingTask->task_no }}
                </h1>
                <p class="text-muted-foreground mt-1">拣货总单详情</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @php $taskStatusMap = [1 => '待分配', 2 => '拣货中', 3 => '已完成']; @endphp
            @php $taskColorMap = [1 => 'orange', 2 => 'blue', 3 => 'green']; @endphp
            @php $tc = $taskColorMap[$pickingTask->status] ?? 'gray'; @endphp
            <span class="inline-flex items-center rounded px-2 py-1 text-sm font-medium bg-{{ $tc }}-100 text-{{ $tc }}-700">
                {{ $taskStatusMap[$pickingTask->status] ?? '-' }}
            </span>
            @if($pickingTask->status === 1)
            <button type="button" wire:click="startPicking" class="rounded-md bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700 transition-colors">开始拣货</button>
            @endif
            @if($pickingTask->status === 2)
            <button type="button" wire:click="completePicking" class="rounded-md bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700 transition-colors">完成拣货</button>
            @endif
        </div>
    </div>

    {{-- 基本信息卡片 --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <div class="rounded-lg border bg-card p-4">
            <h3 class="text-xs font-medium text-muted-foreground uppercase tracking-wider mb-3">任务信息</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-muted-foreground">线路</span>
                    <span class="text-foreground font-medium">{{ $pickingTask->deliveryRoute?->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">送达日期</span>
                    <span class="text-foreground">{{ $pickingTask->delivery_date?->format('Y-m-d') ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">SKU种数</span>
                    <span class="text-foreground">{{ $pickingTask->total_skus ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">总数量</span>
                    <span class="text-foreground">{{ $pickingTask->total_quantity ?? '-' }}</span>
                </div>
            </div>
        </div>

        <div class="rounded-lg border bg-card p-4">
            <h3 class="text-xs font-medium text-muted-foreground uppercase tracking-wider mb-3">仓库配置</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-muted-foreground">仓库</span>
                    <span class="text-foreground font-medium">{{ $pickingTask->warehouse?->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">拣货员</span>
                    <span class="text-foreground">{{ $pickingTask->picker?->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">开始时间</span>
                    <span class="text-foreground">{{ $pickingTask->started_at?->format('Y-m-d H:i:s') ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">完成时间</span>
                    <span class="text-foreground">{{ $pickingTask->completed_at?->format('Y-m-d H:i:s') ?? '-' }}</span>
                </div>
            </div>
        </div>

        <div class="rounded-lg border bg-card p-4">
            <h3 class="text-xs font-medium text-muted-foreground uppercase tracking-wider mb-3">统计</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-muted-foreground">明细条数</span>
                    <span class="text-foreground font-medium">{{ $pickingTask->items->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">待拣货数</span>
                    <span class="text-foreground">{{ $pickingTask->items->where('status', 1)->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">已拣货数</span>
                    <span class="text-foreground">{{ $pickingTask->items->where('status', 2)->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">差异数</span>
                    <span class="text-foreground">{{ $pickingTask->items->where('status', 3)->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- 视图切换标签 --}}
    <div class="flex items-center gap-1 mb-4 rounded-lg border bg-muted/30 p-1 w-fit">
        <button type="button" wire:click="switchViewMode('sku')"
            class="rounded-md px-4 py-1.5 text-sm font-medium transition-colors {{ $viewMode === 'sku' ? 'bg-background text-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground' }}">
            SKU汇总
        </button>
        <button type="button" wire:click="switchViewMode('merchant')"
            class="rounded-md px-4 py-1.5 text-sm font-medium transition-colors {{ $viewMode === 'merchant' ? 'bg-background text-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground' }}">
            商家分组
        </button>
    </div>

    {{-- SKU汇总视图 --}}
    @if($viewMode === 'sku')
    <div class="rounded-lg border bg-card">
        <div class="px-6 py-4 border-b">
            <h3 class="text-sm font-semibold text-foreground">SKU汇总（{{ count($skuSummary) }} 种）</h3>
        </div>
        @if(count($skuSummary) > 0)
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-muted/20">
                <tr class="text-xs text-muted-foreground uppercase tracking-wider">
                    <th class="px-4 py-2 text-left">SKU名称</th>
                    <th class="px-4 py-2 text-left w-20">单位</th>
                    <th class="px-4 py-2 text-right w-24">需求数量</th>
                    <th class="px-4 py-2 text-right w-24">已拣数量</th>
                    <th class="px-4 py-2 text-center w-24">状态</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($skuSummary as $item)
                <tr class="hover:bg-muted/30 transition-colors">
                    <td class="px-4 py-2 text-foreground">{{ $item['sku_name'] }}</td>
                    <td class="px-4 py-2 text-muted-foreground">{{ $item['unit'] }}</td>
                    <td class="px-4 py-2 text-right text-foreground">{{ $item['total_quantity'] }}</td>
                    <td class="px-4 py-2 text-right text-foreground">{{ $item['picked_quantity'] }}</td>
                    <td class="px-4 py-2 text-center">
                        @php $itemStatusMap = [1 => '待拣货', 2 => '已拣货', 3 => '差异']; @endphp
                        @php $itemColorMap = [1 => 'yellow', 2 => 'green', 3 => 'red']; @endphp
                        @php $ic = $itemColorMap[$item['status']] ?? 'gray'; @endphp
                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-{{ $ic }}-100 text-{{ $ic }}-700">{{ $itemStatusMap[$item['status']] ?? '-' }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        @else
        <div class="px-6 py-8 text-center text-sm text-muted-foreground">暂无SKU汇总数据</div>
        @endif
    </div>
    @endif

    {{-- 商家分组视图 --}}
    @if($viewMode === 'merchant')
    @foreach($merchantGroups as $group)
    <div class="rounded-lg border bg-card mb-4">
        <div class="px-6 py-4 border-b flex items-center justify-between cursor-pointer" wire:click.prevent="" x-data="{ open: true }" @click="open = !open">
            <div class="flex items-center gap-2">
                <x-ui.icon name="building-storefront" class="w-4 h-4 text-muted-foreground" />
                <h3 class="text-sm font-semibold text-foreground">{{ $group['merchant_name'] }}</h3>
                <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-muted text-muted-foreground">{{ count($group['items']) }} 项</span>
            </div>
            <div x-show="open" x-transition>
                <x-ui.icon name="chevron-up" class="w-4 h-4 text-muted-foreground" />
            </div>
            <div x-show="!open" x-transition>
                <x-ui.icon name="chevron-down" class="w-4 h-4 text-muted-foreground" />
            </div>
        </div>
        <div x-show="open" x-transition>
        @if(count($group['items']) > 0)
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-muted/20">
                <tr class="text-xs text-muted-foreground uppercase tracking-wider">
                    <th class="px-4 py-2 text-left">SKU名称</th>
                    <th class="px-4 py-2 text-left w-20">单位</th>
                    <th class="px-4 py-2 text-right w-24">需求数量</th>
                    <th class="px-4 py-2 text-right w-24">已拣数量</th>
                    <th class="px-4 py-2 text-left w-32">订单编号</th>
                    <th class="px-4 py-2 text-center w-24">状态</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($group['items'] as $item)
                <tr class="hover:bg-muted/30 transition-colors">
                    <td class="px-4 py-2 text-foreground">{{ $item['sku_name'] }}</td>
                    <td class="px-4 py-2 text-muted-foreground">{{ $item['unit'] }}</td>
                    <td class="px-4 py-2 text-right text-foreground">{{ $item['required_quantity'] }}</td>
                    <td class="px-4 py-2 text-right text-foreground">{{ $item['picked_quantity'] }}</td>
                    <td class="px-4 py-2 font-mono text-xs text-foreground">{{ $item['order_no'] ?? '-' }}</td>
                    <td class="px-4 py-2 text-center">
                        @php $itemStatusMap = [1 => '待拣货', 2 => '已拣货', 3 => '差异']; @endphp
                        @php $itemColorMap = [1 => 'yellow', 2 => 'green', 3 => 'red']; @endphp
                        @php $ic = $itemColorMap[$item['status']] ?? 'gray'; @endphp
                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-{{ $ic }}-100 text-{{ $ic }}-700">{{ $itemStatusMap[$item['status']] ?? '-' }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        @else
        <div class="px-6 py-8 text-center text-sm text-muted-foreground">暂无明细数据</div>
        @endif
        </div>
    </div>
    @endforeach
    @if(count($merchantGroups) === 0)
    <div class="rounded-lg border bg-card">
        <div class="px-6 py-8 text-center text-sm text-muted-foreground">暂无商家分组数据</div>
    </div>
    @endif
    @endif
</div>