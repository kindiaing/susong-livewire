<div class="">
    {{-- 页面标题 --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('delivery-routes') }}" class="text-muted-foreground hover:text-foreground transition-colors">
                <x-ui.icon name="arrow-left" class="w-5 h-5" />
            </a>
            <div>
                <h1 class="text-2xl font-bold text-foreground">
                    {{ $route->name }}
                    @if($route->code)
                    <span class="text-base font-normal text-muted-foreground ml-2">{{ $route->code }}</span>
                    @endif
                </h1>
                <p class="text-muted-foreground mt-1">线路详情与商家排序管理</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 text-sm text-muted-foreground">
                {!! status_badge($route->status, 'active') !!}
            </span>
        </div>
    </div>

    {{-- 基本信息卡片 --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <div class="rounded-lg border bg-card p-4">
            <h3 class="text-xs font-medium text-muted-foreground uppercase tracking-wider mb-3">线路配置</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-muted-foreground">出发仓库</span>
                    <span class="text-foreground font-medium">{{ $route->warehouse?->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">出发时间</span>
                    <span class="text-foreground font-medium">{{ $route->departure_time ? $route->departure_time->format('H:i') : '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">预计时长</span>
                    <span class="text-foreground font-medium">{{ $route->estimated_duration ? $route->estimated_duration . ' 分钟' : '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">预计里程</span>
                    <span class="text-foreground font-medium">{{ $route->estimated_distance ? $route->estimated_distance . ' 公里' : '-' }}</span>
                </div>
            </div>
        </div>
        <div class="rounded-lg border bg-card p-4">
            <h3 class="text-xs font-medium text-muted-foreground uppercase tracking-wider mb-3">默认配置</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-muted-foreground">默认司机</span>
                    <span class="text-foreground font-medium">{{ $route->defaultDriver?->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">默认车辆</span>
                    <span class="text-foreground font-medium">{{ $route->defaultVehicle?->plate_number ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">线路颜色</span>
                    <span class="flex items-center gap-2">
                        <span class="inline-block w-4 h-4 rounded-full" style="background-color: {{ $route->color ?: '#3b82f6' }}"></span>
                        <span class="text-foreground font-medium">{{ $route->color ?: '-' }}</span>
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">排序</span>
                    <span class="text-foreground font-medium">{{ $route->sort }}</span>
                </div>
            </div>
        </div>
        <div class="rounded-lg border bg-card p-4">
            <h3 class="text-xs font-medium text-muted-foreground uppercase tracking-wider mb-3">统计</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-muted-foreground">总点位数</span>
                    <span class="text-foreground font-medium">{{ $stops->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">启用点位</span>
                    <span class="text-foreground font-medium text-green-600">{{ $stops->where('is_active', 1)->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">停用点位</span>
                    <span class="text-foreground font-medium text-gray-500">{{ $stops->where('is_active', 0)->count() }}</span>
                </div>
                @if($route->description)
                <div class="flex justify-between">
                    <span class="text-muted-foreground">描述</span>
                    <span class="text-foreground font-medium">{{ $route->description }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- 商家列表（列拖拽排序） --}}
    <div class="rounded-lg border bg-card">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h2 class="text-base font-semibold text-foreground">商家点位 <span class="text-muted-foreground font-normal text-sm ml-2">（拖拽卡片调整配送顺序）</span></h2>
            @can('delivery.route.stop-manage')
            <button type="button" wire:click="openAddMerchantModal" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                <x-ui.icon name="plus" class="w-4 h-4" />
                添加商家
            </button>
            @endcan
        </div>

        @if($stops->count() > 0)
        <div x-data="{ dragged: null }" class="flex gap-3 p-4 overflow-x-auto">
            @foreach($stops as $index => $stop)
            <div class="flex-shrink-0 w-56 rounded-lg border bg-background overflow-hidden {{ ! $stop->is_active ? 'opacity-50' : '' }} hover:shadow-md transition-shadow"
                 draggable="true"
                 x-on:dragstart="dragged = $el; $el.style.opacity = '0.4'"
                 x-on:dragend="dragged = null; $el.style.opacity = '1'"
                 x-on:dragover.prevent="$el.style.borderLeftWidth = '3px'; $el.style.borderLeftColor = '#3b82f6'"
                 x-on:dragleave="$el.style.borderLeftWidth = '1px'; $el.style.borderLeftColor = ''"
                 x-on:drop.prevent="
                     $el.style.borderLeftWidth = '1px';
                     $el.style.borderLeftColor = '';
                     const from = dragged;
                     const to = $el;
                     const parent = from.parentNode;
                     const allItems = [...parent.querySelectorAll('[draggable]')];
                     const fromIdx = allItems.indexOf(from);
                     const toIdx = allItems.indexOf(to);
                     if (fromIdx < toIdx) { to.after(from); } else { to.before(from); }
                     const ids = [...parent.querySelectorAll('[draggable]')].map(el => el.dataset.stopId);
                     $el.dispatchEvent(new CustomEvent('reorder-stops', { detail: { ids }, bubbles: true }));
                 "
                 data-stop-id="{{ $stop->id }}"
                 wire:key="stop-{{ $stop->id }}"
            >
                {{-- 序号条 --}}
                <div class="flex items-center justify-between px-3 py-1.5 bg-muted/40 border-b">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-bold">{{ $stop->sequence_no }}</span>
                        <span class="text-xs text-muted-foreground">第{{ $stop->sequence_no }}站</span>
                    </div>
                    <div class="text-muted-foreground/60 cursor-grab active:cursor-grabbing">
                        <x-ui.icon name="bars-3" class="w-4 h-4" />
                    </div>
                </div>
                {{-- 内容 --}}
                <div class="px-3 py-2.5 space-y-2">
                    <div class="text-sm font-semibold text-foreground truncate">{{ $stop->merchant?->name ?? '-' }}</div>
                    <div class="text-xs text-muted-foreground flex items-start gap-1.5">
                        <x-ui.icon name="map-pin" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
                        <span class="line-clamp-2">{{ $stop->address ?: $stop->merchant?->address ?? '-' }}</span>
                    </div>
                    <div class="text-xs text-muted-foreground flex items-center gap-1.5">
                        <x-ui.icon name="phone" class="w-3.5 h-3.5 flex-shrink-0" />
                        <span>{{ $stop->merchant?->contact_phone ?? '-' }}</span>
                    </div>
                    <div class="text-xs text-muted-foreground flex items-center gap-1.5">
                        <x-ui.icon name="clock" class="w-3.5 h-3.5 flex-shrink-0" />
                        <span>停留 {{ $stop->default_service_time }} 分钟</span>
                    </div>
                </div>
                {{-- 状态 + 操作 --}}
                <div class="flex items-center justify-between px-3 py-2 border-t bg-muted/20">
                    <span class="inline-flex items-center gap-1 text-xs {{ $stop->is_active ? 'text-green-600' : 'text-gray-400' }}">
                        <span class="inline-block w-2 h-2 rounded-full {{ $stop->is_active ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                        {{ $stop->is_active ? '启用' : '停用' }}
                    </span>
                    <div class="flex items-center gap-0.5">
                        @can('delivery.route.stop-manage')
                        <button type="button" wire:click="openEditStopModal({{ $stop->id }})" class="p-1 rounded text-blue-600 hover:bg-blue-50 hover:text-blue-700 transition-colors" title="编辑"><x-ui.icon name="pencil-square" class="w-3.5 h-3.5" /></button>
                        <button type="button" wire:click="toggleStopActive({{ $stop->id }})" class="p-1 rounded {{ $stop->is_active ? 'text-gray-500 hover:bg-gray-50' : 'text-green-600 hover:bg-green-50' }} transition-colors" title="{{ $stop->is_active ? '停用' : '启用' }}">
                            <x-ui.icon name="{{ $stop->is_active ? 'eye-slash' : 'eye' }}" class="w-3.5 h-3.5" />
                        </button>
                        <button type="button" wire:click="removeStop({{ $stop->id }})" class="p-1 rounded text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors" title="移除"><x-ui.icon name="trash" class="w-3.5 h-3.5" /></button>
                        @endcan
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="px-6 py-12 text-center text-sm text-muted-foreground">
            暂无商家点位，请点击「添加商家」按钮添加
        </div>
        @endif
    </div>

    {{-- 拖拽排序事件监听 --}}
    <script>
        document.addEventListener('reorder-stops', function(e) {
            const ids = e.detail.ids;
            @this.reorderStops(ids);
        });
    </script>

    {{-- 添加商家弹窗 --}}
    @if($showAddMerchantModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4 p-6 max-h-[85vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">添加商家</h2>
                <button type="button" wire:click="closeAddMerchantModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="mb-4">
                <input type="text" wire:model.live="addMerchantSearch" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="搜索商家名称/手机号..." />
            </div>
            <div class="space-y-2 max-h-[50vh] overflow-y-auto">
                @forelse($availableMerchants as $merchant)
                <div class="flex items-center justify-between rounded-md border px-3 py-2 hover:bg-muted/30 transition-colors">
                    <div>
                        <div class="text-sm font-medium text-foreground">{{ $merchant['name'] }}</div>
                        <div class="text-xs text-muted-foreground">{{ $merchant['address'] ?: '无地址' }} · {{ $merchant['contact_phone'] ?: '-' }}</div>
                    </div>
                    <button type="button" wire:click="addMerchant({{ $merchant['id'] }})" class="rounded-md bg-blue-600 px-3 py-1 text-xs font-medium text-white hover:bg-blue-700 transition-colors">添加</button>
                </div>
                @empty
                <div class="text-center text-sm text-muted-foreground py-4">
                    @if($addMerchantSearch)
                    未找到匹配的商家
                    @else
                    请输入商家名称或手机号搜索
                    @endif
                </div>
                @endforelse
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeAddMerchantModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">关闭</button>
            </div>
        </div>
    </div>
    @endif

    {{-- 编辑停靠点弹窗 --}}
    @if($showEditStopModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-md mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">编辑停靠点</h2>
                <button type="button" wire:click="closeEditStopModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">配送地址</label>
                    <input type="text" wire:model="formStopAddress" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" placeholder="配送地址" />
                    @error('formStopAddress') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">停留时间（分钟） <span class="text-red-500">*</span></label>
                        <input type="number" wire:model="formStopServiceTime" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm" min="1" max="120" />
                        @error('formStopServiceTime') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">状态 <span class="text-red-500">*</span></label>
                        <select wire:model="formStopIsActive" class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm">
                            <option value="1">启用</option>
                            <option value="0">停用</option>
                        </select>
                        @error('formStopIsActive') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">备注</label>
                    <textarea wire:model="formStopRemark" rows="2" class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="可选"></textarea>
                    @error('formStopRemark') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" wire:click="closeEditStopModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">取消</button>
                <button type="button" wire:click="saveStop" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">保存</button>
            </div>
        </div>
    </div>
    @endif
</div>
