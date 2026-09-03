<div class="">
    {{-- 页面标题 --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">应收账款</h1>
            <p class="text-muted-foreground mt-1">管理应收账款及收款确认</p>
        </div>
        @can('finance.receivable.create')
        <button type="button" wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            <x-ui.icon name="plus" class="w-4 h-4" />
            新增应收
        </button>
        @endcan
    </div>

    {{-- 搜索栏 + 工具按钮 --}}
    <div class="flex items-center gap-3 mb-4">
        <div x-data class="relative">
            <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background pl-3 pr-8 text-sm" placeholder="搜索订单号/商家..." />
            @if($search)
                <button type="button" wire:click="resetFilters" class="absolute right-2 top-1/2 -translate-y-1/2 p-0.5 rounded-sm text-muted-foreground/50 hover:text-muted-foreground hover:bg-muted transition-colors">
                    <x-ui.icon name="x-mark" class="w-3.5 h-3.5" />
                </button>
            @endif
        </div>
        <select wire:model.live="filterStatus" class="flex h-9 rounded-md border border-input bg-background px-3 text-sm">
            <option value="-1">全部状态</option>
            @foreach(\App\Models\Receivable::statusMap() as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
        <select wire:model.live="filterSettlementType" class="flex h-9 rounded-md border border-input bg-background px-3 text-sm">
            <option value="-1">全部结算方式</option>
            @foreach(\App\Models\Receivable::settlementTypeMap() as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
        <div class="flex items-center gap-1" title="按到期日筛选">
            <input type="date" wire:model.live="filterDateStart" class="flex h-9 rounded-md border border-input bg-background px-2 text-sm" />
            <span class="text-muted-foreground text-sm">~</span>
            <input type="date" wire:model.live="filterDateEnd" class="flex h-9 rounded-md border border-input bg-background px-2 text-sm" />
        </div>
        <div class="flex-1"></div>
        <button type="button" wire:click="openColumnModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="adjustments" class="w-4 h-4" />列配置</button>
        <button type="button" wire:click="openImportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-down-tray" class="w-4 h-4" />导入</button>
        <button type="button" wire:click="openExportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-up-tray" class="w-4 h-4" />导出</button>
        @if($selectedCount > 0)
            <span class="text-sm text-muted-foreground">已选 {{ $selectedCount }} 项</span>
            @can('finance.receivable.delete')
            <button type="button" wire:click="batchDelete" class="inline-flex items-center gap-1 rounded-md bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700 transition-colors">批量删除</button>
            @endcan
            <button type="button" wire:click="clearSelection" class="text-sm text-muted-foreground hover:text-foreground transition-colors">取消选择</button>
        @endif
    </div>

    {{-- 列表 --}}
    @php
        $allCols = collect($this->getAllColumns())
            ->filter(fn($col) => !in_array($col['key'], ['order', 'merchant']))
            ->values();
        $visibleCols = $allCols->filter(fn($col) => $this->isColumnVisible($col['key']));
        $gridCols = '40px 120px 1fr';
        foreach ($visibleCols as $col) {
            $width = $col['width'] ?? '120px';
            $gridCols .= ' ' . $width;
        }
        $gridCols .= ' 120px';
    @endphp

    <div class="rounded-lg border bg-card">
        <div class="grid gap-3 border-b px-6 py-3 text-xs font-medium text-muted-foreground uppercase tracking-wider" style="grid-template-columns: {{ $gridCols }}">
            <div><input type="checkbox" wire:model.live="selectAllPage" class="rounded" /></div>
            <div>应收单号</div>
            <div>商家</div>
            @foreach($visibleCols as $col)
                <div>{{ $col['label'] }}</div>
            @endforeach
            <div class="action-col">操作</div>
        </div>

        @forelse($items as $item)
            <div class="grid gap-3 border-b last:border-b-0 px-6 py-3 items-center hover:bg-muted/30 transition-colors"
                 style="grid-template-columns: {{ $gridCols }}"
                 wire:key="receivable-{{ $item->id }}">
                <div><input type="checkbox" value="{{ $item->id }}" wire:model.live="selectedIds" class="rounded" /></div>
                <div class="text-sm truncate"><a href="{{ route('receivables.detail', $item->id) }}" class="text-blue-600 hover:underline">{{ $item->receivable_no }}</a></div>
                <div class="text-sm text-foreground truncate">{{ $item->merchant?->name ?? '-' }}</div>
                @foreach($visibleCols as $col)
                    @switch($col['key'])
                        @case('id')
                            <div class="text-sm text-muted-foreground">{{ $item->id }}</div>
                            @break
                        @case('original_amount')
                            <div class="text-sm text-foreground text-right">{{ money_format($item->original_amount) }}</div>
                            @break
                        @case('received_amount')
                            <div class="text-sm text-foreground text-right">{{ money_format($item->received_amount) }}</div>
                            @break
                        @case('status')
                            <div class="flex items-center gap-1.5">
                                {!! status_badge($item->status, 'receivable') !!}
                                @if($item->due_date && $item->due_date < \Carbon\Carbon::today() && !in_array($item->status, [\App\Models\Receivable::STATUS_SETTLED, \App\Models\Receivable::STATUS_CLOSED]))
                                    <span class="inline-flex items-center rounded px-1 py-0.5 text-[10px] font-medium bg-red-100 text-red-700" title="到期日 {{ $item->due_date->format('Y-m-d') }}">超期</span>
                                @endif
                            </div>
                            @break
                        @case('remark')
                            <div class="text-sm text-foreground truncate max-w-[200px]">{{ $item->remark ?: '-' }}</div>
                            @break
                        @case('created_at')
                            <div class="text-sm text-foreground">{{ $item->created_at?->format('Y-m-d H:i') }}</div>
                            @break
                        @default
                            <div class="text-sm text-foreground truncate">{{ $item->{$col['key']} ?? '-' }}</div>
                    @endswitch
                @endforeach
                <div class="action-col flex items-center gap-0.5">
                    @can('finance.receivable.edit')
                    @if($item->canEdit())
                    <button type="button" wire:click="openEditModal({{ $item->id }})" class="p-1 rounded text-blue-600 hover:bg-blue-50 hover:text-blue-700 transition-colors" title="编辑"><x-ui.icon name="pencil" class="w-3.5 h-3.5" /></button>
                    @endif
                    @endcan
                    @can('finance.receivable.create')
                    @if(!in_array($item->status, [\App\Models\Receivable::STATUS_SETTLED, \App\Models\Receivable::STATUS_CLOSED]))
                    <button type="button" wire:click="openReceiveModal({{ $item->id }})" class="p-1 rounded text-green-600 hover:bg-green-50 hover:text-green-700 transition-colors" title="收款"><x-ui.icon name="banknotes" class="w-3.5 h-3.5" /></button>
                    @endif
                    @endcan
                    @can('finance.receivable.delete')
                    <button type="button" wire:click="confirmDelete({{ $item->id }})" class="p-1 rounded text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors" title="删除"><x-ui.icon name="trash" class="w-3.5 h-3.5" /></button>
                    @endcan
                </div>
            </div>
        @empty
            <div class="px-6 py-12 text-center text-sm text-muted-foreground">暂无应收数据</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $items->links() }}</div>

    {{-- 编辑弹窗（仅到期日/备注） --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4 p-6 max-h-[85vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">编辑应收</h2>
                <button type="button" wire:click="closeModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-4">
                <div class="rounded-md bg-muted p-3 text-xs text-muted-foreground">
                    <p>应收账款由系统自动生成或手动创建，仅可修改到期日和备注。</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">到期日</label>
                    <input type="date" wire:model="formDueDate" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                    @error('formDueDate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">备注</label>
                    <textarea wire:model="formRemark" rows="2" class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="可选"></textarea>
                    @error('formRemark') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="save" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">更新</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 新增应收大弹窗：商家筛选 → 候选订单多选 → 实时合计，按商家拆分生成汇总单 --}}
    @if($showCreateModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-4xl mx-4 p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">新增应收（汇总单）</h2>
                <button type="button" wire:click="closeCreateModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>

            {{-- 商家筛选 --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">按商家筛选</label>
                    <select wire:model.live="createMerchantId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="0">全部商家</option>
                        @foreach($merchants as $m)
                        <option value="{{ $m->id }}">{{ $m->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2 flex items-end">
                    <p class="text-xs text-muted-foreground leading-relaxed">候选订单为已签收且未生成过应收单的订单；勾选多个商家的订单后，系统将按商家自动拆分生成多张汇总应收单。</p>
                </div>
            </div>

            {{-- 候选订单池 --}}
            @if(count($this->candidateOrders) > 0)
            <div class="border rounded-lg overflow-hidden mb-4">
                <div class="bg-muted/30 px-4 py-2 flex items-center justify-between">
                    <span class="text-sm font-medium text-foreground">候选订单（{{ count($this->candidateOrders) }} 单）</span>
                    <div class="action-col flex items-center gap-0.5">
                        <button type="button" wire:click="selectAllCreateOrders" class="text-xs text-blue-600 hover:text-blue-700 transition-colors">全选</button>
                        <span class="text-muted-foreground">|</span>
                        <button type="button" wire:click="deselectAllCreateOrders" class="text-xs text-muted-foreground hover:text-foreground transition-colors">取消全选</button>
                    </div>
                </div>
                <div class="max-h-[45vh] overflow-y-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-muted/20 sticky top-0">
                            <tr class="text-xs text-muted-foreground uppercase tracking-wider">
                                <th class="w-10 px-3 py-2 text-left"><input type="checkbox" @if(count($createSelectedOrderIds) === count($this->candidateOrders)) checked @endif wire:click="{{ count($createSelectedOrderIds) === count($this->candidateOrders) ? 'deselectAllCreateOrders' : 'selectAllCreateOrders' }}" class="rounded" /></th>
                                <th class="px-3 py-2 text-left">订单编号</th>
                                <th class="px-3 py-2 text-left">商家</th>
                                <th class="px-3 py-2 text-left">签收/送达日期</th>
                                <th class="px-3 py-2 text-right">应收金额</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($this->candidateOrders as $order)
                            <tr class="hover:bg-muted/30 transition-colors {{ in_array($order['id'], $createSelectedOrderIds) ? 'bg-blue-50/50' : '' }}">
                                <td class="px-3 py-2"><input type="checkbox" value="{{ $order['id'] }}" @if(in_array($order['id'], $createSelectedOrderIds)) checked @endif wire:click="toggleCreateOrder({{ $order['id'] }})" class="rounded" /></td>
                                <td class="px-3 py-2 font-mono text-xs text-foreground">{{ $order['order_no'] }}</td>
                                <td class="px-3 py-2 text-foreground">{{ $order['merchant_name'] }}</td>
                                <td class="px-3 py-2 text-muted-foreground">{{ $order['delivery_date'] ?? '-' }}</td>
                                <td class="px-3 py-2 text-right text-foreground">{{ money_format($order['adjusted_amount']) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @else
            <div class="border rounded-lg p-8 text-center text-muted-foreground mb-4">
                <x-ui.icon name="inbox" class="w-8 h-8 mx-auto mb-2 text-muted-foreground/50" />
                <p class="text-sm">没有符合条件的候选订单（需已签收且未生成应收单）</p>
            </div>
            @endif

            {{-- 底部：实时合计 + 按钮 --}}
            <div class="flex items-center justify-between gap-3 pt-2 border-t">
                <div class="text-sm text-muted-foreground">
                    已选 <b class="text-foreground">{{ count($createSelectedOrderIds) }}</b> 单，
                    合计应收 <b class="text-foreground text-base">{{ money_format($this->createSelectedTotal) }}</b> 元
                    @if(count($createSelectedOrderIds) > 0)
                        @php
                            $createMerchantCount = \App\Models\Order::whereIn('id', $createSelectedOrderIds)->distinct('merchant_id')->count('merchant_id');
                        @endphp
                        · 将按商家生成 <b class="text-foreground">{{ $createMerchantCount }}</b> 张汇总单
                    @endif
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="closeCreateModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                    <button type="button" wire:click="save" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors @if(empty($createSelectedOrderIds)) opacity-50 cursor-not-allowed @endif" @if(empty($createSelectedOrderIds)) disabled @endif>
                        生成汇总单（{{ count($createSelectedOrderIds) }} 单）
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- 删除确认弹窗 --}}

    {{-- 部分收款弹窗 --}}
    @if($showReceiveModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">确认收款</h2>
                <button type="button" wire:click="closeReceiveModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">本次收款金额（元） <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="receiveAmount" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="请输入收款金额" />
                    @error('receiveAmount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">备注</label>
                    <textarea wire:model="receiveRemark" rows="2" class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="可选"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeReceiveModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="submitReceive" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">确认收款</button>
            </div>
        </div>
    </div>
    @endif

    @include('partials.column-modal')
    @include('partials.export-modal')
    @include('partials.import-modal')
    @include('partials.delete-confirm')
</div>
