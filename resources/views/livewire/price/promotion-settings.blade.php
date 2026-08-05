<div>
    {{-- 页面标题 --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-foreground">促销设置</h1>
        <p class="text-muted-foreground mt-1">管理促销活动、商家差异化价格与会员折扣规则，修改后即时生效</p>
    </div>

    <div class="flex gap-6">
        {{-- 左侧分组导航 --}}
        <div class="w-48 shrink-0">
            <nav class="sticky top-6 space-y-1">
                @foreach($navGroups as $groupKey => $groupLabel)
                    <button type="button" wire:click="setActiveGroup('{{ $groupKey }}')"
                        class="w-full text-left px-3 py-2 text-sm rounded-md transition-colors {{ $activeGroup === $groupKey ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:bg-muted hover:text-foreground' }}">
                        {{ $groupLabel }}
                    </button>
                @endforeach
            </nav>
        </div>

        {{-- 右侧配置内容 --}}
        <div class="flex-1">

            {{-- ════════════════════════════════════════════ --}}
            {{-- 分组1: 促销活动 --}}
            {{-- ════════════════════════════════════════════ --}}
            @if($activeGroup === 'promotion')
            <div>
                {{-- 工具栏 --}}
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <input type="text" wire:model.live="promoSearch" placeholder="搜索活动名称..."
                            class="flex h-8 w-60 rounded-md border border-input bg-background px-3 text-sm" />
                    </div>
                    <button type="button" wire:click="openPromoCreateModal"
                        class="inline-flex items-center gap-1.5 rounded-md bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700 transition-colors">
                        <x-ui.icon name="plus" class="w-4 h-4" />
                        新增活动
                    </button>
                </div>

                {{-- 列表 --}}
                <div class="rounded-lg border bg-card">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                <th class="px-4 py-2 text-left">活动名称</th>
                                <th class="px-4 py-2 text-left w-24">类型</th>
                                <th class="px-4 py-2 text-center w-20">状态</th>
                                <th class="px-4 py-2 text-left w-36">开始时间</th>
                                <th class="px-4 py-2 text-left w-36">结束时间</th>
                                <th class="px-4 py-2 text-right w-20">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($promoItems as $promo)
                            <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="promo-{{ $promo->id }}">
                                <td class="px-4 py-2">
                                    <span class="text-sm font-medium text-foreground">{{ $promo->name }}</span>
                                </td>
                                <td class="px-4 py-2">
                                    <span class="text-xs text-muted-foreground">{{ $promoTypeMap[$promo->promo_type] ?? '未知' }}</span>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <button type="button" wire:click="togglePromoStatus({{ $promo->id }})"
                                        class="inline-flex items-center justify-center">
                                        <span class="inline-block h-4 w-4 rounded-full {{ $promo->status ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                                    </button>
                                </td>
                                <td class="px-4 py-2 text-xs text-muted-foreground">{{ $promo->start_at?->format('Y-m-d H:i') }}</td>
                                <td class="px-4 py-2 text-xs text-muted-foreground">{{ $promo->end_at?->format('Y-m-d H:i') }}</td>
                                <td class="px-4 py-2 text-right">
                                    <button type="button" wire:click="deletePromotion({{ $promo->id }})"
                                        onclick="return confirm('确认删除？')"
                                        class="inline-flex items-center justify-center rounded p-1 text-red-500 hover:bg-red-50 transition-colors">
                                        <x-ui.icon name="trash" class="w-4 h-4" />
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="px-6 py-12 text-center text-muted-foreground">暂无促销活动</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- 分页 --}}
                <div class="mt-4">
                    {{ $promoItems->links() }}
                </div>
            </div>

            {{-- 新增促销活动弹窗 --}}
            @if($showPromoCreateModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
                <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4">
                    {{-- 标题栏 --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b">
                        <h2 class="text-lg font-semibold text-foreground">新增促销活动</h2>
                        <button type="button" wire:click="closePromoCreateModal" class="text-muted-foreground hover:text-foreground transition-colors">
                            <x-ui.icon name="x" class="w-5 h-5" />
                        </button>
                    </div>

                    {{-- 表单 --}}
                    <div class="px-6 py-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1">活动名称 <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="promoFormName"
                                class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                            @error('promoFormName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1">活动类型 <span class="text-red-500">*</span></label>
                            <select wire:model="promoFormType"
                                class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                                @foreach($promoTypeMap as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('promoFormType') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">开始时间 <span class="text-red-500">*</span></label>
                                <input type="datetime-local" wire:model="promoFormStartAt"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                                @error('promoFormStartAt') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">结束时间 <span class="text-red-500">*</span></label>
                                <input type="datetime-local" wire:model="promoFormEndAt"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                                @error('promoFormEndAt') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- 操作按钮 --}}
                    <div class="flex justify-end gap-3 px-6 py-4 border-t">
                        <button type="button" wire:click="closePromoCreateModal"
                            class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">
                            取消
                        </button>
                        <button type="button" wire:click="createPromotion"
                            class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                            创建
                        </button>
                    </div>
                </div>
            </div>
            @endif
            @endif

            {{-- ════════════════════════════════════════════ --}}
            {{-- 分组2: 商家价格 --}}
            {{-- ════════════════════════════════════════════ --}}
            @if($activeGroup === 'store')
            <div>
                {{-- 工具栏 --}}
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <input type="text" wire:model.live="storePriceSearch" placeholder="搜索SKU编码..."
                            class="flex h-8 w-48 rounded-md border border-input bg-background px-3 text-sm" />
                        <select wire:model.live="storePriceMerchantFilter"
                            class="flex h-8 w-40 rounded-md border border-input bg-background px-2 text-sm">
                            <option value="">全部商家</option>
                            @foreach($merchants as $m)
                                <option value="{{ $m->id }}">{{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="button" wire:click="openStorePriceCreateModal"
                        class="inline-flex items-center gap-1.5 rounded-md bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700 transition-colors">
                        <x-ui.icon name="plus" class="w-4 h-4" />
                        新增商家价格
                    </button>
                </div>

                {{-- 列表 --}}
                <div class="rounded-lg border bg-card">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                <th class="px-4 py-2 text-left">商家</th>
                                <th class="px-4 py-2 text-left">SKU</th>
                                <th class="px-4 py-2 text-left w-24">价格类型</th>
                                <th class="px-4 py-2 text-left w-24">调整方式</th>
                                <th class="px-4 py-2 text-right w-24">调整值</th>
                                <th class="px-4 py-2 text-left w-32">生效时间</th>
                                <th class="px-4 py-2 text-left w-32">失效时间</th>
                                <th class="px-4 py-2 text-center w-16">状态</th>
                                <th class="px-4 py-2 text-right w-16">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($storePriceItems as $sp)
                            <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="store-price-{{ $sp->id }}">
                                <td class="px-4 py-2 text-xs text-foreground">
                                    {{ App\Models\Merchant::find($sp->store_id)?->name ?? "商家#{$sp->store_id}" }}
                                </td>
                                <td class="px-4 py-2 text-xs font-mono text-muted-foreground">
                                    {{ $sp->sku?->sku_code ?? "SKU#{$sp->sku_id}" }}
                                </td>
                                <td class="px-4 py-2">
                                    <span class="text-xs text-muted-foreground">{{ $priceTypeMap[$sp->price_type] ?? '-' }}</span>
                                </td>
                                <td class="px-4 py-2">
                                    <span class="text-xs text-muted-foreground">{{ $adjustModeMap[$sp->adjust_mode] ?? '-' }}</span>
                                </td>
                                <td class="px-4 py-2 text-right font-mono text-xs">
                                    @if($sp->adjust_mode === 3)
                                        {{ money_format($sp->adjust_value) }}
                                    @elseif($sp->adjust_mode === 2)
                                        {{ round($sp->adjust_value / 100, 2) }}%
                                    @else
                                        {{ money_format($sp->adjust_value) }}
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-xs text-muted-foreground">{{ $sp->effective_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                <td class="px-4 py-2 text-xs text-muted-foreground">{{ $sp->expire_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                <td class="px-4 py-2 text-center">
                                    <button type="button" wire:click="toggleStorePriceStatus({{ $sp->id }})"
                                        class="inline-flex items-center justify-center">
                                        <span class="inline-block h-4 w-4 rounded-full {{ $sp->status ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                                    </button>
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <div class="flex justify-end gap-1">
                                        <button type="button" wire:click="editStorePrice({{ $sp->id }})"
                                            class="inline-flex items-center justify-center rounded p-1 text-blue-600 hover:bg-blue-50 transition-colors">
                                            <x-ui.icon name="pencil" class="w-4 h-4" />
                                        </button>
                                        <button type="button" wire:click="deleteStorePrice({{ $sp->id }})"
                                            onclick="return confirm('确认删除？')"
                                            class="inline-flex items-center justify-center rounded p-1 text-red-500 hover:bg-red-50 transition-colors">
                                            <x-ui.icon name="trash" class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="9" class="px-6 py-12 text-center text-muted-foreground">暂无商家价格配置</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $storePriceItems->links() }}
                </div>
            </div>

            {{-- 新增/编辑商家价格弹窗 --}}
            @if($showStorePriceCreateModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
                <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4">
                    <div class="flex items-center justify-between px-6 py-4 border-b">
                        <h2 class="text-lg font-semibold text-foreground">{{ $editingStorePriceId ? '编辑商家价格' : '新增商家价格' }}</h2>
                        <button type="button" wire:click="closeStorePriceCreateModal" class="text-muted-foreground hover:text-foreground transition-colors">
                            <x-ui.icon name="x" class="w-5 h-5" />
                        </button>
                    </div>

                    <div class="px-6 py-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1">商家 <span class="text-red-500">*</span></label>
                            <select wire:model="storePriceFormMerchantId"
                                class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                                <option value="">请选择商家</option>
                                @foreach($merchants as $m)
                                    <option value="{{ $m->id }}">{{ $m->name }}</option>
                                @endforeach
                            </select>
                            @error('storePriceFormMerchantId') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1">SKU <span class="text-red-500">*</span></label>
                            <input type="number" wire:model="storePriceFormSkuId"
                                class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm"
                                placeholder="输入 SKU ID" />
                            @error('storePriceFormSkuId') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">价格类型 <span class="text-red-500">*</span></label>
                                <select wire:model="storePriceFormPriceType"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                                    @foreach($priceTypeMap as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">调整方式 <span class="text-red-500">*</span></label>
                                <select wire:model="storePriceFormAdjustMode"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                                    @foreach($adjustModeMap as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1">调整值 <span class="text-red-500">*</span>
                                <span class="text-xs text-muted-foreground font-normal">
                                    （{{ $storePriceFormAdjustMode == 2 ? '万分比，如 9500=95折' : '厘' }}）
                                </span>
                            </label>
                            <input type="number" wire:model="storePriceFormAdjustValue"
                                class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                            @error('storePriceFormAdjustValue') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">生效时间</label>
                                <input type="datetime-local" wire:model="storePriceFormEffectiveAt"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">失效时间</label>
                                <input type="datetime-local" wire:model="storePriceFormExpireAt"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                                @error('storePriceFormExpireAt') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 px-6 py-4 border-t">
                        <button type="button" wire:click="closeStorePriceCreateModal"
                            class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">
                            取消
                        </button>
                        <button type="button" wire:click="saveStorePrice"
                            class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                            {{ $editingStorePriceId ? '更新' : '创建' }}
                        </button>
                    </div>
                </div>
            </div>
            @endif
            @endif

            {{-- ════════════════════════════════════════════ --}}
            {{-- 分组3: 会员折扣 --}}
            {{-- ════════════════════════════════════════════ --}}
            @if($activeGroup === 'member')
            <div>
                {{-- 工具栏 --}}
                <div class="flex items-center justify-between mb-4">
                    <div class="text-sm text-muted-foreground">
                        管理会员等级折扣率（万分比），常驻折扣长期生效，限时折扣需绑定促销活动
                    </div>
                    <button type="button" wire:click="openMemberDiscountModal"
                        class="inline-flex items-center gap-1.5 rounded-md bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700 transition-colors">
                        <x-ui.icon name="plus" class="w-4 h-4" />
                        新增折扣
                    </button>
                </div>

                {{-- 列表 --}}
                <div class="rounded-lg border bg-card">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                <th class="px-4 py-2 text-left w-28">会员等级</th>
                                <th class="px-4 py-2 text-right w-32">折扣率</th>
                                <th class="px-4 py-2 text-center w-24">类型</th>
                                <th class="px-4 py-2 text-center w-16">状态</th>
                                <th class="px-4 py-2 text-right w-24">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($memberDiscounts as $md)
                            <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="member-discount-{{ $md->id }}">
                                <td class="px-4 py-2">
                                    <span class="text-sm font-medium text-foreground">{{ $memberLevelMap[$md->member_level] ?? '未知' }}</span>
                                </td>
                                <td class="px-4 py-2 text-right font-mono text-sm">
                                    {{ round($md->discount_rate / 100, 2) }}%
                                    <span class="text-xs text-muted-foreground">({{ $md->discount_rate }}‱)</span>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    @if($md->is_permanent)
                                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-medium bg-green-50 text-green-700">常驻</span>
                                    @else
                                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-medium bg-blue-50 text-blue-700">限时</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <button type="button" wire:click="toggleMemberDiscountStatus({{ $md->id }})"
                                        class="inline-flex items-center justify-center">
                                        <span class="inline-block h-4 w-4 rounded-full {{ $md->status ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                                    </button>
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <div class="flex justify-end gap-1">
                                        <button type="button" wire:click="editMemberDiscount({{ $md->id }})"
                                            class="inline-flex items-center justify-center rounded p-1 text-blue-600 hover:bg-blue-50 transition-colors">
                                            <x-ui.icon name="pencil" class="w-4 h-4" />
                                        </button>
                                        <button type="button" wire:click="deleteMemberDiscount({{ $md->id }})"
                                            onclick="return confirm('确认删除？')"
                                            class="inline-flex items-center justify-center rounded p-1 text-red-500 hover:bg-red-50 transition-colors">
                                            <x-ui.icon name="trash" class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-6 py-12 text-center text-muted-foreground">暂无会员折扣配置</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $memberDiscounts->links() }}
                </div>
            </div>

            {{-- 新增/编辑会员折扣弹窗 --}}
            @if($showMemberDiscountModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
                <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4">
                    <div class="flex items-center justify-between px-6 py-4 border-b">
                        <h2 class="text-lg font-semibold text-foreground">{{ $editingMemberDiscountId ? '编辑会员折扣' : '新增会员折扣' }}</h2>
                        <button type="button" wire:click="closeMemberDiscountModal" class="text-muted-foreground hover:text-foreground transition-colors">
                            <x-ui.icon name="x" class="w-5 h-5" />
                        </button>
                    </div>

                    <div class="px-6 py-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1">会员等级 <span class="text-red-500">*</span></label>
                            <select wire:model="memberDiscountFormLevel"
                                class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                                @foreach($memberLevelMap as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('memberDiscountFormLevel') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1">折扣率（万分比）<span class="text-red-500">*</span></label>
                            <input type="number" wire:model="memberDiscountFormRate" min="1" max="10000" step="1"
                                class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm"
                                placeholder="9500=95折" />
                            <p class="text-xs text-muted-foreground mt-1">10000=不打折，9500=95折，9000=9折</p>
                            @error('memberDiscountFormRate') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1">折扣类型 <span class="text-red-500">*</span></label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="radio" wire:model="memberDiscountFormIsPermanent" value="1"
                                        class="h-4 w-4 text-blue-600" />
                                    常驻折扣
                                </label>
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="radio" wire:model="memberDiscountFormIsPermanent" value="0"
                                        class="h-4 w-4 text-blue-600" />
                                    限时折扣
                                </label>
                            </div>
                            @error('memberDiscountFormIsPermanent') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 px-6 py-4 border-t">
                        <button type="button" wire:click="closeMemberDiscountModal"
                            class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">
                            取消
                        </button>
                        <button type="button" wire:click="saveMemberDiscount"
                            class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                            {{ $editingMemberDiscountId ? '更新' : '创建' }}
                        </button>
                    </div>
                </div>
            </div>
            @endif
            @endif

        </div>
    </div>
</div>
