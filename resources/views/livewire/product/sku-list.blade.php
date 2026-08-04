<div class="">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">SKU管理</h1>
            <p class="text-muted-foreground mt-1">管理商品规格及价格信息</p>
        </div>
        @can('product.product.create')
        <button type="button" wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
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
        <button type="button" wire:click="openColumnModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors">列配置</button>
        <button type="button" wire:click="openImportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors">导入</button>
        <button type="button" wire:click="openExportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors">导出</button>
        @if($selectedCount > 0)
            <span class="text-sm text-muted-foreground">已选 {{ $selectedCount }} 项</span>
            @can('product.product.delete')
            <button type="button" wire:click="batchDelete" class="inline-flex items-center gap-1 rounded-md bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700 transition-colors">批量删除</button>
            @endcan
            <button type="button" wire:click="clearSelection" class="text-sm text-muted-foreground hover:text-foreground transition-colors">取消选择</button>
        @endif
    </div>

    <div class="rounded-lg border bg-card overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                    <th class="px-4 py-2 text-left w-10"><input type="checkbox" wire:model.live="selectAll" class="rounded" /></th>
                    <th class="px-4 py-2 text-left">SKU编码</th>
                    <th class="px-4 py-2 text-left">商品名称</th>
                    <th class="px-4 py-2 text-left">采购价</th>
                    <th class="px-4 py-2 text-left">成本价</th>
                    <th class="px-4 py-2 text-left">最低采购限价</th>
                    <th class="px-4 py-2 text-left">吊牌价</th>
                    <th class="px-4 py-2 text-left">零售价</th>
                    <th class="px-4 py-2 text-left">批发价</th>
                    <th class="px-4 py-2 text-left">员工价</th>
                    <th class="px-4 py-2 text-left">门店价</th>
                    <th class="px-4 py-2 text-left">小程序价</th>
                    <th class="px-4 py-2 text-left">配送价</th>
                    <th class="px-4 py-2 text-left">最低销售限价</th>
                    <th class="px-4 py-2 text-left">最高销售限价</th>
                    <th class="px-4 py-2 text-left">库存</th>
                    <th class="px-4 py-2 text-left">状态</th>
                    <th class="px-4 py-2 text-left">审核</th>
                    <th class="px-4 py-2 text-left w-24">操作</th>
                </tr>
            </thead>
            <tbody>
                @forelse($skus as $sku)
                <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="sku-{{ $sku->id }}">
                    <td class="px-4 py-2"><input type="checkbox" value="{{ $sku->id }}" wire:model.live="selectedIds" class="rounded" /></td>
                    <td class="px-4 py-2 font-medium text-foreground font-mono">{{ $sku->sku_code }}</td>
                    <td class="px-4 py-2 text-foreground truncate">{{ $sku->product?->name ?? '-' }}</td>
                    <td class="px-4 py-2 text-foreground">{{ money_format($sku->purchase_price) }}</td>
                    <td class="px-4 py-2 text-foreground">{{ money_format($sku->cost_price) }}</td>
                    <td class="px-4 py-2 text-foreground">{{ money_format($sku->min_purchase_price) }}</td>
                    <td class="px-4 py-2 text-foreground">{{ money_format($sku->list_price) }}</td>
                    <td class="px-4 py-2 text-foreground">{{ money_format($sku->retail_price) }}</td>
                    <td class="px-4 py-2 text-foreground">{{ money_format($sku->wholesale_price) }}</td>
                    <td class="px-4 py-2 text-foreground">{{ money_format($sku->employee_price) }}</td>
                    <td class="px-4 py-2 text-foreground">{{ money_format($sku->offline_price) }}</td>
                    <td class="px-4 py-2 text-foreground">{{ money_format($sku->miniapp_price) }}</td>
                    <td class="px-4 py-2 text-foreground">{{ money_format($sku->delivery_price) }}</td>
                    <td class="px-4 py-2 text-foreground">{{ money_format($sku->min_sale_price) }}</td>
                    <td class="px-4 py-2 text-foreground">{{ money_format($sku->max_sale_price) }}</td>
                    <td class="px-4 py-2 text-foreground">{{ $sku->stock }}</td>
                    <td class="px-4 py-2">
                        @if($sku->status === 1)
                            <span class="inline-flex items-center gap-1.5 text-xs text-green-700"><span class="w-2 h-2 rounded-full bg-green-500"></span>启用</span>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-xs text-gray-500"><span class="w-2 h-2 rounded-full bg-gray-400"></span>禁用</span>
                        @endif
                    </td>
                    <td class="px-4 py-2">
                        {!! status_badge($sku->approval_status, 'sku_approval') !!}
                    </td>
                    <td class="px-4 py-2">
                        <div class="flex items-center gap-2">
                            @can('product.product.edit')
                            <button type="button" wire:click="openEditModal({{ $sku->id }})" class="text-blue-600 hover:text-blue-700 text-sm">编辑</button>
                            @endcan
                            @can('product.product.delete')
                            <button type="button" wire:click="confirmDelete({{ $sku->id }})" class="text-red-600 hover:text-red-700 text-sm">删除</button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="19" class="px-6 py-12 text-center text-muted-foreground">暂无SKU数据</td></tr>
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
                <button type="button" onclick="if(!confirm('关闭后未保存的内容将丢失，确认关闭？')){event.preventDefault()}else{Livewire.dispatch('closeModal')}" class="inline-flex items-center justify-center rounded-md p-1 text-muted-foreground hover:text-foreground hover:bg-muted transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="space-y-5">
                {{-- 基本信息 --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-ui.searchable-select label="商品 *" wire-model="formProductId" :options="$productOptions" placeholder="搜索商品..." wireModel="formProductId" />
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
                    <label class="block text-sm font-medium text-foreground mb-1">状态</label>
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
    @if($showDeleteConfirm)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-sm mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-2">确认删除</h2>
            <p class="text-sm text-muted-foreground mb-6">确定要删除该SKU吗？此操作不可恢复。</p>
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="closeDeleteConfirm" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="delete" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">删除</button>
            </div>
        </div>
    </div>
    @endif

    @include('partials.column-modal')
    @include('partials.export-modal')
    @include('partials.import-modal')
    @include('partials.delete-confirm')
</div>