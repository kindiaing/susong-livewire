<div>
    {{-- 页面标题 --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-foreground">审计设置</h1>
        <p class="text-muted-foreground mt-1">管理审计日志开关与保留策略，修改后即时生效</p>
    </div>

    {{-- 错误提示 --}}
    @error('editingValue')
        <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ $message }}
        </div>
    @enderror

    <div class="flex gap-6">
        {{-- 左侧分组导航 --}}
        <div class="w-48 shrink-0">
            <nav class="sticky top-6 space-y-1">
                @foreach($navGroups as $groupKey => $groupLabel)
                    @if($groupKey === 'logs')
                        <a href="{{ route('audit-logs') }}" wire:navigate
                            class="w-full flex items-center justify-between text-left px-3 py-2 text-sm rounded-md transition-colors text-muted-foreground hover:bg-muted hover:text-foreground">
                            {{ $groupLabel }}
                            <span class="text-[10px] text-muted-foreground/60">&rarr;</span>
                        </a>
                    @else
                        <button type="button" wire:click="setActiveGroup('{{ $groupKey }}')"
                            class="w-full text-left px-3 py-2 text-sm rounded-md transition-colors {{ $activeGroup === $groupKey ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:bg-muted hover:text-foreground' }}">
                            {{ $groupLabel }}
                        </button>
                    @endif
                @endforeach
            </nav>
        </div>

        {{-- 右侧配置内容 --}}
        <div class="flex-1">

            {{-- ════════════════════════════════════════════ --}}
            {{-- 分组1: 审计开关 --}}
            {{-- ════════════════════════════════════════════ --}}
            @if($activeGroup === 'switch')
            <div class="space-y-4 max-w-2xl">
                <div class="rounded-lg border bg-card p-6">
                    <h2 class="text-base font-semibold text-foreground mb-1">状态变更审计</h2>
                    <p class="text-xs text-muted-foreground mb-5">开启后，对应单据的状态流转将自动记录审计日志</p>

                    <div class="space-y-2">
                        {{-- 采购单状态审计 --}}
                        <div class="flex items-center justify-between rounded-md px-3 py-2.5 hover:bg-muted/20 transition-colors">
                            <div>
                                <span class="text-sm font-medium text-foreground">采购单状态审计</span>
                                <p class="text-xs text-muted-foreground mt-0.5">记录采购单创建→接单→发货→入库→完成等状态变更</p>
                            </div>
                            <button type="button" wire:click="toggleAuditSwitch('audit_purchase_order')"
                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $auditPurchaseOrder ? 'bg-blue-600' : 'bg-gray-200' }}">
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $auditPurchaseOrder ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </div>

                        {{-- 采购退货状态审计 --}}
                        <div class="flex items-center justify-between rounded-md px-3 py-2.5 hover:bg-muted/20 transition-colors">
                            <div>
                                <span class="text-sm font-medium text-foreground">采购退货状态审计</span>
                                <p class="text-xs text-muted-foreground mt-0.5">记录退货单创建→审核→退货→入库等状态变更</p>
                            </div>
                            <button type="button" wire:click="toggleAuditSwitch('audit_purchase_return')"
                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $auditPurchaseReturn ? 'bg-blue-600' : 'bg-gray-200' }}">
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $auditPurchaseReturn ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </div>

                        {{-- 损耗单状态审计 --}}
                        <div class="flex items-center justify-between rounded-md px-3 py-2.5 hover:bg-muted/20 transition-colors">
                            <div>
                                <span class="text-sm font-medium text-foreground">损耗单状态审计</span>
                                <p class="text-xs text-muted-foreground mt-0.5">记录损耗单创建→审核→处理等状态变更</p>
                            </div>
                            <button type="button" wire:click="toggleAuditSwitch('audit_loss_order')"
                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $auditLossOrder ? 'bg-blue-600' : 'bg-gray-200' }}">
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $auditLossOrder ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </div>

                        {{-- 价格变更审计 --}}
                        <div class="flex items-center justify-between rounded-md px-3 py-2.5 hover:bg-muted/20 transition-colors">
                            <div>
                                <span class="text-sm font-medium text-foreground">价格变更审计</span>
                                <p class="text-xs text-muted-foreground mt-0.5">记录SKU价格字段的创建、修改与变更前后快照</p>
                            </div>
                            <button type="button" wire:click="toggleAuditSwitch('audit_price_change')"
                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $auditPriceChange ? 'bg-blue-600' : 'bg-gray-200' }}">
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $auditPriceChange ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- 开关汇总 --}}
                <div class="text-xs text-muted-foreground">
                    已启用 <span class="font-medium text-foreground">{{ collect([$auditPurchaseOrder, $auditPurchaseReturn, $auditLossOrder, $auditPriceChange])->filter()->count() }}</span> / 4 项审计
                </div>
            </div>
            @endif

            {{-- ════════════════════════════════════════════ --}}
            {{-- 分组2: 日志策略 --}}
            {{-- ════════════════════════════════════════════ --}}
            @if($activeGroup === 'policy')
            <div class="max-w-2xl">
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
                            @forelse($auditConfigs as $config)
                            <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="audit-config-{{ $config->id }}">
                                {{-- 配置项名称 + 描述 --}}
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

                                {{-- 当前值 / 编辑控件 --}}
                                <td class="px-4 py-3">
                                    @if($editingId === $config->id)
                                        @if($config->config_type === 'enum' && $config->options)
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
                                        @if($config->config_type === 'enum' && $config->options)
                                            @php
                                                $currentOpt = collect($config->options)->first(fn($o) => $o['value'] == $config->config_value)
                                            @endphp
                                            <span class="text-sm text-foreground">{{ $currentOpt['label'] ?? $config->config_value }}</span>
                                        @else
                                            <span class="text-sm text-foreground font-mono">{{ $config->config_value }}</span>
                                        @endif
                                    @endif
                                </td>

                                {{-- 类型标签 --}}
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium bg-muted text-muted-foreground">
                                        {{ $config->config_type }}
                                    </span>
                                </td>

                                {{-- 操作按钮 --}}
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

                <div class="mt-4 text-xs text-muted-foreground">共 {{ $auditConfigs->count() }} 项</div>
            </div>
            @endif

        </div>
    </div>
</div>
