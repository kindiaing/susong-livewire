<div class="">
    {{-- 页面标题 --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-foreground">系统设置</h1>
        <p class="text-muted-foreground mt-1">管理平台业务参数配置，修改后即时生效</p>
    </div>

    {{-- 成功提示 --}}
    @if(session('success'))
        <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

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
                @foreach($groups as $groupKey => $groupLabel)
                    <button type="button" wire:click="setActiveGroup('{{ $groupKey }}')"
                        class="w-full text-left px-3 py-2 text-sm rounded-md transition-colors {{ $activeGroup === $groupKey ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:bg-muted hover:text-foreground' }}"
                    >
                        {{ $groupLabel }}
                    </button>
                @endforeach
            </nav>
        </div>

        {{-- 右侧配置列表 --}}
        <div class="flex-1">
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
                        @forelse($configs as $config)
                        <tr class="border-b last:border-b-0 hover:bg-muted/30 transition-colors" wire:key="config-{{ $config->id }}">
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
                                    {{-- 编辑模式 --}}
                                    @if($config->config_type === 'boolean')
                                        <select
                                            wire:model="editingValue"
                                            class="flex h-8 w-full rounded-md border border-input bg-background px-2 text-sm"
                                        >
                                            <option value="1">开启</option>
                                            <option value="0">关闭</option>
                                        </select>
                                    @elseif($config->config_type === 'enum' && $config->options)
                                        <select
                                            wire:model="editingValue"
                                            class="flex h-8 w-full rounded-md border border-input bg-background px-2 text-sm"
                                        >
                                            @foreach($config->options as $opt)
                                                <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input
                                            type="{{ $config->config_type === 'integer' || $config->config_type === 'decimal' ? 'number' : 'text' }}"
                                            wire:model="editingValue"
                                            class="flex h-8 w-full rounded-md border border-input bg-background px-2 text-sm"
                                            @if($config->config_type === 'decimal') step="0.01" @endif
                                        />
                                    @endif
                                @else
                                    {{-- 展示模式 --}}
                                    @if($config->config_type === 'boolean')
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $config->config_value == '1' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $config->config_value == '1' ? '开启' : '关闭' }}
                                        </span>
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
                                            class="inline-flex items-center justify-center rounded px-2 py-1 text-xs font-medium bg-blue-600 text-white hover:bg-blue-700 transition-colors"
                                        >
                                            保存
                                        </button>
                                        <button type="button" wire:click="cancelEdit"
                                            class="inline-flex items-center justify-center rounded px-2 py-1 text-xs font-medium bg-muted text-muted-foreground hover:bg-muted/80 transition-colors"
                                        >
                                            取消
                                        </button>
                                    @else
                                        @if(!$config->is_readonly)
                                            <button type="button" wire:click="startEdit({{ $config->id }})"
                                                class="inline-flex items-center justify-center rounded px-2 py-1 text-xs font-medium text-blue-600 hover:bg-blue-50 transition-colors"
                                            >
                                                编辑
                                            </button>
                                        @endif
                                        @if($config->default_value && $config->config_value !== $config->default_value && !$config->is_readonly)
                                            <button type="button" wire:click="resetToDefault({{ $config->id }})"
                                                class="inline-flex items-center justify-center rounded px-2 py-1 text-xs font-medium text-orange-600 hover:bg-orange-50 transition-colors"
                                                onclick="return confirm('确认重置为默认值？')"
                                            >
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

            {{-- 底部说明 --}}
            <div class="mt-4 flex items-center gap-4 text-xs text-muted-foreground">
                <span>默认值标注为 <span class="font-mono text-muted-foreground/80">斜体</span></span>
                <span>·</span>
                <span>共 {{ $configs->count() }} 项</span>
            </div>
        </div>
    </div>
</div>
