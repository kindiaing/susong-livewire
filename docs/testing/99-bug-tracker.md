---
AIGC:
  ContentProducer: '001191110102MAD55U9H0F10002'
  ContentPropagator: '001191110102MAD55U9H0F10002'
  Label: '1'
  ProduceID: 'feb3afc6-e229-4af7-b9ef-6e52d8bd55a3'
  PropagateID: 'feb3afc6-e229-4af7-b9ef-6e52d8bd55a3'
  ReservedCode1: '037ad10c-e5e9-4547-8078-88f2c4a47492'
  ReservedCode2: '037ad10c-e5e9-4547-8078-88f2c4a47492'
---

# 99 Bug 跟踪表

> 测试过程中发现的系统 bug 统一记录在此。格式：编号 / 日期 / 来源流程 / 步骤 / 现象 / 严重程度 / 状态

| 编号 | 日期 | 来源流程 | 步骤 | 现象描述 | 严重程度 | 状态 |
|---|---|---|---|---|---|---|
| — | — | — | — | 暂无记录 | — | — |

## 状态说明

- **待修复**：已记录未处理
- **修复中**：开发已认领
- **待验证**：已修复待测试复验
- **已关闭**：复验通过

## 严重程度定义

- **P0**：阻断流程无法继续（如报错 500、数据错乱）
- **P1**：功能不可用但有绕行方案
- **P2**：显示/文案/交互问题，不影响主流程
- **P3**：优化建议

## 已知已修复（历史）

| 日期 | 问题 | 修复提交 |
|---|---|---|
| 2026-09-03 | 通知金额双 ¥ 符号（如 ¥¥260.00），共 5 处调用点 | 18cfe1f |
| 2026-09-03 | 旧逻辑付款数据（审核通过即累计）在新逻辑下被误显「待执行」徽章 | 18cfe1f（数据已修正） |
| 2026-09-03 | 客户账户列表页 500：MerchantAccount 模型从未定义 canEdit()，但 blade 自 8月24日 da1cdc2「列表页通用规范改造」起就调用它，有编辑权限的用户访问即报错（已补 canEdit()/canDelete()） | 2783701 |
| 2026-09-03 | 发票列表/详情页 500：Invoice::merchant()/supplier() 关联带 `where('type',…)` 条件，但 merchants 表无 type 字段，with 预载即报「Unknown column 'type'」 | 18bb5da |
| 2026-09-03 | 改价记录页 500：组件用 with('sku') 但 PriceChangeLog 模型无该关联；且 blade/导出引用 field_name/before_value/after_value 等表里不存在的列（模板套用错误），实际数据是 original_price/new_price/amount_diff（已按真实表结构重写列展示，SKU 经 before_data.sku_id 取） | 18bb5da |
| 2026-09-03 | 发票创建必炸：组件表单/导入映射使用 tax_no/note 字段，但 invoices 建表迁移无这两列（本地库已 ALTER 补列并合并进 create 迁移） | 18bb5da |
| 2026-09-03 | 订单支付状态徽章错显：order-list.blade.php 调 status_badge(status,'order_payment') 但 helpers 无此分支，落入 default（仅 0/1 键），未支付显绿色「启用」、已支付/账期显灰色「未知」（已在 helpers 补 order_payment 分支，随状态后移提交） | 待提交 |