---
AIGC:
  ContentProducer: '001191110102MAD55U9H0F10002'
  ContentPropagator: '001191110102MAD55U9H0F10002'
  Label: '1'
  ProduceID: '46a27e00-5c19-41b3-af3e-3b032cb51019'
  PropagateID: '46a27e00-5c19-41b3-af3e-3b032cb51019'
  ReservedCode1: '89d02e6b-5571-4551-bd64-e8e5d531850a'
  ReservedCode2: '89d02e6b-5571-4551-bd64-e8e5d531850a'
---

# API 前后端接口文档

对应 PRD 版本：V1.2
对应 FSD 版本：V1.2  
技术栈：Laravel 13 + Livewire 4.x（管理后台服务端渲染）+ Sanctum（小程序 API Token 认证）+ MySQL 8.0 + Redis 7.x

> **注意**：管理后台已从 React SPA 迁移至 Livewire 4.x 服务端渲染，管理后台不再使用 REST API，改为 Livewire 组件直接交互。本文档仅保留小程序端（商家端/司机端）的 REST API 接口定义。  

---

## 1 全局规范

### 1.1 请求基础信息

| 项目 | 规范 |
| :--- | :--- |
| 基础地址 | `/api/v1` |
| 请求格式 | JSON (`Content-Type: application/json`) |
| 认证方式 | Laravel Sanctum Token（请求头 `Authorization: Bearer {token}`） |
| 字符编码 | UTF-8 |
| 时间格式 | ISO 8601（`2026-07-25T10:30:00+08:00`） |
| 分页参数 | `page`（页码，从1开始）、`per_page`（每页条数，默认15，最大100） |
| 金额精度 | 请求/响应统一使用字符串或浮点数，保留2位小数（单价4位） |

### 1.2 统一返回格式

```json
{
  "code": 200,
  "message": "操作成功",
  "data": {}
}
```

分页响应格式：

```json
{
  "code": 200,
  "message": "操作成功",
  "data": {
    "items": [],
    "total": 100,
    "per_page": 15,
    "current_page": 1,
    "last_page": 7
  }
}
```

### 1.3 错误码对照表

| 错误码 | 含义 | 说明 |
| :--- | :--- | :--- |
| 200 | 请求成功 | - |
| 401 | 未登录/Token失效 | 前端自动跳转登录页 |
| 403 | 无操作权限 | 隐藏菜单/按钮越权访问 |
| 404 | 接口不存在 | 记录日志 |
| 422 | 参数校验失败 | 返回字段级错误信息 |
| 500 | 服务器异常 | 统一提示"系统异常，请稍后重试" |
| 10001 | 参数校验失败 | 同422 |
| 10002 | 业务逻辑错误 | 如"库存不足""订单状态不允许此操作" |
| 10003 | 审核状态冲突 | 如"已审核单据不可重复审核" |
| 10004 | 数据不存在 | 如"SKU不存在""订单不存在" |

### 1.4 通用查询参数

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| page | int | 页码（默认1） |
| per_page | int | 每页条数（默认15） |
| keyword | string | 关键词搜索 |
| sort_by | string | 排序字段 |
| sort_order | string | 排序方向：asc/desc |
| status | int | 状态筛选 |
| start_date | string | 开始日期（YYYY-MM-DD） |
| end_date | string | 结束日期（YYYY-MM-DD） |

---

## 2 用户与权限管理接口组

### 2.1 认证接口

#### POST /api/v1/auth/login 用户登录

请求参数：

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| username | string | 是 | 用户名/手机号/邮箱 |
| password | string | 是 | 密码 |
| captcha_key | string | 否 | 验证码缓存键（需验证时必填） |
| captcha_code | string | 否 | 图形验证码（需验证时必填） |

返回示例：

```json
{
  "code": 200,
  "message": "登录成功",
  "data": {
    "token": "1|abc123...",
    "token_type": "Bearer",
    "expires_at": "2026-08-01T10:30:00+08:00",
    "user": {
      "id": 1,
      "username": "seeding",
      "name": "系统管理员",
      "phone": "15690631151",
      "avatar": null,
      "roles": ["super_admin"],
      "permissions": ["*"]
    }
  }
}
```

#### POST /api/v1/auth/logout 退出登录

无需参数，需携带 Token。

#### POST /api/v1/auth/password-reset 发送重置密码链接

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| email | string | 是 | 注册邮箱 |

#### PUT /api/v1/auth/password 重置密码

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| old_password | string | 是 | 旧密码 |
| new_password | string | 是 | 新密码 |
| new_password_confirmation | string | 是 | 确认新密码 |

#### GET /api/v1/auth/me 获取当前用户信息

返回当前登录用户的完整信息，含角色、权限列表。

#### POST /api/v1/auth/captcha 获取图形验证码

返回验证码图片 Base64 及缓存键。

### 2.2 用户管理接口

#### GET /api/v1/users 用户列表

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| keyword | string | 搜索用户名/手机号 |
| role_id | int | 按角色筛选 |
| status | int | 状态筛选：0禁用，1启用 |

#### POST /api/v1/users 新增用户

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| username | string | 是 | 用户名（唯一） |
| password | string | 是 | 密码 |
| name | string | 是 | 姓名 |
| phone | string | 否 | 手机号（唯一） |
| email | string | 否 | 邮箱（唯一） |
| avatar | string | 否 | 头像URL |
| role_ids | int[] | 是 | 角色ID数组 |
| status | int | 否 | 状态（默认1） |

#### GET /api/v1/users/{id} 用户详情

#### PUT /api/v1/users/{id} 编辑用户

同新增参数，username 不可修改。

#### DELETE /api/v1/users/{id} 删除用户（软删除）

#### PUT /api/v1/users/{id}/reset-password 重置密码

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| password | string | 是 | 新密码 |

#### PUT /api/v1/users/{id}/toggle-status 启用/禁用

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| status | int | 是 | 0禁用，1启用 |

### 2.3 角色管理接口

#### GET /api/v1/roles 角色列表

#### POST /api/v1/roles 新增角色

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| name | string | 是 | 角色标识（英文） |
| display_name | string | 是 | 角色显示名称 |
| description | string | 否 | 描述 |

#### PUT /api/v1/roles/{id} 编辑角色

#### DELETE /api/v1/roles/{id} 删除角色

#### PUT /api/v1/roles/{id}/permissions 配置角色权限

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| permission_ids | int[] | 是 | 权限ID数组 |

### 2.4 权限管理接口

#### GET /api/v1/permissions 权限列表（树形）

#### POST /api/v1/permissions 新增权限

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| name | string | 是 | 权限标识 |
| display_name | string | 是 | 显示名称 |
| type | int | 是 | 类型：1菜单，2按钮，3接口 |
| parent_id | int | 否 | 父级权限ID（默认0） |
| route | string | 否 | 路由/接口标识 |
| sort | int | 否 | 排序 |
| icon | string | 否 | 图标 |

#### PUT /api/v1/permissions/{id} 编辑权限

#### DELETE /api/v1/permissions/{id} 删除权限

---

## 3 组织主体管理接口组

### 3.1 供应商管理接口

#### GET /api/v1/suppliers 供应商列表

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| keyword | string | 搜索名称/联系人 |
| status | int | 状态筛选 |

#### POST /api/v1/suppliers 新增供应商

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| name | string | 是 | 供应商名称 |
| contact_name | string | 否 | 联系人 |
| contact_phone | string | 否 | 联系电话 |
| address | string | 否 | 地址 |
| bank_name | string | 否 | 开户行 |
| bank_account | string | 否 | 银行账号 |
| settlement_cycle | int | 否 | 结算周期：1周结，2月结，3不定期 |
| remark | string | 否 | 备注 |

#### GET /api/v1/suppliers/{id} 供应商详情

#### PUT /api/v1/suppliers/{id} 编辑供应商

#### DELETE /api/v1/suppliers/{id} 删除供应商（软删除）

#### GET /api/v1/suppliers/{id}/purchase-orders 供应商采购单列表

### 3.2 商家管理接口

#### GET /api/v1/merchants 商家列表

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| keyword | string | 搜索名称/联系人 |
| status | int | 状态筛选 |

#### POST /api/v1/merchants 新增商家

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| name | string | 是 | 商家名称 |
| contact_name | string | 否 | 联系人 |
| contact_phone | string | 否 | 联系电话 |
| address | string | 否 | 默认配送地址 |
| latitude | decimal | 否 | 纬度（GCJ-02坐标系） |
| longitude | decimal | 否 | 经度（GCJ-02坐标系） |
| coordinate_type | string | 否 | 坐标系标识（默认gcj02） |
| geohash | string | 否 | Geohash编码 |
| min_order_amount | decimal | 否 | 起送价 |
| settlement_type | int | 否 | 结算方式：1现结，2账期，3预付款 |
| credit_limit | decimal | 否 | 信用额度 |
| remark | string | 否 | 备注 |

#### GET /api/v1/merchants/{id} 商家详情

返回基本信息 + 最近订单 + 账户余额 + 应收概览。

#### PUT /api/v1/merchants/{id} 编辑商家

#### DELETE /api/v1/merchants/{id} 删除商家（软删除）

#### GET /api/v1/merchants/{id}/addresses 商家收货地址列表

#### POST /api/v1/merchants/{id}/addresses 新增收货地址

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| contact_name | string | 否 | 联系人 |
| contact_phone | string | 否 | 联系电话 |
| address | string | 是 | 收货地址 |
| is_default | int | 否 | 是否默认（默认0） |
| sort | int | 否 | 排序 |

#### PUT /api/v1/merchants/{id}/addresses/{addressId} 编辑收货地址

#### DELETE /api/v1/merchants/{id}/addresses/{addressId} 删除收货地址

### 3.3 配送线路管理接口

#### GET /api/v1/delivery-routes 线路列表

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| code | string | 按编码筛选 |
| name | string | 按名称筛选 |
| status | int | 状态筛选 |

#### POST /api/v1/delivery-routes 新增线路

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| name | string | 是 | 线路名称 |
| code | string | 是 | 线路编码（唯一） |
| warehouse_id | int | 否 | 出发仓库ID |
| default_driver_id | int | 否 | 默认司机ID |
| default_vehicle_id | int | 否 | 默认车辆ID |
| color | string | 否 | 地图显示颜色（默认#3B82F6） |
| departure_time | string | 否 | 默认出发时间（默认06:00） |
| estimated_duration | int | 否 | 预计总时长（分钟） |
| estimated_distance | decimal | 否 | 预计总里程（公里） |
| description | string | 否 | 描述 |
| sort | int | 否 | 排序 |

#### PUT /api/v1/delivery-routes/{id} 编辑线路

字段同新增线路。

#### DELETE /api/v1/delivery-routes/{id} 删除线路

已关联配送任务的线路不可删除。

#### GET /api/v1/delivery-routes/{id} 线路详情

含线路基本信息 + 商家明细列表（含顺序号）。

#### POST /api/v1/delivery-routes/{id}/stops 添加线路商家

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| merchant_id | int | 是 | 商家ID |
| address | string | 否 | 配送地址 |
| latitude | decimal | 否 | 纬度 |
| longitude | decimal | 否 | 经度 |
| default_service_time | int | 否 | 默认停留时间（分钟，默认10） |

#### PUT /api/v1/delivery-routes/{id}/stops/{stopId} 编辑线路商家

#### DELETE /api/v1/delivery-routes/{id}/stops/{stopId} 移除线路商家

#### PUT /api/v1/delivery-routes/{id}/sort-stops 调整线路商家配送顺序

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| stops | object[] | 是 | [{stop_id, sequence_no}] |

### 3.4 司机管理接口

#### GET /api/v1/drivers 司机列表

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| keyword | string | 搜索姓名/手机号 |
| online_status | int | 在线状态：0离线，1在线 |
| status | int | 状态筛选 |

#### POST /api/v1/drivers 新增司机

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| name | string | 是 | 姓名 |
| phone | string | 是 | 手机号（唯一） |
| id_card | string | 否 | 身份证号 |

#### GET /api/v1/drivers/{id} 司机详情（含绑定车辆、历史任务）

#### PUT /api/v1/drivers/{id} 编辑司机

#### DELETE /api/v1/drivers/{id} 删除司机（软删除）

### 3.5 车辆管理接口

#### GET /api/v1/vehicles 车辆列表

#### POST /api/v1/vehicles 新增车辆

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| plate_number | string | 是 | 车牌号（唯一） |
| vehicle_type | string | 否 | 车辆类型 |
| is_cold_chain | int | 否 | 是否冷链：0否，1是 |

#### PUT /api/v1/vehicles/{id} 编辑车辆

#### DELETE /api/v1/vehicles/{id} 删除车辆（软删除）

#### POST /api/v1/vehicles/{id}/bind-driver 绑定司机

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| driver_id | int | 是 | 司机ID |
| is_default | int | 否 | 是否默认车辆（默认0） |

#### DELETE /api/v1/vehicles/{id}/unbind-driver 解绑司机

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| driver_id | int | 是 | 司机ID |

---

## 4 商品管理接口组

### 4.1 商品分类接口

#### GET /api/v1/categories 分类列表（树形）

#### POST /api/v1/categories 新增分类

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| parent_id | int | 否 | 父级分类ID（默认0=根节点） |
| name | string | 是 | 分类名称 |
| icon | string | 否 | 图标 |
| sort | int | 否 | 排序 |

#### PUT /api/v1/categories/{id} 编辑分类

#### DELETE /api/v1/categories/{id} 删除分类

#### PUT /api/v1/categories/{id}/toggle-status 启用/禁用分类

### 4.2 商品管理接口

#### GET /api/v1/products 商品列表

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| keyword | string | 搜索商品名称 |
| category_id | int | 按分类筛选 |
| supplier_id | int | 按供应商筛选 |
| status | int | 状态：0下架，1上架 |

#### POST /api/v1/products 新增商品

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| category_id | int | 是 | 分类ID |
| name | string | 是 | 商品名称 |
| cover | string | 否 | 封面图URL |
| unit | string | 是 | 单位 |
| is_weight_priced | int | 否 | 是否称重改价（默认0） |
| supplier_id | int | 否 | 默认供应商ID |
| stock_warning_value | decimal | 否 | 库存预警值 |
| description | text | 否 | 商品详情 |
| images | string[] | 否 | 详情图URL数组 |
| status | int | 否 | 状态（默认1） |

#### GET /api/v1/products/{id} 商品详情（含SKU列表、可见性配置）

#### PUT /api/v1/products/{id} 编辑商品

#### DELETE /api/v1/products/{id} 删除商品（软删除）

#### PUT /api/v1/products/{id}/toggle-status 上架/下架

### 4.3 SKU 规格接口

#### GET /api/v1/products/{productId}/skus 商品下SKU列表

#### POST /api/v1/products/{productId}/skus 新增SKU

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| sku_code | string | 是 | SKU编码（唯一） |
| specs | json | 否 | 规格属性，如 {"规格":"大份"} |
| purchase_price | decimal | 是 | 采购参考价 |
| wholesale_price | decimal | 是 | 批发销售价 |
| cost_price | decimal | 是 | 财务成本价 |
| stock | decimal | 否 | 当前库存（默认0） |

#### PUT /api/v1/skus/{id} 编辑SKU

> 修改批发销售价幅度 > 15% 时，需运营经理审核通过后方可生效（触发审核流程）。

#### DELETE /api/v1/skus/{id} 删除SKU

### 4.4 SKU 条码接口

#### GET /api/v1/skus/{skuId}/barcodes SKU条码列表

#### POST /api/v1/skus/{skuId}/barcodes 新增条码

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| barcode_type | int | 是 | 条码类型：1厂家，2供应商，3内部，4备用 |
| barcode_code | string | 是 | 条码值 |
| supplier_id | int | 条件必填 | 供应商条码时必填 |
| is_default | int | 否 | 是否默认条码（默认0） |

#### PUT /api/v1/skus/{skuId}/barcodes/{id} 编辑条码

#### DELETE /api/v1/skus/{skuId}/barcodes/{id} 删除条码

#### PUT /api/v1/skus/{skuId}/barcodes/{id}/toggle 启用/禁用条码

#### GET /api/v1/barcodes/scan 扫码识别

| 参数 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| code | string | 是 | 条码值 |

返回匹配的 SKU 信息。

### 4.5 一品多供接口

#### GET /api/v1/skus/{skuId}/suppliers SKU供应商关联列表

#### POST /api/v1/skus/{skuId}/suppliers 新增供应商关联

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| supplier_id | int | 是 | 供应商ID |
| purchase_price | decimal | 是 | 采购参考价 |
| is_default | int | 否 | 是否默认（默认0） |
| sort | int | 否 | 排序 |

#### PUT /api/v1/skus/{skuId}/suppliers/{id} 编辑供应商关联

#### DELETE /api/v1/skus/{skuId}/suppliers/{id} 删除供应商关联

### 4.6 商品可见性接口

#### GET /api/v1/merchant-sku-visibility 可见性配置列表

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| merchant_id | int | 按商家筛选 |
| sku_id | int | 按SKU筛选 |

#### POST /api/v1/merchant-sku-visibility/batch 批量配置可见性

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| merchant_id | int | 是 | 商家ID |
| sku_ids | int[] | 是 | SKU ID数组 |
| is_visible | int | 是 | 0不可见，1可见 |

#### PUT /api/v1/merchant-sku-visibility/{id} 修改可见性

### 4.7 标签与关键词接口

#### GET /api/v1/tags 标签列表

#### POST /api/v1/tags 新增标签

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| name | string | 是 | 标签名称 |
| sort | int | 否 | 排序 |

#### PUT /api/v1/tags/{id} 编辑标签

#### DELETE /api/v1/tags/{id} 删除标签

#### POST /api/v1/products/{id}/tags 为商品配置标签

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| tag_ids | int[] | 是 | 标签ID数组 |

#### GET /api/v1/keywords 关键词列表

#### POST /api/v1/keywords 新增关键词

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| keyword | string | 是 | 关键词 |
| product_id | int | 否 | 关联商品ID |

---

## 5 平台统采接口组

### 5.1 待采清单接口

#### GET /api/v1/purchase-items 待采清单列表

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| sku_id | int | 按SKU筛选 |
| status | int | 状态：1待生成，2已生成 |

#### POST /api/v1/purchase-items 手工添加待采项

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| sku_id | int | 是 | SKU ID |
| quantity | decimal | 是 | 待采数量 |

#### POST /api/v1/purchase-items/generate-order 生成采购单

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| item_ids | int[] | 是 | 待采项ID数组 |
| supplier_id | int | 否 | 指定供应商（不填则取默认供应商） |

### 5.2 采购单接口

#### GET /api/v1/purchase-orders 采购单列表

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| keyword | string | 搜索单号 |
| supplier_id | int | 按供应商筛选 |
| status | int | 状态：1待接单，2备货中，3已发货，4已入库，5完成，9取消 |

#### POST /api/v1/purchase-orders 新增采购单（手工创建）

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| supplier_id | int | 是 | 供应商ID |
| items | array | 是 | 采购明细 |
| items[].sku_id | int | 是 | SKU ID |
| items[].quantity | decimal | 是 | 采购数量 |
| items[].price | decimal | 是 | 采购单价 |
| remark | string | 否 | 备注 |

#### GET /api/v1/purchase-orders/{id} 采购单详情

#### PUT /api/v1/purchase-orders/{id}/status 状态流转

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| status | int | 是 | 目标状态（2备货中/3已发货/4已入库/5完成） |

#### POST /api/v1/purchase-orders/{id}/cancel 取消采购单

仅待接单状态可取消。

### 5.3 采购入库接口

#### POST /api/v1/purchase-orders/{id}/warehouse 确认入库

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| warehouse_id | int | 是 | 入库仓库ID |
| items | array | 是 | 入库明细 |
| items[].purchase_order_item_id | int | 是 | 采购单明细ID |
| items[].actual_quantity | decimal | 是 | 实际入库数量 |
| items[].actual_price | decimal | 是 | 实际入库单价 |
| items[].discrepancy_reason | string | 否 | 入库差异原因 |

> 入库后自动更新仓库库存，记录库存变动日志。

### 5.4 采购退货接口

#### GET /api/v1/purchase-returns 采购退货单列表

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| keyword | string | 搜索单号 |
| supplier_id | int | 按供应商筛选 |
| status | int | 状态：1待审核，2已审核，3已出库，4完成，9取消 |

#### POST /api/v1/purchase-returns 新增采购退货单

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| purchase_order_id | int | 是 | 关联采购单ID |
| warehouse_id | int | 是 | 出库仓库ID |
| items | array | 是 | 退货明细 |
| items[].purchase_order_item_id | int | 是 | 采购单明细ID |
| items[].sku_id | int | 是 | SKU ID |
| items[].quantity | decimal | 是 | 退货数量 |
| items[].price | decimal | 是 | 退货单价 |
| items[].reason | string | 否 | 退货原因 |
| reason | string | 否 | 整单退货原因 |

#### GET /api/v1/purchase-returns/{id} 退货单详情

#### PUT /api/v1/purchase-returns/{id}/audit 审核退货单

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| approved | bool | 是 | true通过，false拒绝 |
| remark | string | 否 | 审核备注 |

#### PUT /api/v1/purchase-returns/{id}/execute-out 实际出库

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| items | array | 是 | 实际出库明细 |
| items[].id | int | 是 | 退货明细ID |
| items[].actual_quantity | decimal | 是 | 实际出库数量 |
| items[].actual_price | decimal | 是 | 实际出库单价 |

> 出库后自动扣减库存、扣减供应商应付。

---

## 6 客户直采接口组

### 6.1 购物车接口

#### GET /api/v1/carts 购物车列表（管理后台查看）

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| merchant_id | int | 按商家筛选 |

#### GET /api/v1/merchants/{merchantId}/cart 商家购物车详情

#### POST /api/v1/merchants/{merchantId}/cart/items 添加购物车商品

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| sku_id | int | 是 | SKU ID |
| quantity | decimal | 是 | 数量 |
| price | decimal | 是 | 加入时单价 |

#### PUT /api/v1/cart-items/{id} 修改购物车商品数量

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| quantity | decimal | 是 | 数量 |

#### DELETE /api/v1/cart-items/{id} 删除购物车商品

#### DELETE /api/v1/merchants/{merchantId}/cart 清空购物车

### 6.2 订单管理接口

#### GET /api/v1/orders 订单列表

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| keyword | string | 搜索订单号 |
| merchant_id | int | 按商家筛选 |
| status | int | 状态筛选 |
| batch | int | 批次筛选：1上午，2下午 |
| start_date | string | 下单日期范围 |
| end_date | string | 下单日期范围 |

#### POST /api/v1/orders 创建订单

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| merchant_id | int | 是 | 商家ID |
| delivery_address | string | 否 | 配送地址 |
| contact_name | string | 否 | 收货联系人 |
| contact_phone | string | 否 | 收货电话 |
| batch | int | 否 | 配送批次（默认1） |
| address_id | int | 否 | 收货地址ID |
| items | array | 是 | 订单明细 |
| items[].sku_id | int | 是 | SKU ID |
| items[].quantity | decimal | 是 | 数量 |
| items[].price | decimal | 是 | 单价 |
| remark | string | 否 | 备注 |

#### GET /api/v1/orders/{id} 订单详情（含明细、配送信息、操作日志）

#### PUT /api/v1/orders/{id}/status 订单状态流转

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| status | int | 是 | 目标状态 |

#### POST /api/v1/orders/{id}/cancel 取消订单

#### PUT /api/v1/orders/{id}/lock 锁定订单

### 6.3 订单明细/称重改价接口

#### PUT /api/v1/order-items/{id}/weighing 称重改价

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| actual_quantity | decimal | 是 | 实际称重数量 |
| actual_price | decimal | 是 | 实际称重单价 |

> 差异超20%自动标记"待运营审核"，触发审核流程。

### 6.4 常购清单接口

#### GET /api/v1/merchants/{merchantId}/frequently-bought 商家常购清单

#### POST /api/v1/merchants/{merchantId}/frequently-bought/add-to-cart 一键加购

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| sku_ids | int[] | 是 | SKU ID数组 |

### 6.5 复购模板接口

#### GET /api/v1/merchants/{merchantId}/repurchase-templates 复购模板列表

#### POST /api/v1/merchants/{merchantId}/repurchase-templates 新增复购模板

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| name | string | 是 | 模板名称 |
| items | array | 是 | 模板明细 |
| items[].sku_id | int | 是 | SKU ID |
| items[].quantity | decimal | 是 | 数量 |

#### POST /api/v1/repurchase-templates/{id}/repurchase 一键复购

### 6.6 售后退货接口

#### GET /api/v1/order-returns 售后退货单列表

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| keyword | string | 搜索单号 |
| merchant_id | int | 按商家筛选 |
| status | int | 状态：1待审核，2已审核，3已退货，4退款完成，9取消 |

#### POST /api/v1/order-returns 新增售后退货单

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| order_id | int | 是 | 关联订单ID |
| merchant_id | int | 是 | 商家ID |
| items | array | 是 | 退货明细 |
| items[].order_item_id | int | 是 | 订单明细ID |
| items[].sku_id | int | 是 | SKU ID |
| items[].quantity | decimal | 是 | 退货数量 |
| items[].price | decimal | 是 | 退货单价 |
| items[].reason | string | 否 | 退货原因 |
| reason | string | 否 | 整单退货原因 |

#### GET /api/v1/order-returns/{id} 退货单详情

#### PUT /api/v1/order-returns/{id}/audit 审核退货单

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| approved | bool | 是 | true通过，false拒绝 |
| remark | string | 否 | 审核备注 |

#### PUT /api/v1/order-returns/{id}/refund 确认退款

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| items | array | 是 | 退款明细 |
| items[].id | int | 是 | 退货明细ID |
| items[].refund_amount | decimal | 是 | 实际退款金额 |

> 退款后自动扣减应收账款。

---

## 7 库存管理接口组

### 7.1 仓库管理接口

#### GET /api/v1/warehouses 仓库列表

#### POST /api/v1/warehouses 新增仓库

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| name | string | 是 | 仓库名称 |
| type | int | 否 | 类型：1总仓，2前置仓 |
| is_cold_chain | int | 否 | 是否冷链：0否，1是 |
| address | string | 否 | 地址 |

#### PUT /api/v1/warehouses/{id} 编辑仓库

#### DELETE /api/v1/warehouses/{id} 删除仓库（软删除）

### 7.2 实时库存接口

#### GET /api/v1/inventory 实时库存列表

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| warehouse_id | int | 按仓库筛选 |
| sku_id | int | 按SKU筛选 |
| keyword | string | 搜索商品名称 |

#### GET /api/v1/inventory/{id} 库存详情

#### PUT /api/v1/inventory/{id}/adjust 库存调整

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| type | int | 是 | 变动类型：3调拨，5报溢，6调整 |
| quantity | decimal | 是 | 调整数量（正增负减） |
| reason | string | 是 | 调整原因 |

### 7.3 库存变动日志接口

#### GET /api/v1/inventory-logs 库存变动日志列表

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| warehouse_id | int | 按仓库筛选 |
| sku_id | int | 按SKU筛选 |
| type | int | 变动类型：1入库，2出库，3调拨，4报损，5报溢，6调整 |
| start_date | string | 开始日期 |
| end_date | string | 结束日期 |

### 7.4 库存预警接口

#### GET /api/v1/inventory/warnings 库存预警列表

返回所有库存低于预警值的 SKU 列表。

---

## 8 损耗管理接口组

### 8.1 损耗单管理接口

#### GET /api/v1/loss-orders 损耗单列表

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| keyword | string | 搜索单号 |
| warehouse_id | int | 按仓库筛选 |
| loss_type | int | 按损耗类型筛选：1存储腐坏，2称重失水，3过期报废，4加工损耗，5盘点差异，6其他 |
| status | int | 状态：1待审核，2已通过，3已执行，4已关闭，9已取消 |
| start_date | string | 开始日期 |
| end_date | string | 结束日期 |

#### POST /api/v1/loss-orders 新增损耗单

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| warehouse_id | int | 是 | 仓库ID |
| loss_type | int | 是 | 主要损耗类型（1~6） |
| reason | string | 否 | 损耗原因 |
| remark | string | 否 | 备注 |
| items | array | 是 | 损耗明细 |
| items[].sku_id | int | 是 | SKU ID |
| items[].loss_type | int | 是 | 明细损耗类型（1~6） |
| items[].quantity | decimal | 是 | 损耗数量 |
| items[].responsible_party | int | 是 | 责任方：1平台，2供应商 |
| items[].supplier_id | int | 条件必填 | 供应商ID（责任方=2时必填） |
| items[].reason | string | 否 | 明细损耗原因 |
| items[].evidence_urls | string[] | 否 | 凭证图片URL数组 |

> 创建时系统自动计算损耗金额 = quantity × skus.cost_price（快照），不支持手动修改。损耗金额超过 `loss_approval_threshold`（默认200元）时自动进入待审核状态。

#### GET /api/v1/loss-orders/{id} 损耗单详情

返回基本信息 + 损耗明细 + 审核记录 + 库存变动记录。

#### PUT /api/v1/loss-orders/{id} 编辑损耗单

仅待审核状态可编辑。参数同新增。

#### DELETE /api/v1/loss-orders/{id} 取损耗单（软删除/取消）

#### PUT /api/v1/loss-orders/{id}/cancel 取消损耗单

仅待审核状态可取消。

### 8.2 损耗审批接口

#### PUT /api/v1/loss-orders/{id}/approve 审核通过

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| remark | string | 否 | 审核备注 |

> 审核通过后自动执行：①扣减仓库库存，记录库存变动日志（变动类型=报损）；②成本归集（责任方=供应商时扣减供应商应付结算）；③写入审计日志。

#### PUT /api/v1/loss-orders/{id}/reject 审核拒绝

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| reason | string | 是 | 拒绝原因 |

> 拒绝后损耗单回到待修改状态，申请人可修改后重新提交。

### 8.3 损耗报表接口

#### GET /api/v1/loss-orders/statistics 损耗统计

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| start_date | string | 是 | 开始日期 |
| end_date | string | 是 | 结束日期 |
| warehouse_id | int | 否 | 按仓库筛选 |
| loss_type | int | 否 | 按损耗类型筛选 |
| responsible_party | int | 否 | 按责任方筛选：1平台，2供应商 |

返回：损耗总数量、损耗总金额、按类型/仓库/责任方的分组统计。

#### GET /api/v1/loss-orders/rate-analysis 损耗率分析

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| start_date | string | 是 | 开始日期 |
| end_date | string | 是 | 结束日期 |
| group_by | string | 否 | 分组维度：category/warehouse/period |

返回：损耗金额 ÷ 同期采购金额 = 损耗率。

#### GET /api/v1/loss-orders/trend 损耗趋势

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| start_date | string | 是 | 开始日期 |
| end_date | string | 是 | 结束日期 |
| granularity | string | 否 | 粒度：day/week/month |

#### GET /api/v1/loss-orders/export 损耗明细导出

返回 Excel 文件下载。

---

## 9 拣货管理接口组

### 9.1 拣货任务接口

#### GET /api/v1/picking-tasks 拣货任务列表

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| warehouse_id | int | 按仓库筛选 |
| picker_id | int | 按拣货员筛选 |
| status | int | 状态：1待分配，2拣货中，3已完成 |
| batch | int | 批次筛选：1上午，2下午 |

#### POST /api/v1/picking-tasks 创建拣货任务

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| warehouse_id | int | 是 | 仓库ID |
| batch | int | 否 | 配送批次（默认1） |
| order_ids | int[] | 是 | 聚合的订单ID数组 |

#### GET /api/v1/picking-tasks/{id} 拣货任务详情

返回任务基本信息 + SKU汇总列表 + 商家分组列表，支持 `?view=sku|merchant` 切换视图。

#### PUT /api/v1/picking-tasks/{id}/assign 分配拣货员

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| picker_id | int | 是 | 拣货员ID |

#### PUT /api/v1/picking-tasks/{id}/start 开始拣货

#### PUT /api/v1/picking-tasks/{id}/complete 完成拣货

提交所有明细的实际拣货数量。

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| items | array | 是 | 拣货明细 |
| items[].id | int | 是 | 拣货任务明细ID |
| items[].picked_quantity | decimal | 是 | 实际拣货数量 |

---

## 10 物流配送接口组

### 10.1 配送任务接口

#### GET /api/v1/delivery-tasks 配送任务列表

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| route_id | int | 按线路筛选 |
| driver_id | int | 按司机筛选 |
| status | int | 状态：1待配送，2已分配，3配送中，4暂停，5已完成，6已取消 |
| delivery_date | string | 送达日期筛选 |
| batch | int | 批次筛选 |
| has_urgent | int | 筛选含加急：1是 |
| has_important | int | 筛选含重要：1是 |

#### POST /api/v1/delivery-tasks 生成配送任务

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| route_id | int | 是 | 线路ID |
| delivery_date | string | 是 | 送达日期 |
| driver_id | int | 否 | 司机ID（默认从线路带入） |
| vehicle_id | int | 否 | 车辆ID（默认从线路带入） |
| batch | int | 否 | 配送批次（默认1） |
| detail_ids | int[] | 是 | 从单据池勾选的明细ID数组 |

> 生成任务时自动创建顺序表（delivery_task_sequences），按线路 sequence_no 排列。

#### GET /api/v1/delivery-tasks/{id} 配送任务详情

含任务信息、明细列表、顺序表、司机/车辆信息、配送轨迹。

#### PUT /api/v1/delivery-tasks/{id}/assign 分配司机/车辆

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| driver_id | int | 是 | 司机ID |
| vehicle_id | int | 否 | 车辆ID |

#### PUT /api/v1/delivery-tasks/{id}/start 开始配送

状态：1待配送/2已分配 → 3配送中。

#### PUT /api/v1/delivery-tasks/{id}/pause 暂停配送

状态：3配送中 → 4暂停。

#### PUT /api/v1/delivery-tasks/{id}/complete 完成配送任务

状态：3配送中 → 5已完成。

#### PUT /api/v1/delivery-tasks/{id}/cancel 取消配送任务

状态：1待配送 → 6已取消。

### 10.2 配送顺序表接口

#### GET /api/v1/delivery-tasks/{id}/sequences 获取配送顺序表

#### PUT /api/v1/delivery-task-sequences/{id}/reorder 调整顺序

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| sequence_no | int | 是 | 新顺序号 |

#### PUT /api/v1/delivery-task-sequences/{id}/mark-urgent 标记加急

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| is_urgent | int | 是 | 0取消，1加急 |
| urgent_reason | string | 否 | 加急原因 |

#### PUT /api/v1/delivery-task-sequences/{id}/mark-important 标记重要

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| is_important | int | 是 | 0取消，1重要 |
| important_reason | string | 否 | 重要原因 |

#### PUT /api/v1/delivery-task-sequences/{id}/arrive 到达商家

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| gps_latitude | decimal | 否 | 到达时纬度 |
| gps_longitude | decimal | 否 | 到达时经度 |

> 自动写入抵达时间流水（delivery_arrival_logs）。

#### PUT /api/v1/delivery-task-sequences/{id}/deliver 确认送达

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| delivery_method | string | 否 | 确认方式：manual/scan/photo/signature |
| delivery_photos | json | 否 | 配送照片 |
| signature_image | string | 否 | 签名图片URL |
| gps_latitude | decimal | 否 | 送达时纬度 |
| gps_longitude | decimal | 否 | 送达时经度 |

#### PUT /api/v1/delivery-task-sequences/{id}/skip 跳过商家

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| skip_reason | string | 是 | 跳过原因 |

#### PUT /api/v1/delivery-task-sequences/{id}/fail 标记配送失败

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| fail_reason | string | 是 | 失败原因 |

### 10.3 配送轨迹接口

#### POST /api/v1/delivery-tracks 上报轨迹

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| delivery_task_id | int | 是 | 配送任务ID |
| latitude | decimal | 是 | 纬度 |
| longitude | decimal | 是 | 经度 |
| location_desc | string | 否 | 位置描述 |

#### GET /api/v1/delivery-tasks/{id}/tracks 获取配送轨迹

#### GET /api/v1/delivery-tasks/{id}/tracks/replay 历史轨迹回放

### 10.4 签收存证接口

#### POST /api/v1/signatures 签收存证

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| order_id | int | 是 | 订单ID |
| delivery_task_id | int | 是 | 配送任务ID |
| type | int | 是 | 类型：1拍照签收，2电子签名，3质检照片 |
| image_url | string | 否 | 图片/签名文件地址 |
| signer_name | string | 否 | 签收人 |

#### GET /api/v1/orders/{id}/signatures 查看订单签收凭证

#### POST /api/v1/temperatures 记录冷链温度

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| delivery_task_id | int | 是 | 配送任务ID |
| temperature | decimal | 是 | 温度值 |

### 10.5 车辆故障接口

#### POST /api/v1/vehicle-issues 上报车辆故障

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| vehicle_id | int | 是 | 车辆ID |
| task_id | int | 否 | 关联配送任务ID |
| issue_type | string | 否 | 故障类型：breakdown/accident/tire/battery/engine/other |
| description | string | 是 | 描述 |
| photos | json | 否 | 故障照片 |

#### PUT /api/v1/vehicle-issues/{id}/resolve 解决故障

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| impact_type | string | 否 | 影响类型 |
| impact_desc | string | 否 | 影响描述 |

#### PUT /api/v1/vehicle-issues/{id}/close 关闭故障记录

#### GET /api/v1/vehicles/{id}/issues 车辆故障历史

### 10.6 送货单接口

#### GET /api/v1/delivery-notes 送货单列表

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| task_id | int | 按配送任务筛选 |
| merchant_id | int | 按商家筛选 |
| status | int | 状态：1待分货，2已分货，3已签收，4已取消 |
| delivery_date | string | 送达日期筛选 |

#### GET /api/v1/delivery-notes/{id} 送货单详情

含送货单基本信息 + 明细列表（SKU级） + 关联配送任务信息。

#### PUT /api/v1/delivery-notes/{id}/deliver 确认分货

状态：1待分货 → 2已分货。所有明细同步更新为已分货。

#### PUT /api/v1/delivery-notes/{id}/sign 确认签收

状态：2已分货 → 3已签收。

#### PUT /api/v1/delivery-notes/{id}/cancel 作废送货单

状态：1待分货/2已分货 → 4已取消。

#### PUT /api/v1/delivery-note-items/{id}/confirm 明细级分货确认

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| picked_quantity | int | 是 | 实际分货数量 |

> 实际数量 >= 应送数量 → 已分货，不足 → 差异。

---

## 11 差异处理接口组

### 11.1 差异单管理接口

#### GET /api/v1/discrepancies 差异单列表

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| keyword | string | 搜索单号/订单号 |
| order_id | int | 按订单筛选 |
| stage | int | 差异环节：1拣货，2配送，3实收 |
| type | int | 差异类型：1少收，2拒收，3残次，4其他 |
| status | int | 状态：1待处理，2处理中，3已处理，4已关闭，5争议中 |

#### POST /api/v1/discrepancies 创建差异单

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| order_id | int | 是 | 关联订单ID |
| order_item_id | int | 否 | 关联订单明细ID |
| stage | int | 是 | 差异环节：1拣货，2配送，3实收 |
| type | int | 是 | 差异类型：1少收，2拒收，3残次，4其他 |
| expected_quantity | decimal | 是 | 预期数量 |
| actual_quantity | decimal | 是 | 实际数量 |
| quantity_diff | decimal | 否 | 数量差异（自动计算） |
| amount_diff | decimal | 否 | 金额差异 |
| reason | string | 否 | 差异原因 |
| evidence_urls | string[] | 否 | 凭证图片数组 |

#### GET /api/v1/discrepancies/{id} 差异单详情

#### PUT /api/v1/discrepancies/{id}/process 处理差异

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| responsible_party | int | 是 | 责任方：1供应商，2平台，3司机，4商家 |
| decision | int | 是 | 处理决策：1补货，2退款，3扣款，4报损，5不计 |
| remark | string | 否 | 处理备注 |

> 决策为退款(2)或扣款(3)时，提交后自动进入待审核状态，需财务经理审核通过后方可执行金额调整。

#### PUT /api/v1/discrepancies/{id}/approve 审核差异（退款/扣款审核）

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| approved | bool | 是 | true通过，false拒绝 |
| remark | string | 否 | 审核备注 |

#### PUT /api/v1/discrepancies/{id}/close 关闭差异单

#### PUT /api/v1/discrepancies/{id}/dispute 转入争议

### 11.2 差异报表接口

#### GET /api/v1/discrepancies/statistics 差异统计

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| start_date | string | 是 | 开始日期 |
| end_date | string | 是 | 结束日期 |
| stage | int | 否 | 按环节筛选 |
| group_by | string | 否 | 分组维度：stage/type/responsible_party |

#### GET /api/v1/discrepancies/trend 差异趋势

#### GET /api/v1/discrepancies/export 差异明细导出

---

## 12 财务对账接口组

### 12.1 客户账户接口

#### GET /api/v1/merchant-accounts 客户账户列表

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| merchant_id | int | 按商家筛选 |

#### GET /api/v1/merchant-accounts/{merchantId} 客户账户详情

返回余额、总充值、总消费、信用额度、充值记录、消费记录。

#### PUT /api/v1/merchant-accounts/{merchantId}/credit 调整信用额度

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| credit_limit | decimal | 是 | 新信用额度 |

> 提交后进入待审核状态，需财务经理审核通过后生效。

### 12.2 客户充值接口

#### GET /api/v1/recharges 充值记录列表

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| merchant_id | int | 按商家筛选 |
| status | int | 状态：1待确认，2成功，3失败 |

#### POST /api/v1/recharges 后台手工充值

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| merchant_id | int | 是 | 商家ID |
| amount | decimal | 是 | 充值金额 |
| payment_method | int | 是 | 支付方式：1微信支付，2线下转账，3后台手工 |
| remark | string | 否 | 备注 |

> 后台手工充值提交后进入待审核状态，需财务经理审核通过后金额入账。

#### PUT /api/v1/recharges/{id}/confirm 确认充值

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| transaction_no | string | 否 | 第三方交易号 |

### 12.3 供应商结算接口

#### GET /api/v1/supplier-settlements 供应商结算单列表

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| supplier_id | int | 按供应商筛选 |
| status | int | 状态：1待结算，2部分付款，3已结清，4已办结 |

#### POST /api/v1/supplier-settlements 创建结算单

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| supplier_id | int | 是 | 供应商ID |
| start_date | date | 是 | 结算周期开始 |
| end_date | date | 是 | 结算周期结束 |
| purchase_order_ids | int[] | 否 | 包含的采购单ID（不填则自动汇总） |
| service_fee | decimal | 否 | 服务费（默认0） |

#### GET /api/v1/supplier-settlements/{id} 结算单详情

#### POST /api/v1/supplier-settlements/{id}/payments 新增付款记录

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| amount | decimal | 是 | 付款金额 |
| payment_method | int | 是 | 付款方式：1银行转账，2线下现金，3后台手工 |
| transaction_no | string | 否 | 第三方交易号 |
| evidence_urls | string[] | 否 | 付款凭证图片数组 |
| remark | string | 否 | 备注 |

> 付款记录提交后进入待审核状态，财务经理审核通过后更新已付金额。

#### PUT /api/v1/supplier-settlements/{id}/close 办结结算单

办结校验：已付 ≥ 应付，且无争议中的差异单。

### 12.4 应收账款接口

#### GET /api/v1/receivables 应收账款列表

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| merchant_id | int | 按商家筛选 |
| status | int | 状态：1未结算，2部分收款，3已结清，4争议中，5已办结 |

#### GET /api/v1/receivables/{id} 应收账款详情

#### POST /api/v1/receivables/{id}/payments 新增收款记录

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| amount | decimal | 是 | 收款金额 |
| payment_method | int | 是 | 收款方式：1余额扣款，2微信支付，3线下转账，4后台手工 |
| transaction_no | string | 否 | 第三方交易号 |
| evidence_urls | string[] | 否 | 收款凭证图片数组 |
| remark | string | 否 | 备注 |

> 收款记录提交后进入待审核状态，财务经理审核通过后更新已收金额。

#### PUT /api/v1/receivables/{id}/close 办结应收单

#### PUT /api/v1/receivables/{id}/adjust 改价折扣调整

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| strategy_discount_amount | decimal | 是 | 改价/促销折扣金额 |
| reason | string | 是 | 调整原因 |

> 提交后进入待审核状态，财务经理审核通过后生效。

### 12.5 发票管理接口

#### GET /api/v1/invoices 发票列表

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| type | int | 类型：1客户发票，2供应商发票 |
| status | int | 状态：1待开具，2已开具，3已寄出 |

#### POST /api/v1/invoices 申请发票

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| type | int | 是 | 类型：1客户发票，2供应商发票 |
| target_id | int | 是 | 关联对象ID（merchant_id/supplier_id） |
| title | string | 否 | 发票抬头 |
| amount | decimal | 是 | 金额 |

#### PUT /api/v1/invoices/{id}/issue 开具发票

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| invoice_no | string | 是 | 发票号 |
| file_url | string | 否 | 发票文件地址 |

#### PUT /api/v1/invoices/{id}/send 寄出发票

### 12.6 单据授权更正接口

#### POST /api/v1/correction-authorizations 授权更正

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| order_id | int | 是 | 订单ID |
| reason | string | 是 | 更正原因 |
| after_data | json | 是 | 修改后数据 |

> 更正记录自动写入审计日志。

---

## 13 价格策略接口组

### 13.1 价格策略管理接口

#### GET /api/v1/price-strategies 价格策略列表

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| type | int | 类型：1促销，2临时改价 |
| target_type | int | 作用对象：1供应商，2商家，3全部 |
| status | int | 状态：0禁用，1启用 |

#### POST /api/v1/price-strategies 新增价格策略

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| name | string | 是 | 策略名称 |
| code | string | 否 | 策略编码（唯一） |
| type | int | 是 | 类型：1促销，2临时改价 |
| target_type | int | 是 | 作用对象：1供应商，2商家，3全部 |
| scope | int | 否 | 作用范围：1采购，2销售，3通用 |
| start_at | string | 否 | 生效开始时间 |
| end_at | string | 否 | 生效结束时间 |
| remark | string | 否 | 备注 |

> 新增后进入待审核状态，需财务经理审核通过后方可生效。

#### GET /api/v1/price-strategies/{id} 策略详情（含明细）

#### PUT /api/v1/price-strategies/{id} 编辑策略

> 修改后重新进入待审核状态。

#### DELETE /api/v1/price-strategies/{id} 删除策略（软删除）

#### PUT /api/v1/price-strategies/{id}/toggle-status 启用/禁用策略

### 13.2 价格策略明细接口

#### GET /api/v1/price-strategies/{id}/items 策略明细列表

#### POST /api/v1/price-strategies/{id}/items 新增明细

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| target_id | int | 否 | 作用对象ID（0=全部） |
| category_id | int | 否 | 商品分类ID |
| product_id | int | 否 | 商品ID |
| sku_id | int | 否 | SKU ID |
| price_type | int | 是 | 价格类型：1固定价，2折扣率，3成本加权 |
| price_value | decimal | 否 | 固定价格 |
| discount_rate | decimal | 否 | 折扣率（%） |
| cost_weight_rate | decimal | 否 | 成本加权率（%） |
| min_quantity | decimal | 否 | 最小起量 |
| effective_start_at | string | 否 | 明细生效开始时间 |
| effective_end_at | string | 否 | 明细生效结束时间 |

#### PUT /api/v1/price-strategy-items/{id} 编辑明细

#### DELETE /api/v1/price-strategy-items/{id} 删除明细

### 13.3 改价记录接口

#### GET /api/v1/price-change-logs 改价记录列表

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| source_type | int | 来源：1促销，2临时改价，3手动改价 |
| target_type | int | 单据类型：1订单，2采购单，3应收，4应付 |
| operator_id | int | 操作人筛选 |
| start_date | string | 开始日期 |
| end_date | string | 结束日期 |

---

## 14 费用均摊接口组

#### GET /api/v1/price-apportionments 费用均摊记录列表

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| target_type | int | 单据类型：1订单，2采购单 |
| apportion_type | int | 均摊类型：1整单改价，2促销差价，3费用，4运费 |
| approval_status | int | 审核状态 |

#### POST /api/v1/price-apportionments 新增均摊记录

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| target_type | int | 是 | 单据类型 |
| target_id | int | 是 | 单据ID |
| apportion_type | int | 是 | 均摊类型 |
| apportion_mode | int | 是 | 均摊方式：1自动，2手动 |
| items | array | 是 | 均摊明细 |
| items[].target_item_id | int | 否 | 单据明细ID |
| items[].amount | decimal | 是 | 均摊金额 |
| items[].manual_amount | decimal | 否 | 手动均摊金额 |

> 手动均摊提交后进入待审核状态，运营经理审核通过后生效；自动均摊默认通过。

#### PUT /api/v1/price-apportionments/{id}/approve 审核均摊

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| approved | bool | 是 | true通过，false拒绝 |
| remark | string | 否 | 审核备注 |

---

## 15 审核管理接口组

### 15.1 审核配置接口

#### GET /api/v1/approval-type-configs 审核配置列表

返回 19 个审核节点配置。

#### PUT /api/v1/approval-type-configs/{id} 编辑审核配置

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| enabled | int | 是 | 是否启用审核：0关闭，1开启 |
| applicant_role_id | int | 否 | 申请人角色ID |
| reviewer_role_id | int | 否 | 审核人角色ID |

> 仅超级管理员可修改。变更即时生效，变更记录写入审计日志。

### 15.2 审核列表接口

#### GET /api/v1/approvals 待审核列表

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| approval_type | string | 审核类型编码 |
| status | int | 状态：1待审核，2已通过，3已拒绝，4已撤回 |

#### GET /api/v1/approvals/history 已审核列表

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| approval_type | string | 审核类型编码 |
| reviewer_id | int | 审核人筛选 |
| status | int | 审核结果：2通过，3拒绝 |
| start_date | string | 开始日期 |
| end_date | string | 结束日期 |

### 15.3 审核操作接口

#### GET /api/v1/approvals/{id} 审核详情

返回操作类型、申请人、申请时间、操作前数据(JSON)、操作后数据(JSON)、金额变动说明。

#### PUT /api/v1/approvals/{id}/approve 审核通过

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| remark | string | 否 | 审核备注 |

> 审核通过后业务操作自动生效（如充值入账、付款更新等），写入审计日志。

#### PUT /api/v1/approvals/{id}/reject 审核拒绝

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| reason | string | 是 | 拒绝原因 |

#### GET /api/v1/approvals/pending-count 待审核数量

返回当前用户角色对应的待审核记录数量。

---

## 16 系统支撑接口组

### 16.1 系统配置接口

> **注意：** 管理后台已迁移至 Livewire 全栈架构，系统配置管理通过 Livewire 组件直接操作，以下 REST API 接口**不适用于管理后台**。如小程序端需要读取公开配置（`is_public=true`），建议新增专用只读接口而非复用以下管理端接口。

#### GET /api/v1/system-configs 配置列表

#### PUT /api/v1/system-configs/{key} 更新配置

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| config_value | string | 是 | 配置值 |

> 仅超级管理员可操作。变更强制写入审计日志。

### 16.2 轮播广告接口

#### GET /api/v1/banners 广告列表

#### POST /api/v1/banners 新增广告

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| title | string | 是 | 标题 |
| image_url | string | 是 | 图片地址 |
| link_url | string | 否 | 跳转链接 |
| sort | int | 否 | 排序 |

#### PUT /api/v1/banners/{id} 编辑广告

#### DELETE /api/v1/banners/{id} 删除广告

### 16.3 运营主推接口

#### GET /api/v1/promotions 运营主推列表

#### POST /api/v1/promotions 新增主推配置

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| type | int | 是 | 类型：1主推商品，2主推品类 |
| target_id | int | 是 | 目标ID（sku_id/category_id） |
| sort | int | 否 | 排序 |
| start_at | string | 否 | 开始时间 |
| end_at | string | 否 | 结束时间 |

#### PUT /api/v1/promotions/{id} 编辑主推配置

#### DELETE /api/v1/promotions/{id} 删除主推配置

### 16.4 操作日志接口

#### GET /api/v1/operation-logs 操作日志列表

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| user_id | int | 操作人筛选 |
| method | string | 请求方法 |
| path | string | 请求路径 |
| start_date | string | 开始日期 |
| end_date | string | 结束日期 |

### 16.5 审计日志接口

#### GET /api/v1/audit-logs 审计日志列表

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| model_type | string | 模型类型 |
| action | string | 操作动作：create/update/delete/lock/correction |
| operator_id | int | 操作人筛选 |
| start_date | string | 开始日期 |
| end_date | string | 结束日期 |

#### GET /api/v1/audit-logs/{id} 审计日志详情

### 16.6 登录日志接口

#### GET /api/v1/login-logs 登录日志列表

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| username | string | 登录账号 |
| login_type | int | 类型：1管理后台，2商家小程序，3司机小程序 |
| status | int | 结果：1成功，0失败 |
| start_date | string | 开始日期 |
| end_date | string | 结束日期 |

---

## 17 微信小程序商家端接口组

### 17.1 认证接口

#### POST /api/v1/wechat/merchant/login 微信登录

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| code | string | 是 | 微信登录凭证code |

### 17.2 首页接口

#### GET /api/v1/merchant/home 首页数据

返回轮播广告、常购清单、收藏商品、运营主推。

#### GET /api/v1/merchant/search 智能搜索

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| keyword | string | 搜索关键词 |
| category_id | int | 按分类筛选 |

返回：采购历史匹配 + 运营主推 + 同类推荐，智能排序。

### 17.3 商品接口

#### GET /api/v1/merchant/products 商品列表（仅可见商品）

#### GET /api/v1/merchant/products/{id} 商品详情

#### GET /api/v1/merchant/favorites 收藏商品列表

#### POST /api/v1/merchant/favorites 添加收藏

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| sku_id | int | 是 | SKU ID |

#### DELETE /api/v1/merchant/favorites/{skuId} 取消收藏

### 17.4 购物车接口

#### GET /api/v1/merchant/cart 购物车详情

#### POST /api/v1/merchant/cart/items 添加购物车商品

#### PUT /api/v1/merchant/cart/items/{id} 修改数量

#### DELETE /api/v1/merchant/cart/items/{id} 删除购物车商品

### 17.5 订单接口

#### POST /api/v1/merchant/orders 创建订单

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| address_id | int | 否 | 收货地址ID |
| batch | int | 否 | 配送批次 |
| items | array | 是 | 订单明细 |
| items[].sku_id | int | 是 | SKU ID |
| items[].quantity | decimal | 是 | 数量 |
| remark | string | 否 | 备注 |

#### GET /api/v1/merchant/orders 订单列表

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| status | int | 状态筛选 |

#### GET /api/v1/merchant/orders/{id} 订单详情

#### PUT /api/v1/merchant/orders/{id}/confirm-sign 确认签收

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| has_discrepancy | bool | 否 | 是否有实收差异（默认false） |
| discrepancy_remark | string | 否 | 差异说明 |

### 17.6 账户接口

#### GET /api/v1/merchant/account 账户信息

#### POST /api/v1/merchant/recharge 充值申请

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| amount | decimal | 是 | 充值金额 |
| payment_method | int | 是 | 支付方式 |

### 17.7 消息接口

#### GET /api/v1/merchant/notifications 消息列表

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| type | int | 类型：1系统通知，2订单状态变更，3补货提醒，4库存预警，5账户变动 |
| is_read | int | 已读状态：0未读，1已读 |

#### PUT /api/v1/merchant/notifications/read 标记已读

#### GET /api/v1/merchant/notifications/unread-count 未读数量

### 17.8 补货提醒接口

#### GET /api/v1/merchant/restock-reminders 补货提醒规则列表

#### POST /api/v1/merchant/restock-reminders 新增补货提醒

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| sku_id | int | 是 | SKU ID |
| threshold_quantity | decimal | 是 | 库存阈值 |
| remind_cycle | int | 否 | 提醒周期：1每日，2每周，3仅一次 |

#### PUT /api/v1/merchant/restock-reminders/{id} 编辑补货提醒

#### DELETE /api/v1/merchant/restock-reminders/{id} 删除补货提醒

---

## 18 微信小程序司机端接口组

### 18.1 认证接口

#### POST /api/v1/wechat/driver/login 司机登录

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| phone | string | 是 | 手机号 |
| verify_code | string | 是 | 短信验证码 |

### 18.2 任务接口

#### GET /api/v1/driver/tasks 今日配送任务列表

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| batch | int | 批次筛选 |

> 返回结果中包含 has_urgent/has_important 标记，司机端据此突出展示。

#### GET /api/v1/driver/tasks/{id} 任务详情

含顺序表（delivery_task_sequences）、关联明细、商家地址、联系方式。

### 18.3 配送执行接口

#### POST /api/v1/driver/tracks 上报轨迹

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| delivery_task_id | int | 是 | 配送任务ID |
| latitude | decimal | 是 | 纬度 |
| longitude | decimal | 是 | 经度 |

#### PUT /api/v1/driver/sequences/{id}/arrive 到达商家

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| gps_latitude | decimal | 否 | 到达时纬度 |
| gps_longitude | decimal | 否 | 到达时经度 |

> 自动写入抵达时间流水。

#### PUT /api/v1/driver/sequences/{id}/deliver 确认送达

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| delivery_method | string | 否 | 确认方式：manual/scan/photo/signature |
| delivery_photos | json | 否 | 配送照片 |
| signature_image | string | 否 | 签名图片URL |
| gps_latitude | decimal | 否 | 送达时纬度 |
| gps_longitude | decimal | 否 | 送达时经度 |

#### PUT /api/v1/driver/sequences/{id}/skip 跳过商家

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| skip_reason | string | 是 | 跳过原因 |

### 18.4 签收接口

#### POST /api/v1/driver/signatures 拍照签收/电子签名

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| order_id | int | 是 | 订单ID |
| delivery_task_id | int | 是 | 配送任务ID |
| type | int | 是 | 类型：1拍照签收，2电子签名，3质检照片 |
| image_url | string | 否 | 图片/签名文件地址 |
| signer_name | string | 否 | 签收人 |

#### POST /api/v1/driver/temperatures 记录温度

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| delivery_task_id | int | 是 | 配送任务ID |
| temperature | decimal | 是 | 温度值 |

#### POST /api/v1/driver/discrepancies 标记实收差异

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| order_id | int | 是 | 订单ID |
| order_item_id | int | 否 | 订单明细ID |
| expected_quantity | decimal | 是 | 预期数量 |
| actual_quantity | decimal | 是 | 实际数量 |
| reason | string | 否 | 差异原因 |

### 18.5 车辆故障接口

#### POST /api/v1/driver/vehicle-issues 上报车辆故障

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| vehicle_id | int | 是 | 车辆ID |
| task_id | int | 否 | 关联配送任务ID |
| issue_type | string | 否 | 故障类型：breakdown/accident/tire/battery/engine/other |
| description | string | 是 | 描述 |
| photos | json | 否 | 故障照片 |

### 18.6 历史任务接口

#### GET /api/v1/driver/history 历史配送任务

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| start_date | string | 开始日期 |
| end_date | string | 结束日期 |

---

## 19 文件上传接口组

#### POST /api/v1/files/upload 通用文件上传

| 字段 | 类型 | 必填 | 说明 |
| :--- | :--- | :--- | :--- |
| file | file | 是 | 上传文件（支持 jpg/png/pdf） |
| type | string | 否 | 业务类型：avatar/product/evidence/invoice/signature |

返回：

```json
{
  "code": 200,
  "message": "上传成功",
  "data": {
    "url": "/uploads/2026/07/25/abc123.jpg",
    "filename": "abc123.jpg",
    "size": 102400
  }
}
```

---

## 20 报表导出接口组

所有导出接口统一返回 Excel 文件流（`Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet`）。

#### GET /api/v1/exports/orders 订单导出

| 参数 | 类型 | 说明 |
| :--- | :--- | :--- |
| 同订单列表筛选参数 | | |

#### GET /api/v1/exports/purchase-orders 采购单导出

#### GET /api/v1/exports/discrepancies 差异单导出

#### GET /api/v1/exports/loss-orders 损耗明细导出

#### GET /api/v1/exports/settlements 供应商结算导出

#### GET /api/v1/exports/receivables 应收账款导出

---

## 21 接口模块与数据表映射表

| 接口组 | 主要关联数据表 |
| :--- | :--- |
| 认证 | users, personal_access_tokens, login_logs |
| 用户管理 | users, roles, permissions, model_has_roles, model_has_permissions |
| 供应商管理 | suppliers |
| 商家管理 | merchants, merchant_addresses |
| 配送线路 | delivery_routes |
| 司机管理 | drivers, driver_vehicles, vehicles |
| 商品管理 | categories, products, product_images, skus, sku_barcodes, sku_suppliers |
| 可见性配置 | merchant_sku_visibility |
| 标签关键词 | tags, product_tags, keywords |
| 平台统采 | purchase_items, purchase_orders, purchase_order_items |
| 采购退货 | purchase_returns, purchase_return_items |
| 客户直采 | carts, cart_items, orders, order_items |
| 常购/复购 | frequently_bought, repurchase_templates, repurchase_template_items |
| 售后退货 | order_returns, order_return_items |
| 库存管理 | warehouses, inventory, inventory_logs |
| 损耗管理 | loss_orders, loss_order_items |
| 拣货管理 | picking_tasks, picking_task_items |
| 物流配送 | delivery_tasks, delivery_task_details, delivery_task_sequences, delivery_notes, delivery_note_items, delivery_tracks, signatures, temperatures |
| 差异处理 | discrepancies |
| 财务对账 | merchant_accounts, recharges, supplier_settlements, supplier_settlement_items, settlement_payments, receivables, receivable_payments, invoices, correction_authorizations |
| 价格策略 | price_strategies, price_strategy_items, price_change_logs |
| 费用均摊 | price_apportionments |
| 审核管理 | approvals, approval_type_configs |
| 系统支撑 | system_configs, banners, promotions, operation_logs, audit_logs, login_logs |
| 微信商家端 | wechat_users, merchant_favorites, notifications, restock_reminders |
| 微信司机端 | wechat_users, delivery_tracks, signatures, temperatures |