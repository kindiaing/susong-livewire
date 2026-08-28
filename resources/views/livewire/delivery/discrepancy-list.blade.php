<div class="">
    {{-- 页面标题 --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">差异处理</h1>
            <p class="text-muted-foreground mt-1">拣货/配送/实收差异的统一登记与处理台</p>
        </div>
    </div>

    {{-- 搜索栏 + 筛选 + 工具按钮 --}}
    <div class="flex items-center gap-3 mb-4">
        <div x-data class="relative">
            <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background pl-3 pr-8 text-sm" placeholder="搜索差异单号..." />
            @if($search)
                <button type="button" wire:click="resetFilters" class="absolute right-2 top-1/2 -translate-y-1/2 p-0.5 rounded-sm text-muted-foreground/50 hover:text-muted-foreground hover:bg-muted transition-colors">
                    <x-ui.icon name="x-mark" class="w-3.5 h-3.5" />
                </button>
            @endif
        </div>
        <select wire:model.live="stageFilter" class="h-9 rounded-md border border-input bg-background px-2 text-sm">
            <option value="">全部环节</option>
            <option value="1">拣货</option>
            <option value="2">配送</option>
            <option value="3">实收</option>
            <option value="4">入库</option>
        </select>
        <select wire:model.live="statusFilter" class="h-9 rounded-md border border-input bg-background px-2 text-sm">
            <option value="">全部状态</option>
            <option value="1">待处理</option>
            <option value="2">处理中</option>
            <option value="3">已处理</option>
            <option value="4">已关闭</option>
            <option value="5">争议中</option>
        </select>
        <div class="flex-1"></div>
        <button type="button" wire:click="openColumnModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="adjustments" class="w-4 h-4" />列配置</button>
        <button type="button" wire:click="openImportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-down-tray" class="w-4 h-4" />导入</button>
        <button type="button" wire:click="openExportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-up-tray" class="w-4 h-4" />导出</button>
        @if($selectedCount > 0)
            <span class="text-sm text-muted-foreground">已选 {{ $selectedCount }} 项</span>
            @can('delivery.discrepancy.delete')
            <button type="button" wire:click="batchDelete" class="inline-flex items-center gap-1 rounded-md bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700 transition-colors">批量删除</button>
            @endcan
            <button type="button" wire:click="clearSelection" class="text-sm text-muted-foreground hover:text-foreground transition-colors">取消选择</button>
        @endif
    </div>

    {{-- 列表 --}}
    @php
        $stageMap = [1 => '拣货', 2 => '配送', 3 => '实收', 4 => '入库'];
        $typeMap = [1 => '少收', 2 => '拒收', 3 => '残次', 4 => '其他'];
        $partyMap = [1 => '供应商', 2 => '平台', 3 => '司机', 4 => '商家'];
        $decisionMap = [1 => '补货', 2 => '退款', 3 => '扣款', 4 => '报损', 5 => '不计'];
        $allCols = collect($this->getAllColumns())
            ->filter(fn($col) => $col['key'] !== 'discrepancy_no')
            ->values();
        $visibleCols = $allCols->filter(fn($col) => $this->isColumnVisible($col['key']));
        $gridCols = '40px 1fr';
        foreach ($visibleCols as $col) {
            $width = $col['width'] ?? '110px';
            $gridCols .= ' ' . $width;
        }
        $gridCols .= ' 140px';
    @endphp

    <div class="rounded-lg border bg-card">
        <div class="grid gap-3 border-b px-6 py-3 text-xs font-medium text-muted-foreground uppercase tracking-wider" style="grid-template-columns: {{ $gridCols }}">
            <div><input type="checkbox" wire:model.live="selectAllPage" class="rounded" /></div>
            <div>差异编号</div>
            @foreach($visibleCols as $col)
                <div>{{ $col['label'] }}</div>
            @endforeach
            <div>操作</div>
        </div>

        @forelse($items as $item)
            <div class="grid gap-3 border-b last:border-b-0 px-6 py-3 items-center hover:bg-muted/30 transition-colors"
                 style="grid-template-columns: {{ $gridCols }}"
                 wire:key="discrepancy-{{ $item->id }}">
                <div><input type="checkbox" value="{{ $item->id }}" wire:model.live="selectedIds" class="rounded" /></div>
                <div class="text-sm font-mono font-medium"><a href="{{ route('discrepancies.detail', $item->id) }}" class="text-blue-600 hover:underline">{{ $item->discrepancy_no }}</a></div>
                @foreach($visibleCols as $col)
                    @switch($col['key'])
                        @case('id')
                            <div class="text-sm text-muted-foreground">{{ $item->id }}</div>
                            @break
                        @case('stage')
                            <div class="text-sm text-foreground">{{ $stageMap[$item->stage] ?? '-' }}</div>
                            @break
                        @case('type')
                            <div class="text-sm text-foreground">{{ $typeMap[$item->type] ?? '-' }}</div>
                            @break
                        @case('quantity_diff')
                            <div class="text-sm text-foreground">{!! format_quantity($item->sku_id, $item->expected_quantity, $item->sku?->baseUnit?->name) !!} → {!! format_quantity($item->sku_id, $item->actual_quantity, $item->sku?->baseUnit?->name) !!}（差 {!! format_quantity($item->sku_id, $item->quantity_diff, $item->sku?->baseUnit?->name) !!}）</div>
                            @break
                        @case('amount_diff')
                            <div class="text-sm text-foreground text-right">{{ money_format($item->amount_diff) }}</div>
                            @break
                        @case('responsible_party')
                            <div class="text-sm text-foreground">{{ $partyMap[$item->responsible_party] ?? '-' }}</div>
                            @break
                        @case('decision')
                            <div class="text-sm text-foreground">{{ $decisionMap[$item->decision] ?? '-' }}</div>
                            @break
                        @case('status')
                            <div>{!! status_badge($item->status, 'discrepancy') !!}</div>
                            @break
                        @case('reason')
                            <div class="text-sm text-foreground truncate max-w-[200px]" title="{{ $item->reason }}">{{ $item->reason ?? '-' }}</div>
                            @break
                        @case('created_at')
                            <div class="text-sm text-foreground">{{ $item->created_at?->format('Y-m-d H:i') }}</div>
                            @break
                        @default
                            <div class="text-sm text-foreground truncate">{{ $item->{$col['key']} ?? '-' }}</div>
                    @endswitch
                @endforeach
                <div class="flex items-center gap-2">
                    @if($item->status === 1 && $this->canProcess())
                    <button type="button" wire:click="markProcessing({{ $item->id }})" class="p-1 rounded text-blue-600 hover:bg-blue-50 transition-colors" title="标记处理中"><x-ui.icon name="clock" class="w-3.5 h-3.5" /></button>
                    @endif
                    @if(in_array($item->status, [1, 2]) && $this->canProcess())
                    <button type="button" wire:click="markDisputed({{ $item->id }})" class="p-1 rounded text-red-600 hover:bg-red-50 transition-colors" title="转入争议"><x-ui.icon name="shield-exclamation" class="w-3.5 h-3.5" /></button>
                    @endif
                    @if(in_array($item->status, [1, 2, 5]) && $this->canProcess())
                    <button type="button" wire:click="openProcess({{ $item->id }})" class="p-1 rounded text-blue-600 hover:bg-blue-50 transition-colors" title="处理"><x-ui.icon name="adjustments" class="w-3.5 h-3.5" /></button>
                    @endif
                    @can('delivery.discrepancy.restock')
                    @if(in_array($item->type, [2, 3]) && $item->quantity_diff > 0 && in_array($item->status, [1, 2, 5]))
                    <button type="button" wire:click="openRestock({{ $item->id }})" class="p-1 rounded text-green-600 hover:bg-green-50 transition-colors" title="回库"><x-ui.icon name="arrow-down-tray" class="w-3.5 h-3.5" /></button>
                    @endif
                    @endcan
                    @can('delivery.discrepancy.delete')
                    <button type="button" wire:click="confirmDelete({{ $item->id }})" class="p-1 rounded text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors" title="删除"><x-ui.icon name="trash" class="w-3.5 h-3.5" /></button>
                    @endcan
                </div>
            </div>
        @empty
            <div class="px-6 py-12 text-center text-sm text-muted-foreground">暂无差异数据</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $items->links() }}</div>

    {{-- 处理弹窗（遮罩不可点击关闭） --}}
    @if($showProcessModal && $processing)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">处理差异单</h2>
                <button type="button" wire:click="closeProcessModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="rounded-md bg-muted p-3 text-sm space-y-1 mb-4">
                <div class="flex justify-between">
                    <span class="text-muted-foreground">差异编号</span>
                    <span class="text-foreground font-mono">{{ $processing->discrepancy_no }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">环节 / 类型</span>
                    <span class="text-foreground">{{ $stageMap[$processing->stage] ?? '-' }} / {{ $typeMap[$processing->type] ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">SKU</span>
                    <span class="text-foreground">{{ $processing->sku?->sku_name ?? $processing->orderItem?->sku?->sku_name ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">关联订单</span>
                    <span class="text-foreground font-mono">{{ $processing->order?->order_no ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">预期 / 实际（差异）</span>
                    <span class="text-foreground">{!! format_quantity($processing->sku_id, $processing->expected_quantity, $processing->sku?->baseUnit?->name) !!} / {!! format_quantity($processing->sku_id, $processing->actual_quantity, $processing->sku?->baseUnit?->name) !!}（<span class="text-red-600">{!! format_quantity($processing->sku_id, $processing->quantity_diff, $processing->sku?->baseUnit?->name) !!}</span>）</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">差异金额（估）</span>
                    <span class="text-foreground">{{ money_format($processing->amount_diff) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">差异原因</span>
                    <span class="text-foreground truncate max-w-[260px]" title="{{ $processing->reason }}">{{ $processing->reason ?? '-' }}</span>
                </div>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">责任方 <span class="text-red-500">*</span></label>
                    <select wire:model="processResponsibleParty" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="1">供应商</option>
                        <option value="2">平台</option>
                        <option value="3">司机</option>
                        <option value="4">商家</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">处理决策 <span class="text-red-500">*</span></label>
                    <select wire:model.live="processDecision" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="1">补货（登记决策，补货单二期生成）</option>
                        <option value="2">退款（登记决策，金额调整二期执行）</option>
                        <option value="3">扣款（登记决策，金额调整二期执行）</option>
                        <option value="4">报损（生成损耗单扣减库存）</option>
                        <option value="5">不计（直接关闭）</option>
                    </select>
                </div>
                @if((int) $processDecision === 4 && setting('discrepancy_auto_create_loss', true))
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">报损扣减仓库 <span class="text-red-500">*</span></label>
                    <select wire:model="processWarehouseId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-muted-foreground mt-1">将自动生成待审核损耗单，审核执行后扣减该仓库库存</p>
                </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">处理备注</label>
                    <textarea wire:model="processRemark" rows="2" maxlength="255" class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeProcessModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="process" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">确认处理</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 回库弹窗（遮罩不可点击关闭） --}}
    @if($showRestockModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">拒收/残次回库</h2>
                <button type="button" wire:click="closeRestockModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <p class="text-sm text-muted-foreground mb-4">确认货品已带回仓库且可再售，差异数量将回补到所选仓库库存并留痕；不可再售的请走「处理 → 报损」。</p>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">回库仓库 <span class="text-red-500">*</span></label>
                    <select wire:model="restockWarehouseId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">备注</label>
                    <textarea wire:model="restockRemark" rows="2" maxlength="255" class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeRestockModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="restock" class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition-colors">确认回库</button>
            </div>
        </div>
    </div>
    @endif

    @include('partials.column-modal')
    @include('partials.export-modal')
    @include('partials.import-modal')
    @include('partials.delete-confirm')
</div>
