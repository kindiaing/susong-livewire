<div class="">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">SKU管理</h1>
            <p class="text-muted-foreground mt-1">管理商品规格及价格信息</p>
        </div>
        @can('product.product.create')
        <button type="button" wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            <x-ui.icon name="plus" class="w-4 h-4" />
            新增SKU
        </button>
        @endcan
    </div>

    <div class="flex items-center gap-3 mb-4">
        <div x-data class="relative">
            <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background pl-3 pr-8 text-sm" placeholder="搜索SKU编码/商品名称..." />
            @if($search)
                <button type="button" wire:click="resetFilters" class="absolute right-2 top-1/2 -translate-y-1/2 p-0.5 rounded-sm text-muted-foreground/50 hover:text-muted-foreground hover:bg-muted transition-colors">
                    <x-ui.icon name="x-mark" class="w-3.5 h-3.5" />
                </button>
            @endif
        </div>
        <select wire:model.live="filterStatus" class="flex h-9 rounded-md border border-input bg-background px-3 text-sm">
            <option value="-1">全部状态</option>
            <option value="1">启用</option>
            <option value="0">禁用</option>
        </select>
        <select wire:model.live="filterApprovalStatus" class="flex h-9 rounded-md border border-input bg-background px-3 text-sm">
            <option value="-1">全部审核</option>
            <option value="1">待审核</option>
            <option value="2">已通过</option>
            <option value="3">已拒绝</option>
        </select>
        <div class="flex-1"></div>
        <button type="button" wire:click="openColumnModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="adjustments" class="w-4 h-4" />列配置</button>
        <button type="button" wire:click="openImportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-down-tray" class="w-4 h-4" />导入</button>
        <button type="button" wire:click="openExportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-up-tray" class="w-4 h-4" />导出</button>
        @if($selectedCount > 0)
            <span class="text-sm text-muted-foreground">已选 {{ $selectedCount }} 项</span>
            @if($isSuperAdmin)
            <button type="button" wire:click="batchDelete" class="inline-flex items-center gap-1 rounded-md bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700 transition-colors">批量删除</button>
            @endif
            <button type="button" wire:click="clearSelection" class="text-sm text-muted-foreground hover:text-foreground transition-colors">取消选择</button>
        @endif
    </div>

    @php
        $allCols = collect($this->getAllColumns());
        $visibleCols = $allCols->filter(fn($col) => $this->isColumnVisible($col['key']))->values();
        $colspan = $visibleCols->count() + 2;
    @endphp

    <div class="rounded-lg border bg-card overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                    <th class="px-4 py-2 text-left w-10"><input type="checkbox" wire:model.live="selectAll" class="rounded" /></th>
                    @foreach($visibleCols as $col)
                    <th class="px-4 py-2 text-left">{{ $col['label'] }}</th>
                    @endforeach
                    <th class="px-4 py-2 text-left w-24">操作</th>
                </tr>
            </thead>
            <tbody>
                @forelse($skus as $sku)
                <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="sku-{{ $sku->id }}">
                    <td class="px-4 py-2"><input type="checkbox" value="{{ $sku->id }}" wire:model.live="selectedIds" class="rounded" /></td>
                    @foreach($visibleCols as $col)
                    @switch($col['key'])
                        @case('id')
                            <td class="px-4 py-2 text-muted-foreground">{{ $sku->id }}</td>
                            @break
                        @case('product_id')
                            <td class="px-4 py-2 text-foreground truncate">{{ $sku->product?->name ?? '-' }}</td>
                            @break
                        @case('sku_code')
                            <td class="px-4 py-2 font-medium text-foreground font-mono">{{ $sku->sku_code }}</td>
                            @break
                        @case('stock')
                            <td class="px-4 py-2 text-foreground">{{ $sku->stock }}</td>
                            @break
                        @case('approval_status')
                            <td class="px-4 py-2">{!! status_badge($sku->approval_status, 'sku_approval') !!}</td>
                            @break
                        @case('status')
                            <td class="px-4 py-2">
                                @if($sku->status === 1)
                                    <span class="inline-flex items-center gap-1.5 text-xs text-green-700"><span class="w-2 h-2 rounded-full bg-green-500"></span>启用</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs text-gray-500"><span class="w-2 h-2 rounded-full bg-gray-400"></span>禁用</span>
                                @endif
                            </td>
                            @break
                        @default
                            @if(in_array($col['key'], ['purchase_price','cost_price','min_purchase_price','list_price','retail_price','wholesale_price','employee_price','offline_price','miniapp_price','delivery_price','min_sale_price','max_sale_price']))
                            <td class="px-4 py-2 text-foreground">{{ money_format($sku->{$col['key']}) }}</td>
                            @else
                            <td class="px-4 py-2 text-foreground truncate">{{ $sku->{$col['key']} ?? '-' }}</td>
                            @endif
                    @endswitch
                    @endforeach
                    <td class="px-4 py-2">
                        <div class="flex items-center gap-2">
                            @can('product.product.edit')
                            <button type="button" wire:click="openEditModal({{ $sku->id }})" class="p-1 rounded text-blue-600 hover:bg-blue-50 hover:text-blue-700 transition-colors" title="编辑"><x-ui.icon name="pencil-square" class="w-3.5 h-3.5" /></button>
                            @endcan
                            @can('product.product.edit')
                            <button type="button" wire:click="toggleStatus({{ $sku->id }})" class="text-sm {{ $sku->status === 1 ? 'text-orange-600 hover:text-orange-700' : 'text-green-600 hover:text-green-700' }}">
                                {{ $sku->status === 1 ? '禁用' : '启用' }}
                            </button>
                            @endcan
                            @if($isSuperAdmin)
                            <button type="button" wire:click="confirmDelete({{ $sku->id }})" class="p-1 rounded text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors" title="删除（软删除）"><x-ui.icon name="trash" class="w-3.5 h-3.5" /></button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="{{ $colspan }}" class="px-6 py-12 text-center text-muted-foreground">暂无SKU数据</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $skus->links() }}</div>

    {{-- Create/Edit Modal --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-2xl mx-4 p-6 max-h-[85vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">{{ $editingId ? '编辑SKU' : '新增SKU' }}</h2>
                <button type="button" wire:click="closeModal" class="inline-flex items-center justify-center rounded-md p-1 text-muted-foreground hover:text-foreground hover:bg-muted transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Tab 切换 --}}
            <div class="flex border-b mb-4">
                <button type="button" wire:click="$set('activeTab','basic')" class="px-4 py-2 text-sm font-medium border-b-2 transition-colors {{ $activeTab === 'basic' ? 'border-blue-600 text-blue-600' : 'border-transparent text-muted-foreground hover:text-foreground' }}">基本信息</button>
                <button type="button" wire:click="$set('activeTab','conversion')" class="px-4 py-2 text-sm font-medium border-b-2 transition-colors {{ $activeTab === 'conversion' ? 'border-blue-600 text-blue-600' : 'border-transparent text-muted-foreground hover:text-foreground' }}">单位换算</button>
            </div>

            {{-- Tab 内容区 --}}
            {{-- 换算配置 Tab --}}
            <div class="space-y-4 {{ $activeTab === 'conversion' ? '' : 'hidden' }}">
                    <div class="rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800">
                        <strong>说明：</strong>单位换算为严格单链路，如 箱→件→包。设置后，库存/订单数量统一用最小单位存储，列表页自动换算显示。
                    </div>

                    {{-- 最小计量单位 --}}
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">最小计量单位（base_unit）<span class="text-red-500">*</span></label>
                        <select wire:model.live="formBaseUnitId" wire:key="base-unit-select" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="">-- 选择最小单位 --</option>
                            @foreach($unitOptions as $unit)
                            <option value="{{ $unit->id }}" {{ (string) $formBaseUnitId === (string) $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-muted-foreground mt-1">所有库存、订单数量统一按此单位存储</p>
                    </div>

                    {{-- 换算链路 --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-sm font-medium text-foreground">换算链路（从大到小）</label>
                            <button type="button" wire:click="addConversionRow" class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-700 font-medium">
                                <x-ui.icon name="plus" class="w-3.5 h-3.5" />添加一级
                            </button>
                        </div>

                        @if(!empty($formConversions))
                        <div class="space-y-3">
                            @foreach($formConversions as $index => $row)
                            <div class="flex items-center gap-2 rounded-md border border-input p-3" wire:key="conv-row-{{ $index }}">
                                <span class="text-xs text-muted-foreground font-medium shrink-0">{{ $loop->iteration }}.</span>
                                <select wire:model.live="formConversions.{{ $index }}.from_unit_id" wire:key="conv-from-{{ $index }}" class="flex h-8 rounded-md border border-input bg-background px-2 text-sm min-w-[80px]">
                                    <option value="">大单位</option>
                                    @foreach($unitOptions as $unit)
                                    <option value="{{ $unit->id }}" {{ (string) ($row['from_unit_id'] ?? '') === (string) $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-muted-foreground text-sm">=</span>
                                <input type="number" wire:model="formConversions.{{ $index }}.ratio" class="flex h-8 w-20 rounded-md border border-input bg-background px-2 text-sm text-center" min="1" placeholder="系数" />
                                <select wire:model.live="formConversions.{{ $index }}.to_unit_id" wire:key="conv-to-{{ $index }}" class="flex h-8 rounded-md border border-input bg-background px-2 text-sm min-w-[80px]">
                                    <option value="">小单位</option>
                                    @foreach($unitOptions as $unit)
                                    <option value="{{ $unit->id }}" {{ (string) ($row['to_unit_id'] ?? '') === (string) $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" wire:click="removeConversionRow({{ $index }})" class="p-1 rounded text-red-500 hover:bg-red-50 transition-colors shrink-0">
                                    <x-ui.icon name="x-mark" class="w-4 h-4" />
                                </button>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-sm text-muted-foreground py-4 text-center">暂未配置换算关系，点击"添加一级"开始</p>
                        @endif

                        {{-- 链路预览 --}}
                        @if(!empty($formConversions))
                        <div class="mt-3 p-3 rounded-lg bg-muted/50 border">
                            <p class="text-xs text-muted-foreground mb-1">链路预览</p>
                            <p class="text-sm font-medium text-foreground">{{ $conversionPreview }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            {{-- 基本信息 Tab --}}
            <div class="space-y-5 {{ $activeTab === 'basic' ? '' : 'hidden' }}">
                {{-- 基本信息 --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-ui.searchable-select label="商品 *" wire:model="formProductId" :options="$productOptions" placeholder="搜索商品..." wireModel="formProductId" />
                        @error('formProductId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">SKU编码 <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="formSkuCode" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="唯一编码" />
                        @error('formSkuCode') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">规格属性（JSON）</label>
                    <textarea wire:model="formSpecs" rows="2" class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm font-mono" placeholder='{"颜色":"红色","规格":"500g"}'></textarea>
                </div>

                {{-- 采购价格组 --}}
                <div class="border rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-foreground mb-3">采购价格</h3>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1">标准采购价（元）</label>
                            <input type="number" wire:model="formPurchasePrice" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" min="0" step="0.001" placeholder="0.000" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1">成本价（元）</label>
                            <input type="number" wire:model="formCostPrice" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" min="0" step="0.001" placeholder="0.000" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1">最低采购限价（元）</label>
                            <input type="number" wire:model="formMinPurchasePrice" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" min="0" step="0.001" placeholder="0.000" />
                        </div>
                    </div>
                </div>

                {{-- 销售价格组 --}}
                <div class="border rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-foreground mb-3">销售价格</h3>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1">吊牌价（元）</label>
                            <input type="number" wire:model="formListPrice" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" min="0" step="0.001" placeholder="0.000" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1">标准零售价（元）</label>
                            <input type="number" wire:model="formRetailPrice" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" min="0" step="0.001" placeholder="0.000" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1">批发团购价（元）</label>
                            <input type="number" wire:model="formWholesalePrice" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" min="0" step="0.001" placeholder="0.000" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1">员工内部价（元）</label>
                            <input type="number" wire:model="formEmployeePrice" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" min="0" step="0.001" placeholder="0.000" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1">最低销售限价（元）</label>
                            <input type="number" wire:model="formMinSalePrice" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" min="0" step="0.001" placeholder="0.000" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1">最高销售限价（元）</label>
                            <input type="number" wire:model="formMaxSalePrice" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" min="0" step="0.001" placeholder="0.000" />
                        </div>
                    </div>
                </div>

                {{-- 渠道基准价组 --}}
                <div class="border rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-foreground mb-3">渠道基准价</h3>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1">门店基准价（元）</label>
                            <input type="number" wire:model="formOfflinePrice" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" min="0" step="0.001" placeholder="0.000" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1">小程序基准价（元）</label>
                            <input type="number" wire:model="formMiniappPrice" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" min="0" step="0.001" placeholder="0.000" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1">配送基准价（元）</label>
                            <input type="number" wire:model="formDeliveryPrice" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" min="0" step="0.001" placeholder="0.000" />
                        </div>
                    </div>
                </div>

                {{-- 状态 --}}
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">状态 <span class="text-red-500">*</span></label>
                    <select wire:model="formStatus" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="1">启用</option>
                        <option value="0">禁用</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="save" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">保存</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Delete Confirm Modal --}}

    @include('partials.column-modal')
    @include('partials.export-modal')
    @include('partials.import-modal')
    @include('partials.delete-confirm')
</div>