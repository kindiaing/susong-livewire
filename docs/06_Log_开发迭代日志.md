
# 开发迭代日志

对应 PRD 版本：V1.2
对应 FSD 版本：V1.2
技术栈：Laravel 13 + Livewire 4.x + Tailwind CSS 4.2+ + Alpine.js + PHP 8.4+ + MySQL 8.0 + Redis 7.x

记录规则：每次迭代新增一节，按版本号倒序排列。每条变更需标注开发人、完成时间和关联模块。

---

## V1.7.0 | 迭代周期：2026-07-31

负责人：项目负责人
参与开发人员：后端开发

### 1 本次新增功能清单

| 序号 | 功能模块 | 功能点 | 开发人 | 完成时间 | 状态 |
| :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | Artisan 命令 | 新增 `admin:create-admin` 命令（替代废弃的 `admin:make-user`），支持 `--name=` `--email=` `--password=` `--role=super_admin` `--guard=web` | 后端 | 2026-07-31 | ✅ |
| 2 | Artisan 命令 | 新增 `admin:create-user` 命令，支持 `--name=` `--email=` `--password=` `--role=` `--guard=web` `--active=1` | 后端 | 2026-07-31 | ✅ |
| 3 | Artisan 命令 | `admin:seed` 增强：`--demo=MODULE` 支持可选模块参数（不传=全部测试数据，传值=指定模块）；新增 `--list` 选项列出可用 Seeder 模块 | 后端 | 2026-07-31 | ✅ |
| 4 | Seeder 体系 | 按模块拆分测试数据：DemoDataSeeder 改为总入口，新增 Demo/ 子目录 11 个分模块 Seeder（含 TestUsersDemoSeeder） | 后端 | 2026-07-31 | ✅ |
| 5 | Seeder 体系 | SystemDataSeeder 不再创建 `superadmin` 测试用户，改为给 Migration 创建的 `seeding` 账户分配 super_admin 角色 + 全部 140 权限 | 后端 | 2026-07-31 | ✅ |
| 6 | Seeder 体系 | 双入口兼容：Seeder 同时支持 `php artisan db:seed` 和 `php artisan admin:seed` 调用 | 后端 | 2026-07-31 | ✅ |
| 7 | 文档 | 03_DB 新增第 9 章「Seeder 体系设计」（设计原则/目录结构/核心vs测试对照/模块依赖/双入口/模块清单速查） | 后端 | 2026-07-31 | ✅ |
| 8 | 文档 | 05_Setup 更新第 9/10/12 章（初始化数据分层说明/命令速查表更新/部署检查清单密码修正） | 后端 | 2026-07-31 | ✅ |
| 9 | 文档 | 06_Log 新增 V1.7.0 迭代记录 | 后端 | 2026-07-31 | ✅ |

### 2 本次优化/重构

| 序号 | 模块 | 优化内容 | 开发人 | 完成时间 |
| :--- | :--- | :--- | :--- | :--- |
| 1 | Artisan 命令 | 废弃 `admin:make-user`，统一用 `admin:create-admin` + `admin:create-user` 替代 | 后端 | 2026-07-31 |
| 2 | Seeder 体系 | DemoDataSeeder 解耦：移除对 SystemDataSeeder 的硬调用，测试数据可独立运行 | 后端 | 2026-07-31 |
| 3 | Seeder 体系 | 测试数据按模块拆分到 Demo/ 子目录，支持 `--demo=organization` 等分模块单独运行 | 后端 | 2026-07-31 |

### 3 本次修复 Bug

无。

### 4 数据库变更

无（本次未新增 Migration，仅调整 Seeder 和命令层）。

### 5 影响范围

| 影响文件 | 变更类型 |
| :--- | :--- |
| app/Console/Commands/AdminSeedCommand.php | 修改（新增 --demo=MODULE / --list 选项） |
| app/Console/Commands/AdminCreateAdminCommand.php | 新增（替代 AdminMakeUserCommand） |
| app/Console/Commands/AdminCreateUserCommand.php | 修改（增强选项：--role / --guard / --active） |
| app/Console/Commands/AdminMakeUserCommand.php | 废弃（标记 @deprecated，指引到 admin:create-admin） |
| database/seeders/SystemDataSeeder.php | 重写（去掉 superadmin 创建，改为给 seeding 分配角色权限） |
| database/seeders/DemoDataSeeder.php | 重写（改为总入口，调用 Demo/ 下各分模块） |
| database/seeders/Demo/OrganizationDemoSeeder.php | 新增（组织主体测试数据） |
| database/seeders/Demo/ProductDemoSeeder.php | 新增（商品管理测试数据） |
| database/seeders/Demo/PurchaseDemoSeeder.php | 新增（采购管理测试数据） |
| database/seeders/Demo/OrderDemoSeeder.php | 新增（订单管理测试数据） |
| database/seeders/Demo/InventoryDemoSeeder.php | 新增（库存管理测试数据） |
| database/seeders/Demo/DeliveryDemoSeeder.php | 新增（配送管理测试数据） |
| database/seeders/Demo/FinanceDemoSeeder.php | 新增（财务对账测试数据） |
| database/seeders/Demo/PriceDemoSeeder.php | 新增（价格策略测试数据） |
| database/seeders/Demo/LossDemoSeeder.php | 新增（损耗管理测试数据） |
| database/seeders/Demo/SystemDemoSeeder.php | 新增（系统支撑测试数据） |
| database/seeders/DatabaseSeeder.php | 修改（确保双入口兼容） |
| docs/03_DB_数据库设计&数据字典.md | 修改（新增第 9 章 Seeder 体系设计） |
| docs/05_Setup_安装部署配置手册.md | 修改（第 9/10/12 章更新） |
| docs/06_Log_开发迭代日志.md | 修改（新增 V1.7.0 迭代记录） |

### 6 关键技术记录

**Seeder 分模块设计：**
- 设计原则：核心数据（SystemDataSeeder）与测试数据（DemoDataSeeder + Demo/*.php）严格分离
- 核心数据包含：9 角色 + 140 权限树 + seeding 账户角色分配 + 19 审核节点配置 + 24 条系统配置
- 测试数据按业务模块拆分为 10 个独立 Seeder，放在 `database/seeders/Demo/` 子目录
- 模块依赖顺序：organization → product → purchase → order → inventory → delivery → finance → price → loss → system-demo
- 双入口兼容：`php artisan db:seed --class=DemoDataSeeder` 等价于 `php artisan admin:seed --demo`
- `admin:seed --demo=organization` 可单独运行指定模块测试数据
- `admin:seed --list` 列出所有可用 Seeder 模块及其说明

**Migration vs Seeder 职责划分：**
- Migration 8.1：创建默认管理员 `seeding` 账户 + 9 个系统角色 + model_has_roles 关联
- SystemDataSeeder：为 `seeding` 账户分配 super_admin 角色 + 全部 140 权限（syncPermissions）
- 生产环境只需运行 `migrate` + `admin:seed --system`，即可获得完整可用系统

---

## V1.6.5 | 迭代周期：2026-07-30

负责人：项目负责人
参与开发人员：后端开发、前端开发

### 1 本次新增功能清单

| 序号 | 功能模块 | 功能点 | 开发人 | 完成时间 | 状态 |
| :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | Artisan 命令 | 新增 `admin:seed` 命令，支持 `--fresh`/`--demo`/`--system`/`--force` 选项，可单独运行种子数据 | 后端 | 2026-07-30 | ✅ |

### 2 本次优化/重构

| 序号 | 模块 | 优化内容 | 开发人 | 完成时间 |
| :--- | :--- | :--- | :--- | :--- |
| 1 | 权限分配弹窗 | 三态 checkbox 彻底重构：权限树数据缓存到 `$permissionTreeData` 组件属性，避免每次 toggle 查数据库 | 后端 | 2026-07-30 |
| 2 | 权限分配弹窗 | 模块/页面 checkbox 改为 `wire:click` + `@checked` + Alpine `x-effect` 驱动 indeterminate 三态 | 前端 | 2026-07-30 |
| 3 | 权限分配弹窗 | 按钮级 checkbox 改为 `wire:click="togglePermission(id)"` + `@checked`，去掉 `wire:model.live` | 前端 | 2026-07-30 |
| 4 | Git 管理 | `database/database.sqlite` 加入版本控制 | 后端 | 2026-07-30 |
| 5 | Composer | 清理 `composer.json` 冗余脚本 | 后端 | 2026-07-30 |

### 3 本次修复 Bug

| 序号 | Bug描述 | 修复内容 | 开发人 | 完成时间 |
| :--- | :--- | :--- | :--- | :--- |
| 1 | 权限分配弹窗模块/页面 checkbox 不反映子权限选中状态 | `@checked` 改为调用 `getModuleState()`/`getPageState()`，用 `array_intersect` 计算交集比例判断 | 后端 | 2026-07-30 |
| 2 | 原生 checkbox indeterminate 在 Livewire morphing 后不重新设置 | 改用 Alpine `x-effect` 监听 checked 状态自动设置 indeterminate | 前端 | 2026-07-30 |
| 3 | 按钮级 `wire:model.live` 写入字符串类型 ID 与 PHP 端 int 类型比较不一致 | 改为 `wire:click="togglePermission(id)"` + `@checked`，PHP 端统一用 int 比较 | 后端 | 2026-07-30 |

### 4 数据库变更

无。

### 5 影响范围

| 影响文件 | 变更类型 |
| :--- | :--- |
| app/Console/Commands/AdminSeedCommand.php | 新增（独立 seed 命令） |
| app/Livewire/User/RoleList.php | 修改（permissionTreeData 缓存 + toggleModulePermissions/togglePagePermissions/togglePermission + getModuleState/getPageState） |
| resources/views/livewire/user/role-list.blade.php | 修改（checkbox + `@checked` + Alpine `x-effect` indeterminate） |
| database/database.sqlite | 新增（加入 git 版本控制） |
| composer.json | 修改（清理冗余脚本） |
| docs/05_Setup_安装部署配置手册.md | 修改（补充 admin:seed 命令） |
| docs/06_Log_开发迭代日志.md | 修改（补充 V1.6.5） |
| docs/08_用户使用手册.md | 修改（更新版本号 + 权限弹窗三态说明） |

### 6 关键技术记录

**权限树三态 checkbox 重构：**
- 根因：原实现存在三个问题：(1) `@checked` 只检查模块/页面自身 ID 不反映子权限状态；(2) 原生 checkbox indeterminate 用 `x-init` 设置，Livewire morphing 后不重新执行；(3) `wire:model.live` 写入字符串类型 ID 与 PHP 端 int 比较不一致
- 修复方案：
  1. 权限树数据缓存到 `$permissionTreeData`，避免每次 toggle 查数据库
  2. 模块/页面 checkbox 用 `wire:click` + `@checked(getModuleState($module_id))` / `@checked(getPageState($page_id))`，通过 `array_intersect` 计算交集比例判断选中/半选/未选
  3. Alpine `x-effect` 监听 checked 变化自动设置 `el.indeterminate`
  4. 按钮级 checkbox 改为 `wire:click="togglePermission($id)"` + `@checked(in_array($id, $selectedPermissions))`

---

## V1.6.4 | 迭代周期：2026-07-30

负责人：项目负责人
参与开发人员：后端开发、前端开发

### 1 本次新增功能清单

| 序号 | 功能模块 | 功能点 | 开发人 | 完成时间 | 状态 |
| :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | 全局 | 新增 `WithToast` Trait，统一 Toast 消息分发接口 | 后端 | 2026-07-30 | ✅ |
| 2 | 全局 | 新增 `docs/09_开发前指导手册.md`，汇总所有开发规范 | 后端 | 2026-07-30 | ✅ |

### 2 本次优化/重构

| 序号 | 模块 | 优化内容 | 开发人 | 完成时间 |
| :--- | :--- | :--- | :--- | :--- |
| 1 | 全局（49个组件+3个Trait） | 所有 `dispatch('toast', message: '...')` 替换为 `WithToast` 方法（`toastSuccess`/`toastError`/`toastWarning`/`toastInfo`） | 后端 | 2026-07-30 |

### 3 本次修复 Bug

| 序号 | Bug描述 | 修复内容 | 开发人 | 完成时间 |
| :--- | :--- | :--- | :--- | :--- |
| 1 | 权限分配弹窗点击始终全选，无法取消勾选 | 模块/页面 checkbox 去掉 `wire:model.live`，只保留 `wire:click` + `@checked()`，避免两者冲突 | 前端 | 2026-07-30 |
| 2 | 全部模块 Toast 提示无文字 | 创建 `WithToast` Trait 统一使用 `title` 字段，批量替换 49 个组件文件 | 后端 | 2026-07-30 |

### 4 数据库变更

无。

### 5 影响范围

| 影响文件 | 变更类型 |
| :--- | :--- |
| app/Livewire/Traits/WithToast.php | 新增（Toast 统一 Trait） |
| app/Livewire/Traits/WithRowSelection.php | 修改（use WithToast; + dispatch 替换） |
| app/Livewire/Traits/WithExcelExport.php | 修改（use WithToast; + dispatch 替换） |
| app/Livewire/Traits/WithExcelImport.php | 修改（use WithToast; + dispatch 替换） |
| 46 个 Livewire 组件文件 | 修改（添加 use WithToast + dispatch 替换为 toastSuccess/Error/Warning/Info） |
| resources/views/livewire/user/role-list.blade.php | 修改（权限 checkbox 修复） |
| docs/09_开发前指导手册.md | 新增（开发规范汇总） |

### 6 关键技术记录

**checkbox 的 wire:model.live 与 wire:click 冲突：**
- 根因：checkbox 同时绑定 `wire:model.live="formArray"` 和 `wire:click="toggleMethod()"` 时，Livewire 先执行 model 绑定自动切换值，再执行 click 方法，导致自定义逻辑基于已修改的数组判断
- 修复：联动 checkbox 只用 `wire:click` + `@checked()` 手动控制选中状态

**Toast 事件字段不匹配：**
- 根因：后端 `dispatch('toast', message: '...')` 发出的 detail 字段为 `message`，前端 `$toast.show()` 期望 `title` 字段
- 修复：创建 `WithToast` Trait 统一使用 `title` + `description` + `type` 字段

---

## V1.6.3 | 迭代周期：2026-07-30

负责人：项目负责人
参与开发人员：后端开发、前端开发

### 1 本次新增功能清单

无新增功能模块。

### 2 本次优化/重构

| 序号 | 模块 | 优化内容 | 开发人 | 完成时间 |
| :--- | :--- | :--- | :--- | :--- |
| 1 | 角色管理 | 权限分配弹窗改为表格形式（4列：复选框、模块、页面、按钮），弹窗加宽到 max-w-4xl | 前端 | 2026-07-30 |
| 2 | 角色管理 | 模块勾选自动全选子权限（toggleModulePermissions），页面勾选自动全选子按钮（togglePagePermissions） | 后端 | 2026-07-30 |

### 3 本次修复 Bug

| 序号 | Bug描述 | 修复内容 | 开发人 | 完成时间 |
| :--- | :--- | :--- | :--- | :--- |
| 1 | 已登录用户访问 /login 不自动跳转 | Login 组件 mount() 增加 auth()->check() 跳转到 dashboard | 后端 | 2026-07-30 |
| 2 | Toast 提示不显示文字 | app.js 事件监听增加 message→title 映射 | 前端 | 2026-07-30 |

### 4 数据库变更

无。

### 5 影响范围

| 影响文件 | 变更类型 |
| :--- | :--- |
| app/Livewire/Auth/Login.php | 修改（mount 增加已登录跳转） |
| resources/js/app.js | 修改（toast 事件监听 message→title 映射） |
| app/Livewire/User/RoleList.php | 修改（新增 toggleModulePermissions/togglePagePermissions） |
| resources/views/livewire/user/role-list.blade.php | 修改（权限弹窗改表格形式） |
| public/build/ | 重建（Vite 构建更新） |

---

## V1.6.2 | 迭代周期：2026-07-30

负责人：项目负责人
参与开发人员：后端开发、前端开发

### 1 本次新增功能清单

| 序号 | 功能模块 | 功能点 | 开发人 | 完成时间 | 状态 |
| :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | 权限管理 | 类型下拉筛选（全部/模块/页面/按钮） | 后端 | 2026-07-30 | ✅ |
| 2 | 权限管理 | 模块下拉筛选（全部/指定模块，含子级页面+按钮） | 后端 | 2026-07-30 | ✅ |
| 3 | 权限管理 | 类型+模块组合筛选 + 重置按钮 | 后端 | 2026-07-30 | ✅ |

### 2 本次优化/重构

无。

### 3 本次修复 Bug

| 序号 | Bug描述 | 修复内容 | 开发人 | 完成时间 |
| :--- | :--- | :--- | :--- | :--- |
| 1 | UTF-8 BOM 导致 Livewire 4 组件边界错位 | 批量移除 38 个 Blade 视图文件的 UTF-8 BOM（\xEF\xBB\xBF） | 后端 | 2026-07-30 |
| 2 | 弹窗取消按钮被 Livewire 验证管道拦截 | 用户/角色/权限三个页面的所有 `<button>` 添加 `type="button"` | 前端 | 2026-07-30 |

### 4 数据库变更

无。

### 5 影响范围

| 影响文件 | 变更类型 |
| :--- | :--- |
| app/Livewire/User/PermissionList.php | 修改（增加 filterType/filterModule 属性、筛选逻辑、resetFilters） |
| resources/views/livewire/user/permission-list.blade.php | 修改（增加类型+模块两个 select 下拉筛选、重置按钮） |
| 38 个 Blade 视图文件 | 修改（移除 UTF-8 BOM） |
| resources/views/livewire/user/user-list.blade.php | 修改（button 添加 type="button"） |
| resources/views/livewire/user/role-list.blade.php | 修改（button 添加 type="button"） |
| resources/views/livewire/user/permission-list.blade.php | 修改（button 添加 type="button"） |

### 6 关键技术记录

**UTF-8 BOM 导致 Livewire 4 morphing 错位问题：**
- 根因：Blade 视图文件开头包含 UTF-8 BOM（\xEF\xBB\xBF），在组件根 `<div>` 前产生了不可见文本节点
- 影响：Livewire 4 morphing 算法将 `wire:id` 绑定到错误子元素（如 `class="flex items-center justify-between mb-6"` 而非 `class="p-6"`），导致弹窗内容和操作按钮脱离组件 DOM 边界
- 修复：批量移除所有 Blade 文件 BOM + 为 `<button>` 添加 `type="button"` 防止 form 提交行为

---

## V1.6.1 | 迭代周期：2026-07-30

负责人：项目负责人
参与开发人员：后端开发、前端开发

### 1 本次新增功能清单

无新增功能模块。

### 2 本次优化/重构

| 序号 | 模块 | 优化内容 | 开发人 | 完成时间 |
| :--- | :--- | :--- | :--- | :--- |
| 1 | 用户管理 | 状态列改为 toggle switch 开关，行内直接点击切换启用/禁用 | 前端 | 2026-07-30 |
| 2 | 用户管理 | 操作按钮从文字改为 SVG 图标（编辑/角色分配/重置密码/删除） | 前端 | 2026-07-30 |
| 3 | 角色管理 | 操作按钮从文字改为 SVG 图标（编辑/权限分配/删除） | 前端 | 2026-07-30 |
| 4 | 权限管理 | 操作按钮从文字改为 SVG 图标（编辑/角色分配/删除） | 前端 | 2026-07-30 |
| 5 | 用户管理 | 超级管理员（super-admin 角色）禁止删除和禁用，列表不显示删除按钮和 toggle 开关 | 后端 | 2026-07-30 |
| 6 | 角色管理 | super-admin 角色禁止删除和编辑，列表不显示编辑/删除按钮 | 后端 | 2026-07-30 |

### 3 本次修复 Bug

| 序号 | Bug描述 | 修复内容 | 开发人 | 完成时间 |
| :--- | :--- | :--- | :--- | :--- |
| 1 | 超级管理员可被删除/禁用 | delete() 和 toggleStatus() 增加 hasRole('super-admin') 检查 | 后端 | 2026-07-30 |
| 2 | super-admin 角色可被删除/编辑 | RoleList delete() 和 openEditModal() 增加 name==='super-admin' 保护 | 后端 | 2026-07-30 |

### 4 数据库变更

无。

### 5 影响范围

| 影响文件 | 变更类型 |
| :--- | :--- |
| app/Livewire/User/UserList.php | 修改（增加超级管理员保护逻辑） |
| app/Livewire/User/RoleList.php | 修改（增加超级管理员角色保护逻辑） |
| resources/views/livewire/user/user-list.blade.php | 修改（toggle switch + 图标按钮 + 超级管理员行 UI 保护） |
| resources/views/livewire/user/role-list.blade.php | 修改（图标按钮 + 超级管理员角色行 UI 保护） |
| resources/views/livewire/user/permission-list.blade.php | 修改（图标按钮） |

---

## V1.6.0 | 迭代周期：2026-07-29

负责人：项目负责人
参与开发人员：后端开发、前端开发

### 1 本次新增功能清单

| 序号 | 功能模块 | 功能点 | 开发人 | 完成时间 | 状态 |
| :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | 补货提醒 | RestockReminderList Livewire 页面（CRUD + 商家/SKU 选择 + 周期配置） | 后端 | 2026-07-29 | ✅ |
| 2 | 采购退货 | PurchaseReturnList 从桩升级为完整 CRUD（关联采购单 + 供应商 + 仓库 + 退货原因） | 后端 | 2026-07-29 | ✅ |
| 3 | 仓库管理 | WarehouseList 从桩升级为完整 CRUD（类型/冷链/地址/状态） | 后端 | 2026-07-29 | ✅ |
| 4 | 库存管理 | InventoryList 从桩升级为完整 CRUD（仓库/SKU/库存数量/批次/效期/预警值） | 后端 | 2026-07-29 | ✅ |
| 5 | 库存日志 | InventoryLogList 从桩升级为只读日志查看器（类型/仓库筛选，禁止删除） | 后端 | 2026-07-29 | ✅ |
| 6 | 损耗管理 | LossOrderList 从桩升级为完整 CRUD + 审核流程（新建/编辑/审核/执行/关闭） | 后端 | 2026-07-29 | ✅ |
| 7 | 路由 | 新增 /restock-reminders 路由 | 后端 | 2026-07-29 | ✅ |
| 8 | 用户管理 | UserList Livewire 组件（CRUD + 角色分配 + 禁用启用 + 重置密码 + 搜索） | 后端 | 2026-07-29 | ✅ |
| 9 | 角色管理 | RoleList 增加权限分配弹窗（模块→页面→按钮 树形复选框） | 后端 | 2026-07-29 | ✅ |
| 10 | 权限管理 | PermissionList 增加角色分配弹窗（多选框，为权限勾选关联角色） | 后端 | 2026-07-29 | ✅ |
| 11 | 路由 | 新增 /users 路由 + 导航修复 href="#" → route('users') | 后端 | 2026-07-29 | ✅ |
| 12 | 全局基础设施 | 安装 maatwebsite/excel + 创建4个通用Trait(WithRowSelection/WithColumnVisibility/WithExcelExport/WithExcelImport) + GenericExport/GenericImport + money_format/status_badge 助手函数 | 后端 | 2026-07-30 | ✅ |
| 13 | 全模块升级 | 49个列表组件全面升级：checkbox多选、批量操作、自定义显示列、Excel导入导出（自定义列）、金额格式化 | 后端 | 2026-07-30 | ✅ |
| 14 | 订单模块 | OrderList/CartList/OrderReturnList 从桩升级为完整CRUD+状态流转+金额格式化 | 后端 | 2026-07-30 | ✅ |
| 15 | 配送模块 | DeliveryTaskList/SignatureList/DiscrepancyList/TemperatureList 升级（修复温度记录搜索bug） | 后端 | 2026-07-30 | ✅ |
| 16 | 财务模块 | 5个财务组件升级为完整CRUD+审核/付款/收款操作+金额格式化+搜索优化 | 后端 | 2026-07-30 | ✅ |
| 17 | 价格策略 | 3个价格组件升级为完整CRUD+审核启停操作 | 后端 | 2026-07-30 | ✅ |
| 18 | 采购库存 | 8个采购/库存组件升级为完整CRUD+状态流转+搜索优化 | 后端 | 2026-07-30 | ✅ |

### 2 本次优化/重构

| 序号 | 模块 | 优化内容 | 开发人 | 完成时间 |
| :--- | :--- | :--- | :--- | :--- |
| 1 | Model | Warehouse / LossOrder / PurchaseReturn 修复 SoftDeletes trait 错位到类外的 Bug | 后端 | 2026-07-29 |
| 2 | Model | Inventory / InventoryLog / LossOrderItem / PurchaseReturnItem 清理未使用的 SoftDeletes import | 后端 | 2026-07-29 |
| 3 | Model | Warehouse 补充 statusMap / relationships / scopeEnabled | 后端 | 2026-07-29 |
| 4 | Model | LossOrder 补充 statusMap / approvalStatusMap / relationships / scopePending | 后端 | 2026-07-29 |
| 5 | Model | PurchaseReturn 补充 statusMap / relationships / scopePending | 后端 | 2026-07-29 |
| 6 | Model | Inventory 补充 warehouse/sku relationships / scopeBelowWarning | 后端 | 2026-07-29 |
| 7 | Model | InventoryLog 补充 warehouse/sku/operator relationships / typeLabel accessor | 后端 | 2026-07-29 |
| 8 | Model | LossOrderItem 补充 responsiblePartyMap / lossOrder/sku/supplier relationships | 后端 | 2026-07-29 |
| 9 | Model | PurchaseReturnItem 补充 purchaseReturn/sku relationships | 后端 | 2026-07-29 |
| 10 | Model | RestockReminder 补充 remindCycleMap / statusMap / merchant/sku relationships / scopeEnabled | 后端 | 2026-07-29 |

### 3 本次修复 Bug

| 序号 | Bug描述 | 修复内容 | 开发人 | 完成时间 |
| :--- | :--- | :--- | :--- | :--- |
| 1 | Warehouse/LossOrder/PurchaseReturn 的 SoftDeletes trait 写在 class 外面导致软删除不生效 | 将 `use SoftDeletes` 移入 class 内部 | 后端 | 2026-07-29 |
| 2 | InventoryList 搜索 sku_id 使用 like 查整型字段 | 改为通过 sku 关联搜索 sku_code / product.name | 后端 | 2026-07-29 |
| 3 | InventoryLogList 允许删除日志记录 | 移除删除功能，改为只读日志查看器 | 后端 | 2026-07-29 |
| 4 | 弹窗取消按钮不关闭 | wire:click="showModal=false" 被 Livewire 验证管道拦截，改用 closeXxxModal() 方法 | 后端 | 2026-07-30 |
| 5 | Model 重复 SoftDeletes import | Banner/Discrepancy/MerchantAddress/OrderReturn/PriceStrategy 删除多余 use SoftDeletes 行 | 后端 | 2026-07-30 |
| 6 | 财务搜索bug | MerchantAccountList 用 merchant_id like 搜索改为关联商家名搜索 | 后端 | 2026-07-30 |
| 7 | 价格搜索bug | PriceApportionmentList 用 amount like 搜索改为按名称搜索 | 后端 | 2026-07-30 |

### 4 数据库变更

无（本次未新增 Migration）

### 5 影响范围

| 影响文件 | 变更类型 |
| :--- | :--- |
| app/Models/Warehouse.php | 重写（修复Bug + 补充关系/状态映射） |
| app/Models/LossOrder.php | 重写（修复Bug + 补充关系/状态映射） |
| app/Models/PurchaseReturn.php | 重写（修复Bug + 补充关系/状态映射） |
| app/Models/Inventory.php | 重写（清理import + 补充关系） |
| app/Models/InventoryLog.php | 重写（清理import + 补充关系） |
| app/Models/LossOrderItem.php | 重写（清理import + 补充关系/责任方映射） |
| app/Models/PurchaseReturnItem.php | 重写（补充关系） |
| app/Models/RestockReminder.php | 重写（补充周期/状态映射/关系） |
| app/Livewire/Product/RestockReminderList.php | 新增 |
| app/Livewire/Purchase/PurchaseReturnList.php | 重写（桩→完整CRUD） |
| app/Livewire/Inventory/WarehouseList.php | 重写（桩→完整CRUD） |
| app/Livewire/Inventory/InventoryList.php | 重写（桩→完整CRUD） |
| app/Livewire/Inventory/InventoryLogList.php | 重写（桩→只读查看器） |
| app/Livewire/Loss/LossOrderList.php | 重写（桩→完整CRUD+审核） |
| resources/views/livewire/product/restock-reminder-list.blade.php | 新增 |
| resources/views/livewire/purchase/purchase-return-list.blade.php | 重写 |
| resources/views/livewire/inventory/warehouse-list.blade.php | 重写 |
| resources/views/livewire/inventory/inventory-list.blade.php | 重写 |
| resources/views/livewire/inventory/inventory-log-list.blade.php | 重写 |
| resources/views/livewire/loss/loss-order-list.blade.php | 重写 |
| routes/web.php | 修改（新增1条路由） |
| app/Livewire/User/UserList.php | 新增（用户管理CRUD+角色分配+禁用启用+重置密码） |
| resources/views/livewire/user/user-list.blade.php | 新增 |
| app/Livewire/User/RoleList.php | 重写（增加权限分配树弹窗） |
| resources/views/livewire/user/role-list.blade.php | 重写（增加权限树+删除确认） |
| app/Livewire/User/PermissionList.php | 重写（增加角色分配弹窗） |
| resources/views/livewire/user/permission-list.blade.php | 重写（增加角色勾选+删除确认） |
| resources/views/components/app-topnav.blade.php | 修改（用户管理导航链接 href="#" → route('users')） |

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

