{{-- 列配置模块（双模式） --}}
{{-- DB 模式（User 模块）：wire:click 直接调 PHP --}}
{{-- localStorage 模式（其他）：Alpine.js + localStorage，零 HTTP 请求 --}}

@if($this->useDbColumnVisibility())
    {{-- DB 模式：纯 wire:click --}}
    @if($showColumnModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">自定义显示列</h2>
                <button type="button" wire:click="closeColumnModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-1 max-h-96 overflow-y-auto border rounded-md p-3">
                @foreach($this->getAllColumns() as $col)
                <label class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-muted/30 cursor-pointer transition-colors">
                    <input
                        type="checkbox"
                        @if($this->isColumnVisible($col['key'])) checked @endif
                        wire:click="toggleColumn('{{ $col['key'] }}')"
                        class="h-4 w-4 rounded border-input text-blue-600 focus:ring-blue-500"
                    />
                    <span class="text-sm text-foreground">{{ $col['label'] }}</span>
                </label>
                @endforeach
            </div>
            <div class="flex justify-between items-center mt-6">
                <div class="flex gap-2">
                    <button type="button" wire:click="selectAllColumns" class="text-xs text-blue-600 hover:text-blue-700">全选</button>
                    <button type="button" wire:click="resetColumns" class="text-xs text-muted-foreground hover:text-foreground">恢复默认</button>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="closeColumnModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">关闭</button>
                    <button type="button" wire:click="closeColumnModal" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">确定</button>
                </div>
            </div>
        </div>
    </div>
    @endif
@else
    {{-- localStorage 模式 --}}
    {{-- 1. 隐藏的同步组件：页面加载时自动读取 localStorage 并同步到 Livewire --}}
    <div
        x-data="colVisSync({ key: @js($this->getColumnVisibilityKey()) })"
        x-init="init()"
        @save-column-visibility.window="onSave($event.detail.key, $event.detail.columns)"
        style="display:none"
    ></div>

    {{-- 2. 列配置弹窗 --}}
    @if($showColumnModal)
    <div
        x-data="columnConfig({
            key: @js($this->getColumnVisibilityKey()),
            allColumns: @js($this->getAllColumns()),
            visibleColumns: @js($visibleColumns),
            defaults: @js($this->getDefaultColumns())
        })"
        x-init="init()"
        class="fixed inset-0 z-50 flex items-center justify-center"
    >
        <div class="fixed inset-0 bg-black/50" aria-hidden="true"></div>
        <div class="relative bg-background rounded-lg border shadow-lg w-full max-w-lg mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-foreground">自定义显示列</h2>
                <button type="button" wire:click="closeColumnModal" class="text-muted-foreground hover:text-foreground transition-colors">
                    <x-ui.icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <div class="space-y-1 max-h-96 overflow-y-auto border rounded-md p-3">
                <template x-for="col in allColumns" :key="col.key">
                    <label class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-muted/30 cursor-pointer transition-colors">
                        <input
                            type="checkbox"
                            :checked="isVisible(col.key)"
                            @change="toggle(col.key)"
                            class="h-4 w-4 rounded border-input text-blue-600 focus:ring-blue-500"
                        />
                        <span class="text-sm text-foreground" x-text="col.label"></span>
                    </label>
                </template>
            </div>
            <div class="flex justify-between items-center mt-6">
                <div class="flex gap-2">
                    <button type="button" @click="selectAll()" class="text-xs text-blue-600 hover:text-blue-700">全选</button>
                    <button type="button" @click="resetDefault()" class="text-xs text-muted-foreground hover:text-foreground">恢复默认</button>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="closeColumnModal" class="rounded-md border border-input px-4 py-2 text-sm hover:bg-accent transition-colors">关闭</button>
                    <button type="button" wire:click="closeColumnModal" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">确定</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <script>
    if (!window.colVisSync) {
    /**
     * 隐藏的同步组件：页面加载时从 localStorage 读取列配置并同步到 Livewire
     * 同时监听 save-column-visibility 事件，将 PHP 侧 dispatch 的配置写入 localStorage
     */
    window.colVisSync = (config) => ({
        key: config.key,

        init() {
            this.syncFromStorage();
        },

        storageKey() {
            return 'col_vis_' + this.key;
        },

        syncFromStorage() {
            try {
                const raw = localStorage.getItem(this.storageKey());
                if (raw) {
                    const saved = JSON.parse(raw);
                    if (Array.isArray(saved) && saved.length > 0) {
                        this.$wire.set('visibleColumns', saved);
                    }
                }
            } catch (e) {}
        },

        onSave(key, columns) {
            if (key !== this.key) return;
            try {
                localStorage.setItem(this.storageKey(), JSON.stringify(columns));
            } catch (e) {}
        },
    });
    }

    if (!window.columnConfig) {
    /**
     * 列配置弹窗组件：Alpine.js 接管交互，勾选即时生效+写 localStorage，零 HTTP 请求
     */
    window.columnConfig = (config) => ({
        key: config.key,
        allColumns: config.allColumns,
        visibleColumns: [...config.visibleColumns],
        defaults: [...config.defaults],

        init() {
            const saved = this.load();
            if (saved) {
                this.visibleColumns = saved;
            }
        },

        storageKey() {
            return 'col_vis_' + this.key;
        },

        load() {
            try {
                const raw = localStorage.getItem(this.storageKey());
                return raw ? JSON.parse(raw) : null;
            } catch (e) {
                return null;
            }
        },

        persist() {
            try {
                localStorage.setItem(this.storageKey(), JSON.stringify(this.visibleColumns));
            } catch (e) {}
        },

        isVisible(key) {
            return this.visibleColumns.includes(key);
        },

        toggle(key) {
            const idx = this.visibleColumns.indexOf(key);
            if (idx !== -1) {
                this.visibleColumns.splice(idx, 1);
            } else {
                this.visibleColumns.push(key);
            }
            this.persist();
            this.syncToLivewire();
        },

        selectAll() {
            this.visibleColumns = this.allColumns.map(c => c.key);
            this.persist();
            this.syncToLivewire();
        },

        resetDefault() {
            this.visibleColumns = [...this.defaults];
            this.persist();
            this.syncToLivewire();
        },

        syncToLivewire() {
            const wire = this.$wire;
            if (wire) {
                wire.set('visibleColumns', [...this.visibleColumns]);
            }
        },
    });
    }
    </script>
@endif
