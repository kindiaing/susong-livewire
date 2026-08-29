<div class="">
    {{-- 页面标题 --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">供应商结算</h1>
            <p class="text-muted-foreground mt-1">管理供应商结算及付款确认</p>
        </div>
        @can('finance.supplier-settlement.create')
        <button type="button" wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            <x-ui.icon name="plus" class="w-4 h-4" />
            新增结算
        </button>
        @endcan
    </div>

    {{-- 搜索栏 + 工具按钮 --}}
    <div class="flex items-center gap-3 mb-4">
        <div x-data class="relative">
            <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background pl-3 pr-8 text-sm" placeholder="搜索结算单号/供应商..." />
            @if($search)
                <button type="button" wire:click="resetFilters" class="absolute right-2 top-1/2 -translate-y-1/2 p-0.5 rounded-sm text-muted-foreground/50 hover:text-muted-foreground hover:bg-muted transition-colors">
                    <x-ui.icon name="x-mark" class="w-3.5 h-3.5" />
                </button>
            @endif
        </div>
        <div class="flex-1"></div>
        <button type="button" wire:click="openColumnModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="adjustments" class="w-4 h-4" />列配置</button>
        <button type="button" wire:click="openImportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-down-tray" class="w-4 h-4" />导入</button>
        <button type="button" wire:click="openExportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-up-tray" class="w-4 h-4" />导出</button>
        @if($selectedCount > 0)
            <span class="text-sm text-muted-foreground">已选 {{ $selectedCount }} 项</span>
            @can('finance.supplier-settlement.delete')
            <button type="button" wire:click="batchDelete" class="inline-flex items-center gap-1 rounded-md bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700 transition-colors">批量删除</button>
            @endcan
            <button type="button" wire:click="clearSelection" class="text-sm text-muted-foreground hover:text-foreground transition-colors">取消选择</button>
        @endif
    </div>

    {{-- 列表 --}}
    @php
        $allCols = collect($this->getAllColumns())
            ->filter(fn($col) => $col['key'] !== 'settlement_no')
            ->values();
        $visibleCols = $allCols->filter(fn($col) => $this->isColumnVisible($col['key']));
        $gridCols = '40px 1fr';
        foreach ($visibleCols as $col) {
            $width = $col['width'] ?? '120px';
            $gridCols .= ' ' . $width;
        }
        $gridCols .= ' 120px';
    @endphp

    <div class="rounded-lg border bg-card">
        <div class="grid gap-2 border-b px-4 py-2 text-xs font-medium text-muted-foreground uppercase tracking-wider" style="grid-template-columns: {{ $gridCols }}">
            <div><input type="checkbox" wire:model.live="selectAllPage" class="rounded" /></div>
            <div>结算单号</div>
            @foreach($visibleCols as $col)
                <div>{{ $col['label'] }}</div>
            @endforeach
            <div class="action-col">操作</div>
        </div>

        @forelse($items as $item)
            <div class="grid gap-2 border-b last:border-b-0 px-4 py-2 items-center hover:bg-muted/30 transition-colors"
                 style="grid-template-columns: {{ $gridCols }}"
                 wire:key="supplier-settlement-{{ $item->id }}">
                <div><input type="checkbox" value="{{ $item->id }}" wire:model.live="selectedIds" class="rounded" /></div>
                <div>
                    <a href="{{ route('supplier-settlements.detail', $item->id) }}" class="text-sm font-mono font-medium text-blue-600 hover:underline">{{ $item->settlement_no }}</a>
                </div>
                @foreach($visibleCols as $col)
                    @switch($col['key'])
                        @case('id')
                            <div class="text-sm text-muted-foreground">{{ $item->id }}</div>
                            @break
                        @case('supplier')
                            <div class="text-sm text-foreground truncate">{{ $item->supplier?->name ?? '-' }}</div>
                            @break
                        @case('date_range')
                            <div class="text-sm text-foreground">{{ $item->start_date?->format('Y-m-d') ?? '-' }} ~ {{ $item->end_date?->format('Y-m-d') ?? '-' }}</div>
                            @break
                        @case('total_amount')
                            <div class="text-sm text-foreground text-right">{{ money_format($item->total_amount) }}</div>
                            @break
                        @case('return_amount')
                            <div class="text-sm text-right {{ $item->return_amount > 0 ? 'text-orange-600' : 'text-muted-foreground' }}">{{ $item->return_amount > 0 ? '-'.money_format($item->return_amount) : '-' }}</div>
                            @break
                        @case('service_fee')
                            <div class="text-sm text-right {{ $item->service_fee > 0 ? 'text-foreground' : 'text-muted-foreground' }}">{{ $item->service_fee > 0 ? '-'.money_format($item->service_fee) : '-' }}</div>
                            @break
                        @case('payable_amount')
                            <div class="text-sm text-foreground font-medium text-right">{{ money_format($item->payable_amount) }}</div>
                            @break
                        @case('paid_amount')
                            <div class="text-sm text-foreground text-right">{{ money_format($item->paid_amount) }}</div>
                            @break
                        @case('proxy_payment')
                            <div class="text-sm text-right {{ $item->proxy_payment > 0 ? 'text-purple-600' : 'text-muted-foreground' }}">{{ $item->proxy_payment > 0 ? money_format($item->proxy_payment) : '-' }}</div>
                            @break
                        @case('status')
                            <div>{!! status_badge($item->status, 'supplier_settlement') !!}</div>
                            @break
                        @case('created_at')
                            <div class="text-sm text-foreground">{{ $item->created_at?->format('Y-m-d H:i') }}</div>
                            @break
                        @default
                            <div class="text-sm text-foreground truncate">{{ $item->{$col['key']} ?? '-' }}</div>
                    @endswitch
                @endforeach
                <div class="action-col flex items-center gap-0.5">
                    @can('finance.supplier-settlement.create')
                    @if($item->status === 1 || $item->status === 2)
                    <button type="button" wire:click="openPartialPayment({{ $item->id }})" class="p-1 rounded text-green-600 hover:bg-green-50 hover:text-green-700 transition-colors" title="付款"><x-ui.icon name="banknotes" class="w-3.5 h-3.5" /></button>
                    @endif
                    @endcan
                    @can('finance.supplier-settlement.delete')
                    <button type="button" wire:click="confirmDelete({{ $item->id }})" class="p-1 rounded text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors" title="删除"><x-ui.icon name="trash" class="w-3.5 h-3.5" /></button>
                    @endcan
                </div>
            </div>
        @empty
            <div class="px-4 py-8 text-center text-sm text-muted-foreground">暂无结算数据</div>
        @endforelse

        {{-- 当页合计行 --}}
        @if($items->isNotEmpty())
        <div class="grid gap-2 border-t-2 border-blue-200 bg-blue-50/50 px-4 py-1.5 items-center font-medium" style="grid-template-columns: {{ $gridCols }}">
            <div></div>
            <div class="text-xs text-blue-700">当页合计</div>
            @foreach($visibleCols as $col)
                @switch($col['key'])
                    @case('total_amount')
                        <div class="text-sm text-blue-700 text-right">{{ money_format($pageTotals['total_amount']) }}</div>
                        @break
                    @case('return_amount')
                        <div class="text-sm text-blue-700 text-right">{{ $pageTotals['return_amount'] > 0 ? '-'.money_format($pageTotals['return_amount']) : '-' }}</div>
                        @break
                    @case('service_fee')
                        <div class="text-sm text-blue-700 text-right">{{ $pageTotals['service_fee'] > 0 ? '-'.money_format($pageTotals['service_fee']) : '-' }}</div>
                        @break
                    @case('payable_amount')
                        <div class="text-sm text-blue-700 font-semibold text-right">{{ money_format($pageTotals['payable_amount']) }}</div>
                        @break
                    @case('paid_amount')
                        <div class="text-sm text-blue-700 text-right">{{ money_format($pageTotals['paid_amount']) }}</div>
                        @break
                    @case('proxy_payment')
                        <div class="text-sm text-blue-700 text-right">{{ $pageTotals['proxy_payment'] > 0 ? money_format($pageTotals['proxy_payment']) : '-' }}</div>
                        @break
                    @default
                        <div></div>
                @endswitch
            @endforeach
            <div></div>
        </div>

        {{-- 全部合计行 --}}
        <div class="grid gap-2 border-t border-blue-300 bg-blue-100/50 px-4 py-1.5 items-center font-bold" style="grid-template-columns: {{ $gridCols }}">
            <div></div>
            <div class="text-xs text-blue-800">全部合计</div>
            @foreach($visibleCols as $col)
                @switch($col['key'])
                    @case('total_amount')
                        <div class="text-sm text-blue-800 text-right">{{ money_format($grandTotals['total_amount']) }}</div>
                        @break
                    @case('return_amount')
                        <div class="text-sm text-blue-800 text-right">{{ $grandTotals['return_amount'] > 0 ? '-'.money_format($grandTotals['return_amount']) : '-' }}</div>
                        @break
                    @case('service_fee')
                        <div class="text-sm text-blue-800 text-right">{{ $grandTotals['service_fee'] > 0 ? '-'.money_format($grandTotals['service_fee']) : '-' }}</div>
                        @break
                    @case('payable_amount')
                        <div class="text-sm text-blue-800 font-bold text-right">{{ money_format($grandTotals['payable_amount']) }}</div>
                        @break
                    @case('paid_amount')
                        <div class="text-sm text-blue-800 text-right">{{ money_format($grandTotals['paid_amount']) }}</div>
                        @break
                    @case('proxy_payment')
                        <div class="text-sm text-blue-800 text-right">{{ $grandTotals['proxy_payment'] > 0 ? money_format($grandTotals['proxy_payment']) : '-' }}</div>
                        @break
                    @default
                        <div></div>
                @endswitch
            @endforeach
            <div></div>
        </div>
        @endif
    </div>

    <div class="mt-4">{{ $items->links() }}</div>

    {{-- 新增弹窗 --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4 p-6 max-h-[85vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">新增结算</h2>
                <button type="button" wire:click="closeModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">供应商 <span class="text-red-500">*</span></label>
                    <select wire:model="formSupplierId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="0">请选择供应商</option>
                        @foreach($suppliers as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                    @error('formSupplierId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">周期开始 <span class="text-red-500">*</span></label>
                        <input type="date" wire:model="formStartDate" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                        @error('formStartDate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">周期结束 <span class="text-red-500">*</span></label>
                        <input type="date" wire:model="formEndDate" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                        @error('formEndDate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">服务费（元）</label>
                    <input type="text" wire:model="formServiceFee" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="可选，将从应付中扣减" />
                    @error('formServiceFee') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="rounded-md bg-muted p-3 text-xs text-muted-foreground space-y-1">
                    <p>应付金额 = 周期内已入库采购单的<strong>实际入库金额</strong>合计 − 周期内采购退货扣减 − 服务费</p>
                    <p>入库差异按实收计价天然持平（少收的货不付钱），差异损耗单作为留痕证据；已结算过的采购单不会重复计入。</p>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="save" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">保存</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 删除确认弹窗 --}}

    {{-- 部分付款弹窗 --}}
    @if($showPaymentModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">确认付款</h2>
                <button type="button" wire:click="closePaymentModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">本次付款金额（元） <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="paymentAmount" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="请输入付款金额" />
                    @error('paymentAmount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">备注</label>
                    <textarea wire:model="paymentRemark" rows="2" class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="可选"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closePaymentModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="submitPartialPayment" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">确认付款</button>
            </div>
        </div>
    </div>
    @endif

    @include('partials.column-modal')
    @include('partials.export-modal')
    @include('partials.import-modal')
</div>
