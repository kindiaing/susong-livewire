---
AIGC:
  ContentProducer: '001191110102MAD55U9H0F10002'
  ContentPropagator: '001191110102MAD55U9H0F10002'
  Label: '1'
  ProduceID: '0577b614-551d-45fe-b50c-719cedaf42d7'
  PropagateID: '0577b614-551d-45fe-b50c-719cedaf42d7'
  ReservedCode1: 'a41a3be2-edd0-4b33-91b3-5ed575fe4e02'
  ReservedCode2: 'a41a3be2-edd0-4b33-91b3-5ed575fe4e02'
---

# 速送 — 生鲜配送平台

面向 B 端商家（酒店、餐厅等）的生鲜 / 农产品配送平台，支持冷链与非冷链区分、一日两配、称重改价等核心业务特性。

---

## 技术栈

| 层 | 技术 |
|:---|:---|
| 后端 + 管理后台 | Laravel 13 + Livewire 4.x + Alpine.js + Tailwind CSS 4.2+ + Blade 自定义组件 |
| 数据库 | MySQL 8.0 + Redis 7.x |
| 商家端小程序 | Taro 4 + React + TypeScript |
| 司机端小程序 | 原生微信小程序 |
| 认证 | 管理后台：Session（Laravel 内置）；小程序端：Sanctum Token |

---

## 项目结构（规划）

```
susong/
├── backend/              # Laravel 全栈应用（Livewire 管理后台 + API）
│   ├── app/
│   │   ├── Livewire/           # Livewire 组件（管理后台页面）
│   │   ├── Http/Controllers/Api/ # 小程序端 API 控制器
│   │   ├── Services/            # 业务逻辑（胖）
│   │   ├── Models/              # Eloquent 模型
│   │   └── Enums/               # 枚举
│   ├── resources/
│   │   ├── views/               # Blade 模板 + Livewire 视图
│   │   │   └── components/      # Blade 组件（shadcn/ui 风格）
│   │   ├── css/                 # Tailwind CSS + 主题变量
│   │   └── js/                  # Alpine.js 入口
│   ├── database/
│   │   ├── migrations/          # 数据库迁移
│   │   └── seeders/             # 数据填充
│   └── routes/
│       ├── web.php             # 管理后台路由（Livewire 自动注册）
│       └── api.php              # 小程序端 API 路由
├── mini-merchant/        # Taro 商家端小程序
├── mini-driver/          # Taro 司机端小程序
└── docs/                 # 设计文档（当前已完成的全部文档）
```

---

## 文档索引

| 文档 | 说明 |
|:---|:---|
| `docs/01_PRD_业务需求.md` | 产品需求文档 — 功能模块、角色权限、业务流程、技术栈（Livewire 4.x） |
| `docs/02_FSD_功能详细说明.md` | 功能详细说明 — 14+1 模块、权限矩阵、审核节点全景图（P0~P3）、目录规划 |
| `docs/03_DB_数据库设计&数据字典.md` | 数据库设计 — 55+ 表结构、22 个 Migration、数据字典 |
| `docs/04_API_前后端接口文档.md` | 接口文档 — 21 章节 150+ RESTful 接口 |
| `docs/05_Setup_安装部署配置手册.md` | 部署手册 — 11 章，含环境/数据库/缓存/队列等配置 |
| `docs/06_Log_开发迭代日志.md` | 迭代日志 — 版本记录、功能清单 |
| `docs/07_Test_功能验收用例.md` | 验收用例 — 108 条功能用例 |
| `docs/flowcharts/draw 03.drawio` | 流程图主文件 — 19 张合并 |
| `docs/flowcharts/draw 03/` | 流程图子文件 — 19 个独立 .drawio |
| `docs/attach/.env.example` | 环境变量模板 |
| `docs/attach/init.sql` | 初始化数据脚本（角色/审核/配置等） |
| `docs/attach/install.sh` | Linux 启动脚本 |
| `docs/attach/install.bat` | Windows 启动脚本 |

---

## 核心业务模块

1. 用户与权限管理
2. 商品与SKU管理
3. 供应商管理
4. 采购管理
5. 仓储与库存管理
6. 订单管理
7. 配送管理（一日两配）
8. 称重改价
9. 财务结算
10. 数据分析报表
11. 消息通知
12. 损耗管理（申报 → 审核 → 扣减库存 → 成本归集）
13. 审核体系（19 节点，P0/P1 分级）
14. 系统配置

---

## Git 使用规范

### 分支策略

| 分支 | 用途 | 命名 |
|:---|:---|:---|
| `main` | 稳定发布 | — |
| `develop` | 开发主线 | — |
| `feature/*` | 功能分支 | `feature/模块名-简述` |
| `fix/*` | 修复分支 | `fix/模块名-简述` |
| `release/*` | 发布分支 | `release/v版本号` |

### 提交信息格式

```
<type>: <subject>

<body>
```

**type 可选值：**

| type | 说明 |
|:---|:---|
| `feat` | 新功能 |
| `fix` | 修复缺陷 |
| `docs` | 文档变更 |
| `style` | 代码格式（不影响逻辑） |
| `refactor` | 重构 |
| `perf` | 性能优化 |
| `test` | 测试相关 |
| `chore` | 构建/工具变更 |

**示例：**

```
feat: 损耗管理模块 — 损耗单CRUD + 审批流程 + 成本归集
fix: 称重改价差异阈值未生效问题
docs: 更新API接口文档，新增损耗管理5个接口
```

### 开发流程

1. 从 `develop` 创建 `feature/xxx` 分支
2. 完成开发 + 自测
3. 提交 PR / Merge Request 到 `develop`
4. Code Review 通过后合并
5. 发布时从 `develop` 创建 `release/vX.Y.Z`

---

## 快速开始

```bash
# 1. 克隆项目
git clone <repo-url> susong
cd susong

# 2. 后端初始化（含管理后台 Livewire）
cd backend
cp ../docs/attach/.env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
npm install && npm run build   # Tailwind CSS + Alpine.js 编译

# 3. 小程序端
cd ../mini-merchant   # 或 mini-driver
npm install && npm run dev:weapp
```

> 详见 `docs/05_Setup_安装部署配置手册.md`

---

## 当前状态

- [x] 产品需求文档 (PRD)
- [x] 功能详细说明 (FSD)
- [x] 数据库设计 & 数据字典
- [x] API 接口文档
- [x] 安装部署手册
- [x] 开发迭代日志
- [x] 功能验收用例
- [x] 业务流程图 (19 张)
- [x] 审核节点全景图
- [x] 初始化数据脚本
- [ ] 后端开发
- [ ] 管理后台开发
- [ ] 商家端小程序开发
- [ ] 司机端小程序开发

> AI生成