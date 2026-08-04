<div>
    {{-- 页面标题 --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-foreground">财务配置</h1>
        <p class="text-muted-foreground mt-1">取价模式、财务风控、金额精度与费用均摊规则，修改后即时生效</p>
    </div>

    {{-- Toast 消息 --}}
    @if(session('success'))
        <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @error('editingValue')
        <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ $message }}
        </div>
    @enderror

    {{-- Tab 导航 --}}
    <div class="mb-6 border-b">
        <nav class="flex gap-6 -mb-px">
            @foreach([
                'pricing' => '取价配置',
                'finance' => '财务风控',
                'money' => '金额精度',
            ] as $tabKey => $tabLabel)
                <button type="button" wire:click="setActiveTab('{{ $tabKey }}')"
                    class="pb-3 text-sm font-medium border-b-2 transition-colors {{ $activeTab === $tabKey ? 'border-blue-600 text-blue-600' : 'border-transparent text-muted-foreground hover:text-foreground hover:border-border' }}">
                    {{ $tabLabel }}
                </button>
            @endforeach
            {{-- 费用均摊跳转链接 --}}
            <a href="{{ route('price-apportionments') }}" wire:navigate
                class="pb-3 text-sm font-medium border-b-2 border-transparent text-muted-foreground hover:text-foreground hover:border-border transition-colors">
                费用均摊
                <span class="text-[10px] ml-1 text-muted-foreground/60">→</span>
            </a>
        </nav>
    </div>

    {{-- ════════════════════════════════════════════ --}}
    {{-- Tab 1: 取价配置 --}}
    {{-- ════════════════════════════════════════════ --}}
    @if($activeTab === 'pricing')
    <div class="space-y-6 max-w-4xl">

        {{-- 取价模式 --}}
        <div class="rounded-lg border bg-card p-6 overflow-hidden">
            <h2 class="text-base font-semibold text-foreground mb-1">取价模式</h2>
            <p class="text-xs text-muted-foreground mb-4">选择系统计算商品售价的策略</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
        <div class="rounded-lg border bg-card p-6 overflow-hidden">
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
                        <input type="number" min="1" max="99"
                            wire:model.live="prioritySortNumbers.{{ $source }}"
                            class="flex h-7 w-14 rounded-md border border-input bg-background px-2 text-xs text-center font-mono"
                        />
                        <span class="text-sm font-medium text-foreground">{{ $sourceLabels[$source] ?? $source }}</span>
                        @php $currentSort = (int) ($prioritySortNumbers[$source] ?? $index + 1); @endphp
                        @if($currentSort === 1)
                            <span class="text-[10px] text-orange-600 bg-orange-50 rounded px-1.5 py-0.5">最高优先</span>
                        @elseif($source === 'retail')
                            <span class="text-[10px] text-muted-foreground bg-muted rounded px-1.5 py-0.5">兜底</span>
                        @endif
                        @if(!($sourceEnabled[$source] ?? true))
                            <span class="text-[10px] text-red-500 bg-red-50 rounded px-1.5 py-0.5">已关闭</span>
                        @endif
                    </div>
                @endforeach
            </div>

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
        <div class="rounded-lg border bg-card p-6 overflow-hidden">
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
    @endif

    {{-- ════════════════════════════════════════════ --}}
    {{-- Tab 2: 财务风控 --}}
    {{-- ════════════════════════════════════════════ --}}
    @if($activeTab === 'finance')
    <div class="max-w-4xl">
        <div class="rounded-lg border bg-card">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                        <th class="px-4 py-2 text-left">配置项</th>
                        <th class="px-4 py-2 text-left w-[200px]">当前值</th>
                        <th class="px-4 py-2 text-left w-20">类型</th>
                        <th class="px-4 py-2 text-right w-24">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($financeConfigs as $config)
                    <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="finance-{{ $config->id }}">
                        <td class="px-4 py-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium text-foreground">{{ $config->label }}</span>
                                    @if($config->is_readonly)
                                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-medium bg-muted text-muted-foreground">只读</span>
                                    @endif
                                </div>
                                @if($config->hint)
                                    <p class="text-xs text-muted-foreground mt-0.5">{{ $config->hint }}</p>
                                @endif
                                <p class="text-[11px] text-muted-foreground/60 mt-0.5 font-mono">{{ $config->config_key }}</p>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @if($editingId === $config->id)
                                @if($config->config_type === 'boolean')
                                    <select wire:model="editingValue" class="flex h-8 w-full rounded-md border border-input bg-background px-2 text-sm">
                                        <option value="1">开启</option>
                                        <option value="0">关闭</option>
                                    </select>
                                @elseif($config->config_type === 'enum' && $config->options)
                                    <select wire:model="editingValue" class="flex h-8 w-full rounded-md border border-input bg-background px-2 text-sm">
                                        @foreach($config->options as $opt)
                                            <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="{{ $config->config_type === 'integer' || $config->config_type === 'decimal' ? 'number' : 'text' }}"
                                        wire:model="editingValue"
                                        class="flex h-8 w-full rounded-md border border-input bg-background px-2 text-sm"
                                        @if($config->config_type === 'decimal') step="0.01" @endif
                                    />
                                @endif
                            @else
                                @if($config->config_type === 'boolean')
                                    {!! status_badge($config->config_value == '1' ? 1 : 0, 'active') !!}
                                @elseif($config->config_type === 'enum' && $config->options)
                                    @php
                                        $currentOpt = collect($config->options)->first(fn($o) => $o['value'] == $config->config_value)
                                    @endphp
                                    <span class="text-sm text-foreground">{{ $currentOpt['label'] ?? $config->config_value }}</span>
                                @else
                                    <span class="text-sm text-foreground font-mono">{{ $config->config_value }}</span>
                                @endif
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-muted text-muted-foreground">
                                {{ $config->config_type }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-1">
                                @if($editingId === $config->id)
                                    <button type="button" wire:click="saveEdit"
                                        class="inline-flex items-center justify-center rounded px-2 py-1 text-xs font-medium bg-blue-600 text-white hover:bg-blue-700 transition-colors">
                                        保存
                                    </button>
                                    <button type="button" wire:click="cancelEdit"
                                        class="inline-flex items-center justify-center rounded px-2 py-1 text-xs font-medium bg-muted text-muted-foreground hover:bg-muted/80 transition-colors">
                                        取消
                                    </button>
                                @else
                                    @if(!$config->is_readonly)
                                        <button type="button" wire:click="startEdit({{ $config->id }})"
                                            class="inline-flex items-center justify-center rounded px-2 py-1 text-xs font-medium text-blue-600 hover:bg-blue-50 transition-colors">
                                            编辑
                                        </button>
                                    @endif
                                    @if($config->default_value && $config->config_value !== $config->default_value && !$config->is_readonly)
                                        <button type="button" wire:click="resetToDefaultItem({{ $config->id }})"
                                            class="inline-flex items-center justify-center rounded px-2 py-1 text-xs font-medium text-orange-600 hover:bg-orange-50 transition-colors"
                                            onclick="return confirm('确认重置为默认值？')">
                                            重置
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-6 py-12 text-center text-muted-foreground">该分组暂无配置项</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 text-xs text-muted-foreground">共 {{ $financeConfigs->count() }} 项</div>
    </div>
    @endif

    {{-- ════════════════════════════════════════════ --}}
    {{-- Tab 3: 金额精度 --}}
    {{-- ════════════════════════════════════════════ --}}
    @if($activeTab === 'money')
    <div class="max-w-4xl">
        {{-- 说明卡片 --}}
        <div class="mb-6 rounded-lg border border-blue-200 bg-blue-50/50 p-4 text-sm text-blue-800">
            <p class="font-medium mb-1">金额三层分离架构</p>
            <ul class="list-disc list-inside text-xs space-y-1 text-blue-700">
                <li><strong>存储层</strong>：所有金额以「厘」为单位 BIGINT 存储，零精度损失</li>
                <li><strong>计算层</strong>：后端运算全部使用整数（厘），无浮点误差</li>
                <li><strong>显示层</strong>：通过 <code class="bg-blue-100 px-1 rounded">money_format()</code> 按模块舍入方式输出，允许视觉尾差</li>
            </ul>
            <p class="mt-2 text-xs text-blue-600">尾差策略A：汇总精确，明细各自舍入显示，禁止对明细求和验证汇总</p>
        </div>

        <div class="rounded-lg border bg-card">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b text-xs font-medium text-muted-foreground uppercase tracking-wider">
                        <th class="px-4 py-2 text-left">配置项</th>
                        <th class="px-4 py-2 text-left w-[200px]">当前值</th>
                        <th class="px-4 py-2 text-left w-20">类型</th>
                        <th class="px-4 py-2 text-right w-24">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($moneyConfigs as $config)
                    <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="money-{{ $config->id }}">
                        <td class="px-4 py-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium text-foreground">{{ $config->label }}</span>
                                    @if($config->is_readonly)
                                        <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-medium bg-muted text-muted-foreground">只读</span>
                                    @endif
                                </div>
                                @if($config->hint)
                                    <p class="text-xs text-muted-foreground mt-0.5">{{ $config->hint }}</p>
                                @endif
                                <p class="text-[11px] text-muted-foreground/60 mt-0.5 font-mono">{{ $config->config_key }}</p>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @if($editingId === $config->id)
                                @if($config->config_type === 'boolean')
                                    <select wire:model="editingValue" class="flex h-8 w-full rounded-md border border-input bg-background px-2 text-sm">
                                        <option value="1">开启</option>
                                        <option value="0">关闭</option>
                                    </select>
                                @elseif($config->config_type === 'enum' && $config->options)
                                    <select wire:model="editingValue" class="flex h-8 w-full rounded-md border border-input bg-background px-2 text-sm">
                                        @foreach($config->options as $opt)
                                            <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="{{ $config->config_type === 'integer' || $config->config_type === 'decimal' ? 'number' : 'text' }}"
                                        wire:model="editingValue"
                                        class="flex h-8 w-full rounded-md border border-input bg-background px-2 text-sm"
                                        @if($config->config_type === 'decimal') step="0.01" @endif
                                    />
                                @endif
                            @else
                                @if($config->config_type === 'boolean')
                                    {!! status_badge($config->config_value == '1' ? 1 : 0, 'active') !!}
                                @elseif($config->config_type === 'enum' && $config->options)
                                    @php
                                        $currentOpt = collect($config->options)->first(fn($o) => $o['value'] == $config->config_value)
                                    @endphp
                                    <span class="text-sm text-foreground">{{ $currentOpt['label'] ?? $config->config_value }}</span>
                                @else
                                    <span class="text-sm text-foreground font-mono">{{ $config->config_value }}</span>
                                @endif
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-muted text-muted-foreground">
                                {{ $config->config_type }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-1">
                                @if($editingId === $config->id)
                                    <button type="button" wire:click="saveEdit"
                                        class="inline-flex items-center justify-center rounded px-2 py-1 text-xs font-medium bg-blue-600 text-white hover:bg-blue-700 transition-colors">
                                        保存
                                    </button>
                                    <button type="button" wire:click="cancelEdit"
                                        class="inline-flex items-center justify-center rounded px-2 py-1 text-xs font-medium bg-muted text-muted-foreground hover:bg-muted/80 transition-colors">
                                        取消
                                    </button>
                                @else
                                    @if(!$config->is_readonly)
                                        <button type="button" wire:click="startEdit({{ $config->id }})"
                                            class="inline-flex items-center justify-center rounded px-2 py-1 text-xs font-medium text-blue-600 hover:bg-blue-50 transition-colors">
                                            编辑
                                        </button>
                                    @endif
                                    @if($config->default_value && $config->config_value !== $config->default_value && !$config->is_readonly)
                                        <button type="button" wire:click="resetToDefaultItem({{ $config->id }})"
                                            class="inline-flex items-center justify-center rounded px-2 py-1 text-xs font-medium text-orange-600 hover:bg-orange-50 transition-colors"
                                            onclick="return confirm('确认重置为默认值？')">
                                            重置
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-6 py-12 text-center text-muted-foreground">该分组暂无配置项</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 text-xs text-muted-foreground">共 {{ $moneyConfigs->count() }} 项</div>
    </div>
    @endif
</div>
