---
AIGC:
  ContentProducer: '001191110102MAD55U9H0F10002'
  ContentPropagator: '001191110102MAD55U9H0F10002'
  Label: '1'
  ProduceID: '5f33a0e5-ec6f-4dd2-bdb3-5e58d1f2ebd9'
  PropagateID: '5f33a0e5-ec6f-4dd2-bdb3-5e58d1f2ebd9'
  ReservedCode1: '24afb12b-6568-4132-b1ed-383ef20e9c81'
  ReservedCode2: '24afb12b-6568-4132-b1ed-383ef20e9c81'
---

AIGC:
  ContentProducer: '001191110102MAD55U9H0F10002'
  ContentPropagator: '001191110102MAD55U9H0F10002'
  Label: '1'
  ProduceID: '0d91c2c2-1b9f-4d7b-b899-385169ebb1a9'
  PropagateID: '0d91c2c2-1b9f-4d7b-b899-385169ebb1a9'
  ReservedCode1: 'b086fd96-cf26-4f04-93ff-3519477417c3'
  ReservedCode2: 'b086fd96-cf26-4f04-93ff-3519477417c3'
---

# 寮€鍙戣凯浠ｆ棩蹇?

瀵瑰簲 PRD 鐗堟湰锛歏1.2
瀵瑰簲 FSD 鐗堟湰锛歏1.2
鎶€鏈爤锛歀aravel 13 + Livewire 4.x + Tailwind CSS 4.2+ + Alpine.js + PHP 8.4+ + MySQL 8.0 + Redis 7.x

璁板綍瑙勫垯锛氭瘡娆¤凯浠ｆ柊澧炰竴鑺傦紝鎸夌増鏈彿鍊掑簭鎺掑垪銆傛瘡鏉″彉鏇撮渶鏍囨敞寮€鍙戜汉銆佸畬鎴愭椂闂村拰鍏宠仈妯″潡銆?

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

璐熻矗浜猴細椤圭洰璐熻矗浜?
鍙備笌寮€鍙戜汉鍛橈細鍚庣寮€鍙?

### 1 鏈鏂板鍔熻兘娓呭崟

| 搴忓彿 | 鍔熻兘妯″潡 | 鍔熻兘鐐?| 寮€鍙戜汉 | 瀹屾垚鏃堕棿 | 鐘舵€?|
| :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | 閫氱煡绠＄悊 | Notification 妯″瀷鏂板 broadcastToAll() / sendToUser() 闈欐€佹柟娉曪紝澶嶇敤 NotificationCreated 浜嬩欢瀹炴椂鎺ㄩ€?| 鍚庣 | 2026-08-28 | 鉁?|
| 2 | 閫氱煡绠＄悊 | NotificationList Livewire 缁勪欢锛氬垪琛ㄥ睍绀?鎼滅储绛涢€?鍙戦€侀€氱煡寮圭獥+鍒犻櫎鍔熻兘 | 鍚庣 | 2026-08-28 | 鉁?|
| 3 | 閫氱煡绠＄悊 | 鑿滃崟/璺敱/鏉冮檺瀹屾暣鎺ュ叆锛歴ystem 鍒嗙粍鏂板鑿滃崟椤广€佽矾鐢辨敞鍐屻€佹潈闄愭爲瀹氫箟銆佽矾鐢辨潈闄愭槧灏?| 鍚庣 | 2026-08-28 | 鉁?|

### 2 鏂板鏂囦欢

| 鏂囦欢 | 璇存槑 |
| :--- | :--- |
| app/Livewire/System/NotificationList.php | 閫氱煡绠＄悊鍒楄〃缁勪欢 |
| resources/views/livewire/system/notification-list.blade.php | 閫氱煡绠＄悊鍒楄〃椤?寮圭獥瑙嗗浘 |

### 3 淇敼鏂囦欢

| 鏂囦欢 | 璇存槑 |
| :--- | :--- |
| app/Models/Notification.php | 鏂板 broadcastToAll() / sendToUser()锛屽紩鍏?Log facade |
| config/menu.php | system 鍒嗙粍鏂板"閫氱煡绠＄悊"鑿滃崟椤?|
| routes/web.php | 鏂板 NotificationList use + /system-notifications 璺敱 |
| app/Http/Middleware/CheckPermission.php | ROUTE_PERMISSION_MAP 鏂板鏄犲皠 |
| database/seeders/SystemDataSeeder.php | 鏉冮檺鏍戞柊澧?system.notification + 鎸夐挳鏉冮檺 + operator 瑙掕壊鍒嗛厤 |

---

## V1.9.1 | 杩唬鍛ㄦ湡锛?026-08-03

璐熻矗浜猴細椤圭洰璐熻矗浜?
鍙備笌寮€鍙戜汉鍛橈細鍚庣寮€鍙?

### 1 鏈鏂板鍔熻兘娓呭崟

| 搴忓彿 | 鍔熻兘妯″潡 | 鍔熻兘鐐?| 寮€鍙戜汉 | 瀹屾垚鏃堕棿 | 鐘舵€?|
| :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | 鏂摼淇 | PurchaseReturnService锛氶€€璐у崟瀹屾暣鐢熷懡鍛ㄦ湡锛堝垱寤衡啋瀹℃牳鈫掑嚭搴撯啋瀹屾垚/鍙栨秷锛?| 鍚庣 | 2026-08-03 | 鉁?|
| 2 | 鏂摼淇 | 閫€璐у嚭搴撹仈鍔?InventoryService::stockOut() 鎵ｅ噺搴撳瓨 | 鍚庣 | 2026-08-03 | 鉁?|
| 3 | 鏂摼淇 | LossOrderService锛氭崯鑰楀崟瀹屾暣鐢熷懡鍛ㄦ湡锛堝垱寤衡啋瀹℃牳鈫掓墽琛屸啋鍏抽棴/鍙栨秷锛?| 鍚庣 | 2026-08-03 | 鉁?|
| 4 | 鏂摼淇 | 鎹熻€楀崟鎵ц鏃惰皟鐢?InventoryService::stockOut() 鎵ｅ噺搴撳瓨 | 鍚庣 | 2026-08-03 | 鉁?|
| 5 | 鏂摼淇 | 鍏ュ簱宸紓鑷姩鍒涘缓鎹熻€楀崟锛歴tockIn() 璁＄畻 discrepancy_quantity锛岀郴缁熼厤缃紑鍏虫帶鍒?| 鍚庣 | 2026-08-03 | 鉁?|
| 6 | 鏂摼淇 | 宸插叆搴撻噰璐崟绂佹鐩存帴鍙栨秷锛屾彁绀鸿蛋閫€璐ф祦绋?| 鍚庣 | 2026-08-03 | 鉁?|
| 7 | 鏂摼淇 | 閲囪喘鍗曞畬鎴愬墠鏍￠獙锛氬瓨鍦ㄦ湭澶勭悊宸紓鏃堕樆姝㈠畬鎴?| 鍚庣 | 2026-08-03 | 鉁?|
| 8 | 鏂摼淇 | 閲囪喘鍗曢€€璐х姸鎬佽拷韪紙return_status锛?鏃犻€€璐?1閮ㄥ垎閫€璐?2鍏ㄩ儴閫€璐э級 | 鍚庣 | 2026-08-03 | 鉁?|

### 2 鏁版嵁搴撳彉鏇?

| 鍙樻洿 | 琛ㄥ悕 | 瀛楁 | 璇存槑 |
| :--- | :--- | :--- | :--- |
| 鏂板瀛楁 | purchase_orders | return_status | 閫€璐х姸鎬侊細0鏃犻€€璐э紝1閮ㄥ垎閫€璐э紝2鍏ㄩ儴閫€璐?|
| 鏂板瀛楁 | purchase_order_items | discrepancy_quantity | 鍏ュ簱宸紓鏁伴噺锛堥噰璐暟閲?瀹為檯鍏ュ簱鏁伴噺锛?|
| 鏂板瀛楁 | purchase_order_items | loss_order_id | 鍏宠仈鎹熻€楀崟ID |
| 鏂板瀛楁 | loss_orders | source_type | 鏉ユ簮绫诲瀷锛歱urchase_order=閲囪喘鍏ュ簱宸紓 |
| 鏂板瀛楁 | loss_orders | source_id | 鏉ユ簮涓氬姟ID |
| 鏂板瀛楁 | loss_order_items | purchase_order_item_id | 閲囪喘鍗曟槑缁咺D |
| 鏂板瀛楁 | loss_order_items | purchase_order_id | 閲囪喘鍗旾D |
| 鏂板閰嶇疆 | system_configs | stockin_auto_create_loss | 鍏ュ簱宸紓鑷姩鍒涘缓鎹熻€楀崟寮€鍏筹紙榛樿寮€鍚級 |

### 3 鏂板鏂囦欢

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
- **瀹屾垚鍓嶇疆鏍￠獙**锛歅urchaseService::complete() 妫€鏌ユ槸鍚︽湁鏈鐞嗙殑宸紓琛岋紙discrepancy_quantity > 0 涓?loss_order_id 涓虹┖锛?

### 5 寰呭鐞?

- **鏂摼3锛堜环鏍肩瓥鐣ヨ仈鍔級**锛氱瓑鏂摼1-2瀹屾垚鍚庣敤鎴峰啀娌熼€?
- **P2 闂**锛歮enu.php 琛?restock-reminders + temperatures 鑿滃崟椤癸紝Invoice Model 琛ュ叧绯诲畾涔?

---

## V1.9.0 | 杩唬鍛ㄦ湡锛?026-08-01

璐熻矗浜猴細椤圭洰璐熻矗浜?
鍙備笌寮€鍙戜汉鍛橈細鍚庣寮€鍙?

### 1 鏈鏂板鍔熻兘娓呭崟

| 搴忓彿 | 鍔熻兘妯″潡 | 鍔熻兘鐐?| 寮€鍙戜汉 | 瀹屾垚鏃堕棿 | 鐘舵€?|
| :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | 鏉冮檺浣撶郴 | 鏉冮檺鍚嶆敼涓轰笁绾ф牸寮忥細妯″潡.椤甸潰.鍔ㄤ綔锛堝 org.supplier.create锛?| 鍚庣 | 2026-08-01 | 鉁?|
| 2 | 鏉冮檺浣撶郴 | 姣忎釜椤甸潰绾ф潈闄愯嚜鍔ㄧ敓鎴?.view 椤甸潰璁块棶鏉冮檺锛堝 org.supplier.view锛?| 鍚庣 | 2026-08-01 | 鉁?|
| 3 | 鏉冮檺浣撶郴 | SystemDataSeeder 瀹屾暣閲嶅啓锛?2妯″潡 + 38椤甸潰 + 128鎸夐挳 = 178鏉℃潈闄?| 鍚庣 | 2026-08-01 | 鉁?|
| 4 | 鏉冮檺浣撶郴 | AppServiceProvider 娉ㄥ唽 Gate::before锛宻uper_admin 瑙掕壊鑷姩閫氳繃鎵€鏈?@can 妫€鏌?| 鍚庣 | 2026-08-01 | 鉁?|
| 5 | 鑿滃崟绯荤粺 | 鏂板 config/menu.php 鑿滃崟閰嶇疆鏂囦欢锛?妯″潡 + 鍏ㄩ儴瀛愯彍鍗曢」锛?| 鍚庣 | 2026-08-01 | 鉁?|
| 6 | 鑿滃崟绯荤粺 | 椤堕儴瀵艰埅鏀逛负鍔ㄦ€佹覆鏌擄細浠?config('menu') 璇诲彇 + @can 杩囨护涓嶅彲瑙佽彍鍗?| 鍚庣 | 2026-08-01 | 鉁?|
| 7 | 鎸夐挳鏉冮檺 | 鍏ㄩ儴鍒楄〃椤?Blade 娣诲姞 @can 鏉冮檺鎺у埗锛堢害 40 涓枃浠讹級 | 鍚庣 | 2026-08-01 | 鉁?|
| 8 | 璺敱淇濇姢 | CheckPermission 涓棿浠?ROUTE_PERMISSION_MAP 鏀逛负涓夌骇鏍煎紡 .view | 鍚庣 | 2026-08-01 | 鉁?|
| 9 | 璺敱淇濇姢 | dashboard 璺敱璁句负浠呴渶鐧诲綍锛坣ull锛夛紝涓嶅啀瑕佹眰鏉冮檺 | 鍚庣 | 2026-08-01 | 鉁?|
| 10 | 瀹夎鍛戒护 | AdminInstallCommand 鍦?migrate:fresh 鍚庢竻 Spatie 鏉冮檺缂撳瓨锛岄伩鍏?Seeder 鍐茬獊 | 鍚庣 | 2026-08-01 | 鉁?|

### 2 鏈浼樺寲/閲嶆瀯

| 搴忓彿 | 浼樺寲椤?| 璇存槑 | 瀹屾垚鏃堕棿 |
| :--- | :--- | :--- | :--- |
| 1 | SUPER_ADMIN_ONLY 鍒楄〃娓呯┖ | 涓嶅啀纭紪鐮佽秴绠′笓灞炶矾鐢憋紝鐢辨潈闄愯〃缁熶竴鎺у埗 | 2026-08-01 |

### 3 Bug 淇

| 搴忓彿 | Bug 鎻忚堪 | 淇鏂规 | 瀹屾垚鏃堕棿 |
| :--- | :--- | :--- | :--- |
| 1 | admin:fresh --seed 鎶?PermissionAlreadyExists | migrate:fresh 鍚?Spatie 缂撳瓨娈嬬暀锛屾坊鍔?forgetCachedPermissions() | 2026-08-01 |
| 2 | 杩愯惀涓撳憳鐧诲綍 403 | dashboard 鏉冮檺鏄犲皠鏀逛负 null锛堜粎闇€鐧诲綍锛夛紝绉婚櫎 SUPER_ADMIN_ONLY 纭紪鐮?| 2026-08-01 |

### 4 寰呬慨澶?Bug 娓呭崟

| # | 涓ラ噸搴?| 鎻忚堪 | 褰卞搷鑼冨洿 | 棰勮鏀瑰姩 |
| :--- | :--- | :--- | :--- | :--- |
| 1 | 浣?| ~~鏂板缓瑙掕壊/鐢ㄦ埛鏃?dashboard 鏉冮檺鏈粯璁ゅ嬀閫墌~ | 鎵€鏈夐潪瓒呯瑙掕壊 | 鉁?宸蹭慨澶嶏細SystemDataSeeder 缁欐墍鏈夎鑹插垎閰?dashboard + 鏂板缓瑙掕壊榛樿甯?dashboard |

### 5 楠岃瘉缁撴灉

- admin:fresh --seed --force 鍏ㄦ祦绋嬮€氳繃锛坢igrate + SystemDataSeeder + DemoDataSeeder锛?
- 瓒呯鐧诲綍锛氭墍鏈夎彍鍗曞彲瑙侊紝鎵€鏈夋寜閽彲瑙?
- 杩愯惀涓撳憳锛坥perator 瑙掕壊 + org/product 鏉冮檺锛夛細浠呭彲瑙佸晢鍝佺鐞嗗拰缁勭粐涓讳綋鑿滃崟锛屾棤鏉冮檺璺敱 403 鎷︽埅

---

## V1.8.0 | 杩唬鍛ㄦ湡锛?026-07-31

璐熻矗浜猴細椤圭洰璐熻矗浜?
鍙備笌寮€鍙戜汉鍛橈細鍚庣寮€鍙?

### 1 鏈鏂板鍔熻兘娓呭崟

| 搴忓彿 | 鍔熻兘妯″潡 | 鍔熻兘鐐?| 寮€鍙戜汉 | 瀹屾垚鏃堕棿 | 鐘舵€?|
| :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | 鏉冮檺涓棿浠?| CheckPermission 璺敱绾т腑闂翠欢锛氳矾鐢卞悕鈫掓潈闄愬悕鑷姩鏄犲皠锛宻uper_admin 鑷姩鏀捐 | 鍚庣 | 2026-07-31 | 鉁?|
| 2 | 鏉冮檺涓棿浠?| 403 鑷畾涔夐敊璇〉闈?+ Livewire JSON 鍝嶅簲澶勭悊 | 鍚庣 | 2026-07-31 | 鉁?|
| 3 | 鏉冮檺涓棿浠?| 瓒呯涓撳睘璺敱淇濇姢锛歳oles/permissions/approval-config/settings 浠?super_admin | 鍚庣 | 2026-07-31 | 鉁?|
| 4 | 鏉冮檺涓棿浠?| 鍏ㄩ儴涓氬姟璺敱搴旂敤 auth + permission 涓棿浠?| 鍚庣 | 2026-07-31 | 鉁?|
| 5 | 鏉冮檺涓棿浠?| 9 涓祴璇曡鑹插垎閰嶅熀纭€椤甸潰鏉冮檺 | 鍚庣 | 2026-07-31 | 鉁?|
| 6 | 搴撳瓨鏈嶅姟 | InventoryService 缁熶竴搴撳瓨鏈嶅姟锛歴tockIn / stockOut / adjust锛孌B::transaction + lockForUpdate | 鍚庣 | 2026-07-31 | 鉁?|
| 7 | 閲囪喘鏈嶅姟 | PurchaseService 閲囪喘鍗曞叏鐢熷懡鍛ㄦ湡锛歝reateFromItems / submit / ship / stockIn / complete / cancel | 鍚庣 | 2026-07-31 | 鉁?|
| 8 | 閲囪喘鏈嶅姟 | 鍏ュ簱鎿嶄綔鑱斿姩 InventoryService.stockIn()锛屽樊寮傝璁板綍 discrepancy_reason | 鍚庣 | 2026-07-31 | 鉁?|
| 9 | 閲囪喘鍗曡鎯呴〉 | PurchaseOrderDetail 缁勪欢锛氬熀鏈俊鎭崱鐗?+ 鏄庣粏鍒楄〃 + 鐘舵€佹祦杞寜閽?| 鍚庣 | 2026-07-31 | 鉁?|
| 10 | 閲囪喘鍗曡鎯呴〉 | 娣诲姞/鍒犻櫎鏄庣粏寮圭獥 | 鍚庣 | 2026-07-31 | 鉁?|
| 11 | 閲囪喘鍗曡鎯呴〉 | 鍏ュ簱鎿嶄綔寮圭獥锛氶€夋嫨浠撳簱 + 鎵规鍙?+ 閫愯濉啓瀹為檯鏁伴噺/閲戦/宸紓鍘熷洜 | 鍚庣 | 2026-07-31 | 鉁?|
| 12 | 閲囪喘鍗曡鎯呴〉 | 鐘舵€佺‘璁ゅ脊绐楋紙鎻愪氦/鍙戣揣/瀹屾垚/鍙栨秷锛?| 鍚庣 | 2026-07-31 | 鉁?|
| 13 | 閲囪喘鍗曞垪琛?| 鍗曞彿鍙偣鍑昏烦杞鎯呴〉锛岄噾棰濅娇鐢?money_format | 鍚庣 | 2026-07-31 | 鉁?|
| 14 | 寰呴噰娓呭崟 | 鏂板"鎵归噺鐢熸垚閲囪喘鍗?鎸夐挳 + 纭寮圭獥锛岃皟鐢?PurchaseService::createFromItems() | 鍚庣 | 2026-07-31 | 鉁?|
| 15 | 鏁版嵁搴?| purchase_orders 鏂板瀛楁锛歸arehouse_id / operator_id / ordered_at / shipped_at / stocked_at | 鍚庣 | 2026-07-31 | 鉁?|
| 16 | 瀵煎叆瀵煎嚭 | WithExcelExport 鍛藉悕鍙傛暟 bug 淇锛歲uery: 鈫?queryOrData:锛屼笌 GenericExport 鏋勯€犲嚱鏁板榻?| 鍚庣 | 2026-07-31 | 鉁?|
| 17 | 瀵煎叆瀵煎嚭 | downloadImportTemplate 鏀圭敤 getImportColumnMap() 鏍囩鐢熸垚妯℃澘锛屼笉鍐嶇敤 getExportableColumns() | 鍚庣 | 2026-07-31 | 鉁?|
| 18 | 瀵煎叆瀵煎嚭 | GenericExport 閲戦 梅100 bug 淇锛氬簾寮冩鍒欑寽娴嬶紝鏀圭敤鍒楀畾涔?type=money 鏄惧紡澹版槑锛屆?000 | 鍚庣 | 2026-07-31 | 鉁?|
| 19 | 瀵煎叆瀵煎嚭 | 3 涓菇鐏?閿欏悕瀛楁淇锛欿eywordList word鈫択eyword锛孲kuList price鈫抪urchase_price+wholesale_price锛孲kuBarcodeList barcode鈫抌arcode_code | 鍚庣 | 2026-07-31 | 鉁?|
| 20 | 瀵煎叆瀵煎嚭 | 閲戦鍒楄ˉ type=money 鏍囪锛歋kuList / SkuSupplierList / PurchaseOrderList | 鍚庣 | 2026-07-31 | 鉁?|
| 21 | 鍒楅厤缃?| WithColumnVisibility 琛?openColumnModal() 鏂规硶 | 鍚庣 | 2026-07-31 | 鉁?|
| 22 | 鍒楅厤缃?| Blade 妯℃澘鍒楁覆鏌撲粠纭紪鐮佹敼涓?getAllColumns() 鍔ㄦ€佽幏鍙?| 鍚庣 | 2026-07-31 | 鉁?|
| 23 | 鍒楅厤缃?| 鍒楅厤缃脊绐楁斁澶?max-w-lg + 鍙充笂瑙?X 鍏抽棴鎸夐挳锛涘鍑哄脊绐楁斁澶?max-w-lg | 鍚庣 | 2026-07-31 | 鉁?|
| 24 | 鐢ㄦ埛鍋忓ソ | 鏂板 user_preferences 琛?+ UserPreference Model锛屽垪閰嶇疆浠?session 杩佺Щ鍒?DB 鎸佷箙鍖栵紙鐢ㄦ埛闅旂锛?| 鍚庣 | 2026-07-31 | 鉁?|
| 25 | 缁勭粐涓讳綋 | drivers 琛ㄦ柊澧?remark 瀛楁 + Driver Model fillable 琛ュ厖 | 鍚庣 | 2026-07-31 | 鉁?|
| 26 | 缁勭粐涓讳綋 | OrganizationDemoSeeder 琛ュ叏娴嬭瘯鏁版嵁锛坰uppliers 琛?bank/settlement_cycle/remark锛沵erchants 琛?delivery_route_id/credit_limit/remark锛沝rivers 琛?id_card/remark锛?| 鍚庣 | 2026-07-31 | 鉁?|
| 27 | 瀵煎叆瀵煎嚭 | SupplierList 琛ュ厖 bank_name/bank_account 鍒板垪瀹氫箟銆侀粯璁ゅ垪銆佸鍑哄洖璋?| 鍚庣 | 2026-07-31 | 鉁?|

### 2 鏈浼樺寲/閲嶆瀯

| 搴忓彿 | 妯″潡 | 浼樺寲鍐呭 | 寮€鍙戜汉 | 瀹屾垚鏃堕棿 |
| :--- | :--- | :--- | :--- | :--- |
| 1 | PurchaseOrder Model | 鏂板鍏宠仈锛歸arehouse() / operator()锛屾柊澧炴柟娉曪細generateOrderNo() / canTransitionTo() / recalculateAmounts() | 鍚庣 | 2026-07-31 |
| 2 | PurchaseOrderList | 鍒涘缓鏃惰嚜鍔ㄥ～鍏?operator_id / ordered_at锛屼娇鐢?Model 鐨?generateOrderNo() | 鍚庣 | 2026-07-31 |
| 3 | WithColumnVisibility | 鍒楅厤缃瓨鍌ㄤ粠 session 杩佺Щ鍒?DB锛坲ser_preferences 琛級锛屾湭鐧诲綍鐢ㄦ埛闄嶇骇 session | 鍚庣 | 2026-07-31 |
| 4 | GenericExport | 閲戦杞崲搴熷純姝ｅ垯鐚滄祴锛屾敼鐢ㄥ垪瀹氫箟 type=money 鏄惧紡澹版槑 | 鍚庣 | 2026-07-31 |

### 3 鏈淇 Bug

鏃犮€?

### 4 鏁版嵁搴撳彉鏇?

| Migration 鏂囦欢鍚?| 鎿嶄綔绫诲瀷 | 娑夊強琛?| 璇存槑 |
| :--- | :--- | :--- | :--- |
| 2026_07_31_000001_add_fields_to_purchase_orders | ALTER | purchase_orders | 鏂板 warehouse_id / operator_id / ordered_at / shipped_at / stocked_at 瀛楁 + 澶栭敭 |
| 2026_07_31_085412_create_user_preferences_table | CREATE | user_preferences | 鐢ㄦ埛鍋忓ソ琛紙user_id + pref_key 鍞竴绱㈠紩锛宲ref_value JSON锛?|

### 5 褰卞搷鑼冨洿

| 褰卞搷鏂囦欢 | 鍙樻洿绫诲瀷 |
| :--- | :--- |
| app/Http/Middleware/CheckPermission.php | 鏂板锛堣矾鐢辨潈闄愪腑闂翠欢锛?|
| app/Services/InventoryService.php | 鏂板锛堢粺涓€搴撳瓨鏈嶅姟锛?|
| app/Services/PurchaseService.php | 鏂板锛堥噰璐崟鏈嶅姟锛?|
| app/Livewire/Purchase/PurchaseOrderDetail.php | 鏂板锛堥噰璐崟璇︽儏椤电粍浠讹級 |
| resources/views/livewire/purchase/purchase-order-detail.blade.php | 鏂板锛堣鎯呴〉瑙嗗浘锛?|
| resources/views/errors/403.blade.php | 鏂板锛?03 椤甸潰锛?|
| database/migrations/2026_07_31_000001_add_fields_to_purchase_orders.php | 鏂板锛圡igration锛?|
| database/migrations/2026_07_31_085412_create_user_preferences_table.php | 鏂板锛堢敤鎴峰亸濂借〃 Migration锛?|
| app/Models/UserPreference.php | 鏂板锛堢敤鎴峰亸濂?Model锛?|
| bootstrap/app.php | 淇敼锛堜腑闂翠欢娉ㄥ唽 + AuthorizationException 娓叉煋锛?|
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
| resources/views/livewire/purchase/purchase-item-list.blade.php | 淇敼锛堟壒閲忕敓鎴愭寜閽?寮圭獥锛?|
| resources/views/partials/column-modal.blade.php | 淇敼锛坢ax-w-lg + X 鍏抽棴鎸夐挳锛?|
| resources/views/partials/export-modal.blade.php | 淇敼锛坢ax-w-lg锛?|
| database/seeders/Demo/OrganizationDemoSeeder.php | 淇敼锛堣ˉ鍏?suppliers/merchants/drivers 娴嬭瘯鏁版嵁锛?|

### 6 鍏抽敭鎶€鏈褰?

**閲囪喘鍗曠姸鎬佹祦杞璁★細**
- 鐘舵€佹満锛?寰呮帴鍗曗啋2澶囪揣涓啋3宸插彂璐р啋4宸插叆搴撯啋5瀹屾垚锛?鍙栨秷
- canTransitionTo() 鏂规硶鎺у埗鍚堟硶娴佽浆璺緞锛屼笉鍏佽璺ㄧ姸鎬佽烦杞?
- 鍙栨秷鎿嶄綔浠呴檺寰呮帴鍗?澶囪揣涓?宸插彂璐х姸鎬?

**鍏ュ簱鎿嶄綔鏍稿績娴佺▼锛?*
- 閲囪喘鍗曡鎯呴〉 鈫?鍏ュ簱寮圭獥 鈫?閫夋嫨浠撳簱+鎵规鍙?鈫?閫愯濉啓瀹為檯鏁伴噺/閲戦 鈫?鎻愪氦
- PurchaseService.stockIn() 鍦?DB::transaction 鍐咃細鏇存柊鏄庣粏瀹為檯鍊?鈫?InventoryService.stockIn() 脳 N 鈫?閲嶇畻閲戦
- 宸紓琛岃嚜鍔ㄩ珮浜紙姗欒壊锛夛紝宸紓鍘熷洜鍙€夊～鍐?

**搴撳瓨鏈嶅姟璁捐鍘熷垯锛?*
- 鎵€鏈夊簱瀛樺彉鍔ㄥ繀椤婚€氳繃 InventoryService 鎵ц锛岀姝㈢洿鎺ユ搷浣?Inventory 妯″瀷
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

璐熻矗浜猴細椤圭洰璐熻矗浜?
鍙備笌寮€鍙戜汉鍛橈細鍚庣寮€鍙?

### 1 鏈鏂板鍔熻兘娓呭崟

| 搴忓彿 | 鍔熻兘妯″潡 | 鍔熻兘鐐?| 寮€鍙戜汉 | 瀹屾垚鏃堕棿 | 鐘舵€?|
| :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | Artisan 鍛戒护 | 鏂板 `admin:create-admin` 鍛戒护锛堟浛浠ｅ簾寮冪殑 `admin:make-user`锛夛紝鏀寔 `--name=` `--email=` `--password=` `--role=super_admin` `--guard=web` | 鍚庣 | 2026-07-31 | 鉁?|
| 2 | Artisan 鍛戒护 | 鏂板 `admin:create-user` 鍛戒护锛屾敮鎸?`--name=` `--email=` `--password=` `--role=` `--guard=web` `--active=1` | 鍚庣 | 2026-07-31 | 鉁?|
| 3 | Artisan 鍛戒护 | `admin:seed` 澧炲己锛歚--demo=MODULE` 鏀寔鍙€夋ā鍧楀弬鏁帮紙涓嶄紶=鍏ㄩ儴娴嬭瘯鏁版嵁锛屼紶鍊?鎸囧畾妯″潡锛夛紱鏂板 `--list` 閫夐」鍒楀嚭鍙敤 Seeder 妯″潡 | 鍚庣 | 2026-07-31 | 鉁?|
| 4 | Seeder 浣撶郴 | 鎸夋ā鍧楁媶鍒嗘祴璇曟暟鎹細DemoDataSeeder 鏀逛负鎬诲叆鍙ｏ紝鏂板 Demo/ 瀛愮洰褰?11 涓垎妯″潡 Seeder锛堝惈 TestUsersDemoSeeder锛?| 鍚庣 | 2026-07-31 | 鉁?|
| 5 | Seeder 浣撶郴 | SystemDataSeeder 涓嶅啀鍒涘缓 `superadmin` 娴嬭瘯鐢ㄦ埛锛屾敼涓虹粰 Migration 鍒涘缓鐨?`seeding` 璐︽埛鍒嗛厤 super_admin 瑙掕壊 + 鍏ㄩ儴 140 鏉冮檺 | 鍚庣 | 2026-07-31 | 鉁?|
| 6 | Seeder 浣撶郴 | 鍙屽叆鍙ｅ吋瀹癸細Seeder 鍚屾椂鏀寔 `php artisan db:seed` 鍜?`php artisan admin:seed` 璋冪敤 | 鍚庣 | 2026-07-31 | 鉁?|
| 7 | 鏂囨。 | 03_DB 鏂板绗?9 绔犮€孲eeder 浣撶郴璁捐銆嶏紙璁捐鍘熷垯/鐩綍缁撴瀯/鏍稿績vs娴嬭瘯瀵圭収/妯″潡渚濊禆/鍙屽叆鍙?妯″潡娓呭崟閫熸煡锛?| 鍚庣 | 2026-07-31 | 鉁?|
| 8 | 鏂囨。 | 05_Setup 鏇存柊绗?9/10/12 绔狅紙鍒濆鍖栨暟鎹垎灞傝鏄?鍛戒护閫熸煡琛ㄦ洿鏂?閮ㄧ讲妫€鏌ユ竻鍗曞瘑鐮佷慨姝ｏ級 | 鍚庣 | 2026-07-31 | 鉁?|
| 9 | 鏂囨。 | 06_Log 鏂板 V1.7.0 杩唬璁板綍 | 鍚庣 | 2026-07-31 | 鉁?|

### 2 鏈浼樺寲/閲嶆瀯

| 搴忓彿 | 妯″潡 | 浼樺寲鍐呭 | 寮€鍙戜汉 | 瀹屾垚鏃堕棿 |
| :--- | :--- | :--- | :--- | :--- |
| 1 | Artisan 鍛戒护 | 搴熷純 `admin:make-user`锛岀粺涓€鐢?`admin:create-admin` + `admin:create-user` 鏇夸唬 | 鍚庣 | 2026-07-31 |
| 2 | Seeder 浣撶郴 | DemoDataSeeder 瑙ｈ€︼細绉婚櫎瀵?SystemDataSeeder 鐨勭‖璋冪敤锛屾祴璇曟暟鎹彲鐙珛杩愯 | 鍚庣 | 2026-07-31 |
| 3 | Seeder 浣撶郴 | 娴嬭瘯鏁版嵁鎸夋ā鍧楁媶鍒嗗埌 Demo/ 瀛愮洰褰曪紝鏀寔 `--demo=organization` 绛夊垎妯″潡鍗曠嫭杩愯 | 鍚庣 | 2026-07-31 |

### 3 鏈淇 Bug

鏃犮€?

### 4 鏁版嵁搴撳彉鏇?

鏃狅紙鏈鏈柊澧?Migration锛屼粎璋冩暣 Seeder 鍜屽懡浠ゅ眰锛夈€?

### 5 褰卞搷鑼冨洿

| 褰卞搷鏂囦欢 | 鍙樻洿绫诲瀷 |
| :--- | :--- |
| app/Console/Commands/AdminSeedCommand.php | 淇敼锛堟柊澧?--demo=MODULE / --list 閫夐」锛?|
| app/Console/Commands/AdminCreateAdminCommand.php | 鏂板锛堟浛浠?AdminMakeUserCommand锛?|
| app/Console/Commands/AdminCreateUserCommand.php | 淇敼锛堝寮洪€夐」锛?-role / --guard / --active锛?|
| app/Console/Commands/AdminMakeUserCommand.php | 搴熷純锛堟爣璁?@deprecated锛屾寚寮曞埌 admin:create-admin锛?|
| database/seeders/SystemDataSeeder.php | 閲嶅啓锛堝幓鎺?superadmin 鍒涘缓锛屾敼涓虹粰 seeding 鍒嗛厤瑙掕壊鏉冮檺锛?|
| database/seeders/DemoDataSeeder.php | 閲嶅啓锛堟敼涓烘€诲叆鍙ｏ紝璋冪敤 Demo/ 涓嬪悇鍒嗘ā鍧楋級 |
| database/seeders/Demo/OrganizationDemoSeeder.php | 鏂板锛堢粍缁囦富浣撴祴璇曟暟鎹級 |
| database/seeders/Demo/ProductDemoSeeder.php | 鏂板锛堝晢鍝佺鐞嗘祴璇曟暟鎹級 |
| database/seeders/Demo/PurchaseDemoSeeder.php | 鏂板锛堥噰璐鐞嗘祴璇曟暟鎹級 |
| database/seeders/Demo/OrderDemoSeeder.php | 鏂板锛堣鍗曠鐞嗘祴璇曟暟鎹級 |
| database/seeders/Demo/InventoryDemoSeeder.php | 鏂板锛堝簱瀛樼鐞嗘祴璇曟暟鎹級 |
| database/seeders/Demo/DeliveryDemoSeeder.php | 鏂板锛堥厤閫佺鐞嗘祴璇曟暟鎹級 |
| database/seeders/Demo/FinanceDemoSeeder.php | 鏂板锛堣储鍔″璐︽祴璇曟暟鎹級 |
| database/seeders/Demo/PriceDemoSeeder.php | 鏂板锛堜环鏍肩瓥鐣ユ祴璇曟暟鎹級 |
| database/seeders/Demo/LossDemoSeeder.php | 鏂板锛堟崯鑰楃鐞嗘祴璇曟暟鎹級 |
| database/seeders/Demo/SystemDemoSeeder.php | 鏂板锛堢郴缁熸敮鎾戞祴璇曟暟鎹級 |
| database/seeders/DatabaseSeeder.php | 淇敼锛堢‘淇濆弻鍏ュ彛鍏煎锛?|
| docs/03_DB_鏁版嵁搴撹璁?鏁版嵁瀛楀吀.md | 淇敼锛堟柊澧炵 9 绔?Seeder 浣撶郴璁捐锛?|
| docs/05_Setup_瀹夎閮ㄧ讲閰嶇疆鎵嬪唽.md | 淇敼锛堢 9/10/12 绔犳洿鏂帮級 |
| docs/06_Log_寮€鍙戣凯浠ｆ棩蹇?md | 淇敼锛堟柊澧?V1.7.0 杩唬璁板綍锛?|

### 6 鍏抽敭鎶€鏈褰?

**Seeder 鍒嗘ā鍧楄璁★細**
- 璁捐鍘熷垯锛氭牳蹇冩暟鎹紙SystemDataSeeder锛変笌娴嬭瘯鏁版嵁锛圖emoDataSeeder + Demo/*.php锛変弗鏍煎垎绂?
- 鏍稿績鏁版嵁鍖呭惈锛? 瑙掕壊 + 140 鏉冮檺鏍?+ seeding 璐︽埛瑙掕壊鍒嗛厤 + 19 瀹℃牳鑺傜偣閰嶇疆 + 24 鏉＄郴缁熼厤缃?
- 娴嬭瘯鏁版嵁鎸変笟鍔℃ā鍧楁媶鍒嗕负 10 涓嫭绔?Seeder锛屾斁鍦?`database/seeders/Demo/` 瀛愮洰褰?
- 妯″潡渚濊禆椤哄簭锛歰rganization 鈫?product 鈫?purchase 鈫?order 鈫?inventory 鈫?delivery 鈫?finance 鈫?price 鈫?loss 鈫?system-demo
- 鍙屽叆鍙ｅ吋瀹癸細`php artisan db:seed --class=DemoDataSeeder` 绛変环浜?`php artisan admin:seed --demo`
- `admin:seed --demo=organization` 鍙崟鐙繍琛屾寚瀹氭ā鍧楁祴璇曟暟鎹?
- `admin:seed --list` 鍒楀嚭鎵€鏈夊彲鐢?Seeder 妯″潡鍙婂叾璇存槑

**Migration vs Seeder 鑱岃矗鍒掑垎锛?*
- Migration 8.1锛氬垱寤洪粯璁ょ鐞嗗憳 `seeding` 璐︽埛 + 9 涓郴缁熻鑹?+ model_has_roles 鍏宠仈
- SystemDataSeeder锛氫负 `seeding` 璐︽埛鍒嗛厤 super_admin 瑙掕壊 + 鍏ㄩ儴 140 鏉冮檺锛坰yncPermissions锛?
- 鐢熶骇鐜鍙渶杩愯 `migrate` + `admin:seed --system`锛屽嵆鍙幏寰楀畬鏁村彲鐢ㄧ郴缁?

---

## V1.6.5 | 杩唬鍛ㄦ湡锛?026-07-30

璐熻矗浜猴細椤圭洰璐熻矗浜?
鍙備笌寮€鍙戜汉鍛橈細鍚庣寮€鍙戙€佸墠绔紑鍙?

### 1 鏈鏂板鍔熻兘娓呭崟

| 搴忓彿 | 鍔熻兘妯″潡 | 鍔熻兘鐐?| 寮€鍙戜汉 | 瀹屾垚鏃堕棿 | 鐘舵€?|
| :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | Artisan 鍛戒护 | 鏂板 `admin:seed` 鍛戒护锛屾敮鎸?`--fresh`/`--demo`/`--system`/`--force` 閫夐」锛屽彲鍗曠嫭杩愯绉嶅瓙鏁版嵁 | 鍚庣 | 2026-07-30 | 鉁?|

### 2 鏈浼樺寲/閲嶆瀯

| 搴忓彿 | 妯″潡 | 浼樺寲鍐呭 | 寮€鍙戜汉 | 瀹屾垚鏃堕棿 |
| :--- | :--- | :--- | :--- | :--- |
| 1 | 鏉冮檺鍒嗛厤寮圭獥 | 涓夋€?checkbox 褰诲簳閲嶆瀯锛氭潈闄愭爲鏁版嵁缂撳瓨鍒?`$permissionTreeData` 缁勪欢灞炴€э紝閬垮厤姣忔 toggle 鏌ユ暟鎹簱 | 鍚庣 | 2026-07-30 |
| 2 | 鏉冮檺鍒嗛厤寮圭獥 | 妯″潡/椤甸潰 checkbox 鏀逛负 `wire:click` + `@checked` + Alpine `x-effect` 椹卞姩 indeterminate 涓夋€?| 鍓嶇 | 2026-07-30 |
| 3 | 鏉冮檺鍒嗛厤寮圭獥 | 鎸夐挳绾?checkbox 鏀逛负 `wire:click="togglePermission(id)"` + `@checked`锛屽幓鎺?`wire:model.live` | 鍓嶇 | 2026-07-30 |
| 4 | Git 绠＄悊 | `database/database.sqlite` 鍔犲叆鐗堟湰鎺у埗 | 鍚庣 | 2026-07-30 |
| 5 | Composer | 娓呯悊 `composer.json` 鍐椾綑鑴氭湰 | 鍚庣 | 2026-07-30 |

### 3 鏈淇 Bug

| 搴忓彿 | Bug鎻忚堪 | 淇鍐呭 | 寮€鍙戜汉 | 瀹屾垚鏃堕棿 |
| :--- | :--- | :--- | :--- | :--- |
| 1 | 鏉冮檺鍒嗛厤寮圭獥妯″潡/椤甸潰 checkbox 涓嶅弽鏄犲瓙鏉冮檺閫変腑鐘舵€?| `@checked` 鏀逛负璋冪敤 `getModuleState()`/`getPageState()`锛岀敤 `array_intersect` 璁＄畻浜ら泦姣斾緥鍒ゆ柇 | 鍚庣 | 2026-07-30 |
| 2 | 鍘熺敓 checkbox indeterminate 鍦?Livewire morphing 鍚庝笉閲嶆柊璁剧疆 | 鏀圭敤 Alpine `x-effect` 鐩戝惉 checked 鐘舵€佽嚜鍔ㄨ缃?indeterminate | 鍓嶇 | 2026-07-30 |
| 3 | 鎸夐挳绾?`wire:model.live` 鍐欏叆瀛楃涓茬被鍨?ID 涓?PHP 绔?int 绫诲瀷姣旇緝涓嶄竴鑷?| 鏀逛负 `wire:click="togglePermission(id)"` + `@checked`锛孭HP 绔粺涓€鐢?int 姣旇緝 | 鍚庣 | 2026-07-30 |

### 4 鏁版嵁搴撳彉鏇?

鏃犮€?

### 5 褰卞搷鑼冨洿

| 褰卞搷鏂囦欢 | 鍙樻洿绫诲瀷 |
| :--- | :--- |
| app/Console/Commands/AdminSeedCommand.php | 鏂板锛堢嫭绔?seed 鍛戒护锛?|
| app/Livewire/User/RoleList.php | 淇敼锛坧ermissionTreeData 缂撳瓨 + toggleModulePermissions/togglePagePermissions/togglePermission + getModuleState/getPageState锛?|
| resources/views/livewire/user/role-list.blade.php | 淇敼锛坈heckbox + `@checked` + Alpine `x-effect` indeterminate锛?|
| database/database.sqlite | 鏂板锛堝姞鍏?git 鐗堟湰鎺у埗锛?|
| composer.json | 淇敼锛堟竻鐞嗗啑浣欒剼鏈級 |
| docs/05_Setup_瀹夎閮ㄧ讲閰嶇疆鎵嬪唽.md | 淇敼锛堣ˉ鍏?admin:seed 鍛戒护锛?|
| docs/06_Log_寮€鍙戣凯浠ｆ棩蹇?md | 淇敼锛堣ˉ鍏?V1.6.5锛?|
| docs/08_鐢ㄦ埛浣跨敤鎵嬪唽.md | 淇敼锛堟洿鏂扮増鏈彿 + 鏉冮檺寮圭獥涓夋€佽鏄庯級 |

### 6 鍏抽敭鎶€鏈褰?

**鏉冮檺鏍戜笁鎬?checkbox 閲嶆瀯锛?*
- 鏍瑰洜锛氬師瀹炵幇瀛樺湪涓変釜闂锛?1) `@checked` 鍙鏌ユā鍧?椤甸潰鑷韩 ID 涓嶅弽鏄犲瓙鏉冮檺鐘舵€侊紱(2) 鍘熺敓 checkbox indeterminate 鐢?`x-init` 璁剧疆锛孡ivewire morphing 鍚庝笉閲嶆柊鎵ц锛?3) `wire:model.live` 鍐欏叆瀛楃涓茬被鍨?ID 涓?PHP 绔?int 姣旇緝涓嶄竴鑷?
- 淇鏂规锛?
  1. 鏉冮檺鏍戞暟鎹紦瀛樺埌 `$permissionTreeData`锛岄伩鍏嶆瘡娆?toggle 鏌ユ暟鎹簱
  2. 妯″潡/椤甸潰 checkbox 鐢?`wire:click` + `@checked(getModuleState($module_id))` / `@checked(getPageState($page_id))`锛岄€氳繃 `array_intersect` 璁＄畻浜ら泦姣斾緥鍒ゆ柇閫変腑/鍗婇€?鏈€?
  3. Alpine `x-effect` 鐩戝惉 checked 鍙樺寲鑷姩璁剧疆 `el.indeterminate`
  4. 鎸夐挳绾?checkbox 鏀逛负 `wire:click="togglePermission($id)"` + `@checked(in_array($id, $selectedPermissions))`

---

## V1.6.4 | 杩唬鍛ㄦ湡锛?026-07-30

璐熻矗浜猴細椤圭洰璐熻矗浜?
鍙備笌寮€鍙戜汉鍛橈細鍚庣寮€鍙戙€佸墠绔紑鍙?

### 1 鏈鏂板鍔熻兘娓呭崟

| 搴忓彿 | 鍔熻兘妯″潡 | 鍔熻兘鐐?| 寮€鍙戜汉 | 瀹屾垚鏃堕棿 | 鐘舵€?|
| :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | 鍏ㄥ眬 | 鏂板 `WithToast` Trait锛岀粺涓€ Toast 娑堟伅鍒嗗彂鎺ュ彛 | 鍚庣 | 2026-07-30 | 鉁?|
| 2 | 鍏ㄥ眬 | 鏂板 `docs/09_寮€鍙戝墠鎸囧鎵嬪唽.md`锛屾眹鎬绘墍鏈夊紑鍙戣鑼?| 鍚庣 | 2026-07-30 | 鉁?|

### 2 鏈浼樺寲/閲嶆瀯

| 搴忓彿 | 妯″潡 | 浼樺寲鍐呭 | 寮€鍙戜汉 | 瀹屾垚鏃堕棿 |
| :--- | :--- | :--- | :--- | :--- |
| 1 | 鍏ㄥ眬锛?9涓粍浠?3涓猅rait锛?| 鎵€鏈?`dispatch('toast', message: '...')` 鏇挎崲涓?`WithToast` 鏂规硶锛坄toastSuccess`/`toastError`/`toastWarning`/`toastInfo`锛?| 鍚庣 | 2026-07-30 |

### 3 鏈淇 Bug

| 搴忓彿 | Bug鎻忚堪 | 淇鍐呭 | 寮€鍙戜汉 | 瀹屾垚鏃堕棿 |
| :--- | :--- | :--- | :--- | :--- |
| 1 | 鏉冮檺鍒嗛厤寮圭獥鐐瑰嚮濮嬬粓鍏ㄩ€夛紝鏃犳硶鍙栨秷鍕鹃€?| 妯″潡/椤甸潰 checkbox 鍘绘帀 `wire:model.live`锛屽彧淇濈暀 `wire:click` + `@checked()`锛岄伩鍏嶄袱鑰呭啿绐?| 鍓嶇 | 2026-07-30 |
| 2 | 鍏ㄩ儴妯″潡 Toast 鎻愮ず鏃犳枃瀛?| 鍒涘缓 `WithToast` Trait 缁熶竴浣跨敤 `title` 瀛楁锛屾壒閲忔浛鎹?49 涓粍浠舵枃浠?| 鍚庣 | 2026-07-30 |

### 4 鏁版嵁搴撳彉鏇?

鏃犮€?

### 5 褰卞搷鑼冨洿

| 褰卞搷鏂囦欢 | 鍙樻洿绫诲瀷 |
| :--- | :--- |
| app/Livewire/Traits/WithToast.php | 鏂板锛圱oast 缁熶竴 Trait锛?|
| app/Livewire/Traits/WithRowSelection.php | 淇敼锛坲se WithToast; + dispatch 鏇挎崲锛?|
| app/Livewire/Traits/WithExcelExport.php | 淇敼锛坲se WithToast; + dispatch 鏇挎崲锛?|
| app/Livewire/Traits/WithExcelImport.php | 淇敼锛坲se WithToast; + dispatch 鏇挎崲锛?|
| 46 涓?Livewire 缁勪欢鏂囦欢 | 淇敼锛堟坊鍔?use WithToast + dispatch 鏇挎崲涓?toastSuccess/Error/Warning/Info锛?|
| resources/views/livewire/user/role-list.blade.php | 淇敼锛堟潈闄?checkbox 淇锛?|
| docs/09_寮€鍙戝墠鎸囧鎵嬪唽.md | 鏂板锛堝紑鍙戣鑼冩眹鎬伙級 |

### 6 鍏抽敭鎶€鏈褰?

**checkbox 鐨?wire:model.live 涓?wire:click 鍐茬獊锛?*
- 鏍瑰洜锛歝heckbox 鍚屾椂缁戝畾 `wire:model.live="formArray"` 鍜?`wire:click="toggleMethod()"` 鏃讹紝Livewire 鍏堟墽琛?model 缁戝畾鑷姩鍒囨崲鍊硷紝鍐嶆墽琛?click 鏂规硶锛屽鑷磋嚜瀹氫箟閫昏緫鍩轰簬宸蹭慨鏀圭殑鏁扮粍鍒ゆ柇
- 淇锛氳仈鍔?checkbox 鍙敤 `wire:click` + `@checked()` 鎵嬪姩鎺у埗閫変腑鐘舵€?

**Toast 浜嬩欢瀛楁涓嶅尮閰嶏細**
- 鏍瑰洜锛氬悗绔?`dispatch('toast', message: '...')` 鍙戝嚭鐨?detail 瀛楁涓?`message`锛屽墠绔?`$toast.show()` 鏈熸湜 `title` 瀛楁
- 淇锛氬垱寤?`WithToast` Trait 缁熶竴浣跨敤 `title` + `description` + `type` 瀛楁

---

## V1.6.3 | 杩唬鍛ㄦ湡锛?026-07-30

璐熻矗浜猴細椤圭洰璐熻矗浜?
鍙備笌寮€鍙戜汉鍛橈細鍚庣寮€鍙戙€佸墠绔紑鍙?

### 1 鏈鏂板鍔熻兘娓呭崟

鏃犳柊澧炲姛鑳芥ā鍧椼€?

### 2 鏈浼樺寲/閲嶆瀯

| 搴忓彿 | 妯″潡 | 浼樺寲鍐呭 | 寮€鍙戜汉 | 瀹屾垚鏃堕棿 |
| :--- | :--- | :--- | :--- | :--- |
| 1 | 瑙掕壊绠＄悊 | 鏉冮檺鍒嗛厤寮圭獥鏀逛负琛ㄦ牸褰㈠紡锛?鍒楋細澶嶉€夋銆佹ā鍧椼€侀〉闈€佹寜閽級锛屽脊绐楀姞瀹藉埌 max-w-4xl | 鍓嶇 | 2026-07-30 |
| 2 | 瑙掕壊绠＄悊 | 妯″潡鍕鹃€夎嚜鍔ㄥ叏閫夊瓙鏉冮檺锛坱oggleModulePermissions锛夛紝椤甸潰鍕鹃€夎嚜鍔ㄥ叏閫夊瓙鎸夐挳锛坱ogglePagePermissions锛?| 鍚庣 | 2026-07-30 |

### 3 鏈淇 Bug

| 搴忓彿 | Bug鎻忚堪 | 淇鍐呭 | 寮€鍙戜汉 | 瀹屾垚鏃堕棿 |
| :--- | :--- | :--- | :--- | :--- |
| 1 | 宸茬櫥褰曠敤鎴疯闂?/login 涓嶈嚜鍔ㄨ烦杞?| Login 缁勪欢 mount() 澧炲姞 auth()->check() 璺宠浆鍒?dashboard | 鍚庣 | 2026-07-30 |
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

璐熻矗浜猴細椤圭洰璐熻矗浜?
鍙備笌寮€鍙戜汉鍛橈細鍚庣寮€鍙戙€佸墠绔紑鍙?

### 1 鏈鏂板鍔熻兘娓呭崟

| 搴忓彿 | 鍔熻兘妯″潡 | 鍔熻兘鐐?| 寮€鍙戜汉 | 瀹屾垚鏃堕棿 | 鐘舵€?|
| :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | 鏉冮檺绠＄悊 | 绫诲瀷涓嬫媺绛涢€夛紙鍏ㄩ儴/妯″潡/椤甸潰/鎸夐挳锛?| 鍚庣 | 2026-07-30 | 鉁?|
| 2 | 鏉冮檺绠＄悊 | 妯″潡涓嬫媺绛涢€夛紙鍏ㄩ儴/鎸囧畾妯″潡锛屽惈瀛愮骇椤甸潰+鎸夐挳锛?| 鍚庣 | 2026-07-30 | 鉁?|
| 3 | 鏉冮檺绠＄悊 | 绫诲瀷+妯″潡缁勫悎绛涢€?+ 閲嶇疆鎸夐挳 | 鍚庣 | 2026-07-30 | 鉁?|

### 2 鏈浼樺寲/閲嶆瀯

鏃犮€?

### 3 鏈淇 Bug

| 搴忓彿 | Bug鎻忚堪 | 淇鍐呭 | 寮€鍙戜汉 | 瀹屾垚鏃堕棿 |
| :--- | :--- | :--- | :--- | :--- |
| 1 | UTF-8 BOM 瀵艰嚧 Livewire 4 缁勪欢杈圭晫閿欎綅 | 鎵归噺绉婚櫎 38 涓?Blade 瑙嗗浘鏂囦欢鐨?UTF-8 BOM锛圽xEF\xBB\xBF锛?| 鍚庣 | 2026-07-30 |
| 2 | 寮圭獥鍙栨秷鎸夐挳琚?Livewire 楠岃瘉绠￠亾鎷︽埅 | 鐢ㄦ埛/瑙掕壊/鏉冮檺涓変釜椤甸潰鐨勬墍鏈?`<button>` 娣诲姞 `type="button"` | 鍓嶇 | 2026-07-30 |

### 4 鏁版嵁搴撳彉鏇?

鏃犮€?

### 5 褰卞搷鑼冨洿

| 褰卞搷鏂囦欢 | 鍙樻洿绫诲瀷 |
| :--- | :--- |
| app/Livewire/User/PermissionList.php | 淇敼锛堝鍔?filterType/filterModule 灞炴€с€佺瓫閫夐€昏緫銆乺esetFilters锛?|
| resources/views/livewire/user/permission-list.blade.php | 淇敼锛堝鍔犵被鍨?妯″潡涓や釜 select 涓嬫媺绛涢€夈€侀噸缃寜閽級 |
| 38 涓?Blade 瑙嗗浘鏂囦欢 | 淇敼锛堢Щ闄?UTF-8 BOM锛?|
| resources/views/livewire/user/user-list.blade.php | 淇敼锛坆utton 娣诲姞 type="button"锛?|
| resources/views/livewire/user/role-list.blade.php | 淇敼锛坆utton 娣诲姞 type="button"锛?|
| resources/views/livewire/user/permission-list.blade.php | 淇敼锛坆utton 娣诲姞 type="button"锛?|

### 6 鍏抽敭鎶€鏈褰?

**UTF-8 BOM 瀵艰嚧 Livewire 4 morphing 閿欎綅闂锛?*
- 鏍瑰洜锛欱lade 瑙嗗浘鏂囦欢寮€澶村寘鍚?UTF-8 BOM锛圽xEF\xBB\xBF锛夛紝鍦ㄧ粍浠舵牴 `<div>` 鍓嶄骇鐢熶簡涓嶅彲瑙佹枃鏈妭鐐?
- 褰卞搷锛歀ivewire 4 morphing 绠楁硶灏?`wire:id` 缁戝畾鍒伴敊璇瓙鍏冪礌锛堝 `class="flex items-center justify-between mb-6"` 鑰岄潪 `class="p-6"`锛夛紝瀵艰嚧寮圭獥鍐呭鍜屾搷浣滄寜閽劚绂荤粍浠?DOM 杈圭晫
- 淇锛氭壒閲忕Щ闄ゆ墍鏈?Blade 鏂囦欢 BOM + 涓?`<button>` 娣诲姞 `type="button"` 闃叉 form 鎻愪氦琛屼负

---

## V1.6.1 | 杩唬鍛ㄦ湡锛?026-07-30

璐熻矗浜猴細椤圭洰璐熻矗浜?
鍙備笌寮€鍙戜汉鍛橈細鍚庣寮€鍙戙€佸墠绔紑鍙?

### 1 鏈鏂板鍔熻兘娓呭崟

鏃犳柊澧炲姛鑳芥ā鍧椼€?

### 2 鏈浼樺寲/閲嶆瀯

| 搴忓彿 | 妯″潡 | 浼樺寲鍐呭 | 寮€鍙戜汉 | 瀹屾垚鏃堕棿 |
| :--- | :--- | :--- | :--- | :--- |
| 1 | 鐢ㄦ埛绠＄悊 | 鐘舵€佸垪鏀逛负 toggle switch 寮€鍏筹紝琛屽唴鐩存帴鐐瑰嚮鍒囨崲鍚敤/绂佺敤 | 鍓嶇 | 2026-07-30 |
| 2 | 鐢ㄦ埛绠＄悊 | 鎿嶄綔鎸夐挳浠庢枃瀛楁敼涓?SVG 鍥炬爣锛堢紪杈?瑙掕壊鍒嗛厤/閲嶇疆瀵嗙爜/鍒犻櫎锛?| 鍓嶇 | 2026-07-30 |
| 3 | 瑙掕壊绠＄悊 | 鎿嶄綔鎸夐挳浠庢枃瀛楁敼涓?SVG 鍥炬爣锛堢紪杈?鏉冮檺鍒嗛厤/鍒犻櫎锛?| 鍓嶇 | 2026-07-30 |
| 4 | 鏉冮檺绠＄悊 | 鎿嶄綔鎸夐挳浠庢枃瀛楁敼涓?SVG 鍥炬爣锛堢紪杈?瑙掕壊鍒嗛厤/鍒犻櫎锛?| 鍓嶇 | 2026-07-30 |
| 5 | 鐢ㄦ埛绠＄悊 | 瓒呯骇绠＄悊鍛橈紙super-admin 瑙掕壊锛夌姝㈠垹闄ゅ拰绂佺敤锛屽垪琛ㄤ笉鏄剧ず鍒犻櫎鎸夐挳鍜?toggle 寮€鍏?| 鍚庣 | 2026-07-30 |
| 6 | 瑙掕壊绠＄悊 | super-admin 瑙掕壊绂佹鍒犻櫎鍜岀紪杈戯紝鍒楄〃涓嶆樉绀虹紪杈?鍒犻櫎鎸夐挳 | 鍚庣 | 2026-07-30 |

### 3 鏈淇 Bug

| 搴忓彿 | Bug鎻忚堪 | 淇鍐呭 | 寮€鍙戜汉 | 瀹屾垚鏃堕棿 |
| :--- | :--- | :--- | :--- | :--- |
| 1 | 瓒呯骇绠＄悊鍛樺彲琚垹闄?绂佺敤 | delete() 鍜?toggleStatus() 澧炲姞 hasRole('super-admin') 妫€鏌?| 鍚庣 | 2026-07-30 |
| 2 | super-admin 瑙掕壊鍙鍒犻櫎/缂栬緫 | RoleList delete() 鍜?openEditModal() 澧炲姞 name==='super-admin' 淇濇姢 | 鍚庣 | 2026-07-30 |

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

璐熻矗浜猴細椤圭洰璐熻矗浜?
鍙備笌寮€鍙戜汉鍛橈細鍚庣寮€鍙戙€佸墠绔紑鍙?

### 1 鏈鏂板鍔熻兘娓呭崟

| 搴忓彿 | 鍔熻兘妯″潡 | 鍔熻兘鐐?| 寮€鍙戜汉 | 瀹屾垚鏃堕棿 | 鐘舵€?|
| :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | 琛ヨ揣鎻愰啋 | RestockReminderList Livewire 椤甸潰锛圕RUD + 鍟嗗/SKU 閫夋嫨 + 鍛ㄦ湡閰嶇疆锛?| 鍚庣 | 2026-07-29 | 鉁?|
| 2 | 閲囪喘閫€璐?| PurchaseReturnList 浠庢々鍗囩骇涓哄畬鏁?CRUD锛堝叧鑱旈噰璐崟 + 渚涘簲鍟?+ 浠撳簱 + 閫€璐у師鍥狅級 | 鍚庣 | 2026-07-29 | 鉁?|
| 3 | 浠撳簱绠＄悊 | WarehouseList 浠庢々鍗囩骇涓哄畬鏁?CRUD锛堢被鍨?鍐烽摼/鍦板潃/鐘舵€侊級 | 鍚庣 | 2026-07-29 | 鉁?|
| 4 | 搴撳瓨绠＄悊 | InventoryList 浠庢々鍗囩骇涓哄畬鏁?CRUD锛堜粨搴?SKU/搴撳瓨鏁伴噺/鎵规/鏁堟湡/棰勮鍊硷級 | 鍚庣 | 2026-07-29 | 鉁?|
| 5 | 搴撳瓨鏃ュ織 | InventoryLogList 浠庢々鍗囩骇涓哄彧璇绘棩蹇楁煡鐪嬪櫒锛堢被鍨?浠撳簱绛涢€夛紝绂佹鍒犻櫎锛?| 鍚庣 | 2026-07-29 | 鉁?|
| 6 | 鎹熻€楃鐞?| LossOrderList 浠庢々鍗囩骇涓哄畬鏁?CRUD + 瀹℃牳娴佺▼锛堟柊寤?缂栬緫/瀹℃牳/鎵ц/鍏抽棴锛?| 鍚庣 | 2026-07-29 | 鉁?|
| 7 | 璺敱 | 鏂板 /restock-reminders 璺敱 | 鍚庣 | 2026-07-29 | 鉁?|
| 8 | 鐢ㄦ埛绠＄悊 | UserList Livewire 缁勪欢锛圕RUD + 瑙掕壊鍒嗛厤 + 绂佺敤鍚敤 + 閲嶇疆瀵嗙爜 + 鎼滅储锛?| 鍚庣 | 2026-07-29 | 鉁?|
| 9 | 瑙掕壊绠＄悊 | RoleList 澧炲姞鏉冮檺鍒嗛厤寮圭獥锛堟ā鍧椻啋椤甸潰鈫掓寜閽?鏍戝舰澶嶉€夋锛?| 鍚庣 | 2026-07-29 | 鉁?|
| 10 | 鏉冮檺绠＄悊 | PermissionList 澧炲姞瑙掕壊鍒嗛厤寮圭獥锛堝閫夋锛屼负鏉冮檺鍕鹃€夊叧鑱旇鑹诧級 | 鍚庣 | 2026-07-29 | 鉁?|
| 11 | 璺敱 | 鏂板 /users 璺敱 + 瀵艰埅淇 href="#" 鈫?route('users') | 鍚庣 | 2026-07-29 | 鉁?|
| 12 | 鍏ㄥ眬鍩虹璁炬柦 | 瀹夎 maatwebsite/excel + 鍒涘缓4涓€氱敤Trait(WithRowSelection/WithColumnVisibility/WithExcelExport/WithExcelImport) + GenericExport/GenericImport + money_format/status_badge 鍔╂墜鍑芥暟 | 鍚庣 | 2026-07-30 | 鉁?|
| 13 | 鍏ㄦā鍧楀崌绾?| 49涓垪琛ㄧ粍浠跺叏闈㈠崌绾э細checkbox澶氶€夈€佹壒閲忔搷浣溿€佽嚜瀹氫箟鏄剧ず鍒椼€丒xcel瀵煎叆瀵煎嚭锛堣嚜瀹氫箟鍒楋級銆侀噾棰濇牸寮忓寲 | 鍚庣 | 2026-07-30 | 鉁?|
| 14 | 璁㈠崟妯″潡 | OrderList/CartList/OrderReturnList 浠庢々鍗囩骇涓哄畬鏁碈RUD+鐘舵€佹祦杞?閲戦鏍煎紡鍖?| 鍚庣 | 2026-07-30 | 鉁?|
| 15 | 閰嶉€佹ā鍧?| DeliveryTaskList/SignatureList/DiscrepancyList/TemperatureList 鍗囩骇锛堜慨澶嶆俯搴﹁褰曟悳绱ug锛?| 鍚庣 | 2026-07-30 | 鉁?|
| 16 | 璐㈠姟妯″潡 | 5涓储鍔＄粍浠跺崌绾т负瀹屾暣CRUD+瀹℃牳/浠樻/鏀舵鎿嶄綔+閲戦鏍煎紡鍖?鎼滅储浼樺寲 | 鍚庣 | 2026-07-30 | 鉁?|
| 17 | 浠锋牸绛栫暐 | 3涓环鏍肩粍浠跺崌绾т负瀹屾暣CRUD+瀹℃牳鍚仠鎿嶄綔 | 鍚庣 | 2026-07-30 | 鉁?|
| 18 | 閲囪喘搴撳瓨 | 8涓噰璐?搴撳瓨缁勪欢鍗囩骇涓哄畬鏁碈RUD+鐘舵€佹祦杞?鎼滅储浼樺寲 | 鍚庣 | 2026-07-30 | 鉁?|

### 2 鏈浼樺寲/閲嶆瀯

| 搴忓彿 | 妯″潡 | 浼樺寲鍐呭 | 寮€鍙戜汉 | 瀹屾垚鏃堕棿 |
| :--- | :--- | :--- | :--- | :--- |
| 1 | Model | Warehouse / LossOrder / PurchaseReturn 淇 SoftDeletes trait 閿欎綅鍒扮被澶栫殑 Bug | 鍚庣 | 2026-07-29 |
| 2 | Model | Inventory / InventoryLog / LossOrderItem / PurchaseReturnItem 娓呯悊鏈娇鐢ㄧ殑 SoftDeletes import | 鍚庣 | 2026-07-29 |
| 3 | Model | Warehouse 琛ュ厖 statusMap / relationships / scopeEnabled | 鍚庣 | 2026-07-29 |
| 4 | Model | LossOrder 琛ュ厖 statusMap / approvalStatusMap / relationships / scopePending | 鍚庣 | 2026-07-29 |
| 5 | Model | PurchaseReturn 琛ュ厖 statusMap / relationships / scopePending | 鍚庣 | 2026-07-29 |
| 6 | Model | Inventory 琛ュ厖 warehouse/sku relationships / scopeBelowWarning | 鍚庣 | 2026-07-29 |
| 7 | Model | InventoryLog 琛ュ厖 warehouse/sku/operator relationships / typeLabel accessor | 鍚庣 | 2026-07-29 |
| 8 | Model | LossOrderItem 琛ュ厖 responsiblePartyMap / lossOrder/sku/supplier relationships | 鍚庣 | 2026-07-29 |
| 9 | Model | PurchaseReturnItem 琛ュ厖 purchaseReturn/sku relationships | 鍚庣 | 2026-07-29 |
| 10 | Model | RestockReminder 琛ュ厖 remindCycleMap / statusMap / merchant/sku relationships / scopeEnabled | 鍚庣 | 2026-07-29 |

### 3 鏈淇 Bug

| 搴忓彿 | Bug鎻忚堪 | 淇鍐呭 | 寮€鍙戜汉 | 瀹屾垚鏃堕棿 |
| :--- | :--- | :--- | :--- | :--- |
| 1 | Warehouse/LossOrder/PurchaseReturn 鐨?SoftDeletes trait 鍐欏湪 class 澶栭潰瀵艰嚧杞垹闄や笉鐢熸晥 | 灏?`use SoftDeletes` 绉诲叆 class 鍐呴儴 | 鍚庣 | 2026-07-29 |
| 2 | InventoryList 鎼滅储 sku_id 浣跨敤 like 鏌ユ暣鍨嬪瓧娈?| 鏀逛负閫氳繃 sku 鍏宠仈鎼滅储 sku_code / product.name | 鍚庣 | 2026-07-29 |
| 3 | InventoryLogList 鍏佽鍒犻櫎鏃ュ織璁板綍 | 绉婚櫎鍒犻櫎鍔熻兘锛屾敼涓哄彧璇绘棩蹇楁煡鐪嬪櫒 | 鍚庣 | 2026-07-29 |
| 4 | 寮圭獥鍙栨秷鎸夐挳涓嶅叧闂?| wire:click="showModal=false" 琚?Livewire 楠岃瘉绠￠亾鎷︽埅锛屾敼鐢?closeXxxModal() 鏂规硶 | 鍚庣 | 2026-07-30 |
| 5 | Model 閲嶅 SoftDeletes import | Banner/Discrepancy/MerchantAddress/OrderReturn/PriceStrategy 鍒犻櫎澶氫綑 use SoftDeletes 琛?| 鍚庣 | 2026-07-30 |
| 6 | 璐㈠姟鎼滅储bug | MerchantAccountList 鐢?merchant_id like 鎼滅储鏀逛负鍏宠仈鍟嗗鍚嶆悳绱?| 鍚庣 | 2026-07-30 |
| 7 | 浠锋牸鎼滅储bug | PriceApportionmentList 鐢?amount like 鎼滅储鏀逛负鎸夊悕绉版悳绱?| 鍚庣 | 2026-07-30 |

### 4 鏁版嵁搴撳彉鏇?

鏃狅紙鏈鏈柊澧?Migration锛?

### 5 褰卞搷鑼冨洿

| 褰卞搷鏂囦欢 | 鍙樻洿绫诲瀷 |
| :--- | :--- |
| app/Models/Warehouse.php | 閲嶅啓锛堜慨澶岯ug + 琛ュ厖鍏崇郴/鐘舵€佹槧灏勶級 |
| app/Models/LossOrder.php | 閲嶅啓锛堜慨澶岯ug + 琛ュ厖鍏崇郴/鐘舵€佹槧灏勶級 |
| app/Models/PurchaseReturn.php | 閲嶅啓锛堜慨澶岯ug + 琛ュ厖鍏崇郴/鐘舵€佹槧灏勶級 |
| app/Models/Inventory.php | 閲嶅啓锛堟竻鐞唅mport + 琛ュ厖鍏崇郴锛?|
| app/Models/InventoryLog.php | 閲嶅啓锛堟竻鐞唅mport + 琛ュ厖鍏崇郴锛?|
| app/Models/LossOrderItem.php | 閲嶅啓锛堟竻鐞唅mport + 琛ュ厖鍏崇郴/璐ｄ换鏂规槧灏勶級 |
| app/Models/PurchaseReturnItem.php | 閲嶅啓锛堣ˉ鍏呭叧绯伙級 |
| app/Models/RestockReminder.php | 閲嶅啓锛堣ˉ鍏呭懆鏈?鐘舵€佹槧灏?鍏崇郴锛?|
| app/Livewire/Product/RestockReminderList.php | 鏂板 |
| app/Livewire/Purchase/PurchaseReturnList.php | 閲嶅啓锛堟々鈫掑畬鏁碈RUD锛?|
| app/Livewire/Inventory/WarehouseList.php | 閲嶅啓锛堟々鈫掑畬鏁碈RUD锛?|
| app/Livewire/Inventory/InventoryList.php | 閲嶅啓锛堟々鈫掑畬鏁碈RUD锛?|
| app/Livewire/Inventory/InventoryLogList.php | 閲嶅啓锛堟々鈫掑彧璇绘煡鐪嬪櫒锛?|
| app/Livewire/Loss/LossOrderList.php | 閲嶅啓锛堟々鈫掑畬鏁碈RUD+瀹℃牳锛?|
| resources/views/livewire/product/restock-reminder-list.blade.php | 鏂板 |
| resources/views/livewire/purchase/purchase-return-list.blade.php | 閲嶅啓 |
| resources/views/livewire/inventory/warehouse-list.blade.php | 閲嶅啓 |
| resources/views/livewire/inventory/inventory-list.blade.php | 閲嶅啓 |
| resources/views/livewire/inventory/inventory-log-list.blade.php | 閲嶅啓 |
| resources/views/livewire/loss/loss-order-list.blade.php | 閲嶅啓 |
| routes/web.php | 淇敼锛堟柊澧?鏉¤矾鐢憋級 |
| app/Livewire/User/UserList.php | 鏂板锛堢敤鎴风鐞咰RUD+瑙掕壊鍒嗛厤+绂佺敤鍚敤+閲嶇疆瀵嗙爜锛?|
| resources/views/livewire/user/user-list.blade.php | 鏂板 |
| app/Livewire/User/RoleList.php | 閲嶅啓锛堝鍔犳潈闄愬垎閰嶆爲寮圭獥锛?|
| resources/views/livewire/user/role-list.blade.php | 閲嶅啓锛堝鍔犳潈闄愭爲+鍒犻櫎纭锛?|
| app/Livewire/User/PermissionList.php | 閲嶅啓锛堝鍔犺鑹插垎閰嶅脊绐楋級 |
| resources/views/livewire/user/permission-list.blade.php | 閲嶅啓锛堝鍔犺鑹插嬀閫?鍒犻櫎纭锛?|
| resources/views/components/app-topnav.blade.php | 淇敼锛堢敤鎴风鐞嗗鑸摼鎺?href="#" 鈫?route('users')锛?|

---

## V1.5.0 | 杩唬鍛ㄦ湡锛?026-07-29

璐熻矗浜猴細椤圭洰璐熻矗浜?
鍙備笌寮€鍙戜汉鍛橈細鍚庣寮€鍙戙€佸墠绔紑鍙?

### 1 鏈鏂板鍔熻兘娓呭崟

| 搴忓彿 | 鍔熻兘妯″潡 | 鍔熻兘鐐?| 寮€鍙戜汉 | 瀹屾垚鏃堕棿 | 鐘舵€?|
| :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | 鍥炬爣 | icon.blade.php 鏂板 16 涓?Heroicons 鍥炬爣锛坈lipboard-document-list / document-text / star / arrow-path / photo / sparkles / key / chat-bubble-left-right / map-pin / clock / plus / x-mark / magnifying-glass / shield-check / shield-exclamation / check-circle锛?| 鍓嶇 | 2026-07-29 | 鉁?|
| 2 | 瀵艰埅 | nav-menu-icon-link 缁勪欢澧炲姞 description 鍙傛暟锛屽叏閮ㄥ瓙鑿滃崟娣诲姞绠€鐭姛鑳芥弿杩?| 鍓嶇 | 2026-07-29 | 鉁?|
| 3 | 瀵艰埅 | 鐢ㄦ埛鏉冮檺/绯荤粺绠＄悊瀛愯彍鍗曠粺涓€鏀圭敤 icon-link+description 鏍峰紡锛堝師閮ㄥ垎浣跨敤 nav-menu-link锛?| 鍓嶇 | 2026-07-29 | 鉁?|
| 4 | 娴嬭瘯鏁版嵁 | DemoDataSeeder 鎵╁睍鍏ㄦā鍧楃ず渚嬫暟鎹紙30+ 寮犱笟鍔¤〃锛岃鐩栭噰璐?璁㈠崟/搴撳瓨/閰嶉€?鎹熻€?鎷ｈ揣/宸紓/璐㈠姟/閫€璐?浠锋牸绛栫暐/Banner/鐧诲綍鏃ュ織绛夛級 | 鍚庣 | 2026-07-29 | 鉁?|

### 2 鏈浼樺寲/閲嶆瀯

| 搴忓彿 | 妯″潡 | 浼樺寲鍐呭 | 寮€鍙戜汉 | 瀹屾垚鏃堕棿 |
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
| resources/views/components/app-topnav.blade.php | 淇敼锛堝叏閮ㄥ瓙鑿滃崟娣诲姞description锛?|
| database/seeders/DemoDataSeeder.php | 淇敼锛堟墿灞?0+琛ㄧず渚嬫暟鎹級 |

---

## V1.4.0 | 杩唬鍛ㄦ湡锛?026-07-29

璐熻矗浜猴細椤圭洰璐熻矗浜?
鍙備笌寮€鍙戜汉鍛橈細鍚庣寮€鍙戙€佸墠绔紑鍙?

### 1 鏈鏂板鍔熻兘娓呭崟

| 搴忓彿 | 鍔熻兘妯″潡 | 鍔熻兘鐐?| 寮€鍙戜汉 | 瀹屾垚鏃堕棿 | 鐘舵€?|
| :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | 搴撳瓨绠＄悊 | Warehouse / Inventory / InventoryLog Model + 3 涓?Livewire 椤甸潰 | 鍚庣 | 2026-07-29 | 鉁?|
| 2 | 鎹熻€楃鐞?| LossOrder / LossOrderItem Model + 1 涓?Livewire 椤甸潰 | 鍚庣 | 2026-07-29 | 鉁?|
| 3 | 鎷ｈ揣绠＄悊 | PickingTask / PickingTaskItem Model + 1 涓?Livewire 椤甸潰 | 鍚庣 | 2026-07-29 | 鉁?|
| 4 | 閰嶉€佺鐞?| DeliveryTask / DeliveryTaskOrder / DeliveryTrack / Signature / Temperature Model + 3 涓?Livewire 椤甸潰 | 鍚庣 | 2026-07-29 | 鉁?|
| 5 | 宸紓澶勭悊 | Discrepancy Model + 1 涓?Livewire 椤甸潰 | 鍚庣 | 2026-07-29 | 鉁?|
| 6 | 璐㈠姟瀵硅处 | MerchantAccount / Recharge / SupplierSettlement / SupplierSettlementItem / SettlementPayment / Receivable / ReceivablePayment / Invoice / CorrectionAuthorization Model + 8 涓?Livewire 椤甸潰 | 鍚庣 | 2026-07-29 | 鉁?|
| 7 | 閫€璐?浠锋牸 | PurchaseReturn / PurchaseReturnItem / OrderReturn / OrderReturnItem / PriceStrategy / PriceStrategyItem / PriceChangeLog Model + 4 涓?Livewire 椤甸潰 | 鍚庣 | 2026-07-29 | 鉁?|
| 8 | 鎵╁睍+绯荤粺 | PriceApportionment / MerchantAddress / MerchantFavorite / Banner / Promotion / LoginLog / WechatUser Model + 5 涓?Livewire 椤甸潰 | 鍚庣 | 2026-07-29 | 鉁?|
| 9 | 璺敱 | 鏂板 26 鏉¤矾鐢辫鐩栧叏閮ㄦā鍧?| 鍚庣 | 2026-07-29 | 鉁?|
| 10 | 瀵艰埅 | 鍏ㄩ儴瀵艰埅鑿滃崟閾炬帴鏇挎崲涓虹湡瀹炶矾鐢?| 鍓嶇 | 2026-07-29 | 鉁?|

### 2 鏈浼樺寲/閲嶆瀯

| 搴忓彿 | 妯″潡 | 浼樺寲鍐呭 | 寮€鍙戜汉 | 瀹屾垚鏃堕棿 |
| :--- | :--- | :--- | :--- | :--- |
| 1 | 瀵艰埅 | 鍏ㄩ儴瀛愯彍鍗曠粺涓€鏀圭敤 icon-link 鏍峰紡 | 鍓嶇 | 2026-07-29 |

### 3 鏈淇 Bug

鏃?

### 4 寰呭姙浜嬮」

| 搴忓彿 | 妯″潡 | 寰呭姙鍐呭 | 浼樺厛绾?|
| :--- | :--- | :--- | :--- |
| 1 | 鍏ㄦā鍧?| 鍚勬ā鍧?Detail/Form 椤甸潰寰呭紑鍙?| 涓?|
| 2 | 鍏ㄦā鍧?| 涓氬姟閫昏緫 Service 灞傚緟瀹炵幇 | 楂?|
| 3 | 鍏ㄦā鍧?| 灏忕▼搴忕 API 寰呭紑鍙?| 楂?|

### 5 绱瀹屾垚缁熻

| 鎸囨爣 | V1.3.0 | V1.4.0 | 澧為噺 |
| :--- | :--- | :--- | :--- |
| Model 鏁?| 36 | 72 | +36 |
| Livewire 缁勪欢 | 28 | 54 | +26 |
| 璺敱鏁?| 30 | 56 | +26 |
| 鏁版嵁搴撹〃 | 77 | 77 | 鈥?|

### 6 鏁版嵁搴?閰嶇疆鍙樻洿璁板綍

鏃犳柊澧?Migration锛孧odel 鍩轰簬宸叉湁琛ㄧ粨鏋勫垱寤恒€?

### 7 鍙戝竷璁板綍

| 鏃ユ湡 | 鐗堟湰 | 鎿嶄綔浜?| Git Commit | 澶囨敞 |
| :--- | :--- | :--- | :--- | :--- |
| 2026-07-29 | V1.4.0 | 椤圭洰璐熻矗浜?| 鈥?| 鍏ㄦā鍧?Model/Livewire/璺敱/瀵艰埅涓€娆℃€т氦浠?|

---

## V1.3.0 | 杩唬鍛ㄦ湡锛?026-07-29

璐熻矗浜猴細椤圭洰璐熻矗浜?
鍙備笌寮€鍙戜汉鍛橈細鍚庣寮€鍙戙€佸墠绔紑鍙?

### 1 鏈鏂板鍔熻兘娓呭崟

| 搴忓彿 | 鍔熻兘妯″潡 | 鍔熻兘鐐?| 寮€鍙戜汉 | 瀹屾垚鏃堕棿 | 鐘舵€?|
| :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | 閲囪喘绠＄悊 | PurchaseItem Model + Livewire 鍒楄〃椤?| 鍚庣 | 2026-07-29 | 鉁?|
| 2 | 閲囪喘绠＄悊 | PurchaseOrder / PurchaseOrderItem Model + Livewire 鍒楄〃椤?| 鍚庣 | 2026-07-29 | 鉁?|
| 3 | 璁㈠崟閰嶉€?| Cart / CartItem Model + Livewire 鍒楄〃椤?| 鍚庣 | 2026-07-29 | 鉁?|
| 4 | 璁㈠崟閰嶉€?| Order / OrderItem Model + Livewire 鍒楄〃椤?| 鍚庣 | 2026-07-29 | 鉁?|
| 5 | 璁㈠崟閰嶉€?| FrequentlyBought Model + Livewire 鍒楄〃椤?| 鍚庣 | 2026-07-29 | 鉁?|
| 6 | 璁㈠崟閰嶉€?| RepurchaseTemplate / RepurchaseTemplateItem Model + Livewire 鍒楄〃椤?| 鍚庣 | 2026-07-29 | 鉁?|
| 7 | 璺敱 | 鏂板 6 鏉¤矾鐢憋紙purchase-items / purchase-orders / orders / carts / frequently-bought / repurchase-templates锛?| 鍚庣 | 2026-07-29 | 鉁?|
| 8 | 瀵艰埅 | 閲囪喘绠＄悊瀛愯彍鍗曢摼鎺ュ緟閲囨竻鍗?閲囪喘鍗曠鐞?| 鍓嶇 | 2026-07-29 | 鉁?|
| 9 | 瀵艰埅 | 璁㈠崟閰嶉€佸瓙鑿滃崟閾炬帴瀹㈡埛璁㈠崟/璐墿杞?甯歌喘娓呭崟/澶嶈喘妯℃澘 | 鍓嶇 | 2026-07-29 | 鉁?|
| 10 | 鏂囨。 | FSD 9.2 Models 鐘舵€佹爣娉?+ 9.9 鏄犲皠琛ㄦ洿鏂?| 鍚庣 | 2026-07-29 | 鉁?|

### 2 鏈浼樺寲/閲嶆瀯

| 搴忓彿 | 妯″潡 | 浼樺寲鍐呭 | 寮€鍙戜汉 | 瀹屾垚鏃堕棿 |
| :--- | :--- | :--- | :--- | :--- |
| 1 | 瀵艰埅 | 閲囪喘绠＄悊瀛愯彍鍗曟敼鐢?icon-link 鏍峰紡锛堜笌鍟嗗搧绠＄悊/璁㈠崟閰嶉€佺粺涓€锛?| 鍓嶇 | 2026-07-29 |

### 3 鏈淇 Bug

鏃?

### 4 寰呭姙浜嬮」

| 搴忓彿 | 妯″潡 | 寰呭姙鍐呭 | 浼樺厛绾?|
| :--- | :--- | :--- | :--- |
| 1 | 閲囪喘绠＄悊 | PurchaseOrderDetail / PurchaseReturn / PurchaseReturnDetail 椤甸潰寰呭紑鍙?| 涓?|
| 2 | 璁㈠崟閰嶉€?| OrderDetail / OrderReturn / OrderReturnDetail 椤甸潰寰呭紑鍙?| 涓?|
| 3 | 璁㈠崟閰嶉€?| 閰嶉€佷换鍔?绛炬敹瀛樿瘉/宸紓澶勭悊妯″潡寰呭紑鍙?| 楂?|

### 5 绱瀹屾垚缁熻

| 鎸囨爣 | V1.2.0 | V1.3.0 | 澧為噺 |
| :--- | :--- | :--- | :--- |
| Model 鏁?| 26 | 36 | +10 |
| Livewire 缁勪欢 | 22 | 28 | +6 |
| 璺敱鏁?| 24 | 30 | +6 |
| 鏁版嵁搴撹〃 | 77 | 77 | 鈥?|

### 6 鏁版嵁搴?閰嶇疆鍙樻洿璁板綍

鏃犳柊澧?Migration锛孧odel 鍩轰簬宸叉湁琛ㄧ粨鏋勫垱寤恒€?

### 7 鍙戝竷璁板綍

| 鏃ユ湡 | 鐗堟湰 | 鎿嶄綔浜?| Git Commit | 澶囨敞 |
| :--- | :--- | :--- | :--- | :--- |
| 2026-07-29 | V1.3.0 | 椤圭洰璐熻矗浜?| 鈥?| 閲囪喘绠＄悊+璁㈠崟閰嶉€?Model/Livewire/璺敱/瀵艰埅 |

---

## V1.2.0 | 杩唬鍛ㄦ湡锛?026-07-29

璐熻矗浜猴細椤圭洰璐熻矗浜?
鍙備笌寮€鍙戜汉鍛橈細鍚庣寮€鍙戙€佸墠绔紑鍙?

### 1 鏈鏂板鍔熻兘娓呭崟

| 搴忓彿 | 鍔熻兘妯″潡 | 鍔熻兘鐐?| 寮€鍙戜汉 | 瀹屾垚鏃堕棿 | 鐘舵€?|
| :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | 鍟嗗搧绠＄悊 | Category Model锛堟爲褰㈠垎绫伙紝parent_id/鐘舵€?鎺掑簭锛?| AI | 2026-07-29 | 宸插畬鎴?|
| 2 | 鍟嗗搧绠＄悊 | Product Model锛堝晢鍝佷富琛紝鍒嗙被/渚涘簲鍟?绉伴噸鏀逛环/涓婁笅鏋讹級 | AI | 2026-07-29 | 宸插畬鎴?|
| 3 | 鍟嗗搧绠＄悊 | ProductImage Model锛堝晢鍝佸鍥撅級 | AI | 2026-07-29 | 宸插畬鎴?|
| 4 | 鍟嗗搧绠＄悊 | Sku Model锛圫KU瑙勬牸锛屼环鏍兼暣鏁板瓨鍌紝瀹℃牳鐘舵€侊級 | AI | 2026-07-29 | 宸插畬鎴?|
| 5 | 鍟嗗搧绠＄悊 | MerchantSkuVisibility Model锛堝晢瀹禨KU鍙鎬э級 | AI | 2026-07-29 | 宸插畬鎴?|
| 6 | 鍟嗗搧绠＄悊 | Tag / ProductTag Model锛堟爣绛捐瘝搴?鍟嗗搧鏍囩鍏宠仈锛?| AI | 2026-07-29 | 宸插畬鎴?|
| 7 | 鍟嗗搧绠＄悊 | Keyword Model锛堟悳绱㈠叧閿瘝鑱旀兂+鐑害锛?| AI | 2026-07-29 | 宸插畬鎴?|
| 8 | 鏉＄爜绠＄悊 | SkuBarcode Model锛?绉嶆潯鐮佺被鍨嬶細鍘傚/渚涘簲鍟?鍐呴儴/澶囩敤锛?| AI | 2026-07-29 | 宸插畬鎴?|
| 9 | 涓€鍝佸渚?| SkuSupplier Model锛圫KU-渚涘簲鍟嗗叧鑱旓紝榛樿渚涘簲鍟?閲囪喘浠凤級 | AI | 2026-07-29 | 宸插畬鎴?|
| 10 | 鍟嗗搧绠＄悊 | CategoryList Livewire锛堝垎绫籆RUD+鎼滅储+寮圭獥琛ㄥ崟锛?| AI | 2026-07-29 | 宸插畬鎴?|
| 11 | 鍟嗗搧绠＄悊 | ProductList Livewire锛堝晢鍝丆RUD+鍒嗙被绛涢€?鐘舵€佺瓫閫夛級 | AI | 2026-07-29 | 宸插畬鎴?|
| 12 | 鍟嗗搧绠＄悊 | SkuList Livewire锛圫KU鍒楄〃+瀹℃牳鐘舵€佹爣绛撅級 | AI | 2026-07-29 | 宸插畬鎴?|
| 13 | 鍟嗗搧绠＄悊 | TagList Livewire锛堟爣绛綜RUD+鎼滅储锛?| AI | 2026-07-29 | 宸插畬鎴?|
| 14 | 鍟嗗搧绠＄悊 | KeywordList Livewire锛堝叧閿瘝CRUD+鎼滅储娆℃暟鎺掑簭锛?| AI | 2026-07-29 | 宸插畬鎴?|
| 15 | 鏉＄爜绠＄悊 | SkuBarcodeList Livewire锛堟潯鐮丆RUD+绫诲瀷绛涢€夛級 | AI | 2026-07-29 | 宸插畬鎴?|
| 16 | 涓€鍝佸渚?| SkuSupplierList Livewire锛堝叧鑱擟RUD+渚涘簲鍟嗕笅鎷夛級 | AI | 2026-07-29 | 宸插畬鎴?|
| 17 | 璺敱 | web.php 鏂板 7 鏉¤矾鐢憋紙categories/products/skus/tags/keywords/sku-barcodes/sku-suppliers锛?| AI | 2026-07-29 | 宸插畬鎴?|
| 18 | 瀵艰埅 | 鍟嗗搧绠＄悊瀛愯彍鍗曞叏閮ㄦ寚鍚戠湡瀹炶矾鐢?| AI | 2026-07-29 | 宸插畬鎴?|
| 19 | 鏂囨。 | FSD 9.2 Models 鐩綍鏍囨敞宸插垱寤虹姸鎬侊紝娓呯悊灏鹃儴閲嶅娈嬬暀 | AI | 2026-07-29 | 宸插畬鎴?|

### 2 鍘熸湁鍔熻兘浼樺寲椤?

1. FSD 鏂囨。娓呯悊 鈥?鍒犻櫎灏鹃儴 2293-2733 琛岄噸澶嶆畫鐣欏唴瀹癸紙鏃х増 Livewire 缁勪欢/Model/璺敱瑙勫垝閲嶅娈碉級锛?026-07-29锛?

### 3 BUG 淇璁板綍

鏃?

### 4 閬楃暀寰呰凯浠ｉ渶姹傦紙涓嬩竴鐗堟湰澶勭悊锛?

| 搴忓彿 | 闇€姹傛弿杩?| 浼樺厛绾?| 澶囨敞 |
| :--- | :--- | :--- | :--- |
| 1 | Model 绗?~15鎵癸紙绾?9涓級 | P0 | 閲囪喘鈫掕鍗曗啋鎷ｈ揣鈫掗厤閫佲啋宸紓鎹熻€椻啋閫€璐р啋浠锋牸绛栫暐鈫掕储鍔♀啋鍧囨憡+鎵╁睍+绯荤粺+寰俊 |
| 2 | 瀹夎 Redis PHP 鎵╁睍 | P1 | .env 鐩墠鐢?file 椹卞姩 |

### 5 瀹屾垚缁熻

| 缁村害 | 宸插畬鎴?| 寰呭紑鍙?| 鍚堣 |
| :--- | :--- | :--- | :--- |
| 鍔熻兘鐐?| 38 | 15 | 53 |
| 鏁版嵁搴撹〃 | 77锛堝凡寤?Migration锛?| 0 | 77 |
| Blade 缁勪欢 | 34+ | - | 34+ |
| Model | 26 | ~42 | ~68 |
| Livewire 缁勪欢 | 22 | - | 22 |
| 璺敱 | 24 | - | 24 |

### 6 鐗堟湰鍙戝竷璁板綍

| 椤圭洰 | 鍐呭 |
| :--- | :--- |
| 鐗堟湰鍙?| V1.2.0-alpha |
| 鍙戝竷鏃堕棿 | 2026-07-29 |
| 閮ㄧ讲鐜 | 寮€鍙戠幆澧冿紙Laragon锛宧ttps://livewire.test锛?|
| Git 鍒嗘敮 | navigate |
| 杩滅▼浠撳簱 | git@github.com:kindiaing/susong-livewire.git |
| 鏂板鏂囦欢 | 10 Model + 7 Livewire 缁勪欢 + 7 Livewire 瑙嗗浘 |
| 淇敼鏂囦欢 | web.php / app-topnav.blade.php / FSD 9.2 + 9.9 |

---

## V1.1.0 | 杩唬鍛ㄦ湡锛?026-07-29

璐熻矗浜猴細椤圭洰璐熻矗浜?
鍙備笌寮€鍙戜汉鍛橈細鍚庣寮€鍙戙€佸墠绔紑鍙?

### 1 鏈鏂板鍔熻兘娓呭崟

| 搴忓彿 | 鍔熻兘妯″潡 | 鍔熻兘鐐?| 寮€鍙戜汉 | 瀹屾垚鏃堕棿 | 鐘舵€?|
| :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | 鐢ㄦ埛鏉冮檺 | Role Model锛堢户鎵?SpatieRole锛屾墿灞?display_name/type/parent_id锛?| AI | 2026-07-29 | 宸插畬鎴?|
| 2 | 鐢ㄦ埛鏉冮檺 | Permission Model锛堢户鎵?SpatiePermission锛屾墿灞?display_name/type/parent_id锛?| AI | 2026-07-29 | 宸插畬鎴?|
| 3 | 缁勭粐涓讳綋 | Supplier Model锛堜緵搴斿晢锛屽惈鐘舵€?绫诲瀷甯搁噺銆佸叧鑱斿叧绯汇€佷綔鐢ㄥ煙锛?| AI | 2026-07-29 | 宸插畬鎴?|
| 4 | 缁勭粐涓讳綋 | Merchant Model锛堝晢瀹讹紝鍚姸鎬佸父閲忋€佸鍦板潃鍏宠仈锛?| AI | 2026-07-29 | 宸插畬鎴?|
| 5 | 缁勭粐涓讳綋 | DeliveryRoute Model锛堥厤閫佺嚎璺紝鍚帓搴?鐘舵€佸父閲忥級 | AI | 2026-07-29 | 宸插畬鎴?|
| 6 | 缁勭粐涓讳綋 | Driver Model锛堝徃鏈猴紝鍚姸鎬?绫诲瀷甯搁噺銆佽溅杈嗙粦瀹氬叧鑱旓級 | AI | 2026-07-29 | 宸插畬鎴?|
| 7 | 缁勭粐涓讳綋 | Vehicle Model锛堣溅杈嗭紝鍚溅鍨?鐘舵€?鍐烽摼鏍囪甯搁噺锛?| AI | 2026-07-29 | 宸插畬鎴?|
| 8 | 缁勭粐涓讳綋 | DriverVehicle Model锛堝徃鏈?杞﹁締缁戝畾锛屽瀵瑰涓棿琛級 | AI | 2026-07-29 | 宸插畬鎴?|
| 9 | 鐢ㄦ埛鏉冮檺 | RoleList Livewire锛堣鑹插垪琛?鎼滅储+寮圭獥琛ㄥ崟+鍒犻櫎锛?| AI | 2026-07-29 | 宸插畬鎴?|
| 10 | 鐢ㄦ埛鏉冮檺 | PermissionList Livewire锛堟潈闄愬垪琛?鎼滅储+寮圭獥琛ㄥ崟+鍒犻櫎锛?| AI | 2026-07-29 | 宸插畬鎴?|
| 11 | 缁勭粐涓讳綋 | SupplierList Livewire锛堜緵搴斿晢CRUD+鎼滅储+鐘舵€佹爣绛撅級 | AI | 2026-07-29 | 宸插畬鎴?|
| 12 | 缁勭粐涓讳綋 | MerchantList Livewire锛堝晢瀹禖RUD+鎼滅储+鐘舵€佹爣绛撅級 | AI | 2026-07-29 | 宸插畬鎴?|
| 13 | 缁勭粐涓讳綋 | RouteList Livewire锛堢嚎璺疌RUD+鎼滅储+鎺掑簭锛?| AI | 2026-07-29 | 宸插畬鎴?|
| 14 | 缁勭粐涓讳綋 | DriverList Livewire锛堝徃鏈篊RUD+鎼滅储+鐘舵€佹爣绛撅級 | AI | 2026-07-29 | 宸插畬鎴?|
| 15 | 缁勭粐涓讳綋 | VehicleList Livewire锛堣溅杈咰RUD+鎼滅储+鍐烽摼/鐘舵€佹爣绛撅級 | AI | 2026-07-29 | 宸插畬鎴?|
| 16 | 璺敱 | web.php 鏂板 7 鏉¤矾鐢憋紙roles/permissions/suppliers/merchants/delivery-routes/drivers/vehicles锛?| AI | 2026-07-29 | 宸插畬鎴?|
| 17 | 瀵艰埅 | 鐢ㄦ埛鏉冮檺瀛愯彍鍗曢摼鎺ョ湡瀹炶矾鐢憋紙瑙掕壊绠＄悊鈫抮oles銆佹潈闄愮鐞嗏啋permissions锛?| AI | 2026-07-29 | 宸插畬鎴?|
| 18 | 瀵艰埅 | 缁勭粐涓讳綋鍏ㄩ儴 5 涓瓙鑿滃崟鎸囧悜鐪熷疄璺敱 | AI | 2026-07-29 | 宸插畬鎴?|
| 19 | 鏂囨。 | 08_鐢ㄦ埛浣跨敤鎵嬪唽.md 鍒涘缓锛堣鐩栧凡瀹屾垚鐨勫叏閮ㄥ姛鑳芥ā鍧楁搷浣滆鏄庯級 | AI | 2026-07-29 | 宸插畬鎴?|
| 20 | 鏂囨。 | FSD 9.3.3 鏁版嵁搴撶洰褰曟洿鏂颁负 Livewire 椤圭洰瀹為檯鐘舵€?| AI | 2026-07-29 | 宸插畬鎴?|

### 2 鍘熸湁鍔熻兘浼樺寲椤?

1. .env.example 涓枃涔辩爜淇 鈥?閲嶅啓涓烘棤 BOM UTF-8 缂栫爜锛?026-07-29锛?
2. AdminInstallCommand 寮曠敤淇 鈥?RolePermissionSeeder 鈫?SystemDataSeeder锛?026-07-29锛?
3. 绉诲姩绔眽鍫¤彍鍗曚唬鐮佹竻闄?鈥?绠＄悊鍚庡彴涓嶅仛绉诲姩绔€傞厤锛?026-07-29锛?
4. composer.json post-autoload-dump 娣诲姞鑷姩鍒涘缓缂哄け鐩綍鑴氭湰锛?026-07-29锛?

### 3 BUG 淇璁板綍

| BUG缂栧彿 | 闂鎻忚堪 | 澶嶇幇姝ラ | 淇鏂规 | 楠岃瘉鐘舵€?|
| :--- | :--- | :--- | :--- | :--- |
| 8 | AdminInstallCommand 寮曠敤宸插垹闄ょ殑 RolePermissionSeeder | `php artisan admin:install --reset --force` 鎶ラ敊绫讳笉瀛樺湪 | 鏀逛负寮曠敤 SystemDataSeeder | 宸查獙璇?|
| 9 | .env.example UTF-8 BOM 瀵艰嚧涓枃娉ㄩ噴涔辩爜 | Git clone 鍚庡湪鍏朵粬鐜鎵撳紑 .env.example 涓枃鍏ㄩ儴涔辩爜 | 閲嶅啓涓烘棤 BOM UTF-8 | 宸查獙璇?|

### 4 閬楃暀寰呰凯浠ｉ渶姹傦紙涓嬩竴鐗堟湰澶勭悊锛?

| 搴忓彿 | 闇€姹傛弿杩?| 浼樺厛绾?| 澶囨敞 |
| :--- | :--- | :--- | :--- |
| 1 | Model 绗?~15鎵癸紙绾?9涓級 | P0 | 鍟嗗搧鈫扴KU鏉＄爜鈫掍粨搴撳簱瀛樷啋閲囪喘鈫掕鍗曗啋鎷ｈ揣鈫掗厤閫佲啋宸紓鎹熻€椻啋閫€璐р啋浠锋牸绛栫暐鈫掕储鍔♀啋鍧囨憡+鎵╁睍+绯荤粺+寰俊 |
| 2 | 瀵瑰簲 Livewire 椤甸潰 | P0 | 姣忔壒 Model 閰嶅鍒楄〃/璇︽儏/琛ㄥ崟椤甸潰 |
| 3 | 瀹夎 Redis PHP 鎵╁睍 | P1 | .env 鐩墠鐢?file 椹卞姩 |
| 4 | 鏁版嵁澶у睆缁熻鍔熻兘 | P2 | 灞曠ず璁㈠崟閲忋€侀攢鍞銆佹崯鑰楃巼绛夊疄鏃舵寚鏍?|

### 5 瀹屾垚缁熻

| 缁村害 | 宸插畬鎴?| 寰呭紑鍙?| 鍚堣 |
| :--- | :--- | :--- | :--- |
| 鍔熻兘鐐?| 38 | 15 | 53 |
| 鏁版嵁搴撹〃 | 77锛堝凡寤?Migration锛?| 0 | 77 |
| Blade 缁勪欢 | 34+ | - | 34+ |
| Model | 16 | ~52 | ~68 |
| Livewire 缁勪欢 | 15 | - | 15 |
| 璺敱 | 17 | - | 17 |

### 6 鐗堟湰鍙戝竷璁板綍

| 椤圭洰 | 鍐呭 |
| :--- | :--- |
| 鐗堟湰鍙?| V1.1.0-alpha |
| 鍙戝竷鏃堕棿 | 2026-07-29 |
| 閮ㄧ讲鐜 | 寮€鍙戠幆澧冿紙Laragon锛宧ttps://livewire.test锛?|
| Git 鍒嗘敮 | navigate |
| 杩滅▼浠撳簱 | git@github.com:kindiaing/susong-livewire.git |
| 鏂板鏂囦欢 | 7 Model + 7 Livewire 缁勪欢 + 7 Livewire 瑙嗗浘 + 1 鐢ㄦ埛鎵嬪唽 |
| 淇敼鏂囦欢 | web.php / app-topnav.blade.php / .env.example / AdminInstallCommand / composer.json / FSD |

---

## V1.0.0 | 杩唬鍛ㄦ湡锛?026-07-24 ~ 2026-07-29

璐熻矗浜猴細椤圭洰璐熻矗浜?
鍙備笌寮€鍙戜汉鍛橈細鍚庣寮€鍙戙€佸墠绔紑鍙戙€佹祴璇?

### 1 鏈鏂板鍔熻兘娓呭崟

| 搴忓彿 | 鍔熻兘妯″潡 | 鍔熻兘鐐?| 寮€鍙戜汉 | 瀹屾垚鏃堕棿 | 鐘舵€?|
| :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | 椤圭洰鍒濆鍖?| Laravel 13 + Livewire 4.x + Tailwind CSS 4.2 鍏ㄦ爤鑴氭墜鏋舵惌寤?| AI | 2026-07-24 | 宸插畬鎴?|
| 2 | 鏁版嵁搴?| 20 涓?Migration锛?7 寮犺〃锛夛紝鏁存暟閲戦瀛樺偍锛屾棤澶栭敭绾︽潫 | AI | 2026-07-27 | 宸插畬鎴?|
| 3 | 鏁版嵁搴?| SystemDataSeeder锛堝唴缃細瑙掕壊/鏉冮檺/瓒呯骇绠＄悊鍛?瀹℃牳鑺傜偣/绯荤粺閰嶇疆锛?| AI | 2026-07-27 | 宸插畬鎴?|
| 4 | 鏁版嵁搴?| DemoDataSeeder锛堟祴璇曪細9 鐢ㄦ埛 + 23 閰嶇疆椤?+ 9 瑙掕壊锛?| AI | 2026-07-27 | 宸插畬鎴?|
| 5 | UI 缁勪欢搴?| 34+ Blade 缁勪欢锛坰hadcn/ui 璁捐鐞嗗康锛夛紝CSS 鍙橀噺 + 璇箟鍖?token | AI | 2026-07-25 | 宸插畬鎴?|
| 6 | 鐢ㄦ埛涓庢潈闄?| 鐧诲綍椤碉紙鐢ㄦ埛鍚?鎵嬫満/閭澶氭柟寮忥紝鑺傛祦 5 娆￠攣瀹氾紝璁颁綇 7 澶╋級 | AI | 2026-07-25 | 宸插畬鎴?|
| 7 | 鐢ㄦ埛涓庢潈闄?| 涓汉涓績锛堜慨鏀硅祫鏂?+ 淇敼瀵嗙爜锛屼腑鏂囬獙璇侊紝鏈慨鏀逛笉淇濆瓨锛?| AI | 2026-07-27 | 宸插畬鎴?|
| 8 | 绯荤粺鏀拺 | 棣栭〉娆㈣繋椤碉紙鍏紑锛屽簳閮ㄧ増鏉冿細鎶€鏈爤/澶囨鍙?寮€鍙戣€咃級 | AI | 2026-07-28 | 宸插畬鎴?|
| 9 | 绯荤粺鏀拺 | 绯荤粺閰嶇疆椤碉紙6 缁?23 椤癸紝鍒嗙粍瀵艰埅 + 琛屽唴缂栬緫 + 閲嶇疆榛樿鍊硷級 | AI | 2026-07-27 | 宸插畬鎴?|
| 10 | 绯荤粺鏀拺 | 绯荤粺璁剧疆 Drawer锛堝彸渚у叏灞忔粦鍏ワ紝鍒嗙粍 Tab锛岃鍐呯紪杈戯紝鏇夸唬鐙珛椤甸潰锛?| AI | 2026-07-29 | 宸插畬鎴?|
| 11 | 绯荤粺鏀拺 | 鎿嶄綔鏃ュ織锛圠ogOperation 涓棿浠讹紝鑷姩璁板綍鍐欐搷浣滐紝蹇界暐 Livewire 鍐呴儴锛?| AI | 2026-07-28 | 宸插畬鎴?|
| 12 | 绯荤粺鏀拺 | 瀹¤鏃ュ織椤甸潰锛堝姩浣?妯″瀷/鎿嶄綔浜?鏃ユ湡绛涢€夛紝鏁版嵁蹇収寮圭獥锛?| AI | 2026-07-28 | 宸插畬鎴?|
| 13 | 瀹℃牳绠＄悊 | 瀹℃牳閰嶇疆椤甸潰锛?9 鑺傜偣寮€鍏筹紝缁熻鍗＄墖锛屾ā鍧?椋庨櫓绛涢€夛紝Alert Dialog 纭锛?| AI | 2026-07-28 | 宸插畬鎴?|
| 14 | 瀹℃牳绠＄悊 | 瀹℃壒鍒楄〃椤甸潰锛圱ab 绛涢€夛紝璇︽儏寮圭獥锛岄€氳繃/鎷掔粷/鎾ゅ洖锛孉lert Dialog 纭锛?| AI | 2026-07-28 | 宸插畬鎴?|
| 15 | 瀹℃牳绠＄悊 | ApprovalService锛堝垱寤?閫氳繃/鎷掔粷/鎾ゅ洖锛岄槻閲嶅锛屽璁℃棩蹇楄仈鍔級 | AI | 2026-07-28 | 宸插畬鎴?|
| 16 | 娑堟伅閫氱煡 | 閫氱煡 Drawer锛堝彸渚у叏灞忥紝鏁版嵁搴撳姞杞斤紝鏈瑙掓爣锛孴ab 绛涢€夛紝鏍囪宸茶锛?| AI | 2026-07-28 | 宸插畬鎴?|
| 17 | 娑堟伅閫氱煡 | RestockReminder Model锛堟櫤鑳借ˉ璐ф彁閱掕鍒欙級 | AI | 2026-07-28 | 宸插畬鎴?|
| 18 | 瀵艰埅 | 椤堕儴瀵艰埅鏍忥紙Logo 灞呭乏 + 鑿滃崟鎼滅储灞呬腑 + 涓汉涓績灞呭彸锛?| AI | 2026-07-27 | 宸插畬鎴?|
| 19 | 瀵艰埅 | Command 鎼滅储闈㈡澘锛圕trl+K锛孉lpine.js 鍛戒护闈㈡澘锛?| AI | 2026-07-27 | 宸插畬鎴?|
| 20 | 瀵艰埅 | 绯荤粺绠＄悊瀛愯彍鍗曢摼鎺ョ湡瀹炶矾鐢憋紙settings/approval-config/operation-logs/audit-logs锛?| AI | 2026-07-28 | 宸插畬鎴?|
| 21 | UI 澧炲己 | Alert Dialog 缁勪欢锛坰hadcn/ui 椋庢牸锛? 绉?variant锛屾敮鎸?Livewire 鍥炶皟锛?| AI | 2026-07-28 | 宸插畬鎴?|
| 22 | UI 澧炲己 | Toast 閫氱煡锛堝叏灞€ Alpine store锛? 绉嶇被鍨嬶紝缁熶竴 duration 5s锛?| AI | 2026-07-28 | 宸插畬鎴?|
| 23 | 瀹夊叏 | CSRF Token 杩囨湡鑷姩鍒锋柊锛?19 鈫?reload锛岀敤鎴锋棤鎰熺煡锛?| AI | 2026-07-29 | 宸插畬鎴?|
| 24 | 缁勭粐涓讳綋 | 渚涘簲鍟嗙鐞?| AI | 2026-07-29 | V1.1.0 宸插畬鎴?|
| 25 | 缁勭粐涓讳綋 | 鍟嗗绠＄悊锛堝惈澶氬湴鍧€锛?| AI | 2026-07-29 | V1.1.0 宸插畬鎴?|
| 26 | 缁勭粐涓讳綋 | 閰嶉€佺嚎璺?鍙告満/杞﹁締绠＄悊 | AI | 2026-07-29 | V1.1.0 宸插畬鎴?|
| 27 | 鍟嗗搧绠＄悊 | 鍒嗙被/鍟嗗搧/SKU/鏉＄爜/涓€鍝佸渚?鍙鎬?鍏抽敭璇?| AI | 2026-07-29 | V1.2.0 宸插畬鎴?|
| 28 | 骞冲彴缁熼噰 | 寰呴噰娓呭崟/閲囪喘鍗?閲囪喘鍏ュ簱/閲囪喘閫€璐?| - | - | 寰呭紑鍙?|
| 29 | 瀹㈡埛鐩撮噰 | 璐墿杞?璁㈠崟/绉伴噸鏀逛环/鍞悗閫€璐?| - | - | 寰呭紑鍙?|
| 30 | 搴撳瓨绠＄悊 | 浠撳簱/瀹炴椂搴撳瓨/搴撳瓨鏃ュ織/搴撳瓨棰勮 | - | - | 寰呭紑鍙?|
| 31 | 鎹熻€楃鐞?| 鎹熻€楀崟锛堢敵鎶モ啋瀹℃牳鈫掓墸鍑忊啋鎴愭湰褰掗泦锛?| - | - | 寰呭紑鍙?|
| 32 | 鎷ｈ揣绠＄悊 | 鎷ｈ揣浠诲姟鍒涘缓/鍒嗛厤/鎵ц | - | - | 寰呭紑鍙?|
| 33 | 鐗╂祦閰嶉€?| 閰嶉€佷换鍔?杞ㄨ抗/绛炬敹/鍐烽摼娓╁害 | - | - | 寰呭紑鍙?|
| 34 | 宸紓澶勭悊 | 鎷ｈ揣/閰嶉€?瀹炴敹宸紓锛岄噾棰濊皟鏁村鎵?| - | - | 寰呭紑鍙?|
| 35 | 璐㈠姟瀵硅处 | 瀹㈡埛璐︽埛/鍏呭€?渚涘簲鍟嗙粨绠?搴旀敹/鍙戠エ/鎺堟潈鏇存/璐圭敤鍧囨憡 | - | - | 寰呭紑鍙?|
| 36 | 浠锋牸绛栫暐 | 绛栫暐绠＄悊/绛栫暐鏄庣粏/鏀逛环璁板綍 | - | - | 寰呭紑鍙?|
| 37 | 寰俊灏忕▼搴忓晢瀹剁 | 鐧诲綍/棣栭〉/鎼滅储/鍟嗗搧璇︽儏/璐墿杞?璁㈠崟/绛炬敹/璐︽埛/娑堟伅/琛ヨ揣鎻愰啋 | - | - | 寰呭紑鍙?|
| 38 | 寰俊灏忕▼搴忓徃鏈虹 | 鐧诲綍/浠诲姟/閰嶉€?杞ㄨ抗/绛炬敹/宸紓鏍囪/鍘嗗彶 | - | - | 寰呭紑鍙?|

### 2 鍘熸湁鍔熻兘浼樺寲椤?

1. 閫€鍑虹櫥褰曞悗璺宠浆棣栭〉 `/` 鑰岄潪 `/login`锛?026-07-28锛?
2. SystemConfig 缂撳瓨鍙嶅簭鍒楀寲闃插尽 鈥?`instanceof` 妫€鏌?+ try/catch锛?026-07-28锛?
3. 閫氱煡 Drawer 鍚庣鍖?鈥?浠?app.js 纭紪鐮佹敼涓?Livewire 缁勪欢鏁版嵁搴撳姞杞斤紙2026-07-28锛?
4. Migration 鏁寸悊 鈥?25鈫?0 涓枃浠讹紝5 涓?alter 鍚堝苟鍒?create锛屾椂闂存埑缂栧彿 000001~000020锛?026-07-28锛?
5. Seeder 鏁寸悊 鈥?SystemDataSeeder锛堝唴缃級+ DemoDataSeeder锛堟祴璇曪級锛?026-07-28锛?
6. 瀹℃牳绠＄悊/瀹℃壒鍒楄〃 鈥?wire:confirm 鏇挎崲涓?shadcn/ui Alert Dialog锛?026-07-28锛?
7. 绯荤粺璁剧疆 Drawer 鈥?浠庡簳閮ㄧ獎闈㈡澘鏀逛负鍙充晶鍏ㄥ睆 Drawer锛屾暣鍚堝叏閮?6 缁勯厤缃」锛?026-07-29锛?
8. 绯荤粺绠＄悊鍥炬爣 鈥?浠?cog 鏀逛负 adjustments-horizontal锛堥敮榻胯缃浘鏍囷級锛?026-07-29锛?
9. 瀹℃牳鑺傜偣鍏ㄦ櫙鍥?鈥?鍚堝苟鍒?FSD 鏂囨。锛屽垹闄ょ嫭绔?md 鏂囦欢锛?026-07-29锛?

### 3 BUG 淇璁板綍

| BUG缂栧彿 | 闂鎻忚堪 | 澶嶇幇姝ラ | 淇鏂规 | 楠岃瘉鐘舵€?|
| :--- | :--- | :--- | :--- | :--- |
| 1 | Livewire 缁勪欢 `private` 灞炴€ц姹傞棿涓嶆寔涔呭寲 | 涓汉涓績淇敼璧勬枡瀵规瘮鍘熷鍊煎け璐?| 鏀逛负浠庢暟鎹簱閲嶈鍘熷鍊煎姣?| 宸查獙璇?|
| 2 | SystemConfig 缂撳瓨鍙嶅簭鍒楀寲杩斿洖 `__PHP_Incomplete_Class` | Migration 鏂板鍒楀悗缂撳瓨鏈埛鏂?| `getAll()` 澧炲姞 instanceof 妫€鏌?+ try/catch | 宸查獙璇?|
| 3 | `Setting::get()` 鍦ㄦ暟鎹簱琛ㄤ笉瀛樺湪鏃舵姤閿?| 鏈墽琛?migrate 鏃惰闂椤?| try/catch 鍖呰９ | 宸查獙璇?|
| 4 | audit-logs.blade.php 宓屽涓夊厓 `}}}` 瀵艰嚧 ParseError | 澶嶆潅宓屽涓夊厓杩愮畻绗?| `@php` 棰勫鐞嗛鑹叉槧灏勬暟缁?| 宸查獙璇?|
| 5 | LogOperation 涓棿浠惰褰?Livewire 鍐呴儴璇锋眰 | 鎵€鏈?Livewire POST 璇锋眰琚啓鍏ユ搷浣滄棩蹇?| 蹇界暐璺緞鍒楄〃澧炲姞 `livewire-` 鍓嶇紑 | 宸查獙璇?|
| 6 | CSRF Token 杩囨湡瀵艰嚧 419 "This page has expired" | Session 杩囨湡鍚庢彁浜ょ櫥褰曡〃鍗?| 寮傚父澶勭悊鎹曡幏 TokenMismatchException + 鍓嶇 419 鑷姩 reload | 宸查獙璇?|
| 7 | Toast duration 涓嶄竴鑷达紙4s vs 5s锛?| 澶氬 toast 瓒呮椂鏃堕棿涓嶇粺涓€ | store 榛樿瓒呮椂缁熶竴涓?5s | 宸查獙璇?|

### 4 閬楃暀寰呰凯浠ｉ渶姹傦紙涓嬩竴鐗堟湰澶勭悊锛?

| 搴忓彿 | 闇€姹傛弿杩?| 浼樺厛绾?| 澶囨敞 |
| :--- | :--- | :--- | :--- |
| 1 | Model 灞傚紑鍙戯紙绗?~15鎵癸級 | P0 | 鍟嗗搧/SKU/搴撳瓨/閲囪喘/璁㈠崟/鎷ｈ揣/閰嶉€?宸紓/閫€璐?浠锋牸/璐㈠姟绛夌害 49 涓?|
| 2 | 瀹夎 Redis PHP 鎵╁睍 | P1 | .env 鐩墠鐢?file 椹卞姩 |
| 3 | 鏁版嵁澶у睆缁熻鍔熻兘 | P2 | 灞曠ず璁㈠崟閲忋€侀攢鍞銆佹崯鑰楃巼绛夊疄鏃舵寚鏍?|
| 4 | 鎿嶄綔鏃ュ織瀵煎嚭 | P2 | 鏀寔鎸夋椂闂磋寖鍥淬€佹搷浣滀汉銆佹ā鍧楀鍑?|
| 5 | 鏁堟湡绠＄悊锛堜簩鏈熸崯鑰楀寮猴級 | P2 | 鏁堟湡棰勮銆佽嚜鍔ㄨЕ鍙戞崯鑰楃敵鎶?|
| 6 | 鎹熻€楀垎鏋愭姤琛紙浜屾湡锛?| P2 | 鎸夊搧绫汇€佷緵搴斿晢銆佹椂闂寸淮搴︽崯鑰楄秼鍔垮垎鏋?|
| 7 | AI 鏅鸿兘琛ヨ揣鎺ㄨ崘 | P3 | 鍩轰簬閲囪喘棰戞鍜屽簱瀛樿嚜鍔ㄦ帹鑽愯ˉ璐ф竻鍗?|

### 5 瀹屾垚缁熻

| 缁村害 | 宸插畬鎴?| 寰呭紑鍙?| 鍚堣 |
| :--- | :--- | :--- | :--- |
| 鍔熻兘鐐?| 23 | 15 | 38 |
| 鏁版嵁搴撹〃 | 77锛堝凡寤?Migration锛?| 0 | 77 |
| Blade 缁勪欢 | 34+ | - | 34+ |
| Model | 8 | ~60 | ~68 |
| Livewire 缁勪欢 | 8 | - | 8 |
| 璺敱 | 10 | - | 10 |

### 6 鐗堟湰鍙戝竷璁板綍

| 椤圭洰 | 鍐呭 |
| :--- | :--- |
| 鐗堟湰鍙?| V1.0.0-alpha |
| 鍙戝竷鏃堕棿 | 寰呭畾 |
| 閮ㄧ讲鐜 | 寮€鍙戠幆澧冿紙Laragon锛宧ttps://livewire.test锛?|
| Git 鍒嗘敮 | navigate |
| 杩滅▼浠撳簱 | git@github.com:kindiaing/susong-livewire.git |
| 瓒呯骇绠＄悊鍛?| 鐢ㄦ埛鍚? seeding / 瀵嗙爜: Password |

### 7 鏁版嵁搴撳彉鏇磋褰?

| Migration 鏂囦欢鍚?| 鎿嶄綔绫诲瀷 | 娑夊強琛?| 璇存槑 |
| :--- | :--- | :--- | :--- |
| 2026_07_27_000001~000020 | CREATE | 77 寮犺〃 | 瀹屾暣寤鸿〃锛屾棤澶栭敭绾︽潫锛屾暣鏁伴噾棰濆瓨鍌?|
| SystemDataSeeder | INSERT | roles/permissions/users/approval_type_configs/system_configs | 鍐呯疆鏁版嵁锛岀敓浜х幆澧冨繀闇€ |
| DemoDataSeeder | INSERT | users/notifications | 娴嬭瘯鏁版嵁锛屼粎寮€鍙戠幆澧?|

### 8 閰嶇疆鍙樻洿璁板綍

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
| 鏀归€?| delivery_routes | 鏂板 8 瀛楁锛歝ode(UNIQUE)銆亀arehouse_id銆乨efault_driver_id銆乨efault_vehicle_id銆乧olor銆乨eparture_time銆乪stimated_duration銆乪stimated_distance銆乺emark |
| 鏀归€?| delivery_tasks | 瀛楁澶ч噺鏀瑰悕锛坉elivery_route_id鈫抮oute_id銆乸lanned_at鈫抪lanned_start_time 绛夛級+ 鏂板 delivery_date銆乬enerated_at銆乭as_urgent銆乭as_important銆乼otal_stops 绛夛紱鐘舵€佷粠 3 绉嶆墿灞曚负 6 绉?|
| 鏀归€?| vehicles | 鏂板 name銆乧apacity_kg銆乧apacity_volume銆乴ast_maintenance_date銆乶ext_maintenance_date銆乺emark锛泇ehicle_type鈫抰ype锛涚姸鎬佹敼涓?3 绉嶏紙1鍙敤/2缁翠慨涓?3鎶ュ簾锛?|
| 鏀瑰悕閲嶅缓 | delivery_task_orders 鈫?delivery_task_details | 鏁磋〃閲嶆瀯锛屾柊澧?merchant_id/merchant_name/merchant_address/product_summary/source_type/delivery_method/delivery_photos 绛夊瓧娈?|
| 鏂板缓 | delivery_route_stops | 绾胯矾鏄庣粏-鍟嗗鎺掑簭琛紙鏍稿績锛?|
| 鏂板缓 | delivery_task_sequences | 閰嶉€侀『搴忚〃锛堟牳蹇冿級锛屽惈鍔犳€?閲嶈鏍囪銆乥ase_sequence_no/sequence_no 鍙屽簭鍙?|
| 鏂板缓 | delivery_arrival_logs | 鎶佃揪鏃堕棿娴佹按锛屼笉鍙彉璁板綍 |
| 鏂板缓 | vehicle_issues | 杞﹁締鏁呴殰璁板綍琛?|

### Model 鍙樻洿

| 鎿嶄綔 | Model | 璇存槑 |
| :--- | :--- | :--- |
| 鏇存柊 | DeliveryRoute | 鏂板 warehouse() 鍏宠仈锛? 涓柊瀛楁 |
| 鏇存柊 | DeliveryTask | 6 鐘舵€?+ generateTaskNo() + canTransitionTo() |
| 鏇存柊 | Vehicle | 4 绫诲瀷 + 3 鐘舵€?|
| 鏇存柊 | Merchant | 绉婚櫎 delivery_route_id/delivery_sort锛屾柊澧?routeStops() |
| 鏂板缓 | DeliveryTaskDetail | 鍘?DeliveryTaskOrder 鏀瑰悕 |
| 鏂板缓 | DeliveryRouteStop | 鍚?resequence()/batchReorder() |
| 鏂板缓 | DeliveryTaskSequence | 鍚?markUrgent()/unmarkUrgent()/markImportant()/unmarkImportant() |
| 鏂板缓 | DeliveryArrivalLog | 鎶佃揪鏃堕棿娴佹按 |
| 鏂板缓 | VehicleIssue | 杞﹁締鏁呴殰 |
| 鍒犻櫎 | DeliveryTaskOrder | 宸茶 DeliveryTaskDetail 鏇夸唬 |

### Livewire 缁勪欢鍙樻洿

| 鎿嶄綔 | 缁勪欢 | 璇存槑 |
| :--- | :--- | :--- |
| 鏂板缓 | Delivery\DeliveryRouteList | 鏇夸唬鏃?Org\RouteList锛寁2 鍏ㄥ瓧娈?|
| 鏂板缓 | Delivery\DeliveryRouteDetail | 绾胯矾璇︽儏椤碉紙淇℃伅鍗＄墖 + 鍟嗗鍒楄〃 + HTML5 鎷栨嫿鎺掑簭锛?|
| 閲嶅啓 | Delivery\DeliveryTaskList | 閫傞厤 v2 瀛楁鍚?|
| 閲嶅啓 | Delivery\VehicleList | 閫傞厤 v2 瀛楁鍚?|
| 搴熷純 | Org\RouteList + Org\RouteSort | 宸茶 Delivery 缁勪欢鏇夸唬锛屽緟娓呯悊 |

### 璺敱/鑿滃崟/鏉冮檺杩佺Щ

| 鎿嶄綔 | 璇存槑 |
| :--- | :--- |
| 璺敱 | delivery-routes 鏀规寚鏂扮粍浠?+ 鏂板 delivery-routes/{id} 璇︽儏璺敱 |
| 鑿滃崟 | 鏂板缓銆岄厤閫佺鐞嗐€嶅垎缁勶紝閰嶉€佺嚎璺?浠诲姟/绛炬敹/宸紓/娓╁害浠庛€岃鍗曢厤閫併€嶇Щ鍏?|
| 鏉冮檺 | org.route 鈫?delivery.route锛堝惈 stop-manage 瀛愭潈闄愶級锛岃鑹插垎閰嶅悓姝ユ洿鏂?|

### Git 淇℃伅

| 椤圭洰 | 鍊?|
| :--- | :--- |
| 鍒嗘敮 | feature/delivery-task |
| 鎻愪氦 | f569d4e锛圡igration + Model锛夈€乤ed481a锛圠ivewire + 璺敱/鑿滃崟/鏉冮檺锛?|
| 楠岃瘉 | migrate:refresh --seed 閫氳繃 |

---

## V2.0.1 鈥?鍗曟嵁缂栬緫/鍒犻櫎闂ㄦ帶鏀剁揣锛堜粎鍒濆鐘舵€佸彲鏀癸級

### 鍙樻洿姒傝堪

缁熶竴瑙勫垯锛氬崟鎹粎鍦ㄥ垵濮嬬姸鎬侊紙鐘舵€?1锛夊彲缂栬緫/鍒犻櫎锛涙彁浜ゅ悗涓嶅彲淇敼锛岄渶瓒呯/绠＄悊鍛樺湪璁㈠崟/閲囪喘鍗曡鎯呴〉銆屽洖閫€銆嶇姸鎬佸悗鏂瑰彲缂栬緫銆傚洖閫€浠呮敼鐘舵€?+ 瀹¤鏃ュ織锛屼笉鍋氬簱瀛?閲戦/鍏宠仈鍗曟嵁鑱斿姩锛屽叾浣欏崟鎹笉鎻愪緵鍥為€€鍏ュ彛銆?

### Livewire 缁勪欢鍙樻洿

| 鎿嶄綔 | 缁勪欢 | 璇存槑 |
| :--- | :--- | :--- |
| 鏀剁揣 | Order\OrderList | 缂栬緫鎸夐挳 1~3 鈫?浠呭緟鎷ｈ揣锛涘垹闄ゆ寜閽ˉ浠呭緟鎷ｈ揣锛沷penEditModal/save/delete/batchDelete 鏈嶅姟绔牎楠?|
| 鏀剁揣 | Purchase\PurchaseOrderList | 缂栬緫鎸夐挳 1~4 鈫?浠呭緟鎺ュ崟锛涘垹闄ゆ寜閽?1~3 鈫?浠呭緟鎺ュ崟锛涙湇鍔＄鏍￠獙鍚屾 |
| 鏀剁揣 | Purchase\PurchaseReturnList | 缂栬緫鎸夐挳 1~3 鈫?浠呭緟瀹℃牳锛涘垹闄ゆ寜閽ˉ浠呭緟瀹℃牳锛涙湇鍔＄鏍￠獙鍚屾 |
| 鏀剁揣 | Order\OrderReturnList | 缂栬緫鎸夐挳 1~2 鈫?浠呭緟瀹℃牳锛涘垹闄ゆ寜閽ˉ浠呭緟瀹℃牳锛涜鐩栨壒閲忓垹闄わ紙Trait 閫氱敤瀹炵幇鏃犵姸鎬佹牎楠岋級锛屼粎寰呭鏍稿彲鍒?|
| 鏀剁揣 | Loss\LossOrderList | 缂栬緫鏈嶅姟绔?1~2 鈫?浠呭緟瀹℃牳锛涘垹闄ゆ寜閽ˉ浠呭緟瀹℃牳锛涙湇鍔＄鏍￠獙鍚屾 |
| 鏀剁揣 | Inventory\PickingTaskList | 缂栬緫鎸夐挳 1~2 鈫?浠呭緟鍒嗛厤锛涙湇鍔＄鏍￠獙鍚屾锛堝垹闄ゅ師鏈変粎寰呭垎閰嶆牎楠屼繚鐣欙級 |
| 鏀剁揣 | Delivery\DeliveryTaskList | 缂栬緫鎸夐挳 1~4 鈫?浠呭緟閰嶉€侊紱鍒犻櫎鎸夐挳琛ヤ粎寰呴厤閫侊紱鏈嶅姟绔牎楠屽悓姝?|
| 鏀剁揣 | Finance\RechargeList | 鍒犻櫎鎸夐挳琛ヤ粎寰呯‘璁わ紱save/delete 鏈嶅姟绔牎楠岋紱鎻愮ず鏂囨缁熶竴 |
| 鏀剁揣 | Finance\InvoiceList | 鍒犻櫎鎸夐挳琛ヤ粎寰呭紑鍏凤紱save/delete 鏈嶅姟绔牎楠岋紱鎻愮ず鏂囨缁熶竴 |
| 鏀剁揣 | Order\OrderDetail | canEditItems 绉婚櫎瓒呯浠绘剰鐘舵€佺洿鏀圭壒鏉?鈫?浠呭緟鎷ｈ揣锛沞xecuteSaveItem/deleteItem 鏈嶅姟绔牎楠?|
| 鏀剁揣 | Purchase\PurchaseOrderDetail | 鏄庣粏澧?鏀?鍒?瀵煎叆鐢便€岀姸鎬?1~2 + 瓒呯浠绘剰鐘舵€併€嶁啋 浠呭緟鎺ュ崟锛沞xecuteImport 鏈嶅姟绔牎楠?|

### Service 鍙樻洿

| 鎿嶄綔 | Service | 璇存槑 |
| :--- | :--- | :--- |
| 鏀剁揣 | PurchaseService::addItem / updateItem / removeItem | 鏄庣粏鎿嶄綔浠呭緟鎺ュ崟鐘舵€佸彲鐢紝绉婚櫎瓒呯鐩存敼鐗规潈锛堣秴绠￠』鍏堝洖閫€鐘舵€佸啀缂栬緫锛夛紱rollbackStatus 淇濈暀涓嶅彉 |

### 淇濈暀涓嶅彉

- 璁㈠崟/閲囪喘鍗曡鎯呴〉銆岃秴绠＄姸鎬佸洖閫€銆嶆寜閽淮鎸佺幇鐘讹紙浠呮敼鐘舵€?+ AuditLog锛屾棤鑱斿姩锛?
- 鍞悗閫€璐?鎹熻€楀崟/鎷ｈ揣浠诲姟/閰嶉€佷换鍔?鍏呭€?鍙戠エ涓嶆彁渚涘洖閫€鍏ュ彛
- 鏃犳暟鎹簱缁撴瀯鍙樻洿

### Git 淇℃伅

| 椤圭洰 | 鍊?|
| :--- | :--- |
| 鍒嗘敮 | bugfix |
| 鎻愪氦 | 瑙佹湰娆℃彁浜?|
| 楠岃瘉 | Pint 閫氳繃锛涙祴璇曞浠?9 涓け璐ヤ负杩佺Щ绱㈠紩閲嶅锛坕dx_model锛夋棦鏈夐棶棰橈紝涓庢湰娆℃棤鍏?|

---

## V2.0.2 鈥?杩佺Щ绱㈠紩淇 + 鍏呭€肩‘璁ゅ叆璐?

### 鍙樻洿姒傝堪

1. 淇 `model_has_roles` / `model_has_permissions` 鍚屽悕绱㈠紩 `idx_model` 鍐茬獊锛圫QLite 娴嬭瘯搴撹姹傜储寮曞悕鍏ㄥ眬鍞竴锛夛紝鍚庤€呮敼鍚?`idx_model_permission`锛屾祴璇曞浠舵仮澶嶅彲杩愯銆?
2. 鎸?`03_DB` 6.11.2 鑺傝璁¤ˉ涓婂厖鍊肩‘璁ゅ叆璐︼細瀹℃牳閫氳繃鍚庝簨鍔″唴鏇存柊 `merchant_accounts.balance` 鍜?`total_recharge`锛堟棤璐︽埛鍒欏垱寤猴級锛屽苟璁板綍瀹¤鏃ュ織锛涙嫆缁濇椂鍚屾 `approval_status=3`銆?

### 鍙樻洿鏄庣粏

| 鎿嶄綔 | 鏂囦欢 | 璇存槑 |
| :--- | :--- | :--- |
| 淇 | 2026_07_27_000001_create_users_and_permissions_tables.php | `model_has_permissions` 绱㈠紩 `idx_model` 鈫?`idx_model_permission`锛堟敼 create 杩佺Щ锛岀鍚?鏃?alter"瑙勮寖锛涘瓨閲?MySQL 搴撲笉鍙楀奖鍝嶏級 |
| 瀹屽杽 | Finance\RechargeList::approve | 浜嬪姟鍐咃細鐘舵€佲啋鎴愬姛 + approval_status鈫掑凡閫氳繃 + MerchantAccount 鍏ヨ处锛坆alance/total_recharge 鍘熷瓙 increment锛? AuditLog |
| 瀹屽杽 | Finance\RechargeList::reject | 鍚屾 approval_status鈫掑凡鎷掔粷 |

### Git 淇℃伅

| 椤圭洰 | 鍊?|
| :--- | :--- |
| 鍒嗘敮 | bugfix 鈫?鍚堝苟 main |
| 鎻愪氦 | 瑙佹湰娆℃彁浜?|
| 楠岃瘉 | 娴嬭瘯 9 閫氳繃 / 1 澶辫触锛圲nitConversionServiceTest 涓夌骇鎹㈢畻鏂█涓烘棦鏈夐棶棰橈紝涓庢湰娆℃棤鍏筹級锛汸int 閫氳繃 |
---

## V2.0.3 鈥?宸紓鍔熻兘涓€鏈燂細鐧昏鍏ュ彛 + 澶勭悊鍙帮紙鍏堣揣鍚庨挶锛?

### 鍙樻洿姒傝堪

鎸?`docs/research/11_宸紓鎹熻€楃暀鐥曚笌璐﹀姟鎸佸钩璁捐.md` 钀藉湴宸紓鍔熻兘涓€鏈燂細宸紓锛堜簨瀹炵櫥璁帮紝涓嶅姩搴撳瓨鍜岄挶锛変笌鎹熻€楋紙澶勭疆锛屽姩搴撳瓨鎴愭湰锛夊垎绂汇€傛嫞璐?瀹炴敹鐜妭鐧昏宸紓鍗曪紝宸紓澶勭悊鍙扮粺涓€璺熻繘锛氬畾璐ｄ换鏂?+ 澶勭悊鍐崇瓥锛堣ˉ璐?閫€娆?鎵ｆ/鎶ユ崯/涓嶈锛夛紝鎶ユ崯鑷姩鐢熸垚寰呭鏍告崯鑰楀崟锛屾嫆鏀?娈嬫鏀寔鍥炲簱鍥炶ˉ搴撳瓨銆傞€€娆?鎵ｆ鏈湡鍙櫥璁板喅绛栦笉鍔ㄩ挶锛岄噾棰濊皟鏁翠负浜屾湡銆?

### 鍙樻洿鏄庣粏

| 鎿嶄綔 | 鏂囦欢 | 璇存槑 |
| :--- | :--- | :--- |
| 鏂板 | Services/DiscrepancyService | 宸紓鍗曞叏鐢熷懡鍛ㄦ湡锛歝reate锛堢櫥璁?鍞竴鎬ф牎楠?鎸夎鍗曟槑缁嗗崟浠蜂及绠楅噾棰濆樊寮傦級銆乸rocess锛堣矗浠绘柟+鍐崇瓥鍒嗘祦锛夈€乺estockToWarehouse锛堟嫆鏀跺洖搴?TYPE_IN 鐣欑棔锛夛紱瀹¤鍙?audit_discrepancy 寮€鍏虫帶鍒?|
| 瀹屽杽 | Services/LossOrderService::create | 鏂板 sourceType/sourceId 鍙傛暟锛堥粯璁?manual/null锛屽悜鍚庡吋瀹癸級锛屾敮鎸佹潵婧?宸紓鍗?|
| 瀹屽杽 | Inventory/PickingTaskDetail | SKU 姹囨€昏銆屽綍鍏ュ疄鎷ｃ€嶅脊绐楋細瀹炴嫞鎸夎鍗曟槑缁嗛『搴忓垎鎽婏紝缂哄彛鑷姩鐢熸垚鎷ｈ揣宸紓鍗曪紙stage=1锛?|
| 瀹屽杽 | Delivery/DeliveryNoteDetail | 瀹炴敹灏戜簬搴旈€佹椂寮瑰嚭宸紓鐧昏锛堝疄鏀?0 榛樿鎷掓敹锛宻tage=3锛夛紝鐢熸垚宸紓鍗曞緟澶勭悊 |
| 閲嶆瀯 | Delivery/DiscrepancyList | 瑁稿垪琛ㄥ崌绾т负澶勭悊鍙帮細鐜妭/鐘舵€佺瓫閫夈€佽矗浠绘柟+鍐崇瓥澶勭悊寮圭獥銆佹嫆鏀跺洖搴撳脊绐楋紱淇鍒楅厤缃?amount鈫抋mount_diff锛岃ˉ鐜妭/鏁伴噺宸紓/璐ｄ换鏂?鍐崇瓥鍒?|
| 鏂板 | tests/Feature/DiscrepancyServiceTest | 6 鏉＄敤渚嬶細鐧昏/閲嶅鎷︽埅/鎶ユ崯鑱斿姩鎹熻€楀崟/涓嶈鍏抽棴/鍥炲簱鍥炶ˉ搴撳瓨/閲嶅澶勭悊鎷︽埅 |

### 鏁版嵁搴撳彉鏇?

| 鍙樻洿 | 琛ㄥ悕 | 瀛楁 | 璇存槑 |
| :--- | :--- | :--- | :--- |
| 璋冩暣 | discrepancies | order_id | 鏀逛负鍙┖锛堥€佽揣鍗曟槑缁嗗彲鑳芥棤鍏宠仈璁㈠崟锛?|
| 鏂板閰嶇疆 | system_configs | discrepancy_auto_create_loss | 宸紓鎶ユ崯鑷姩鐢熸垚鎹熻€楀崟寮€鍏筹紙inventory 缁勶紝榛樿寮€鍚級 |
| 鏂板閰嶇疆 | system_configs | audit_discrepancy | 宸紓鍗曠櫥璁?澶勭悊/鍥炲簱瀹¤寮€鍏筹紙audit 缁勶紝榛樿寮€鍚級 |

### 鏉冮檺璁捐

鎸夐挳绾ф帶鍒跺鐢ㄧ幇鏈夋潈闄愮偣锛屾湭鏂板锛氬鐞嗗喅绛栨寜鎸夐挳鏄犲皠锛堣ˉ璐р啋delivery.discrepancy.restock锛岄€€娆?鎵ｆ鈫抮efund锛屾姤鎹熲啋writeoff锛屼笉璁♀啋浠讳竴鍐崇瓥鏉冮檺锛夛紝鍥炲簱鈫抮estock锛涘鏍哥被寮€鍏宠蛋 system_configs 閰嶇疆銆?

### Git 淇℃伅

| 椤圭洰 | 鍊?|
| :--- | :--- |
| 鍒嗘敮 | bugfix |
| 鎻愪氦 | 瑙佹湰娆℃彁浜?|
| 楠岃瘉 | 娴嬭瘯 15 閫氳繃 / 1 澶辫触锛圲nitConversionServiceTest 涓夌骇鎹㈢畻鏂█涓烘棦鏈夐棶棰橈紝涓庢湰娆℃棤鍏筹級锛汸int 閫氳繃锛泇iew:cache 閫氳繃 |

---

## V2.0.4 鈥?宸紓鍔熻兘涓€鏈熷姞鍥猴細鍗曟嵁婧簮瀛楁 + 搴撳瓨鏃ュ織璇箟鍖?+ 鎹熻€楁墽琛屾墸搴撳瓨淇

> 寮€鍙戣€咃細Kimi锛堝樊寮?搴撳瓨閾捐矾锛夈€傚悓鏈熷彟涓€宸ュ叿瀹屾垚鍗曟嵁鐘舵€侀攣瀹氭敼閫狅紙瑙?`f6e5160` / `ca2bf51`锛夈€?
> 鍐崇瓥渚濇嵁锛歚docs/research/11_宸紓鎹熻€楃暀鐥曚笌璐﹀姟鎸佸钩璁捐.md` 绗?11 绔狅紙鍐崇瓥 1 鏂规 A 鍏ㄩ噺瀛楁 / 鍐崇瓥 3 鎹熻€楁槑缁嗘函婧?/ 鍐崇瓥 4 鏂规 B 鏃ュ織绫诲瀷缁嗗垎鐢辫皟鐢ㄦ柟鏄惧紡浼犲叆锛夈€?

### 鍙樻洿姒傝堪

1. **淇涓ラ噸 Bug**锛氭崯鑰楀崟鍒楄〃椤点€屾墽琛屻€嶇洿鎺ユ敼鐘舵€佷笉鎵ｅ簱瀛橈紝鏀逛负璋冪敤 `LossOrderService::execute()` 璧板簱瀛樻墸鍑忋€?
2. **宸紓鍗曡嚜鍖呭惈涓婁笅鏂?*锛歚discrepancies` 琛ㄦ柊澧?8 涓叧鑱斿瓧娈碉紝鍚勭幆鑺傜櫥璁版椂鍥炲～锛屽鐞?瀵硅处涓嶅啀渚濊禆闂存帴鍏宠仈銆?
3. **鎹熻€楁槑缁嗗彲婧簮宸紓鍗?*锛歚loss_order_items.discrepancy_id`锛屽樊寮傛姤鎹熻嚜鍔ㄥ啓鍏ャ€?
4. **搴撳瓨鏃ュ織璇箟鍖?*锛氭柊澧炵被鍨?7 鎶ユ崯鍑哄簱 / 8 鎷掓敹鍥炲簱 / 9 鍞悗閫€搴擄紙棰勭暀锛夛紝`stockIn/stockOut` 鏀寔鏄惧紡浼犵被鍨嬶紝鎹熻€楁墽琛屼笌鎷掓敹鍥炲簱宸查€傞厤銆?

### 鍙樻洿鏄庣粏

| 鎿嶄綔 | 鏂囦欢 | 璇存槑 |
| :--- | :--- | :--- |
| 淇 | Loss/LossOrderList::execute | 鏀硅蛋 `LossOrderService::execute()`锛屾墽琛屾椂鐪熷疄鎵ｅ噺搴撳瓨锛堜簨鍔″唴锛?|
| 璋冩暣 | 000011_create_discrepancy_tables | 鏂板 purchase_order_id / picking_task_id / delivery_task_id / delivery_note_item_id / sku_id / warehouse_id / unit_id / unit_quantity + 4 涓储寮?|
| 璋冩暣 | 000008_create_loss_tables | loss_order_items 鏂板 discrepancy_id + 绱㈠紩 |
| 瀹屽杽 | Models/Discrepancy | 鏂板瓧娈?fillable/casts + sku() 鍏宠仈 |
| 瀹屽杽 | Models/LossOrderItem | discrepancy_id fillable/casts |
| 瀹屽杽 | Models/InventoryLog | 鏂板 TYPE_LOSS_REPORT=7 / TYPE_REJECT_RETURN=8 / TYPE_AFTER_SALES_RETURN=9 + 绫诲瀷鏄犲皠 |
| 瀹屽杽 | Services/InventoryService | stockIn/stockOut 鏂板 `?int $type` 鍙傛暟锛堥粯璁?TYPE_IN/TYPE_OUT锛屽悜鍚庡吋瀹癸級 |
| 瀹屽杽 | Services/LossOrderService | execute 浼?TYPE_LOSS_REPORT锛沜reate 鍐欏叆鏄庣粏 discrepancy_id |
| 瀹屽杽 | Services/DiscrepancyService | create 鎺ユ敹骞跺洖濉?8 涓叧鑱斿瓧娈碉紱resolveSkuId 浼樺厛鐩磋 sku_id锛沜reateLossOrder 浼?discrepancy_id锛況estockToWarehouse 浼?TYPE_REJECT_RETURN |
| 瀹屽杽 | Inventory/PickingTaskDetail | 鎷ｈ揣宸紓鐧昏鍥炲～ picking_task_id / sku_id / warehouse_id |
| 瀹屽杽 | Delivery/DeliveryNoteDetail | 瀹炴敹宸紓鐧昏鎸夎鍗?SKU 鍙嶆煡鍥炲～ order_item_id锛屽洖濉?delivery_note_item_id / sku_id |
| 瀹屽杽 | Delivery/DiscrepancyList | 澶勭悊寮圭獥 SKU 浼樺厛鐩磋宸紓鍗?sku 鍏宠仈 |
| 娴嬭瘯 | tests/Feature/DiscrepancyServiceTest | 琛ュ叧鑱斿瓧娈靛洖濉€佹崯鑰楁槑缁?discrepancy_id銆佹棩蹇楃被鍨?7/8 鏂█锛涙柊澧炲叏閾捐矾鐢ㄤ緥锛堝樊寮傗啋鎶ユ崯鈫掑鏍糕啋鎵ц鈫掑簱瀛樻墸鍑忥級 |

### 鍐崇瓥璁板綍

| 鍐崇瓥 | 閫夋嫨 | 璇存槑 |
| :--- | :--- | :--- |
| 宸紓鍗曞叧鑱斿瓧娈?| 鍏ㄩ噺 8 瀛楁 | 宸紓鍗曡嚜鍖呭惈锛屽璐︽晥鐜囨渶楂?|
| 鏃ュ織绫诲瀷浼犲叆鏂瑰紡 | 璋冪敤鏂规樉寮忎紶 | 鎺у埗绮剧‘锛岄粯璁ゅ弬鏁颁繚鎸佸悜鍚庡吋瀹?|
| 鍥炲簱瀹℃牳闂搁棬 | 涓嶅姞锛堢幇鐘讹級 | 澶勭悊鍙板洖搴撴搷浣滀汉鍗充粨绠＄‘璁わ紝浠呭璁＄暀鐥曪紱浜屾湡鍙姞寮€鍏?|

### Git 淇℃伅

| 椤圭洰 | 鍊?|
| :--- | :--- |
| 鍒嗘敮 | bugfix锛堝樊寮傛崯鑰楀伐浣滃垎鏀紝寰呯敤鎴风‘璁ゅ悗鍚堝苟 main锛?|
| 鎻愪氦 | 瑙佹湰娆℃彁浜?|
| 楠岃瘉 | 娴嬭瘯 16 閫氳繃 / 1 澶辫触锛圲nitConversionServiceTest 鏃㈡湁闂锛夛紱Pint 閫氳繃锛泇iew:cache 閫氳繃锛涘紑鍙戝簱闇€ migrate:fresh --seed 鐢熸晥鏂板瓧娈?|

---

## V2.0.5 鈥?宸紓娴佺▼寮€鍏冲寲 + 鍏佸樊闃堝€硷細鍥涚幆鑺傜嫭绔嬬鎺т笌宸紓灏辫繎鍙

> 寮€鍙戣€咃細Kimi銆傚喅绛栦緷鎹細鐢ㄦ埛鎷嶆澘銆屽叏鐜妭榛樿瀹芥澗妯″紡 + 鍏佸樊闃堝€兼湰鏈熻惤鍦帮紝闃堝€兼敮鎸佽嚜瀹氫箟銆嶏紱`docs/research/11_宸紓鎹熻€楃暀鐥曚笌璐﹀姟鎸佸钩璁捐.md` 宸紓绠℃帶绔犺妭銆?

### 鍙樻洿姒傝堪

1. **鍥涚幆鑺傚樊寮傜瓥鐣ュ紑鍏?*锛氶噰璐叆搴?/ 鎷ｈ揣 / 閰嶉€佸垎璐?/ 瀹炴敹绛炬敹鍚勬湁鐙珛銆屽厑璁稿樊寮傘€嶅紑鍏筹紙榛樿鍏ㄥ紑=瀹芥澗妯″紡锛夈€傚叧闂嵆涓ユ牸妯″紡锛氳鐜妭鏀跺彂蹇呴』涓€鑷达紝褰曞叆宸紓鐩存帴闃绘柇骞舵彁绀哄嚭鍙ｏ紙璋冩暣璁㈠崟 / 鍞悗閫€璐?/ 绠＄悊鍛樺洖閫€锛夈€?
2. **鍏佸樊闃堝€硷紙%锛?*锛氶噰璐?/ 鎷ｈ揣 / 瀹炴敹鍚勮嚜鍙厤缃槇鍊硷紙0~100锛岄粯璁?0=浠讳綍宸紓閮界敓鎴愬崟鎹級銆傚樊寮傜巼 鈮?闃堝€兼椂鍙爣璁扮暀鐥曪紙鏄庣粏鏍囧樊寮?/ 璁板綍宸紓鏁伴噺锛夛紝涓嶇敓鎴愬樊寮傚崟 / 鎹熻€楀崟锛涜秴闃堝€兼甯哥敓鎴愩€?
3. **宸紓灏辫繎鍙**锛氭嫞璐ф€诲崟璇︽儏鏂板銆屾湰浠诲姟宸紓鍗曘€嶅尯鍧楋紱閫佽揣鍗曟槑缁嗚鏍囨敞宸紓鍗曞彿涓庡鐞嗙姸鎬侊紱閲囪喘鍗曟槑缁嗗瓙琛屾樉绀哄樊寮傛暟閲忎笌鍏宠仈鎹熻€楀崟鍙凤紙闃堝€煎唴鏄剧ず銆屼粎鐣欑棔銆嶏級锛涚櫥璁板樊寮傚悗鐨?toast 鍧囧甫宸紓鍗曞彿銆?
4. **閲囪喘鍗曞畬鎴愭牎楠岄槇鍊煎寲**锛氶槇鍊煎唴鐣欑棔鐨勫叆搴撳樊寮備笉鍐嶉樆鏂噰璐崟瀹屾垚锛堟鍓?loss_order_id 涓虹┖鍗抽樆鏂紝浼氬鑷撮槇鍊煎唴宸紓姘歌繙鏃犳硶瀹屾垚锛夈€?

### 鍙樻洿鏄庣粏

| 鎿嶄綔 | 鏂囦欢 | 璇存槑 |
| :--- | :--- | :--- |
| 璋冩暣 | 000013_create_system_tables | 鏂板 8 涓厤缃」锛歩nventory 缁?purchase/picking 寮€鍏?闃堝€硷紙sort 34-37锛夛紝delivery 缁?delivery/receiving 寮€鍏?闃堝€硷紙sort 13-16锛夛紱閰嶇疆璁℃暟娉ㄩ噴 26鈫?4 |
| 瀹屽杽 | Inventory/PickingTaskDetail::savePickedQuantity | 涓ユ牸妯″紡闃绘柇锛堜笉淇濆瓨锛夛紱宸紓鐜?鈮?闃堝€煎彧鍒嗘憡鏍囪涓嶅缓宸紓鍗曪紱瓒呴槇鍊煎缓鍗曞苟鏀堕泦鍗曞彿锛宼oast 鍒嗕笁绉嶅満鏅紙闃堝€肩暀鐥?寤哄崟甯﹀崟鍙?姝ｅ父锛?|
| 瀹屽杽 | Delivery/DeliveryNoteDetail::confirmItemDelivery | 涓ユ牸妯″紡闃绘柇锛堜笉淇濆瓨鏄庣粏锛夛紱闃堝€煎唴鍙爣璁?STATUS_DISCREPANCY 涓嶅脊绐楋紱瓒呴槇鍊肩淮鎸佸脊绐楃櫥璁?|
| 瀹屽杽 | Delivery/DeliveryNoteDetail::markDelivered | 鍒嗚揣鍓嶆鏌ュ樊寮傛槑缁嗭細涓ユ牸妯″紡闃绘柇锛屽鏉炬ā寮?toast 璀﹀憡宸紓鏉℃暟鍚庢斁琛?|
| 瀹屽杽 | Delivery/DeliveryNoteDetail::saveItemDiscrepancy / render | toast 甯﹀樊寮傚崟鍙凤紱render 杈撳嚭鏄庣粏绾у樊寮傚崟鏄犲皠锛堟寜 delivery_note_item_id锛?|
| 瀹屽杽 | Services/PurchaseService::stockIn | 涓ユ牸妯″紡浠讳竴琛屽疄闄呪墵閲囪喘鏁伴噺鍗?throw 闃绘柇锛沜reateFromDiscrepancy 浼犲叆 purchase 闃堝€?|
| 瀹屽杽 | Services/PurchaseService::complete | 鏈鐞嗗樊寮傛牎楠屾敼涓洪槇鍊兼劅鐭ワ細宸紓鐜?鈮?闃堝€艰涓哄凡鐣欑棔澶勭悊 |
| 瀹屽杽 | Services/LossOrderService::createFromDiscrepancy | 鏂板 `?int $thresholdPct` 鍙傛暟锛堥粯璁?null 涓嶈繃婊わ紝鍚戝悗鍏煎锛夛紝鎸夊樊寮傜巼杩囨护闃堝€煎唴鏄庣粏 |
| 瀹屽杽 | Inventory/PickingTaskDetail::render + blade | 銆屾湰浠诲姟宸紓鍗曘€嶅尯鍧楋紙鍗曞彿/SKU/绫诲瀷/搴旀嫞/瀹炴嫞/宸紓閲?鐘舵€?鍘熷洜 + 澶勭悊鍙伴摼鎺ワ級 |
| 瀹屽杽 | delivery-note-detail.blade | 鏄庣粏琛岀姸鎬佸垪涓嬫爣娉ㄥ樊寮傚崟鍙蜂笌澶勭悊鐘舵€?|
| 瀹屽杽 | purchase-order-detail.blade | 宸紓瀛愯琛ュ厖宸紓鏁伴噺涓庢崯鑰楀崟鍙峰睍绀猴紝闃堝€煎唴鏄剧ず銆屼粎鐣欑棔銆?|
| 娴嬭瘯 | tests/Feature/DiscrepancyThresholdTest | 4 鏉＄敤渚嬶細涓ユ牸闃绘柇 / 闃堝€煎厤鍗曪紙鍚畬鎴愪笉闃绘柇锛? 瓒呴槇鍊煎缓鍗?/ 榛樿閰嶇疆鍏滃簳鏂█ |

### 鍐崇瓥璁板綍

| 鍐崇瓥 | 閫夋嫨 | 璇存槑 |
| :--- | :--- | :--- |
| 榛樿妯″紡 | 鍏ㄥ鏉?| 鐢ㄦ埛鎷嶆澘锛涗弗鏍兼ā寮忛€氳繃绯荤粺璁剧疆閫愮幆鑺傚紑鍚?|
| 鍏佸樊闃堝€?| 鏈湡钀藉湴 | 闃堝€煎彲鑷畾涔変慨鏀癸紙0~100%锛夛紝0=浠讳綍宸紓閮界敓鎴愬崟鎹?|
| 閰嶉€佺幆鑺傛寕鐐?| markDelivered 纭鍒嗚揣 | 鍒嗚揣鏄厤閫佷晶鍞竴鏁伴噺纭鍔ㄤ綔 |
| 闃堝€煎唴宸紓鐣欑棔鏂瑰紡 | 鍙爣璁颁笉寤哄崟 | 鏄庣粏鏍囧樊寮傜姸鎬?璁板綍宸紓鏁伴噺锛屼笉浜х敓宸紓鍗曟崯鑰楀崟锛岄噰璐崟鍙甯稿畬鎴?|

### Git 淇℃伅

| 椤圭洰 | 鍊?|
| :--- | :--- |
| 鍒嗘敮 | bugfix锛堝樊寮傛崯鑰楀伐浣滃垎鏀紝寰呯敤鎴风‘璁ゅ悗鍚堝苟 main锛?|
| 鎻愪氦 | 瑙佹湰娆℃彁浜?|
| 楠岃瘉 | 娴嬭瘯 20 閫氳繃 / 1 澶辫触锛圲nitConversionServiceTest 鏃㈡湁闂锛夛紱Pint 閫氳繃锛泇iew:cache 閫氳繃锛涘紑鍙戝簱闇€ migrate:fresh --seed 鐢熸晥鏂伴厤缃?|

---

## V2.0.6 鈥?閲囪喘宸紓鎹熻€楀崟浠呰处鍔＄暀鐥曪紙涓嶆墸搴撳瓨锛? 閲囪喘鍗曞垪琛ㄥ樊寮傝绀哄浘鏍?

> 寮€鍙戣€咃細Kimi銆傜敤鎴锋媿鏉裤€孊 涓轰富 A 鍏滃簳銆嶏細閲囪喘鍏ュ簱宸紓鐨勬崯鑰楀崟瀹℃牳鍗崇粓鎬侊紝鏈嶅姟灞傚啀鍏滃簳闃茶鎵ｃ€?

### 鍙樻洿姒傝堪

1. **淇闅愭偅**锛氶噰璐叆搴撳樊寮傝嚜鍔ㄧ敓鎴愮殑鎹熻€楀崟锛屽樊寮傞儴鍒嗙殑璐ф湭鍏ュ簱锛屻€屾墽琛屻€嶅啀鎵ｅ簱瀛樺睘鍙岄噸鎵ｅ噺銆傜幇鏀逛负锛氬垪琛ㄩ〉瀵规潵婧?閲囪喘鍗曠殑鎹熻€楀崟涓嶆樉绀恒€屾墽琛屻€嶆寜閽紙B 涓轰富锛夛紝瀹℃牳閫氳繃鍚庡彲鐩存帴銆屽叧闂€嶏紱`LossOrderService::execute()` 瀵规潵婧?閲囪喘鍗曡烦杩囧簱瀛樻墸鍑忥紙A 鍏滃簳锛岄槻鎺ュ彛璇皟锛夈€?
2. **閲囪喘鍗曞垪琛ㄨ绀哄浘鏍?*锛氬崟鍙锋梺璀︾ず鍥炬爣鍒嗚壊鈥斺€旂孩鑹?宸茬敓鎴愭崯鑰楀崟寰呭鏍革紝姗欒壊=鍏佸樊闃堝€煎唴浠呯暀鐥曪紱hover 鎻愮ず宸紓鏄庣粏鏉℃暟涓庣煭缂烘€婚噺銆?

### 鍙樻洿鏄庣粏

| 鎿嶄綔 | 鏂囦欢 | 璇存槑 |
| :--- | :--- | :--- |
| 瀹屽杽 | Services/LossOrderService::execute | 鏉ユ簮=閲囪喘鍗曡烦杩?stockOut锛屼粎鐣欒处锛堟敞閲婅鏄庨槻鍙岄噸鎵ｅ噺锛?|
| 瀹屽杽 | Loss/LossOrderList::close | 鏉ユ簮=閲囪喘鍗曚笖宸插鏍哥殑鎹熻€楀崟鍏佽鐩存帴鍏抽棴 |
| 瀹屽杽 | loss-order-list.blade | 鏉ユ簮=閲囪喘鍗曢殣钘忔墽琛屾寜閽紱瀹℃牳鍚庢樉绀哄叧闂寜閽紙title 璇存槑浠呯暀鐥曪級 |
| 瀹屽杽 | Purchase/PurchaseOrderList + blade | withSum/withCount 棰勭粺璁″樊寮傦紱璀︾ず鍥炬爣鍒嗙孩锛堝緟瀹℃牳鎹熻€楀崟锛?姗欙紙浠呯暀鐥曪級涓よ壊 + hover 鎽樿 |
| 娴嬭瘯 | tests/Feature/DiscrepancyThresholdTest | 鏂板鐢ㄤ緥锛氶噰璐潵婧愭崯鑰楀崟鎵ц鍚庡簱瀛樹粛涓哄疄鏀舵暟閲?|

### Git 淇℃伅

| 椤圭洰 | 鍊?|
| :--- | :--- |
| 鍒嗘敮 | bugfix锛堝緟鐢ㄦ埛纭鍚庡悎骞?main锛?|
| 鎻愪氦 | 瑙佹湰娆℃彁浜?|
| 楠岃瘉 | 娴嬭瘯 21 閫氳繃 / 1 澶辫触锛圲nitConversionServiceTest 鏃㈡湁闂锛夛紱Pint / view:cache 閫氳繃 |

---

## V2.0.7 鈥?渚涘簲鍟嗙粨绠椾慨澶嶏細鍛ㄦ湡缁撶畻妯″紡钀藉湴 + 璐﹀姟鎸佸钩鑱斿姩

> 寮€鍙戣€咃細Kimi銆傜敤鎴锋媿鏉匡細鏂瑰悜涓€锛堣创鍥炶〃缁撴瀯鐨勫懆鏈熺粨绠楋級+ 鍙ｅ緞 A锛堝簲浠?瀹為檯鍏ュ簱閲戦鈭掗€€璐р垝鏈嶅姟璐癸級銆?

### 鍙樻洿姒傝堪

1. **淇鏂**锛氱粨绠楃粍浠舵寜琛ㄤ腑涓嶅瓨鍦ㄧ殑骞诲奖瀛楁锛坧urchase_order_id/settlement_date/note锛夊疄鐜帮紝鍒涘缓缁撶畻鍗曞繀鎶?SQL 閿欒銆傞噸鍐欎负琛ㄧ粨鏋勮璁＄殑鍛ㄦ湡缁撶畻妯″紡锛氫緵搴斿晢 + 缁撶畻鍛ㄦ湡 鈫?鑷姩姹囨€诲懆鏈熷唴宸插叆搴?宸插畬鎴愰噰璐崟锛堟寜鍏ュ簱鏃堕棿锛夌敓鎴愮粨绠楁槑缁嗭紝宸茬粨绠楅噰璐崟闃查噸澶嶈鍏ャ€?
2. **璐﹀姟鎸佸钩锛堝彛寰?A锛?*锛氬簲浠?= 危 瀹為檯鍏ュ簱閲戦 鈭?鍛ㄦ湡鍐呭凡瀹屾垚閲囪喘閫€璐у疄闄呭嚭搴撻噾棰?鈭?鏈嶅姟璐广€傚叆搴撳樊寮傛寜瀹炴敹璁′环澶╃劧鎸佸钩锛堝皯鏀剁殑璐т笉浠橀挶锛夛紝宸紓鎹熻€楀崟浣滅暀鐥曡瘉鎹€?
3. **浠樻闂幆**锛氱Щ闄や笉寤轰粯娆捐褰曠殑 confirmPayment 鎹峰緞锛涢儴鍒嗕粯娆剧粺涓€鍐?settlement_payments + 绱 paid_amount + 婊￠鑷姩缁撴竻锛涙柊澧炶秴浠樻嫤鎴紱浠呭緟缁撶畻鐘舵€佸彲鍒犻櫎锛堟槑缁嗚繛甯︽竻鐞嗭級銆?
4. **璇︽儏寮圭獥**锛氳ˉ缁撶畻鏄庣粏锛堥噰璐崟鍙?璁′环閲戦锛屾湁宸紓鏍囨敞鍘熻鍗曢噾棰濓級涓庝粯娆捐褰曪紱鍒楄〃鍒楁敼涓哄懆鏈?姹囨€?閫€璐ф墸鍑?鏈嶅姟璐?搴斾粯/宸蹭粯銆?
5. **妯″瀷娓呯悊**锛歋upplierSettlement 绉婚櫎骞诲奖 fillable/casts 涓?purchaseOrder 鍏宠仈锛堢粨绠楀崟涓庨噰璐崟涓轰竴瀵瑰锛岃蛋 settlement_items锛夈€?

### 鍙樻洿鏄庣粏

| 鎿嶄綔 | 鏂囦欢 | 璇存槑 |
| :--- | :--- | :--- |
| 鏂板 | Services/SupplierSettlementService | generate()锛氬懆鏈熷唴宸插叆搴撻噰璐崟姹囨€伙紙鎺掗櫎宸茬粨绠楋級+ 閫€璐ф墸鍑?+ 鏈嶅姟璐癸紝浜嬪姟寤哄崟+鏄庣粏 |
| 閲嶅啓 | Finance/SupplierSettlementList | 琛ㄥ崟鏀瑰懆鏈熺粨绠楋紱鍒楅厤缃榻愮湡瀹炲瓧娈碉紱浠樻闂幆+瓒呬粯鎷︽埅锛涘垹闄ら檺鍒跺緟缁撶畻 |
| 瀹屽杽 | Models/SupplierSettlement | 娓呯悊骞诲奖瀛楁 fillable/casts/purchaseOrder 鍏宠仈 |
| 瀹屽杽 | supplier-settlement-list.blade | 琛ㄥ崟/鍒楄〃鍒?璇︽儏寮圭獥瀵归綈鍛ㄦ湡缁撶畻锛涗慨澶嶄粯娆捐褰曞尯 SettlementPayment 闈為檺瀹氱被鍚嶉殣鎮?|
| 娴嬭瘯 | tests/Feature/SupplierSettlementTest | 6 鏉＄敤渚嬶細鍛ㄦ湡姹囨€诲彛寰?闃查噸澶嶇粨绠?绌哄懆鏈熸姤閿?缁勪欢琛ㄥ崟寤哄崟/閮ㄥ垎浠樻+瓒呬粯鎷︽埅+缁撴竻/鍒犻櫎闄愬埗 |

### 鍐崇瓥璁板綍

| 鍐崇瓥 | 閫夋嫨 | 璇存槑 |
| :--- | :--- | :--- |
| 缁撶畻妯″紡 | 鍛ㄦ湡缁撶畻锛堟柟鍚戜竴锛?| 琛ㄧ粨鏋?绉嶅瓙鏁版嵁鍧囨寜鍛ㄦ湡璁捐锛岀粍浠舵槸璺戝亸鏂?|
| 搴斾粯鍙ｅ緞 | 瀹為檯鍏ュ簱閲戦锛堝彛寰?A锛?| 宸紓澶╃劧鎸佸钩锛屾墸鍑忚矾寰勬渶鐭?|
| 閫€璐ф墸鍑忓彛寰?| 宸插畬鎴愰€€璐х殑瀹為檯鍑哄簱閲戦 | 涓庡叆搴撳彛寰勫绉?|
| 浠樻瀹℃牳娴?| 浜屾湡 | approval_status 瀛楁淇濈暀锛屾殏涓嶆帴瀹℃牳绠＄悊 |

### Git 淇℃伅

| 椤圭洰 | 鍊?|
| :--- | :--- |
| 鍒嗘敮 | feature/discrepancy-loss |
| 鎻愪氦 | 瑙佹湰娆℃彁浜?|
| 楠岃瘉 | 娴嬭瘯 27 閫氳繃 / 1 澶辫触锛圲nitConversionServiceTest 鏃㈡湁闂锛夛紱Pint / view:cache 閫氳繃 |

---

## V2.0.8 鈥?渚涘簲鍟嗙粨绠椾氦浜掍慨澶?+ 婕旂ず鏁版嵁瀵归綈

> 寮€鍙戣€咃細Kimi銆傜敤鎴峰疄娴嬪弽棣堜慨澶嶃€?

### 鍙樻洿姒傝堪

1. **淇寮圭獥鍏抽棴澶辨晥**锛氳鎯?浠樻寮圭獥浣跨敤 `wire:click="showXxx = false"` 璧嬪€艰娉曪紙Livewire 涓嶆墽琛岋級锛? 澶勫叏閮ㄦ敼涓?`closeDetailModal()` / `closePaymentModal()` 鏂规硶璋冪敤銆?
2. **缁撶畻鍗曞彿閾炬帴鍖?*锛氫笌鍏朵粬鍒楄〃椤典竴鑷粹€斺€旇摑鑹插瓧浣?hover 涓嬪垝绾匡紝鐐瑰嚮鍗曞彿鎵撳紑璇︽儏寮圭獥锛涚Щ闄ゆ搷浣滃垪鐨勭溂鐫涢瑙堝浘鏍囥€?
3. **婕旂ず鏁版嵁瀵归綈鍛ㄦ湡缁撶畻**锛歅O-20260725-00001 琛?stocked_at/completed_at 涓庡湡璞嗘槑缁?discrepancy_quantity=200锛涙柊澧?PO-20260726-00003锛堜赴娑﹁倝涓毬锋棤宸紓 160000锛変笌 PO-20260727-00004锛堜赴娑﹁倝涓毬烽矞铏剧煭缂?200 鏈夊樊寮傦紝璁?120000 瀹炴敹 100000锛夛紝涓ゅ崟宸插叆搴擄紱SS-20260728-00002 琛ヤ袱鏉＄粨绠楁槑缁嗭紝姹囨€?260000 鈭?鏈嶅姟璐?3000 = 搴斾粯 257000锛屼笌缁撶畻鍗曞ご涓€鑷淬€?

### Git 淇℃伅

| 椤圭洰 | 鍊?|
| :--- | :--- |
| 鍒嗘敮 | feature/discrepancy-loss |
| 鎻愪氦 | 瑙佹湰娆℃彁浜?|
| 楠岃瘉 | 娴嬭瘯 27 閫氳繃 / 1 澶辫触锛堟棦鏈夐棶棰橈級锛汸int / view:cache 閫氳繃锛沵igrate:fresh + DemoDataSeeder 閲嶈窇楠岃瘉鏁版嵁涓€鑷?|

---

## V2.0.9 鈥?閲囪喘閫€璐у弻妯″紡锛氬叧鑱旈€€璐?+ 鏅€氶€€璐?

> 寮€鍙戣€咃細Kimi銆傜敤鎴锋媿鏉匡細1A锛堟暟閲忎笂闄?瀹為檯鍏ュ簱鏁伴噺锛夈€?A锛堟櫘閫氶€€璐т緵搴斿晢+浠撳簱蹇呭～锛夈€?A锛堟櫘閫氶€€璐у崟浠锋槑缁嗚鎵嬪伐濉啓锛夈€?

### 鍙樻洿姒傝堪

1. **鍙屾ā寮忛€€璐?*锛氭柊澧?`return_type`锛? 鏅€?/ 2 鍏宠仈锛岄粯璁ゅ叧鑱旓級銆傚叧鑱旈€€璐ф部鐢ㄩ噰璐崟鏄庣粏璋冨彇锛涙櫘閫氶€€璐т笉鍏宠仈閲囪喘鍗曪紙`purchase_order_id` 鍙┖锛夛紝鏄庣粏瀹屽叏鑷畾涔夈€?
2. **鍏宠仈閫€璐т笂闄愪慨姝?*锛氶€€璐ф暟閲忔嫤鎴彛寰勪粠璁㈣喘鏁伴噺鏀剁揣涓?*瀹為檯鍏ュ簱鏁伴噺**鈥斺€斿惈鍏ュ簱宸紓鏃讹紝灏戦€佺殑璐т笉鍏佽琚€€锛屼繚璇侀€€鐨勮揣鍦ㄥ簱閲屻€?
3. **鏅€氶€€璐ф槑缁嗗伐鍏?*锛氬垱寤哄脊绐楁敮鎸侀€愯娣诲姞锛圫KU 涓嬫媺 + 鏁伴噺 + 鍗曚环(鍏? + 鍘熷洜锛夈€佷笅杞?Excel 瀵煎叆妯℃澘銆佹寜 SKU 缂栫爜鍖归厤瀵煎叆锛堟湭璇嗗埆缂栫爜姹囨€绘彁绀猴級銆佸鍑烘槑缁?CSV锛堝甫 BOM锛夈€?
4. **鏃犲崟娴佺▼閫傞厤**锛氬嚭搴撲粨搴撴櫘閫氶€€璐у彇閫€璐у崟澶?`warehouse_id`锛涘畬鎴?浣滃簾鏃?`purchaseOrder?->updateReturnStatus()` 绌哄畨鍏紝鏅€氶€€璐т笉瑙﹀彂閲囪喘鍗曢€€璐х姸鎬佹洿鏂般€?
5. **缁撶畻鍙ｅ緞涓嶅彉**锛氫緵搴斿晢缁撶畻 return_amount 鎸?supplier + completed_at 姹囨€诲凡瀹屾垚閫€璐э紝澶╃劧鍏煎鏅€氶€€璐э紝鏃犻渶鏀瑰姩銆?
6. **璇︽儏椤甸€傞厤**锛氭櫘閫氶€€璐ф樉绀恒€屾櫘閫氶€€璐э紙鏃犲叧鑱旈噰璐崟锛夈€嶆浛浠ｉ噰璐崟閾炬帴锛屾柊澧炵被鍨嬪窘绔犮€?

### 鍙樻洿鏄庣粏

| 鎿嶄綔 | 鏂囦欢 | 璇存槑 |
| :--- | :--- | :--- |
| 淇敼 | 杩佺Щ 000016_create_return_tables | `purchase_order_id` / `purchase_return_items.purchase_order_item_id` 鍙┖锛涙柊澧?`return_type` tinyint default 2 |
| 瀹屽杽 | Models/PurchaseReturn | TYPE_NORMAL/TYPE_LINKED 甯搁噺銆乼ypeMap銆乺eturn_type_label 璁块棶鍣?|
| 瀹屽杽 | Services/PurchaseReturnService | 鍘?create() 鏀瑰悕 createLinked()锛堣秴瀹為檯鍏ュ簱鏁伴噺鎷︽埅锛夛紱鏂板 createNormal()锛泂hip/complete/cancel 鏃犲崟绌哄畨鍏?|
| 閲嶅啓 | Livewire/Purchase/PurchaseReturnList | 绫诲瀷鍒囨崲銆乫ormItems 鏄庣粏琛岀紪杈戙€乤ddItemRow/removeItemRow銆丒xcel 瀵煎叆/妯℃澘涓嬭浇/鏄庣粏瀵煎嚭銆佸垪琛ㄦ柊澧炵被鍨嬪垪 |
| 淇敼 | purchase-return-list.blade | 鍒涘缓寮圭獥鍙屾ā寮?UI锛涚被鍨嬪窘绔犲垪 |
| 淇敼 | purchase-return-detail.blade | 鏅€氶€€璐ф棤閲囪喘鍗曟椂鐨勬樉绀轰笌绫诲瀷寰界珷 |
| 娴嬭瘯 | tests/Feature/PurchaseReturnFlowTest | 7 鏉＄敤渚嬶細鍏宠仈鍒涘缓璁′环/瓒呭疄鍏ユ嫤鎴?鏈叆搴撴嫤鎴?鏅€氬垱寤?绌烘槑缁嗕笌闆舵暟閲忔嫤鎴?鏅€氶€€璐у叏娴佺▼鎵ｅ簱瀛?缁撶畻姹囨€诲彛寰?|
| 鏂囨。 | docs/07銆乨ocs/12 | 閲囪喘閫€璐х珷鑺傝ˉ鍙屾ā寮忕敤渚嬩笌鎿嶄綔璇存槑锛?7 鐢ㄤ緥缁熻 122鈫?26锛?|

### 鍐崇瓥璁板綍

| 鍐崇瓥 | 閫夋嫨 | 璇存槑 |
| :--- | :--- | :--- |
| 1A 鍏宠仈閫€璐ф暟閲忎笂闄?| 瀹為檯鍏ュ簱鏁伴噺 | 閫€鐨勮揣蹇呴』鍦ㄥ簱閲岋紝涓庡叆搴撳樊寮傚彛寰勪竴鑷?|
| 2A 鏅€氶€€璐у繀濉」 | 渚涘簲鍟?+ 浠撳簱 | 渚涘簲鍟嗗繀濉繚璇佺粨绠楁眹鎬诲彛寰勶紱浠撳簱蹇呭～淇濊瘉鍑哄簱鎵ｅ簱瀛樻湁鐩爣 |
| 3A 鏅€氶€€璐у崟浠?| 鏄庣粏琛屾墜宸ュ～鍐?| 鍏冭緭鍏ャ€佸帢瀛樺偍锛岀伒娲昏鐩栧巻鍙?闆舵暎閫€璐т环鏍?|

### Git 淇℃伅

| 椤圭洰 | 鍊?|
| :--- | :--- |
| 鍒嗘敮 | feature/discrepancy-loss |
| 鎻愪氦 | 瑙佹湰娆℃彁浜?|
| 楠岃瘉 | 鏂板娴嬭瘯 7 閫氳繃锛涘叏閲?34 閫氳繃 / 1 澶辫触锛圲nitConversionServiceTest 鏃㈡湁闂锛夛紱Pint / view:cache 閫氳繃锛沵igrate:fresh + DemoDataSeeder 閲嶈窇楠岃瘉 |

---

## V2.0.10 鈥?閲囪喘閫€璐т氦浜掗噸鏋勶細澶村厛琛?+ 璇︽儏椤电淮鎶ゆ槑缁?

> 寮€鍙戣€咃細Kimi銆傜敤鎴峰疄娴嬪弽棣堬細鍒涘缓寮圭獥澶皬锛屼笉閫傚悎鏁寸悊鏄庣粏鈥斺€旀敼涓哄厛瀛樺崟澶淬€佷繚瀛樺悗璺宠鎯呴〉鍐嶆坊鍔?瀵煎叆/瀵煎嚭鏄庣粏銆?

### 鍙樻洿姒傝堪

1. **涓ゆ寤哄崟**锛氭柊澧炲脊绐楀彧濉崟澶达紙绫诲瀷 / 鍏宠仈閲囪喘鍗?鎴?渚涘簲鍟?浠撳簱 / 鍘熷洜澶囨敞锛夛紝淇濆瓨鍚庤嚜鍔ㄨ烦杞€€璐у崟璇︽儏椤点€?
2. **鏄庣粏缁存姢杩佺Щ鑷宠鎯呴〉**锛氬緟瀹℃牳鐘舵€佷笅鏄庣粏琛ㄦ牸鍙紪杈戔€斺€旀坊鍔?鍒犻櫎琛屻€佹櫘閫氶€€璐т笅杞藉鍏ユā鏉?+ Excel/CSV 瀵煎叆锛堟寜 SKU 缂栫爜鍖归厤锛? 瀵煎嚭鏄庣粏 CSV锛屻€屼繚瀛樻槑缁嗐€嶆暣鍗曟浛鎹㈢敓鏁堬紱闈炲緟瀹℃牳鐘舵€佸彧璇汇€?
3. **鏈嶅姟灞傞厤濂?*锛歝reateLinked/createNormal 鍏佽绌哄ご鍒涘缓锛涙柊澧?updateItems()锛堜粎寰呭鏍革紝鍏堝垹鍚庡缓 + 閲嶇畻閲戦锛屽叧鑱旀ā寮忓鐢ㄥ疄闄呭叆搴撴暟閲忎笂闄愭牎楠屼笌閲嶅琛屾嫤鎴級锛沘pprove() 鎷︽埅鏃犳槑缁嗗崟鎹紙"璇峰厛娣诲姞閫€璐ф槑缁嗗啀瀹℃牳"锛夈€?
4. **鍏宠仈妯″紡閫夎浜や簰**锛氳鎯呴〉鏄庣粏琛屼粠閲囪喘鍗曟槑缁嗕笅鎷夐€夋嫨锛岃嚜鍔ㄥ甫鍑?SKU銆佸彲閫€涓婇檺锛堝疄闄呭叆搴撴暟閲忥級銆佸崟浠枫€?

### 鍙樻洿鏄庣粏

| 鎿嶄綔 | 鏂囦欢 | 璇存槑 |
| :--- | :--- | :--- |
| 瀹屽杽 | Services/PurchaseReturnService | 绌哄ご鍒涘缓锛泆pdateItems()锛涙槑缁嗚鍒涘缓鎻愬彇涓?createLinkedItem/createNormalItem锛涘鏍告牎楠屾槑缁嗛潪绌?|
| 閲嶅啓 | Livewire/Purchase/PurchaseReturnList | 绉婚櫎鏄庣粏琛?瀵煎叆瀵煎嚭閫昏緫锛泂ave() 鍒涘缓鍚?redirect 璇︽儏椤?|
| 閲嶅啓 | Livewire/Purchase/PurchaseReturnDetail | 杩佸叆鏄庣粏缂栬緫锛歠ormItems 鍚屾銆乤ddItemRow/pickOrderItem/removeItemRow/importItems/exportItemsTemplate/exportItems/saveItems |
| 淇敼 | purchase-return-list.blade | 寮圭獥鍘婚櫎鏄庣粏鍖猴紝鏀逛负璺宠浆鎻愮ず |
| 淇敼 | purchase-return-detail.blade | 寰呭鏍稿彲缂栬緫鏄庣粏琛ㄦ牸锛堝惈宸ュ叿鏍忥級锛岄潪寰呭鏍稿彧璇?|
| 娴嬭瘯 | tests/Feature/PurchaseReturnFlowTest | 璋冩暣绌哄ご鐢ㄤ緥涓哄鏍告嫤鎴紱鏂板 updateItems 鏁村崟鏇挎崲+鐘舵€佹嫤鎴€佸垪琛ㄧ粍浠跺缓鍗曡烦杞紝鍏?10 鐢ㄤ緥 |
| 鏂囨。 | docs/07銆乨ocs/12 | 閲囪喘閫€璐х敤渚嬫敼涓ゆ娴佺▼+鏂板 TC-PUR-006e 绌哄ご瀹℃牳鎷︽埅锛堢粺璁?126鈫?27锛夛紱鐢ㄦ埛鎵嬪唽 4.7 涓ゆ鎿嶄綔璇存槑 |

### Git 淇℃伅

| 椤圭洰 | 鍊?|
| :--- | :--- |
| 鍒嗘敮 | feature/discrepancy-loss |
| 鎻愪氦 | 瑙佹湰娆℃彁浜?|
| 楠岃瘉 | 閫€璐ф祴璇?10 閫氳繃锛涘叏閲?37 閫氳繃 / 1 澶辫触锛圲nitConversionServiceTest 鏃㈡湁闂锛夛紱Pint / view:cache 閫氳繃 |

---

## V2.0.11 鈥?鍞悗閫€璐ч噸鏋勶細瀵归綈閲囪喘閫€璐с€屽ご鍏堣 + 璇︽儏椤垫槑缁嗐€嶆ā寮?

> 寮€鍙戣€咃細Kimi銆傜敤鎴锋媿鏉匡細鍞悗閫€璐х粺涓€涓洪噰璐€€璐у悓娆句氦浜掋€?

### 鍙樻洿姒傝堪

1. **琛ラ綈缂哄け灞?*锛氬敭鍚庨€€璐ф鍓嶆棤鏈嶅姟灞傘€佹棤璇︽儏椤点€佹槑缁嗕粠涓嶅啓鍏ワ紙鍒涘缓浠呰鍗?鍟嗗+鍘熷洜锛夛紝涓旂粍浠跺啓 `note` 骞诲奖瀛楁锛堣〃瀛楁涓?remark锛岄潤榛樹涪澶憋級鈥斺€旀湰娆″叏閮ㄤ慨澶嶃€?
2. **鏂板缓 OrderReturnService**锛氱┖澶村缓鍗曪紙浠呭凡绛炬敹/宸查攣瀹氳鍗曪紝鍟嗗鑷姩鍙栬嚜璁㈠崟锛夛紱updateItems() 鏁村崟鏇挎崲锛堜粎寰呭鏍革級锛涙暟閲忎笂闄?= 瀹㈡埛瀹炴敹鏁伴噺锛坅ctual_quantity ?: quantity锛夛紝鍗曚环鍙栧疄鏀跺崟浠凤紱approve 鎷︽埅鏃犳槑缁嗗崟鎹紱markReturned / refund锛堥€€娆鹃噾棰?閫€璐ф€婚噾棰濓紝鏄庣粏 refund_amount 鍚屾锛? cancel 鍏ㄧ姸鎬佹祦杞?+ 瀹¤鏃ュ織銆?
3. **鏂板璇︽儏椤?*锛歚/order-returns/{id}` 璺敱 + OrderReturnDetail 缁勪欢鈥斺€旀憳瑕併€佸緟瀹℃牳鍙紪杈戞槑缁嗭紙璁㈠崟鏄庣粏涓嬫媺閫夎锛岃嚜鍔ㄥ甫鍑哄彲閫€涓婇檺/鍗曚环锛夈€佸彧璇绘槑缁嗐€佺姸鎬佹寜閽€佽秴绠″洖閫€銆佹椂闂寸嚎锛堝鏍?閫€璐?閫€娆?浣滃簾鏃堕棿锛夈€?
4. **鍒楄〃椤甸厤濂?*锛氶€€璐у崟鍙疯摑鑹查摼鎺ヨ繘璇︽儏锛涙柊澧炲脊绐楀ご鍏堣锛堥€夎鍗曡嚜鍔ㄥ甫鍟嗗锛屼繚瀛樺悗璺宠鎯呴〉锛夛紱鐘舵€佹搷浣滃叏閮ㄥ鎵樻湇鍔″眰锛涗慨 note鈫抮emark锛堝垪琛ㄥ垪/瀵煎嚭/瀵煎叆鏄犲皠/琛ㄥ崟锛夛紱鍒楄〃鏂板閫€璐ч噾棰濆垪銆?
5. **鏉傞」淇**锛氳縼绉昏ˉ returned_at/refunded_at/cancelled_at 鏃堕棿鎴筹紱helpers 琛?order_return 鐘舵€佸窘绔狅紱婕旂ず鏁版嵁 RT-20260729-00001 澶撮噾棰?6900 鈫?6900000锛堜笌鏄庣粏 500脳13800 涓€鑷达級銆?

### 鍙樻洿鏄庣粏

| 鎿嶄綔 | 鏂囦欢 | 璇存槑 |
| :--- | :--- | :--- |
| 淇敼 | 杩佺Щ 000016_create_return_tables | order_returns 琛?returned_at/refunded_at/cancelled_at |
| 瀹屽杽 | Models/OrderReturn | 涓変釜鏃堕棿鎴冲叆 fillable/casts |
| 鏂板 | Services/OrderReturnService | create/updateItems/approve/markReturned/refund/cancel + 瀹¤鏃ュ織 |
| 閲嶅啓 | Livewire/Order/OrderReturnList | 澶村厛琛屽垱寤?璺宠浆锛涚姸鎬佹祦杞鎵樻湇鍔″眰锛沶ote鈫抮emark 骞诲奖瀛楁淇 |
| 鏂板 | Livewire/Order/OrderReturnDetail | 鏄庣粏缂栬緫锛坅ddItemRow/pickOrderItem/removeItemRow/exportItems/saveItems锛? 鐘舵€佹祦杞?+ 鍥為€€ |
| 淇敼 | order-return-list.blade | 鍗曞彿閾炬帴鍖栵紱寮圭獥澶村厛琛岋紱remark 淇锛涢€€璐ч噾棰濆垪 |
| 鏂板 | order-return-detail.blade | 寰呭鏍稿彲缂栬緫鏄庣粏 + 鍙鏄庣粏 + 鏃堕棿绾?|
| 淇敼 | helpers.php / routes/web.php | order_return 鐘舵€佸窘绔狅紱璇︽儏璺敱 |
| 淇 | Demo/OrderDemoSeeder | RT-20260729-00001 澶撮噾棰濅笌鏄庣粏瀵归綈 |
| 娴嬭瘯 | tests/Feature/OrderReturnFlowTest | 7 鐢ㄤ緥锛氱┖澶村缓鍗?鏈鏀舵嫤鎴?瀹炴敹璁′环+瓒呴噺鎷︽埅/updateItems+鐘舵€佹嫤鎴?绌哄ご瀹℃牳鎷︽埅/鍏ㄦ祦绋嬮€€娆惧悓姝?缁勪欢寤哄崟璺宠浆 |
| 鏂囨。 | docs/07銆乨ocs/12 | TC-ORDER-006 绯诲垪 5 鏉★紙缁熻 127鈫?31锛夛紱鐢ㄦ埛鎵嬪唽 4.8 閲嶅啓 |

### Git 淇℃伅

| 椤圭洰 | 鍊?|
| :--- | :--- |
| 鍒嗘敮 | feature/discrepancy-loss |
| 鎻愪氦 | 瑙佹湰娆℃彁浜?|
| 楠岃瘉 | 鍞悗閫€璐ф祴璇?7 閫氳繃锛涘叏閲忔祴璇曘€丳int銆乿iew:cache 瑙佹彁浜ゆ椂鐘舵€侊紱migrate:fresh + DemoDataSeeder 閲嶈窇楠岃瘉 |

---

## V2.0.12 鈥?閫€璐ц〃鍗?bugfix锛氬師鍥犻潪蹇呭～ + 鍏宠仈閫€璐ф敼浠?

> 寮€鍙戣€咃細Kimi銆傜敤鎴峰疄娴嬪弽棣堜慨澶嶃€?

### 鍙樻洿姒傝堪

1. **閫€璐у師鍥犻潪蹇呭～**锛氶噰璐€€璐э紙鍒涘缓/缂栬緫锛変笌鍞悗閫€璐э紙鍒涘缓锛夎〃鍗曟牎楠岀敱 required 鏀?nullable锛屽脊绐楀幓闄ゅ繀濉槦鏍囥€?
2. **鍏宠仈閫€璐у嚭搴撲粨搴撳紑鏀炬墜閫?*锛氬師绂佺敤鐘舵€佸鑷存棤娉曟敼浠撯€斺€旂幇榛樿甯﹀嚭閲囪喘鍗曞叆搴撲粨搴撱€佸厑璁告敼浠撻€€璐э紱createLinked() 鏂板 warehouseId 鍙傛暟锛涘嚭搴撴墸搴撳瓨缁熶竴浠ラ€€璐у崟澶?warehouse_id 涓哄噯锛堝師鏉ュ叧鑱旈€€璐у彇閲囪喘鍗曚粨搴擄紝浼氬拷鐣ュ崟澶存敼鍔級銆?

### Git 淇℃伅

| 椤圭洰 | 鍊?|
| :--- | :--- |
| 鍒嗘敮 | feature/discrepancy-loss |
| 鎻愪氦 | 瑙佹湰娆℃彁浜?|
| 楠岃瘉 | 鍏ㄩ噺 44 閫氳繃 / 1 澶辫触锛堟棦鏈夐棶棰橈級锛汸int / view:cache 閫氳繃 |

---

## V2.0.13 鈥?鎹熻€楀崟鏄庣粏琛?+ 鎹熻€楃被鍨嬪姛鑳芥€у父閲?

> 寮€鍙戣€咃細TeleAgent銆?8-28 鎹熻€楃鐞嗘敼杩涖€?

### 鍙樻洿姒傝堪

1. **LossOrder 妯″瀷鏂板鍔熻兘鎬ф潵婧愮被鍨嬪父閲?*锛歀OSS_PURCHASE=7(閲囪喘灏戞敹)銆丩OSS_PICKING=8(鎷ｈ揣宸紓)銆丩OSS_DELIVERY=9(閰嶉€佸樊寮?锛屾洿鏂?typeMap()銆乻ourceTypeMap()銆丳HPDoc銆傛墜鍔ㄥ垱寤哄脊绐楃被鍨嬩笅鎷夋浠呮樉绀?1-6锛堝娉ㄥ瀷锛夛紝7-9 鐢辩郴缁熻嚜鍔ㄨ祴鍊笺€傚垪琛ㄧ瓫閫変笅鎷夋鏄剧ず鍏ㄩ儴 9 绉嶇被鍨嬨€?
2. **createFromDiscrepancy() 浣跨敤 LOSS_PURCHASE**锛氶噰璐叆搴撳樊寮傝嚜鍔ㄧ敓鎴愮殑鎹熻€楀崟 loss_type 浠?LOSS_OTHER 鏀逛负 LOSS_PURCHASE锛堜富琛ㄥ拰鏄庣粏涓ゅ锛夈€?
3. **LossOrderList 缁勪欢鏂板鏄庣粏琛岀鐞?*锛歛ddItemRow()/removeItemRow()/updatedFormItems()锛圫KU 閫夋嫨鑷姩甯﹀嚭鎴愭湰浠凤紝cost_price 涓?0 鏃?fallback 鍒?purchase_price锛?formTotalAmountCents 璁＄畻灞炴€с€俹penCreateModal() 鍒涘缓鏃惰嚜鍔ㄦ坊鍔犱竴琛岀┖鏄庣粏锛沷penEditModal() 鍔犺浇宸叉湁鏄庣粏鍒拌〃鍗曘€?
4. **save() 鏀圭敤 LossOrderService::create()**锛氭柊寤烘ā寮忚皟鐢?Service 灞傚垱寤烘崯鑰楀崟锛堝惈鏄庣粏锛夛紝缂栬緫妯″紡鍒犻櫎鏃ф槑缁嗛噸鏂板垱寤猴紝鑷姩璁＄畻 total_amount銆?
5. **寮圭獥 UI 鏀归€?*锛氫粨搴?绫诲瀷/鍘熷洜/澶囨敞鏀逛负涓ゅ垪甯冨眬锛屾柊澧炴崯鑰楁槑缁嗚琛ㄦ牸锛圫KU閫夋嫨+鎴愭湰浠?鏁伴噺+鎹熻€楅噾棰?璐ｄ换鏂?渚涘簲鍟?鍘熷洜+鍒犻櫎琛岋級锛屽簳閮ㄥ悎璁￠噾棰濓紝寮圭獥瀹藉害 max-w-3xl銆傝矗浠绘柟閫?渚涘簲鍟?鏃跺姩鎬佹樉绀轰緵搴斿晢涓嬫媺妗嗐€?

### 璁捐鍐崇瓥
- 鎵嬪姩鍒涘缓寮圭獥绫诲瀷浠呭彲閫?1-6锛屽姛鑳芥€х被鍨?7-9 涓嶅彲鎵嬪姩閫夋嫨
- 鍒楄〃绛涢€夋敮鎸佸叏閮?9 绉嶇被鍨?
- 鎴愭湰浠疯嚜鍔ㄥ甫鍑?SKU 鐨?cost_price锛堝姞鏉冨钩鍧囨垚鏈环锛夛紝涓?0 鏃?fallback 鍒?purchase_price锛屽厑璁告墜鍔ㄤ慨鏀?
- 缂栬緫妯″紡鏄庣粏澶勭悊锛氬垹闄ゆ棫鏄庣粏閲嶆柊鍒涘缓锛堜粎寰呭鏍哥姸鎬佸彲缂栬緫锛屾棤搴撳瓨鍙樺姩椋庨櫓锛?

### 鏁版嵁搴撳彉鏇?
鏃狅紙鏃犳柊澧炲瓧娈碉紝浠呭父閲忓畾涔夊拰涓氬姟閫昏緫鍙樻洿锛?

### 娑夊強鏂囦欢
| 鍙樻洿 | 鏂囦欢 | 璇存槑 |
| :--- | :--- | :--- |
| 淇敼 | app/Models/LossOrder.php | 鏂板甯搁噺 7/8/9 + typeMap/sourceTypeMap 琛ュ厖 |
| 淇敼 | app/Services/LossOrderService.php | createFromDiscrepancy() loss_type 鏀逛负 LOSS_PURCHASE |
| 淇敼 | app/Livewire/Loss/LossOrderList.php | 鏂板鏄庣粏琛岀鐞?+ save() 鏀圭敤 Service + render 浼犻€掑彉閲?|
| 淇敼 | resources/views/livewire/loss/loss-order-list.blade.php | 绛涢€変笅鎷夋 + 寮圭獥 UI 鏀归€?|

### Git 淇℃伅

| 椤圭洰 | 鍊?|
| :--- | :--- |
| 鍒嗘敮 | fix/bugfix-round-1 |
| 楠岃瘉 | PHP 璇硶妫€鏌?3 鏂囦欢閫氳繃锛泇iew:clear + config:clear锛沘dmin:install --seed --reset --force 22 杩佺Щ + 鍏ㄩ儴 Seeder 鏃犻敊璇?|