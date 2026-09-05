import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { commandPalette } from './command-palette.js';

window.Pusher = Pusher;
window.commandPalette = commandPalette;

// 广播初始化
// 通过 VITE_BROADCAST_DRIVER 环境变量切换 Pusher 云服务 / Reverb 自建
//   pusher = 使用 Pusher 云服务（本地开发和 Cloud 测试环境默认）
//   reverb = 使用 Reverb 自建 WebSocket（多项目共用时切换，需部署 Reverb 服务器）
// try-catch 防止 key 为空时异常中断整个 app.js 模块（导致 Alpine store 未注册）
try {
    const driver = import.meta.env.VITE_BROADCAST_DRIVER || 'pusher';

    if (driver === 'reverb') {
        // Reverb 自建（兼容 Pusher 协议）
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: import.meta.env.VITE_REVERB_APP_KEY,
            wsHost: import.meta.env.VITE_REVERB_HOST,
            wsPort: import.meta.env.VITE_REVERB_PORT,
            wssPort: import.meta.env.VITE_REVERB_PORT,
            forceTLS: import.meta.env.VITE_REVERB_SCHEME === 'https',
            disableStats: true,
            enabledTransports: ['ws', 'wss'],
        });
    } else {
        // Pusher 云服务
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: import.meta.env.VITE_PUSHER_APP_KEY,
            cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
            forceTLS: true,
            encrypted: true,
        });
    }
} catch (e) {
    console.error('Echo initialization failed:', e);
}

// searchable-select 组件 Alpine.js 逻辑
window.__searchableSelect = (data) => ({
    ...data,
    page: 1,
    hasMore: false,
    toggle() {
        if (this.$el.closest('[disabled]')) return;
        this.open = !this.open;
        if (this.open) {
            this.search = '';
            this.activeIndex = -1;
            // 服务端搜索模式：打开即加载默认列表（第一页）
            if (this.serverSearch && this.options.length === 0) {
                this.doServerSearch();
            }
            this.$nextTick(() => this.$refs.searchInput?.focus());
        }
    },
    hasSelection() {
        return this.selectedValue !== '' && this.selectedValue !== '0';
    },
    async doServerSearch(page = 1) {
        if (!this.serverSearch || !this.$wire) return;
        const keyword = (this.search || '').trim();
        this.loading = true;
        try {
            // 服务端方法返回 [{value,label},...] 或 {options:[...], hasMore:bool, page:int}
            const result = await this.$wire.call(this.serverSearch, keyword, page);
            const list = Array.isArray(result) ? result : (result?.options ?? []);
            const mapped = list.map(o => ({
                value: String(o.value ?? o.id ?? ''),
                label: String(o.label ?? o.name ?? ''),
            }));
            if (Array.isArray(result) || page === 1) {
                this.options = mapped;
            } else {
                // 追加模式：过滤掉已存在的值，避免重复
                const existing = new Set(this.options.map(o => o.value));
                this.options = this.options.concat(mapped.filter(o => !existing.has(o.value)));
            }
            this.hasMore = !Array.isArray(result) && Boolean(result?.hasMore);
            this.page = page;
            this.activeIndex = -1;
        } finally {
            this.loading = false;
        }
    },
    async loadMore() {
        if (this.loading || !this.hasMore) return;
        await this.doServerSearch(this.page + 1);
    },
    clearSearch() {
        this.search = '';
        this.doServerSearch();
    },
    onSearchInput() {
        this.doServerSearch();
    },
    filteredOptions() {
        if (this.serverSearch) return this.options;
        if (!this.search) return this.options;
        const q = this.search.toLowerCase();
        return this.options.filter(o => o.label.toLowerCase().includes(q));
    },
    selectOption(opt) {
        this.selectedValue = opt.value;
        this.selectedLabel = opt.label;
        this.search = '';
        this.open = false;
        this.activeIndex = -1;
        // Sync to Livewire via $wire
        if (this.wireModelProperty && this.$wire) {
            this.$wire.set(this.wireModelProperty, opt.value === '' ? 0 : parseInt(opt.value));
        }
    },
    clearValue() {
        this.selectedValue = '';
        this.selectedLabel = '';
        this.search = '';
        this.open = false;
        this.activeIndex = -1;
        if (this.wireModelProperty && this.$wire) {
            this.$wire.set(this.wireModelProperty, 0);
        }
    },
    navigateUp() {
        const items = this.filteredOptions();
        if (items.length === 0) return;
        if (this.activeIndex > 0) this.activeIndex--;
        else this.activeIndex = items.length - 1;
        this.scrollActive();
    },
    navigateDown() {
        const items = this.filteredOptions();
        if (items.length === 0) return;
        if (this.activeIndex < items.length - 1) this.activeIndex++;
        else this.activeIndex = 0;
        this.scrollActive();
    },
    selectActive() {
        const items = this.filteredOptions();
        if (this.activeIndex >= 0 && this.activeIndex < items.length) {
            this.selectOption(items[this.activeIndex]);
        }
    },
    scrollActive() {
        this.$nextTick(() => {
            const list = this.$refs.optionList;
            if (!list) return;
            const items = list.querySelectorAll('[data-option-idx]');
            if (items[this.activeIndex]) {
                items[this.activeIndex].scrollIntoView({ block: 'nearest' });
            }
        });
    }
});

// Toast 全局注册 — 在 Alpine 初始化前注册 store
document.addEventListener('alpine:init', () => {
    Alpine.store('toasts', {
        items: [],
        _nextId: 0,
        add(toast) {
            toast.id = toast.id || ++this._nextId;
            toast.show = true;
            // 倒计时条动画时长与自动关闭时长一致（默认 3s；error/warning 特殊提醒 5s）
            const duration = toast.duration ?? (toast.type === 'error' || toast.type === 'warning' ? 5000 : 3000);
            toast.duration = duration;
            toast.durationAt = Date.now();
            this.items.push(toast);
            if (duration !== 0) {
                const id = toast.id;
                setTimeout(() => this.remove(id), duration);
            }
            return toast.id;
        },
        remove(id) {
            const idx = this.items.findIndex(t => t.id === id);
            if (idx > -1) this.items.splice(idx, 1);
        },
    });

    // 界面设置 store
    Alpine.store('uiSettings', {
        open: false,
        closeOnOutside: window.__UI_CLOSE_ON_OUTSIDE ?? true,
        contentWidth: window.__UI_CONTENT_WIDTH ?? 'default',
        applyContentWidth() {
            const wrapper = document.getElementById('content-wrapper');
            if (!wrapper) return;
            const base = 'mx-auto py-6';
            const widthMap = {
                default: 'max-w-7xl px-4 sm:px-6 lg:px-8',
                wide: 'max-w-[1600px] px-4 sm:px-6 lg:px-8',
                full: 'max-w-none px-12',
            };
            // 移除旧宽度 class
            wrapper.classList.remove('max-w-7xl', 'max-w-[1600px]', 'max-w-none', 'px-4', 'sm:px-6', 'lg:px-8', 'px-12');
            // 添加新宽度 class
            const classes = (widthMap[this.contentWidth] || widthMap.default).split(' ');
            classes.forEach(c => wrapper.classList.add(c));
        },
    });
});

// $toast 全局 API — 在 Alpine 初始化后可用
document.addEventListener('alpine:initialized', () => {
    window.$toast = {
        show(opts) {
            const o = typeof opts === 'string' ? { title: opts } : opts;
            return Alpine.store('toasts').add({
                title: o.title || '',
                description: o.description || '',
                type: o.type || 'default',
                duration: o.duration,
            });
        },
        success(title, description) { return this.show({ title, description, type: 'success' }); },
        error(title, description)   { return this.show({ title, description, type: 'error' }); },
        warning(title, description)  { return this.show({ title, description, type: 'warning' }); },
        info(title, description)    { return this.show({ title, description, type: 'info' }); },
    };

    // Livewire v4 dispatch 事件监听
    window.addEventListener('toast', (e) => {
        const detail = e.detail || {};
        window.$toast.show({
            title: detail.title || detail.message || '',
            description: detail.description || '',
            type: detail.type || 'default',
            duration: detail.duration,
        });
    });
});

// CSRF Token 过期处理：Livewire 4 内置 419 处理机制（自动刷新页面）
// 不再需要手动 hook，Livewire 默认行为已足够

// 全局顶部加载进度条 — Alpine 数据 + Livewire commit hook 驱动
// 用法：app-layout body 内固定定位元素 x-data="__topLoadBar()"
window.__topLoadBar = () => ({
    state: 'idle',
    init() {
        document.addEventListener('livewire:init', () => {
            Livewire.hook('commit', ({ succeed, fail }) => {
                this.start();
                succeed(() => this.finish());
                fail(() => this.finish());
            });
        });
    },
    start() {
        this.state = 'loading';
    },
    finish() {
        if (this.state !== 'loading') return;
        this.state = 'finish';
        setTimeout(() => { this.state = 'idle'; }, 350);
    },
});
