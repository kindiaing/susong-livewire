---
AIGC:
  ContentProducer: '001191110102MAD55U9H0F10002'
  ContentPropagator: '001191110102MAD55U9H0F10002'
  Label: '1'
  ProduceID: '0761e56f-56b3-49bb-97ab-1ed2fe0835b7'
  PropagateID: '0761e56f-56b3-49bb-97ab-1ed2fe0835b7'
  ReservedCode1: '6cb5138e-cad0-4b0a-b05d-e3c6dd4ff5d8'
  ReservedCode2: '6cb5138e-cad0-4b0a-b05d-e3c6dd4ff5d8'
---

# Pusher 云服务实时推送 — 开发教程

> 本文档面向项目新成员，讲解 Pusher 云服务在本项目中的完整链路：注册配置、事件广播、前端订阅、双模式切换与常见排查。
>
> 姊妹文档：[11b Reverb 教程](11b_Reverb_实时推送开发教程.md)（自建 WebSocket 备选方案）、[11d Redis 教程](11d_Redis_缓存队列与实战.md)（缓存与队列）

---

## 1 概述

**Pusher** 是托管在云端的 WebSocket 服务（SaaS），Laravel 后端通过 HTTP API 触发事件，Pusher 云端将事件推送给所有订阅的浏览器，前端无需轮询即可实时刷新。

本项目采用 **Pusher 云服务（默认）/ Laravel Reverb（备选）双模式** 架构：

- **前期（单项目）用 Pusher**：免部署免运维，沙盒免费额度（100 并发连接、200k 条/天消息）够开发与初期上线
- **后期（多项目）切 Reverb**：避免多项目共用云服务产生月费，自建 WebSocket（详见 11b 教程）
- 两者协议兼容，前端事件监听代码零改动，通过环境变量一键切换

### 数据流

```
业务操作 → NotificationService → DB 入库 + broadcast() 触发
                ↓                        ↓
          notifications 表         Pusher 云 API（HTTPS）
                                          ↓
                                    WebSocket 推送
                                          ↓
                          前端 Echo 监听 → Livewire 刷新
```

---

## 2 配置层

### 2.1 注册与获取凭证

1. 注册 [pusher.com](https://pusher.com) 账号，创建 Channels 应用
2. 前端语言选 **JavaScript**，集群就近选 **ap1（新加坡）**
3. 在 App Keys 页拿到四件套：`app_id` / `key` / `secret` / `cluster`

### 2.2 .env 环境变量（当前项目的值）

```env
# 服务端广播驱动（本项目当前固定 pusher）
BROADCAST_CONNECTION=pusher
PUSHER_APP_ID=2187610
PUSHER_APP_KEY=e41d07ff0b5a81c0e860      # 暴露给前端，属公开信息
PUSHER_APP_SECRET=a757656a71a32f008aa1   # 仅服务端使用，切勿泄露
PUSHER_APP_CLUSTER=ap1                    # 新加坡集群

# Vite 前端自动读取（无需手动改）
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

> 注意：`VITE_PUSHER_APP_KEY` 会被编译进前端 JS，任何人可见——这是设计如此（Pusher 靠频道授权保证安全，不靠 key 保密）。但 `PUSHER_APP_SECRET` 绝不能出现在任何 `VITE_*` 变量中。

### 2.3 config/broadcasting.php

```php
// config/broadcasting.php
'default' => env('BROADCAST_CONNECTION', 'pusher'),

'connections' => [
    'pusher' => [
        'driver' => 'pusher',
        'key' => env('PUSHER_APP_KEY'),
        'secret' => env('PUSHER_APP_SECRET'),
        'app_id' => env('PUSHER_APP_ID'),
        'options' => [
            'cluster' => env('PUSHER_APP_CLUSTER', 'ap1'),
            'host' => env('PUSHER_HOST') ?: 'api-'.env('PUSHER_APP_CLUSTER', 'ap1').'.pusher.com',
            'port' => env('PUSHER_PORT', 443),
            'scheme' => env('PUSHER_SCHEME', 'https'),
            'encrypted' => true,
            'useTLS' => true,
        ],
    ],
],
```

> 服务端广播时，Laravel 用 `pusher/pusher-php-server` SDK 向 `https://api-ap1.pusher.com` 发 POST 请求，Pusher 云端再分发给订阅客户端。

### 2.4 BroadcastServiceProvider 注册

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

### 2.5 频道授权 — routes/channels.php

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

> **作用**：前端 Echo 订阅 `private-notifications.1` 时，Pusher 会回调 `/broadcasting/auth`，Laravel 用此回调判断是否授权。返回 `true` 允许，`false` 拒绝（前端收到 403）。

---

## 3 事件层

### 3.1 广播事件类 — app/Events/NotificationCreated.php

这是本项目唯一一个广播事件，所有通知场景共用：

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

统一入口，负责：创建通知记录 → 触发广播。业务代码不直接 broadcast()。

**基础方法：**

```php
// 发送给指定用户
$svc->toUser(int $userId, int $type, string $title, ?string $content, ?array $data): Notification

// 发送给指定商家
$svc->toMerchant(int $merchantId, int $type, string $title, ?string $content, ?array $data): Notification

// 全站广播
$svc->broadcast(int $type, string $title, ?string $content, ?array $data): Notification

// 发送给某个角色的所有用户
$svc->toRole(string $roleName, int $type, string $title, ?string $content, ?array $data): array

// 发送给多个用户
$svc->toUsers(array $userIds, int $type, string $title, ?string $content, ?array $data): array
```

**业务便捷方法（推荐使用）：**

```php
// 订单状态变更 → 通知商家
$svc->orderStatusChanged(int $merchantId, string $orderNo, string $fromStatus, string $toStatus)

// 充值审核通过 → 通知商家
$svc->rechargeApproved(int $merchantId, string $rechargeNo, string $amount)

// 供应商结算完成 → 通知操作用户
$svc->settlementCompleted(int $userId, string $settlementNo, string $amount)

// 供应商付款审核通过 → 通知登记人执行付款
$svc->supplierPaymentApproved(int $userId, string $settlementNo, string $amount)

// 应收收款确认 → 通知操作用户
$svc->receivableCollected(int $userId, string $receivableNo, string $amount)

// 库存预警 → 通知运营用户
$svc->inventoryWarning(int $userId, string $skuName, int $currentStock, int $threshold)

// 采购单提交 → 通知待审核用户
$svc->purchaseSubmitted(int $userId, string $purchaseNo)
```

**内部执行顺序（app/Services/NotificationService.php::create）：**

```php
// 1. 写库
$notification = Notification::create([...]);

// 2. 触发广播（try-catch 包裹，失败不影响入库）
try {
    broadcast(new NotificationCreated($notification))->toOthers();
} catch (\Throwable $e) {
    logger()->warning('通知广播失败: ' . $e->getMessage(), [
        'notification_id' => $notification->id,
    ]);
}
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

---

## 5 业务埋点

在 Livewire 组件的状态变更方法中调用 `NotificationService`：

```php
use App\Services\NotificationService;

// 订单确认（状态 → 拣货中）
public function confirmOrder(int $id): void
{
    $order = Order::findOrFail($id);
    $order->update(['status' => Order::STATUS_PICKING]);

    if ($order->merchant_id) {
        app(NotificationService::class)->orderStatusChanged(
            $order->merchant_id,
            $order->order_no,
            '待拣货',
            '拣货中',
        );
    }
}
```

---

## 6 前端层

### 6.1 Echo 初始化（双模式） — resources/js/app.js

```js
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// 通过 VITE_BROADCAST_DRIVER 切换 Pusher 云服务 / Reverb 自建
// try-catch 防止 key 为空时异常中断整个 app.js 模块
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
```

> 注意：两种模式 `broadcaster` 都是 `'pusher'`（Reverb 兼容 Pusher 协议），差别只在连接参数。

### 6.2 用户 ID 注入 — app-layout.blade.php

```html
<script>
    window.Laravel = { userId: {{ auth()->id() ?? 'null' }} };
</script>
```

> 前端 Echo 订阅私有频道时需要此 ID 构造频道名 `notifications.{userId}`。

### 6.3 NotificationDrawer 监听 — notification-drawer.blade.php

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

### 6.4 Livewire 响应 — NotificationDrawer.php

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

## 7 完整调用链（以订单确认为例）

```
1. 用户点击「确认」按钮
   ↓
2. Livewire: OrderList::confirmOrder($id)
   ↓
3. 更新订单状态 → AuditLog::log()
   ↓
4. app(NotificationService::class)->orderStatusChanged(...)
   ↓
5. NotificationService::create()
   ├── Notification::create()          → 写入 DB
   └── broadcast(new NotificationCreated($notification))->toOthers()
        ↓
6. NotificationCreated::broadcastOn()
   → 返回 [new PrivateChannel('merchant-notifications.{merchantId}')]
        ↓
7. Laravel 通过 pusher-php-server SDK 调 Pusher 云 API
   → Pusher 云端通过 WebSocket 推送给所有订阅该频道的客户端
        ↓
8. 前端 Echo.private('merchant-notifications.{merchantId}')
   .listen('.notification.created', callback)
        ↓
9. callback → this.$wire.handleNotificationReceived()
   ↓
10. Livewire 更新 unreadCount + 重新渲染通知列表（无需 F5）
```

---

## 8 如何新增一个通知场景

以「采购单审核通过」为例：

### 步骤 1：在 NotificationService 添加便捷方法

```php
// app/Services/NotificationService.php
public function purchaseApproved(int $userId, string $purchaseNo, string $supplierName): Notification
{
    return $this->toUser($userId, Notification::TYPE_SYSTEM, '采购单已审核', "采购单 {$purchaseNo}（供应商：{$supplierName}）已审核通过", [
        'purchase_no' => $purchaseNo,
        'supplier_name' => $supplierName,
    ]);
}
```

### 步骤 2：在业务代码中调用

```php
// app/Livewire/Purchase/PurchaseOrderList.php
use App\Services\NotificationService;

public function approve(int $id): void
{
    $item = PurchaseOrder::findOrFail($id);
    $item->update(['status' => 3]);

    app(NotificationService::class)->purchaseApproved(
        auth()->id(),
        $item->purchase_no,
        $item->supplier?->name ?? '未知',
    );

    $this->toastSuccess('采购单已审核');
}
```

### 步骤 3：无需修改前端

前端已监听 `private-notifications.{userId}` 频道的 `.notification.created` 事件，新通知自动推送并刷新。

---

## 9 启动与验证

### 9.1 本地开发（Pusher 模式无需启动任何额外服务）

```bash
# Vite 前端热更新
npm run dev

# Laravel 开发服务器（如果不用 Laragon）
php artisan serve
```

> Pusher 云服务模式下**无需** `reverb:start`，WebSocket 服务托管在 Pusher 云端。

### 9.2 验证清单

| 检查项 | 方法 |
|--------|------|
| WebSocket 是否连接 | 浏览器 F12 → Network → WS 标签，应有 `wss://ws-ap1.pusher.com/app/...` 连接 |
| 事件是否到达 Pusher | Pusher Dashboard → Debug Console，实时显示 event 请求 |
| 通知是否入库 | 操作后检查 `notifications` 表 |
| 前端是否实时刷新 | 操作触发通知后，NotificationDrawer 未读数自动更新（无需 F5） |

> **Debug Console 是排障利器**：能同时看到服务端 API 调用与客户端连接/订阅，一眼区分「服务端没发」和「前端没收到」。

### 9.3 常见问题

| 问题 | 排查 |
|------|------|
| WebSocket 连接 401/403 | `routes/channels.php` 授权回调返回 false；或 `PUSHER_APP_KEY` 与前端 `VITE_PUSHER_APP_KEY` 不一致 |
| 连接成功但收不到推送 | `broadcastAs()` 返回的事件名与前端 `.listen()` 不一致（前端要加前导点 `.notification.created`） |
| 前端报 `Echo is not defined` | `npm run dev` 未启动，或 `VITE_*` 变量改动后未重启 Vite |
| 改了 `.env` 但仍连旧值 | 执行 `npm run dev` 重启（Vite 只在启动时读 `.env`）；服务端执行 `php artisan config:clear` |
| 服务端广播报 curl 超时 | 服务器无法访问 `api-ap1.pusher.com`（防火墙/DNS），检查出网 |
| 超出免费额度 | Pusher Dashboard 查看用量，超 100 并发或 200k 消息/天需升级或切 Reverb |

---

## 10 双模式切换（Pusher ↔ Reverb）

完整切换步骤见 `.env.example` 注释，核心是两组变量保持一致：

```env
# ── 方案一：Pusher 云服务（当前）─────────────
BROADCAST_CONNECTION=pusher
VITE_BROADCAST_DRIVER=pusher
PUSHER_APP_ID=...
PUSHER_APP_KEY=...
PUSHER_APP_SECRET=...
PUSHER_APP_CLUSTER=ap1
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

# ── 方案二：Reverb 自建（取消注释以启用）─────────
# BROADCAST_CONNECTION=reverb
# VITE_BROADCAST_DRIVER=reverb
# REVERB_APP_ID / KEY / SECRET / HOST / PORT / SCHEME ...
# VITE_REVERB_APP_KEY / HOST / PORT / SCHEME ...
```

**切换要点：**

| 要点 | 说明 |
|------|------|
| 两个变量都要改 | `BROADCAST_CONNECTION`（服务端）与 `VITE_BROADCAST_DRIVER`（前端）必须一致 |
| 前端零代码改动 | 两种模式都走 Pusher 协议，`.listen()` 代码完全相同 |
| 改完重启 Vite | `VITE_*` 只在 Vite 启动时读取 |
| 服务端清缓存 | `php artisan config:clear` |

---

## 11 与 Reverb 的对比与选型

| 维度 | Pusher 云服务 | Reverb 自建 |
|------|--------------|-------------|
| 部署 | 零部署 | 需跑 `reverb:start` + Supervisor + Nginx 反代 |
| 费用 | 沙盒免费（100 并发/200k 消息日）；超出付费 | 自有服务器成本，无按量费用 |
| 运维 | 云端全托管 | 自己管理进程/重启/日志 |
| 适用 | 前期单项目、快速上线 | 多项目、高并发、成本敏感 |

**项目决策**：前期（当前）用 Pusher 云服务；多项目后生产环境切 Laravel Cloud 托管的 Reverb 或自建（详见 [11b Reverb 教程](11b_Reverb_实时推送开发教程.md)）。