<div class="">
    {{-- 页面标题 --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">商家管理</h1>
            <p class="text-muted-foreground mt-1">管理商家信息及结算方式</p>
        </div>
        @can('org.merchant.create')
        <button type="button" wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            <x-ui.icon name="plus" class="w-4 h-4" />
            新增商家
        </button>
        @endcan
    </div>

    {{-- 搜索栏 --}}
    <div class="flex items-center gap-3 mb-4">
        <div x-data class="relative">
            <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background pl-3 pr-8 text-sm" placeholder="搜索商家名称/联系人/电话..." />
            @if($search)
                <button type="button" wire:click="resetFilters" class="absolute right-2 top-1/2 -translate-y-1/2 p-0.5 rounded-sm text-muted-foreground/50 hover:text-muted-foreground hover:bg-muted transition-colors">
                    <x-ui.icon name="x-mark" class="w-3.5 h-3.5" />
                </button>
            @endif
        </div>
        <select
            wire:model.live="filterStatus"
            class="flex h-9 w-32 rounded-md border border-input bg-background px-3 text-sm"
        >
            <option value="">全部状态</option>
            <option value="1">启用</option>
            <option value="0">禁用</option>
        </select>
        <select
            wire:model.live="filterSettlementType"
            class="flex h-9 w-32 rounded-md border border-input bg-background px-3 text-sm"
        >
            <option value="">全部结算</option>
            <option value="1">现结</option>
            <option value="2">账期</option>
            <option value="3">预付款</option>
        </select>
        <div class="flex-1"></div>
        <button type="button" wire:click="openColumnModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="adjustments" class="w-4 h-4" />列配置</button>
        <button type="button" wire:click="openImportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-down-tray" class="w-4 h-4" />导入</button>
        <button type="button" wire:click="openExportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-up-tray" class="w-4 h-4" />导出</button>
        @if($selectedCount > 0)
            <span class="text-sm text-muted-foreground">已选 {{ $selectedCount }} 项</span>
            @can('org.merchant.delete')
            <button type="button" wire:click="batchDelete" class="inline-flex items-center gap-1 rounded-md bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700 transition-colors">批量删除</button>
            @endcan
            <button type="button" wire:click="clearSelection" class="text-sm text-muted-foreground hover:text-foreground transition-colors">取消选择</button>
        @endif
    </div>

    {{-- 商家列表 --}}
    @php
        $allCols = collect($this->getAllColumns())
            ->filter(fn($col) => $col['key'] !== 'name')
            ->values();
        $visibleCols = $allCols->filter(fn($col) => $this->isColumnVisible($col['key']));
        $gridCols = '40px 1fr';
        foreach ($visibleCols as $col) {
            $width = $col['width'] ?? '120px';
            $gridCols .= ' ' . $width;
        }
        $gridCols .= ' 100px';
    @endphp

    <div class="rounded-lg border bg-card overflow-x-auto">
        <div class="grid gap-3 border-b px-6 py-3 text-xs font-medium text-muted-foreground uppercase tracking-wider min-w-[900px]" style="grid-template-columns: {{ $gridCols }}">
            <div><input type="checkbox" wire:model.live="selectAll" class="rounded" /></div>
            <div>商家名称</div>
            @foreach($visibleCols as $col)
                <div>{{ $col['label'] }}</div>
            @endforeach
            <div>操作</div>
        </div>

        @forelse($merchants as $merchant)
            <div class="grid gap-3 border-b last:border-b-0 px-6 py-3 items-center hover:bg-muted/30 transition-colors min-w-[900px]"
                 style="grid-template-columns: {{ $gridCols }}"
                 wire:key="merchant-{{ $merchant->id }}">
                <div><input type="checkbox" value="{{ $merchant->id }}" wire:model.live="selectedIds" class="rounded" /></div>
                <div class="text-sm font-medium text-foreground truncate">{{ $merchant->name }}</div>
                @foreach($visibleCols as $col)
                    @switch($col['key'])
                        @case('id')
                            <div class="text-sm text-muted-foreground">{{ $merchant->id }}</div>
                            @break
                        @case('contact_name')
                            <div class="text-sm text-foreground">{{ $merchant->contact_name ?? '-' }}</div>
                            @break
                        @case('contact_phone')
                            <div class="text-sm text-foreground">{{ $merchant->contact_phone ?? '-' }}</div>
                            @break
                        @case('settlement_type')
                            <div class="text-sm text-foreground">{{ \App\Models\Merchant::settlementTypeMap()[$merchant->settlement_type] ?? '-' }}</div>
                            @break
                        @case('min_order_amount')
                            <div class="text-sm text-foreground">{{ money_format($merchant->min_order_amount) }}</div>
                            @break
                        @case('credit_limit')
                            <div class="text-sm text-foreground">{{ money_format($merchant->credit_limit) }}</div>
                            @break
                        @case('status')
                            <div>
                                {!! status_badge($merchant->status, 'active') !!}
                            </div>
                            @break
                        @case('address')
                            <div class="text-sm text-foreground truncate">{{ $merchant->address ?? '-' }}</div>
                            @break
                        @case('latitude')
                            <div class="text-sm text-foreground">{{ $merchant->latitude ?? '-' }}</div>
                            @break
                        @case('longitude')
                            <div class="text-sm text-foreground">{{ $merchant->longitude ?? '-' }}</div>
                            @break
                        @case('created_at')
                            <div class="text-sm text-foreground">{{ $merchant->created_at?->format('Y-m-d H:i') }}</div>
                            @break
                        @default
                            <div class="text-sm text-foreground truncate">{{ $merchant->{$col['key']} ?? '-' }}</div>
                    @endswitch
                @endforeach
                <div class="flex items-center gap-2">
                    <button type="button" wire:click="openDetailModal({{ $merchant->id }})" class="p-1 rounded text-blue-600 hover:bg-blue-50 hover:text-blue-700 transition-colors" title="详情"><x-ui.icon name="eye" class="w-3.5 h-3.5" /></button>
                    @can('org.merchant.edit')
                    <button type="button" wire:click="openEditModal({{ $merchant->id }})" class="p-1 rounded text-blue-600 hover:bg-blue-50 hover:text-blue-700 transition-colors" title="编辑"><x-ui.icon name="pencil-square" class="w-3.5 h-3.5" /></button>
                    @endcan
                    @can('org.merchant.delete')
                    <button type="button" wire:click="confirmDelete({{ $merchant->id }})" class="p-1 rounded text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors" title="删除"><x-ui.icon name="trash" class="w-3.5 h-3.5" /></button>
                    @endcan
                </div>
            </div>
        @empty
            <div class="px-6 py-12 text-center text-sm text-muted-foreground">暂无商家数据</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $merchants->links() }}</div>

    {{-- 新增/编辑弹窗 --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4 p-6 max-h-[85vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">{{ $editingId ? '编辑商家' : '新增商家' }}</h2>
                <button type="button" wire:click="closeModal" class="text-muted-foreground hover:text-foreground transition-colors"><x-ui.icon name="x-mark" class="w-5 h-5" /></button>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">商家名称 <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="formName" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="请输入商家名称" />
                    @error('formName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">联系人 <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="formContactName" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="联系人姓名" />
                        @error('formContactName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">联系电话 <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="formContactPhone" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="联系电话" />
                        @error('formContactPhone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">地址 <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="formAddress" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="详细地址" />
                    @error('formAddress') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">纬度</label>
                        <input type="text" wire:model="formLatitude" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="如 39.908823" />
                        @error('formLatitude') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">经度</label>
                        <input type="text" wire:model="formLongitude" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="如 116.397470" />
                        @error('formLongitude') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">起送金额（元） <span class="text-red-500">*</span></label>
                        <input type="number" wire:model="formMinOrderAmount" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" min="0" step="0.01" placeholder="0.00" />
                        @error('formMinOrderAmount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">信用额度（元） <span class="text-red-500">*</span></label>
                        <input type="number" wire:model="formCreditLimit" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" min="0" step="0.01" placeholder="0.00" />
                        @error('formCreditLimit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">结算方式 <span class="text-red-500">*</span></label>
                        <select wire:model="formSettlementType" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="1">现结</option>
                            <option value="2">账期</option>
                            <option value="3">预付款</option>
                        </select>
                        @error('formSettlementType') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">状态 <span class="text-red-500">*</span></label>
                        <select wire:model="formStatus" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="1">启用</option>
                            <option value="0">禁用</option>
                        </select>
                        @error('formStatus') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">备注</label>
                    <textarea wire:model="formRemark" rows="2" class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="可选"></textarea>
                    @error('formRemark') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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

    {{-- 详情弹窗 --}}
    @if($showDetailModal && $detailItem)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true" wire:click="showDetailModal = false"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4 p-6 max-h-[85vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">商家详情</h2>
                <button type="button" wire:click="showDetailModal = false" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-3 text-sm">
                <div class="flex"><span class="w-24 text-muted-foreground">名称</span><span class="text-foreground">{{ $detailItem->name }}</span></div>
                <div class="flex"><span class="w-24 text-muted-foreground">联系人</span><span class="text-foreground">{{ $detailItem->contact_name ?? '-' }}</span></div>
                <div class="flex"><span class="w-24 text-muted-foreground">联系电话</span><span class="text-foreground">{{ $detailItem->contact_phone ?? '-' }}</span></div>
                <div class="flex"><span class="w-24 text-muted-foreground">地址</span><span class="text-foreground">{{ $detailItem->address ?? '-' }}</span></div>
                <div class="flex"><span class="w-24 text-muted-foreground">结算方式</span><span class="text-foreground">{{ \App\Models\Merchant::settlementTypeMap()[$detailItem->settlement_type] ?? '-' }}</span></div>
                <div class="flex"><span class="w-24 text-muted-foreground">起送金额</span><span class="text-foreground">{{ money_format($detailItem->min_order_amount) }}</span></div>
                <div class="flex"><span class="w-24 text-muted-foreground">信用额度</span><span class="text-foreground">{{ money_format($detailItem->credit_limit) }}</span></div>
                <div class="flex"><span class="w-24 text-muted-foreground">状态</span><span class="text-foreground">{{ $detailItem->status ? '启用' : '禁用' }}</span></div>
                <div class="flex"><span class="w-24 text-muted-foreground">备注</span><span class="text-foreground">{{ $detailItem->remark ?: '-' }}</span></div>
                <div class="flex"><span class="w-24 text-muted-foreground">创建时间</span><span class="text-foreground">{{ $detailItem->created_at?->format('Y-m-d H:i') }}</span></div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="showDetailModal = false" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">关闭</button>
            </div>
        </div>
    </div>
    @endif

    @include('partials.column-modal')
    @include('partials.export-modal')
    @include('partials.import-modal')
    @include('partials.delete-confirm')
</div>
