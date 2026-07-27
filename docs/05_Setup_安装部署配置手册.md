---
AIGC:
  ContentProducer: '001191110102MAD55U9H0F10002'
  ContentPropagator: '001191110102MAD55U9H0F10002'
  Label: '1'
  ProduceID: '6244578a-126f-4528-9750-73ec90e3e33a'
  PropagateID: '6244578a-126f-4528-9750-73ec90e3e33a'
  ReservedCode1: '13c35e3e-636f-4e29-9578-4d3450922738'
  ReservedCode2: '13c35e3e-636f-4e29-9578-4d3450922738'
---

# Setup 系统安装部署配置手册

对应 PRD 版本：V1.2
对应 FSD 版本：V1.2
技术栈：Laravel 13 + Livewire 4.x + PHP 8.3+（推荐 8.5） + Tailwind CSS 4.2+ + Alpine.js + MySQL 8.0 + Redis 7.x + Nginx

---

## 1 环境要求

### 1.1 服务器最低配置

| 项目 | 最低要求 | 推荐配置 |
| :--- | :--- | :--- |
| CPU | 2 核 | 4 核 |
| 内存 | 4 GB | 8 GB |
| 磁盘 | 40 GB SSD | 100 GB SSD |
| 操作系统 | CentOS 7+ / Ubuntu 20.04+ | Ubuntu 22.04 LTS |
| 网络 | 公网 IP + 域名 | 公网 IP + 域名 + HTTPS |

### 1.2 软件环境

| 软件 | 最低版本 | 推荐版本 | 说明 |
| :--- | :--- | :--- | :--- |
| PHP | 8.2 | 8.3 | 需启用扩展：BCMath、Ctype、cURL、dom、fileinfo、JSON、Mbstring、OpenSSL、PDO、Tokenizer、XML、redis |
| MySQL | 8.0 | 8.0.35+ | 字符集 utf8mb4 |
| Redis | 7.0 | 7.2 | 用于缓存、队列、会话 |
| Node.js | 18.x | 20.x LTS | 前端构建 |
| Nginx | 1.20 | 1.24 | Web 服务器 |
| Composer | 2.6+ | 最新 | PHP 依赖管理 |
| Git | 2.30+ | 最新 | 版本控制 |

### 1.3 微信小程序相关

| 项目 | 说明 |
| :--- | :--- |
| 微信开放平台账号 | 已认证的服务号/小程序主体 |
| AppID | 商家端小程序 AppID |
| AppSecret | 商家端小程序 AppSecret |
| 司机端 AppID | 司机端小程序 AppID |
| 司机端 AppSecret | 司机端小程序 AppSecret |

---

## 2 前置依赖安装

### 2.1 Ubuntu/Debian

```bash
# 更新系统
sudo apt update && sudo apt upgrade -y

# PHP 8.3 + 扩展
sudo apt install -y php8.3 php8.3-fpm php8.3-cli php8.3-common \
  php8.3-mysql php8.3-redis php8.3-mbstring php8.3-xml \
  php8.3-curl php8.3-bcmath php8.3-gd php8.3-zip php8.3-intl

# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Node.js 20.x
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo bash -
sudo apt install -y nodejs

# MySQL 8.0
sudo apt install -y mysql-server
sudo mysql_secure_installation

# Redis 7.x
sudo apt install -y redis-server

# Nginx
sudo apt install -y nginx

# Git
sudo apt install -y git
```

### 2.2 CentOS/RHEL

```bash
# PHP 8.3（Remi 仓库）
sudo yum install -y epel-release
sudo yum install -y https://rpms.remirepo.net/enterprise/remi-release-8.rpm
sudo yum install -y php83 php83-php-fpm php83-php-cli php83-php-common \
  php83-php-mysqlnd php83-php-redis php83-php-mbstring php83-php-xml \
  php83-php-curl php83-php-bcmath php83-php-gd php83-php-zip php83-php-intl

# 其余软件同 Ubuntu，使用 yum 安装
sudo yum install -y mysql-server redis nginx git composer nodejs
```

### 2.3 Windows（本地开发）

1. 安装 PHP 8.3：下载 [windows.php.net](https://windows.php.net/download/)，解压到 `C:\php`，添加环境变量
2. 安装 MySQL 8.0：下载 [MySQL Installer](https://dev.mysql.com/downloads/installer/)
3. 安装 Redis：下载 [Memurai](https://www.memurai.com/) 或 WSL 运行 Redis
4. 安装 Node.js 20.x：下载 [nodejs.org](https://nodejs.org/)
5. 安装 Composer：下载 [Composer-Setup.exe](https://getcomposer.org/Composer-Setup.exe)
6. 安装 Git：下载 [git-scm.com](https://git-scm.com/)

---

## 3 一键部署步骤

### 3.1 生产环境部署（Linux）

```bash
# 1. 克隆代码仓库
cd /var/www
sudo git clone <仓库地址> susong
cd susong

# 2. 复制环境配置文件
cp docs/attach/.env.example .env

# 3. 修改 .env 配置（参见第 4 章）
vi .env

# 4. 安装后端依赖
composer install --optimize-autoloader --no-dev

# 5. 生成应用密钥
php artisan key:generate

# 6. 执行数据库迁移（含初始数据）
php artisan migrate --force

# 7. 导入初始化数据（角色、权限、审核配置等）
mysql -u${DB_USERNAME} -p${DB_DATABASE} < docs/attach/init.sql

# 8. 创建存储目录软链接
php artisan storage:link

# 9. 缓存优化
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 10. 设置目录权限
sudo chown -R www-data:www-data /var/www/susong
sudo chmod -R 755 /var/www/susong
sudo chmod -R 775 storage bootstrap/cache

# 11. 前端构建
cd resources/react  # 前端项目目录（按实际调整）
npm install
npm run build
cd ../..

# 12. 启动服务（使用 deploy 脚本）
chmod +x docs/attach/install.sh
./docs/attach/install.sh
```

### 3.2 Windows 本地开发

```bat
REM 1. 克隆代码仓库
git clone <仓库地址> D:\WWW\susong
cd D:\WWW\susong

REM 2. 复制配置文件
copy docs\attach\.env.example .env

REM 3. 修改 .env 配置

REM 4. 安装后端依赖
composer install

REM 5. 生成密钥
php artisan key:generate

REM 6. 执行迁移
php artisan migrate

REM 7. 导入初始化数据
mysql -u账号 -p库名 < docs/attach/init.sql

REM 8. 创建存储链接
php artisan storage:link

REM 9. 启动开发服务
docs\attach\install.bat
```

---

## 4 核心配置说明

### 4.1 .env 配置项详解

#### 4.1.1 应用基础

| 配置项 | 默认值 | 说明 |
| :--- | :--- | :--- |
| APP_NAME | "Susong" | 应用名称 |
| APP_ENV | local | 环境：local/staging/production |
| APP_KEY | - | 自动生成，`php artisan key:generate` |
| APP_DEBUG | true | 调试模式（生产必须 false） |
| APP_URL | http://localhost | 应用 URL |
| APP_PORT | 8000 | 后端服务端口 |
| FRONTEND_URL | http://localhost:3000 | 前端 URL（CORS 配置用） |
| TIMEZONE | Asia/Shanghai | 时区 |

#### 4.1.2 数据库

| 配置项 | 默认值 | 说明 |
| :--- | :--- | :--- |
| DB_CONNECTION | mysql | 数据库驱动 |
| DB_HOST | 127.0.0.1 | 数据库地址 |
| DB_PORT | 3306 | 数据库端口 |
| DB_DATABASE | susong | 数据库名 |
| DB_USERNAME | root | 数据库用户名 |
| DB_PASSWORD | - | 数据库密码 |
| DB_CHARSET | utf8mb4 | 字符集 |
| DB_COLLATION | utf8mb4_unicode_ci | 排序规则 |

#### 4.1.3 Redis

| 配置项 | 默认值 | 说明 |
| :--- | :--- | :--- |
| REDIS_HOST | 127.0.0.1 | Redis 地址 |
| REDIS_PASSWORD | null | Redis 密码 |
| REDIS_PORT | 6379 | Redis 端口 |
| REDIS_DB | 0 | 默认数据库 |
| REDIS_CACHE_DB | 1 | 缓存数据库 |
| REDIS_QUEUE_DB | 2 | 队列数据库 |
| REDIS_SESSION_DB | 3 | 会话数据库 |

#### 4.1.4 文件存储

| 配置项 | 默认值 | 说明 |
| :--- | :--- | :--- |
| FILESYSTEM_DRIVER | local | 存储驱动：local/oss/s3 |
| OSS_ACCESS_KEY_ID | - | 阿里云 OSS AccessKey |
| OSS_ACCESS_KEY_SECRET | - | 阿里云 OSS SecretKey |
| OSS_BUCKET | - | OSS Bucket 名称 |
| OSS_ENDPOINT | - | OSS Endpoint |
| OSS_CDN | - | OSS CDN 域名（可选） |

#### 4.1.5 队列与定时任务

| 配置项 | 默认值 | 说明 |
| :--- | :--- | :--- |
| QUEUE_CONNECTION | redis | 队列驱动：redis/sync/database |
| QUEUE_RETRY_AFTER | 90 | 队列任务超时重试时间（秒） |
| SCHEDULER_ENABLED | true | 定时任务开关 |

#### 4.1.6 微信小程序

| 配置项 | 默认值 | 说明 |
| :--- | :--- | :--- |
| WECHAT_MINI_APPID | - | 商家端小程序 AppID |
| WECHAT_MINI_SECRET | - | 商家端小程序 AppSecret |
| WECHAT_DRIVER_APPID | - | 司机端小程序 AppID |
| WECHAT_DRIVER_SECRET | - | 司机端小程序 AppSecret |

#### 4.1.7 跨域与前端

| 配置项 | 默认值 | 说明 |
| :--- | :--- | :--- |
| CORS_ALLOWED_ORIGINS | http://localhost:3000 | 允许的跨域来源（逗号分隔） |
| SANCTUM_STATEFUL_DOMAINS | localhost:3000 | Sanctum 有状态域名 |
| SESSION_DOMAIN | localhost | Session 域名 |

#### 4.1.8 日志与审计

| 配置项 | 默认值 | 说明 |
| :--- | :--- | :--- |
| LOG_CHANNEL | daily | 日志通道：daily/single/stack |
| LOG_LEVEL | debug | 日志级别（生产建议 info） |
| LOG_DAYS | 30 | 日志保留天数 |
| AUDIT_RETENTION_DAYS | 365 | 审计日志保留天数 |

---

## 5 Nginx 配置

### 5.1 管理后台（Web 端）

```nginx
server {
    listen 80;
    server_name admin.susong.com;
    root /var/www/susong/public;
    index index.php;

    # 静态资源（Tailwind CSS / JS 编译产物）
    location /assets/ {
        root /var/www/susong/public;
        expires 30d;
        add_header Cache-Control "public, immutable";
    }

    # Laravel 后端（Livewire + API）
    location / {
        try_files $uri $uri/ /index.php?$query_string;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root/index.php;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    # 禁止访问隐藏文件
    location ~ /\. {
        deny all;
    }

    # WebSocket 反向代理（Laravel Reverb）
    location /app {
        proxy_pass http://127.0.0.1:8080;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_read_timeout 86400;
    }

    # 上传文件大小限制
    client_max_body_size 20M;
}
```

### 5.2 微信小程序 API

```nginx
server {
    listen 80;
    server_name api.susong.com;
    root /var/www/susong/public;

    location /api/ {
        try_files $uri $uri/ /index.php?$query_string;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root/index.php;
        include fastcgi_params;
    }

    client_max_body_size 10M;
}
```

---

## 6 进程管理

### 6.1 Supervisor 配置（队列消费者）

```ini
# /etc/supervisor/conf.d/susong-worker.conf
[program:susong-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/susong/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/susong/storage/logs/worker.log
stopwaitsecs=3600
```

### 6.1.1 Supervisor 配置（Laravel Reverb WebSocket 服务器）

```ini
# /etc/supervisor/conf.d/susong-reverb.conf
[program:susong-reverb]
command=php /var/www/susong/artisan reverb:start --port=8080
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/susong/storage/logs/reverb.log
stopwaitsecs=10
```

### 6.2 Cron 配置（Laravel 调度器）

```bash
# /etc/crontab 添加
* * * * * www-data cd /var/www/susong && php artisan schedule:run >> /dev/null 2>&1
```

### 6.3 定时任务清单

| 任务 | 执行频率 | 说明 |
| :--- | :--- | :--- |
| 订单超时自动取消 | 每分钟 | 超过截单时间未确认的订单 |
| 库存预警检测 | 每 5 分钟 | 低于预警值触发通知 |
| 补货提醒推送 | 每日 8:00 | 按商家配置规则推送 |
| 审计日志清理 | 每日 2:00 | 按 `audit_retention_days` 清理 |
| 过期策略失效 | 每小时 | 检查价格策略 end_at |
| 供应商结算周期汇总 | 每周一 1:00 | 按结算周期自动生成结算单 |
| 登录日志统计 | 每日 0:10 | 汇总前一日登录数据 |

---

## 7 Redis 配置

### 7.1 内存策略

```conf
# /etc/redis/redis.conf
maxmemory 2gb
maxmemory-policy allkeys-lru
```

### 7.2 缓存 Key 前缀规范

| 前缀 | 用途 | TTL |
| :--- | :--- | :--- |
| `susong:config:*` | 系统配置缓存 | 24h |
| `susong:permission:*` | 用户权限缓存 | 2h |
| `susong:inventory:*` | 实时库存缓存 | 5min |
| `susong:search:*` | 搜索关键词联想 | 1h |
| `susong:cart:*` | 购物车缓存 | 30min |
| `susong:lock:*` | 分布式锁 | 30s |

---

## 8 HTTPS 配置（生产环境）

```bash
# 安装 Certbot
sudo apt install -y certbot python3-certbot-nginx

# 申请 Let's Encrypt 证书
sudo certbot --nginx -d admin.susong.com -d api.susong.com

# 自动续签
sudo certbot renew --dry-run
```

---

## 9 数据库初始化说明

### 9.1 迁移执行顺序

系统共 22 个 Migration，按业务依赖顺序执行。`php artisan migrate` 命令会自动按时间戳顺序执行：

1. 用户与权限（users, roles, permissions 及关联表）
2. 组织主体（suppliers, merchants, delivery_routes, drivers, vehicles, driver_vehicles, merchant_addresses）
3. 商品管理（categories, products, product_images, skus, merchant_sku_visibility, tags, product_tags, keywords, sku_barcodes, sku_suppliers）
4. 平台统采（purchase_items, purchase_orders, purchase_order_items）
5. 客户直采（carts, cart_items, orders, order_items, frequently_bought, repurchase_templates, repurchase_template_items）
6. 库存管理（warehouses, inventory, inventory_logs）
7. **损耗管理**（loss_orders, loss_order_items）
8. 拣货管理（picking_tasks, picking_task_items）
9. 物流配送（delivery_tasks, delivery_task_orders, delivery_tracks, signatures, temperatures）
10. 差异处理（discrepancies）
11. 财务对账（merchant_accounts, recharges, supplier_settlements, supplier_settlement_items, settlement_payments, receivables, receivable_payments, invoices, correction_authorizations）
12. 系统支撑（system_configs, banners, promotions, operation_logs, audit_logs, login_logs）
13. 微信（wechat_users）
14. 价格策略（price_strategies, price_strategy_items, price_change_logs）
15. 退货管理（purchase_returns, purchase_return_items, order_returns, order_return_items）
16. 费用均摊（price_apportionments）
17. 商家扩展（merchant_favorites）
18. 消息与提醒（notifications, restock_reminders）
19. 审核管理（approvals, approval_type_configs）

### 9.2 初始化数据

`docs/attach/init.sql` 包含以下初始数据：

| 数据类别 | 说明 |
| :--- | :--- |
| 超级管理员账号 | admin / admin123（首次登录强制修改密码） |
| 8 个系统角色 | 超级管理员、运营管理员、运营经理、财务人员、出纳、财务经理、拣货员、司机 |
| 19 个审核节点配置 | approval_type_configs 默认记录（前 10 个 P0 节点默认开启） |
| 系统默认配置 | system_configs 默认键值（损耗审批阈值、审计保留天数等） |
| 2 个默认仓库 | 总仓 + 前置仓 |
| 2 条默认配送线路 | 线路A + 线路B |

---

## 10 常见故障排查

| 序号 | 问题 | 排查步骤 | 解决方案 |
| :--- | :--- | :--- | :--- |
| 1 | 数据库连接失败 | 检查 .env 中 DB_* 配置；确认 MySQL 服务运行中；确认账号密码正确；确认数据库已创建 | `mysql -u root -p -e "CREATE DATABASE susong CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"` |
| 2 | 端口占用 | `netstat -tlnp \| grep :8000` 或 `:80` | 修改 .env 中 APP_PORT 或停止占用进程 |
| 3 | 前端打包报错 | 清理 node_modules 重新安装；检查 Node 版本 >= 18 | `rm -rf node_modules && npm install && npm run build` |
| 4 | 登录 500 错误 | 检查 APP_KEY 是否已生成；检查数据库是否迁移完成 | `php artisan key:generate && php artisan migrate` |
| 5 | Redis 连接失败 | 确认 Redis 服务运行中；检查 REDIS_* 配置 | `sudo systemctl restart redis-server` |
| 6 | 队列任务不执行 | 检查 Supervisor 配置；检查 Redis 连接 | `sudo supervisorctl restart susong-worker:*` |
| 7 | 文件上传失败 | 检查 storage 目录权限；检查 `client_max_body_size` | `chmod -R 775 storage` |
| 8 | CORS 跨域错误 | 检查 .env 中 CORS_ALLOWED_ORIGINS 和 SANCTUM_STATEFUL_DOMAINS | 添加前端域名到配置项 |
| 9 | 定时任务不执行 | 检查 Cron 是否配置；检查 SCHEDULER_ENABLED | `php artisan schedule:run` 手动测试 |
| 10 | 审核流程不触发 | 检查 approval_type_configs 表对应节点 enabled 字段 | 在"审核管理→审核配置"中开启对应节点 |

---

## 11 部署检查清单

### 11.1 上线前必检项

- [ ] `.env` 中 `APP_ENV=production`，`APP_DEBUG=false`
- [ ] `APP_KEY` 已生成且唯一
- [ ] 数据库迁移已执行，`init.sql` 已导入
- [ ] Redis 服务运行中，连接正常
- [ ] Supervisor 队列消费者运行中
- [ ] Cron 定时任务已配置
- [ ] `storage` 和 `bootstrap/cache` 目录权限 775
- [ ] Nginx 配置正确，前端静态资源可访问
- [ ] HTTPS 证书已配置
- [ ] 超级管理员密码已修改（非默认 admin123）
- [ ] 文件存储路径可写（本地或 OSS 配置正确）
- [ ] 跨域配置匹配前端域名

### 11.2 上线后验证项

- [ ] 管理后台登录正常
- [ ] 商家端小程序登录正常
- [ ] 司机端小程序登录正常
- [ ] 商品列表加载正常
- [ ] 下单→拣货→配送→签收全流程通过
- [ ] 采购→入库→库存变动正常
- [ ] 损耗单创建→审核→库存扣减正常
- [ ] 应收/应付结算流程正常
- [ ] 审核节点开关生效
- [ ] 操作日志、审计日志、登录日志正常记录

> AI生成