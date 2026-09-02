<div class="" x-data="{ activeTab: 'items' }">
    {{-- 顶部：返回 + 单号 + 状态 + 操作按钮 --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('loss-orders') }}" class="text-muted-foreground hover:text-foreground transition-colors" title="返回列表">
                <x-ui.icon name="arrow-left" class="w-5 h-5" />
            </a>
            <h1 class="text-xl font-bold text-foreground">{{ $lossOrder->loss_no }}</h1>
            {!! status_badge($lossOrder->status, 'loss_status') !!}
            {!! status_badge($lossOrder->approval_status, 'loss_approval') !!}
        </div>
        <div class="flex items-center gap-2">
            @can('loss.loss-order.edit')
            @if($lossOrder->approval_status === \App\Models\LossOrder::APPROVAL_PENDING)
                <button type="button" wire:click="confirmApprove" class="rounded-md bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700 transition-colors">审核</button>
            @endif
            @if($lossOrder->status === \App\Models\LossOrder::STATUS_APPROVED && $lossOrder->source_type !== 'purchase_order')
                <button type="button" wire:click="execute" class="rounded-md bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700 transition-colors">执行</button>
            @endif
            @if($lossOrder->status === \App\Models\LossOrder::STATUS_EXECUTED || ($lossOrder->status === \App\Models\LossOrder::STATUS_APPROVED && $lossOrder->source_type === 'purchase_order'))
                <button type="button" wire:click="confirmClose" class="rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors" title="{{ $lossOrder->source_type === 'purchase_order' && $lossOrder->status === 2 ? '采购入库差异仅账务留痕不扣库存，审核后可直接关闭' : '' }}">关闭</button>
            @endif
            @if($lossOrder->canEdit())
                <a href="{{ route('loss-orders') }}?edit={{ $lossOrder->id }}" class="rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors">编辑单据</a>
            @endif
            @endcan
        </div>
    </div>

    {{-- 单据摘要信息 --}}
    <div class="flex flex-wrap items-center gap-x-6 gap-y-1 text-sm text-muted-foreground mb-6">
        <span>仓库：<b class="text-foreground">{{ $lossOrder->warehouse?->name ?? '-' }}</b></span>
        <span>损耗类型：<b class="text-foreground">{{ $typeMap[$lossOrder->loss_type] ?? '-' }}</b></span>
        <span>来源：<b class="text-foreground">{{ $lossOrder->source_type_label }}</b></span>
        @if($lossOrder->applicant)
            <span>申请人：<b class="text-foreground">{{ $lossOrder->applicant->name }}</b></span>
        @endif
        @if($lossOrder->reviewer)
            <span>审核人：<b class="text-foreground">{{ $lossOrder->reviewer->name }}</b></span>
        @endif
        @if($lossOrder->reviewed_at)
            <span>审核时间：<b class="text-foreground">{{ $lossOrder->reviewed_at->format('Y-m-d H:i') }}</b></span>
        @endif
        @if($lossOrder->executed_at)
            <span>执行时间：<b class="text-foreground">{{ $lossOrder->executed_at->format('Y-m-d H:i') }}</b></span>
        @endif
        @if($lossOrder->closed_at)
            <span>关闭时间：<b class="text-foreground">{{ $lossOrder->closed_at->format('Y-m-d H:i') }}</b></span>
        @endif
    </div>

    {{-- Tab 切换 --}}
    <div class="border-b mb-4">
        <nav class="flex gap-6">
            <button type="button" @click="activeTab = 'items'" :class="activeTab === 'items' ? 'border-blue-600 text-foreground' : 'border-transparent text-muted-foreground hover:text-foreground'" class="pb-2 text-sm font-medium border-b-2 transition-colors">损耗明细</button>
            <button type="button" @click="activeTab = 'logs'" :class="activeTab === 'logs' ? 'border-blue-600 text-foreground' : 'border-transparent text-muted-foreground hover:text-foreground'" class="pb-2 text-sm font-medium border-b-2 transition-colors">
                状态变更记录
                @if($lossOrder->auditLogs->isNotEmpty())
                    <span class="ml-1.5 inline-flex items-center justify-center rounded-full bg-muted text-muted-foreground text-[10px] font-medium px-1.5 py-0.5 leading-none">{{ $lossOrder->auditLogs->count() }}</span>
                @endif
            </button>
        </nav>
    </div>

    {{-- Tab 内容：损耗明细 --}}
    <div x-show="activeTab === 'items'" x-transition>
    {{-- 金额信息 --}}
    <div class="rounded-lg border bg-card mb-6">
        <div class="px-4 py-2 border-b">
            <h2 class="text-sm font-semibold text-foreground">损耗金额</h2>
        </div>
        <div class="px-4 py-3 space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-muted-foreground">损耗总金额</span>
                <span class="text-foreground font-bold text-base">{{ money_format($lossOrder->total_amount) }}</span>
            </div>
            @if($lossOrder->reason)
            <div class="flex justify-between">
                <span class="text-muted-foreground">损耗原因</span>
                <span class="text-foreground text-right max-w-[400px]">{{ $lossOrder->reason }}</span>
            </div>
            @endif
            @if($lossOrder->remark)
            <div class="flex justify-between">
                <span class="text-muted-foreground">备注</span>
                <span class="text-foreground text-right max-w-[400px]">{{ $lossOrder->remark }}</span>
            </div>
            @endif
        </div>
    </div>

    {{-- 损耗明细 --}}
    <div class="rounded-lg border bg-card mb-6">
        <div class="px-4 py-2 border-b flex items-center justify-between">
            <h2 class="text-sm font-semibold text-foreground">损耗明细（{{ $lossOrder->items->count() }} 项）</h2>
            @if($canEditItems)
            <div class="flex items-center gap-2">
                <button type="button" wire:click="openImportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-2.5 py-1 text-xs hover:bg-accent transition-colors">
                    <x-ui.icon name="arrow-down-tray" class="w-3.5 h-3.5" />导入
                </button>
                <button type="button" wire:click="openExportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-2.5 py-1 text-xs hover:bg-accent transition-colors">
                    <x-ui.icon name="arrow-up-tray" class="w-3.5 h-3.5" />导出
                </button>
                <button type="button" wire:click="openAddItemModal" class="inline-flex items-center gap-1 rounded-md bg-blue-600 px-2.5 py-1 text-xs font-medium text-white hover:bg-blue-700 transition-colors">
                    <x-ui.icon name="plus" class="w-3.5 h-3.5" />添加明细
                </button>
            </div>
            @endif
        </div>
        @if($lossOrder->items->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                        <th class="px-4 py-2 text-left">SKU</th>
                        <th class="px-4 py-2 text-left">损耗类型</th>
                        <th class="px-4 py-2 text-right">数量</th>
                        <th class="px-4 py-2 text-right">成本价</th>
                        <th class="px-4 py-2 text-right">损耗金额</th>
                        <th class="px-4 py-2 text-left">责任方</th>
                        <th class="px-4 py-2 text-left">原因</th>
                        @if($canEditItems)<th class="px-4 py-2 text-right w-20">操作</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($lossOrder->items as $item)
                    <tr class="hover:bg-muted/30 transition-colors" wire:key="loss-item-{{ $item->id }}">
                        <td class="px-4 py-2 text-foreground">{{ $item->sku?->name ?? "SKU-{$item->sku_id}" }}</td>
                        <td class="px-4 py-2 text-foreground">{{ $typeMap[$item->loss_type] ?? '-' }}</td>
                        <td class="px-4 py-2 text-right text-foreground">{!! format_quantity($item->sku_id, $item->quantity, $item->sku?->baseUnit?->name) !!}</td>
                        <td class="px-4 py-2 text-right text-foreground">{{ money_format($item->cost_price) }}</td>
                        <td class="px-4 py-2 text-right text-foreground font-medium">{{ money_format($item->loss_amount) }}</td>
                        <td class="px-4 py-2 text-foreground">{{ $responsiblePartyMap[$item->responsible_party] ?? '-' }}</td>
                        <td class="px-4 py-2 text-muted-foreground truncate max-w-[200px]">{{ $item->reason ?? '-' }}</td>
                        @if($canEditItems)
                        <td class="px-4 py-2">
                            <div class="flex items-center justify-end gap-0.5">
                                <button type="button" wire:click="openEditItemModal({{ $item->id }})" class="p-1 rounded text-blue-600 hover:bg-blue-50 hover:text-blue-700 transition-colors" title="编辑">
                                    <x-ui.icon name="pencil-square" class="w-3.5 h-3.5" />
                                </button>
                                <button type="button" wire:click="deleteItem({{ $item->id }})" wire:confirm="确认删除此条明细？" class="p-1 rounded text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors" title="删除">
                                    <x-ui.icon name="trash" class="w-3.5 h-3.5" />
                                </button>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 font-medium">
                        <td colspan="4" class="px-4 py-2 text-right text-muted-foreground">合计</td>
                        <td class="px-4 py-2 text-right text-foreground font-bold">{{ money_format($lossOrder->total_amount) }}</td>
                        @if($canEditItems)<td colspan="2"></td>@else<td colspan="2"></td>@endif
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="px-4 py-12 text-center">
            <p class="text-sm text-muted-foreground mb-3">暂无损耗明细</p>
            @if($canEditItems)
            <div class="flex items-center justify-center gap-2">
                <button type="button" wire:click="openAddItemModal" class="inline-flex items-center gap-1 rounded-md bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                    <x-ui.icon name="plus" class="w-4 h-4" />添加明细
                </button>
                <button type="button" wire:click="openImportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors">
                    <x-ui.icon name="arrow-down-tray" class="w-4 h-4" />批量导入
                </button>
            </div>
            @endif
        </div>
        @endif
    </div>

    {{-- Tab 内容：状态变更记录 --}}
    <div x-show="activeTab === 'logs'" x-transition>
        @if($lossOrder->auditLogs->isNotEmpty())
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
                    @foreach($lossOrder->auditLogs->sortByDesc('created_at') as $log)
                    <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors">
                        <td class="px-4 py-2 text-muted-foreground tabular-nums">{{ $log->created_at?->format('Y-m-d H:i:s') }}</td>
                        <td class="px-4 py-2">
                            <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium
                                @if($log->action === 'cancel') bg-red-100 text-red-700
                                @elseif($log->action === 'rollback') bg-purple-100 text-purple-700
                                @elseif($log->action === 'status_change') bg-blue-100 text-blue-700
                                @elseif($log->action === 'create') bg-green-100 text-green-700
                                @elseif($log->action === 'approve') bg-green-100 text-green-700
                                @elseif($log->action === 'reject') bg-red-100 text-red-700
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
                                {!! status_badge($beforeStatus, 'loss_status') !!}
                                <x-ui.icon name="arrow-right" class="w-3.5 h-3.5 inline text-muted-foreground mx-1" />
                                {!! status_badge($afterStatus, 'loss_status') !!}
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

    {{-- 明细新增/编辑弹窗 --}}
    @if($showItemModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">{{ $editingItemId ? '编辑明细' : '添加明细' }}</h2>
                <button type="button" wire:click="closeItemModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">SKU <span class="text-red-500">*</span></label>
                    <select wire:model.live="itemSkuId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="0">请选择 SKU</option>
                        @foreach($skus as $sku)
                        <option value="{{ $sku->id }}">{{ $sku->product?->name ?? 'SKU' }}（{{ $sku->sku_code }}）</option>
                        @endforeach
                    </select>
                    @error('itemSkuId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">成本价（元） <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="itemCostPriceYuan" placeholder="0.00" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                        @error('itemCostPriceYuan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">数量 <span class="text-red-500">*</span></label>
                        <input type="number" wire:model="itemQuantity" min="1" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                        @error('itemQuantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">责任方 <span class="text-red-500">*</span></label>
                        <select wire:model.live="itemResponsibleParty" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="1">平台</option>
                            <option value="2">供应商</option>
                        </select>
                    </div>
                    <div>
                        @if($itemResponsibleParty == 2)
                        <label class="block text-sm font-medium text-foreground mb-1">供应商</label>
                        <select wire:model="itemSupplierId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="0">请选择供应商</option>
                            @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                        @else
                        <label class="block text-sm font-medium text-foreground mb-1">供应商</label>
                        <div class="flex h-9 items-center text-sm text-muted-foreground">—</div>
                        @endif
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">原因</label>
                    <input type="text" wire:model="itemReason" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="可选" />
                </div>
                @php
                    $previewAmount = (float) ($itemCostPriceYuan ?: 0) * (int) ($itemQuantity ?: 0);
                @endphp
                @if($previewAmount > 0)
                <div class="rounded-md border border-blue-100 bg-blue-50/50 px-3 py-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">损耗金额预览</span>
                        <span class="text-foreground font-bold">{{ number_format($previewAmount, 2) }} 元</span>
                    </div>
                </div>
                @endif
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeItemModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="saveItem" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">{{ $editingItemId ? '保存' : '添加' }}</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 明细导出弹窗 --}}
    @if($showExportModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">导出损耗明细</h2>
                <button type="button" wire:click="closeExportModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-3 mb-4">
                @foreach($itemExportColumns as $key => $label)
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" value="{{ $key }}" wire:click="toggleExportColumn('{{ $key }}')" @checked(in_array($key, $exportColumns)) class="rounded" />
                    {{ $label }}
                </label>
                @endforeach
            </div>
            <div class="flex items-center gap-4 mb-4">
                <label class="flex items-center gap-2 text-sm">
                    <input type="radio" wire:model="exportFormat" value="xlsx" class="rounded" /> xlsx
                </label>
                <label class="flex items-center gap-2 text-sm">
                    <input type="radio" wire:model="exportFormat" value="csv" class="rounded" /> csv
                </label>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="closeExportModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="doExport" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">导出</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 明细导入弹窗 --}}
    @if($showImportModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">导入损耗明细</h2>
                <button type="button" wire:click="closeImportModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-4">
                <div class="rounded-md border border-blue-100 bg-blue-50/50 p-3">
                    <div class="flex items-start gap-2">
                        <x-ui.icon name="information-circle" class="w-4 h-4 text-blue-600 mt-0.5 shrink-0" />
                        <div class="text-xs text-blue-800 space-y-1">
                            <p>必填列：SKU编码、数量、成本价</p>
                            <p>成本价单位为元（如 12.50），导入后自动转厘</p>
                            <p>责任方填"平台"或"供应商"，供应商责任方需填写供应商名称</p>
                        </div>
                    </div>
                </div>
                <div>
                    <button type="button" wire:click="downloadImportTemplate" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors">
                        <x-ui.icon name="arrow-down-tray" class="w-4 h-4" />下载导入模板
                    </button>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">选择文件</label>
                    <input type="file" wire:model="importFile" accept=".xlsx,.xls,.csv" class="block w-full text-sm" />
                    @error('importFile') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                @if($importMessage)
                <div class="rounded-md border px-3 py-2 text-sm {{ !empty($importErrors) ? 'border-red-200 bg-red-50 text-red-800' : 'border-green-200 bg-green-50 text-green-800' }}">
                    {{ $importMessage }}
                </div>
                @endif
                @if(!empty($importErrors))
                <div class="max-h-40 overflow-y-auto rounded-md border border-red-200 bg-red-50 p-2">
                    @foreach($importErrors as $err)
                    <p class="text-xs text-red-700">第 {{ $err['row'] }} 行：{{ $err['error'] }}</p>
                    @endforeach
                </div>
                @endif
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeImportModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="doImport" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">开始导入</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 审核弹窗 --}}
    @if($showApproveConfirm)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-sm mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">审核损耗单</h2>
                <button type="button" wire:click="closeApproveConfirm" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <p class="text-sm text-muted-foreground mb-6">请选择审核结果</p>
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="closeApproveConfirm" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="reject" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">拒绝</button>
                <button type="button" wire:click="approve" class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition-colors">通过</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 关闭确认弹窗 --}}
    @if($showCloseConfirm)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-sm mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">确认关闭损耗单</h2>
                <button type="button" wire:click="closeCloseConfirm" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <p class="text-sm text-muted-foreground mb-6">关闭后单据将无法再进行任何操作（不可编辑、执行或回退状态），请确认是否继续？</p>
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="closeCloseConfirm" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">再想想</button>
                <button type="button" wire:click="close" class="rounded-md bg-orange-600 px-4 py-2 text-sm font-medium text-white hover:bg-orange-700 transition-colors">确认关闭</button>
            </div>
        </div>
    </div>
    @endif
</div>
