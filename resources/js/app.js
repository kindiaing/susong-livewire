import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST ?? window.location.hostname,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

// Toast 全局注册 — 在 Alpine 初始化前注册 store
document.addEventListener('alpine:init', () => {
    Alpine.store('toasts', {
        items: [],
        _nextId: 0,
        add(toast) {
            toast.id = toast.id || ++this._nextId;
            toast.show = true;
            this.items.push(toast);
            if (toast.duration !== 0) {
                const id = toast.id;
                setTimeout(() => this.remove(id), toast.duration || 4000);
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
                duration: o.duration ?? 5000,
            });
        },
        success(title, description) { return this.show({ title, description, type: 'success' }); },
        error(title, description)   { return this.show({ title, description, type: 'error' }); },
        warning(title, description)  { return this.show({ title, description, type: 'warning' }); },
        info(title, description)    { return this.show({ title, description, type: 'info' }); },
    };

    // Livewire v4 dispatch 事件监听
    window.addEventListener('toast', (e) => {
        window.$toast.show(e.detail || {});
    });
});

// 通知 Drawer Alpine 组件函数
function notificationDrawer() {
    return {
        open: false,
        activeTab: '全部',
        notifications: [
            { id: 1, type: 4, title: '库存预警', content: '商品「有机牛奶」库存低于安全线，当前库存 5 件，安全线 20 件', is_read: 0, time: '5 分钟前' },
            { id: 2, type: 2, title: '订单状态变更', content: '订单 #20260728001 已由配送司机张三确认取货，预计 30 分钟送达', is_read: 0, time: '12 分钟前' },
            { id: 3, type: 3, title: '补货提醒', content: '商家「鲜果超市」有 3 个 SKU 库存不足，请及时补货', is_read: 0, time: '1 小时前' },
            { id: 4, type: 5, title: '账户变动', content: '商家「鲜果超市」充值 ¥5,000.00 到账，当前余额 ¥12,350.00', is_read: 1, time: '2 小时前' },
            { id: 5, type: 1, title: '系统通知', content: '系统将于今晚 23:00-23:30 进行维护升级，届时服务将短暂中断', is_read: 1, time: '3 小时前' },
            { id: 6, type: 2, title: '订单状态变更', content: '订单 #20260728005 客户已签收，签收存证已生成', is_read: 1, time: '昨天 18:30' },
            { id: 7, type: 4, title: '库存预警', content: '商品「进口牛排」库存低于安全线，当前库存 2 件，安全线 15 件', is_read: 1, time: '昨天 14:20' },
        ],
        init() {
            // 可在此加载后端通知数据
        },
        get unreadCount() {
            return this.notifications.filter(n => !n.is_read).length;
        },
        get filteredNotifications() {
            if (this.activeTab === '未读') {
                return this.notifications.filter(n => !n.is_read);
            }
            return this.notifications;
        },
        markRead(n) {
            if (!n.is_read) {
                n.is_read = 1;
            }
        },
        markAllRead() {
            this.notifications.forEach(n => n.is_read = 1);
        },
    };
}
window.notificationDrawer = notificationDrawer;


