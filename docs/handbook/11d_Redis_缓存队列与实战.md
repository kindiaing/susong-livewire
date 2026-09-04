---
AIGC:
  ContentProducer: '001191110102MAD55U9H0F10002'
  ContentPropagator: '001191110102MAD55U9H0F10002'
  Label: '1'
  ProduceID: '36d0809c-1024-414d-9f4f-961066715592'
  PropagateID: '36d0809c-1024-414d-9f4f-961066715592'
  ReservedCode1: 'bd086247-9aab-4f3d-b9fb-0eb6d36a0ac9'
  ReservedCode2: 'bd086247-9aab-4f3d-b9fb-0eb6d36a0ac9'
---

# Redis 缓存、队列与实战 — 开发教程

> 本文档面向项目新成员，讲解 Redis 在本项目中的定位：缓存驱动、队列驱动、会话驱动三种角色，当前配置现状，以及如何在需要时启用与排查。
>
> 姊妹文档：[11c Pusher 教程](11c_Pusher_云服务实时推送开发教程.md)、[11b Reverb 教程](11b_Reverb_实时推送开发教程.md)（实时推送）

---

## 1 概述

**Redis** 是内存键值数据库，Laravel 中可承担三种角色（可任选组合）：

| 角色 | 驱动变量 | 用途 | 项目当前状态 |
|------|---------|------|------------|
| 缓存 | `CACHE_STORE` | 热点数据加速读取 | **未启用**（当前 file） |
| 队列 | `QUEUE_CONNECTION` | 异步任务处理 | **未启用**（当前 database） |
| 会话 | `SESSION_DRIVER` | 登录态存储 | **未启用**（当前 file） |

**当前决策**：本地开发与初期上线阶段全部走轻量驱动（file/database），Redis 已在 `.env` 预留配置位，待并发上来后切换。

> 项目使用 `phpredis` 扩展（非 predis 包），需 PHP 环境装好 `redis` 扩展（`php -m | grep redis` 检查）。

---

## 2 配置层

### 2.1 .env 环境变量（当前项目的值）

```env
# ── 三个驱动开关（当前均为轻量方案）─────
SESSION_DRIVER=file            # 可选 file / database / redis
CACHE_STORE=file               # 可选 file / database / redis
QUEUE_CONNECTION=database      # 可选 sync / database / redis

# ── Redis 连接（切 redis 驱动时生效）────
REDIS_CLIENT=phpredis          # 客户端：phpredis / predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 2.2 切换为 Redis 的场景配置

```env
# 场景一：仅缓存走 Redis（最常见的第一步）
CACHE_STORE=redis

# 场景二：队列走 Redis（任务量大时）
QUEUE_CONNECTION=redis

# 场景三：会话走 Redis（多机负载均衡时必须）
SESSION_DRIVER=redis
```

> 改完后执行 `php artisan config:clear` 清配置缓存生效。

### 2.3 config/database.php — Redis 连接定义

```php
// config/database.php
'redis' => [
    'client' => env('REDIS_CLIENT', 'phpredis'),

    'options' => [
        'cluster' => env('REDIS_CLUSTER', 'redis'),
        // 所有 key 自动加前缀（laravel-database-），多项目共用一个 Redis 时不冲突
        'prefix' => env('REDIS_PREFIX', Str::slug((string) env('APP_NAME', 'laravel')).'-database-'),
        'persistent' => env('REDIS_PERSISTENT', false),
    ],

    'default' => [           // 通用连接（Redis::connection() 默认走它）
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_DB', '0'),
    ],

    'cache' => [             // 缓存专用连接
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_CACHE_DB', '1'),   // 用 db1 与通用 db0 隔离
    ],
],
```

### 2.4 config/cache.php — 缓存存储定义

```php
// config/cache.php
'default' => env('CACHE_STORE', 'database'),

'stores' => [
    // ... array / database / file ...

    'redis' => [
        'driver' => 'redis',
        'connection' => 'cache',      // 指向上方 database.php 的 cache 连接（db1）
    ],
],
```

> 注意两层映射：`CACHE_STORE=redis` → stores.redis → connection `cache` → database.redis.cache（db1）。

### 2.5 config/queue.php — 队列连接定义

```php
// config/queue.php
'default' => env('QUEUE_CONNECTION', 'database'),

'connections' => [
    'database' => [          // 当前：任务存 MySQL jobs 表
        'driver' => 'database',
        'connection' => env('DB_QUEUE_CONNECTION'),
        'table' => env('DB_QUEUE_TABLE', 'jobs'),
        'queue' => env('DB_QUEUE', 'default'),
        'retry_after' => (int) env('DB_QUEUE_RETRY_AFTER', 90),
    ],

    'redis' => [             // 可选：任务存 Redis list
        'driver' => 'redis',
        'connection' => env('REDIS_QUEUE_CONNECTION', 'default'),
        'queue' => env('REDIS_QUEUE', 'default'),
        'retry_after' => (int) env('REDIS_QUEUE_RETRY_AFTER', 90),
    ],
],
```

---

## 3 缓存实战 — 项目中的两个实例

项目通过 `Cache` Facade 使用缓存（不直接操作 Redis），这样缓存驱动切换（file → redis）时代码零改动。

### 3.1 实例一：系统配置全表缓存 — app/Models/SystemConfig.php

系统配置（`setting()` 辅助函数底层）读多写少的典型场景，全表缓存 1 小时：

```php
class SystemConfig extends Model
{
    /**
     * 带缓存的全表读取
     */
    protected static function getAll(): \Illuminate\Support\Collection
    {
        try {
            $result = Cache::remember('system_configs_all', 3600, fn() =>
                static::orderBy('config_group')->orderBy('sort_order')->get()
            );

            // 防御反序列化失败（Migration 变更后旧缓存会返回 __PHP_Incomplete_Class）
            if (!($result instanceof \Illuminate\Support\Collection)) {
                Cache::forget('system_configs_all');
                $result = static::orderBy('config_group')->orderBy('sort_order')->get();
            }

            return $result;
        } catch (\Throwable) {
            Cache::forget('system_configs_all');
            return static::orderBy('config_group')->orderBy('sort_order')->get();
        }
    }

    /**
     * 清除缓存（写入配置后调用，保证下次读到最新值）
     */
    public static function flushCache(): void
    {
        Cache::forget('system_configs_all');
    }
}
```

**要点：**

| 要点 | 说明 |
|------|------|
| `Cache::remember(key, ttl, closure)` | 读缓存，未命中则执行闭包写缓存 |
| 写操作后必须 `forget` | 系统配置每次保存/重置后都调用 `flushCache()`，否则读到旧值 |
| 双重防御 | 反序列化失败回退直查 + try-catch 兜底，缓存故障不影响业务 |

### 3.2 实例二：SKU 单位换算链路缓存 — app/Services/UnitConversionService.php

单位换算链路（箱→件→包）需要递归构建，每次购物车/订单/拣货都用到，按 SKU 缓存 1 小时：

```php
class UnitConversionService
{
    private const CACHE_PREFIX = 'unit_chain_';
    private const CACHE_TTL = 3600; // 1小时

    /**
     * 获取指定 SKU 的完整换算链路（从大到小）
     */
    public function getChain(int $skuId): array
    {
        return Cache::remember(
            self::CACHE_PREFIX . $skuId,      // key: unit_chain_{skuId}
            self::CACHE_TTL,
            function () use ($skuId) {
                return $this->buildChain($skuId);   // 递归构建，开销大
            }
        );
    }

    /**
     * 清除指定 SKU 的缓存（单位配置变更后调用）
     */
    public function clearChainCache(int $skuId): void
    {
        Cache::forget(self::CACHE_PREFIX . $skuId);
    }
}
```

> 库存按 base_unit 最小单位管理，购物车→订单→采购→拣货→配送全链路的单位换算都经此服务，缓存命中后 0 次 DB 查询。

### 3.3 常用缓存 API

```php
use Illuminate\Support\Facades\Cache;

// 读写
Cache::put('key', $value, 3600);           // 写，3600 秒过期
Cache::get('key', $default);               // 读
Cache::remember('key', 3600, fn() => ...); // 读缓存/未命中写缓存（最常用）
Cache::rememberForever('key', fn() => ...);// 永久缓存
Cache::forget('key');                      // 删单个
Cache::flush();                            // 清空当前 store 所有缓存（慎用）

// 原子操作（驱动支持时，redis 支持、file 部分支持）
Cache::increment('counter');
Cache::decrement('counter');
Cache::lock('lock-key', 10)->get(fn() => ...);  // 分布式锁

// 直接用 Redis 连接（绕过 Cache 层，极少用）
use Illuminate\Support\Facades\Redis;
Redis::connection('default')->set('raw:key', 'value');
Redis::connection('default')->get('raw:key');
Redis::lpush('queue:name', $payload);      // 队列底层就是这么写的
```

---

## 4 队列实战

### 4.1 项目队列现状

- 当前 `QUEUE_CONNECTION=database`：任务存 MySQL `jobs` 表，`queue:work` 进程消费
- 广播事件 `NotificationCreated` 用 `ShouldBroadcastNow` **同步广播不走队列**（通知即时性优先）
- 后续若引入耗时任务（批量导入、报表生成、消息推送），建议切 `QUEUE_CONNECTION=redis`

### 4.2 队列使用范式

```php
// 1. 定义任务（Laravel 13 内置生成器）
// php artisan make:job SendReportEmail
class SendReportEmail implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $userId,
        public string $filePath,
    ) {}

    // 任务句柄——队列进程里执行
    public function handle(): void
    {
        // 耗时操作：生成报表、发邮件...
    }
}

// 2. 业务代码分发
SendReportEmail::dispatch($userId, $filePath);

// 3. 延迟执行
SendReportEmail::dispatch($data)->delay(now()->addMinutes(5));

// 4. 指定队列（隔离耗时任务，避免阻塞默认队列）
SendReportEmail::dispatch($data)->onQueue('reports');
// queue:work redis --queue=reports 专门消费

// 5. 事务提交后再入队（避免任务先于数据可见执行）
DB::transaction(function () {
    $order->update([...]);
    SendReportEmail::dispatch($order->id)->afterCommit();
});
```

### 4.3 队列进程管理

```bash
# 开发环境：前台跑一个 worker
php artisan queue:work

# 生产环境：Supervisor 守护（与 Reverb 同套路）
[program:susong-worker]
command=php /var/www/susong/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
numprocs=2                        # 队列可多进程（与 Reverb 必须单进程不同）
redirect_stderr=true
stdout_logfile=/var/www/susong/storage/logs/worker.log
stopwaitsecs=3600

# 注意：改代码后要重启 worker（worker 常驻内存不会自动加载新代码）
php artisan queue:restart
```

> 完整 Supervisor 配置见 `docs/05_Setup_安装部署配置手册.md` 第 6.1 节。

---

## 5 何时切换到 Redis

| 信号 | 建议 |
|------|------|
| cache 表/文件 IO 成为瓶颈（慢查询日志显示 cache 读写频繁） | `CACHE_STORE=redis` |
| 引入耗时异步任务（导入/报表/推送），jobs 表积压 | `QUEUE_CONNECTION=redis` |
| 部署多台应用服务器（负载均衡） | `SESSION_DRIVER=redis`（会话必须共享）+ `CACHE_STORE=redis` |

**切换步骤**（以缓存为例）：

```bash
# 1. 确认 Redis 运行 + phpredis 扩展可用
redis-cli ping              # → PONG
php -m | findstr redis      # Windows 本地；Linux: php -m | grep redis

# 2. 改 .env
# CACHE_STORE=redis

# 3. 清配置缓存
php artisan config:clear

# 4. 验证：登录后台随便逛两页，redis-cli 里应看到带前缀的 key
redis-cli keys "laravel-database-*"
```

---

## 6 排查与常用命令

| 命令 | 作用 |
|------|------|
| `redis-cli ping` | 检查 Redis 是否存活（返回 PONG） |
| `redis-cli info memory` | 内存使用情况 |
| `redis-cli keys "laravel-database-*"` | 查看项目 key（注意前缀） |
| `redis-cli monitor` | 实时查看所有命令流（排障利器，生产慎用） |
| `php artisan queue:failed` | 查看失败任务 |
| `php artisan queue:retry all` | 重试所有失败任务 |
| `php artisan cache:clear` | 清应用缓存（走 Cache 层） |

**常见问题：**

| 问题 | 排查 |
|------|------|
| `Class Redis not found` | phpredis 扩展未装，`php -m` 检查 |
| `Connection refused 127.0.0.1:6379` | Redis 服务未启动 |
| 切 redis 后页面 500 | `php artisan config:clear` 未执行 |
| 改了配置代码但缓存不更新 | 写操作后漏了 `Cache::forget()`（参照 SystemConfig 范式） |
| 多项目共用 Redis 互相干扰 | 每个项目设不同 `REDIS_PREFIX` 或 `REDIS_DB` 编号 |

---

## 7 小结：三件套各司其职

```
┌─────────────────────────────────────────────┐
│  Pusher/Reverb：实时推送（WebSocket 广播）      │  → 11b / 11c 教程
├─────────────────────────────────────────────┤
│  Redis：缓存 / 队列 / 会话（可切换的后端）      │  → 本教程
├─────────────────────────────────────────────┤
│  当前配置：推送走 Pusher 云，缓存走 file，       │
│  队列走 database —— 轻量起步，预留升级路径      │
└─────────────────────────────────────────────┘
```

- **推送与 Redis 无关**：本项目广播走 Pusher/Reverb（HTTP API + WebSocket），不用 Redis pub/sub，`broadcasting.php` 中无 redis 连接
- **业务代码不感知驱动**：缓存用 `Cache::`、队列用 `dispatch()`，切 file/redis/database 驱动时代码零改动
- **升级路径**：并发或任务量上来后，改 3 个环境变量即可平滑切换（第 5 章）