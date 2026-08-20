<div class="">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">可见性配置</h1>
            <p class="text-muted-foreground mt-1">管理商家对商品/SKU的可见性配置（SKU级优先于商品级）</p>
        </div>
        @can('product.visibility.create')
        <button type="button" wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            <x-ui.icon name="plus" class="w-4 h-4" />
            新增配置
        </button>
        @endcan
    </div>

    {{-- 筛选区 --}}
    <div class="flex flex-wrap items-center gap-3 mb-4">
        <div x-data class="relative">
            <input type="text" wire:model.live="searchMerchant" class="flex h-9 w-48 rounded-md border border-input bg-background pl-3 pr-8 text-sm" placeholder="搜索商家名称..." />
            @if($searchMerchant)
                <button type="button" wire:click="$set('searchMerchant','')" class="absolute right-2 top-1/2 -translate-y-1/2 p-0.5 rounded-sm text-muted-foreground/50 hover:text-muted-foreground hover:bg-muted transition-colors">
                    <x-ui.icon name="x-mark" class="w-3.5 h-3.5" />
                </button>
            @endif
        </div>
        <div x-data class="relative">
            <input type="text" wire:model.live="searchTarget" class="flex h-9 w-48 rounded-md border border-input bg-background pl-3 pr-8 text-sm" placeholder="搜索商品/SKU名称..." />
            @if($searchTarget)
                <button type="button" wire:click="$set('searchTarget','')" class="absolute right-2 top-1/2 -translate-y-1/2 p-0.5 rounded-sm text-muted-foreground/50 hover:text-muted-foreground hover:bg-muted transition-colors">
                    <x-ui.icon name="x-mark" class="w-3.5 h-3.5" />
                </button>
            @endif
        </div>
        <select wire:model.live="filterMerchantId" class="flex h-9 w-44 rounded-md border border-input bg-background px-3 text-sm">
            <option value="">全部商家</option>
            @foreach($merchants as $merchant)
                <option value="{{ $merchant->id }}">{{ $merchant->name }}</option>
            @endforeach
        </select>
        <select wire:model.live="filterTargetType" class="flex h-9 w-36 rounded-md border border-input bg-background px-3 text-sm">
            <option value="">全部类型</option>
            <option value="product">商品级</option>
            <option value="sku">SKU级</option>
        </select>
        {{-- 日期范围筛选 --}}
        <input type="date" wire:model.live="filterDateStart" class="flex h-9 rounded-md border border-input bg-background px-3 text-sm" title="开始日期" />
        <span class="text-muted-foreground text-sm">~</span>
        <input type="date" wire:model.live="filterDateEnd" class="flex h-9 rounded-md border border-input bg-background px-3 text-sm" title="结束日期" />
        @if($filterDateStart || $filterDateEnd)
            <button type="button" wire:click="$set('filterDateStart',''); $set('filterDateEnd','')" class="p-1 rounded text-muted-foreground hover:text-foreground hover:bg-muted transition-colors" title="清除日期">
                <x-ui.icon name="x-mark" class="w-3.5 h-3.5" />
            </button>
        @endif
        <div class="flex-1"></div>
        <button type="button" wire:click="openColumnModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="adjustments" class="w-4 h-4" />列配置</button>
        <button type="button" wire:click="openExportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-up-tray" class="w-4 h-4" />导出</button>
        <button type="button" wire:click="openImportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-down-tray" class="w-4 h-4" />导入</button>
        @if($selectedCount > 0)
            <span class="text-sm text-muted-foreground">已选 {{ $selectedCount }} 项</span>
            @can('product.visibility.delete')
            <button type="button" wire:click="batchDelete" class="inline-flex items-center gap-1 rounded-md bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700 transition-colors">批量删除</button>
            @endcan
            <button type="button" wire:click="clearSelection" class="text-sm text-muted-foreground hover:text-foreground transition-colors">取消选择</button>
        @endif
    </div>

    @php
        $allCols = collect($this->getAllColumns());
        $visibleCols = $allCols->filter(fn($col) => $this->isColumnVisible($col['key']))->values();
        $colspan = $visibleCols->count() + 2;
    @endphp

    {{-- 列表 --}}
    <div class="rounded-lg border bg-card">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                    <th class="px-4 py-2 text-left w-10"><input type="checkbox" wire:model.live="selectAll" class="rounded" /></th>
                    @foreach($visibleCols as $col)
                    <th class="px-4 py-2 text-left">{{ $col['label'] }}</th>
                    @endforeach
                    <th class="px-4 py-2 text-left w-28">操作</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $record)
                <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="visibility-{{ $record->id }}">
                    <td class="px-4 py-2"><input type="checkbox" value="{{ $record->id }}" wire:model.live="selectedIds" class="rounded" /></td>
                    @foreach($visibleCols as $col)
                    @switch($col['key'])
                        @case('id')
                            <td class="px-4 py-2 text-muted-foreground">{{ $record->id }}</td>
                            @break
                        @case('merchant_id')
                            <td class="px-4 py-2 font-medium text-foreground">{{ $record->merchant?->name ?? '-' }}</td>
                            @break
                        @case('target_type')
                            <td class="px-4 py-2">{!! status_badge($record->target_type, 'target_type') !!}</td>
                            @break
                        @case('product_id')
                            <td class="px-4 py-2 text-foreground">{{ $record->product?->name ?? '-' }}</td>
                            @break
                        @case('sku_id')
                            <td class="px-4 py-2 text-muted-foreground font-mono">{{ $record->sku?->sku_code ?? '-' }}</td>
                            @break
                        @case('is_visible')
                            <td class="px-4 py-2">
                                <button type="button" wire:click="toggleVisibility({{ $record->id }})" class="inline-flex items-center gap-1.5 cursor-pointer">
                                    @if($record->is_visible)
                                        <span class="w-4 h-4 rounded-full bg-green-500 inline-block"></span>
                                        <span class="text-xs text-green-700">可见</span>
                                    @else
                                        <span class="w-4 h-4 rounded-full bg-gray-300 inline-block"></span>
                                        <span class="text-xs text-gray-500">不可见</span>
                                    @endif
                                </button>
                            </td>
                            @break
                        @case('created_at')
                            <td class="px-4 py-2 text-muted-foreground text-xs">{{ $record->created_at?->format('Y-m-d H:i') }}</td>
                            @break
                        @default
                            <td class="px-4 py-2 text-foreground">{{ $record->{$col['key']} ?? '-' }}</td>
                    @endswitch
                    @endforeach
                    <td class="px-4 py-2">
                        <div class="inline-flex items-center gap-0.5">
                            @can('product.visibility.edit')
                            <button type="button" wire:click="openEditModal({{ $record->id }})" class="p-1 rounded text-blue-600 hover:bg-blue-50 hover:text-blue-700 transition-colors" title="编辑"><x-ui.icon name="pencil-square" class="w-3.5 h-3.5" /></button>
                            @endcan
                            @can('product.visibility.delete')
                            <button type="button" wire:click="confirmDelete({{ $record->id }})" class="p-1 rounded text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors" title="删除"><x-ui.icon name="trash" class="w-3.5 h-3.5" /></button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="{{ $colspan }}" class="px-6 py-12 text-center text-muted-foreground">暂无可见性配置数据</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $records->links() }}</div>

    {{-- 新增/编辑弹窗 --}}
    @if($showCreateModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">{{ $editingId ? '编辑可见性配置' : '新增可见性配置' }}</h2>
                <button type="button" wire:click="closeCreateModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-4">
                {{-- 商家选择 --}}
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">商家 <span class="text-red-500">*</span></label>
                    <select wire:model="formMerchantId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="0">请选择商家</option>
                        @foreach($merchants as $merchant)
                            <option value="{{ $merchant->id }}">{{ $merchant->name }}</option>
                        @endforeach
                    </select>
                    @error('formMerchantId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- 配置类型 --}}
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">配置类型 <span class="text-red-500">*</span></label>
                    <div class="flex gap-4">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" wire:model.live="formTargetType" value="product" class="rounded" />
                            <span class="text-sm text-foreground">商品级（整件商品的所有SKU）</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" wire:model.live="formTargetType" value="sku" class="rounded" />
                            <span class="text-sm text-foreground">SKU级（单个SKU）</span>
                        </label>
                    </div>
                </div>

                {{-- 商品级：选择商品 --}}
                @if($formTargetType === 'product')
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">商品 <span class="text-red-500">*</span></label>
                    <select wire:model="formProductId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="0">请选择商品</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                    @error('formProductId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                @endif

                {{-- SKU级：选择SKU --}}
                @if($formTargetType === 'sku')
                <div>
                    <x-ui.searchable-select label="SKU *" wire-model="formSkuId" :options="$skuOptions" placeholder="搜索SKU..." wireModel="formSkuId" :value="$formSkuId" />
                    @error('formSkuId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                @endif

                {{-- 可见性 --}}
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">可见性</label>
                    <select wire:model="formIsVisible" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="0">不可见</option>
                        <option value="1">可见</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end mt-6">
                <button type="button" wire:click="save" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">{{ $editingId ? '保存' : '添加' }}</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 删除确认弹窗 --}}

    @include('partials.column-modal')
    @include('partials.export-modal')
    @include('partials.import-modal')
    @include('partials.delete-confirm')
</div>
