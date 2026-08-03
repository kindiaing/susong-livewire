<div>
    {{-- 页面标题 --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-foreground">取价配置</h1>
        <p class="text-muted-foreground mt-1">配置取价模式、来源开关和优先级顺序，修改后即时生效</p>
    </div>

    <div class="space-y-6 max-w-4xl">

        {{-- 取价模式 --}}
        <div class="rounded-lg border bg-card px-5 pt-5 pb-4">
            <h2 class="text-base font-semibold text-foreground mb-1">取价模式</h2>
            <p class="text-xs text-muted-foreground mb-4">选择系统计算商品售价的策略</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- 最低价模式 --}}
                <button type="button" wire:click="requestModeSwitch('lowest')"
                    class="relative text-left rounded-lg border-2 p-4 transition-all {{ $pricingMode === 'lowest' ? 'border-blue-500 bg-blue-50/50' : 'border-border hover:border-blue-300' }}">
                    @if($pricingMode === 'lowest')
                        <div class="absolute top-2 right-2">
                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        </div>
                    @endif
                    <div class="font-medium text-foreground text-sm">最低价模式</div>
                    <div class="text-xs text-muted-foreground mt-2 leading-relaxed">
                        所有启用的固定价来源取<strong>最低值</strong>，再叠乘会员折扣率。消费者最友好，推荐日常使用。
                    </div>
                    <div class="mt-3 text-[11px] font-mono text-muted-foreground/70 bg-muted/50 rounded px-2 py-1">
                        促销80 + 零售100 + 会员9折 &rarr; min(80,100) &times; 0.9 = 72
                    </div>
                </button>

                {{-- 命中即止模式 --}}
                <button type="button" wire:click="requestModeSwitch('first_hit')"
                    class="relative text-left rounded-lg border-2 p-4 transition-all {{ $pricingMode === 'first_hit' ? 'border-blue-500 bg-blue-50/50' : 'border-border hover:border-blue-300' }}">
                    @if($pricingMode === 'first_hit')
                        <div class="absolute top-2 right-2">
                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        </div>
                    @endif
                    <div class="font-medium text-foreground text-sm">命中即止模式</div>
                    <div class="text-xs text-muted-foreground mt-2 leading-relaxed">
                        按优先级从高到低遍历，第一个有值的来源即最终价。严格控价，促销与会员互斥。
                    </div>
                    <div class="mt-3 text-[11px] font-mono text-muted-foreground/70 bg-muted/50 rounded px-2 py-1">
                        促销80 + 零售100 + 会员9折 &rarr; 促销命中 = 80（会员跳过）
                    </div>
                </button>
            </div>
        </div>

        {{-- 来源开关 --}}
        <div class="rounded-lg border bg-card px-5 pt-5 pb-4">
            <h2 class="text-base font-semibold text-foreground mb-1">取价来源开关</h2>
            <p class="text-xs text-muted-foreground mb-4">关闭某个来源后，该来源不参与取价计算（标准零售价不可关闭）</p>

            <div class="space-y-2">
                @foreach($sourceLabels as $key => $label)
                    <div class="flex items-center justify-between rounded-md px-3 py-2 {{ $key === 'retail' ? 'bg-muted/40' : 'hover:bg-muted/20' }} transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-medium text-foreground">{{ $label }}</span>
                            @if($key === 'retail')
                                <span class="text-[10px] text-muted-foreground bg-muted rounded px-1.5 py-0.5">兜底必选</span>
                            @endif
                        </div>
                        <button type="button"
                            wire:click="toggleSource('{{ $key }}')"
                            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ ($sourceEnabled[$key] ?? true) ? 'bg-blue-600' : 'bg-gray-200' }}"
                            @if($key === 'retail') disabled @endif
                        >
                            <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ ($sourceEnabled[$key] ?? true) ? 'translate-x-5' : 'translate-x-0' }}"></span>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- 优先级排序（仅命中即止模式） --}}
        <div class="rounded-lg border bg-card {{ $pricingMode === 'first_hit' ? '' : 'opacity-50 pointer-events-none' }}">
            <div class="px-5 pt-5 pb-2">
                <h2 class="text-base font-semibold text-foreground mb-1">取价优先级</h2>
                <p class="text-xs text-muted-foreground">
                    仅<strong>命中即止模式</strong>下生效。修改排序号后点击「保存排序」，系统按排序号从小到大排列。
                </p>
            </div>

            <div class="space-y-1 px-5 pb-2">
                @foreach($pricingPriority as $index => $source)
                    <div class="flex items-center gap-3 rounded-md px-3 py-2 hover:bg-muted/20 transition-colors" wire:key="priority-{{ $source }}">
                        {{-- 排序号输入 --}}
                        <input type="number" min="1" max="99"
                            wire:model.live="prioritySortNumbers.{{ $source }}"
                            class="flex h-7 w-14 rounded-md border border-input bg-background px-2 text-xs text-center font-mono"
                        />

                        {{-- 来源名称 --}}
                        <span class="text-sm font-medium text-foreground">{{ $sourceLabels[$source] ?? $source }}</span>

                        {{-- 状态标注 --}}
                        @php $currentSort = (int) ($prioritySortNumbers[$source] ?? $index + 1); @endphp
                        @if($currentSort === 1)
                            <span class="text-[10px] text-orange-600 bg-orange-50 rounded px-1.5 py-0.5">最高优先</span>
                        @elseif($source === 'retail')
                            <span class="text-[10px] text-muted-foreground bg-muted rounded px-1.5 py-0.5">兜底</span>
                        @endif

                        {{-- 已关闭标记 --}}
                        @if(!($sourceEnabled[$source] ?? true))
                            <span class="text-[10px] text-red-500 bg-red-50 rounded px-1.5 py-0.5">已关闭</span>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- 保存排序按钮 --}}
            <div class="px-5 py-3 border-t flex justify-end">
                <button type="button" wire:click="savePrioritySort" class="rounded px-3 py-1.5 text-xs font-medium bg-blue-600 text-white hover:bg-blue-700 transition-colors">
                    保存排序
                </button>
            </div>

            @if($pricingMode !== 'first_hit')
                <div class="px-5 pb-4 text-xs text-muted-foreground">
                    当前为最低价模式，优先级排序不生效。请先切换到「命中即止模式」。
                </div>
            @endif
        </div>

        {{-- 常见配置组合速查 --}}
        <div class="rounded-lg border bg-card px-5 pt-5 pb-4">
            <h2 class="text-base font-semibold text-foreground mb-1">常见配置组合速查</h2>
            <p class="text-xs text-muted-foreground mb-4">根据业务场景快速选择配置</p>

            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-b text-muted-foreground">
                            <th class="px-2 py-1.5 text-left font-medium">场景</th>
                            <th class="px-2 py-1.5 text-left font-medium">模式</th>
                            <th class="px-2 py-1.5 text-left font-medium">启用来源</th>
                            <th class="px-2 py-1.5 text-left font-medium">推荐理由</th>
                        </tr>
                    </thead>
                    <tbody class="text-foreground">
                        <tr class="border-b hover:bg-muted/20">
                            <td class="px-2 py-1.5 font-medium">初期上线</td>
                            <td class="px-2 py-1.5"><span class="text-blue-600">最低价</span></td>
                            <td class="px-2 py-1.5">促销+渠道+会员</td>
                            <td class="px-2 py-1.5">消费者体验最好</td>
                        </tr>
                        <tr class="border-b hover:bg-muted/20">
                            <td class="px-2 py-1.5 font-medium">成本敏感期</td>
                            <td class="px-2 py-1.5"><span class="text-orange-600">命中即止</span></td>
                            <td class="px-2 py-1.5">促销>渠道>零售</td>
                            <td class="px-2 py-1.5">严格控价，防双重优惠</td>
                        </tr>
                        <tr class="border-b hover:bg-muted/20">
                            <td class="px-2 py-1.5 font-medium">门店加盟</td>
                            <td class="px-2 py-1.5"><span class="text-orange-600">命中即止</span></td>
                            <td class="px-2 py-1.5">门店>促销>渠道>零售</td>
                            <td class="px-2 py-1.5">门店自主定价权最大</td>
                        </tr>
                        <tr class="border-b hover:bg-muted/20">
                            <td class="px-2 py-1.5 font-medium">促销大促</td>
                            <td class="px-2 py-1.5"><span class="text-blue-600">最低价</span></td>
                            <td class="px-2 py-1.5">促销+渠道+零售（关会员）</td>
                            <td class="px-2 py-1.5">促销已够优惠，关会员防漏</td>
                        </tr>
                        <tr class="hover:bg-muted/20">
                            <td class="px-2 py-1.5 font-medium">纯渠道定价</td>
                            <td class="px-2 py-1.5"><span class="text-blue-600">最低价 / 命中即止</span></td>
                            <td class="px-2 py-1.5">渠道+零售</td>
                            <td class="px-2 py-1.5">无促销无会员的简单模式</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 底部状态 + 重置 --}}
        <div class="flex items-center justify-between">
            <div class="text-xs text-muted-foreground">
                当前模式：<span class="font-medium text-foreground">{{ $pricingMode === 'lowest' ? '最低价模式' : '命中即止模式' }}</span>
                &middot; 启用来源：<span class="font-medium text-foreground">{{ collect($sourceEnabled)->filter()->count() }} / {{ count($sourceLabels) }}</span>
            </div>
            <button type="button" wire:click="openResetConfirm" class="rounded px-3 py-1.5 text-xs font-medium text-orange-600 hover:bg-orange-50 transition-colors">重置为默认值</button>
        </div>
    </div>

    {{-- 模式切换确认弹窗 --}}
    @if($showModeSwitchConfirm)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-sm mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-2">确认切换取价模式</h2>
            <p class="text-sm text-muted-foreground mb-1">
                将从 <strong>{{ $pricingMode === 'lowest' ? '最低价模式' : '命中即止模式' }}</strong>
                切换为 <strong>{{ $pendingMode === 'lowest' ? '最低价模式' : '命中即止模式' }}</strong>
            </p>
            @if($pendingMode === 'lowest')
                <p class="text-xs text-muted-foreground">切换后：所有固定价来源取最低值，再叠乘会员折扣率。消费者更友好。</p>
            @else
                <p class="text-xs text-muted-foreground">切换后：按优先级遍历取价，促销与会员互斥。严格控价，防止双重优惠。</p>
            @endif
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="cancelModeSwitch" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="confirmModeSwitch" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">确认切换</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 重置确认弹窗 --}}
    @if($showResetConfirm)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-sm mx-4 p-6">
            <h2 class="text-lg font-semibold text-foreground mb-2">重置取价配置</h2>
            <p class="text-sm text-muted-foreground mb-6">确定要将取价模式、来源开关和优先级顺序全部恢复为默认值吗？此操作不可撤销。</p>
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="closeResetConfirm" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="resetToDefault" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">确认重置</button>
            </div>
        </div>
    </div>
    @endif
</div>
