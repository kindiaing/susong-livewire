---
AIGC:
  ContentProducer: '001191110102MAD55U9H0F10002'
  ContentPropagator: '001191110102MAD55U9H0F10002'
  Label: '1'
  ProduceID: 'f126e1dd-1ceb-4c95-b91e-e10a0a40c081'
  PropagateID: 'f126e1dd-1ceb-4c95-b91e-e10a0a40c081'
  ReservedCode1: '3a0c2c1a-1ecc-4b4a-864b-f20caa898fd7'
  ReservedCode2: '3a0c2c1a-1ecc-4b4a-864b-f20caa898fd7'
---

# Laravel Reverb 实时推送 — 开发教程

> 本文档面向项目新成员，讲解 Reverb 自建 WebSocket 在本项目中的完整链路：安装配置、事件广播、前端订阅、与 Pusher 的切换及生产部署。
>
> 姊妹文档：[11c Pusher 教程](11c_Pusher_云服务实时推送开发教程.md)（当前默认方案）、[11d Redis 教程](11d_Redis_缓存队列与实战.md)（缓存与队列）

---

## 1 概述

**Laravel Reverb** 是 Laravel 官方自建的 WebSocket 服务器，功能与 Pusher 云服务等价（协议兼容），但跑在自己的服务器上，无按量费用。

本项目采用 **Pusher 云服务（默认）/ Reverb（备选）双模式** 架构：

- **当前用 Pusher 云服务**：免部署免运维（详见 11c 教程）
- **多项目后切 Reverb**：避免多项目共用云服务产生月费
- 两者协议兼容，前端事件监听代码零改动，通过环境变量一键切换

### 数据流

```
业务操作 → NotificationService → DB 入库 + broadcast() 触发
                ↓                        ↓
          notifications 表         Reverb 服务器（本机 8080）
                                          ↓
                                    WebSocket 推送
                                          ↓
                          前端 Echo 监听 → Livewire 刷新
```

> 与 Pusher 唯一的区别：事件分发方从 Pusher 云端换成自己服务器上的 Reverb 进程。

---

## 2 配置层

### 2.1 安装与生成凭证

```bash
# 安装（已装可跳过，laravel/reverb 已在 composer.json）
composer require laravel/reverb

# 首次部署执行：自动生成 REVERB_APP_ID/KEY/SECRET 追加到 .env
php artisan reverb:install
```

### 2.2 .env 环境变量（从 Pusher 切到 Reverb）

```env
# ── 1. 服务端广播驱动 ─────────────
BROADCAST_CONNECTION=reverb        # 原 pusher

# ── 2. 前端驱动（与上面必须一致）─────────
VITE_BROADCAST_DRIVER=reverb       # 原 pusher

# ── 3. Reverb 凭证（reverb:install 自动生成）──
REVERB_APP_ID=86150937
REVERB_APP_KEY=GDyuc59XqQdW24UliwKa
REVERB_APP_SECRET=nx8Kv1hQ6EzfSgjPG3tquH9BZAbo7wLRXVaTpcDe
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http                # 本地 http；生产 https

# ── 4. Vite 前端自动读取（无需手动改）────
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"

# ── 5. 注释掉 Pusher 段（避免混淆）─────
# BROADCAST_CONNECTION=pusher / PUSHER_* / VITE_PUSHER_*
```

> 完整切换模板见 `.env.example` 中「方案二」注释块。

### 2.3 config/broadcasting.php

```php
// config/broadcasting.php
'default' => env('BROADCAST_CONNECTION', 'pusher'),

'connections' => [
    'reverb' => [
        'driver' => 'reverb',
        'key'    => env('REVERB_APP_KEY'),
        'secret' => env('REVERB_APP_SECRET'),
        'app_id' => env('REVERB_APP_ID'),
        'options' => [
            'host'   => env('REVERB_HOST', 'localhost'),
            'port'   => env('REVERB_PORT', 8080),
            'scheme' => env('REVERB_SCHEME', 'http'),
            'useTLS' => env('REVERB_SCHEME', 'https') === 'https',
        ],
    ],
],
```

> 服务端广播时，Laravel 向本机 Reverb 进程（`http://localhost:8080`）发 HTTP 请求，Reverb 再通过 WebSocket 分发给订阅客户端。

### 2.4 config/reverb.php

由 `reverb:install` 自动发布，一般不需要修改。核心项：

```php
// config/reverb.php
'servers' => [
    'reverb' => [
        'host' => env('REVERB_SERVER_HOST', '0.0.0.0'),
        'port' => env('REVERB_SERVER_PORT', 8080),
    ],
],
'apps' => [
    'provider' => 'config',
    'apps' => [
        [
            'app_id'  => env('REVERB_APP_ID'),
            'key'     => env('REVERB_APP_KEY'),
            'secret'  => env('REVERB_APP_SECRET'),
        ],
    ],
],
```

### 2.5 BroadcastServiceProvider 注册

```php
// bootstrap/providers.php
return [
    AppServiceProvider::class,
    BroadcastServiceProvider::class,   // ← 必须注册
];
```

```php
// app/Providers/BroadcastServiceProvider.php
public function boot(): void
{
    Broadcast::routes();                       // 注册 /broadcasting/auth 路由
    require base_path('routes/channels.php');  // 加载频道授权
}
```

### 2.6 频道授权 — routes/channels.php

```php
// routes/channels.php

// 用户私有频道：仅本人可订阅
Broadcast::channel('notifications.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// 商家频道：管理员/运营/财务角色可订阅
Broadcast::channel('merchant-notifications.{merchantId}', function ($user, $merchantId) {
    if ($user->hasRole('admin')) return true;
    if ($user->hasRole('operator') || $user->hasRole('operator_manager')) return true;
    if ($user->hasRole('finance') || $user->hasRole('cashier') || $user->hasRole('finance_manager')) return true;
    return false;
});
```

> **作用**：前端通过 Echo 订阅 `private-notifications.1` 时，Reverb 会回调 `/broadcasting/auth`，Laravel 用此回调判断是否授权。返回 `true` 允许，`false` 拒绝。

---

## 3 事件层

### 3.1 广播事件类 — app/Events/NotificationCreated.php

与 Pusher 模式**共用同一个事件类**，零改动：

```php
class NotificationCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Notification $notification
    ) {}

    // 广播到哪些频道
    public function broadcastOn(): array
    {
        $channels = [];

        if ($this->notification->user_id) {
            // 指定用户 → 私有频道
            $channels[] = new PrivateChannel('notifications.' . $this->notification->user_id);
        }

        // 全站广播（user_id=null）→ 公共频道
        if ($this->notification->user_id === null) {
            $channels[] = new Channel('notifications');
        }

        // 商家通知 → 商家频道
        if ($this->notification->merchant_id) {
            $channels[] = new PrivateChannel('merchant-notifications.' . $this->notification->merchant_id);
        }

        return $channels;
    }

    // 事件名（前端监听用）
    public function broadcastAs(): string
    {
        return 'notification.created';
    }

    // 推送给前端的数据
    public function broadcastWith(): array
    {
        return [
            'id' => $this->notification->id,
            'type' => $this->notification->type,
            'type_label' => $this->notification->type_label,
            'type_color' => $this->notification->type_color,
            'title' => $this->notification->title,
            'content' => $this->notification->content,
            'data' => $this->notification->data,
            'is_read' => $this->notification->is_read,
            'created_at' => $this->notification->created_at?->toIso8601String(),
            'time_ago' => $this->notification->time_ago,
        ];
    }
}
```

**关键点：**

| 要素 | 说明 |
|------|------|
| `ShouldBroadcastNow` | 同步广播（不走队列），通知即时性要求高 |
| `broadcastOn()` | 根据通知的 `user_id` / `merchant_id` 决定频道 |
| `broadcastAs()` | 前端监听事件名为 `.notification.created`（注意前导点） |
| `broadcastWith()` | 只推必要数据，避免序列化整个 Model |

### 3.2 频道路由规则

| 通知对象 | `user_id` | `merchant_id` | 广播频道 |
|---------|-----------|---------------|---------|
| 指定用户 | 1 | null | `private-notifications.1` |
| 指定商家 | null | 5 | `private-merchant-notifications.5` |
| 全站广播 | null | null | `notifications`（公共频道） |

---

## 4 服务层

### 4.1 NotificationService — app/Services/NotificationService.php

与 Pusher 模式**共用同一个服务**，业务代码零改动：

```php
// 便捷方法一览（内部走 create() → broadcast()）
$svc->toUser(int $userId, int $type, string $title, ?string $content, ?array $data): Notification
$svc->toMerchant(int $merchantId, int $type, string $title, ?string $content, ?array $data): Notification
$svc->broadcast(int $type, string $title, ?string $content, ?array $data): Notification
$svc->toRole(string $roleName, ...): array
$svc->toUsers(array $userIds, ...): array

// 业务场景快捷方法
$svc->orderStatusChanged(...)       // 订单状态变更 → 商家
$svc->rechargeApproved(...)         // 充值审核通过 → 商家
$svc->settlementCompleted(...)      // 供应商结算完成 → 用户
$svc->supplierPaymentApproved(...)  // 付款审核通过待执行 → 用户
$svc->receivableCollected(...)      // 应收收款确认 → 用户
$svc->inventoryWarning(...)         // 库存预警 → 用户
$svc->purchaseSubmitted(...)        // 采购单待审核 → 用户
```

**内部执行顺序：**

```
1. Notification::create()       ← 写入 DB
2. broadcast(new NotificationCreated($notification))->toOthers()  ← 触发 Reverb 推送
3. catch 异常 → logger()->warning()   ← 广播失败不影响入库
```

### 4.2 通知类型常量

定义在 `app/Models/Notification.php`：

| 常量 | 值 | 标签 | 颜色 |
|------|---|------|------|
| `TYPE_SYSTEM` | 1 | 系统通知 | blue |
| `TYPE_ORDER` | 2 | 订单状态变更 | green |
| `TYPE_RESTOCK` | 3 | 补货提醒 | orange |
| `TYPE_INVENTORY` | 4 | 库存预警 | red |
| `TYPE_ACCOUNT` | 5 | 账户变动 | purple |

> 业务埋点与新增通知场景的做法与 Pusher 模式完全一致，此处不再重复，参见 [11c Pusher 教程](11c_Pusher_云服务实时推送开发教程.md) 第 5、8 章。

---

## 5 前端层

### 5.1 Echo 初始化（双模式） — resources/js/app.js

```js
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// 通过 VITE_BROADCAST_DRIVER 切换 Pusher 云服务 / Reverb 自建
// try-catch 防止 key 为空时异常中断整个 app.js 模块
try {
    const driver = import.meta.env.VITE_BROADCAST_DRIVER || 'pusher';

    if (driver === 'reverb') {
        // Reverb 自建（兼容 Pusher 协议，broadcaster 仍为 'pusher'）
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
```

### 5.2 用户 ID 注入 — app-layout.blade.php

```html
<script>
    window.Laravel = { userId: {{ auth()->id() ?? 'null' }} };
</script>
```

### 5.3 NotificationDrawer 监听 — notification-drawer.blade.php

```html
<div x-data="{
    notificationPanelOpen: false,
    init() {
        // 监听用户私有频道
        if (window.Echo && window.Laravel?.userId) {
            window.Echo.private(`notifications.${window.Laravel.userId}`)
                .listen('.notification.created', () => {
                    this.$wire.handleNotificationReceived();
                });
        }

        // 监听全站公共频道
        if (window.Echo) {
            window.Echo.channel('notifications')
                .listen('.notification.created', () => {
                    this.$wire.handleNotificationReceived();
                });
        }
    }
}">
```

### 5.4 Livewire 响应 — NotificationDrawer.php

```php
class NotificationDrawer extends Component
{
    protected $listeners = [
        'notification-received' => 'handleNotificationReceived',
    ];

    public function handleNotificationReceived(): void
    {
        $this->updateUnreadCount();
        // Livewire 自动重新渲染，通知列表通过 computed property 刷新
    }
}
```

---

## 6 完整调用链（以订单状态变更为例）

```
1. 用户点击操作按钮
   ↓
2. Livewire: OrderList::confirmOrder($id)
   ↓
3. 更新订单状态 → AuditLog::log()
   ↓
4. app(NotificationService::class)->orderStatusChanged(...)
   ↓
5. NotificationService::create()
   ├── Notification::create()          → 写入 DB
   └── broadcast(new NotificationCreated($notification))
        ↓
6. NotificationCreated::broadcastOn()
   → 返回 [new PrivateChannel('merchant-notifications.{merchantId}')]
        ↓
7. Reverb 服务器（本机 8080）收到广播请求
   → 通过 WebSocket 推送给所有订阅该频道的客户端
        ↓
8. 前端 Echo.private('merchant-notifications.{merchantId}')
   .listen('.notification.created', callback)
        ↓
9. callback → this.$wire.handleNotificationReceived()
   ↓
10. Livewire 更新 unreadCount + 重新渲染通知列表
```

---

## 7 启动与验证

### 7.1 本地开发

```bash
# 终端 1：Reverb WebSocket 服务（必须保持运行，Pusher 模式下不需要）
php artisan reverb:start

# 终端 2：Vite 前端热更新（改 .env 后需重启）
npm run dev

# 终端 3：Laravel 开发服务器（如果不用 Laragon）
php artisan serve
```

### 7.2 验证清单

| 检查项 | 方法 |
|--------|------|
| Reverb 是否运行 | 终端窗口应显示 `INFO Server running` |
| WebSocket 是否连接 | 浏览器 F12 → Network → WS 标签，应有 `ws://localhost:8080/app/...` 连接 |
| 通知是否入库 | 操作后检查 `notifications` 表 |
| 前端是否实时刷新 | 操作触发通知后，NotificationDrawer 未读数应自动更新（无需 F5） |

### 7.3 常见问题

| 问题 | 排查 |
|------|------|
| WebSocket 连接 404 | Reverb 未启动；或 Nginx 未配 `/app` 反向代理（生产环境） |
| 连接 403 | `routes/channels.php` 授权回调返回 false |
| 连接成功但收不到推送 | `broadcastAs()` 返回的事件名与前端 `.listen()` 是否一致（前端要加前导点） |
| 前端报 `Echo is not defined` | `npm run dev` 未启动，或 `VITE_*` 变量改动后未重启 Vite |
| `.env` 改了凭证但仍连旧值 | 前端重启 Vite（`VITE_*` 仅启动时读取）；服务端 `php artisan config:clear` |
| 广播报 curl 连接失败 | Reverb 进程没跑（`BROADCAST_CONNECTION=reverb` 时必须启动 `reverb:start`） |

---

## 8 生产环境部署

### 8.1 环境变量

| 配置项 | 生产环境值 |
|--------|-----------|
| `REVERB_HOST` | 实际域名（如 `ws.susong.com`） |
| `REVERB_PORT` | 443 |
| `REVERB_SCHEME` | https |

### 8.2 Nginx 反向代理（必须）

```nginx
location /app {
    proxy_pass http://127.0.0.1:8080;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "Upgrade";
    proxy_set_header Host $host;
    proxy_read_timeout 86400;
}
```

### 8.3 Supervisor 守护进程

```ini
# /etc/supervisor/conf.d/susong-reverb.conf
[program:susong-reverb]
command=php /var/www/susong/artisan reverb:start --port=8080
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1                       # 必须为 1，Reverb 不支持多进程
redirect_stderr=true
stdout_logfile=/var/www/susong/storage/logs/reverb.log
stopwaitsecs=10
```

> 更完整的部署检查清单见 `docs/05_Setup_安装部署配置手册.md` 第 6.1.1 节。

### 8.4 Laravel Cloud 部署

Laravel Cloud 原生托管 Reverb，**无需** Supervisor 与 Nginx 配置：

- Build 阶段无需额外命令
- Deploy Commands 无需 `reverb:start`（平台自动管理进程）
- 仅需 `php artisan reverb:install` 生成配置（或预先在环境变量中填好 `REVERB_*` 三件套）

---

## 9 与 Pusher 的对比与选型

| 维度 | Pusher 云服务 | Reverb 自建 |
|------|--------------|-------------|
| 部署 | 零部署 | 需跑 `reverb:start` + Supervisor + Nginx 反代 |
| 费用 | 沙盒免费（100 并发/200k 消息日）；超出付费 | 自有服务器成本，无按量费用 |
| 运维 | 云端全托管 | 自己管理进程/重启/日志 |
| 适用 | 前期单项目、快速上线 | 多项目、高并发、成本敏感 |

**项目决策**：前期（当前）用 Pusher 云服务；多项目后生产环境切 Laravel Cloud 托管的 Reverb 或自建。切换方法见 [11c Pusher 教程](11c_Pusher_云服务实时推送开发教程.md) 第 10 章。