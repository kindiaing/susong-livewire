---
AIGC:
  ContentProducer: '001191110102MAD55U9H0F10002'
  ContentPropagator: '001191110102MAD55U9H0F10002'
  Label: '1'
  ProduceID: 'dc025470-5671-46ac-a5c2-f83c9cb771d1'
  PropagateID: 'dc025470-5671-46ac-a5c2-f83c9cb771d1'
  ReservedCode1: 'c8dea1c9-88e6-42ee-9213-5e9bd7539869'
  ReservedCode2: 'c8dea1c9-88e6-42ee-9213-5e9bd7539869'
---

# 开发迭代日志

对应 PRD 版本：V1.2
对应 FSD 版本：V1.2
技术栈：Laravel 13 + Livewire 4.x + Tailwind CSS 4.2+ + Alpine.js + PHP 8.4+ + MySQL 8.0 + Redis 7.x

记录规则：每次迭代新增一节，按版本号倒序排列。每条变更需标注开发人、完成时间和关联模块。

---

## V1.5.0 | 迭代周期：2026-07-29

负责人：项目负责人
参与开发人员：后端开发、前端开发

### 1 本次新增功能清单

| 序号 | 功能模块 | 功能点 | 开发人 | 完成时间 | 状态 |
| :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | 图标 | icon.blade.php 新增 16 个 Heroicons 图标（clipboard-document-list / document-text / star / arrow-path / photo / sparkles / key / chat-bubble-left-right / map-pin / clock / plus / x-mark / magnifying-glass / shield-check / shield-exclamation / check-circle） | 前端 | 2026-07-29 | ✅ |
| 2 | 导航 | nav-menu-icon-link 组件增加 description 参数，全部子菜单添加简短功能描述 | 前端 | 2026-07-29 | ✅ |
| 3 | 导航 | 用户权限/系统管理子菜单统一改用 icon-link+description 样式（原部分使用 nav-menu-link） | 前端 | 2026-07-29 | ✅ |
| 4 | 测试数据 | DemoDataSeeder 扩展全模块示例数据（30+ 张业务表，覆盖采购/订单/库存/配送/损耗/拣货/差异/财务/退货/价格策略/Banner/登录日志等） | 后端 | 2026-07-29 | ✅ |

### 2 本次优化/重构

| 序号 | 模块 | 优化内容 | 开发人 | 完成时间 |
| :--- | :--- | :--- | :--- | :--- |
| 1 | 导航 | nav-menu-icon-link 组件重构：文字区改为 flex 容器，支持可选 description 行 | 前端 | 2026-07-29 |

### 3 本次修复 Bug

无

### 4 数据库变更

无（本次未新增 Migration）

### 5 影响范围

| 影响文件 | 变更类型 |
| :--- | :--- |
| resources/views/components/ui/icon.blade.php | 修改（新增16个图标） |
| resources/views/components/nav-menu-icon-link.blade.php | 修改（新增description参数） |
| resources/views/components/app-topnav.blade.php | 修改（全部子菜单添加description） |
| database/seeders/DemoDataSeeder.php | 修改（扩展30+表示例数据） |

---

## V1.4.0 | 迭代周期：2026-07-29

负责人：项目负责人
参与开发人员：后端开发、前端开发

### 1 本次新增功能清单

| 序号 | 功能模块 | 功能点 | 开发人 | 完成时间 | 状态 |
| :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | 库存管理 | Warehouse / Inventory / InventoryLog Model + 3 个 Livewire 页面 | 后端 | 2026-07-29 | ✅ |
| 2 | 损耗管理 | LossOrder / LossOrderItem Model + 1 个 Livewire 页面 | 后端 | 2026-07-29 | ✅ |
| 3 | 拣货管理 | PickingTask / PickingTaskItem Model + 1 个 Livewire 页面 | 后端 | 2026-07-29 | ✅ |
| 4 | 配送管理 | DeliveryTask / DeliveryTaskOrder / DeliveryTrack / Signature / Temperature Model + 3 个 Livewire 页面 | 后端 | 2026-07-29 | ✅ |
| 5 | 差异处理 | Discrepancy Model + 1 个 Livewire 页面 | 后端 | 2026-07-29 | ✅ |
| 6 | 财务对账 | MerchantAccount / Recharge / SupplierSettlement / SupplierSettlementItem / SettlementPayment / Receivable / ReceivablePayment / Invoice / CorrectionAuthorization Model + 8 个 Livewire 页面 | 后端 | 2026-07-29 | ✅ |
| 7 | 退货+价格 | PurchaseReturn / PurchaseReturnItem / OrderReturn / OrderReturnItem / PriceStrategy / PriceStrategyItem / PriceChangeLog Model + 4 个 Livewire 页面 | 后端 | 2026-07-29 | ✅ |
| 8 | 扩展+系统 | PriceApportionment / MerchantAddress / MerchantFavorite / Banner / Promotion / LoginLog / WechatUser Model + 5 个 Livewire 页面 | 后端 | 2026-07-29 | ✅ |
| 9 | 路由 | 新增 26 条路由覆盖全部模块 | 后端 | 2026-07-29 | ✅ |
| 10 | 导航 | 全部导航菜单链接替换为真实路由 | 前端 | 2026-07-29 | ✅ |

### 2 本次优化/重构

| 序号 | 模块 | 优化内容 | 开发人 | 完成时间 |
| :--- | :--- | :--- | :--- | :--- |
| 1 | 导航 | 全部子菜单统一改用 icon-link 样式 | 前端 | 2026-07-29 |

### 3 本次修复 Bug

无

### 4 待办事项

| 序号 | 模块 | 待办内容 | 优先级 |
| :--- | :--- | :--- | :--- |
| 1 | 全模块 | 各模块 Detail/Form 页面待开发 | 中 |
| 2 | 全模块 | 业务逻辑 Service 层待实现 | 高 |
| 3 | 全模块 | 小程序端 API 待开发 | 高 |

### 5 累计完成统计

| 指标 | V1.3.0 | V1.4.0 | 增量 |
| :--- | :--- | :--- | :--- |
| Model 数 | 36 | 72 | +36 |
| Livewire 组件 | 28 | 54 | +26 |
| 路由数 | 30 | 56 | +26 |
| 数据库表 | 77 | 77 | — |

### 6 数据库/配置变更记录

无新增 Migration，Model 基于已有表结构创建。

### 7 发布记录

| 日期 | 版本 | 操作人 | Git Commit | 备注 |
| :--- | :--- | :--- | :--- | :--- |
| 2026-07-29 | V1.4.0 | 项目负责人 | — | 全模块 Model/Livewire/路由/导航一次性交付 |

---

## V1.3.0 | 迭代周期：2026-07-29

负责人：项目负责人
参与开发人员：后端开发、前端开发

### 1 本次新增功能清单

| 序号 | 功能模块 | 功能点 | 开发人 | 完成时间 | 状态 |
| :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | 采购管理 | PurchaseItem Model + Livewire 列表页 | 后端 | 2026-07-29 | ✅ |
| 2 | 采购管理 | PurchaseOrder / PurchaseOrderItem Model + Livewire 列表页 | 后端 | 2026-07-29 | ✅ |
| 3 | 订单配送 | Cart / CartItem Model + Livewire 列表页 | 后端 | 2026-07-29 | ✅ |
| 4 | 订单配送 | Order / OrderItem Model + Livewire 列表页 | 后端 | 2026-07-29 | ✅ |
| 5 | 订单配送 | FrequentlyBought Model + Livewire 列表页 | 后端 | 2026-07-29 | ✅ |
| 6 | 订单配送 | RepurchaseTemplate / RepurchaseTemplateItem Model + Livewire 列表页 | 后端 | 2026-07-29 | ✅ |
| 7 | 路由 | 新增 6 条路由（purchase-items / purchase-orders / orders / carts / frequently-bought / repurchase-templates） | 后端 | 2026-07-29 | ✅ |
| 8 | 导航 | 采购管理子菜单链接待采清单/采购单管理 | 前端 | 2026-07-29 | ✅ |
| 9 | 导航 | 订单配送子菜单链接客户订单/购物车/常购清单/复购模板 | 前端 | 2026-07-29 | ✅ |
| 10 | 文档 | FSD 9.2 Models 状态标注 + 9.9 映射表更新 | 后端 | 2026-07-29 | ✅ |

### 2 本次优化/重构

| 序号 | 模块 | 优化内容 | 开发人 | 完成时间 |
| :--- | :--- | :--- | :--- | :--- |
| 1 | 导航 | 采购管理子菜单改用 icon-link 样式（与商品管理/订单配送统一） | 前端 | 2026-07-29 |

### 3 本次修复 Bug

无

### 4 待办事项

| 序号 | 模块 | 待办内容 | 优先级 |
| :--- | :--- | :--- | :--- |
| 1 | 采购管理 | PurchaseOrderDetail / PurchaseReturn / PurchaseReturnDetail 页面待开发 | 中 |
| 2 | 订单配送 | OrderDetail / OrderReturn / OrderReturnDetail 页面待开发 | 中 |
| 3 | 订单配送 | 配送任务/签收存证/差异处理模块待开发 | 高 |

### 5 累计完成统计

| 指标 | V1.2.0 | V1.3.0 | 增量 |
| :--- | :--- | :--- | :--- |
| Model 数 | 26 | 36 | +10 |
| Livewire 组件 | 22 | 28 | +6 |
| 路由数 | 24 | 30 | +6 |
| 数据库表 | 77 | 77 | — |

### 6 数据库/配置变更记录

无新增 Migration，Model 基于已有表结构创建。

### 7 发布记录

| 日期 | 版本 | 操作人 | Git Commit | 备注 |
| :--- | :--- | :--- | :--- | :--- |
| 2026-07-29 | V1.3.0 | 项目负责人 | — | 采购管理+订单配送 Model/Livewire/路由/导航 |

---

## V1.2.0 | 迭代周期：2026-07-29

负责人：项目负责人
参与开发人员：后端开发、前端开发

### 1 本次新增功能清单

| 序号 | 功能模块 | 功能点 | 开发人 | 完成时间 | 状态 |
| :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | 商品管理 | Category Model（树形分类，parent_id/状态/排序） | AI | 2026-07-29 | 已完成 |
| 2 | 商品管理 | Product Model（商品主表，分类/供应商/称重改价/上下架） | AI | 2026-07-29 | 已完成 |
| 3 | 商品管理 | ProductImage Model（商品多图） | AI | 2026-07-29 | 已完成 |
| 4 | 商品管理 | Sku Model（SKU规格，价格整数存储，审核状态） | AI | 2026-07-29 | 已完成 |
| 5 | 商品管理 | MerchantSkuVisibility Model（商家SKU可见性） | AI | 2026-07-29 | 已完成 |
| 6 | 商品管理 | Tag / ProductTag Model（标签词库+商品标签关联） | AI | 2026-07-29 | 已完成 |
| 7 | 商品管理 | Keyword Model（搜索关键词联想+热度） | AI | 2026-07-29 | 已完成 |
| 8 | 条码管理 | SkuBarcode Model（4种条码类型：厂家/供应商/内部/备用） | AI | 2026-07-29 | 已完成 |
| 9 | 一品多供 | SkuSupplier Model（SKU-供应商关联，默认供应商/采购价） | AI | 2026-07-29 | 已完成 |
| 10 | 商品管理 | CategoryList Livewire（分类CRUD+搜索+弹窗表单） | AI | 2026-07-29 | 已完成 |
| 11 | 商品管理 | ProductList Livewire（商品CRUD+分类筛选+状态筛选） | AI | 2026-07-29 | 已完成 |
| 12 | 商品管理 | SkuList Livewire（SKU列表+审核状态标签） | AI | 2026-07-29 | 已完成 |
| 13 | 商品管理 | TagList Livewire（标签CRUD+搜索） | AI | 2026-07-29 | 已完成 |
| 14 | 商品管理 | KeywordList Livewire（关键词CRUD+搜索次数排序） | AI | 2026-07-29 | 已完成 |
| 15 | 条码管理 | SkuBarcodeList Livewire（条码CRUD+类型筛选） | AI | 2026-07-29 | 已完成 |
| 16 | 一品多供 | SkuSupplierList Livewire（关联CRUD+供应商下拉） | AI | 2026-07-29 | 已完成 |
| 17 | 路由 | web.php 新增 7 条路由（categories/products/skus/tags/keywords/sku-barcodes/sku-suppliers） | AI | 2026-07-29 | 已完成 |
| 18 | 导航 | 商品管理子菜单全部指向真实路由 | AI | 2026-07-29 | 已完成 |
| 19 | 文档 | FSD 9.2 Models 目录标注已创建状态，清理尾部重复残留 | AI | 2026-07-29 | 已完成 |

### 2 原有功能优化项

1. FSD 文档清理 — 删除尾部 2293-2733 行重复残留内容（旧版 Livewire 组件/Model/路由规划重复段）（2026-07-29）

### 3 BUG 修复记录

无

### 4 遗留待迭代需求（下一版本处理）

| 序号 | 需求描述 | 优先级 | 备注 |
| :--- | :--- | :--- | :--- |
| 1 | Model 第5~15批（约39个） | P0 | 采购→订单→拣货→配送→差异损耗→退货→价格策略→财务→均摊+扩展+系统+微信 |
| 2 | 安装 Redis PHP 扩展 | P1 | .env 目前用 file 驱动 |

### 5 完成统计

| 维度 | 已完成 | 待开发 | 合计 |
| :--- | :--- | :--- | :--- |
| 功能点 | 38 | 15 | 53 |
| 数据库表 | 77（已建 Migration） | 0 | 77 |
| Blade 组件 | 34+ | - | 34+ |
| Model | 26 | ~42 | ~68 |
| Livewire 组件 | 22 | - | 22 |
| 路由 | 24 | - | 24 |

### 6 版本发布记录

| 项目 | 内容 |
| :--- | :--- |
| 版本号 | V1.2.0-alpha |
| 发布时间 | 2026-07-29 |
| 部署环境 | 开发环境（Laragon，https://livewire.test） |
| Git 分支 | navigate |
| 远程仓库 | git@github.com:kindiaing/susong-livewire.git |
| 新增文件 | 10 Model + 7 Livewire 组件 + 7 Livewire 视图 |
| 修改文件 | web.php / app-topnav.blade.php / FSD 9.2 + 9.9 |

---

## V1.1.0 | 迭代周期：2026-07-29

负责人：项目负责人
参与开发人员：后端开发、前端开发

### 1 本次新增功能清单

| 序号 | 功能模块 | 功能点 | 开发人 | 完成时间 | 状态 |
| :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | 用户权限 | Role Model（继承 SpatieRole，扩展 display_name/type/parent_id） | AI | 2026-07-29 | 已完成 |
| 2 | 用户权限 | Permission Model（继承 SpatiePermission，扩展 display_name/type/parent_id） | AI | 2026-07-29 | 已完成 |
| 3 | 组织主体 | Supplier Model（供应商，含状态/类型常量、关联关系、作用域） | AI | 2026-07-29 | 已完成 |
| 4 | 组织主体 | Merchant Model（商家，含状态常量、多地址关联） | AI | 2026-07-29 | 已完成 |
| 5 | 组织主体 | DeliveryRoute Model（配送线路，含排序/状态常量） | AI | 2026-07-29 | 已完成 |
| 6 | 组织主体 | Driver Model（司机，含状态/类型常量、车辆绑定关联） | AI | 2026-07-29 | 已完成 |
| 7 | 组织主体 | Vehicle Model（车辆，含车型/状态/冷链标记常量） | AI | 2026-07-29 | 已完成 |
| 8 | 组织主体 | DriverVehicle Model（司机-车辆绑定，多对多中间表） | AI | 2026-07-29 | 已完成 |
| 9 | 用户权限 | RoleList Livewire（角色列表+搜索+弹窗表单+删除） | AI | 2026-07-29 | 已完成 |
| 10 | 用户权限 | PermissionList Livewire（权限列表+搜索+弹窗表单+删除） | AI | 2026-07-29 | 已完成 |
| 11 | 组织主体 | SupplierList Livewire（供应商CRUD+搜索+状态标签） | AI | 2026-07-29 | 已完成 |
| 12 | 组织主体 | MerchantList Livewire（商家CRUD+搜索+状态标签） | AI | 2026-07-29 | 已完成 |
| 13 | 组织主体 | RouteList Livewire（线路CRUD+搜索+排序） | AI | 2026-07-29 | 已完成 |
| 14 | 组织主体 | DriverList Livewire（司机CRUD+搜索+状态标签） | AI | 2026-07-29 | 已完成 |
| 15 | 组织主体 | VehicleList Livewire（车辆CRUD+搜索+冷链/状态标签） | AI | 2026-07-29 | 已完成 |
| 16 | 路由 | web.php 新增 7 条路由（roles/permissions/suppliers/merchants/delivery-routes/drivers/vehicles） | AI | 2026-07-29 | 已完成 |
| 17 | 导航 | 用户权限子菜单链接真实路由（角色管理→roles、权限管理→permissions） | AI | 2026-07-29 | 已完成 |
| 18 | 导航 | 组织主体全部 5 个子菜单指向真实路由 | AI | 2026-07-29 | 已完成 |
| 19 | 文档 | 08_用户使用手册.md 创建（覆盖已完成的全部功能模块操作说明） | AI | 2026-07-29 | 已完成 |
| 20 | 文档 | FSD 9.3.3 数据库目录更新为 Livewire 项目实际状态 | AI | 2026-07-29 | 已完成 |

### 2 原有功能优化项

1. .env.example 中文乱码修复 — 重写为无 BOM UTF-8 编码（2026-07-29）
2. AdminInstallCommand 引用修复 — RolePermissionSeeder → SystemDataSeeder（2026-07-29）
3. 移动端汉堡菜单代码清除 — 管理后台不做移动端适配（2026-07-29）
4. composer.json post-autoload-dump 添加自动创建缺失目录脚本（2026-07-29）

### 3 BUG 修复记录

| BUG编号 | 问题描述 | 复现步骤 | 修复方案 | 验证状态 |
| :--- | :--- | :--- | :--- | :--- |
| 8 | AdminInstallCommand 引用已删除的 RolePermissionSeeder | `php artisan admin:install --reset --force` 报错类不存在 | 改为引用 SystemDataSeeder | 已验证 |
| 9 | .env.example UTF-8 BOM 导致中文注释乱码 | Git clone 后在其他环境打开 .env.example 中文全部乱码 | 重写为无 BOM UTF-8 | 已验证 |

### 4 遗留待迭代需求（下一版本处理）

| 序号 | 需求描述 | 优先级 | 备注 |
| :--- | :--- | :--- | :--- |
| 1 | Model 第3~15批（约49个） | P0 | 商品→SKU条码→仓库库存→采购→订单→拣货→配送→差异损耗→退货→价格策略→财务→均摊+扩展+系统+微信 |
| 2 | 对应 Livewire 页面 | P0 | 每批 Model 配套列表/详情/表单页面 |
| 3 | 安装 Redis PHP 扩展 | P1 | .env 目前用 file 驱动 |
| 4 | 数据大屏统计功能 | P2 | 展示订单量、销售额、损耗率等实时指标 |

### 5 完成统计

| 维度 | 已完成 | 待开发 | 合计 |
| :--- | :--- | :--- | :--- |
| 功能点 | 38 | 15 | 53 |
| 数据库表 | 77（已建 Migration） | 0 | 77 |
| Blade 组件 | 34+ | - | 34+ |
| Model | 16 | ~52 | ~68 |
| Livewire 组件 | 15 | - | 15 |
| 路由 | 17 | - | 17 |

### 6 版本发布记录

| 项目 | 内容 |
| :--- | :--- |
| 版本号 | V1.1.0-alpha |
| 发布时间 | 2026-07-29 |
| 部署环境 | 开发环境（Laragon，https://livewire.test） |
| Git 分支 | navigate |
| 远程仓库 | git@github.com:kindiaing/susong-livewire.git |
| 新增文件 | 7 Model + 7 Livewire 组件 + 7 Livewire 视图 + 1 用户手册 |
| 修改文件 | web.php / app-topnav.blade.php / .env.example / AdminInstallCommand / composer.json / FSD |

---

## V1.0.0 | 迭代周期：2026-07-24 ~ 2026-07-29

负责人：项目负责人
参与开发人员：后端开发、前端开发、测试

### 1 本次新增功能清单

| 序号 | 功能模块 | 功能点 | 开发人 | 完成时间 | 状态 |
| :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | 项目初始化 | Laravel 13 + Livewire 4.x + Tailwind CSS 4.2 全栈脚手架搭建 | AI | 2026-07-24 | 已完成 |
| 2 | 数据库 | 20 个 Migration（77 张表），整数金额存储，无外键约束 | AI | 2026-07-27 | 已完成 |
| 3 | 数据库 | SystemDataSeeder（内置：角色/权限/超级管理员/审核节点/系统配置） | AI | 2026-07-27 | 已完成 |
| 4 | 数据库 | DemoDataSeeder（测试：9 用户 + 23 配置项 + 9 角色） | AI | 2026-07-27 | 已完成 |
| 5 | UI 组件库 | 34+ Blade 组件（shadcn/ui 设计理念），CSS 变量 + 语义化 token | AI | 2026-07-25 | 已完成 |
| 6 | 用户与权限 | 登录页（用户名/手机/邮箱多方式，节流 5 次锁定，记住 7 天） | AI | 2026-07-25 | 已完成 |
| 7 | 用户与权限 | 个人中心（修改资料 + 修改密码，中文验证，未修改不保存） | AI | 2026-07-27 | 已完成 |
| 8 | 系统支撑 | 首页欢迎页（公开，底部版权：技术栈/备案号/开发者） | AI | 2026-07-28 | 已完成 |
| 9 | 系统支撑 | 系统配置页（6 组 23 项，分组导航 + 行内编辑 + 重置默认值） | AI | 2026-07-27 | 已完成 |
| 10 | 系统支撑 | 系统设置 Drawer（右侧全屏滑入，分组 Tab，行内编辑，替代独立页面） | AI | 2026-07-29 | 已完成 |
| 11 | 系统支撑 | 操作日志（LogOperation 中间件，自动记录写操作，忽略 Livewire 内部） | AI | 2026-07-28 | 已完成 |
| 12 | 系统支撑 | 审计日志页面（动作/模型/操作人/日期筛选，数据快照弹窗） | AI | 2026-07-28 | 已完成 |
| 13 | 审核管理 | 审核配置页面（19 节点开关，统计卡片，模块/风险筛选，Alert Dialog 确认） | AI | 2026-07-28 | 已完成 |
| 14 | 审核管理 | 审批列表页面（Tab 筛选，详情弹窗，通过/拒绝/撤回，Alert Dialog 确认） | AI | 2026-07-28 | 已完成 |
| 15 | 审核管理 | ApprovalService（创建/通过/拒绝/撤回，防重复，审计日志联动） | AI | 2026-07-28 | 已完成 |
| 16 | 消息通知 | 通知 Drawer（右侧全屏，数据库加载，未读角标，Tab 筛选，标记已读） | AI | 2026-07-28 | 已完成 |
| 17 | 消息通知 | RestockReminder Model（智能补货提醒规则） | AI | 2026-07-28 | 已完成 |
| 18 | 导航 | 顶部导航栏（Logo 居左 + 菜单搜索居中 + 个人中心居右） | AI | 2026-07-27 | 已完成 |
| 19 | 导航 | Command 搜索面板（Ctrl+K，Alpine.js 命令面板） | AI | 2026-07-27 | 已完成 |
| 20 | 导航 | 系统管理子菜单链接真实路由（settings/approval-config/operation-logs/audit-logs） | AI | 2026-07-28 | 已完成 |
| 21 | UI 增强 | Alert Dialog 组件（shadcn/ui 风格，4 种 variant，支持 Livewire 回调） | AI | 2026-07-28 | 已完成 |
| 22 | UI 增强 | Toast 通知（全局 Alpine store，5 种类型，统一 duration 5s） | AI | 2026-07-28 | 已完成 |
| 23 | 安全 | CSRF Token 过期自动刷新（419 → reload，用户无感知） | AI | 2026-07-29 | 已完成 |
| 24 | 组织主体 | 供应商管理 | AI | 2026-07-29 | V1.1.0 已完成 |
| 25 | 组织主体 | 商家管理（含多地址） | AI | 2026-07-29 | V1.1.0 已完成 |
| 26 | 组织主体 | 配送线路/司机/车辆管理 | AI | 2026-07-29 | V1.1.0 已完成 |
| 27 | 商品管理 | 分类/商品/SKU/条码/一品多供/可见性/关键词 | AI | 2026-07-29 | V1.2.0 已完成 |
| 28 | 平台统采 | 待采清单/采购单/采购入库/采购退货 | - | - | 待开发 |
| 29 | 客户直采 | 购物车/订单/称重改价/售后退货 | - | - | 待开发 |
| 30 | 库存管理 | 仓库/实时库存/库存日志/库存预警 | - | - | 待开发 |
| 31 | 损耗管理 | 损耗单（申报→审核→扣减→成本归集） | - | - | 待开发 |
| 32 | 拣货管理 | 拣货任务创建/分配/执行 | - | - | 待开发 |
| 33 | 物流配送 | 配送任务/轨迹/签收/冷链温度 | - | - | 待开发 |
| 34 | 差异处理 | 拣货/配送/实收差异，金额调整审批 | - | - | 待开发 |
| 35 | 财务对账 | 客户账户/充值/供应商结算/应收/发票/授权更正/费用均摊 | - | - | 待开发 |
| 36 | 价格策略 | 策略管理/策略明细/改价记录 | - | - | 待开发 |
| 37 | 微信小程序商家端 | 登录/首页/搜索/商品详情/购物车/订单/签收/账户/消息/补货提醒 | - | - | 待开发 |
| 38 | 微信小程序司机端 | 登录/任务/配送/轨迹/签收/差异标记/历史 | - | - | 待开发 |

### 2 原有功能优化项

1. 退出登录后跳转首页 `/` 而非 `/login`（2026-07-28）
2. SystemConfig 缓存反序列化防御 — `instanceof` 检查 + try/catch（2026-07-28）
3. 通知 Drawer 后端化 — 从 app.js 硬编码改为 Livewire 组件数据库加载（2026-07-28）
4. Migration 整理 — 25→20 个文件，5 个 alter 合并到 create，时间戳编号 000001~000020（2026-07-28）
5. Seeder 整理 — SystemDataSeeder（内置）+ DemoDataSeeder（测试）（2026-07-28）
6. 审核管理/审批列表 — wire:confirm 替换为 shadcn/ui Alert Dialog（2026-07-28）
7. 系统设置 Drawer — 从底部窄面板改为右侧全屏 Drawer，整合全部 6 组配置项（2026-07-29）
8. 系统管理图标 — 从 cog 改为 adjustments-horizontal（锯齿设置图标）（2026-07-29）
9. 审核节点全景图 — 合并到 FSD 文档，删除独立 md 文件（2026-07-29）

### 3 BUG 修复记录

| BUG编号 | 问题描述 | 复现步骤 | 修复方案 | 验证状态 |
| :--- | :--- | :--- | :--- | :--- |
| 1 | Livewire 组件 `private` 属性请求间不持久化 | 个人中心修改资料对比原始值失败 | 改为从数据库重读原始值对比 | 已验证 |
| 2 | SystemConfig 缓存反序列化返回 `__PHP_Incomplete_Class` | Migration 新增列后缓存未刷新 | `getAll()` 增加 instanceof 检查 + try/catch | 已验证 |
| 3 | `Setting::get()` 在数据库表不存在时报错 | 未执行 migrate 时访问首页 | try/catch 包裹 | 已验证 |
| 4 | audit-logs.blade.php 嵌套三元 `}}}` 导致 ParseError | 复杂嵌套三元运算符 | `@php` 预处理颜色映射数组 | 已验证 |
| 5 | LogOperation 中间件记录 Livewire 内部请求 | 所有 Livewire POST 请求被写入操作日志 | 忽略路径列表增加 `livewire-` 前缀 | 已验证 |
| 6 | CSRF Token 过期导致 419 "This page has expired" | Session 过期后提交登录表单 | 异常处理捕获 TokenMismatchException + 前端 419 自动 reload | 已验证 |
| 7 | Toast duration 不一致（4s vs 5s） | 多处 toast 超时时间不统一 | store 默认超时统一为 5s | 已验证 |

### 4 遗留待迭代需求（下一版本处理）

| 序号 | 需求描述 | 优先级 | 备注 |
| :--- | :--- | :--- | :--- |
| 1 | Model 层开发（第3~15批） | P0 | 商品/SKU/库存/采购/订单/拣货/配送/差异/退货/价格/财务等约 49 个 |
| 2 | 安装 Redis PHP 扩展 | P1 | .env 目前用 file 驱动 |
| 3 | 数据大屏统计功能 | P2 | 展示订单量、销售额、损耗率等实时指标 |
| 4 | 操作日志导出 | P2 | 支持按时间范围、操作人、模块导出 |
| 5 | 效期管理（二期损耗增强） | P2 | 效期预警、自动触发损耗申报 |
| 6 | 损耗分析报表（二期） | P2 | 按品类、供应商、时间维度损耗趋势分析 |
| 7 | AI 智能补货推荐 | P3 | 基于采购频次和库存自动推荐补货清单 |

### 5 完成统计

| 维度 | 已完成 | 待开发 | 合计 |
| :--- | :--- | :--- | :--- |
| 功能点 | 23 | 15 | 38 |
| 数据库表 | 77（已建 Migration） | 0 | 77 |
| Blade 组件 | 34+ | - | 34+ |
| Model | 8 | ~60 | ~68 |
| Livewire 组件 | 8 | - | 8 |
| 路由 | 10 | - | 10 |

### 6 版本发布记录

| 项目 | 内容 |
| :--- | :--- |
| 版本号 | V1.0.0-alpha |
| 发布时间 | 待定 |
| 部署环境 | 开发环境（Laragon，https://livewire.test） |
| Git 分支 | navigate |
| 远程仓库 | git@github.com:kindiaing/susong-livewire.git |
| 超级管理员 | 用户名: seeding / 密码: Password |

### 7 数据库变更记录

| Migration 文件名 | 操作类型 | 涉及表 | 说明 |
| :--- | :--- | :--- | :--- |
| 2026_07_27_000001~000020 | CREATE | 77 张表 | 完整建表，无外键约束，整数金额存储 |
| SystemDataSeeder | INSERT | roles/permissions/users/approval_type_configs/system_configs | 内置数据，生产环境必需 |
| DemoDataSeeder | INSERT | users/notifications | 测试数据，仅开发环境 |

### 8 配置变更记录

| 配置项 | 变更前 | 变更后 | 说明 |
| :--- | :--- | :--- | :--- |
| SESSION_DRIVER | redis | file | Redis 扩展未安装 |
| CACHE_STORE | redis | file | Redis 扩展未安装 |

---

## 迭代日志模板（后续版本使用）

## V1.X.X | 迭代周期：YYYY-MM-DD ~ YYYY-MM-DD

负责人：
参与开发人员：

### 1 本次新增功能清单

| 序号 | 功能模块 | 功能点 | 开发人 | 完成时间 | 状态 |
| :--- | :--- | :--- | :--- | :--- | :--- |
| - | - | - | - | - | - |

### 2 原有功能优化项

1.

### 3 BUG 修复记录

| BUG编号 | 问题描述 | 复现步骤 | 修复方案 | 验证状态 |
| :--- | :--- | :--- | :--- | :--- |
| - | - | - | - | - |

### 4 遗留待迭代需求（下一版本处理）

| 序号 | 需求描述 | 优先级 | 备注 |
| :--- | :--- | :--- | :--- |
| - | - | - | - |

### 5 版本发布记录

| 项目 | 内容 |
| :--- | :--- |
| 版本号 | V1.X.X |
| 发布时间 | |
| 部署环境 | 测试环境/生产环境 |
| 更新文件清单 | |

### 6 数据库变更记录

| Migration 文件名 | 操作类型 | 涉及表 | 说明 |
| :--- | :--- | :--- | :--- |
| - | - | - | - |

### 7 API 变更记录

| 接口路径 | 变更类型 | 说明 |
| :--- | :--- | :--- |
| - | 新增/修改/废弃 | - |

### 8 配置变更记录

| 配置项 | 变更前 | 变更后 | 说明 |
| :--- | :--- | :--- | :--- |
| - | - | - | - |

> AI生成