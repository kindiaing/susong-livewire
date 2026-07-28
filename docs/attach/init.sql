-- ============================================================
-- 本地速送服务平台 — 初始化数据脚本
-- 执行方式：mysql -u账号 -p库名 < docs/attach/init.sql
-- 说明：本脚本仅插入初始数据，表结构由 Laravel Migration 创建
-- 前置条件：php artisan migrate 已执行
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- 1. 系统角色（8 个）
-- ============================================================
INSERT INTO `roles` (`id`, `name`, `guard_name`, `display_name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'super_admin',   'web', '超级管理员', '拥有全部权限，不可删除',      NOW(), NOW()),
(2, 'ops_admin',     'web', '运营管理员', '日常运营操作权限',            NOW(), NOW()),
(3, 'ops_manager',   'web', '运营经理', '审核运营类单据',              NOW(), NOW()),
(4, 'finance_staff', 'web', '财务人员', '日常财务操作权限',            NOW(), NOW()),
(5, 'cashier',       'web', '出纳',     '收付款操作权限',              NOW(), NOW()),
(6, 'finance_mgr',   'web', '财务经理', '审核财务类单据',              NOW(), NOW()),
(7, 'picker',        'web', '拣货员',   '拣货任务执行',              NOW(), NOW()),
(8, 'driver',        'web', '司机',     '配送任务执行',              NOW(), NOW());

-- ============================================================
-- 2. 超级管理员账号（首次登录强制修改密码）
-- ============================================================
INSERT INTO `users` (`id`, `username`, `password`, `name`, `phone`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$12$ykxm.c5o1/q0j0q0q0q0qeH7KZ1LJ3Z4R5W6x7y8z9A0B1C2D3E4F5G', '超级管理员', '13800000000', 1, NOW(), NOW());

-- 关联超级管理员角色
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1);

-- ============================================================
-- 3. 审核类型配置（19 个节点）
-- ============================================================
INSERT INTO `approval_type_configs` (`id`, `type_code`, `type_name`, `module_name`, `risk_level`, `enabled`, `applicant_role_id`, `reviewer_role_id`, `sort_order`, `description`, `created_at`, `updated_at`) VALUES
-- P0 核心资金节点（默认开启）
(1,  'manual_recharge',       '后台手工充值',       '财务对账', 'P0', 1, 4, 6, 1,  '后台手工充值需财务经理审核后入账',                            NOW(), NOW()),
(2,  'supplier_payment',     '供应商付款',         '财务对账', 'P0', 1, 5, 6, 2,  '供应商结算付款需财务经理审核',                              NOW(), NOW()),
(3,  'receivable_refund',    '应收退款',           '财务对账', 'P0', 1, 4, 6, 3,  '售后退货退款需财务经理审核',                                NOW(), NOW()),
(4,  'correction_auth',      '单据授权更正',       '财务对账', 'P0', 1, 1, 6, 4,  '锁定单据更正需财务经理授权',                                NOW(), NOW()),
(5,  'credit_limit_adjust',  '信用额度调整',       '财务对账', 'P0', 1, 2, 6, 5,  '商家信用额度调整需财务经理审核',                            NOW(), NOW()),
(6,  'price_strategy',       '价格策略审批',       '价格策略', 'P0', 1, 2, 6, 6,  '新增/修改价格策略需财务经理审核',                            NOW(), NOW()),
(7,  'wholesale_price_change','批发价大幅变更',     '商品管理', 'P0', 1, 2, 3, 7,  '批发销售价修改幅度>15%需运营经理审核',                      NOW(), NOW()),
(8,  'discrepancy_refund',   '差异退款审批',       '差异处理', 'P0', 1, 2, 6, 8,  '差异决策为退款时需财务经理审核',                            NOW(), NOW()),
(9,  'discrepancy_deduct',   '差异扣款审批',       '差异处理', 'P0', 1, 2, 6, 9,  '差异决策为扣款时需财务经理审核',                            NOW(), NOW()),
(10, 'manual_apportion',     '手动费用均摊',       '财务对账', 'P0', 1, 4, 3, 10, '手动费用均摊需运营经理审核',                                NOW(), NOW()),
-- P1 影响金额计算节点（默认关闭，按业务需要开启）
(11, 'loss_order',           '损耗单审批',         '损耗管理', 'P1', 1, 2, 3, 11, '损耗金额>审批阈值时需运营经理审核',                          NOW(), NOW()),
(12, 'purchase_return',      '采购退货审核',       '平台统采', 'P1', 0, 2, 3, 12, '采购退货需运营经理审核',                                    NOW(), NOW()),
(13, 'order_return',         '售后退货审核',       '客户直采', 'P1', 0, 2, 3, 13, '售后退货需运营经理审核',                                    NOW(), NOW()),
(14, 'settlement_close',     '供应商结算办结',     '财务对账', 'P1', 0, 4, 6, 14, '供应商结算单办结需财务经理确认',                            NOW(), NOW()),
(15, 'receivable_close',     '应收账款办结',       '财务对账', 'P1', 0, 4, 6, 15, '应收账款办结需财务经理确认',                                NOW(), NOW()),
(16, 'batch_lock',           '订单批次锁定',       '客户直采', 'P1', 0, 2, 3, 16, '订单批次锁定需运营经理确认',                                NOW(), NOW()),
(17, 'inventory_adjust',     '库存手动调整',       '库存管理', 'P1', 0, 2, 3, 17, '手动库存调整需运营经理审核',                                NOW(), NOW()),
(18, 'merchant_disable',     '商家禁用',           '组织主体', 'P1', 0, 2, 3, 18, '商家禁用操作需运营经理确认',                                NOW(), NOW()),
(19, 'supplier_disable',     '供应商禁用',         '组织主体', 'P1', 0, 2, 3, 19, '供应商禁用操作需运营经理确认',                              NOW(), NOW());

-- ============================================================
-- 4. 系统配置（system_configs 默认键值，完整 24 条）
-- ============================================================
-- 注意：此 SQL 与 Migration 的初始数据保持一致。
-- 增强字段（default_value, config_type, config_group, label, hint,
-- options, validation_rules, sort_order, is_public, is_readonly）
-- 由 enhance_system_configs_table 迁移自动添加。
-- 以下 INSERT 仅包含建表时的 4 个业务字段 + timestamps，
-- 增强字段通过 ALTER TABLE 后由应用层或后续 Migration 填充。
INSERT INTO `system_configs` (`id`, `config_key`, `config_value`, `description`, `created_at`, `updated_at`) VALUES
(1,  'site_name',                              '本地速送服务平台',   '站点名称',                                               NOW(), NOW()),
(2,  'contact_phone',                          '15690631151',        '客服电话',                                               NOW(), NOW()),
(3,  'max_upload_size_mb',                     '20',                 '管理后台和商家端文件上传限制（MB）',                       NOW(), NOW()),
(4,  'site_icp_number',                        '',                   'ICP 备案号，留空不显示',                                   NOW(), NOW()),
(5,  'site_tech_stack_url',                    'https://laravel.com', '底部版权栏技术栈文字跳转链接',                            NOW(), NOW()),
(6,  'site_developer_name',                    'Seeding',            '底部版权栏显示的开发者名称',                               NOW(), NOW()),
(7,  'site_developer_url',                     '',                   '底部版权栏开发者名称跳转链接，留空只显示文字',               NOW(), NOW()),
(8,  'site_icp_url',                           'https://beian.miit.gov.cn/', '底部版权栏备案号跳转链接',                      NOW(), NOW()),
(9,  'order_auto_confirm_hours',               '24',                 '订单配送完成后的自动签收等待时长（小时）',                 NOW(), NOW()),
(10, 'min_delivery_amount',                    '0',                  '商家下单金额门槛（元），0表示无限制',                     NOW(), NOW()),
(11, 'allow_merchant_self_order',              '1',                  '商家端小程序是否允许自主下单（1=是，0=否）',               NOW(), NOW()),
(12, 'default_delivery_batch',                 '1',                  '默认配送批次：1上午，2下午',                               NOW(), NOW()),
(13, 'delivery_timeout_minutes',               '30',                 '配送任务超时自动标记异常（分钟）',                         NOW(), NOW()),
(14, 'allow_driver_multi_task',                '1',                  '司机并发配送开关（1=允许同时接多单，0=否）',               NOW(), NOW()),
(15, 'max_daily_recharge_amount',              '50000',              '单商家每日充值累计上限（元）',                             NOW(), NOW()),
(16, 'credit_limit_default',                   '5000',               '新注册商家自动分配的信用额度（元）',                       NOW(), NOW()),
(17, 'enable_weighing_auto_debit',             '0',                  '称重差异自动扣款开关（1=自动，0=人工确认）',               NOW(), NOW()),
(18, 'weighing_diff_threshold',                '20',                 '称重差异阈值（百分比），超过需人工确认',                   NOW(), NOW()),
(19, 'inventory_warning_enabled',              '1',                  '库存预警检测开关（1=开启，0=关闭）',                       NOW(), NOW()),
(20, 'inventory_warning_interval_minutes',     '5',                  '库存预警定时检测周期（分钟）',                             NOW(), NOW()),
(21, 'audit_retention_days',                   '90',                 '审计/日志保留天数：0=永久保留，1-180天，到期每日定时清理', NOW(), NOW()),
(22, 'loss_approval_threshold',                '200',                '损耗审批阈值（元）：超过此值需运营经理审核',               NOW(), NOW()),
(23, 'ui_close_on_outside',                    '1',                  '点击通知面板外部区域自动关闭（1=是，0=否）',               NOW(), NOW()),
(24, 'ui_show_footer',                         '1',                  '显示底部版权栏（1=是，0=否）',                             NOW(), NOW());

-- ============================================================
-- 5. 默认仓库（2 个）
-- ============================================================
INSERT INTO `warehouses` (`id`, `name`, `type`, `is_cold_chain`, `address`, `status`, `created_at`, `updated_at`) VALUES
(1, '总仓',       1, 0, '上海市松江区XX路1号', 1, NOW(), NOW()),
(2, '前置仓-冷链', 2, 1, '上海市松江区XX路2号', 1, NOW(), NOW());

-- ============================================================
-- 6. 默认配送线路（2 条）
-- ============================================================
INSERT INTO `delivery_routes` (`id`, `name`, `description`, `sort`, `status`, `created_at`, `updated_at`) VALUES
(1, '线路A-城区',  '覆盖城区商圈',     1, 1, NOW(), NOW()),
(2, '线路B-郊区',  '覆盖郊区餐饮聚集区', 2, 1, NOW(), NOW());

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- 初始化数据汇总
-- ============================================================
-- 角色：8 条（super_admin ~ driver）
-- 超管账号：admin / admin123（首次登录强制修改）
-- 审核节点：19 条（1-10 默认开启，11-19 默认关闭）
-- 系统配置：24 条
-- 仓库：2 条（总仓 + 前置仓）
-- 配送线路：2 条
-- ============================================================
