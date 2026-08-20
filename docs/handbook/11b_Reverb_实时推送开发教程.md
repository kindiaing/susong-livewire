---
AIGC:
  ContentProducer: '001191110102MAD55U9H0F10002'
  ContentPropagator: '001191110102MAD55U9H0F10002'
  Label: '1'
  ProduceID: '08a928f5-5fbc-4142-af50-49e2c3bd79bd'
  PropagateID: '08a928f5-5fbc-4142-af50-49e2c3bd79bd'
  ReservedCode1: '1315e351-a433-4c73-93f6-e813ac23e559'
  ReservedCode2: '1315e351-a433-4c73-93f6-e813ac23e559'
---

# Laravel Reverb 实时推送 — 开发教程

> 本文档面向项目新成员，系统讲解 Reverb 在项目中的完整链路：从配置、事件、服务到前端订阅，以及如何新增一个通知场景。

---

## 1 概述

**Laravel Reverb** 是 Laravel 官方的 WebSocket 服务器，用于服务端向浏览器实时推送事件（无需前端轮询）。

本项目中，Reverb 的核心用途是**通知实时推送**——当业务操作触发通知时，通知入库的同时通过 WebSocket 广播，前端 NotificationDrawer 自动刷新。

### 数据流

```
业务操作 → NotificationService → DB 入库 + broadcast() 触发
                ↓                        ↓
          notifications 表         Reverb 服务器
                                          ↓
                                    WebSocket 推送
                                          ↓
                              前端 Echo 监听 → Livewire 刷新
```

---

## 2 配置层

### 2.1 .env 环境变量

```env
# 广播驱动
BROADCAST_CONNECTION=reverb

# Reverb 凭证（首次部署执行 php artisan reverb:install 自动生成）
REVERB_APP_ID=86150937
REVERB_APP_KEY=GDyuc59XqQdW24UliwKa
REVERB_APP_SECRET=nx8Kv1hQ6EzfSgjPG3tquH9BZAbo7wLRXVaTpcDe
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

# Vite 前端自动读取（无需手动改）
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

> **首次部署**：先填好 `DB_*`，然后执行 `php artisan reverb:install`，会自动生成 `REVERB_APP_ID/KEY/SECRET` 并追加到 `.env`。

### 2.2 config/broadcasting.php

定义广播驱动为 reverb，读取 `.env` 中的凭证：

```php
// config/broadcasting.php
'default' => env('BROADCAST_CONNECTION', 'reverb'),

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

### 2.3 config/reverb.php

Reverb 服务端配置，由 `composer require laravel/reverb` 自动发布，一般不需要修改。核心项：

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
    Broadcast::routes();                    // 注册 /broadcasting/auth 路由
    require base_path('routes/channels.php'); // 加载频道授权
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
    if ($user->hasRole('super_admin') || $user->hasRole('admin')) return true;
    if ($user->hasRole('operator') || $user->hasRole('operator_manager')) return true;
    if ($user->hasRole('finance') || $user->hasRole('cashier') || $user->hasRole('finance_manager')) return true;
    return false;
});
```

> **作用**：前端通过 Echo 订阅 `private-notifications.1` 时，Reverb 会调用 `/broadcasting/auth`，Laravel 用此回调判断是否授权。返回 `true` 允许，`false` 拒绝。

---

## 3 事件层

### 3.1 广播事件类 — app/Events/NotificationCreated.php

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

        if ($this->notification->user_id === null) {
            // 全站广播（user_id=null）→ 公共频道
            $channels[] = new Channel('notifications');
        }

        if ($this->notification->merchant_id) {
            // 商家通知 → 商家频道
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
            'id'         => $this->notification->id,
            'type'       => $this->notification->type,
            'type_label' => $this->notification->type_label,
            'title'      => $this->notification->title,
            'content'    => $this->notification->content,
            'data'       => $this->notification->data,
            'is_read'    => $this->notification->is_read,
            'created_at' => $this->notification->created_at?->toIso8601String(),
            'time_ago'   => $this->notification->time_ago,
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

统一入口，负责：创建通知记录 → 触发广播。

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

// 应收收款确认 → 通知操作用户
$svc->receivableCollected(int $userId, string $receivableNo, string $amount)

// 库存预警 → 通知运营用户
$svc->inventoryWarning(int $userId, string $skuName, int $currentStock, int $threshold)

// 采购单提交 → 通知待审核用户
$svc->purchaseSubmitted(int $userId, string $purchaseNo)
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

---

## 5 业务埋点

在 Livewire 组件的状态变更方法中，调用 `NotificationService`。

### 5.1 订单状态变更 — OrderList.php

```php
use App\Services\NotificationService;

// 确认订单（待拣货 → 拣货中）
public function confirmOrder(int $id): void
{
    $order = Order::findOrFail($id);
    $oldStatus = $order->status;
    $order->update(['status' => Order::STATUS_PICKING]);

    // 通知商家
    if ($order->merchant_id) {
        app(NotificationService::class)->orderStatusChanged(
            $order->merchant_id,
            $order->order_no,
            self::$statusMap[$oldStatus] ?? '未知',
            '拣货中',
        );
    }
}

// 作废订单
public function cancelOrder(int $id): void { /* ... 同上，最后参数传 '已作废' */ }

// 完成订单（配送中 → 已签收）
public function completeOrder(int $id): void { /* ... 同上，最后参数传 '已签收' */ }
```

### 5.2 充值审核通过 — RechargeList.php

```php
public function approve(int $id): void
{
    $item = Recharge::findOrFail($id);
    $item->update(['status' => 2]);

    if ($item->merchant_id) {
        app(NotificationService::class)->rechargeApproved(
            $item->merchant_id,
            $item->transaction_no,
            money_format($item->amount),
        );
    }
}
```

### 5.3 供应商结算完成 — SupplierSettlementList.php

```php
public function submitPartialPayment(): void
{
    // ... 付款逻辑 ...
    $newStatus = $newPaidAmount >= $item->payable_amount ? 3 : 2;

    if ($newStatus === 3) {
        app(NotificationService::class)->settlementCompleted(
            auth()->id(),
            $item->settlement_no,
            money_format($item->payable_amount),
        );
    }
}
```

### 5.4 应收收款确认 — ReceivableList.php

```php
public function submitReceive(): void
{
    // ... 收款逻辑 ...
    $newStatus = $newReceived >= $item->adjusted_amount ? 3 : 2;

    if ($newStatus === 3) {
        app(NotificationService::class)->receivableCollected(
            auth()->id(),
            $item->receivable_no,
            money_format($item->adjusted_amount),
        );
    }
}
```

---

## 6 前端层

### 6.1 Echo 初始化 — resources/js/app.js

```js
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
```

> 此文件由 Vite 编译，`VITE_*` 变量从 `.env` 读取。

### 6.2 用户 ID 注入 — app-layout.blade.php

```html
<script>
    window.Laravel = { userId: {{ auth()->id() ?? 'null' }} };
</script>
```

> 前端 Echo 订阅私有频道时，需要此 ID 构造频道名 `notifications.{userId}`。

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
        // Livewire 4 自动重新渲染，通知列表通过 computed property 刷新
    }
}
```

---

## 7 完整调用链（以订单作废为例）

```
1. 用户点击「作废」按钮
   ↓
2. Livewire: OrderList::cancelOrder($id)
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
7. Reverb 服务器收到广播请求
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

前端已监听 `private-notifications.{userId}` 频道的 `.notification.created` 事件，新通知会自动推送并刷新。

### 步骤 4：补充测试数据（可选）

在 `database/seeders/Demo/SystemDemoSeeder.php` 的 `seedNotifications()` 中添加一条示例通知。

---

## 9 启动与验证

### 9.1 本地开发

```bash
# 终端 1：Reverb WebSocket 服务（必须保持运行）
php artisan reverb:start

# 终端 2：Vite 前端热更新（开发时）
npm run dev

# 终端 3：Laravel 开发服务器（如果不用 Laragon）
php artisan serve
```

### 9.2 验证清单

| 检查项 | 方法 |
|--------|------|
| Reverb 是否运行 | 终端窗口应显示 `Reverb server started` |
| WebSocket 是否连接 | 浏览器 F12 → Network → WS 标签，应有 `app?...` 连接 |
| 通知是否入库 | 操作后检查 `notifications` 表 |
| 前端是否实时刷新 | 操作触发通知后，NotificationDrawer 未读数应自动更新（无需 F5） |

### 9.3 常见问题

| 问题 | 排查 |
|------|------|
| WebSocket 连接 404 | Reverb 未启动，或 Nginx 未配 `/app` 反向代理 |
| 连接 403 | `routes/channels.php` 授权回调返回 false |
| 连接成功但收不到推送 | 检查 `broadcastAs()` 返回的事件名与前端 `.listen()` 是否一致 |
| 前端报 `Echo is not defined` | `npm run dev` 未启动，或 `app.js` 未被 Vite 编译 |
| `.env` 改了凭证但仍连旧值 | 执行 `php artisan config:clear` 清缓存 |

---

## 10 生产环境部署

| 配置项 | 生产环境值 |
|--------|-----------|
| `REVERB_HOST` | 实际域名（如 `ws.susong.com`） |
| `REVERB_PORT` | 443 |
| `REVERB_SCHEME` | https |

```nginx
# Nginx 反向代理（必须）
location /app {
    proxy_pass http://127.0.0.1:8080;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "Upgrade";
    proxy_set_header Host $host;
    proxy_read_timeout 86400;
}
```

```ini
# Supervisor 守护进程（numprocs 必须为 1）
[program:susong-reverb]
command=php /var/www/susong/artisan reverb:start --port=8080
numprocs=1
autorestart=true
```