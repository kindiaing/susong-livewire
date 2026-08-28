<div class="">
    {{-- 顶部：返回 + 单号 + 状态 + 操作按钮 --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('discrepancies') }}" class="text-muted-foreground hover:text-foreground transition-colors" title="返回列表">
                <x-ui.icon name="arrow-left" class="w-5 h-5" />
            </a>
            <h1 class="text-xl font-bold text-foreground">{{ $discrepancy->discrepancy_no }}</h1>
            {!! status_badge($discrepancy->status, 'discrepancy') !!}
        </div>
        <div class="flex items-center gap-2">
            @if(in_array($discrepancy->status, [1, 2, 5]) && $this->canProcess())
                <button type="button" wire:click="openProcess" class="rounded-md bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700 transition-colors">处理</button>
            @endif
            @can('delivery.discrepancy.restock')
            @if(in_array($discrepancy->type, [2, 3]) && $discrepancy->quantity_diff > 0 && in_array($discrepancy->status, [1, 2, 5]))
                <button type="button" wire:click="openRestock" class="rounded-md bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700 transition-colors">回库</button>
            @endif
            @endcan
            @if($discrepancy->status === 1 && $this->canProcess())
                <button type="button" wire:click="markProcessing" class="rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors">标记处理中</button>
            @endif
            @if(in_array($discrepancy->status, [1, 2]) && $this->canProcess())
                <button type="button" wire:click="openDispute" class="rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors">转入争议</button>
            @endif
        </div>
    </div>

    {{-- 单据摘要信息 --}}
    <div class="flex flex-wrap items-center gap-x-6 gap-y-1 text-sm text-muted-foreground mb-6">
        <span>环节：<b class="text-foreground">{{ $stageMap[$discrepancy->stage] ?? '-' }}</b></span>
        <span>类型：<b class="text-foreground">{{ $typeMap[$discrepancy->type] ?? '-' }}</b></span>
        <span>SKU：<b class="text-foreground">{{ $discrepancy->sku?->sku_name ?? $discrepancy->orderItem?->sku?->sku_name ?? '-' }}</b></span>
        @if($discrepancy->order)
            <span>关联订单：<b class="text-foreground"><a href="{{ route('orders.detail', $discrepancy->order_id) }}" class="text-blue-600 hover:text-blue-700">{{ $discrepancy->order->order_no }}</a></b></span>
        @endif
        @if($discrepancy->created_at)
            <span>创建时间：<b class="text-foreground">{{ $discrepancy->created_at->format('Y-m-d H:i') }}</b></span>
        @endif
        @if($discrepancy->handler)
            <span>处理人：<b class="text-foreground">{{ $discrepancy->handler->name }}</b></span>
        @endif
        @if($discrepancy->handled_at)
            <span>处理时间：<b class="text-foreground">{{ $discrepancy->handled_at->format('Y-m-d H:i') }}</b></span>
        @endif
    </div>

    {{-- 差异信息 --}}
    <div class="rounded-lg border bg-card mb-6">
        <div class="px-4 py-2 border-b">
            <h2 class="text-sm font-semibold text-foreground">差异信息</h2>
        </div>
        <div class="px-4 py-3 space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-muted-foreground">预期数量</span>
                <span class="text-foreground font-medium">{!! format_quantity($discrepancy->sku_id, $discrepancy->expected_quantity, $discrepancy->sku?->baseUnit?->name) !!}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-muted-foreground">实际数量</span>
                <span class="text-foreground font-medium">{!! format_quantity($discrepancy->sku_id, $discrepancy->actual_quantity, $discrepancy->sku?->baseUnit?->name) !!}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-muted-foreground">数量差异</span>
                <span class="text-red-600 font-medium">{!! format_quantity($discrepancy->sku_id, $discrepancy->quantity_diff, $discrepancy->sku?->baseUnit?->name) !!}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-muted-foreground">差异金额（估）</span>
                <span class="text-foreground font-medium">{{ money_format($discrepancy->amount_diff) }}</span>
            </div>
            @if($discrepancy->reason)
            <div class="flex justify-between">
                <span class="text-muted-foreground">差异原因</span>
                <span class="text-foreground">{{ $discrepancy->reason }}</span>
            </div>
            @endif
        </div>
    </div>

    {{-- 处理信息 --}}
    <div class="rounded-lg border bg-card mb-6">
        <div class="px-4 py-2 border-b">
            <h2 class="text-sm font-semibold text-foreground">处理信息</h2>
        </div>
        <div class="px-4 py-3 space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-muted-foreground">责任方</span>
                <span class="text-foreground font-medium">{{ $partyMap[$discrepancy->responsible_party] ?? '-' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-muted-foreground">处理决策</span>
                <span class="text-foreground font-medium">{{ $decisionMap[$discrepancy->decision] ?? '-' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-muted-foreground">审核状态</span>
                <span class="text-foreground font-medium">{{ $discrepancy->approval_status_label }}</span>
            </div>
            @if($discrepancy->remark)
            <div class="flex justify-between">
                <span class="text-muted-foreground">备注</span>
                <span class="text-foreground">{{ $discrepancy->remark }}</span>
            </div>
            @endif
        </div>
    </div>

    {{-- 关联补货提醒 --}}
    @if($discrepancy->restockReminders->isNotEmpty())
    <div class="rounded-lg border bg-card mb-6">
        <div class="px-4 py-2 border-b">
            <h2 class="text-sm font-semibold text-foreground">关联补货提醒</h2>
        </div>
        <div class="divide-y">
            @foreach($discrepancy->restockReminders as $reminder)
            <div class="px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    <div>
                        <div class="text-sm text-foreground">
                            <a href="{{ route('restock-reminders') }}" class="text-blue-600 hover:underline">补货提醒 #{{ $reminder->id }}</a>
                            · {{ \App\Models\RestockReminder::remindCycleMap()[$reminder->remind_cycle] ?? '-' }}
                        </div>
                        <div class="text-xs text-muted-foreground mt-0.5">
                            阈值 {!! format_quantity($reminder->sku_id, $reminder->threshold_quantity) !!} · {{ $reminder->remark ?? '-' }}
                        </div>
                    </div>
                </div>
                <span class="text-sm">{!! status_badge($reminder->status, 'active') !!}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- 处理弹窗 --}}
    @if($showProcessModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">处理差异单</h2>
                <button type="button" wire:click="closeProcessModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">责任方 <span class="text-red-500">*</span></label>
                    <select wire:model="processResponsibleParty" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        @foreach($partyMap as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">处理决策 <span class="text-red-500">*</span></label>
                    <select wire:model.live="processDecision" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="1">补货（自动生成补货提醒）</option>
                        <option value="2">退款（待审核通过后回写应收）</option>
                        <option value="3">扣款（待审核通过后回写应收）</option>
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

    {{-- 回库弹窗 --}}
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
            <p class="text-sm text-muted-foreground mb-4">确认货品已带回仓库且可再售，差异数量将回补到所选仓库库存并留痕；不可再售的请走「处理 → 报损」。@if(\App\Models\ApprovalTypeConfig::isApprovalRequired('diff_restock'))<br /><span class="text-orange-600">当前回库审核已开启，提交后将进入待审核状态，审核通过后才执行库存回补。</span>@endif</p>
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

    {{-- 争议弹窗 --}}
    @if($showDisputeModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">转入争议</h2>
                <button type="button" wire:click="closeDisputeModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-4">
                <div class="text-sm text-muted-foreground">标记为争议中后，该差异单暂不参与处理，需核实责任与金额后再处理。</div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">争议说明</label>
                    <textarea wire:model="disputeRemark" rows="2" maxlength="255" class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="可选"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeDisputeModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="markDisputed" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">确认转入争议</button>
            </div>
        </div>
    </div>
    @endif
</div>