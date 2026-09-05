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
                                    @can('price.promotion.delete')
                                    <button type="button" wire:click="confirmAction('deletePromotion', '确认删除此促销活动？', '删除后数据不可恢复', {{ $promo->id }})"
                                        class="inline-flex items-center justify-center rounded p-1 text-red-500 hover:bg-red-50 transition-colors">
                                        <x-ui.icon name="trash" class="w-4 h-4" />
                                    </button>
                                    @endcan
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
            {{-- 分组2: 活动明细 --}}
            {{-- ════════════════════════════════════════════ --}}
            @if($activeGroup === 'detail')
            <div>
                {{-- 活动选择器 --}}
                <div class="flex items-center justify-between mb-4 gap-4">
                    <div class="flex items-center gap-3 flex-1">
                        <select wire:model.live="detailPromotionId"
                            class="flex h-8 w-72 rounded-md border border-input bg-background px-3 text-sm">
                            <option value="">请选择促销活动</option>
                            @foreach($detailPromotions as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}（{{ $promoTypeMap[$p->promo_type] ?? '未知' }}）</option>
                            @endforeach
                        </select>
                        @if($detailPromotion)
                            <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-blue-100 text-blue-700">
                                {{ $promoTypeMap[$detailPromotion->promo_type] ?? '未知' }}
                            </span>
                            <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium {{ $detailPromotion->status ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $detailPromotion->status ? '已启用' : '已禁用' }}
                            </span>
                        @endif
                    </div>
                </div>

                @if(!$detailPromotion)
                    <div class="rounded-lg border bg-card px-6 py-12 text-center text-muted-foreground">请先在上方选择促销活动，或到「促销活动」页新增</div>
                @else
                {{-- 普通促销 / 清仓临期 / 拼团 / 秒杀：活动商品明细 --}}
                @if(in_array($detailPromoType, [1, 5, 6, 7]))
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-foreground">活动商品明细</h3>
                    <div class="flex items-center gap-2">
                        <input type="text" wire:model.live="detailSearch" placeholder="搜索SKU编码..."
                            class="flex h-8 w-48 rounded-md border border-input bg-background px-3 text-sm" />
                        <button type="button" wire:click="openDetailSkuModal"
                            class="inline-flex items-center gap-1.5 rounded-md bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700 transition-colors">
                            <x-ui.icon name="plus" class="w-4 h-4" />
                            添加商品
                        </button>
                    </div>
                </div>
                <div class="rounded-lg border bg-card mb-6">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                <th class="px-4 py-2 text-left">SKU</th>
                                <th class="px-4 py-2 text-left w-20">定价方式</th>
                                <th class="px-4 py-2 text-right w-24">促销价/折扣</th>
                                <th class="px-4 py-2 text-right w-20">限购</th>
                                <th class="px-4 py-2 text-right w-20">库存限量</th>
                                <th class="px-4 py-2 text-center w-16">状态</th>
                                <th class="px-4 py-2 text-right w-24">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($detailSkus as $ps)
                            <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="detail-sku-{{ $ps->id }}">
                                <td class="px-4 py-2 text-xs">
                                    <span class="font-mono text-foreground">{{ $ps->sku?->sku_code ?? "SKU#{$ps->sku_id}" }}</span>
                                    <span class="text-muted-foreground ml-1">{{ $ps->sku?->product?->name ?? '' }}</span>
                                </td>
                                <td class="px-4 py-2 text-xs text-muted-foreground">{{ $detailPromoTypeMap[$ps->price_type] ?? '-' }}</td>
                                <td class="px-4 py-2 text-right font-mono text-xs">
                                    @if($ps->price_type === 1)
                                        {{ money_format($ps->fixed_price) }}
                                    @else
                                        {{ round($ps->discount_rate / 100, 2) }}%
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-right text-xs text-muted-foreground">{{ $ps->max_quantity ?: '-' }}</td>
                                <td class="px-4 py-2 text-right text-xs text-muted-foreground">{{ $ps->stock_limit ?: '-' }}</td>
                                <td class="px-4 py-2 text-center">
                                    <button type="button" wire:click="toggleDetailSkuStatus({{ $ps->id }})"
                                        class="inline-flex items-center justify-center">
                                        <span class="inline-block h-4 w-4 rounded-full {{ $ps->status ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                                    </button>
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <div class="flex justify-end gap-1">
                                        <button type="button" wire:click="editDetailSku({{ $ps->id }})"
                                            class="inline-flex items-center justify-center rounded p-1 text-blue-600 hover:bg-blue-50 transition-colors">
                                            <x-ui.icon name="pencil-square" class="w-4 h-4" />
                                        </button>
                                        <button type="button" wire:click="confirmAction('deleteDetailSku', '确认移除此活动商品？', '移除后该SKU不再享受活动价', {{ $ps->id }})"
                                            class="inline-flex items-center justify-center rounded p-1 text-red-500 hover:bg-red-50 transition-colors">
                                            <x-ui.icon name="trash" class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="px-6 py-12 text-center text-muted-foreground">暂无活动商品，添加后该活动才可生效取价</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @endif

                {{-- 满减：阶梯明细 --}}
                @if($detailPromoType === 2)
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-foreground">满减阶梯</h3>
                    <button type="button" wire:click="openFullReductionModal"
                        class="inline-flex items-center gap-1.5 rounded-md bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700 transition-colors">
                        <x-ui.icon name="plus" class="w-4 h-4" />
                        添加阶梯
                    </button>
                </div>
                <div class="rounded-lg border bg-card mb-6">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                <th class="px-4 py-2 text-left w-24">门槛类型</th>
                                <th class="px-4 py-2 text-right">门槛值</th>
                                <th class="px-4 py-2 text-left w-20">减免方式</th>
                                <th class="px-4 py-2 text-right">减免内容</th>
                                <th class="px-4 py-2 text-center w-16">可叠加</th>
                                <th class="px-4 py-2 text-right w-24">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($fullReductions as $fr)
                            <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="fr-{{ $fr->id }}">
                                <td class="px-4 py-2 text-xs text-muted-foreground">{{ $fr->threshold_type === 1 ? '按金额' : '按件数' }}</td>
                                <td class="px-4 py-2 text-right font-mono text-xs">{{ $fr->threshold_type === 1 ? money_format($fr->threshold_amount) : $fr->threshold_amount.'件' }}</td>
                                <td class="px-4 py-2 text-xs text-muted-foreground">{{ ['','固定减','折扣','赠品'][$fr->reduction_type] ?? '-' }}</td>
                                <td class="px-4 py-2 text-right font-mono text-xs">
                                    @if($fr->reduction_type === 1)
                                        减 {{ money_format($fr->reduction_amount) }}
                                    @elseif($fr->reduction_type === 2)
                                        {{ round($fr->discount_rate / 100, 2) }}%
                                    @elseif($fr->reduction_type === 3)
                                        赠 {{ $fr->giftSku?->sku_code ?? "SKU#{$fr->gift_sku_id}" }} × {{ $fr->gift_quantity }}
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-center text-xs">{{ $fr->is_stacked ? '是' : '否' }}</td>
                                <td class="px-4 py-2 text-right">
                                    <div class="flex justify-end gap-1">
                                        <button type="button" wire:click="editFullReduction({{ $fr->id }})"
                                            class="inline-flex items-center justify-center rounded p-1 text-blue-600 hover:bg-blue-50 transition-colors">
                                            <x-ui.icon name="pencil-square" class="w-4 h-4" />
                                        </button>
                                        <button type="button" wire:click="confirmAction('deleteFullReduction', '确认删除此满减阶梯？', '删除后立即失效', {{ $fr->id }})"
                                            class="inline-flex items-center justify-center rounded p-1 text-red-500 hover:bg-red-50 transition-colors">
                                            <x-ui.icon name="trash" class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="px-6 py-12 text-center text-muted-foreground">暂无满减阶梯</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @endif

                {{-- 优惠券：券明细 --}}
                @if($detailPromoType === 3)
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-foreground">优惠券明细</h3>
                    <button type="button" wire:click="openCouponModal"
                        class="inline-flex items-center gap-1.5 rounded-md bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700 transition-colors">
                        <x-ui.icon name="plus" class="w-4 h-4" />
                        添加优惠券
                    </button>
                </div>
                <div class="rounded-lg border bg-card mb-6">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                <th class="px-4 py-2 text-left">券名</th>
                                <th class="px-4 py-2 text-left w-28">编码</th>
                                <th class="px-4 py-2 text-left w-20">类型</th>
                                <th class="px-4 py-2 text-right">面额/折扣</th>
                                <th class="px-4 py-2 text-right w-20">总量</th>
                                <th class="px-4 py-2 text-right w-20">已领取</th>
                                <th class="px-4 py-2 text-right w-20">已使用</th>
                                <th class="px-4 py-2 text-center w-16">状态</th>
                                <th class="px-4 py-2 text-right w-24">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($coupons as $coupon)
                            <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="coupon-{{ $coupon->id }}">
                                <td class="px-4 py-2 text-xs font-medium text-foreground">{{ $coupon->name }}</td>
                                <td class="px-4 py-2 text-xs font-mono text-muted-foreground">{{ $coupon->coupon_code }}</td>
                                <td class="px-4 py-2 text-xs text-muted-foreground">{{ $couponTypeMap[$coupon->coupon_type] ?? '-' }}</td>
                                <td class="px-4 py-2 text-right font-mono text-xs">
                                    @if(in_array($coupon->coupon_type, [1, 3]))
                                        减 {{ money_format($coupon->reduction_amount) }}
                                    @elseif($coupon->coupon_type === 2)
                                        {{ round($coupon->discount_rate / 100, 2) }}%
                                    @else
                                        运费券
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-right text-xs">{{ $coupon->total_quantity ?: '-' }}</td>
                                <td class="px-4 py-2 text-right text-xs text-muted-foreground">{{ $coupon->claimed_quantity }}</td>
                                <td class="px-4 py-2 text-right text-xs text-muted-foreground">{{ $coupon->used_quantity }}</td>
                                <td class="px-4 py-2 text-center">
                                    <button type="button" wire:click="toggleCouponStatus({{ $coupon->id }})"
                                        class="inline-flex items-center justify-center">
                                        <span class="inline-block h-4 w-4 rounded-full {{ $coupon->status ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                                    </button>
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <div class="flex justify-end gap-1">
                                        <button type="button" wire:click="editCoupon({{ $coupon->id }})"
                                            class="inline-flex items-center justify-center rounded p-1 text-blue-600 hover:bg-blue-50 transition-colors">
                                            <x-ui.icon name="pencil-square" class="w-4 h-4" />
                                        </button>
                                        <button type="button" wire:click="confirmAction('deleteCoupon', '确认删除此优惠券？', '删除后已领取的券仍可使用至过期', {{ $coupon->id }})"
                                            class="inline-flex items-center justify-center rounded p-1 text-red-500 hover:bg-red-50 transition-colors">
                                            <x-ui.icon name="trash" class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="9" class="px-6 py-12 text-center text-muted-foreground">暂无优惠券</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @endif

                {{-- 组合套餐：套餐明细（含子商品行） --}}
                @if($detailPromoType === 4)
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-foreground">组合套餐</h3>
                    <button type="button" wire:click="openBundleModal"
                        class="inline-flex items-center gap-1.5 rounded-md bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700 transition-colors">
                        <x-ui.icon name="plus" class="w-4 h-4" />
                        添加套餐
                    </button>
                </div>
                <div class="rounded-lg border bg-card mb-6">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                <th class="px-4 py-2 text-left">套餐名称</th>
                                <th class="px-4 py-2 text-left">套餐内容</th>
                                <th class="px-4 py-2 text-right w-24">套餐价</th>
                                <th class="px-4 py-2 text-right w-24">原价合计</th>
                                <th class="px-4 py-2 text-center w-16">状态</th>
                                <th class="px-4 py-2 text-right w-24">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bundles as $bundle)
                            <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="bundle-{{ $bundle->id }}">
                                <td class="px-4 py-2 text-xs font-medium text-foreground">{{ $bundle->bundle_name }}</td>
                                <td class="px-4 py-2 text-xs text-muted-foreground">
                                    {{ $bundle->items->sortBy('sort')->map(fn ($item) => ($item->sku?->sku_code ?? "SKU#{$item->sku_id}").'×'.$item->quantity)->implode(' + ') }}
                                </td>
                                <td class="px-4 py-2 text-right font-mono text-xs">{{ money_format($bundle->bundle_price) }}</td>
                                <td class="px-4 py-2 text-right font-mono text-xs text-muted-foreground">{{ money_format($bundle->original_total) }}</td>
                                <td class="px-4 py-2 text-center">
                                    <button type="button" wire:click="toggleBundleStatus({{ $bundle->id }})"
                                        class="inline-flex items-center justify-center">
                                        <span class="inline-block h-4 w-4 rounded-full {{ $bundle->status ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                                    </button>
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <div class="flex justify-end gap-1">
                                        <button type="button" wire:click="editBundle({{ $bundle->id }})"
                                            class="inline-flex items-center justify-center rounded p-1 text-blue-600 hover:bg-blue-50 transition-colors">
                                            <x-ui.icon name="pencil-square" class="w-4 h-4" />
                                        </button>
                                        <button type="button" wire:click="confirmAction('deleteBundle', '确认删除此套餐？', '删除后套餐及其明细一并移除', {{ $bundle->id }})"
                                            class="inline-flex items-center justify-center rounded p-1 text-red-500 hover:bg-red-50 transition-colors">
                                            <x-ui.icon name="trash" class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="px-6 py-12 text-center text-muted-foreground">暂无套餐</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @endif

                {{-- 秒杀：场次明细 --}}
                @if($detailPromoType === 7)
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-foreground">秒杀场次</h3>
                    <button type="button" wire:click="openFlashSaleModal"
                        class="inline-flex items-center gap-1.5 rounded-md bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700 transition-colors">
                        <x-ui.icon name="plus" class="w-4 h-4" />
                        添加场次
                    </button>
                </div>
                <div class="rounded-lg border bg-card mb-6">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                <th class="px-4 py-2 text-right">秒杀价</th>
                                <th class="px-4 py-2 text-right w-24">总库存</th>
                                <th class="px-4 py-2 text-right w-24">已售</th>
                                <th class="px-4 py-2 text-right w-20">每人限购</th>
                                <th class="px-4 py-2 text-left w-36">开始时间</th>
                                <th class="px-4 py-2 text-left w-36">结束时间</th>
                                <th class="px-4 py-2 text-center w-16">状态</th>
                                <th class="px-4 py-2 text-right w-24">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($flashSales as $fs)
                            <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="fs-{{ $fs->id }}">
                                <td class="px-4 py-2 text-right font-mono text-xs">{{ money_format($fs->flash_price) }}</td>
                                <td class="px-4 py-2 text-right text-xs">{{ $fs->total_stock }}</td>
                                <td class="px-4 py-2 text-right text-xs text-muted-foreground">{{ $fs->sold_stock }}</td>
                                <td class="px-4 py-2 text-right text-xs">{{ $fs->per_user_limit }}</td>
                                <td class="px-4 py-2 text-xs text-muted-foreground">{{ $fs->flash_start_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                <td class="px-4 py-2 text-xs text-muted-foreground">{{ $fs->flash_end_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                <td class="px-4 py-2 text-center">
                                    <button type="button" wire:click="toggleFlashSaleStatus({{ $fs->id }})"
                                        class="inline-flex items-center justify-center">
                                        <span class="inline-block h-4 w-4 rounded-full {{ $fs->status ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                                    </button>
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <div class="flex justify-end gap-1">
                                        <button type="button" wire:click="editFlashSale({{ $fs->id }})"
                                            class="inline-flex items-center justify-center rounded p-1 text-blue-600 hover:bg-blue-50 transition-colors">
                                            <x-ui.icon name="pencil-square" class="w-4 h-4" />
                                        </button>
                                        <button type="button" wire:click="confirmAction('deleteFlashSale', '确认删除此秒杀场次？', '删除后立即失效', {{ $fs->id }})"
                                            class="inline-flex items-center justify-center rounded p-1 text-red-500 hover:bg-red-50 transition-colors">
                                            <x-ui.icon name="trash" class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="px-6 py-12 text-center text-muted-foreground">暂无秒杀场次</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @endif

                {{-- 拼团：规则明细 --}}
                @if($detailPromoType === 6)
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-foreground">拼团规则</h3>
                    <button type="button" wire:click="openGroupBuyModal"
                        class="inline-flex items-center gap-1.5 rounded-md bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700 transition-colors">
                        <x-ui.icon name="plus" class="w-4 h-4" />
                        添加规则
                    </button>
                </div>
                <div class="rounded-lg border bg-card mb-6">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                <th class="px-4 py-2 text-right">拼团价</th>
                                <th class="px-4 py-2 text-right w-24">成团人数</th>
                                <th class="px-4 py-2 text-right w-24">最多人数</th>
                                <th class="px-4 py-2 text-right w-24">时限(分钟)</th>
                                <th class="px-4 py-2 text-center w-20">虚拟凑团</th>
                                <th class="px-4 py-2 text-center w-16">状态</th>
                                <th class="px-4 py-2 text-right w-24">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($groupBuys as $gb)
                            <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="gb-{{ $gb->id }}">
                                <td class="px-4 py-2 text-right font-mono text-xs">{{ money_format($gb->group_price) }}</td>
                                <td class="px-4 py-2 text-right text-xs">{{ $gb->min_group_size }}</td>
                                <td class="px-4 py-2 text-right text-xs">{{ $gb->max_group_size }}</td>
                                <td class="px-4 py-2 text-right text-xs">{{ $gb->time_limit }}</td>
                                <td class="px-4 py-2 text-center text-xs">{{ $gb->virtual_join ? '是' : '否' }}</td>
                                <td class="px-4 py-2 text-center">
                                    <button type="button" wire:click="toggleGroupBuyStatus({{ $gb->id }})"
                                        class="inline-flex items-center justify-center">
                                        <span class="inline-block h-4 w-4 rounded-full {{ $gb->status ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                                    </button>
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <div class="flex justify-end gap-1">
                                        <button type="button" wire:click="editGroupBuy({{ $gb->id }})"
                                            class="inline-flex items-center justify-center rounded p-1 text-blue-600 hover:bg-blue-50 transition-colors">
                                            <x-ui.icon name="pencil-square" class="w-4 h-4" />
                                        </button>
                                        <button type="button" wire:click="confirmAction('deleteGroupBuy', '确认删除此拼团规则？', '删除后立即失效', {{ $gb->id }})"
                                            class="inline-flex items-center justify-center rounded p-1 text-red-500 hover:bg-red-50 transition-colors">
                                            <x-ui.icon name="trash" class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="px-6 py-12 text-center text-muted-foreground">暂无拼团规则</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @endif

                {{-- 清仓临期：规则明细 --}}
                @if($detailPromoType === 5)
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-foreground">清仓临期规则</h3>
                    <button type="button" wire:click="openClearanceModal"
                        class="inline-flex items-center gap-1.5 rounded-md bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700 transition-colors">
                        <x-ui.icon name="plus" class="w-4 h-4" />
                        添加规则
                    </button>
                </div>
                <div class="rounded-lg border bg-card mb-6">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                                <th class="px-4 py-2 text-left w-20">类型</th>
                                <th class="px-4 py-2 text-left w-32">临期截止</th>
                                <th class="px-4 py-2 text-right">折扣率</th>
                                <th class="px-4 py-2 text-right">清仓价</th>
                                <th class="px-4 py-2 text-center w-16">状态</th>
                                <th class="px-4 py-2 text-right w-24">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($clearances as $cl)
                            <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="cl-{{ $cl->id }}">
                                <td class="px-4 py-2 text-xs text-muted-foreground">{{ $clearanceTypeMap[$cl->clearance_type] ?? '-' }}</td>
                                <td class="px-4 py-2 text-xs text-muted-foreground">{{ $cl->expiry_date?->format('Y-m-d') ?? '-' }}</td>
                                <td class="px-4 py-2 text-right font-mono text-xs">{{ $cl->discount_rate !== 10000 ? round($cl->discount_rate / 100, 2).'%' : '-' }}</td>
                                <td class="px-4 py-2 text-right font-mono text-xs">{{ $cl->fixed_price > 0 ? money_format($cl->fixed_price) : '-' }}</td>
                                <td class="px-4 py-2 text-center">
                                    <button type="button" wire:click="toggleClearanceStatus({{ $cl->id }})"
                                        class="inline-flex items-center justify-center">
                                        <span class="inline-block h-4 w-4 rounded-full {{ $cl->status ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                                    </button>
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <div class="flex justify-end gap-1">
                                        <button type="button" wire:click="editClearance({{ $cl->id }})"
                                            class="inline-flex items-center justify-center rounded p-1 text-blue-600 hover:bg-blue-50 transition-colors">
                                            <x-ui.icon name="pencil-square" class="w-4 h-4" />
                                        </button>
                                        <button type="button" wire:click="confirmAction('deleteClearance', '确认删除此清仓规则？', '删除后立即失效', {{ $cl->id }})"
                                            class="inline-flex items-center justify-center rounded p-1 text-red-500 hover:bg-red-50 transition-colors">
                                            <x-ui.icon name="trash" class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="px-6 py-12 text-center text-muted-foreground">暂无清仓规则</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @endif

                {{-- 会员折扣类型：提示到会员折扣分组维护 --}}
                @if($detailPromoType === 8)
                    <div class="rounded-lg border bg-card px-6 py-12 text-center text-muted-foreground">
                        会员折扣活动无需商品明细，折扣率请在「会员折扣」分组维护（限时折扣需关联此活动）
                    </div>
                @endif
                @endif
            </div>

            {{-- 活动商品弹窗 --}}
            @if($showDetailSkuModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
                <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4">
                    <div class="flex items-center justify-between px-6 py-4 border-b">
                        <h2 class="text-lg font-semibold text-foreground">{{ $editingDetailSkuId ? '编辑活动商品' : '添加活动商品' }}</h2>
                        <button type="button" wire:click="closeDetailSkuModal" class="text-muted-foreground hover:text-foreground transition-colors">
                            <x-ui.icon name="x" class="w-5 h-5" />
                        </button>
                    </div>
                    <div class="px-6 py-4 space-y-4">
                        <div>
                            <x-ui.searchable-select label="SKU *" wire:model="detailSkuFormSkuId" placeholder="搜索SKU编码/商品名..." wireModel="detailSkuFormSkuId" :value="$detailSkuFormSkuId" search="searchDetailSkuOptions" />
                            @error('detailSkuFormSkuId') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">定价方式 <span class="text-red-500">*</span></label>
                                <select wire:model.live="detailSkuFormPriceType"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                                    @foreach($detailPromoTypeMap as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if($detailSkuFormPriceType === 1)
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">促销价（元） <span class="text-red-500">*</span></label>
                                <input type="number" wire:model="detailSkuFormFixedPrice" min="0" step="0.001"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="0.000" />
                                @error('detailSkuFormFixedPrice') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            @else
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">折扣率（%） <span class="text-red-500">*</span></label>
                                <input type="number" wire:model="detailSkuFormDiscountRate" min="0" max="100" step="0.01"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="95 = 95折" />
                                <p class="text-xs text-muted-foreground mt-1">按零售价百分比计价，如 95 = 95折</p>
                                @error('detailSkuFormDiscountRate') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            @endif
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">限购数量</label>
                                <input type="number" wire:model="detailSkuFormMaxQuantity" min="0"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="0=不限" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">每人限购</label>
                                <input type="number" wire:model="detailSkuFormMaxPerCustomer" min="0"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="0=不限" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">库存限量</label>
                                <input type="number" wire:model="detailSkuFormStockLimit" min="0"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="0=不限" />
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 px-6 py-4 border-t">
                        <button type="button" wire:click="closeDetailSkuModal"
                            class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                        <button type="button" wire:click="saveDetailSku"
                            class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">{{ $editingDetailSkuId ? '更新' : '创建' }}</button>
                    </div>
                </div>
            </div>
            @endif

            {{-- 满减阶梯弹窗 --}}
            @if($showFullReductionModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
                <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4">
                    <div class="flex items-center justify-between px-6 py-4 border-b">
                        <h2 class="text-lg font-semibold text-foreground">{{ $editingFullReductionId ? '编辑满减阶梯' : '添加满减阶梯' }}</h2>
                        <button type="button" wire:click="closeFullReductionModal" class="text-muted-foreground hover:text-foreground transition-colors">
                            <x-ui.icon name="x" class="w-5 h-5" />
                        </button>
                    </div>
                    <div class="px-6 py-4 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">门槛类型 <span class="text-red-500">*</span></label>
                                <select wire:model.live="fullReductionFormThresholdType"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                                    <option value="1">按金额</option>
                                    <option value="2">按件数</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">
                                    门槛值 <span class="text-red-500">*</span>
                                    <span class="text-xs text-muted-foreground font-normal">（{{ $fullReductionFormThresholdType === 1 ? '元' : '件' }}）</span>
                                </label>
                                <input type="number" wire:model="fullReductionFormThresholdAmount" min="0" step="0.001"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                                @error('fullReductionFormThresholdAmount') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">减免方式 <span class="text-red-500">*</span></label>
                                <select wire:model.live="fullReductionFormReductionType"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                                    <option value="1">固定减</option>
                                    <option value="2">折扣</option>
                                    <option value="3">赠品</option>
                                </select>
                            </div>
                            @if($fullReductionFormReductionType === 1)
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">减免金额（元） <span class="text-red-500">*</span></label>
                                <input type="number" wire:model="fullReductionFormReductionAmount" min="0" step="0.001"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                                @error('fullReductionFormReductionAmount') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            @elseif($fullReductionFormReductionType === 2)
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">折扣率（%） <span class="text-red-500">*</span></label>
                                <input type="number" wire:model="fullReductionFormDiscountRate" min="0" max="100" step="0.01"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="95 = 95折" />
                                @error('fullReductionFormDiscountRate') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            @else
                            <div class="grid grid-cols-2 gap-2">
                                <div class="col-span-2">
                                    <x-ui.searchable-select label="赠品SKU *" wire:model="fullReductionFormGiftSkuId" placeholder="搜索SKU..." wireModel="fullReductionFormGiftSkuId" :value="$fullReductionFormGiftSkuId" search="searchDetailSkuOptions" />
                                    @error('fullReductionFormGiftSkuId') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-foreground mb-1">赠品数量</label>
                                    <input type="number" wire:model="fullReductionFormGiftQuantity" min="0"
                                        class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                                </div>
                            </div>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1">是否可叠加</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="radio" wire:model="fullReductionFormIsStacked" value="0" class="h-4 w-4 text-blue-600" />
                                    不可叠加
                                </label>
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="radio" wire:model="fullReductionFormIsStacked" value="1" class="h-4 w-4 text-blue-600" />
                                    可叠加
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 px-6 py-4 border-t">
                        <button type="button" wire:click="closeFullReductionModal"
                            class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                        <button type="button" wire:click="saveFullReduction"
                            class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">{{ $editingFullReductionId ? '更新' : '创建' }}</button>
                    </div>
                </div>
            </div>
            @endif

            {{-- 优惠券弹窗 --}}
            @if($showCouponModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
                <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4">
                    <div class="flex items-center justify-between px-6 py-4 border-b">
                        <h2 class="text-lg font-semibold text-foreground">{{ $editingCouponId ? '编辑优惠券' : '添加优惠券' }}</h2>
                        <button type="button" wire:click="closeCouponModal" class="text-muted-foreground hover:text-foreground transition-colors">
                            <x-ui.icon name="x" class="w-5 h-5" />
                        </button>
                    </div>
                    <div class="px-6 py-4 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">券名 <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="couponFormName"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                                @error('couponFormName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">券编码 <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="couponFormCouponCode"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                                @error('couponFormCouponCode') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1">券类型 <span class="text-red-500">*</span></label>
                            <select wire:model.live="couponFormType"
                                class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                                @foreach($couponTypeMap as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">使用门槛（元）</label>
                                <input type="number" wire:model="couponFormThresholdAmount" min="0" step="0.001"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="0=无门槛" />
                            </div>
                            @if(in_array($couponFormType, [1, 3]))
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">抵扣金额（元） <span class="text-red-500">*</span></label>
                                <input type="number" wire:model="couponFormReductionAmount" min="0" step="0.001"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                                @error('couponFormReductionAmount') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            @elseif($couponFormType === 2)
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">折扣率（%） <span class="text-red-500">*</span></label>
                                <input type="number" wire:model="couponFormDiscountRate" min="0" max="100" step="0.01"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="95 = 95折" />
                                @error('couponFormDiscountRate') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            @endif
                        </div>
                        @if($couponFormType === 2)
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1">最大优惠上限（元）</label>
                            <input type="number" wire:model="couponFormMaxDiscount" min="0" step="0.001"
                                class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="0=不限" />
                        </div>
                        @endif
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">发放总量</label>
                                <input type="number" wire:model="couponFormTotalQuantity" min="0"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="0=不限" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">每人限领</label>
                                <input type="number" wire:model="couponFormPerUserLimit" min="1"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">有效天数</label>
                                <input type="number" wire:model="couponFormValidDays" min="1"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 px-6 py-4 border-t">
                        <button type="button" wire:click="closeCouponModal"
                            class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                        <button type="button" wire:click="saveCoupon"
                            class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">{{ $editingCouponId ? '更新' : '创建' }}</button>
                    </div>
                </div>
            </div>
            @endif

            {{-- 组合套餐弹窗（含明细行） --}}
            @if($showBundleModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
                <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-2xl mx-4">
                    <div class="flex items-center justify-between px-6 py-4 border-b">
                        <h2 class="text-lg font-semibold text-foreground">{{ $editingBundleId ? '编辑套餐' : '添加套餐' }}</h2>
                        <button type="button" wire:click="closeBundleModal" class="text-muted-foreground hover:text-foreground transition-colors">
                            <x-ui.icon name="x" class="w-5 h-5" />
                        </button>
                    </div>
                    <div class="px-6 py-4 space-y-4">
                        <div class="grid grid-cols-3 gap-4">
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-foreground mb-1">套餐名称 <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="bundleFormName"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                                @error('bundleFormName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">每组件数</label>
                                <input type="number" wire:model="bundleFormQuantity" min="1"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1">套餐价（元） <span class="text-red-500">*</span></label>
                            <input type="number" wire:model="bundleFormPrice" min="0" step="0.001"
                                class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                            <p class="text-xs text-muted-foreground mt-1">原价合计按明细 SKU 零售价自动计算</p>
                            @error('bundleFormPrice') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-medium text-foreground">套餐明细 <span class="text-red-500">*</span></label>
                                <button type="button" wire:click="addBundleItemRow"
                                    class="rounded-md border border-blue-300 px-2 py-1 text-xs text-blue-600 hover:bg-blue-50 transition-colors">添加商品行</button>
                            </div>
                            @foreach($bundleFormItems as $i => $row)
                            <div class="flex gap-2 mb-2" wire:key="bundle-item-{{ $i }}">
                                <div class="flex-1">
                                    <x-ui.searchable-select wire:model="bundleFormItems.{{ $i }}.sku_id" placeholder="搜索SKU..." wireModel="bundleFormItems.{{ $i }}.sku_id" :value="$row['sku_id']" search="searchDetailSkuOptions" button-class="h-9 px-2" />
                                </div>
                                <input type="number" wire:model="bundleFormItems.{{ $i }}.quantity" min="1"
                                    class="flex h-9 w-24 rounded-md border border-input bg-background px-3 text-sm" placeholder="数量" />
                                <button type="button" wire:click="removeBundleItemRow({{ $i }})"
                                    class="shrink-0 inline-flex items-center justify-center rounded-md border border-input px-2 text-muted-foreground hover:text-red-500 hover:border-red-300 transition-colors">
                                    <x-ui.icon name="trash" class="w-4 h-4" />
                                </button>
                            </div>
                            @error('bundleFormItems.'.$i.'.sku_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            @endforeach
                            @error('bundleFormItems') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 px-6 py-4 border-t">
                        <button type="button" wire:click="closeBundleModal"
                            class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                        <button type="button" wire:click="saveBundle"
                            class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">{{ $editingBundleId ? '更新' : '创建' }}</button>
                    </div>
                </div>
            </div>
            @endif

            {{-- 秒杀弹窗 --}}
            @if($showFlashSaleModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
                <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4">
                    <div class="flex items-center justify-between px-6 py-4 border-b">
                        <h2 class="text-lg font-semibold text-foreground">{{ $editingFlashSaleId ? '编辑秒杀场次' : '添加秒杀场次' }}</h2>
                        <button type="button" wire:click="closeFlashSaleModal" class="text-muted-foreground hover:text-foreground transition-colors">
                            <x-ui.icon name="x" class="w-5 h-5" />
                        </button>
                    </div>
                    <div class="px-6 py-4 space-y-4">
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">秒杀价（元） <span class="text-red-500">*</span></label>
                                <input type="number" wire:model="flashSaleFormPrice" min="0" step="0.001"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                                @error('flashSaleFormPrice') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">总库存 <span class="text-red-500">*</span></label>
                                <input type="number" wire:model="flashSaleFormTotalStock" min="1"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                                @error('flashSaleFormTotalStock') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">每人限购</label>
                                <input type="number" wire:model="flashSaleFormPerUserLimit" min="1"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">秒杀开始时间</label>
                                <input type="datetime-local" wire:model="flashSaleFormStartAt"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">秒杀结束时间</label>
                                <input type="datetime-local" wire:model="flashSaleFormEndAt"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                                @error('flashSaleFormEndAt') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 px-6 py-4 border-t">
                        <button type="button" wire:click="closeFlashSaleModal"
                            class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                        <button type="button" wire:click="saveFlashSale"
                            class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">{{ $editingFlashSaleId ? '更新' : '创建' }}</button>
                    </div>
                </div>
            </div>
            @endif

            {{-- 拼团弹窗 --}}
            @if($showGroupBuyModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
                <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4">
                    <div class="flex items-center justify-between px-6 py-4 border-b">
                        <h2 class="text-lg font-semibold text-foreground">{{ $editingGroupBuyId ? '编辑拼团规则' : '添加拼团规则' }}</h2>
                        <button type="button" wire:click="closeGroupBuyModal" class="text-muted-foreground hover:text-foreground transition-colors">
                            <x-ui.icon name="x" class="w-5 h-5" />
                        </button>
                    </div>
                    <div class="px-6 py-4 space-y-4">
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">拼团价（元） <span class="text-red-500">*</span></label>
                                <input type="number" wire:model="groupBuyFormPrice" min="0" step="0.001"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                                @error('groupBuyFormPrice') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">成团人数 <span class="text-red-500">*</span></label>
                                <input type="number" wire:model="groupBuyFormMinSize" min="2"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                                @error('groupBuyFormMinSize') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">最多人数</label>
                                <input type="number" wire:model="groupBuyFormMaxSize" min="2"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                                @error('groupBuyFormMaxSize') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">拼团时限（分钟）</label>
                                <input type="number" wire:model="groupBuyFormTimeLimit" min="1"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">虚拟凑团</label>
                                <div class="flex gap-4">
                                    <label class="flex items-center gap-2 text-sm">
                                        <input type="radio" wire:model="groupBuyFormVirtualJoin" value="0" class="h-4 w-4 text-blue-600" />
                                        否
                                    </label>
                                    <label class="flex items-center gap-2 text-sm">
                                        <input type="radio" wire:model="groupBuyFormVirtualJoin" value="1" class="h-4 w-4 text-blue-600" />
                                        是
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 px-6 py-4 border-t">
                        <button type="button" wire:click="closeGroupBuyModal"
                            class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                        <button type="button" wire:click="saveGroupBuy"
                            class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">{{ $editingGroupBuyId ? '更新' : '创建' }}</button>
                    </div>
                </div>
            </div>
            @endif

            {{-- 清仓临期弹窗 --}}
            @if($showClearanceModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
                <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4">
                    <div class="flex items-center justify-between px-6 py-4 border-b">
                        <h2 class="text-lg font-semibold text-foreground">{{ $editingClearanceId ? '编辑清仓规则' : '添加清仓规则' }}</h2>
                        <button type="button" wire:click="closeClearanceModal" class="text-muted-foreground hover:text-foreground transition-colors">
                            <x-ui.icon name="x" class="w-5 h-5" />
                        </button>
                    </div>
                    <div class="px-6 py-4 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">类型 <span class="text-red-500">*</span></label>
                                <select wire:model.live="clearanceFormType"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                                    @foreach($clearanceTypeMap as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if($clearanceFormType === 2)
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">临期截止日期 <span class="text-red-500">*</span></label>
                                <input type="date" wire:model="clearanceFormExpiryDate"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                                @error('clearanceFormExpiryDate') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            @endif
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">折扣率（%）</label>
                                <input type="number" wire:model="clearanceFormDiscountRate" min="0" max="100" step="0.01"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="95 = 95折" />
                                <p class="text-xs text-muted-foreground mt-1">与清仓价至少填一项</p>
                                @error('clearanceFormDiscountRate') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1">清仓价（元）</label>
                                <input type="number" wire:model="clearanceFormFixedPrice" min="0" step="0.001"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" />
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 px-6 py-4 border-t">
                        <button type="button" wire:click="closeClearanceModal"
                            class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                        <button type="button" wire:click="saveClearance"
                            class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">{{ $editingClearanceId ? '更新' : '创建' }}</button>
                    </div>
                </div>
            </div>
            @endif
            @endif

            {{-- ════════════════════════════════════════════ --}}
            {{-- 分组3: 商家价格 --}}
            {{-- ════════════════════════════════════════════ --}}
            @if($activeGroup === 'store')
            <div>
                {{-- 工具栏 --}}
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <input type="text" wire:model.live="storePriceSearch" placeholder="搜索SKU编码..."
                            class="flex h-8 w-48 rounded-md border border-input bg-background px-3 text-sm" />
                        <x-ui.searchable-select wire:model.live="storePriceMerchantFilter" placeholder="全部商家" wireModel="storePriceMerchantFilter" :value="$storePriceMerchantFilter" :selected-label="$storePriceFilterMerchantName" search="searchMerchantOptions" clearable button-class="h-8 px-2 w-40" />
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
                                            <x-ui.icon name="pencil-square" class="w-4 h-4" />
                                        </button>
                                        @can('price.promotion.delete')
                                        <button type="button" wire:click="confirmAction('deleteStorePrice', '确认删除此门店价格？', '删除后恢复默认价格策略', {{ $sp->id }})"
                                            class="inline-flex items-center justify-center rounded p-1 text-red-500 hover:bg-red-50 transition-colors">
                                            <x-ui.icon name="trash" class="w-4 h-4" />
                                        </button>
                                        @endcan
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
                            <x-ui.searchable-select label="商家 *" wire:model="storePriceFormMerchantId" placeholder="搜索商家..." wireModel="storePriceFormMerchantId" :value="$storePriceFormMerchantId" :selected-label="$storePriceFormMerchant?->name ?? ''" search="searchMerchantOptions" />
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
                                            <x-ui.icon name="pencil-square" class="w-4 h-4" />
                                        </button>
                                        @can('price.promotion.delete')
                                        <button type="button" wire:click="confirmAction('deleteMemberDiscount', '确认删除此会员折扣？', '删除后立即失效', {{ $md->id }})"
                                            class="inline-flex items-center justify-center rounded p-1 text-red-500 hover:bg-red-50 transition-colors">
                                            <x-ui.icon name="trash" class="w-4 h-4" />
                                        </button>
                                        @endcan
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

    @include('partials.action-confirm')
</div>
