
# 开发迭代日志

对应 PRD 版本：v1.2
对应 FSD 版本：v1.2
技术栈：Laravel 13 + Livewire 4.x + Tailwind CSS 4.2+ + Alpine.js + PHP 8.4+ + MySQL 8.0 + Redis 7.x

记录规则：每次迭代新增一节，按版本号倒序排列。每条变更需标注开发人、完成时间和关联模块。

---

## V2.0.18 | 下拉搜索选择改造 + 商家逗号分隔搜索 + 拣货一键匹配

负责人：项目负责人
参与开发人员：后端开发
完成时间：2026-09-04

### 1 本次新增功能清单

| 序号 | 功能模块 | 功能点 | 开发人 | 完成时间 | 状态 |
|------|---------|--------|--------|---------|------|
| 1 | 搜索选择 | searchable-select 组件升级服务端搜索 + 供应商下拉 7 处改造（commit ee662a5） | 后端 | 2026-09-04 | ✅ 已完成 |
| 2 | 搜索选择 | 商家下拉 12 处改造（表单 8 + 筛选 4）（commit 358b5d8） | 后端 | 2026-09-04 | ✅ 已完成 |
| 3 | 商家搜索 | 6 处纯商家搜索支持英文逗号分隔多商家（commit 3e99437） | 后端 | 2026-09-04 | ✅ 已完成 |
| 4 | 拣货任务 | 详情页一键匹配数量（commit 50a1c9b） | 后端 | 2026-09-04 | ✅ 已完成 |
| 5 | 交互修复 | 供应商结算状态标签单行显示（commit a0fe736） | 后端 | 2026-09-04 | ✅ 已完成 |
| 6 | 缺陷修复 | 应收汇总单生成按钮无反应（commit 79d729a） | 后端 | 2026-09-04 | ✅ 已完成 |
| 7 | 文档 | Pusher/Reverb/Redis 三份教程重写（commit 3c8db98） | 后端 | 2026-09-04 | ✅ 已完成 |

### 2 变更摘要

#### searchable-select 服务端搜索改造（commit ee662a5 / 358b5d8）
- 组件新增 search（服务端方法名）/ selectedLabel（选中回显）/ button-class（按钮高度）属性，JS 端 debounce 300ms 服务端搜索限 20 条
- 供应商下拉 7 处：采购单、供应商结算、商品、一品多供、损耗单详情、待采清单、SKU 条码
- 商家下拉 12 处：订单/购物车/充值/发票/补货提醒/商家地址/SKU 可见性/促销设置（表单 8），购物车/SKU 可见性/促销设置/送货单（筛选 4）
- 采购退货单：关联模式供应商改只读文本展示（采购单自动带出），普通退货用搜索选择
- 背景：生产环境预计商家 600-1000 家，原生 select 全量加载不可行

#### 商家搜索逗号分隔（commit 3e99437）
- helpers.php 新增 split_search_keywords()：英文逗号拆分、逐项 trim、过滤空项，无逗号行为不变
- 6 处改造：商家列表（名称/联系人/电话）、客户账户（列表+弹窗）、应收汇总弹窗、配送线路加商家、SKU 可见性；命中任一关键词即显示

#### 拣货一键匹配数量（commit 50a1c9b）
- PickingTaskDetail 右上角新增按钮（仅拣货中显示，请求期间禁用防重）
- 仅处理待拣货明细：实拣=应拣并置已拣货；已录入实拣/差异的行不动；不生成差异单、不自动完成任务
- DB 事务 + chunkById 分批，Toast 反馈匹配明细数与总件数

### 3 设计决策

- 逗号分隔搜索仅覆盖纯商家搜索页（约 6 处），订单列表等混合搜索页（订单号/商家名）不改
- SKU/商品/单号下拉共 10 处隐患记入 14_Backlog（P1/P2），本轮不展开
- 拣货一键匹配遵循「先一键填好、再录差异」的拣货员操作习惯，任务完成仍由人工确认触发出库

## V2.0.17 | 列表页四大改造 + 超管数据清理功能

负责人：项目负责人
参与开发人员：后端开发
完成时间：2026-08-29

### 1 本次新增功能清单

| 序号 | 功能模块 | 功能点 | 开发人 | 完成时间 | 状态 |
|------|---------|--------|--------|---------|------|
| 1 | 列表页改造 | 操作列图标化+居右统一（第1批） | 后端 | 2026-08-29 | ✅ 已完成 |
| 2 | 导入导出 | 8个模块补 getImportRequiredFields（第2批） | 后端 | 2026-08-29 | ✅ 已完成 |
| 3 | 编辑表单 | 6个模块编辑表单补全（第3批） | 后端 | 2026-08-29 | ✅ 已完成 |
| 4 | 软删除 | 主子表一致性修复+无用import清理（第4批） | 后端 | 2026-08-29 | ✅ 已完成 |
| 5 | 系统管理 | 超管数据清理功能（已删除统计+永久清理） | 后端 | 2026-08-29 | ✅ 已完成 |

### 2 变更摘要

#### 第1批：列表操作列图标化（commit c60ec2e）
- 13个文件文字按钮改为图标按钮（p-1 rounded + w-3.5 h-3.5 + gap-0.5）
- 23个表格操作列表头 text-left → text-right
- 4个文件 action-col class 重复去重
- picking-task-list 数据行补 action-col class
- vehicle-issue-list 统一使用 partials export-modal + delete-confirm

#### 第2批：导入必填字段标红（commit b114be7）
8个模块补 `getImportRequiredFields()` 方法，使导入模板必填字段标红：
- OrderReturnList: 订单ID/商家ID/退货原因
- DeliveryTaskList: 任务编号/线路ID/送达日期/批次
- SupplierSettlementList: 供应商ID/结算周期开始/结束
- InvoiceList: 类型/抬头/税号
- DiscrepancyList: 差异编号/差异类型
- MerchantSkuVisibilityList: 商家ID/配置类型/商品ID/SKU ID/是否可见
- PriceApportionmentList: 均摊类型
- CorrectionAuthorizationList: 更正类型/更正原因

#### 第3批：编辑表单补全（commit b114be7）
6个模块编辑表单字段补全：
- PurchaseOrderList: render传warehouses变量 + Blade加仓库下拉
- OrderList: openEditModal加载5个配送字段 + save验证更新 + Blade编辑模式显示
- DeliveryTaskList: 补formDeliveryDate属性/加载/验证/更新/重置 + Blade日期字段
- SupplierSettlementList: 新增编辑模式(可改service_fee) + Blade双模式弹窗 + 行编辑按钮
- ReceivableList: 新增编辑模式(due_date/remark) + Blade双模式弹窗 + 行编辑按钮
- InvoiceList: 补formFileUrl文件上传 + Blade文件字段(查看当前文件)

权限创建：delivery.delivery-task.edit / finance.invoice.edit / finance.receivable.edit / finance.receivable.delete / finance.supplier-settlement.delete

#### 第4批：软删除一致性修复（commit fa3113a）
- PurchaseReturnItem: 添加 SoftDeletes trait + 迁移加 deleted_at 列
- OrderReturnItem: 类内补 use SoftDeletes + 迁移加 deleted_at 列
- SupplierSettlementItem: 类内补 use SoftDeletes + 迁移加 deleted_at 列
- LossOrderList::delete(): 补 $item->items()->delete() 防止子表孤儿数据
- 清理13个模型无用 SoftDeletes import（DeliveryTask/DeliveryTaskDetail/PriceChangeLog/VehicleIssue/PickingTaskItem/DeliveryRouteStop/WechatUser/Temperature/PriceStrategyItem/MerchantFavorite/LoginLog/FeaturedPromotion/DeliveryTrack）

#### 新需求：超管数据清理功能（commit a07646c）
- 新建 TrashCleanup 组件：统计13类业务单据的已删除数量
- 支持一键永久清理（forceDelete），含子表级联清理
- 二次确认弹窗，防止误操作
- 路由 /trash-cleanup，权限 system.trash-cleanup.view（仅超管）
- 顶部导航栏新增「数据清理」入口（仅超管可见）
- Profile 个人中心新增「数据清理」Tab（仅超管可见）
- CheckPermission 中间件注册路由权限映射

### Git 信息

| 项目 | 值 |
|------|------|
| 分支 | bugfix |
| Commits | c60ec2e / b114be7 / fa3113a / a07646c |
| 验证 | PHP 语法检查通过；路由检查通过；UTF-8 无 BOM 验证通过 |

---

## V2.0.16 | 明细页 Tab 化改造（第二批）

负责人：项目负责人
参与开发人员：后端开发
完成时间：2026-08-29

### 1 本次新增功能清单

| 序号 | 功能模块 | 功能点 | 开发人 | 完成时间 | 状态 |
| :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | 明细页改造 | 6 个无表格明细页 Tab 化改造：供应商结算/应收/充值/发票/客户账户/配送线路详情页统一选项卡+动画切换+状态变更记录 | 后端 | 2026-08-29 | ✅ |

### 2 修改文件

| 操作 | 文件 | 说明 |
| :--- | :--- | :--- |
| 修改 | app/Models/SupplierSettlement.php | 新增 auditLogs() morphMany 关系 |
| 修改 | app/Models/Receivable.php | 新增 auditLogs() morphMany 关系 |
| 修改 | app/Models/Recharge.php | 新增 auditLogs() morphMany 关系 |
| 修改 | app/Models/Invoice.php | 新增 auditLogs() morphMany 关系 |
| 修改 | app/Models/MerchantAccount.php | 新增 auditLogs() morphMany 关系 |
| 修改 | app/Models/DeliveryRoute.php | 新增 auditLogs() morphMany 关系 |
| 修改 | app/Livewire/Finance/SupplierSettlementDetail.php | with() 加 auditLogs.operator，render() 加载审计日志 |
| 修改 | app/Livewire/Finance/ReceivableDetail.php | with() 加 auditLogs.operator，render() 加载审计日志 |
| 修改 | app/Livewire/Finance/RechargeDetail.php | with() 加 auditLogs.operator，render() 加载审计日志 |
| 修改 | app/Livewire/Finance/InvoiceDetail.php | with() 加 auditLogs.operator，render() 加载审计日志 |
| 修改 | app/Livewire/Finance/MerchantAccountDetail.php | with() 加 auditLogs.operator，render() 加载审计日志 |
| 修改 | app/Livewire/Delivery/DeliveryRouteDetail.php | with() 加 auditLogs.operator，render() 加载审计日志 |
| 修改 | resources/views/livewire/finance/supplier-settlement-detail.blade.php | Tab 化：info(金额+明细)/payments/logs |
| 修改 | resources/views/livewire/finance/receivable-detail.blade.php | Tab 化：info(金额)/payments/logs |
| 修改 | resources/views/livewire/finance/recharge-detail.blade.php | Tab 化：info(金额+审核)/logs |
| 修改 | resources/views/livewire/finance/invoice-detail.blade.php | Tab 化：info(发票信息)/logs(状态轨迹替换为AuditLog) |
| 修改 | resources/views/livewire/finance/merchant-account-detail.blade.php | Tab 化：info(余额+资金)/recharges/logs |
| 修改 | resources/views/livewire/delivery/delivery-route-detail.blade.php | Tab 化：stops(商家点位)/logs |

### 3 Tab 结构说明

| 页面 | Tab 数 | Tab 内容 |
| :--- | :--- | :--- |
| 供应商结算详情 | 3 | info(金额构成+结算明细) / payments(付款记录) / logs(状态变更记录) |
| 应收详情 | 3 | info(金额构成) / payments(收款记录) / logs(状态变更记录) |
| 充值详情 | 2 | info(充值金额+审核信息) / logs(状态变更记录) |
| 发票详情 | 2 | info(发票信息) / logs(状态轨迹替换为 AuditLog) |
| 客户账户详情 | 3 | info(余额+资金汇总) / recharges(充值记录) / logs(状态变更记录) |
| 配送线路详情 | 2 | stops(商家点位) / logs(状态变更记录) |

### Git 信息

| 项目 | 值 |
| :--- | :--- |
| 分支 | bugfix |
| Commit | 8a45233 |
| 验证 | PHP 语法检查 6 文件通过；view:cache + view:clear 通过；UTF-8 无 BOM 验证通过；路由检查通过 |

---
## V2.0.14 | 杩唬鍛ㄦ湡锛?026-08-28


| :--- | :--- | :--- | :--- | :--- | :--- |


| 鏂囦欢 | 璇存槑 |
| :--- | :--- |

### 3 淇敼鏂囦欢

| 鏂囦欢 | 璇存槑 |
| :--- | :--- |

---

## V1.9.1 | 杩唬鍛ㄦ湡锛?026-08-03


| :--- | :--- | :--- | :--- | :--- | :--- |

### 2 鏁版嵁搴撳彉鏇?

| 鍙樻洿 | 琛ㄥ悕 | 瀛楁 | 璇存槑 |
| :--- | :--- | :--- | :--- |


| 鏂囦欢 | 璇存槑 |
| :--- | :--- |
| app/Services/PurchaseReturnService.php | 閲囪喘閫€璐ф湇鍔?|
| app/Services/LossOrderService.php | 鎹熻€楀崟鏈嶅姟 |
| database/migrations/2026_08_03_004635_add_return_status_to_purchase_orders_and_enhance_loss_tables.php | 鏂摼淇杩佺Щ |

### 4 鏍稿績涓氬姟瑙勫垯

- **閫€璐у嚭搴?*锛歅urchaseReturnService::ship() 璋冪敤 InventoryService::stockOut()锛宻ource_type='purchase_return'
- **鍏ュ簱宸紓鈫掓崯鑰楀崟**锛歅urchaseService::stockIn() 璁＄畻姣忚 discrepancy_quantity锛屽綋 > 0 涓?stockin_auto_create_loss=true 鏃惰嚜鍔ㄥ垱寤烘崯鑰楀崟
- **鎹熻€楁墽琛屾墸搴撳瓨**锛歀ossOrderService::execute() 璋冪敤 InventoryService::stockOut()锛宻ource_type='loss_order'
- **宸插叆搴撶姝㈠彇娑?*锛歅urchaseOrder::canTransitionTo() 涓?STATUS_STOCKED 鍙兘杞埌 STATUS_COMPLETED

### 5 寰呭鐞?


---

## V1.9.0 | 杩唬鍛ㄦ湡锛?026-08-01


| :--- | :--- | :--- | :--- | :--- | :--- |

### 2 鏈浼樺寲/閲嶆瀯

| :--- | :--- | :--- | :--- |
| 1 | SUPER_ADMIN_ONLY 鍒楄〃娓呯┖ | 涓嶅啀纭紪鐮佽秴绠′笓灞炶矾鐢憋紝鐢辨潈闄愯〃缁熶竴鎺у埗 | 2026-08-01 |

### 3 Bug 淇

| :--- | :--- | :--- | :--- |
| 1 | admin:fresh --seed 鎶?PermissionAlreadyExists | migrate:fresh 鍚?Spatie 缂撳瓨娈嬬暀锛屾坊鍔?forgetCachedPermissions() | 2026-08-01 |

### 4 寰呬慨澶?Bug 娓呭崟

| # | 涓ラ噸搴?| 鎻忚堪 | 褰卞搷鑼冨洿 | 棰勮鏀瑰姩 |
| :--- | :--- | :--- | :--- | :--- |

### 5 楠岃瘉缁撴灉

- admin:fresh --seed --force 鍏ㄦ祦绋嬮€氳繃锛坢igrate + SystemDataSeeder + DemoDataSeeder锛?
- 瓒呯鐧诲綍锛氭墍鏈夎彍鍗曞彲瑙侊紝鎵€鏈夋寜閽彲瑙?

---

## V1.8.0 | 杩唬鍛ㄦ湡锛?026-07-31


| :--- | :--- | :--- | :--- | :--- | :--- |

### 2 鏈浼樺寲/閲嶆瀯

| :--- | :--- | :--- | :--- | :--- |

### 3 鏈淇 Bug

鏃犮€?

### 4 鏁版嵁搴撳彉鏇?

| Migration 鏂囦欢鍚?| 鎿嶄綔绫诲瀷 | 娑夊強琛?| 璇存槑 |
| :--- | :--- | :--- | :--- |
| 2026_07_31_085412_create_user_preferences_table | CREATE | user_preferences | 鐢ㄦ埛鍋忓ソ琛紙user_id + pref_key 鍞竴绱㈠紩锛宲ref_value JSON锛?|

### 5 褰卞搷鑼冨洿

| 褰卞搷鏂囦欢 | 鍙樻洿绫诲瀷 |
| :--- | :--- |
| routes/web.php | 淇敼锛堟柊澧?detail 璺敱 + auth+permission 涓棿浠讹級 |
| app/Models/PurchaseOrder.php | 淇敼锛堟柊澧炲瓧娈?鍏宠仈/鏂规硶锛?|
| app/Models/Driver.php | 淇敼锛坒illable 琛?remark锛?|
| app/Livewire/Purchase/PurchaseOrderList.php | 淇敼锛圓uth::id / generateOrderNo锛?|
| app/Livewire/Org/SupplierList.php | 淇敼锛堝垪瀹氫箟琛ュ厖 bank_name/bank_account锛?|
| app/Livewire/Product/KeywordList.php | 淇敼锛坵ord 鈫?keyword锛?|
| app/Livewire/Product/SkuList.php | 淇敼锛坧rice 鈫?purchase_price+wholesale_price锛宼ype=money锛?|
| app/Livewire/Product/SkuSupplierList.php | 淇敼锛坧urchase_price 鍔?type=money锛?|
| app/Livewire/Product/SkuBarcodeList.php | 淇敼锛坆arcode 鈫?barcode_code锛?|
| app/Livewire/Traits/WithColumnVisibility.php | 淇敼锛坰ession 鈫?DB 鎸佷箙鍖?+ openColumnModal锛?|
| app/Livewire/Traits/WithExcelExport.php | 淇敼锛坬ueryOrData 鍛藉悕鍙傛暟 + moneyColumns + 妯℃澘淇锛?|
| app/Exports/GenericExport.php | 淇敼锛坱ype=money 鏄惧紡澹版槑锛岄噾棰?梅1000锛?|
| resources/views/livewire/purchase/purchase-order-list.blade.php | 淇敼锛堣鎯呴摼鎺?/ money_format锛?|
| resources/views/partials/column-modal.blade.php | 淇敼锛坢ax-w-lg + X 鍏抽棴鎸夐挳锛?|
| resources/views/partials/export-modal.blade.php | 淇敼锛坢ax-w-lg锛?|
| database/seeders/Demo/OrganizationDemoSeeder.php | 淇敼锛堣ˉ鍏?suppliers/merchants/drivers 娴嬭瘯鏁版嵁锛?|


**閲囪喘鍗曠姸鎬佹祦杞璁★細**
- canTransitionTo() 鏂规硶鎺у埗鍚堟硶娴佽浆璺緞锛屼笉鍏佽璺ㄧ姸鎬佽烦杞?
- 鍙栨秷鎿嶄綔浠呴檺寰呮帴鍗?澶囪揣涓?宸插彂璐х姸鎬?

**鍏ュ簱鎿嶄綔鏍稿績娴佺▼锛?*
- PurchaseService.stockIn() 鍦?DB::transaction 鍐咃細鏇存柊鏄庣粏瀹為檯鍊?鈫?InventoryService.stockIn() 脳 N 鈫?閲嶇畻閲戦
- 宸紓琛岃嚜鍔ㄩ珮浜紙姗欒壊锛夛紝宸紓鍘熷洜鍙€夊～鍐?

**搴撳瓨鏈嶅姟璁捐鍘熷垯锛?*
- 鍏ュ簱锛氭煡鎵炬垨鍒涘缓锛坵arehouse_id + sku_id + batch_no 鍞竴锛夛紝increment + 鍐欐棩蹇?
- 鍑哄簱锛欶IFO 鏁堟湡浼樺厛锛屽簱瀛樹笉瓒虫姏寮傚父
- 璋冩暣锛氬樊棰濊皟鏁达紝姝ｆ暟=鎶ユ孩锛岃礋鏁?璋冩暣

**瀵煎叆瀵煎嚭鏋舵瀯淇锛?*
- 閲戦鍒楃粺涓€鐢?`'type' => 'money'` 鏄惧紡澹版槑锛孏enericExport 閫氳繃 `$moneyColumns` 鍙傛暟鏀堕泦锛岀粺涓€ 梅1000
- 瀵煎叆妯℃澘琛ㄥご缁熶竴浠?`getImportColumnMap()` 鏍囩鐢熸垚锛屼笌瀵煎叆鏄犲皠淇濇寔涓€鑷?
- WithExcelExport 鍛藉悕鍙傛暟淇涓?`queryOrData:`锛屼笌 GenericExport 鏋勯€犲嚱鏁板榻?

**鐢ㄦ埛鍋忓ソ鎸佷箙鍖栵細**
- user_preferences 琛紙user_id + pref_key 鍞竴绱㈠紩锛夛紝pref_value 瀛?JSON
- WithColumnVisibility 浼樺厛浠?DB 璇诲彇锛圓uth::user 闅旂锛夛紝鏈櫥褰曢檷绾?session
- UserPreference Model 鎻愪緵 getPreference()/setPreference() 闈欐€佹柟娉?

---

## V1.7.0 | 杩唬鍛ㄦ湡锛?026-07-31


| :--- | :--- | :--- | :--- | :--- | :--- |

### 2 鏈浼樺寲/閲嶆瀯

| :--- | :--- | :--- | :--- | :--- |

### 3 鏈淇 Bug

鏃犮€?

### 4 鏁版嵁搴撳彉鏇?

鏃狅紙鏈鏈柊澧?Migration锛屼粎璋冩暣 Seeder 鍜屽懡浠ゅ眰锛夈€?

### 5 褰卞搷鑼冨洿

| 褰卞搷鏂囦欢 | 鍙樻洿绫诲瀷 |
| :--- | :--- |
| app/Console/Commands/AdminSeedCommand.php | 淇敼锛堟柊澧?--demo=MODULE / --list 閫夐」锛?|
| app/Console/Commands/AdminCreateUserCommand.php | 淇敼锛堝寮洪€夐」锛?-role / --guard / --active锛?|
| app/Console/Commands/AdminMakeUserCommand.php | 搴熷純锛堟爣璁?@deprecated锛屾寚寮曞埌 admin:create-admin锛?|
| database/seeders/DemoDataSeeder.php | 閲嶅啓锛堟敼涓烘€诲叆鍙ｏ紝璋冪敤 Demo/ 涓嬪悇鍒嗘ā鍧楋級 |
| database/seeders/DatabaseSeeder.php | 淇敼锛堢‘淇濆弻鍏ュ彛鍏煎锛?|
| docs/03_DB_鏁版嵁搴撹璁?鏁版嵁瀛楀吀.md | 淇敼锛堟柊澧炵 9 绔?Seeder 浣撶郴璁捐锛?|
| docs/05_Setup_瀹夎閮ㄧ讲閰嶇疆鎵嬪唽.md | 淇敼锛堢 9/10/12 绔犳洿鏂帮級 |


**Seeder 鍒嗘ā鍧楄璁★細**
- 璁捐鍘熷垯锛氭牳蹇冩暟鎹紙SystemDataSeeder锛変笌娴嬭瘯鏁版嵁锛圖emoDataSeeder + Demo/*.php锛変弗鏍煎垎绂?
- 娴嬭瘯鏁版嵁鎸変笟鍔℃ā鍧楁媶鍒嗕负 10 涓嫭绔?Seeder锛屾斁鍦?`database/seeders/Demo/` 瀛愮洰褰?
- 鍙屽叆鍙ｅ吋瀹癸細`php artisan db:seed --class=DemoDataSeeder` 绛変环浜?`php artisan admin:seed --demo`
- `admin:seed --demo=organization` 鍙崟鐙繍琛屾寚瀹氭ā鍧楁祴璇曟暟鎹?

**Migration vs Seeder 鑱岃矗鍒掑垎锛?*
- Migration 8.1锛氬垱寤洪粯璁ょ鐞嗗憳 `seeding` 璐︽埛 + 9 涓郴缁熻鑹?+ model_has_roles 鍏宠仈
- 鐢熶骇鐜鍙渶杩愯 `migrate` + `admin:seed --system`锛屽嵆鍙幏寰楀畬鏁村彲鐢ㄧ郴缁?

---

## V1.6.5 | 杩唬鍛ㄦ湡锛?026-07-30


| :--- | :--- | :--- | :--- | :--- | :--- |

### 2 鏈浼樺寲/閲嶆瀯

| :--- | :--- | :--- | :--- | :--- |

### 3 鏈淇 Bug

| :--- | :--- | :--- | :--- | :--- |

### 4 鏁版嵁搴撳彉鏇?

鏃犮€?

### 5 褰卞搷鑼冨洿

| 褰卞搷鏂囦欢 | 鍙樻洿绫诲瀷 |
| :--- | :--- |
| app/Livewire/User/RoleList.php | 淇敼锛坧ermissionTreeData 缂撳瓨 + toggleModulePermissions/togglePagePermissions/togglePermission + getModuleState/getPageState锛?|
| resources/views/livewire/user/role-list.blade.php | 淇敼锛坈heckbox + `@checked` + Alpine `x-effect` indeterminate锛?|
| composer.json | 淇敼锛堟竻鐞嗗啑浣欒剼鏈級 |
| docs/05_Setup_瀹夎閮ㄧ讲閰嶇疆鎵嬪唽.md | 淇敼锛堣ˉ鍏?admin:seed 鍛戒护锛?|


- 淇鏂规锛?
  3. Alpine `x-effect` 鐩戝惉 checked 鍙樺寲鑷姩璁剧疆 `el.indeterminate`
  4. 鎸夐挳绾?checkbox 鏀逛负 `wire:click="togglePermission($id)"` + `@checked(in_array($id, $selectedPermissions))`

---

## V1.6.4 | 杩唬鍛ㄦ湡锛?026-07-30


| :--- | :--- | :--- | :--- | :--- | :--- |

### 2 鏈浼樺寲/閲嶆瀯

| :--- | :--- | :--- | :--- | :--- |

### 3 鏈淇 Bug

| :--- | :--- | :--- | :--- | :--- |

### 4 鏁版嵁搴撳彉鏇?

鏃犮€?

### 5 褰卞搷鑼冨洿

| 褰卞搷鏂囦欢 | 鍙樻洿绫诲瀷 |
| :--- | :--- |
| app/Livewire/Traits/WithRowSelection.php | 淇敼锛坲se WithToast; + dispatch 鏇挎崲锛?|
| app/Livewire/Traits/WithExcelExport.php | 淇敼锛坲se WithToast; + dispatch 鏇挎崲锛?|
| app/Livewire/Traits/WithExcelImport.php | 淇敼锛坲se WithToast; + dispatch 鏇挎崲锛?|
| 46 涓?Livewire 缁勪欢鏂囦欢 | 淇敼锛堟坊鍔?use WithToast + dispatch 鏇挎崲涓?toastSuccess/Error/Warning/Info锛?|
| resources/views/livewire/user/role-list.blade.php | 淇敼锛堟潈闄?checkbox 淇锛?|


**checkbox 鐨?wire:model.live 涓?wire:click 鍐茬獊锛?*
- 鏍瑰洜锛歝heckbox 鍚屾椂缁戝畾 `wire:model.live="formArray"` 鍜?`wire:click="toggleMethod()"` 鏃讹紝Livewire 鍏堟墽琛?model 缁戝畾鑷姩鍒囨崲鍊硷紝鍐嶆墽琛?click 鏂规硶锛屽鑷磋嚜瀹氫箟閫昏緫鍩轰簬宸蹭慨鏀圭殑鏁扮粍鍒ゆ柇

**Toast 浜嬩欢瀛楁涓嶅尮閰嶏細**
- 鏍瑰洜锛氬悗绔?`dispatch('toast', message: '...')` 鍙戝嚭鐨?detail 瀛楁涓?`message`锛屽墠绔?`$toast.show()` 鏈熸湜 `title` 瀛楁
- 淇锛氬垱寤?`WithToast` Trait 缁熶竴浣跨敤 `title` + `description` + `type` 瀛楁

---

## V1.6.3 | 杩唬鍛ㄦ湡锛?026-07-30


鏃犳柊澧炲姛鑳芥ā鍧椼€?

### 2 鏈浼樺寲/閲嶆瀯

| :--- | :--- | :--- | :--- | :--- |

### 3 鏈淇 Bug

| :--- | :--- | :--- | :--- | :--- |
| 2 | Toast 鎻愮ず涓嶆樉绀烘枃瀛?| app.js 浜嬩欢鐩戝惉澧炲姞 message鈫抰itle 鏄犲皠 | 鍓嶇 | 2026-07-30 |

### 4 鏁版嵁搴撳彉鏇?

鏃犮€?

### 5 褰卞搷鑼冨洿

| 褰卞搷鏂囦欢 | 鍙樻洿绫诲瀷 |
| :--- | :--- |
| app/Livewire/Auth/Login.php | 淇敼锛坢ount 澧炲姞宸茬櫥褰曡烦杞級 |
| resources/js/app.js | 淇敼锛坱oast 浜嬩欢鐩戝惉 message鈫抰itle 鏄犲皠锛?|
| app/Livewire/User/RoleList.php | 淇敼锛堟柊澧?toggleModulePermissions/togglePagePermissions锛?|
| resources/views/livewire/user/role-list.blade.php | 淇敼锛堟潈闄愬脊绐楁敼琛ㄦ牸褰㈠紡锛?|
| public/build/ | 閲嶅缓锛圴ite 鏋勫缓鏇存柊锛?|

---

## V1.6.2 | 杩唬鍛ㄦ湡锛?026-07-30


| :--- | :--- | :--- | :--- | :--- | :--- |

### 2 鏈浼樺寲/閲嶆瀯

鏃犮€?

### 3 鏈淇 Bug

| :--- | :--- | :--- | :--- | :--- |

### 4 鏁版嵁搴撳彉鏇?

鏃犮€?

### 5 褰卞搷鑼冨洿

| 褰卞搷鏂囦欢 | 鍙樻洿绫诲瀷 |
| :--- | :--- |
| app/Livewire/User/PermissionList.php | 淇敼锛堝鍔?filterType/filterModule 灞炴€с€佺瓫閫夐€昏緫銆乺esetFilters锛?|
| 38 涓?Blade 瑙嗗浘鏂囦欢 | 淇敼锛堢Щ闄?UTF-8 BOM锛?|
| resources/views/livewire/user/user-list.blade.php | 淇敼锛坆utton 娣诲姞 type="button"锛?|
| resources/views/livewire/user/role-list.blade.php | 淇敼锛坆utton 娣诲姞 type="button"锛?|
| resources/views/livewire/user/permission-list.blade.php | 淇敼锛坆utton 娣诲姞 type="button"锛?|


**UTF-8 BOM 瀵艰嚧 Livewire 4 morphing 閿欎綅闂锛?*
- 淇锛氭壒閲忕Щ闄ゆ墍鏈?Blade 鏂囦欢 BOM + 涓?`<button>` 娣诲姞 `type="button"` 闃叉 form 鎻愪氦琛屼负

---

## V1.6.1 | 杩唬鍛ㄦ湡锛?026-07-30


鏃犳柊澧炲姛鑳芥ā鍧椼€?

### 2 鏈浼樺寲/閲嶆瀯

| :--- | :--- | :--- | :--- | :--- |

### 3 鏈淇 Bug

| :--- | :--- | :--- | :--- | :--- |

### 4 鏁版嵁搴撳彉鏇?

鏃犮€?

### 5 褰卞搷鑼冨洿

| 褰卞搷鏂囦欢 | 鍙樻洿绫诲瀷 |
| :--- | :--- |
| app/Livewire/User/UserList.php | 淇敼锛堝鍔犺秴绾х鐞嗗憳淇濇姢閫昏緫锛?|
| app/Livewire/User/RoleList.php | 淇敼锛堝鍔犺秴绾х鐞嗗憳瑙掕壊淇濇姢閫昏緫锛?|
| resources/views/livewire/user/user-list.blade.php | 淇敼锛坱oggle switch + 鍥炬爣鎸夐挳 + 瓒呯骇绠＄悊鍛樿 UI 淇濇姢锛?|
| resources/views/livewire/user/role-list.blade.php | 淇敼锛堝浘鏍囨寜閽?+ 瓒呯骇绠＄悊鍛樿鑹茶 UI 淇濇姢锛?|
| resources/views/livewire/user/permission-list.blade.php | 淇敼锛堝浘鏍囨寜閽級 |

---

## V1.6.0 | 杩唬鍛ㄦ湡锛?026-07-29


| :--- | :--- | :--- | :--- | :--- | :--- |

### 2 鏈浼樺寲/閲嶆瀯

| :--- | :--- | :--- | :--- | :--- |

### 3 鏈淇 Bug

| :--- | :--- | :--- | :--- | :--- |

### 4 鏁版嵁搴撳彉鏇?

鏃狅紙鏈鏈柊澧?Migration锛?

### 5 褰卞搷鑼冨洿

| 褰卞搷鏂囦欢 | 鍙樻洿绫诲瀷 |
| :--- | :--- |
| app/Models/Inventory.php | 閲嶅啓锛堟竻鐞唅mport + 琛ュ厖鍏崇郴锛?|
| app/Models/InventoryLog.php | 閲嶅啓锛堟竻鐞唅mport + 琛ュ厖鍏崇郴锛?|
| app/Models/LossOrderItem.php | 閲嶅啓锛堟竻鐞唅mport + 琛ュ厖鍏崇郴/璐ｄ换鏂规槧灏勶級 |
| app/Models/PurchaseReturnItem.php | 閲嶅啓锛堣ˉ鍏呭叧绯伙級 |
| app/Livewire/Purchase/PurchaseReturnList.php | 閲嶅啓锛堟々鈫掑畬鏁碈RUD锛?|
| app/Livewire/Inventory/WarehouseList.php | 閲嶅啓锛堟々鈫掑畬鏁碈RUD锛?|
| app/Livewire/Inventory/InventoryList.php | 閲嶅啓锛堟々鈫掑畬鏁碈RUD锛?|
| app/Livewire/Inventory/InventoryLogList.php | 閲嶅啓锛堟々鈫掑彧璇绘煡鐪嬪櫒锛?|
| app/Livewire/Loss/LossOrderList.php | 閲嶅啓锛堟々鈫掑畬鏁碈RUD+瀹℃牳锛?|
| resources/views/livewire/purchase/purchase-return-list.blade.php | 閲嶅啓 |
| resources/views/livewire/inventory/warehouse-list.blade.php | 閲嶅啓 |
| resources/views/livewire/inventory/inventory-list.blade.php | 閲嶅啓 |
| resources/views/livewire/inventory/inventory-log-list.blade.php | 閲嶅啓 |
| resources/views/livewire/loss/loss-order-list.blade.php | 閲嶅啓 |
| routes/web.php | 淇敼锛堟柊澧?鏉¤矾鐢憋級 |
| app/Livewire/User/PermissionList.php | 閲嶅啓锛堝鍔犺鑹插垎閰嶅脊绐楋級 |
| resources/views/components/app-topnav.blade.php | 淇敼锛堢敤鎴风鐞嗗鑸摼鎺?href="#" 鈫?route('users')锛?|

---

## V1.5.0 | 杩唬鍛ㄦ湡锛?026-07-29


| :--- | :--- | :--- | :--- | :--- | :--- |

### 2 鏈浼樺寲/閲嶆瀯

| :--- | :--- | :--- | :--- | :--- |
| 1 | 瀵艰埅 | nav-menu-icon-link 缁勪欢閲嶆瀯锛氭枃瀛楀尯鏀逛负 flex 瀹瑰櫒锛屾敮鎸佸彲閫?description 琛?| 鍓嶇 | 2026-07-29 |

### 3 鏈淇 Bug

鏃?

### 4 鏁版嵁搴撳彉鏇?

鏃狅紙鏈鏈柊澧?Migration锛?

### 5 褰卞搷鑼冨洿

| 褰卞搷鏂囦欢 | 鍙樻洿绫诲瀷 |
| :--- | :--- |
| resources/views/components/ui/icon.blade.php | 淇敼锛堟柊澧?6涓浘鏍囷級 |
| resources/views/components/nav-menu-icon-link.blade.php | 淇敼锛堟柊澧瀌escription鍙傛暟锛?|
| database/seeders/DemoDataSeeder.php | 淇敼锛堟墿灞?0+琛ㄧず渚嬫暟鎹級 |

---

## V1.4.0 | 杩唬鍛ㄦ湡锛?026-07-29


| :--- | :--- | :--- | :--- | :--- | :--- |

### 2 鏈浼樺寲/閲嶆瀯

| :--- | :--- | :--- | :--- | :--- |
| 1 | 瀵艰埅 | 鍏ㄩ儴瀛愯彍鍗曠粺涓€鏀圭敤 icon-link 鏍峰紡 | 鍓嶇 | 2026-07-29 |

### 3 鏈淇 Bug

鏃?

### 4 寰呭姙浜嬮」

| :--- | :--- | :--- | :--- |
| 1 | 鍏ㄦā鍧?| 鍚勬ā鍧?Detail/Form 椤甸潰寰呭紑鍙?| 涓?|
| 2 | 鍏ㄦā鍧?| 涓氬姟閫昏緫 Service 灞傚緟瀹炵幇 | 楂?|
| 3 | 鍏ㄦā鍧?| 灏忕▼搴忕 API 寰呭紑鍙?| 楂?|


| 鎸囨爣 | V1.3.0 | V1.4.0 | 澧為噺 |
| :--- | :--- | :--- | :--- |
| Model 鏁?| 36 | 72 | +36 |
| Livewire 缁勪欢 | 28 | 54 | +26 |
| 璺敱鏁?| 30 | 56 | +26 |
| 鏁版嵁搴撹〃 | 77 | 77 | 鈥?|


鏃犳柊澧?Migration锛孧odel 鍩轰簬宸叉湁琛ㄧ粨鏋勫垱寤恒€?


| 鏃ユ湡 | 鐗堟湰 | 鎿嶄綔浜?| Git Commit | 澶囨敞 |
| :--- | :--- | :--- | :--- | :--- |

---

## V1.3.0 | 杩唬鍛ㄦ湡锛?026-07-29


| :--- | :--- | :--- | :--- | :--- | :--- |
| 8 | 瀵艰埅 | 閲囪喘绠＄悊瀛愯彍鍗曢摼鎺ュ緟閲囨竻鍗?閲囪喘鍗曠鐞?| 鍓嶇 | 2026-07-29 | 鉁?|

### 2 鏈浼樺寲/閲嶆瀯

| :--- | :--- | :--- | :--- | :--- |
| 1 | 瀵艰埅 | 閲囪喘绠＄悊瀛愯彍鍗曟敼鐢?icon-link 鏍峰紡锛堜笌鍟嗗搧绠＄悊/璁㈠崟閰嶉€佺粺涓€锛?| 鍓嶇 | 2026-07-29 |

### 3 鏈淇 Bug

鏃?

### 4 寰呭姙浜嬮」

| :--- | :--- | :--- | :--- |
| 1 | 閲囪喘绠＄悊 | PurchaseOrderDetail / PurchaseReturn / PurchaseReturnDetail 椤甸潰寰呭紑鍙?| 涓?|
| 2 | 璁㈠崟閰嶉€?| OrderDetail / OrderReturn / OrderReturnDetail 椤甸潰寰呭紑鍙?| 涓?|


| 鎸囨爣 | V1.2.0 | V1.3.0 | 澧為噺 |
| :--- | :--- | :--- | :--- |
| Model 鏁?| 26 | 36 | +10 |
| Livewire 缁勪欢 | 22 | 28 | +6 |
| 璺敱鏁?| 24 | 30 | +6 |
| 鏁版嵁搴撹〃 | 77 | 77 | 鈥?|


鏃犳柊澧?Migration锛孧odel 鍩轰簬宸叉湁琛ㄧ粨鏋勫垱寤恒€?


| 鏃ユ湡 | 鐗堟湰 | 鎿嶄綔浜?| Git Commit | 澶囨敞 |
| :--- | :--- | :--- | :--- | :--- |

---

## V1.2.0 | 杩唬鍛ㄦ湡锛?026-07-29


| :--- | :--- | :--- | :--- | :--- | :--- |
| 3 | 鍟嗗搧绠＄悊 | ProductImage Model锛堝晢鍝佸鍥撅級 | AI | 2026-07-29 | 宸插畬鎴?|
| 5 | 鍟嗗搧绠＄悊 | MerchantSkuVisibility Model锛堝晢瀹禨KU鍙鎬э級 | AI | 2026-07-29 | 宸插畬鎴?|
| 6 | 鍟嗗搧绠＄悊 | Tag / ProductTag Model锛堟爣绛捐瘝搴?鍟嗗搧鏍囩鍏宠仈锛?| AI | 2026-07-29 | 宸插畬鎴?|
| 7 | 鍟嗗搧绠＄悊 | Keyword Model锛堟悳绱㈠叧閿瘝鑱旀兂+鐑害锛?| AI | 2026-07-29 | 宸插畬鎴?|
| 8 | 鏉＄爜绠＄悊 | SkuBarcode Model锛?绉嶆潯鐮佺被鍨嬶細鍘傚/渚涘簲鍟?鍐呴儴/澶囩敤锛?| AI | 2026-07-29 | 宸插畬鎴?|
| 9 | 涓€鍝佸渚?| SkuSupplier Model锛圫KU-渚涘簲鍟嗗叧鑱旓紝榛樿渚涘簲鍟?閲囪喘浠凤級 | AI | 2026-07-29 | 宸插畬鎴?|
| 16 | 涓€鍝佸渚?| SkuSupplierList Livewire锛堝叧鑱擟RUD+渚涘簲鍟嗕笅鎷夛級 | AI | 2026-07-29 | 宸插畬鎴?|
| 18 | 瀵艰埅 | 鍟嗗搧绠＄悊瀛愯彍鍗曞叏閮ㄦ寚鍚戠湡瀹炶矾鐢?| AI | 2026-07-29 | 宸插畬鎴?|
| 19 | 鏂囨。 | FSD 9.2 Models 鐩綍鏍囨敞宸插垱寤虹姸鎬侊紝娓呯悊灏鹃儴閲嶅娈嬬暀 | AI | 2026-07-29 | 宸插畬鎴?|


鏃?

### 4 閬楃暀寰呰凯浠ｉ渶姹傦紙涓嬩竴鐗堟湰澶勭悊锛?

| :--- | :--- | :--- | :--- |
| 1 | Model 绗?~15鎵癸紙绾?9涓級 | P0 | 閲囪喘鈫掕鍗曗啋鎷ｈ揣鈫掗厤閫佲啋宸紓鎹熻€椻啋閫€璐р啋浠锋牸绛栫暐鈫掕储鍔♀啋鍧囨憡+鎵╁睍+绯荤粺+寰俊 |
| 2 | 瀹夎 Redis PHP 鎵╁睍 | P1 | .env 鐩墠鐢?file 椹卞姩 |


| 缁村害 | 宸插畬鎴?| 寰呭紑鍙?| 鍚堣 |
| :--- | :--- | :--- | :--- |
| 鏁版嵁搴撹〃 | 77锛堝凡寤?Migration锛?| 0 | 77 |
| Blade 缁勪欢 | 34+ | - | 34+ |
| Model | 26 | ~42 | ~68 |
| Livewire 缁勪欢 | 22 | - | 22 |
| 璺敱 | 24 | - | 24 |


| :--- | :--- |
| 鐗堟湰鍙?| V1.2.0-alpha |
| 鍙戝竷鏃堕棿 | 2026-07-29 |
| Git 鍒嗘敮 | navigate |
| 杩滅▼浠撳簱 | git@github.com:kindiaing/susong-livewire.git |
| 淇敼鏂囦欢 | web.php / app-topnav.blade.php / FSD 9.2 + 9.9 |

---

## V1.1.0 | 杩唬鍛ㄦ湡锛?026-07-29


| :--- | :--- | :--- | :--- | :--- | :--- |
| 4 | 缁勭粐涓讳綋 | Merchant Model锛堝晢瀹讹紝鍚姸鎬佸父閲忋€佸鍦板潃鍏宠仈锛?| AI | 2026-07-29 | 宸插畬鎴?|
| 6 | 缁勭粐涓讳綋 | Driver Model锛堝徃鏈猴紝鍚姸鎬?绫诲瀷甯搁噺銆佽溅杈嗙粦瀹氬叧鑱旓級 | AI | 2026-07-29 | 宸插畬鎴?|


1. .env.example 涓枃涔辩爜淇 鈥?閲嶅啓涓烘棤 BOM UTF-8 缂栫爜锛?026-07-29锛?
2. AdminInstallCommand 寮曠敤淇 鈥?RolePermissionSeeder 鈫?SystemDataSeeder锛?026-07-29锛?
3. 绉诲姩绔眽鍫¤彍鍗曚唬鐮佹竻闄?鈥?绠＄悊鍚庡彴涓嶅仛绉诲姩绔€傞厤锛?026-07-29锛?
4. composer.json post-autoload-dump 娣诲姞鑷姩鍒涘缓缂哄け鐩綍鑴氭湰锛?026-07-29锛?


| :--- | :--- | :--- | :--- | :--- |
| 8 | AdminInstallCommand 寮曠敤宸插垹闄ょ殑 RolePermissionSeeder | `php artisan admin:install --reset --force` 鎶ラ敊绫讳笉瀛樺湪 | 鏀逛负寮曠敤 SystemDataSeeder | 宸查獙璇?|
| 9 | .env.example UTF-8 BOM 瀵艰嚧涓枃娉ㄩ噴涔辩爜 | Git clone 鍚庡湪鍏朵粬鐜鎵撳紑 .env.example 涓枃鍏ㄩ儴涔辩爜 | 閲嶅啓涓烘棤 BOM UTF-8 | 宸查獙璇?|

### 4 閬楃暀寰呰凯浠ｉ渶姹傦紙涓嬩竴鐗堟湰澶勭悊锛?

| :--- | :--- | :--- | :--- |
| 1 | Model 绗?~15鎵癸紙绾?9涓級 | P0 | 鍟嗗搧鈫扴KU鏉＄爜鈫掍粨搴撳簱瀛樷啋閲囪喘鈫掕鍗曗啋鎷ｈ揣鈫掗厤閫佲啋宸紓鎹熻€椻啋閫€璐р啋浠锋牸绛栫暐鈫掕储鍔♀啋鍧囨憡+鎵╁睍+绯荤粺+寰俊 |
| 3 | 瀹夎 Redis PHP 鎵╁睍 | P1 | .env 鐩墠鐢?file 椹卞姩 |


| 缁村害 | 宸插畬鎴?| 寰呭紑鍙?| 鍚堣 |
| :--- | :--- | :--- | :--- |
| 鏁版嵁搴撹〃 | 77锛堝凡寤?Migration锛?| 0 | 77 |
| Blade 缁勪欢 | 34+ | - | 34+ |
| Model | 16 | ~52 | ~68 |
| Livewire 缁勪欢 | 15 | - | 15 |
| 璺敱 | 17 | - | 17 |


| :--- | :--- |
| 鐗堟湰鍙?| V1.1.0-alpha |
| 鍙戝竷鏃堕棿 | 2026-07-29 |
| Git 鍒嗘敮 | navigate |
| 杩滅▼浠撳簱 | git@github.com:kindiaing/susong-livewire.git |
| 淇敼鏂囦欢 | web.php / app-topnav.blade.php / .env.example / AdminInstallCommand / composer.json / FSD |

---

## V1.0.0 | 杩唬鍛ㄦ湡锛?026-07-24 ~ 2026-07-29


| :--- | :--- | :--- | :--- | :--- | :--- |
| 2 | 鏁版嵁搴?| 20 涓?Migration锛?7 寮犺〃锛夛紝鏁存暟閲戦瀛樺偍锛屾棤澶栭敭绾︽潫 | AI | 2026-07-27 | 宸插畬鎴?|
| 4 | 鏁版嵁搴?| DemoDataSeeder锛堟祴璇曪細9 鐢ㄦ埛 + 23 閰嶇疆椤?+ 9 瑙掕壊锛?| AI | 2026-07-27 | 宸插畬鎴?|
| 5 | UI 缁勪欢搴?| 34+ Blade 缁勪欢锛坰hadcn/ui 璁捐鐞嗗康锛夛紝CSS 鍙橀噺 + 璇箟鍖?token | AI | 2026-07-25 | 宸插畬鎴?|
| 6 | 鐢ㄦ埛涓庢潈闄?| 鐧诲綍椤碉紙鐢ㄦ埛鍚?鎵嬫満/閭澶氭柟寮忥紝鑺傛祦 5 娆￠攣瀹氾紝璁颁綇 7 澶╋級 | AI | 2026-07-25 | 宸插畬鎴?|
| 7 | 鐢ㄦ埛涓庢潈闄?| 涓汉涓績锛堜慨鏀硅祫鏂?+ 淇敼瀵嗙爜锛屼腑鏂囬獙璇侊紝鏈慨鏀逛笉淇濆瓨锛?| AI | 2026-07-27 | 宸插畬鎴?|
| 15 | 瀹℃牳绠＄悊 | ApprovalService锛堝垱寤?閫氳繃/鎷掔粷/鎾ゅ洖锛岄槻閲嶅锛屽璁℃棩蹇楄仈鍔級 | AI | 2026-07-28 | 宸插畬鎴?|
| 20 | 瀵艰埅 | 绯荤粺绠＄悊瀛愯彍鍗曢摼鎺ョ湡瀹炶矾鐢憋紙settings/approval-config/operation-logs/audit-logs锛?| AI | 2026-07-28 | 宸插畬鎴?|
| 21 | UI 澧炲己 | Alert Dialog 缁勪欢锛坰hadcn/ui 椋庢牸锛? 绉?variant锛屾敮鎸?Livewire 鍥炶皟锛?| AI | 2026-07-28 | 宸插畬鎴?|
| 23 | 瀹夊叏 | CSRF Token 杩囨湡鑷姩鍒锋柊锛?19 鈫?reload锛岀敤鎴锋棤鎰熺煡锛?| AI | 2026-07-29 | 宸插畬鎴?|
| 24 | 缁勭粐涓讳綋 | 渚涘簲鍟嗙鐞?| AI | 2026-07-29 | V1.1.0 宸插畬鎴?|
| 25 | 缁勭粐涓讳綋 | 鍟嗗绠＄悊锛堝惈澶氬湴鍧€锛?| AI | 2026-07-29 | V1.1.0 宸插畬鎴?|
| 26 | 缁勭粐涓讳綋 | 閰嶉€佺嚎璺?鍙告満/杞﹁締绠＄悊 | AI | 2026-07-29 | V1.1.0 宸插畬鎴?|
| 28 | 骞冲彴缁熼噰 | 寰呴噰娓呭崟/閲囪喘鍗?閲囪喘鍏ュ簱/閲囪喘閫€璐?| - | - | 寰呭紑鍙?|
| 29 | 瀹㈡埛鐩撮噰 | 璐墿杞?璁㈠崟/绉伴噸鏀逛环/鍞悗閫€璐?| - | - | 寰呭紑鍙?|
| 31 | 鎹熻€楃鐞?| 鎹熻€楀崟锛堢敵鎶モ啋瀹℃牳鈫掓墸鍑忊啋鎴愭湰褰掗泦锛?| - | - | 寰呭紑鍙?|
| 32 | 鎷ｈ揣绠＄悊 | 鎷ｈ揣浠诲姟鍒涘缓/鍒嗛厤/鎵ц | - | - | 寰呭紑鍙?|
| 33 | 鐗╂祦閰嶉€?| 閰嶉€佷换鍔?杞ㄨ抗/绛炬敹/鍐烽摼娓╁害 | - | - | 寰呭紑鍙?|
| 34 | 宸紓澶勭悊 | 鎷ｈ揣/閰嶉€?瀹炴敹宸紓锛岄噾棰濊皟鏁村鎵?| - | - | 寰呭紑鍙?|
| 35 | 璐㈠姟瀵硅处 | 瀹㈡埛璐︽埛/鍏呭€?渚涘簲鍟嗙粨绠?搴旀敹/鍙戠エ/鎺堟潈鏇存/璐圭敤鍧囨憡 | - | - | 寰呭紑鍙?|
| 38 | 寰俊灏忕▼搴忓徃鏈虹 | 鐧诲綍/浠诲姟/閰嶉€?杞ㄨ抗/绛炬敹/宸紓鏍囪/鍘嗗彶 | - | - | 寰呭紑鍙?|


1. 閫€鍑虹櫥褰曞悗璺宠浆棣栭〉 `/` 鑰岄潪 `/login`锛?026-07-28锛?
2. SystemConfig 缂撳瓨鍙嶅簭鍒楀寲闃插尽 鈥?`instanceof` 妫€鏌?+ try/catch锛?026-07-28锛?
4. Migration 鏁寸悊 鈥?25鈫?0 涓枃浠讹紝5 涓?alter 鍚堝苟鍒?create锛屾椂闂存埑缂栧彿 000001~000020锛?026-07-28锛?
5. Seeder 鏁寸悊 鈥?SystemDataSeeder锛堝唴缃級+ DemoDataSeeder锛堟祴璇曪級锛?026-07-28锛?
6. 瀹℃牳绠＄悊/瀹℃壒鍒楄〃 鈥?wire:confirm 鏇挎崲涓?shadcn/ui Alert Dialog锛?026-07-28锛?
7. 绯荤粺璁剧疆 Drawer 鈥?浠庡簳閮ㄧ獎闈㈡澘鏀逛负鍙充晶鍏ㄥ睆 Drawer锛屾暣鍚堝叏閮?6 缁勯厤缃」锛?026-07-29锛?
8. 绯荤粺绠＄悊鍥炬爣 鈥?浠?cog 鏀逛负 adjustments-horizontal锛堥敮榻胯缃浘鏍囷級锛?026-07-29锛?
9. 瀹℃牳鑺傜偣鍏ㄦ櫙鍥?鈥?鍚堝苟鍒?FSD 鏂囨。锛屽垹闄ょ嫭绔?md 鏂囦欢锛?026-07-29锛?


| :--- | :--- | :--- | :--- | :--- |
| 1 | Livewire 缁勪欢 `private` 灞炴€ц姹傞棿涓嶆寔涔呭寲 | 涓汉涓績淇敼璧勬枡瀵规瘮鍘熷鍊煎け璐?| 鏀逛负浠庢暟鎹簱閲嶈鍘熷鍊煎姣?| 宸查獙璇?|
| 3 | `Setting::get()` 鍦ㄦ暟鎹簱琛ㄤ笉瀛樺湪鏃舵姤閿?| 鏈墽琛?migrate 鏃惰闂椤?| try/catch 鍖呰９ | 宸查獙璇?|
| 4 | audit-logs.blade.php 宓屽涓夊厓 `}}}` 瀵艰嚧 ParseError | 澶嶆潅宓屽涓夊厓杩愮畻绗?| `@php` 棰勫鐞嗛鑹叉槧灏勬暟缁?| 宸查獙璇?|
| 5 | LogOperation 涓棿浠惰褰?Livewire 鍐呴儴璇锋眰 | 鎵€鏈?Livewire POST 璇锋眰琚啓鍏ユ搷浣滄棩蹇?| 蹇界暐璺緞鍒楄〃澧炲姞 `livewire-` 鍓嶇紑 | 宸查獙璇?|
| 6 | CSRF Token 杩囨湡瀵艰嚧 419 "This page has expired" | Session 杩囨湡鍚庢彁浜ょ櫥褰曡〃鍗?| 寮傚父澶勭悊鎹曡幏 TokenMismatchException + 鍓嶇 419 鑷姩 reload | 宸查獙璇?|
| 7 | Toast duration 涓嶄竴鑷达紙4s vs 5s锛?| 澶氬 toast 瓒呮椂鏃堕棿涓嶇粺涓€ | store 榛樿瓒呮椂缁熶竴涓?5s | 宸查獙璇?|

### 4 閬楃暀寰呰凯浠ｉ渶姹傦紙涓嬩竴鐗堟湰澶勭悊锛?

| :--- | :--- | :--- | :--- |
| 1 | Model 灞傚紑鍙戯紙绗?~15鎵癸級 | P0 | 鍟嗗搧/SKU/搴撳瓨/閲囪喘/璁㈠崟/鎷ｈ揣/閰嶉€?宸紓/閫€璐?浠锋牸/璐㈠姟绛夌害 49 涓?|
| 2 | 瀹夎 Redis PHP 鎵╁睍 | P1 | .env 鐩墠鐢?file 椹卞姩 |
| 4 | 鎿嶄綔鏃ュ織瀵煎嚭 | P2 | 鏀寔鎸夋椂闂磋寖鍥淬€佹搷浣滀汉銆佹ā鍧楀鍑?|
| 5 | 鏁堟湡绠＄悊锛堜簩鏈熸崯鑰楀寮猴級 | P2 | 鏁堟湡棰勮銆佽嚜鍔ㄨЕ鍙戞崯鑰楃敵鎶?|
| 6 | 鎹熻€楀垎鏋愭姤琛紙浜屾湡锛?| P2 | 鎸夊搧绫汇€佷緵搴斿晢銆佹椂闂寸淮搴︽崯鑰楄秼鍔垮垎鏋?|
| 7 | AI 鏅鸿兘琛ヨ揣鎺ㄨ崘 | P3 | 鍩轰簬閲囪喘棰戞鍜屽簱瀛樿嚜鍔ㄦ帹鑽愯ˉ璐ф竻鍗?|


| 缁村害 | 宸插畬鎴?| 寰呭紑鍙?| 鍚堣 |
| :--- | :--- | :--- | :--- |
| 鏁版嵁搴撹〃 | 77锛堝凡寤?Migration锛?| 0 | 77 |
| Blade 缁勪欢 | 34+ | - | 34+ |
| Model | 8 | ~60 | ~68 |
| Livewire 缁勪欢 | 8 | - | 8 |
| 璺敱 | 10 | - | 10 |


| :--- | :--- |
| 鐗堟湰鍙?| V1.0.0-alpha |
| 鍙戝竷鏃堕棿 | 寰呭畾 |
| Git 鍒嗘敮 | navigate |
| 杩滅▼浠撳簱 | git@github.com:kindiaing/susong-livewire.git |
| 瓒呯骇绠＄悊鍛?| 鐢ㄦ埛鍚? seeding / 瀵嗙爜: Password |

### 7 鏁版嵁搴撳彉鏇磋褰?

| Migration 鏂囦欢鍚?| 鎿嶄綔绫诲瀷 | 娑夊強琛?| 璇存槑 |
| :--- | :--- | :--- | :--- |
| SystemDataSeeder | INSERT | roles/permissions/users/approval_type_configs/system_configs | 鍐呯疆鏁版嵁锛岀敓浜х幆澧冨繀闇€ |


| 閰嶇疆椤?| 鍙樻洿鍓?| 鍙樻洿鍚?| 璇存槑 |
| :--- | :--- | :--- | :--- |
| SESSION_DRIVER | redis | file | Redis 鎵╁睍鏈畨瑁?|
| CACHE_STORE | redis | file | Redis 鎵╁睍鏈畨瑁?|

---

## V2.0.0 鈥?閰嶉€佹ā鍧?v2 鏀归€?

### 鍙樻洿姒傝堪

閰嶉€佹ā鍧椾粠 v1锛? 寮犻厤閫佽〃 + 绠€鍗曚笁鎬佹祦杞級鍏ㄩ潰鏀归€犱负 v2锛屾敮鎸佺嚎璺浐瀹氳鍒?+ 浠诲姟鎸夐渶鐢熸垚 + 閰嶉€侀『搴忚〃 + 鍔犳€?閲嶈鏍囪 + 杞﹁締鏁呴殰澶勭悊銆?

### 鏁版嵁搴撳彉鏇?

| 鎿嶄綔 | 琛ㄥ悕 | 璇存槑 |
| :--- | :--- | :--- |
| 鏀瑰悕閲嶅缓 | delivery_task_orders 鈫?delivery_task_details | 鏁磋〃閲嶆瀯锛屾柊澧?merchant_id/merchant_name/merchant_address/product_summary/source_type/delivery_method/delivery_photos 绛夊瓧娈?|

### Model 鍙樻洿

| 鎿嶄綔 | Model | 璇存槑 |
| :--- | :--- | :--- |
| 鏇存柊 | Merchant | 绉婚櫎 delivery_route_id/delivery_sort锛屾柊澧?routeStops() |

### Livewire 缁勪欢鍙樻洿

| 鎿嶄綔 | 缁勪欢 | 璇存槑 |
| :--- | :--- | :--- |
| 閲嶅啓 | Delivery\DeliveryTaskList | 閫傞厤 v2 瀛楁鍚?|
| 閲嶅啓 | Delivery\VehicleList | 閫傞厤 v2 瀛楁鍚?|
| 搴熷純 | Org\RouteList + Org\RouteSort | 宸茶 Delivery 缁勪欢鏇夸唬锛屽緟娓呯悊 |


| 鎿嶄綔 | 璇存槑 |
| :--- | :--- |

### Git 淇℃伅

| :--- | :--- |
| 鍒嗘敮 | feature/delivery-task |
| 楠岃瘉 | migrate:refresh --seed 閫氳繃 |

---


### 鍙樻洿姒傝堪


### Livewire 缁勪欢鍙樻洿

| 鎿嶄綔 | 缁勪欢 | 璇存槑 |
| :--- | :--- | :--- |
| 鏀剁揣 | Order\OrderList | 缂栬緫鎸夐挳 1~3 鈫?浠呭緟鎷ｈ揣锛涘垹闄ゆ寜閽ˉ浠呭緟鎷ｈ揣锛沷penEditModal/save/delete/batchDelete 鏈嶅姟绔牎楠?|
| 鏀剁揣 | Purchase\PurchaseOrderList | 缂栬緫鎸夐挳 1~4 鈫?浠呭緟鎺ュ崟锛涘垹闄ゆ寜閽?1~3 鈫?浠呭緟鎺ュ崟锛涙湇鍔＄鏍￠獙鍚屾 |
| 鏀剁揣 | Purchase\PurchaseReturnList | 缂栬緫鎸夐挳 1~3 鈫?浠呭緟瀹℃牳锛涘垹闄ゆ寜閽ˉ浠呭緟瀹℃牳锛涙湇鍔＄鏍￠獙鍚屾 |
| 鏀剁揣 | Order\OrderReturnList | 缂栬緫鎸夐挳 1~2 鈫?浠呭緟瀹℃牳锛涘垹闄ゆ寜閽ˉ浠呭緟瀹℃牳锛涜鐩栨壒閲忓垹闄わ紙Trait 閫氱敤瀹炵幇鏃犵姸鎬佹牎楠岋級锛屼粎寰呭鏍稿彲鍒?|
| 鏀剁揣 | Loss\LossOrderList | 缂栬緫鏈嶅姟绔?1~2 鈫?浠呭緟瀹℃牳锛涘垹闄ゆ寜閽ˉ浠呭緟瀹℃牳锛涙湇鍔＄鏍￠獙鍚屾 |
| 鏀剁揣 | Inventory\PickingTaskList | 缂栬緫鎸夐挳 1~2 鈫?浠呭緟鍒嗛厤锛涙湇鍔＄鏍￠獙鍚屾锛堝垹闄ゅ師鏈変粎寰呭垎閰嶆牎楠屼繚鐣欙級 |

### Service 鍙樻洿

| 鎿嶄綔 | Service | 璇存槑 |
| :--- | :--- | :--- |

### 淇濈暀涓嶅彉

- 鍞悗閫€璐?鎹熻€楀崟/鎷ｈ揣浠诲姟/閰嶉€佷换鍔?鍏呭€?鍙戠エ涓嶆彁渚涘洖閫€鍏ュ彛
- 鏃犳暟鎹簱缁撴瀯鍙樻洿

### Git 淇℃伅

| :--- | :--- |
| 鍒嗘敮 | bugfix |
| 鎻愪氦 | 瑙佹湰娆℃彁浜?|
| 楠岃瘉 | Pint 閫氳繃锛涙祴璇曞浠?9 涓け璐ヤ负杩佺Щ绱㈠紩閲嶅锛坕dx_model锛夋棦鏈夐棶棰橈紝涓庢湰娆℃棤鍏?|

---

## V2.0.2 鈥?杩佺Щ绱㈠紩淇 + 鍏呭€肩‘璁ゅ叆璐?

### 鍙樻洿姒傝堪

1. 淇 `model_has_roles` / `model_has_permissions` 鍚屽悕绱㈠紩 `idx_model` 鍐茬獊锛圫QLite 娴嬭瘯搴撹姹傜储寮曞悕鍏ㄥ眬鍞竴锛夛紝鍚庤€呮敼鍚?`idx_model_permission`锛屾祴璇曞浠舵仮澶嶅彲杩愯銆?

### 鍙樻洿鏄庣粏

| 鎿嶄綔 | 鏂囦欢 | 璇存槑 |
| :--- | :--- | :--- |
| 淇 | 2026_07_27_000001_create_users_and_permissions_tables.php | `model_has_permissions` 绱㈠紩 `idx_model` 鈫?`idx_model_permission`锛堟敼 create 杩佺Щ锛岀鍚?鏃?alter"瑙勮寖锛涘瓨閲?MySQL 搴撲笉鍙楀奖鍝嶏級 |
| 瀹屽杽 | Finance\RechargeList::reject | 鍚屾 approval_status鈫掑凡鎷掔粷 |

### Git 淇℃伅

| :--- | :--- |
| 鍒嗘敮 | bugfix 鈫?鍚堝苟 main |
| 鎻愪氦 | 瑙佹湰娆℃彁浜?|
| 楠岃瘉 | 娴嬭瘯 9 閫氳繃 / 1 澶辫触锛圲nitConversionServiceTest 涓夌骇鎹㈢畻鏂█涓烘棦鏈夐棶棰橈紝涓庢湰娆℃棤鍏筹級锛汸int 閫氳繃 |
---


### 鍙樻洿姒傝堪


### 鍙樻洿鏄庣粏

| 鎿嶄綔 | 鏂囦欢 | 璇存槑 |
| :--- | :--- | :--- |
| 瀹屽杽 | Inventory/PickingTaskDetail | SKU 姹囨€昏銆屽綍鍏ュ疄鎷ｃ€嶅脊绐楋細瀹炴嫞鎸夎鍗曟槑缁嗛『搴忓垎鎽婏紝缂哄彛鑷姩鐢熸垚鎷ｈ揣宸紓鍗曪紙stage=1锛?|
| 瀹屽杽 | Delivery/DeliveryNoteDetail | 瀹炴敹灏戜簬搴旈€佹椂寮瑰嚭宸紓鐧昏锛堝疄鏀?0 榛樿鎷掓敹锛宻tage=3锛夛紝鐢熸垚宸紓鍗曞緟澶勭悊 |

### 鏁版嵁搴撳彉鏇?

| 鍙樻洿 | 琛ㄥ悕 | 瀛楁 | 璇存槑 |
| :--- | :--- | :--- | :--- |
| 璋冩暣 | discrepancies | order_id | 鏀逛负鍙┖锛堥€佽揣鍗曟槑缁嗗彲鑳芥棤鍏宠仈璁㈠崟锛?|


### Git 淇℃伅

| :--- | :--- |
| 鍒嗘敮 | bugfix |
| 鎻愪氦 | 瑙佹湰娆℃彁浜?|
| 楠岃瘉 | 娴嬭瘯 15 閫氳繃 / 1 澶辫触锛圲nitConversionServiceTest 涓夌骇鎹㈢畻鏂█涓烘棦鏈夐棶棰橈紝涓庢湰娆℃棤鍏筹級锛汸int 閫氳繃锛泇iew:cache 閫氳繃 |

---


> 鍐崇瓥渚濇嵁锛歚docs/research/11_宸紓鎹熻€楃暀鐥曚笌璐﹀姟鎸佸钩璁捐.md` 绗?11 绔狅紙鍐崇瓥 1 鏂规 A 鍏ㄩ噺瀛楁 / 鍐崇瓥 3 鎹熻€楁槑缁嗘函婧?/ 鍐崇瓥 4 鏂规 B 鏃ュ織绫诲瀷缁嗗垎鐢辫皟鐢ㄦ柟鏄惧紡浼犲叆锛夈€?

### 鍙樻洿姒傝堪

2. **宸紓鍗曡嚜鍖呭惈涓婁笅鏂?*锛歚discrepancies` 琛ㄦ柊澧?8 涓叧鑱斿瓧娈碉紝鍚勭幆鑺傜櫥璁版椂鍥炲～锛屽鐞?瀵硅处涓嶅啀渚濊禆闂存帴鍏宠仈銆?
3. **鎹熻€楁槑缁嗗彲婧簮宸紓鍗?*锛歚loss_order_items.discrepancy_id`锛屽樊寮傛姤鎹熻嚜鍔ㄥ啓鍏ャ€?
4. **搴撳瓨鏃ュ織璇箟鍖?*锛氭柊澧炵被鍨?7 鎶ユ崯鍑哄簱 / 8 鎷掓敹鍥炲簱 / 9 鍞悗閫€搴擄紙棰勭暀锛夛紝`stockIn/stockOut` 鏀寔鏄惧紡浼犵被鍨嬶紝鎹熻€楁墽琛屼笌鎷掓敹鍥炲簱宸查€傞厤銆?

### 鍙樻洿鏄庣粏

| 鎿嶄綔 | 鏂囦欢 | 璇存槑 |
| :--- | :--- | :--- |
| 淇 | Loss/LossOrderList::execute | 鏀硅蛋 `LossOrderService::execute()`锛屾墽琛屾椂鐪熷疄鎵ｅ噺搴撳瓨锛堜簨鍔″唴锛?|
| 瀹屽杽 | Models/LossOrderItem | discrepancy_id fillable/casts |
| 瀹屽杽 | Services/LossOrderService | execute 浼?TYPE_LOSS_REPORT锛沜reate 鍐欏叆鏄庣粏 discrepancy_id |
| 瀹屽杽 | Services/DiscrepancyService | create 鎺ユ敹骞跺洖濉?8 涓叧鑱斿瓧娈碉紱resolveSkuId 浼樺厛鐩磋 sku_id锛沜reateLossOrder 浼?discrepancy_id锛況estockToWarehouse 浼?TYPE_REJECT_RETURN |
| 瀹屽杽 | Inventory/PickingTaskDetail | 鎷ｈ揣宸紓鐧昏鍥炲～ picking_task_id / sku_id / warehouse_id |
| 瀹屽杽 | Delivery/DeliveryNoteDetail | 瀹炴敹宸紓鐧昏鎸夎鍗?SKU 鍙嶆煡鍥炲～ order_item_id锛屽洖濉?delivery_note_item_id / sku_id |
| 娴嬭瘯 | tests/Feature/DiscrepancyServiceTest | 琛ュ叧鑱斿瓧娈靛洖濉€佹崯鑰楁槑缁?discrepancy_id銆佹棩蹇楃被鍨?7/8 鏂█锛涙柊澧炲叏閾捐矾鐢ㄤ緥锛堝樊寮傗啋鎶ユ崯鈫掑鏍糕啋鎵ц鈫掑簱瀛樻墸鍑忥級 |


| 鍐崇瓥 | 閫夋嫨 | 璇存槑 |
| :--- | :--- | :--- |
| 宸紓鍗曞叧鑱斿瓧娈?| 鍏ㄩ噺 8 瀛楁 | 宸紓鍗曡嚜鍖呭惈锛屽璐︽晥鐜囨渶楂?|
| 鏃ュ織绫诲瀷浼犲叆鏂瑰紡 | 璋冪敤鏂规樉寮忎紶 | 鎺у埗绮剧‘锛岄粯璁ゅ弬鏁颁繚鎸佸悜鍚庡吋瀹?|

### Git 淇℃伅

| :--- | :--- |
| 鍒嗘敮 | bugfix锛堝樊寮傛崯鑰楀伐浣滃垎鏀紝寰呯敤鎴风‘璁ゅ悗鍚堝苟 main锛?|
| 鎻愪氦 | 瑙佹湰娆℃彁浜?|

---


### 鍙樻洿姒傝堪

1. **鍥涚幆鑺傚樊寮傜瓥鐣ュ紑鍏?*锛氶噰璐叆搴?/ 鎷ｈ揣 / 閰嶉€佸垎璐?/ 瀹炴敹绛炬敹鍚勬湁鐙珛銆屽厑璁稿樊寮傘€嶅紑鍏筹紙榛樿鍏ㄥ紑=瀹芥澗妯″紡锛夈€傚叧闂嵆涓ユ牸妯″紡锛氳鐜妭鏀跺彂蹇呴』涓€鑷达紝褰曞叆宸紓鐩存帴闃绘柇骞舵彁绀哄嚭鍙ｏ紙璋冩暣璁㈠崟 / 鍞悗閫€璐?/ 绠＄悊鍛樺洖閫€锛夈€?

### 鍙樻洿鏄庣粏

| 鎿嶄綔 | 鏂囦欢 | 璇存槑 |
| :--- | :--- | :--- |
| 瀹屽杽 | Inventory/PickingTaskDetail::savePickedQuantity | 涓ユ牸妯″紡闃绘柇锛堜笉淇濆瓨锛夛紱宸紓鐜?鈮?闃堝€煎彧鍒嗘憡鏍囪涓嶅缓宸紓鍗曪紱瓒呴槇鍊煎缓鍗曞苟鏀堕泦鍗曞彿锛宼oast 鍒嗕笁绉嶅満鏅紙闃堝€肩暀鐥?寤哄崟甯﹀崟鍙?姝ｅ父锛?|
| 瀹屽杽 | Delivery/DeliveryNoteDetail::confirmItemDelivery | 涓ユ牸妯″紡闃绘柇锛堜笉淇濆瓨鏄庣粏锛夛紱闃堝€煎唴鍙爣璁?STATUS_DISCREPANCY 涓嶅脊绐楋紱瓒呴槇鍊肩淮鎸佸脊绐楃櫥璁?|
| 瀹屽杽 | Delivery/DeliveryNoteDetail::markDelivered | 鍒嗚揣鍓嶆鏌ュ樊寮傛槑缁嗭細涓ユ牸妯″紡闃绘柇锛屽鏉炬ā寮?toast 璀﹀憡宸紓鏉℃暟鍚庢斁琛?|
| 瀹屽杽 | Delivery/DeliveryNoteDetail::saveItemDiscrepancy / render | toast 甯﹀樊寮傚崟鍙凤紱render 杈撳嚭鏄庣粏绾у樊寮傚崟鏄犲皠锛堟寜 delivery_note_item_id锛?|
| 瀹屽杽 | Services/PurchaseService::stockIn | 涓ユ牸妯″紡浠讳竴琛屽疄闄呪墵閲囪喘鏁伴噺鍗?throw 闃绘柇锛沜reateFromDiscrepancy 浼犲叆 purchase 闃堝€?|
| 瀹屽杽 | Services/PurchaseService::complete | 鏈鐞嗗樊寮傛牎楠屾敼涓洪槇鍊兼劅鐭ワ細宸紓鐜?鈮?闃堝€艰涓哄凡鐣欑棔澶勭悊 |
| 瀹屽杽 | purchase-order-detail.blade | 宸紓瀛愯琛ュ厖宸紓鏁伴噺涓庢崯鑰楀崟鍙峰睍绀猴紝闃堝€煎唴鏄剧ず銆屼粎鐣欑棔銆?|
| 娴嬭瘯 | tests/Feature/DiscrepancyThresholdTest | 4 鏉＄敤渚嬶細涓ユ牸闃绘柇 / 闃堝€煎厤鍗曪紙鍚畬鎴愪笉闃绘柇锛? 瓒呴槇鍊煎缓鍗?/ 榛樿閰嶇疆鍏滃簳鏂█ |


| 鍐崇瓥 | 閫夋嫨 | 璇存槑 |
| :--- | :--- | :--- |
| 榛樿妯″紡 | 鍏ㄥ鏉?| 鐢ㄦ埛鎷嶆澘锛涗弗鏍兼ā寮忛€氳繃绯荤粺璁剧疆閫愮幆鑺傚紑鍚?|
| 鍏佸樊闃堝€?| 鏈湡钀藉湴 | 闃堝€煎彲鑷畾涔変慨鏀癸紙0~100%锛夛紝0=浠讳綍宸紓閮界敓鎴愬崟鎹?|
| 閰嶉€佺幆鑺傛寕鐐?| markDelivered 纭鍒嗚揣 | 鍒嗚揣鏄厤閫佷晶鍞竴鏁伴噺纭鍔ㄤ綔 |

### Git 淇℃伅

| :--- | :--- |
| 鍒嗘敮 | bugfix锛堝樊寮傛崯鑰楀伐浣滃垎鏀紝寰呯敤鎴风‘璁ゅ悗鍚堝苟 main锛?|
| 鎻愪氦 | 瑙佹湰娆℃彁浜?|
| 楠岃瘉 | 娴嬭瘯 20 閫氳繃 / 1 澶辫触锛圲nitConversionServiceTest 鏃㈡湁闂锛夛紱Pint 閫氳繃锛泇iew:cache 閫氳繃锛涘紑鍙戝簱闇€ migrate:fresh --seed 鐢熸晥鏂伴厤缃?|

---

## V2.0.6 鈥?閲囪喘宸紓鎹熻€楀崟浠呰处鍔＄暀鐥曪紙涓嶆墸搴撳瓨锛? 閲囪喘鍗曞垪琛ㄥ樊寮傝绀哄浘鏍?


### 鍙樻洿姒傝堪

2. **閲囪喘鍗曞垪琛ㄨ绀哄浘鏍?*锛氬崟鍙锋梺璀︾ず鍥炬爣鍒嗚壊鈥斺€旂孩鑹?宸茬敓鎴愭崯鑰楀崟寰呭鏍革紝姗欒壊=鍏佸樊闃堝€煎唴浠呯暀鐥曪紱hover 鎻愮ず宸紓鏄庣粏鏉℃暟涓庣煭缂烘€婚噺銆?

### 鍙樻洿鏄庣粏

| 鎿嶄綔 | 鏂囦欢 | 璇存槑 |
| :--- | :--- | :--- |
| 瀹屽杽 | Services/LossOrderService::execute | 鏉ユ簮=閲囪喘鍗曡烦杩?stockOut锛屼粎鐣欒处锛堟敞閲婅鏄庨槻鍙岄噸鎵ｅ噺锛?|
| 瀹屽杽 | Loss/LossOrderList::close | 鏉ユ簮=閲囪喘鍗曚笖宸插鏍哥殑鎹熻€楀崟鍏佽鐩存帴鍏抽棴 |
| 瀹屽杽 | loss-order-list.blade | 鏉ユ簮=閲囪喘鍗曢殣钘忔墽琛屾寜閽紱瀹℃牳鍚庢樉绀哄叧闂寜閽紙title 璇存槑浠呯暀鐥曪級 |

### Git 淇℃伅

| :--- | :--- |
| 鍒嗘敮 | bugfix锛堝緟鐢ㄦ埛纭鍚庡悎骞?main锛?|
| 鎻愪氦 | 瑙佹湰娆℃彁浜?|
| 楠岃瘉 | 娴嬭瘯 21 閫氳繃 / 1 澶辫触锛圲nitConversionServiceTest 鏃㈡湁闂锛夛紱Pint / view:cache 閫氳繃 |

---

## V2.0.7 鈥?渚涘簲鍟嗙粨绠椾慨澶嶏細鍛ㄦ湡缁撶畻妯″紡钀藉湴 + 璐﹀姟鎸佸钩鑱斿姩


### 鍙樻洿姒傝堪

1. **淇鏂**锛氱粨绠楃粍浠舵寜琛ㄤ腑涓嶅瓨鍦ㄧ殑骞诲奖瀛楁锛坧urchase_order_id/settlement_date/note锛夊疄鐜帮紝鍒涘缓缁撶畻鍗曞繀鎶?SQL 閿欒銆傞噸鍐欎负琛ㄧ粨鏋勮璁＄殑鍛ㄦ湡缁撶畻妯″紡锛氫緵搴斿晢 + 缁撶畻鍛ㄦ湡 鈫?鑷姩姹囨€诲懆鏈熷唴宸插叆搴?宸插畬鎴愰噰璐崟锛堟寜鍏ュ簱鏃堕棿锛夌敓鎴愮粨绠楁槑缁嗭紝宸茬粨绠楅噰璐崟闃查噸澶嶈鍏ャ€?

### 鍙樻洿鏄庣粏

| 鎿嶄綔 | 鏂囦欢 | 璇存槑 |
| :--- | :--- | :--- |
| 閲嶅啓 | Finance/SupplierSettlementList | 琛ㄥ崟鏀瑰懆鏈熺粨绠楋紱鍒楅厤缃榻愮湡瀹炲瓧娈碉紱浠樻闂幆+瓒呬粯鎷︽埅锛涘垹闄ら檺鍒跺緟缁撶畻 |
| 瀹屽杽 | Models/SupplierSettlement | 娓呯悊骞诲奖瀛楁 fillable/casts/purchaseOrder 鍏宠仈 |


| 鍐崇瓥 | 閫夋嫨 | 璇存槑 |
| :--- | :--- | :--- |
| 缁撶畻妯″紡 | 鍛ㄦ湡缁撶畻锛堟柟鍚戜竴锛?| 琛ㄧ粨鏋?绉嶅瓙鏁版嵁鍧囨寜鍛ㄦ湡璁捐锛岀粍浠舵槸璺戝亸鏂?|
| 搴斾粯鍙ｅ緞 | 瀹為檯鍏ュ簱閲戦锛堝彛寰?A锛?| 宸紓澶╃劧鎸佸钩锛屾墸鍑忚矾寰勬渶鐭?|
| 閫€璐ф墸鍑忓彛寰?| 宸插畬鎴愰€€璐х殑瀹為檯鍑哄簱閲戦 | 涓庡叆搴撳彛寰勫绉?|
| 浠樻瀹℃牳娴?| 浜屾湡 | approval_status 瀛楁淇濈暀锛屾殏涓嶆帴瀹℃牳绠＄悊 |

### Git 淇℃伅

| :--- | :--- |
| 鍒嗘敮 | feature/discrepancy-loss |
| 鎻愪氦 | 瑙佹湰娆℃彁浜?|
| 楠岃瘉 | 娴嬭瘯 27 閫氳繃 / 1 澶辫触锛圲nitConversionServiceTest 鏃㈡湁闂锛夛紱Pint / view:cache 閫氳繃 |

---

## V2.0.8 鈥?渚涘簲鍟嗙粨绠椾氦浜掍慨澶?+ 婕旂ず鏁版嵁瀵归綈


### 鍙樻洿姒傝堪

3. **婕旂ず鏁版嵁瀵归綈鍛ㄦ湡缁撶畻**锛歅O-20260725-00001 琛?stocked_at/completed_at 涓庡湡璞嗘槑缁?discrepancy_quantity=200锛涙柊澧?PO-20260726-00003锛堜赴娑﹁倝涓毬锋棤宸紓 160000锛変笌 PO-20260727-00004锛堜赴娑﹁倝涓毬烽矞铏剧煭缂?200 鏈夊樊寮傦紝璁?120000 瀹炴敹 100000锛夛紝涓ゅ崟宸插叆搴擄紱SS-20260728-00002 琛ヤ袱鏉＄粨绠楁槑缁嗭紝姹囨€?260000 鈭?鏈嶅姟璐?3000 = 搴斾粯 257000锛屼笌缁撶畻鍗曞ご涓€鑷淬€?

### Git 淇℃伅

| :--- | :--- |
| 鍒嗘敮 | feature/discrepancy-loss |
| 鎻愪氦 | 瑙佹湰娆℃彁浜?|
| 楠岃瘉 | 娴嬭瘯 27 閫氳繃 / 1 澶辫触锛堟棦鏈夐棶棰橈級锛汸int / view:cache 閫氳繃锛沵igrate:fresh + DemoDataSeeder 閲嶈窇楠岃瘉鏁版嵁涓€鑷?|

---

## V2.0.9 鈥?閲囪喘閫€璐у弻妯″紡锛氬叧鑱旈€€璐?+ 鏅€氶€€璐?


### 鍙樻洿姒傝堪

1. **鍙屾ā寮忛€€璐?*锛氭柊澧?`return_type`锛? 鏅€?/ 2 鍏宠仈锛岄粯璁ゅ叧鑱旓級銆傚叧鑱旈€€璐ф部鐢ㄩ噰璐崟鏄庣粏璋冨彇锛涙櫘閫氶€€璐т笉鍏宠仈閲囪喘鍗曪紙`purchase_order_id` 鍙┖锛夛紝鏄庣粏瀹屽叏鑷畾涔夈€?
2. **鍏宠仈閫€璐т笂闄愪慨姝?*锛氶€€璐ф暟閲忔嫤鎴彛寰勪粠璁㈣喘鏁伴噺鏀剁揣涓?*瀹為檯鍏ュ簱鏁伴噺**鈥斺€斿惈鍏ュ簱宸紓鏃讹紝灏戦€佺殑璐т笉鍏佽琚€€锛屼繚璇侀€€鐨勮揣鍦ㄥ簱閲屻€?
3. **鏅€氶€€璐ф槑缁嗗伐鍏?*锛氬垱寤哄脊绐楁敮鎸侀€愯娣诲姞锛圫KU 涓嬫媺 + 鏁伴噺 + 鍗曚环(鍏? + 鍘熷洜锛夈€佷笅杞?Excel 瀵煎叆妯℃澘銆佹寜 SKU 缂栫爜鍖归厤瀵煎叆锛堟湭璇嗗埆缂栫爜姹囨€绘彁绀猴級銆佸鍑烘槑缁?CSV锛堝甫 BOM锛夈€?
4. **鏃犲崟娴佺▼閫傞厤**锛氬嚭搴撲粨搴撴櫘閫氶€€璐у彇閫€璐у崟澶?`warehouse_id`锛涘畬鎴?浣滃簾鏃?`purchaseOrder?->updateReturnStatus()` 绌哄畨鍏紝鏅€氶€€璐т笉瑙﹀彂閲囪喘鍗曢€€璐х姸鎬佹洿鏂般€?
6. **璇︽儏椤甸€傞厤**锛氭櫘閫氶€€璐ф樉绀恒€屾櫘閫氶€€璐э紙鏃犲叧鑱旈噰璐崟锛夈€嶆浛浠ｉ噰璐崟閾炬帴锛屾柊澧炵被鍨嬪窘绔犮€?

### 鍙樻洿鏄庣粏

| 鎿嶄綔 | 鏂囦欢 | 璇存槑 |
| :--- | :--- | :--- |
| 淇敼 | 杩佺Щ 000016_create_return_tables | `purchase_order_id` / `purchase_return_items.purchase_order_item_id` 鍙┖锛涙柊澧?`return_type` tinyint default 2 |
| 瀹屽杽 | Models/PurchaseReturn | TYPE_NORMAL/TYPE_LINKED 甯搁噺銆乼ypeMap銆乺eturn_type_label 璁块棶鍣?|
| 閲嶅啓 | Livewire/Purchase/PurchaseReturnList | 绫诲瀷鍒囨崲銆乫ormItems 鏄庣粏琛岀紪杈戙€乤ddItemRow/removeItemRow銆丒xcel 瀵煎叆/妯℃澘涓嬭浇/鏄庣粏瀵煎嚭銆佸垪琛ㄦ柊澧炵被鍨嬪垪 |
| 淇敼 | purchase-return-detail.blade | 鏅€氶€€璐ф棤閲囪喘鍗曟椂鐨勬樉绀轰笌绫诲瀷寰界珷 |
| 娴嬭瘯 | tests/Feature/PurchaseReturnFlowTest | 7 鏉＄敤渚嬶細鍏宠仈鍒涘缓璁′环/瓒呭疄鍏ユ嫤鎴?鏈叆搴撴嫤鎴?鏅€氬垱寤?绌烘槑缁嗕笌闆舵暟閲忔嫤鎴?鏅€氶€€璐у叏娴佺▼鎵ｅ簱瀛?缁撶畻姹囨€诲彛寰?|
| 鏂囨。 | docs/07銆乨ocs/12 | 閲囪喘閫€璐х珷鑺傝ˉ鍙屾ā寮忕敤渚嬩笌鎿嶄綔璇存槑锛?7 鐢ㄤ緥缁熻 122鈫?26锛?|


| 鍐崇瓥 | 閫夋嫨 | 璇存槑 |
| :--- | :--- | :--- |
| 1A 鍏宠仈閫€璐ф暟閲忎笂闄?| 瀹為檯鍏ュ簱鏁伴噺 | 閫€鐨勮揣蹇呴』鍦ㄥ簱閲岋紝涓庡叆搴撳樊寮傚彛寰勪竴鑷?|
| 2A 鏅€氶€€璐у繀濉」 | 渚涘簲鍟?+ 浠撳簱 | 渚涘簲鍟嗗繀濉繚璇佺粨绠楁眹鎬诲彛寰勶紱浠撳簱蹇呭～淇濊瘉鍑哄簱鎵ｅ簱瀛樻湁鐩爣 |
| 3A 鏅€氶€€璐у崟浠?| 鏄庣粏琛屾墜宸ュ～鍐?| 鍏冭緭鍏ャ€佸帢瀛樺偍锛岀伒娲昏鐩栧巻鍙?闆舵暎閫€璐т环鏍?|

### Git 淇℃伅

| :--- | :--- |
| 鍒嗘敮 | feature/discrepancy-loss |
| 鎻愪氦 | 瑙佹湰娆℃彁浜?|

---

## V2.0.10 鈥?閲囪喘閫€璐т氦浜掗噸鏋勶細澶村厛琛?+ 璇︽儏椤电淮鎶ゆ槑缁?


### 鍙樻洿姒傝堪

1. **涓ゆ寤哄崟**锛氭柊澧炲脊绐楀彧濉崟澶达紙绫诲瀷 / 鍏宠仈閲囪喘鍗?鎴?渚涘簲鍟?浠撳簱 / 鍘熷洜澶囨敞锛夛紝淇濆瓨鍚庤嚜鍔ㄨ烦杞€€璐у崟璇︽儏椤点€?
3. **鏈嶅姟灞傞厤濂?*锛歝reateLinked/createNormal 鍏佽绌哄ご鍒涘缓锛涙柊澧?updateItems()锛堜粎寰呭鏍革紝鍏堝垹鍚庡缓 + 閲嶇畻閲戦锛屽叧鑱旀ā寮忓鐢ㄥ疄闄呭叆搴撴暟閲忎笂闄愭牎楠屼笌閲嶅琛屾嫤鎴級锛沘pprove() 鎷︽埅鏃犳槑缁嗗崟鎹紙"璇峰厛娣诲姞閫€璐ф槑缁嗗啀瀹℃牳"锛夈€?
4. **鍏宠仈妯″紡閫夎浜や簰**锛氳鎯呴〉鏄庣粏琛屼粠閲囪喘鍗曟槑缁嗕笅鎷夐€夋嫨锛岃嚜鍔ㄥ甫鍑?SKU銆佸彲閫€涓婇檺锛堝疄闄呭叆搴撴暟閲忥級銆佸崟浠枫€?

### 鍙樻洿鏄庣粏

| 鎿嶄綔 | 鏂囦欢 | 璇存槑 |
| :--- | :--- | :--- |
| 瀹屽杽 | Services/PurchaseReturnService | 绌哄ご鍒涘缓锛泆pdateItems()锛涙槑缁嗚鍒涘缓鎻愬彇涓?createLinkedItem/createNormalItem锛涘鏍告牎楠屾槑缁嗛潪绌?|
| 閲嶅啓 | Livewire/Purchase/PurchaseReturnList | 绉婚櫎鏄庣粏琛?瀵煎叆瀵煎嚭閫昏緫锛泂ave() 鍒涘缓鍚?redirect 璇︽儏椤?|
| 閲嶅啓 | Livewire/Purchase/PurchaseReturnDetail | 杩佸叆鏄庣粏缂栬緫锛歠ormItems 鍚屾銆乤ddItemRow/pickOrderItem/removeItemRow/importItems/exportItemsTemplate/exportItems/saveItems |
| 淇敼 | purchase-return-detail.blade | 寰呭鏍稿彲缂栬緫鏄庣粏琛ㄦ牸锛堝惈宸ュ叿鏍忥級锛岄潪寰呭鏍稿彧璇?|

### Git 淇℃伅

| :--- | :--- |
| 鍒嗘敮 | feature/discrepancy-loss |
| 鎻愪氦 | 瑙佹湰娆℃彁浜?|
| 楠岃瘉 | 閫€璐ф祴璇?10 閫氳繃锛涘叏閲?37 閫氳繃 / 1 澶辫触锛圲nitConversionServiceTest 鏃㈡湁闂锛夛紱Pint / view:cache 閫氳繃 |

---

## V2.0.11 鈥?鍞悗閫€璐ч噸鏋勶細瀵归綈閲囪喘閫€璐с€屽ご鍏堣 + 璇︽儏椤垫槑缁嗐€嶆ā寮?


### 鍙樻洿姒傝堪

1. **琛ラ綈缂哄け灞?*锛氬敭鍚庨€€璐ф鍓嶆棤鏈嶅姟灞傘€佹棤璇︽儏椤点€佹槑缁嗕粠涓嶅啓鍏ワ紙鍒涘缓浠呰鍗?鍟嗗+鍘熷洜锛夛紝涓旂粍浠跺啓 `note` 骞诲奖瀛楁锛堣〃瀛楁涓?remark锛岄潤榛樹涪澶憋級鈥斺€旀湰娆″叏閮ㄤ慨澶嶃€?

### 鍙樻洿鏄庣粏

| 鎿嶄綔 | 鏂囦欢 | 璇存槑 |
| :--- | :--- | :--- |
| 淇敼 | 杩佺Щ 000016_create_return_tables | order_returns 琛?returned_at/refunded_at/cancelled_at |
| 瀹屽杽 | Models/OrderReturn | 涓変釜鏃堕棿鎴冲叆 fillable/casts |
| 閲嶅啓 | Livewire/Order/OrderReturnList | 澶村厛琛屽垱寤?璺宠浆锛涚姸鎬佹祦杞鎵樻湇鍔″眰锛沶ote鈫抮emark 骞诲奖瀛楁淇 |
| 淇 | Demo/OrderDemoSeeder | RT-20260729-00001 澶撮噾棰濅笌鏄庣粏瀵归綈 |
| 鏂囨。 | docs/07銆乨ocs/12 | TC-ORDER-006 绯诲垪 5 鏉★紙缁熻 127鈫?31锛夛紱鐢ㄦ埛鎵嬪唽 4.8 閲嶅啓 |

### Git 淇℃伅

| :--- | :--- |
| 鍒嗘敮 | feature/discrepancy-loss |
| 鎻愪氦 | 瑙佹湰娆℃彁浜?|

---

## V2.0.12 鈥?閫€璐ц〃鍗?bugfix锛氬師鍥犻潪蹇呭～ + 鍏宠仈閫€璐ф敼浠?


### 鍙樻洿姒傝堪

1. **閫€璐у師鍥犻潪蹇呭～**锛氶噰璐€€璐э紙鍒涘缓/缂栬緫锛変笌鍞悗閫€璐э紙鍒涘缓锛夎〃鍗曟牎楠岀敱 required 鏀?nullable锛屽脊绐楀幓闄ゅ繀濉槦鏍囥€?

### Git 淇℃伅

| :--- | :--- |
| 鍒嗘敮 | feature/discrepancy-loss |
| 鎻愪氦 | 瑙佹湰娆℃彁浜?|
| 楠岃瘉 | 鍏ㄩ噺 44 閫氳繃 / 1 澶辫触锛堟棦鏈夐棶棰橈級锛汸int / view:cache 閫氳繃 |

---

## V2.0.13 鈥?鎹熻€楀崟鏄庣粏琛?+ 鎹熻€楃被鍨嬪姛鑳芥€у父閲?


### 鍙樻洿姒傝堪

2. **createFromDiscrepancy() 浣跨敤 LOSS_PURCHASE**锛氶噰璐叆搴撳樊寮傝嚜鍔ㄧ敓鎴愮殑鎹熻€楀崟 loss_type 浠?LOSS_OTHER 鏀逛负 LOSS_PURCHASE锛堜富琛ㄥ拰鏄庣粏涓ゅ锛夈€?

### 璁捐鍐崇瓥
- 鎴愭湰浠疯嚜鍔ㄥ甫鍑?SKU 鐨?cost_price锛堝姞鏉冨钩鍧囨垚鏈环锛夛紝涓?0 鏃?fallback 鍒?purchase_price锛屽厑璁告墜鍔ㄤ慨鏀?
- 缂栬緫妯″紡鏄庣粏澶勭悊锛氬垹闄ゆ棫鏄庣粏閲嶆柊鍒涘缓锛堜粎寰呭鏍哥姸鎬佸彲缂栬緫锛屾棤搴撳瓨鍙樺姩椋庨櫓锛?

### 鏁版嵁搴撳彉鏇?
鏃狅紙鏃犳柊澧炲瓧娈碉紝浠呭父閲忓畾涔夊拰涓氬姟閫昏緫鍙樻洿锛?

### 娑夊強鏂囦欢
| 鍙樻洿 | 鏂囦欢 | 璇存槑 |
| :--- | :--- | :--- |
| 淇敼 | app/Services/LossOrderService.php | createFromDiscrepancy() loss_type 鏀逛负 LOSS_PURCHASE |

### Git 淇℃伅

| :--- | :--- |
| 鍒嗘敮 | fix/bugfix-round-1 |
| 楠岃瘉 | PHP 璇硶妫€鏌?3 鏂囦欢閫氳繃锛泇iew:clear + config:clear锛沘dmin:install --seed --reset --force 22 杩佺Щ + 鍏ㄩ儴 Seeder 鏃犻敊璇?|