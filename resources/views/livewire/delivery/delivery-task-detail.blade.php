<div class="">
    {{-- Page Title --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('delivery-tasks') }}" class="text-muted-foreground hover:text-foreground transition-colors">
                <x-ui.icon name="arrow-left" class="w-5 h-5" />
            </a>
            <div>
                <h1 class="text-2xl font-bold text-foreground">
                    {{ $task->task_no }}
                </h1>
                <p class="text-muted-foreground mt-1">配送任务详情</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @php $c = $statusColorMap[$task->status] ?? 'gray'; @endphp
            <span class="inline-flex items-center rounded px-2 py-1 text-sm font-medium bg-{{ $c }}-100 text-{{ $c }}-700">
                {{ $statusMap[$task->status] ?? '-' }}
            </span>
        </div>
    </div>

    {{-- Basic Info Cards --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <div class="rounded-lg border bg-card p-4">
            <h3 class="text-xs font-medium text-muted-foreground uppercase tracking-wider mb-3">任务信息</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-muted-foreground">线路</span>
                    <span class="text-foreground font-medium">{{ $task->deliveryRoute?->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">送达日期</span>
                    <span class="text-foreground">{{ $task->delivery_date?->format('Y-m-d') ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">批次</span>
                    <span class="text-foreground">{{ $batchMap[$task->batch] ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">生成时间</span>
                    <span class="text-foreground">{{ $task->generated_at?->format('Y-m-d H:i:s') ?? '-' }}</span>
                </div>
            </div>
        </div>

        <div class="rounded-lg border bg-card p-4">
            <h3 class="text-xs font-medium text-muted-foreground uppercase tracking-wider mb-3">配送配置</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-muted-foreground">司机</span>
                    <span class="text-foreground font-medium">{{ $task->driver?->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">车辆</span>
                    <span class="text-foreground">{{ $task->vehicle?->plate_number ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">计划出发</span>
                    <span class="text-foreground">{{ $task->planned_start_time?->format('Y-m-d H:i') ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">实际开始</span>
                    <span class="text-foreground">{{ $task->actual_start_time?->format('Y-m-d H:i:s') ?? '-' }}</span>
                </div>
            </div>
        </div>

        <div class="rounded-lg border bg-card p-4">
            <h3 class="text-xs font-medium text-muted-foreground uppercase tracking-wider mb-3">配送统计</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-muted-foreground">总点位</span>
                    <span class="text-foreground font-medium">{{ $task->total_stops }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">已完成</span>
                    <span class="text-foreground">{{ $task->completed_stops }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">总单据</span>
                    <span class="text-foreground">{{ $task->total_orders }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">备注</span>
                    <span class="text-foreground">{{ $task->remark ?: '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Delivery Sequence Table --}}
    <div class="rounded-lg border bg-card mb-6">
        <div class="px-6 py-4 border-b">
            <h3 class="text-sm font-semibold text-foreground">配送顺序表（{{ $task->sequences->count() }} 个点位）</h3>
        </div>
        @if($task->sequences->count() > 0)
        <table class="w-full text-sm">
            <thead class="bg-muted/20">
                <tr class="text-xs text-muted-foreground uppercase tracking-wider">
                    <th class="px-4 py-2 text-left w-16">顺序</th>
                    <th class="px-4 py-2 text-left">商户</th>
                    <th class="px-4 py-2 text-left">地址</th>
                    <th class="px-4 py-2 text-center w-24">单据数</th>
                    <th class="px-4 py-2 text-center w-24">状态</th>
                    <th class="px-4 py-2 text-center w-32">到达时间</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($task->sequences->sortBy('sequence_no') as $seq)
                <tr class="hover:bg-muted/30 transition-colors">
                    <td class="px-4 py-2 text-foreground font-medium">{{ $seq->sequence_no }}</td>
                    <td class="px-4 py-2 text-foreground">{{ $seq->merchant_name }}</td>
                    <td class="px-4 py-2 text-muted-foreground truncate max-w-[200px]">{{ $seq->merchant_address ?: '-' }}</td>
                    <td class="px-4 py-2 text-center text-foreground">{{ is_array($seq->task_detail_ids) ? count($seq->task_detail_ids) : 0 }}</td>
                    <td class="px-4 py-2 text-center">
                        @php $seqStatusMap = [1 => '待配送', 2 => '配送中', 3 => '已到达', 4 => '已送达', 5 => '已跳过', 6 => '失败']; @endphp
                        @php $seqColorMap = [1 => 'yellow', 2 => 'blue', 3 => 'orange', 4 => 'green', 5 => 'gray', 6 => 'red']; @endphp
                        @php $sc = $seqColorMap[$seq->status] ?? 'gray'; @endphp
                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-{{ $sc }}-100 text-{{ $sc }}-700">{{ $seqStatusMap[$seq->status] ?? '-' }}</span>
                    </td>
                    <td class="px-4 py-2 text-center text-muted-foreground">{{ $seq->actual_arrival?->format('H:i') ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="px-6 py-8 text-center text-sm text-muted-foreground">暂无配送顺序数据</div>
        @endif
    </div>

    {{-- Delivery Details Table --}}
    <div class="rounded-lg border bg-card">
        <div class="px-6 py-4 border-b">
            <h3 class="text-sm font-semibold text-foreground">配送明细（{{ $task->details->count() }} 条）</h3>
        </div>
        @if($task->details->count() > 0)
        <table class="w-full text-sm">
            <thead class="bg-muted/20">
                <tr class="text-xs text-muted-foreground uppercase tracking-wider">
                    <th class="px-4 py-2 text-left">订单编号</th>
                    <th class="px-4 py-2 text-left">商户</th>
                    <th class="px-4 py-2 text-left">商品摘要</th>
                    <th class="px-4 py-2 text-right">数量</th>
                    <th class="px-4 py-2 text-right">金额</th>
                    <th class="px-4 py-2 text-center">状态</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($task->details as $detail)
                <tr class="hover:bg-muted/30 transition-colors">
                    <td class="px-4 py-2 font-mono text-xs text-foreground">{{ $detail->order?->order_no ?? '-' }}</td>
                    <td class="px-4 py-2 text-foreground">{{ $detail->merchant_name }}</td>
                    <td class="px-4 py-2 text-muted-foreground truncate max-w-[200px]">{{ $detail->product_summary ?: '-' }}</td>
                    <td class="px-4 py-2 text-right text-foreground">{{ $detail->total_quantity }}</td>
                    <td class="px-4 py-2 text-right text-foreground">{{ money_format($detail->order?->final_amount ?? 0) }}</td>
                    <td class="px-4 py-2 text-center">
                        @php $detailStatusMap = [1 => '待配送', 2 => '配送中', 3 => '已送达', 4 => '已取消']; @endphp
                        @php $dsc = [1 => 'yellow', 2 => 'blue', 3 => 'green', 4 => 'red']; @endphp
                        @php $dc = $dsc[$detail->status] ?? 'gray'; @endphp
                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-{{ $dc }}-100 text-{{ $dc }}-700">{{ $detailStatusMap[$detail->status] ?? '-' }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="px-6 py-8 text-center text-sm text-muted-foreground">暂无配送明细数据</div>
        @endif
    </div>
</div>