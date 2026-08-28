---
AIGC:
  ContentProducer: '001191110102MAD55U9H0F10002'
  ContentPropagator: '001191110102MAD55U9H0F10002'
  Label: '1'
  ProduceID: 'b97f2f80-2de9-4db3-b7bb-30350cc0661e'
  PropagateID: 'b97f2f80-2de9-4db3-b7bb-30350cc0661e'
  ReservedCode1: '2b88e147-9b64-4d5c-8c92-01251ff86a3a'
  ReservedCode2: '2b88e147-9b64-4d5c-8c92-01251ff86a3a'
---

# 寮€鍙戜换鍔℃竻鍗?
> 鎸夋棩涓哄崟浣嶈褰曞紑鍙戜换鍔℃竻鍗曞拰寰呭姙娓呭崟锛屾瘡瀹屾垚涓€椤规爣璁扮姸鎬併€傛柊澧炲唴瀹圭疆浜庨《閮紙鍊掑簭锛夈€?
---

# 开发任务清单
> 按日为单位记录开发任务清单和待办清单，每完成一项标记状态。新增内容置于顶部（倒序）。
---

## 2026-08-28（损耗单明细行 + 损耗类型常量）
### 已完成任务
| 序号 | 功能模块 | 任务内容 | 状态 |
| :--- | :--- | :--- | :--- |
| 1 | 损耗管理 | LossOrder 模型新增功能性来源类型常量：LOSS_PURCHASE=7(采购少收)、LOSS_PICKING=8(拣货差异)、LOSS_DELIVERY=9(配送差异)，更新 typeMap() 和 sourceTypeMap()，更新 PHPDoc | ✅ |
| 2 | 损耗管理 | LossOrderService::createFromDiscrepancy() 中 loss_type 从 LOSS_OTHER 改为 LOSS_PURCHASE，使采购入库差异自动生成的损耗单标记为"采购少收" | ✅ |
| 3 | 损耗管理 | LossOrderList 组件新增明细行管理：addItemRow()/removeItemRow()/updatedFormItems()（SKU 选择自动带出成本价，cost_price 为 0 时 fallback 到 purchase_price）/formTotalAmountCents 计算属性 | ✅ |
| 4 | 损耗管理 | LossOrderList::save() 改用 LossOrderService::create() 创建损耗单（含明细），编辑模式删除旧明细重新创建，自动计算 total_amount | ✅ |
| 5 | 损耗管理 | openCreateModal() 新增方法，创建时自动添加一行空明细；openEditModal() 加载已有明细到表单 | ✅ |
| 6 | 损耗管理 | loss-order-list.blade.php 弹窗改造：仓库/类型/原因/备注改为两列布局，新增损耗明细行表格（SKU选择+成本价+数量+损耗金额+责任方+供应商+原因+删除行），底部合计金额，弹窗宽度 max-w-3xl | ✅ |
| 7 | 损耗管理 | 列表页筛选下拉框新增 7-9 类型选项（采购少收/拣货差异/配送差异），手动创建弹窗类型下拉框仅显示 1-6（功能性类型不可手动选择） | ✅ |

### 涉及文件

**模型（1 个文件）：**
- `app/Models/LossOrder.php` — 新增 LOSS_PURCHASE=7/LOSS_PICKING=8/LOSS_DELIVERY=9 常量 + typeMap() 补充 + sourceTypeMap() 补充 picking/delivery + PHPDoc 更新

**服务（1 个文件）：**
- `app/Services/LossOrderService.php` — createFromDiscrepancy() 中 loss_type 从 LOSS_OTHER 改为 LOSS_PURCHASE（主表和明细两处）

**Livewire 组件（1 个文件）：**
- `app/Livewire/Loss/LossOrderList.php` — 新增 formItems 属性 + openCreateModal/addItemRow/removeItemRow/updatedFormItems/getFormTotalAmountCentsProperty + save() 改用 LossOrderService::create() + openEditModal 加载明细 + resetForm 清空明细 + render() 传递 suppliers/skus/formTotalAmount + 引入 Sku/Supplier/LossOrderItem/DB

**Blade 视图（1 个文件）：**
- `resources/views/livewire/loss/loss-order-list.blade.php` — 筛选下拉框加 7-9 类型 + 弹窗改两列布局 + 新增损耗明细行表格（SKU/成本价/数量/金额/责任方/供应商/原因/删除）+ 合计行 + 弹窗宽度 max-w-3xl

### 设计决策
- 手动创建弹窗类型下拉框仅显示 1-6（备注型），7-9 由系统自动赋值，不可手动选择
- 列表筛选下拉框显示全部 9 种类型，支持筛选自动生成的损耗单
- 成本价自动带出 SKU 的 cost_price（加权平均成本价），为 0 时 fallback 到 purchase_price，允许手动修改
- 编辑模式明细处理：删除旧明细重新创建（仅待审核状态可编辑，无库存变动风险）
- 责任方选"供应商"时动态显示供应商下拉框，选"平台"时显示"—"

---

## 2026-08-28（差异处理二期 + 付款审核流）
### 已完成任务
| 序号 | 功能模块 | 任务内容 | 状态 |
| :--- | :--- | :--- | :--- |
| 1 | 差异处理 | 补货决策自动生成补货提醒：process() 中 DECISION_RESTOCK 分支新增 createRestockReminder()，生成 CYCLE_ONCE 补货提醒关联差异单 | ✅ |
| 2 | 差异处理 | 退款/扣款审核后流转：process() 中 REFUND/DEDUCT 检查审核开关，需要审核则创建审批记录；新增 applyRefundDeductApproval()/rejectRefundDeductApproval() 方法 | ✅ |
| 3 | 差异处理 | 回库审核闸门：restockToWarehouse() 检查 diff_restock 审核开关，开启时创建审批记录延后执行；新增 applyRestockApproval()/rejectRestockApproval() 方法 | ✅ |
| 4 | 差异处理 | 审批后业务联动：Approvals 组件新增 dispatchApprovalBusiness() 方法，审核通过/拒绝后分发到对应 Service | ✅ |
| 5 | 供应商结算 | 付款审核流：SupplierSettlementService 新增 recordPayment()/applyPayment()/rejectPayment()；SupplierSettlementDetail 组件改用新方法 | ✅ |
| 6 | 审批配置 | 新增 diff_restock 审核类型（P1 默认关闭），Seeder 初始化 | ✅ |
| 7 | 补货提醒 | RestockReminderList 新增差异单号列+蓝色链接，查询加 with('discrepancy')，搜索支持差异单号 | ✅ |
| 8 | 差异详情 | discrepancy-detail 视图新增关联补货提醒区块，处理弹窗选项文案更新，回库提示文案更新 | ✅ |
| 9 | 结算详情 | supplier-settlement-detail 视图付款记录加审核状态徽章（待审/已通过/已拒绝），已拒绝行标灰 | ✅ |

### 涉及文件

**迁移（2 个文件）：**
- `database/migrations/2026_07_27_000019_create_notification_tables.php` — restock_reminders 表 merchant_id 改 nullable + 新增 discrepancy_id + remark 字段 + 索引调整
- `database/migrations/2026_07_27_000020_create_approval_tables.php` — 新增 diff_restock 审核类型 Seeder

**模型（2 个文件）：**
- `app/Models/RestockReminder.php` — fillable 加 discrepancy_id/remark + casts + discrepancy() 关联
- `app/Models/Discrepancy.php` — 新增 restockReminders() hasMany 关联

**服务（2 个文件）：**
- `app/Services/DiscrepancyService.php` — process() 补货/退款/扣款分支改造 + createRestockReminder() + applyRefundDeductApproval()/rejectRefundDeductApproval() + restockToWarehouse 审核闸门 + applyRestockApproval()/rejectRestockApproval()
- `app/Services/SupplierSettlementService.php` — 新增 recordPayment()/applyPayment()/rejectPayment()

**Livewire 组件（3 个文件）：**
- `app/Livewire/System/Approvals.php` — approve()/reject() 后调用 dispatchApprovalBusiness() 分发
- `app/Livewire/Delivery/DiscrepancyDetail.php` — loadDiscrepancy 加 restockReminders + process/restock toast 文案更新
- `app/Livewire/Finance/SupplierSettlementDetail.php` — submitPayment 改用 SupplierSettlementService::recordPayment()
- `app/Livewire/Product/RestockReminderList.php` — getAllColumns 加差异单号列 + 查询加 with('discrepancy') + 搜索支持

**Blade 视图（3 个文件）：**
- `resources/views/livewire/delivery/discrepancy-detail.blade.php` — 关联补货提醒区块 + 处理弹窗选项文案 + 回库提示文案
- `resources/views/livewire/finance/supplier-settlement-detail.blade.php` — 付款记录加审核状态徽章
- `resources/views/livewire/product/restock-reminder-list.blade.php` — 差异单号列+蓝色链接

---

## 2026-08-28锛堟潈闄愭敹鏁?+ 閲戦瀛楁瑕嗙洊纭锛?
### 宸插畬鎴愪换鍔?
| 搴忓彿 | 鍔熻兘妯″潡 | 浠诲姟鍐呭 | 鐘舵€?|
| :--- | :--- | :--- | :--- |
| 1 | 鏉冮檺涓棿浠?| 琛ラ綈 CheckPermission::ROUTE_PERMISSION_MAP 缂哄け鏄犲皠锛?0 澶勮鎯?椤甸潰璺敱锛夛紝淇 2 澶勯敊璇槧灏勶紙picking-tasks鈫抜nventory.picking-task.view銆乨elivery-routes鈫抎elivery.route.view锛?| 鉁?|
| 2 | 鏉冮檺涓棿浠?| 绉婚櫎涓嶅瓨鍦ㄧ殑 price-strategies 姝婚摼鏄犲皠 | 鉁?|
| 3 | 鏉冮檺楠岃瘉 | 鏍￠獙鑴氭湰楠岃瘉锛?7 涓彈鏉冮檺淇濇姢璺敱鍏ㄩ儴鏈夋槧灏勶紝78 涓槧灏勬潈闄愬悕鍏ㄩ儴瀛樺湪锛屾棤姝婚摼 | 鉁?|
| 4 | 閲戦瀛楁 | 璋冪爺 29 涓?Model 閲戦瀛楁 money_format 瑕嗙洊锛氬叏閮ㄨ鍥鹃噾棰濆睍绀哄凡鐢?money_format锛屾棤閬楁紡 | 鉁?|
| 5 | 閲戦瀛楁 | 纭 2 澶?number_format(/1000) 涓鸿緭鍏ユ琛ㄥ崟鍊硷紙price_yuan锛夈€佸鍑?Excel 涓烘暟鎹浆鎹㈠眰锛屽悎鐞嗕笉澶勭悊 | 鉁?|
| 6 | 璇硶楠岃瘉 | 12 涓?PHP 鏂囦欢璇硶妫€鏌ュ叏閮ㄩ€氳繃 + view:clear | 鉁?|

---

## 2026-08-27锛圥1 璇︽儏椤佃ˉ鍏級

### 宸插畬鎴愪换鍔?
| 搴忓彿 | 鍔熻兘妯″潡 | 浠诲姟鍐呭 | 鐘舵€?|
| :--- | :--- | :--- | :--- |
| 1 | 渚涘簲鍟嗙粨绠?| `SupplierSettlementDetail` 鐙珛璇︽儏椤碉細閲戦鏋勬垚鏄庣粏/缁撶畻鏄庣粏锛堝叧鑱旈噰璐崟锛?浠樻璁板綍鏃堕棿绾匡紝浠樻+鍔炵粨寮圭獥浠庡垪琛ㄨ縼绉诲埌璇︽儏椤碉紝鍒楄〃钃濊壊閾炬帴璺宠浆 | 瀹屾垚 |
| 2 | 鍏呭€肩鐞?| `RechargeDetail` 鐙珛璇︽儏椤碉細鍏呭€奸噾棰?瀹℃牳淇℃伅锛屽鏍搁€氳繃/鎷掔粷/纭鍒拌处鎿嶄綔鎸夐挳锛屽垪琛ㄨ摑鑹查摼鎺ヨ烦杞?| 瀹屾垚 |
| 3 | 鍙戠エ绠＄悊 | `InvoiceDetail` 鐙珛璇︽儏椤碉細鍙戠エ淇℃伅/鐘舵€佽建杩癸紙寰呭紑鍏封啋宸插紑鍏封啋宸插瘎鍑猴級锛屽紑鍏?瀵勫嚭鎿嶄綔鎸夐挳锛屽垪琛ㄨ摑鑹查摼鎺ヨ烦杞?| 瀹屾垚 |
| 4 | 瀹㈡埛璐︽埛 | `MerchantAccountDetail` 鐙珛璇︽儏椤碉細璐︽埛浣欓锛堜綑棰濃垝鍐荤粨=鍙敤锛?璧勯噾姹囨€伙紙鎬诲厖鍊?鎬绘秷璐?淇＄敤棰濆害锛?鏈€杩戝厖鍊艰褰曪紝鍒楄〃钃濊壊閾炬帴璺宠浆 | 瀹屾垚 |
| 5 | 鎹熻€楃鐞?| `LossOrderDetail` 鐙珛璇︽儏椤碉細鎹熻€楅噾棰?鎹熻€楁槑缁嗚〃鏍硷紙SKU脳鏁伴噺脳鎴愭湰浠访楁崯鑰楅噾棰澝楄矗浠绘柟锛?鐘舵€佽建杩癸紝瀹℃牳/鎵ц/鍏抽棴鎿嶄綔鎸夐挳锛屽垪琛ㄨ摑鑹查摼鎺ヨ烦杞?| 瀹屾垚 |

### 娑夊強鏂囦欢

**鏂板锛?0 涓枃浠讹級**锛?- `app/Livewire/Finance/SupplierSettlementDetail.php`
- `app/Livewire/Finance/RechargeDetail.php`
- `app/Livewire/Finance/InvoiceDetail.php`
- `app/Livewire/Finance/MerchantAccountDetail.php`
- `app/Livewire/Loss/LossOrderDetail.php`
- `resources/views/livewire/finance/supplier-settlement-detail.blade.php`
- `resources/views/livewire/finance/recharge-detail.blade.php`
- `resources/views/livewire/finance/invoice-detail.blade.php`
- `resources/views/livewire/finance/merchant-account-detail.blade.php`
- `resources/views/livewire/loss/loss-order-detail.blade.php`

**淇敼锛?0 涓枃浠讹級**锛?- `app/Livewire/Finance/SupplierSettlementList.php` 鈥?绉婚櫎璇︽儏寮圭獥閫昏緫
- `app/Livewire/Finance/RechargeList.php` 鈥?绉婚櫎璇︽儏寮圭獥閫昏緫
- `app/Livewire/Finance/InvoiceList.php` 鈥?绉婚櫎璇︽儏寮圭獥閫昏緫锛宨ssued_at 鏃ユ湡鏍煎紡鍖?- `app/Livewire/Finance/MerchantAccountList.php` 鈥?绉婚櫎璇︽儏寮圭獥閫昏緫
- `resources/views/livewire/finance/supplier-settlement-list.blade.php` 鈥?寮圭獥鈫掕矾鐢遍摼鎺?- `resources/views/livewire/finance/recharge-list.blade.php` 鈥?寮圭獥鈫掕矾鐢遍摼鎺?- `resources/views/livewire/finance/invoice-list.blade.php` 鈥?寮圭獥鈫掕矾鐢遍摼鎺?- `resources/views/livewire/finance/merchant-account-list.blade.php` 鈥?寮圭獥鈫掕矾鐢遍摼鎺?- `resources/views/livewire/loss/loss-order-list.blade.php` 鈥?鎹熻€楀崟鍙锋敼涓鸿摑鑹查摼鎺?- `routes/web.php` 鈥?鏂板 5 涓鎯呰矾鐢?+ use 寮曠敤

---

## 2026-08-26锛堝樊寮傚鐞嗗彴鏀归€狅級

### 宸插畬鎴愪换鍔?
| 搴忓彿 | 鍔熻兘妯″潡 | 浠诲姟鍐呭 | 鐘舵€?|
| :--- | :--- | :--- | :--- |
| 1 | 宸紓澶勭悊鍙?| 鍥炲簱寮圭獥鍏抽棴鎸夐挳缁熶竴涓?`closeRestockModal()` 鏂规硶锛屾竻鐞?restockId/restockRemark | 鉁?|
| 2 | 宸紓澶勭悊鍙?| DiscrepancyService 鏂板 `markProcessing()` 鐘舵€佹祦杞柟娉曪紙寰呭鐞嗏啋澶勭悊涓級锛屽惈瀹¤鏃ュ織 | 鉁?|
| 3 | 宸紓澶勭悊鍙?| DiscrepancyService 鏂板 `markDisputed()` 鐘舵€佹祦杞柟娉曪紙寰呭鐞?澶勭悊涓啋浜夎涓級锛屽惈瀹¤鏃ュ織 | 鉁?|
| 4 | 宸紓澶勭悊鍙?| 鍒楄〃椤垫柊澧炪€屾爣璁板鐞嗕腑銆嶆寜閽紙浠呭緟澶勭悊鐘舵€?鏈夊鐞嗘潈闄愭椂鏄剧ず锛?| 鉁?|
| 5 | 宸紓澶勭悊鍙?| 鍒楄〃椤垫柊澧炪€岃浆鍏ヤ簤璁€嶆寜閽紙寰呭鐞?澶勭悊涓姸鎬佹樉绀猴級+ 璇︽儏椤典簤璁脊绐楋紙鍚娉ㄨ緭鍏ワ級 | 鉁?|
| 6 | 宸紓澶勭悊鍙?| 鏂板缓 `DiscrepancyDetail` 璇︽儏椤电粍浠?瑙嗗浘+璺敱 `/discrepancies/{id}`锛屽惈宸紓淇℃伅/澶勭悊淇℃伅鍗＄墖 | 鉁?|
| 7 | 宸紓澶勭悊鍙?| 鍒楄〃宸紓缂栧彿鍒楁敼涓鸿摑鑹查摼鎺ワ紙`text-blue-600 hover:underline`锛夎繘鍏ヨ鎯呴〉 | 鉁?|
| 8 | 宸紓澶勭悊鍙?| Discrepancy 妯″瀷鏂板 `STAGE_PURCHASE(4)` 甯搁噺 + stageMap 澧炲姞銆屽叆搴撱€? `purchaseOrderItem` 鍏宠仈 | 鉁?|
| 9 | 宸紓澶勭悊鍙?| discrepancies 琛?create 杩佺Щ鍚堝苟 `purchase_order_item_id` 瀛楁 + 绱㈠紩 + stage 娉ㄩ噴鏇存柊 | 鉁?|
| 10 | 宸紓澶勭悊鍙?| DiscrepancyService::create() 鏀寔閲囪喘鍏ュ簱鐜妭锛堥噸澶嶆鏌?鎴愭湰浠?sku 鍥炲～锛?| 鉁?|
| 11 | 宸紓澶勭悊鍙?| PurchaseService::stockIn() 鍏ュ簱宸紓鏃惰嚜鍔ㄨ皟鐢?DiscrepancyService 鐧昏宸紓鍗?| 鉁?|
| 12 | 宸紓澶勭悊鍙?| 鍒楄〃椤?stageMap 鍜岀瓫閫夊櫒澧炲姞銆屽叆搴撱€嶇幆鑺?| 鉁?|
| 13 | 鏁版嵁搴?| 鎵ц `admin:install --seed` 閲嶅缓鏁版嵁搴擄紙create 杩佺Щ鍚堝苟瀛楁锛?| 鉁?|
| 14 | 楠岃瘉 | PHP 璇硶妫€鏌ワ紙7 涓枃浠跺叏閮ㄩ€氳繃锛? view:clear + UTF-8 鏃?BOM 妫€鏌ワ紙9 涓枃浠跺叏閮ㄩ€氳繃锛?| 鉁?|

### 鏁版嵁搴撳彉鏇?
| 鍙樻洿 | 琛ㄥ悕 | 瀛楁 | 璇存槑 |
| :--- | :--- | :--- | :--- |
| 鏂板瀛楁 | discrepancies | purchase_order_item_id | 鍏宠仈閲囪喘鍗曟槑缁咺D锛堝叆搴撶幆鑺傦級 |
| 鏂板绱㈠紩 | discrepancies | idx_purchase_order_item_id | 閲囪喘鏄庣粏鏌ヨ鍔犻€?|
| 淇敼娉ㄩ噴 | discrepancies | stage | 宸紓鐜妭澧炲姞 4=鍏ュ簱 |
| 鏂板甯搁噺 | Discrepancy | STAGE_PURCHASE = 4 | 鍏ュ簱鐜妭甯搁噺 |

### 鏂板鏂囦欢

| 鏂囦欢 | 璇存槑 |
| :--- | :--- |
| app/Livewire/Delivery/DiscrepancyDetail.php | 宸紓鍗曡鎯呴〉缁勪欢锛堝鐞?鍥炲簱/鐘舵€佹祦杞搷浣滐級 |
| resources/views/livewire/delivery/discrepancy-detail.blade.php | 宸紓鍗曡鎯呴〉瑙嗗浘 |

### 寰呮祴璇?/ 寰呭姙

| 搴忓彿 | 鍐呭 | 鐘舵€?|
| :--- | :--- | :--- |
| 1 | 娴忚鍣ㄦ祴璇曪細鍒楄〃椤点€屾爣璁板鐞嗕腑銆嶆寜閽祦杞?| 鈴?寰呮祴璇?|
| 2 | 娴忚鍣ㄦ祴璇曪細鍒楄〃椤点€岃浆鍏ヤ簤璁€嶆寜閽?寮圭獥娴佽浆 | 鈴?寰呮祴璇?|
| 3 | 娴忚鍣ㄦ祴璇曪細宸紓缂栧彿钃濊壊閾炬帴杩涘叆璇︽儏椤?| 鈴?寰呮祴璇?|
| 4 | 娴忚鍣ㄦ祴璇曪細璇︽儏椤靛鐞?鍥炲簱/鐘舵€佹祦杞搷浣?| 鈴?寰呮祴璇?|
| 5 | 娴忚鍣ㄦ祴璇曪細閲囪喘鍏ュ簱鍚庢槸鍚﹁嚜鍔ㄧ敓鎴愬樊寮傚崟锛堝樊寮?0 鏃讹級 | 鈴?寰呮祴璇?|
| 6 | 娴嬭瘯閫氳繃鍚庡悎骞?`feature/discrepancy-revamp` 鈫?`main` | 鈴?寰呭悎骞?|

### 浜屾湡寰呭姙锛堟湰娆′笉鍋氾級

| 搴忓彿 | 鍐呭 | 璇存槑 |
| :--- | :--- | :--- |
| 1 | 琛ヨ揣鍐崇瓥鐢熸垚琛ヨ揣鍗?| 褰撳墠浠呯櫥璁板喅绛栵紝琛ヨ揣鍗曠敓鎴愬緟浜屾湡 |
| 2 | 閫€娆?鎵ｆ閲戦璋冩暣 | 褰撳墠浠呯櫥璁板喅绛栵紝閲戦璋冩暣寰呰储鍔′簩鏈熸墽琛?|
| 3 | 鍥炲簱瀹℃牳闂搁棬 | 褰撳墠鍥炲簱鎿嶄綔浜哄嵆浠撶纭锛屼簩鏈熷彲鍔犲鏍稿紑鍏?|
| 4 | 浠樻瀹℃牳娴?| approval_status 瀛楁宸蹭繚鐣欙紝浜屾湡鎺ュ叆瀹℃牳绠＄悊 |

---

## 2026-08-24锛堝簲鏀惰仈鍔?+ 鍒楄〃椤甸€氱敤瑙勮寖 + 搴撳瓨姹囨€?+ 宸紓鏂囨。锛?
### 宸插畬鎴愪换鍔?
| 搴忓彿 | 鍔熻兘妯″潡 | 浠诲姟鍐呭 | 鐘舵€?|
| :--- | :--- | :--- | :--- |
| 1 | 搴旀敹绠＄悊 | 搴旀敹鍒楄〃鍗曞彿鏀逛负钃濊壊閾炬帴杩涘叆璇︽儏椤碉紝绉婚櫎鎿嶄綔鍒楄鎯呭叆鍙?| 鉁?|
| 2 | 瀹㈡埛璐︽埛 | 瀹㈡埛璐︽埛鍒楄〃鍟嗗鍒楁敼涓鸿摑鑹查摼鎺?+ 鏂板寮圭獥鍟嗗鏀规悳绱㈣緭鍏ワ紙debounce 300ms锛? 璇︽儏寮圭獥琛ュ喕缁撻噾棰?| 鉁?|
| 3 | 鍏呭€肩鐞?| 瀹㈡埛鍏呭€间氦鏄撳彿钃濊壊閾炬帴 + 鍏抽棴鎸夐挳淇 | 鉁?|
| 4 | 鍙戠エ绠＄悊 | 鍙戠エ鍙疯摑鑹查摼鎺?+ 鍏抽棴鎸夐挳淇 | 鉁?|
| 5 | 绛炬敹绠＄悊 | signatures 鏂板缓杩佺Щ琛ュ瓧娈碉紙status/signer_type 绛夛級 | 鉁?|
| 6 | 瀹炴椂搴撳瓨 | 鏂板鎸?SKU 姹囨€昏鍥撅紙褰撳墠椤垫眹鎬?/ 鎵€鏈夐〉姹囨€伙級锛屽簳閮?Tab 鍒囨崲 | 鉁?|
| 7 | 瀹炴椂搴撳瓨 | 淇 SKU 姹囨€?GROUP BY 鏌ヨ only_full_group_by 鎶ラ敊锛堝姞 `->reorder()` 娓呮帀缁ф壙鐨?ORDER BY锛?| 鉁?|
| 8 | .gitignore | 鏂板 AI 宸ュ叿鏈湴缂撳瓨蹇界暐瑙勫垯 + PNG 渚嬪鎵╁ぇ + `*.log` 閫氱敤鏃ュ織蹇界暐 | 鉁?|
| 9 | 鐢ㄦ埛鎵嬪唽 | 4.9.4 宸紓澶勭悊绔犺妭閲嶅啓锛堝叆鍙ｈ〃/绫诲瀷琛?鍐崇瓥鑱斿姩琛?鐘舵€佹祦杞浘/瀹℃牳鑺傜偣琛?鏉冮檺鐭╅樀锛?| 鉁?|
| 10 | 闀挎湡璁板繂 | MEMORY.md 鏇存柊寮€鍙戝崗浣滆鑼冿紙.gitignore 缁存姢 + commit message 涓嶆彁鍙?AIGC 娓呯悊锛?| 鉁?|
| 11 | AIGC 娓呯悊 | 娓呴櫎 README.md銆乨ocs/02_FSD銆乨ocs/12_UserGuide 鐨?AIGC front matter + skip-worktree 闃查噸鏂版敞鍏?| 鉁?|

---

## 2026-08-22锛堝樊寮備竴鏈熷姞鍥?+ 渚涘簲鍟嗙粨绠?+ 閫€璐у弻妯″紡 + 鐘舵€侀攣瀹氾級

### 宸插畬鎴愪换鍔?
| 搴忓彿 | 鍔熻兘妯″潡 | 浠诲姟鍐呭 | 鐘舵€?|
| :--- | :--- | :--- | :--- |
| 1 | 宸紓涓€鏈?| 宸紓鍗曠櫥璁板叆鍙ｏ紙鎷ｈ揣/瀹炴敹锛? 澶勭悊鍙帮紙璐ｄ换鏂?鍐崇瓥/鎶ユ崯鑱斿姩鎹熻€楀崟/鎷掓敹鍥炲簱锛?| 鉁?|
| 2 | 宸紓涓€鏈?| 宸紓鍗?8 鍏宠仈瀛楁 + 鎹熻€楁槑缁嗘函婧?+ 搴撳瓨鏃ュ織绫诲瀷 7/8/9锛堟姤鎹?鎷掓敹/鍞悗閫€锛?| 鉁?|
| 3 | 宸紓涓€鏈?| 宸紓娴佺▼寮€鍏冲寲 + 鍏佸樊闃堝€硷紙鍥涚幆鑺傜嫭绔嬬鎺э級涓庡樊寮傚氨杩戝彲瑙?| 鉁?|
| 4 | 宸紓涓€鏈?| 閲囪喘鍗曞垪琛ㄥ鍔犲叆搴撳樊寮傝绀哄浘鏍囷紙鍒嗚壊锛?| 鉁?|
| 5 | 渚涘簲鍟嗙粨绠?| 閲嶆瀯涓哄懆鏈熺粨绠楁ā寮?+ 璐﹀姟鎸佸钩鑱斿姩 | 鉁?|
| 6 | 渚涘簲鍟嗙粨绠?| 缁撶畻璇︽儏寮圭獥鍏抽棴澶辨晥淇 + 缁撶畻鍗曞彿閾炬帴鍖?+ 婕旂ず鏁版嵁瀵归綈 | 鉁?|
| 7 | 閲囪喘閫€璐?| 閲囪喘閫€璐у弻妯″紡锛堝叧鑱旈€€璐?+ 鏅€氶€€璐э級 | 鉁?|
| 8 | 閲囪喘閫€璐?| 澶村厛琛屽缓鍗?+ 璇︽儏椤电淮鎶ゆ槑缁?| 鉁?|
| 9 | 鍞悗閫€璐?| 鍞悗閫€璐у榻愬ご鍏堣 + 璇︽儏椤垫槑缁嗘ā寮?| 鉁?|
| 10 | 鍞悗閫€璐?| 閫€璐у師鍥犻潪蹇呭～ + 鍏宠仈閫€璐у嚭搴撲粨搴撳彲鏀逛粨 | 鉁?|
| 11 | 鍗曟嵁鐘舵€侀攣瀹?| 鎻愪氦鍚庝笉鍙紪杈戯紝闇€绠＄悊鍛?瓒呯鍥為€€鍚庢墠鍙慨鏀?| 鉁?|
| 12 | 閲囪喘宸紓鎹熻€?| 閲囪喘宸紓鎹熻€楀崟浠呰处鍔＄暀鐥曚笉鎵ｅ簱瀛?| 鉁?|

---

## 2026-08-20 ~ 21锛堥€氱煡绯荤粺 + 骞挎挱鍒囨崲 + 鏉冮檺鏀剁揣 + 绉嶅瓙淇锛?
### 宸插畬鎴愪换鍔?
| 搴忓彿 | 鍔熻兘妯″潡 | 浠诲姟鍐呭 | 鐘舵€?|
| :--- | :--- | :--- | :--- |
| 1 | 骞挎挱绯荤粺 | 骞挎挱椹卞姩浠?Reverb 鍒囨崲涓?Pusher锛坅p1 闆嗙兢 + TLS锛?| 鉁?|
| 2 | 閫氱煡绯荤粺 | Drawer 鍗曟潯闅愯棌/鏍囪鏈 + 鍘嗗彶閫氱煡椤碉紙鍏ㄩ噺绠＄悊/鍒犻櫎/鎭㈠鏄剧ず锛?| 鉁?|
| 3 | 鍗曟嵁闂ㄦ帶 | 鍗曟嵁缂栬緫/鍒犻櫎闂ㄦ帶鏀剁揣 鈥?浠呭垵濮嬬姸鎬佸彲鏀癸紝瓒呯鍥為€€鍚庢柟鍙紪杈?| 鉁?|
| 4 | 鍏呭€煎鏍?| 鍏呭€煎鏍哥‘璁ゅ叆璐?merchant_accounts + 杩佺Щ绱㈠紩淇 | 鉁?|
| 5 | 绉嶅瓙鏁版嵁 | SKU 绉嶅瓙琛ュ厖 base_unit_id + 鍗曚綅鎹㈢畻 + ops_manager 鏉冮檺鏀逛负鍏ㄤ笟鍔℃帓闄?delete/cancel + 琛ュ厖 5 涓綔搴熸潈闄?| 鉁?|
| 6 | 鏂囨。娓呯悊 | 娓呴櫎鍏ㄩ儴鏂囨。 AIGC 鐥曡抗锛圷AML front matter + AI 鐢熸垚姘村嵃锛?| 鉁?|
| 7 | 鏂囨。鏇存柊 | .env.example 鏍囨敞蹇呭～/鍙€?+ 09 鏂板閫氱煡绯荤粺寮€鍙戣鑼?+ 12 鏂板閫氱煡瑙﹀彂鍦烘櫙琛?| 鉁?|