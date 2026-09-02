<div>
    {{-- 页面标题 --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-foreground">通知设置</h1>
        <p class="text-muted-foreground mt-1">管理系统通知类型开关与通知发送，修改后即时生效</p>
    </div>

    <div class="flex gap-6">
        {{-- 左侧分组导航 --}}
        <div class="w-48 shrink-0">
            <nav class="sticky top-6 space-y-1">
                @foreach($navGroups as $groupKey => $groupLabel)
                    @if($groupKey === 'list')
                        <a href="{{ route('system-notifications') }}" wire:navigate
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
            {{-- 分组1: 通知开关 --}}
            {{-- ════════════════════════════════════════════ --}}
            @if($activeGroup === 'switch')
            <div class="space-y-4 max-w-2xl">
                <div class="rounded-lg border bg-card p-6">
                    <h2 class="text-base font-semibold text-foreground mb-1">通知类型开关</h2>
                    <p class="text-xs text-muted-foreground mb-5">开启后对应类型的通知将正常入库并实时推送至用户端，关闭后不再发送</p>

                    <div class="space-y-2">
                        @foreach($notificationTypes as $type)
                        <div class="flex items-center justify-between rounded-md px-3 py-2.5 hover:bg-muted/20 transition-colors">
                            <div class="flex items-start gap-3">
                                <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-md bg-{{ $type['color'] }}-100">
                                    <x-ui.icon name="{{ $type['icon'] }}" class="w-4 h-4 text-{{ $type['color'] }}-600" />
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-foreground">{{ $type['label'] }}</span>
                                    <p class="text-xs text-muted-foreground mt-0.5">{{ $type['desc'] }}</p>
                                </div>
                            </div>
                            <button type="button" wire:click="toggleSwitch('{{ $type['key'] }}')"
                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $this->{$type['prop']} ? 'bg-blue-600' : 'bg-gray-200' }}">
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $this->{$type['prop']} ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- 开关汇总 --}}
                <div class="text-xs text-muted-foreground">
                    已启用 <span class="font-medium text-foreground">{{ collect([$notifySystem, $notifyOrder, $notifyRestock, $notifyInventory, $notifyAccount])->filter()->count() }}</span> / 5 类通知
                </div>

                {{-- 说明卡片 --}}
                <div class="rounded-lg border border-blue-100 bg-blue-50/50 p-4">
                    <div class="flex items-start gap-2">
                        <x-ui.icon name="information-circle" class="w-4 h-4 text-blue-600 mt-0.5 shrink-0" />
                        <div class="text-xs text-blue-800 space-y-1">
                            <p><b>关闭通知类型后：</b></p>
                            <ul class="list-disc list-inside space-y-0.5 ml-1">
                                <li>该类型的新通知将不再入库和推送</li>
                                <li>已发送的历史通知不受影响，用户仍可查看</li>
                                <li>Toast 瞬时提示（操作成功/失败提醒）不受此开关控制</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
