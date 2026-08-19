<div class="">
    {{-- 页面标题 --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-foreground">购物车</h1>
            <p class="text-muted-foreground mt-1">查看各商家购物车数据，按商户生成客户订单</p>
        </div>
        <div class="flex items-center gap-3">
            @can('order.cart.create')
            <button type="button" wire:click="openCreateModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                <x-ui.icon name="plus" class="w-4 h-4" />
                新增购物车
            </button>
            @endcan
        </div>
    </div>

    {{-- 搜索栏 + 筛选 + 工具按钮 --}}
    <div class="flex items-center gap-3 mb-4">
        <div x-data class="relative">
            <input type="text" wire:model.live="search" class="flex h-9 w-64 rounded-md border border-input bg-background pl-3 pr-8 text-sm" placeholder="搜索商家名称/SKU..." />
            @if($search)
                <button type="button" wire:click="resetFilters" class="absolute right-2 top-1/2 -translate-y-1/2 p-0.5 rounded-sm text-muted-foreground/50 hover:text-muted-foreground hover:bg-muted transition-colors">
                    <x-ui.icon name="x-mark" class="w-3.5 h-3.5" />
                </button>
            @endif
        </div>
        <select wire:model.live="filterMerchantId" class="flex h-9 rounded-md border border-input bg-background px-3 text-sm">
            <option value="">全部商家</option>
            @foreach($merchants as $m)
            <option value="{{ $m->id }}">{{ $m->name }}</option>
            @endforeach
        </select>
        <div class="flex-1"></div>
        <button type="button" wire:click="openColumnModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="adjustments" class="w-4 h-4" />列配置</button>
        <button type="button" wire:click="openImportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-down-tray" class="w-4 h-4" />导入</button>
        <button type="button" wire:click="openExportModal" class="inline-flex items-center gap-1 rounded-md border border-input px-3 py-1.5 text-sm hover:bg-accent transition-colors"><x-ui.icon name="arrow-up-tray" class="w-4 h-4" />导出</button>
        @if($selectedCount > 0)
            <span class="text-sm text-muted-foreground">已选 {{ $selectedCount }} 项</span>
            @can('order.cart.delete')
            <button type="button" wire:click="batchDelete" class="inline-flex items-center gap-1 rounded-md bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700 transition-colors">批量删除</button>
            @endcan
            <button type="button" wire:click="clearSelection" class="text-sm text-muted-foreground hover:text-foreground transition-colors">取消选择</button>
        @endif
    </div>

    {{-- 按商户分组展示 --}}
    @forelse($groupedItems as $group)
        <div class="mb-4 rounded-lg border bg-card overflow-hidden">
            {{-- 商户分组头 --}}
            <div class="flex items-center justify-between px-6 py-3 bg-muted/40 border-b">
                <div class="flex items-center gap-3">
                    <span class="text-sm font-semibold text-foreground">{{ $group['merchant_name'] }}</span>
                    <span class="text-xs text-muted-foreground">{{ $group['item_count'] }} 项</span>
                    <span class="text-xs text-muted-foreground">合计：<span class="font-medium text-foreground">{{ money_format($group['total_amount']) }}</span></span>
                </div>
                <button type="button" wire:click="confirmCreateOrder({{ $group['merchant_id'] }})" class="inline-flex items-center gap-1 rounded-md bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700 transition-colors">
                    <x-ui.icon name="document-plus" class="w-4 h-4" />
                    生成订单
                </button>
            </div>

            {{-- 明细表格 --}}
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                        <th class="px-4 py-2 text-left w-10"><input type="checkbox" class="rounded" /></th>
                        <th class="px-4 py-2 text-left">SKU编码</th>
                        <th class="px-4 py-2 text-left">商品名称</th>
                        <th class="px-4 py-2 text-right">数量</th>
                        <th class="px-4 py-2 text-right">单价</th>
                        <th class="px-4 py-2 text-right">金额</th>
                        <th class="px-4 py-2 text-right w-24">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($group['items'] as $cart)
                    <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="cart-{{ $cart->id }}">
                        <td class="px-4 py-2"><input type="checkbox" value="{{ $cart->id }}" wire:model.live="selectedIds" class="rounded" /></td>
                        <td class="px-4 py-2 font-mono text-foreground">{{ $cart->sku?->sku_code ?? '-' }}</td>
                        <td class="px-4 py-2 text-foreground">{{ $cart->sku?->product?->name ?? '-' }}</td>
                        <td class="px-4 py-2 text-right text-foreground">@php
                            $svc = app(\App\Services\UnitConversionService::class);
                            if ($cart->unit_id && $cart->unit_quantity) {
                                echo $svc->formatWithConversion($cart->sku_id, $cart->unit_id, $cart->unit_quantity);
                            } else {
                                echo $svc->formatHuman($cart->sku_id, $cart->quantity);
                            }
                        @endphp</td>
                        <td class="px-4 py-2 text-right text-foreground">{{ money_format($cart->price) }}</td>
                        <td class="px-4 py-2 text-right text-foreground font-medium">{{ money_format($cart->quantity * $cart->price) }}</td>
                        <td class="px-4 py-2 text-right">
                            <div class="flex items-center justify-end gap-1">
                                @can('order.cart.edit')
                                <button type="button" wire:click="openEditModal({{ $cart->id }})" class="p-1 rounded text-blue-600 hover:bg-blue-50 hover:text-blue-700 transition-colors" title="编辑"><x-ui.icon name="pencil-square" class="w-3.5 h-3.5" /></button>
                                @endcan
                                @can('order.cart.delete')
                                <button type="button" wire:click="confirmDelete({{ $cart->id }})" class="p-1 rounded text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors" title="删除"><x-ui.icon name="trash" class="w-3.5 h-3.5" /></button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @empty
        <div class="rounded-lg border bg-card px-6 py-12 text-center text-sm text-muted-foreground">暂无购物车数据</div>
    @endforelse

    <div class="mt-4">{{ $items->links() }}</div>

    {{-- 新增/编辑弹窗 --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4 p-6 max-h-[85vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">{{ $editingId ? '编辑购物车' : '新增购物车' }}</h2>
                <button type="button" wire:click="closeModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-4">
                @if(!$editingId)
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">商家 <span class="text-red-500">*</span></label>
                    <select wire:model="formMerchantId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="0">请选择商家</option>
                        @foreach($merchants as $m)
                        <option value="{{ $m->id }}">{{ $m->name }}</option>
                        @endforeach
                    </select>
                    @error('formMerchantId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">SKU <span class="text-red-500">*</span></label>
                    <select wire:model="formSkuId" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="0">请选择SKU</option>
                        @foreach($skus as $s)
                        <option value="{{ $s->id }}">{{ $s->sku_code }} - {{ $s->product?->name ?? '' }}</option>
                        @endforeach
                    </select>
                    @error('formSkuId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">数量 <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <input type="number" wire:model.live="formUnitQuantity" min="1" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="0" />
                        @if($availableUnits)
                        <select wire:model.live="formUnitId" class="flex h-9 w-28 rounded-md border border-input bg-background px-2 text-sm">
                            <option value="">单位</option>
                            @foreach($availableUnits as $unit)
                                <option value="{{ $unit['unit_id'] }}">{{ $unit['unit_name'] }}</option>
                            @endforeach
                        </select>
                        @endif
                    </div>
                    @if($unitPreview)
                        <p class="text-xs text-blue-600 mt-1">换算：{{ $unitPreview }}</p>
                    @endif
                    @if($formUnitId && $formUnitQuantity > 0 && $formQuantity > 0)
                        <p class="text-xs text-muted-foreground mt-1">基础数量：{{ $formQuantity }}</p>
                    @endif
                    @error('formQuantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                @if(!$editingId)
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">单价（元） <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="formUnitPrice" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="请输入单价（元）" />
                    @error('formUnitPrice') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                @endif
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="save" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">保存</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 生成订单确认弹窗 --}}
    @if($showCreateOrderConfirm)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">确认生成订单</h2>
                <button type="button" wire:click="closeCreateOrderConfirm" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-3 text-sm text-foreground">
                <p>将为以下商家生成客户订单：</p>
                <div class="rounded-md border bg-muted/30 p-3 space-y-1">
                    <div class="flex justify-between"><span class="text-muted-foreground">商家</span><span class="font-medium">{{ $createOrderMerchantName }}</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">明细数</span><span class="font-medium">{{ $createOrderItemCount }} 项</span></div>
                    <div class="flex justify-between"><span class="text-muted-foreground">合计金额</span><span class="font-medium">{{ money_format($createOrderTotalAmount) }}</span></div>
                </div>
                <p class="text-muted-foreground text-xs">生成后该商家的购物车数据将被清空，订单状态默认为「待拣货」。</p>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeCreateOrderConfirm" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="executeCreateOrder" class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition-colors">确认生成</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 列配置 / 导入 / 导出 / 删除确认弹窗 --}}
    @include('partials.column-modal')
    @include('partials.export-modal')
    @include('partials.import-modal')
    @include('partials.delete-confirm')
</div>
