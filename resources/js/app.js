import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { commandPalette } from './command-palette.js';

window.Pusher = Pusher;
window.commandPalette = commandPalette;

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
                setTimeout(() => this.remove(id), toast.duration || 5000);
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
        const detail = e.detail || {};
        window.$toast.show({
            title: detail.title || detail.message || '',
            description: detail.description || '',
            type: detail.type || 'default',
        });
    });
});

// CSRF Token 过期自动刷新：Livewire 收到 419 时前端会显示 "This page has expired" 对话框
// 监听 Livewire 的请求错误，自动刷新页面获取新 token
document.addEventListener('livewire:init', () => {
    Livewire.hook('request', ({ status, respond }) => {
        respond(({ response }) => {
            if (response.status === 419) {
                window.location.reload();
            }
        });
    });
});
