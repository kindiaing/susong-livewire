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

    {{-- 视图切换 --}}
    <div class="flex items-center gap-2 mb-4">
        <button type="button" wire:click="switchView('sku')"
            class="rounded-md px-3 py-1.5 text-sm font-medium transition-colors {{ $viewMode === 'sku' ? 'bg-blue-600 text-white' : 'bg-card border text-muted-foreground hover:text-foreground' }}">
            SKU汇总视图
        </button>
        <button type="button" wire:click="switchView('merchant')"
            class="rounded-md px-3 py-1.5 text-sm font-medium transition-colors {{ $viewMode === 'merchant' ? 'bg-blue-600 text-white' : 'bg-card border text-muted-foreground hover:text-foreground' }}">
            商家分组视图
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
                    @if($pickingTask->status === 2)
                    <th class="px-4 py-2 text-center w-24">操作</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($skuSummary as $item)
                <tr class="hover:bg-muted/30 transition-colors">
                    <td class="px-4 py-2 text-foreground">{{ $item['sku_name'] }}</td>
                    <td class="px-4 py-2 text-muted-foreground">{{ $item['unit'] }}</td>
                    <td class="px-4 py-2 text-right text-foreground">{{ $item['total_qty_display'] }}</td>
                    <td class="px-4 py-2 text-right text-foreground">{{ $item['picked_qty_display'] }}</td>
                    <td class="px-4 py-2 text-center">
                        @php $itemStatusMap = [1 => '待拣货', 2 => '已拣货', 3 => '差异']; @endphp
                        @php $itemColorMap = [1 => 'yellow', 2 => 'green', 3 => 'red']; @endphp
                        @php $ic = $itemColorMap[$item['status']] ?? 'gray'; @endphp
                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-{{ $ic }}-100 text-{{ $ic }}-700">{{ $itemStatusMap[$item['status']] ?? '-' }}</span>
                    </td>
                    @if($pickingTask->status === 2)
                    <td class="px-4 py-2 text-center">
                        <button type="button" wire:click="openPickedModal({{ $item['sku_id'] }})" class="rounded-md border border-input px-2 py-1 text-xs hover:bg-accent transition-colors">录入实拣</button>
                    </td>
                    @endif
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
    <div class="space-y-4">
        @foreach($merchantGroups as $group)
        @php $itemStatusMap = [1 => '待拣货', 2 => '已拣货', 3 => '差异']; @endphp
        @php $itemColorMap = [1 => 'yellow', 2 => 'green', 3 => 'red']; @endphp
        @php $gc = $itemColorMap[$group['status']] ?? 'gray'; @endphp
        <div class="rounded-lg border bg-card overflow-hidden">
            {{-- 商家分组标题 --}}
            <div class="px-4 py-3 border-b bg-muted/20 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <h3 class="text-sm font-semibold text-foreground">{{ $group['merchant_name'] }}</h3>
                    <span class="text-xs text-muted-foreground">{{ $group['sku_count'] }} 种 SKU</span>
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <span class="text-muted-foreground">需 {{ $group['total_quantity'] }} / 拣 {{ $group['picked_quantity'] }}</span>
                    <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-{{ $gc }}-100 text-{{ $gc }}-700">{{ $itemStatusMap[$group['status']] ?? '-' }}</span>
                </div>
            </div>
            {{-- 商家明细表 --}}
            <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs text-muted-foreground uppercase tracking-wider">
                        <th class="px-4 py-2 text-left">SKU名称</th>
                        <th class="px-4 py-2 text-left w-20">单位</th>
                        <th class="px-4 py-2 text-left w-32">订单号</th>
                        <th class="px-4 py-2 text-right w-24">需求数量</th>
                        <th class="px-4 py-2 text-right w-24">已拣数量</th>
                        <th class="px-4 py-2 text-center w-24">状态</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($group['items'] as $item)
                    <tr class="hover:bg-muted/30 transition-colors">
                        <td class="px-4 py-2 text-foreground">{{ $item['sku_name'] }}</td>
                        <td class="px-4 py-2 text-muted-foreground">{{ $item['unit'] }}</td>
                        <td class="px-4 py-2 text-muted-foreground">{{ $item['order_no'] }}</td>
                        <td class="px-4 py-2 text-right text-foreground">{{ $item['required_qty_display'] }}</td>
                        <td class="px-4 py-2 text-right text-foreground">{{ $item['picked_qty_display'] }}</td>
                        <td class="px-4 py-2 text-center">
                            @php $ic = $itemColorMap[$item['status']] ?? 'gray'; @endphp
                            <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-{{ $ic }}-100 text-{{ $ic }}-700">{{ $itemStatusMap[$item['status']] ?? '-' }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- 实拣录入弹窗（遮罩不可点击关闭） --}}
    @if($showPickedModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">录入实拣数量</h2>
                <button type="button" wire:click="closePickedModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-4">
                <div class="rounded-md bg-muted p-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">SKU</span>
                        <span class="text-foreground font-medium">{{ $editingSkuName }}</span>
                    </div>
                    <div class="flex justify-between mt-1">
                        <span class="text-muted-foreground">需求数量</span>
                        <span class="text-foreground font-medium">{{ $editingRequiredQty }}</span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">实拣数量 <span class="text-red-500">*</span></label>
                    <input type="number" wire:model="inputPickedQty" min="0" max="{{ $editingRequiredQty }}" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                </div>
                @if((int) $inputPickedQty < $editingRequiredQty)
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">差异类型</label>
                    <select wire:model="inputDiffType" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="1">少收（库存不足）</option>
                        <option value="3">残次（无法出库）</option>
                        <option value="4">其他</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">差异原因 <span class="text-red-500">*</span></label>
                    <textarea wire:model="inputDiffReason" rows="2" maxlength="255" class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="实拣少于需求时必填，将生成差异单待处理"></textarea>
                </div>
                <p class="text-xs text-muted-foreground">缺口 {{ $editingRequiredQty - (int) $inputPickedQty }} 将按订单明细分摊并生成拣货差异单，差异处理台统一跟进</p>
                @endif
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closePickedModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="savePickedQuantity" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">保存</button>
            </div>
        </div>
    </div>
    @endif
</div>