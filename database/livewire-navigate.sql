/*
 Navicat Premium Data Transfer

 Source Server         : laragon
 Source Server Type    : MySQL
 Source Server Version : 80403 (8.4.3)
 Source Host           : localhost:3306
 Source Schema         : livewire

 Target Server Type    : MySQL
 Target Server Version : 80403 (8.4.3)
 File Encoding         : 65001

 Date: 28/07/2026 12:26:39
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for approval_type_configs
-- ----------------------------
DROP TABLE IF EXISTS `approval_type_configs`;
CREATE TABLE `approval_type_configs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `type_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '审核类型编码（唯一）',
  `type_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '审核类型名称',
  `module_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '所属模块名称',
  `risk_level` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'P1' COMMENT '风险等级：P0/P1',
  `enabled` tinyint NOT NULL DEFAULT 0 COMMENT '是否启用审核：0关闭，1开启',
  `applicant_role_id` bigint UNSIGNED NOT NULL COMMENT '申请人角色ID',
  `reviewer_role_id` bigint UNSIGNED NOT NULL COMMENT '审核人角色ID',
  `sort_order` int NOT NULL DEFAULT 0 COMMENT '显示排序',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '审核节点说明',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `approval_type_configs_type_code_unique`(`type_code` ASC) USING BTREE,
  INDEX `approval_type_configs_enabled_index`(`enabled` ASC) USING BTREE,
  INDEX `approval_type_configs_reviewer_role_id_index`(`reviewer_role_id` ASC) USING BTREE,
  INDEX `approval_type_configs_applicant_role_id_index`(`applicant_role_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 20 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '审核类型配置表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of approval_type_configs
-- ----------------------------
INSERT INTO `approval_type_configs` VALUES (1, 'manual_recharge', '后台手工充值', '财务对账', 'P0', 1, 2, 6, 1, '运营管理员为商家手动充值', '2026-07-27 09:23:33', '2026-07-27 09:23:33');
INSERT INTO `approval_type_configs` VALUES (2, 'supplier_payment', '供应商付款录入', '财务对账', 'P0', 1, 5, 6, 2, '出纳录入供应商付款记录', '2026-07-27 09:23:33', '2026-07-27 09:23:33');
INSERT INTO `approval_type_configs` VALUES (3, 'customer_receipt', '客户收款录入', '财务对账', 'P0', 1, 5, 6, 3, '出纳录入客户收款记录', '2026-07-27 09:23:33', '2026-07-27 09:23:33');
INSERT INTO `approval_type_configs` VALUES (4, 'credit_limit', '信用额度调整', '商家管理', 'P0', 1, 2, 6, 4, '修改商家信用额度', '2026-07-27 09:23:33', '2026-07-27 09:23:33');
INSERT INTO `approval_type_configs` VALUES (5, 'price_strategy', '价格策略创建/修改', '价格策略', 'P0', 1, 2, 6, 5, '创建或修改促销/临时改价策略', '2026-07-27 09:23:33', '2026-07-27 09:23:33');
INSERT INTO `approval_type_configs` VALUES (6, 'manual_apportion', '手动均摊调整', '费用均摊', 'P0', 1, 4, 3, 6, '手动修改费用均摊金额', '2026-07-27 09:23:33', '2026-07-27 09:23:33');
INSERT INTO `approval_type_configs` VALUES (7, 'diff_refund_deduct', '差异退款/扣款决策', '差异处理', 'P0', 1, 2, 6, 7, '差异处理决策为退款或扣款', '2026-07-27 09:23:33', '2026-07-27 09:23:33');
INSERT INTO `approval_type_configs` VALUES (8, 'sku_price_change', 'SKU 批发价修改(>15%)', '商品管理', 'P1', 1, 2, 3, 8, '修改SKU批发销售价幅度>15%', '2026-07-27 09:23:33', '2026-07-27 09:23:33');
INSERT INTO `approval_type_configs` VALUES (9, 'receivable_adjust', '应收改价折扣调整', '财务对账', 'P0', 1, 2, 6, 9, '改价/促销导致应收金额调整', '2026-07-27 09:23:33', '2026-07-27 09:23:33');
INSERT INTO `approval_type_configs` VALUES (10, 'recharge_confirm', '商家充值确认', '财务对账', 'P0', 1, 2, 6, 10, '商家微信/线下充值待确认', '2026-07-27 09:23:33', '2026-07-27 09:23:33');
INSERT INTO `approval_type_configs` VALUES (11, 'purchase_return', '采购退货', '平台统采', 'P0', 0, 2, 6, 11, '采购退货审批', '2026-07-27 09:23:33', '2026-07-27 09:23:33');
INSERT INTO `approval_type_configs` VALUES (12, 'after_sale_return', '售后退货退款', '客户直采', 'P0', 0, 2, 6, 12, '售后退货退款审批', '2026-07-27 09:23:33', '2026-07-27 09:23:33');
INSERT INTO `approval_type_configs` VALUES (13, 'auth_correction', '单据授权更正', '财务对账', 'P0', 0, 4, 6, 13, '解锁已锁定数据允许更正', '2026-07-27 09:23:33', '2026-07-27 09:23:33');
INSERT INTO `approval_type_configs` VALUES (14, 'weighing_price', '称重改价(≤20%)', '客户直采', 'P1', 0, 7, 3, 14, '称重改价金额生效', '2026-07-27 09:23:33', '2026-07-27 09:23:33');
INSERT INTO `approval_type_configs` VALUES (15, 'purchase_warehouse', '采购入库确认', '平台统采', 'P1', 0, 2, 3, 15, '入库确认触发库存联动', '2026-07-27 09:23:33', '2026-07-27 09:23:33');
INSERT INTO `approval_type_configs` VALUES (16, 'supplier_bank_edit', '供应商银行信息修改', '组织主体', 'P1', 0, 2, 6, 16, '银行收付款信息生效', '2026-07-27 09:23:33', '2026-07-27 09:23:33');
INSERT INTO `approval_type_configs` VALUES (17, 'manual_close', '手动办结', '财务对账', 'P1', 0, 4, 6, 17, '单据办结锁定', '2026-07-27 09:23:33', '2026-07-27 09:23:33');
INSERT INTO `approval_type_configs` VALUES (18, 'sku_price_minor', 'SKU小幅改价(≤15%)', '商品管理', 'P1', 0, 2, 3, 18, '小幅改价生效', '2026-07-27 09:23:33', '2026-07-27 09:23:33');
INSERT INTO `approval_type_configs` VALUES (19, 'loss_order', '损耗单审批', '损耗管理', 'P1', 1, 2, 3, 19, '损耗金额超过审批阈值时需运营经理审核', '2026-07-27 09:23:33', '2026-07-27 09:23:33');

-- ----------------------------
-- Table structure for approvals
-- ----------------------------
DROP TABLE IF EXISTS `approvals`;
CREATE TABLE `approvals`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `approval_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '审核类型编码，关联 approval_type_configs.type_code',
  `target_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '关联单据类型',
  `target_id` bigint UNSIGNED NOT NULL COMMENT '关联单据ID',
  `applicant_id` bigint UNSIGNED NOT NULL COMMENT '申请人ID',
  `applicant_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '申请人姓名',
  `before_data` json NULL COMMENT '操作前数据快照',
  `after_data` json NULL COMMENT '操作后数据快照',
  `amount` bigint NULL DEFAULT NULL COMMENT '涉及金额',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：1待审核，2已通过，3已拒绝，4已撤回',
  `reviewer_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '审核人ID',
  `reviewer_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '审核人姓名',
  `review_remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '审核备注（拒绝原因等）',
  `reviewed_at` timestamp NULL DEFAULT NULL COMMENT '审核时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `approvals_approval_type_index`(`approval_type` ASC) USING BTREE,
  INDEX `approvals_target_type_target_id_index`(`target_type` ASC, `target_id` ASC) USING BTREE,
  INDEX `approvals_applicant_id_index`(`applicant_id` ASC) USING BTREE,
  INDEX `approvals_status_index`(`status` ASC) USING BTREE,
  INDEX `approvals_reviewer_id_index`(`reviewer_id` ASC) USING BTREE,
  INDEX `approvals_created_at_index`(`created_at` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '审批记录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of approvals
-- ----------------------------

-- ----------------------------
-- Table structure for audit_logs
-- ----------------------------
DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `model_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '模型类型',
  `model_id` bigint UNSIGNED NOT NULL COMMENT '模型ID',
  `action` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '操作动作',
  `before_data` json NULL COMMENT '修改前数据',
  `after_data` json NULL COMMENT '修改后数据',
  `operator_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '操作人ID',
  `ip` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '操作人IP地址',
  `user_agent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '浏览器/客户端UA',
  `reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '操作原因',
  `relation_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '关联类型',
  `relation_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '关联ID',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `audit_logs_model_type_model_id_index`(`model_type` ASC, `model_id` ASC) USING BTREE,
  INDEX `audit_logs_operator_id_index`(`operator_id` ASC) USING BTREE,
  INDEX `audit_logs_created_at_index`(`created_at` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '审计日志表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of audit_logs
-- ----------------------------

-- ----------------------------
-- Table structure for banners
-- ----------------------------
DROP TABLE IF EXISTS `banners`;
CREATE TABLE `banners`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标题',
  `image_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '图片地址',
  `link_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '跳转链接',
  `sort` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：0禁用，1启用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `banners_status_index`(`status` ASC) USING BTREE,
  INDEX `banners_sort_index`(`sort` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '轮播广告表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of banners
-- ----------------------------

-- ----------------------------
-- Table structure for cart_items
-- ----------------------------
DROP TABLE IF EXISTS `cart_items`;
CREATE TABLE `cart_items`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `cart_id` bigint UNSIGNED NOT NULL COMMENT '购物车ID',
  `sku_id` bigint UNSIGNED NOT NULL COMMENT 'SKU ID',
  `quantity` bigint NOT NULL DEFAULT 0 COMMENT '数量',
  `price` bigint NOT NULL DEFAULT 0 COMMENT '加入时单价',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `cart_items_cart_id_index`(`cart_id` ASC) USING BTREE,
  INDEX `cart_items_sku_id_index`(`sku_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '购物车明细表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cart_items
-- ----------------------------

-- ----------------------------
-- Table structure for carts
-- ----------------------------
DROP TABLE IF EXISTS `carts`;
CREATE TABLE `carts`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `merchant_id` bigint UNSIGNED NOT NULL COMMENT '商家ID',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `carts_merchant_id_unique`(`merchant_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '购物车表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of carts
-- ----------------------------

-- ----------------------------
-- Table structure for categories
-- ----------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `parent_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '父级分类ID，0为根节点',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '分类名称',
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '图标',
  `sort` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：0禁用，1启用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `categories_parent_id_index`(`parent_id` ASC) USING BTREE,
  INDEX `categories_status_index`(`status` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '商品分类表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of categories
-- ----------------------------
INSERT INTO `categories` VALUES (1, 0, '蔬菜', NULL, 1, 1, '2026-07-27 09:23:35', '2026-07-27 09:23:35', NULL);
INSERT INTO `categories` VALUES (2, 0, '水果', NULL, 2, 1, '2026-07-27 09:23:35', '2026-07-27 09:23:35', NULL);
INSERT INTO `categories` VALUES (3, 0, '肉类', NULL, 3, 1, '2026-07-27 09:23:35', '2026-07-27 09:23:35', NULL);
INSERT INTO `categories` VALUES (4, 0, '水产', NULL, 4, 1, '2026-07-27 09:23:35', '2026-07-27 09:23:35', NULL);
INSERT INTO `categories` VALUES (5, 0, '粮油', NULL, 5, 1, '2026-07-27 09:23:35', '2026-07-27 09:23:35', NULL);
INSERT INTO `categories` VALUES (6, 0, '调料', NULL, 6, 1, '2026-07-27 09:23:35', '2026-07-27 09:23:35', NULL);
INSERT INTO `categories` VALUES (7, 0, '豆制品', NULL, 7, 1, '2026-07-27 09:23:35', '2026-07-27 09:23:35', NULL);
INSERT INTO `categories` VALUES (8, 0, '冷冻食品', NULL, 8, 1, '2026-07-27 09:23:35', '2026-07-27 09:23:35', NULL);

-- ----------------------------
-- Table structure for correction_authorizations
-- ----------------------------
DROP TABLE IF EXISTS `correction_authorizations`;
CREATE TABLE `correction_authorizations`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `order_id` bigint UNSIGNED NOT NULL COMMENT '订单ID',
  `operator_id` bigint UNSIGNED NOT NULL COMMENT '授权人ID',
  `reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '更正原因',
  `before_data` json NULL COMMENT '修改前数据',
  `after_data` json NULL COMMENT '修改后数据',
  `authorized_at` timestamp NULL DEFAULT NULL COMMENT '授权时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `correction_authorizations_order_id_index`(`order_id` ASC) USING BTREE,
  INDEX `correction_authorizations_operator_id_index`(`operator_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '单据授权更正表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of correction_authorizations
-- ----------------------------

-- ----------------------------
-- Table structure for delivery_routes
-- ----------------------------
DROP TABLE IF EXISTS `delivery_routes`;
CREATE TABLE `delivery_routes`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '线路名称',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '描述',
  `sort` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `delivery_routes_status_index`(`status` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '配送线路表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of delivery_routes
-- ----------------------------
INSERT INTO `delivery_routes` VALUES (1, '城区北线', '人民路-淮海路-汴河路北侧', 0, 1, '2026-07-27 09:23:35', '2026-07-27 09:23:35', NULL);
INSERT INTO `delivery_routes` VALUES (2, '城区南线', '银河路-胜利路-宿怀路南侧', 0, 1, '2026-07-27 09:23:35', '2026-07-27 09:23:35', NULL);

-- ----------------------------
-- Table structure for delivery_task_orders
-- ----------------------------
DROP TABLE IF EXISTS `delivery_task_orders`;
CREATE TABLE `delivery_task_orders`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `delivery_task_id` bigint UNSIGNED NOT NULL COMMENT '配送任务ID',
  `order_id` bigint UNSIGNED NOT NULL COMMENT '订单ID',
  `delivery_sort` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '配送顺序',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：1待配送，2已送达',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `delivery_task_orders_delivery_task_id_index`(`delivery_task_id` ASC) USING BTREE,
  INDEX `delivery_task_orders_order_id_index`(`order_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '配送任务订单关联表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of delivery_task_orders
-- ----------------------------

-- ----------------------------
-- Table structure for delivery_tasks
-- ----------------------------
DROP TABLE IF EXISTS `delivery_tasks`;
CREATE TABLE `delivery_tasks`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `task_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '任务编号',
  `delivery_route_id` bigint UNSIGNED NOT NULL COMMENT '线路ID',
  `driver_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '司机ID',
  `vehicle_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '车辆ID',
  `batch` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '配送批次：1上午，2下午',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：1待配送，2配送中，3任务完成',
  `planned_at` timestamp NULL DEFAULT NULL COMMENT '计划配送时间',
  `started_at` timestamp NULL DEFAULT NULL COMMENT '开始时间',
  `completed_at` timestamp NULL DEFAULT NULL COMMENT '完成时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `delivery_tasks_task_no_unique`(`task_no` ASC) USING BTREE,
  INDEX `delivery_tasks_delivery_route_id_index`(`delivery_route_id` ASC) USING BTREE,
  INDEX `delivery_tasks_driver_id_index`(`driver_id` ASC) USING BTREE,
  INDEX `delivery_tasks_status_index`(`status` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '配送任务表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of delivery_tasks
-- ----------------------------

-- ----------------------------
-- Table structure for delivery_tracks
-- ----------------------------
DROP TABLE IF EXISTS `delivery_tracks`;
CREATE TABLE `delivery_tracks`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `delivery_task_id` bigint UNSIGNED NOT NULL COMMENT '配送任务ID',
  `driver_id` bigint UNSIGNED NOT NULL COMMENT '司机ID',
  `latitude` int NOT NULL DEFAULT 0 COMMENT '纬度',
  `longitude` int NOT NULL DEFAULT 0 COMMENT '经度',
  `location_desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '位置描述',
  `reported_at` timestamp NULL DEFAULT NULL COMMENT '上报时间',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `delivery_tracks_delivery_task_id_index`(`delivery_task_id` ASC) USING BTREE,
  INDEX `delivery_tracks_driver_id_index`(`driver_id` ASC) USING BTREE,
  INDEX `delivery_tracks_reported_at_index`(`reported_at` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '配送轨迹表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of delivery_tracks
-- ----------------------------

-- ----------------------------
-- Table structure for discrepancies
-- ----------------------------
DROP TABLE IF EXISTS `discrepancies`;
CREATE TABLE `discrepancies`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `discrepancy_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '差异单号',
  `order_id` bigint UNSIGNED NOT NULL COMMENT '关联订单ID',
  `order_item_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '关联订单明细ID',
  `stage` tinyint UNSIGNED NOT NULL COMMENT '差异环节：1拣货，2配送，3实收',
  `type` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '差异类型：1少收，2拒收，3残次，4其他',
  `expected_quantity` bigint NOT NULL DEFAULT 0 COMMENT '预期数量',
  `actual_quantity` bigint NOT NULL DEFAULT 0 COMMENT '实际数量',
  `quantity_diff` bigint NOT NULL DEFAULT 0 COMMENT '数量差异',
  `amount_diff` bigint NOT NULL DEFAULT 0 COMMENT '金额差异',
  `reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '差异原因',
  `evidence_urls` json NULL COMMENT '凭证图片数组',
  `responsible_party` tinyint UNSIGNED NULL DEFAULT NULL COMMENT '责任方：1供应商，2平台，3司机，4商家',
  `decision` tinyint UNSIGNED NULL DEFAULT NULL COMMENT '处理决策：1补货，2退款，3扣款，4报损，5不计',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：1待处理，2处理中，3已处理，4已关闭，5争议中',
  `handler_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '处理人ID',
  `handled_at` timestamp NULL DEFAULT NULL COMMENT '处理时间',
  `is_amount_adjusted` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否已调整金额',
  `approval_status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '审核状态：1待审核，2已通过，3已拒绝（决策为退款/扣款时有效）',
  `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `discrepancies_discrepancy_no_unique`(`discrepancy_no` ASC) USING BTREE,
  INDEX `discrepancies_order_id_index`(`order_id` ASC) USING BTREE,
  INDEX `discrepancies_order_item_id_index`(`order_item_id` ASC) USING BTREE,
  INDEX `discrepancies_stage_index`(`stage` ASC) USING BTREE,
  INDEX `discrepancies_status_index`(`status` ASC) USING BTREE,
  INDEX `discrepancies_approval_status_index`(`approval_status` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '差异单表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of discrepancies
-- ----------------------------

-- ----------------------------
-- Table structure for driver_vehicles
-- ----------------------------
DROP TABLE IF EXISTS `driver_vehicles`;
CREATE TABLE `driver_vehicles`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `driver_id` bigint UNSIGNED NOT NULL COMMENT '司机ID',
  `vehicle_id` bigint UNSIGNED NOT NULL COMMENT '车辆ID',
  `is_default` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否默认车辆',
  `bound_at` timestamp NULL DEFAULT NULL COMMENT '绑定时间',
  `unbound_at` timestamp NULL DEFAULT NULL COMMENT '解绑时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `driver_vehicles_driver_id_index`(`driver_id` ASC) USING BTREE,
  INDEX `driver_vehicles_vehicle_id_index`(`vehicle_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '司机车辆绑定表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of driver_vehicles
-- ----------------------------
INSERT INTO `driver_vehicles` VALUES (1, 1, 1, 0, NULL, NULL, '2026-07-27 09:23:35', '2026-07-27 09:23:35');
INSERT INTO `driver_vehicles` VALUES (2, 2, 2, 0, NULL, NULL, '2026-07-27 09:23:35', '2026-07-27 09:23:35');

-- ----------------------------
-- Table structure for drivers
-- ----------------------------
DROP TABLE IF EXISTS `drivers`;
CREATE TABLE `drivers`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '关联登录用户ID',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '姓名',
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '手机号',
  `id_card` varchar(18) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '身份证号',
  `online_status` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '在线状态：0离线，1在线',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：0禁用，1启用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `drivers_phone_unique`(`phone` ASC) USING BTREE,
  INDEX `drivers_user_id_index`(`user_id` ASC) USING BTREE,
  INDEX `drivers_status_index`(`status` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '司机表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of drivers
-- ----------------------------
INSERT INTO `drivers` VALUES (1, NULL, '周师傅', '13700000001', NULL, 0, 1, '2026-07-27 09:23:35', '2026-07-27 09:23:35', NULL);
INSERT INTO `drivers` VALUES (2, NULL, '马师傅', '13700000002', NULL, 0, 1, '2026-07-27 09:23:35', '2026-07-27 09:23:35', NULL);

-- ----------------------------
-- Table structure for frequently_bought
-- ----------------------------
DROP TABLE IF EXISTS `frequently_bought`;
CREATE TABLE `frequently_bought`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `merchant_id` bigint UNSIGNED NOT NULL COMMENT '商家ID',
  `sku_id` bigint UNSIGNED NOT NULL COMMENT 'SKU ID',
  `buy_count` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '购买次数',
  `last_buy_at` timestamp NULL DEFAULT NULL COMMENT '最近购买时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `frequently_bought_merchant_id_sku_id_unique`(`merchant_id` ASC, `sku_id` ASC) USING BTREE,
  INDEX `frequently_bought_sku_id_index`(`sku_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '常购清单表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of frequently_bought
-- ----------------------------

-- ----------------------------
-- Table structure for inventory
-- ----------------------------
DROP TABLE IF EXISTS `inventory`;
CREATE TABLE `inventory`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `warehouse_id` bigint UNSIGNED NOT NULL COMMENT '仓库ID',
  `sku_id` bigint UNSIGNED NOT NULL COMMENT 'SKU ID',
  `total_stock` bigint NOT NULL DEFAULT 0 COMMENT '总库存',
  `locked_stock` bigint NOT NULL DEFAULT 0 COMMENT '锁定库存',
  `available_stock` bigint NOT NULL DEFAULT 0 COMMENT '可用库存',
  `batch_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '入库批次号',
  `expiry_date` date NULL DEFAULT NULL COMMENT '效期',
  `warning_value` bigint NOT NULL DEFAULT 0 COMMENT '预警值',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `inventory_warehouse_id_sku_id_batch_no_unique`(`warehouse_id` ASC, `sku_id` ASC, `batch_no` ASC) USING BTREE,
  INDEX `inventory_sku_id_index`(`sku_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '实时库存表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of inventory
-- ----------------------------

-- ----------------------------
-- Table structure for inventory_logs
-- ----------------------------
DROP TABLE IF EXISTS `inventory_logs`;
CREATE TABLE `inventory_logs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `warehouse_id` bigint UNSIGNED NOT NULL COMMENT '仓库ID',
  `sku_id` bigint UNSIGNED NOT NULL COMMENT 'SKU ID',
  `type` tinyint UNSIGNED NOT NULL COMMENT '变动类型：1入库，2出库，3调拨，4报损，5报溢，6调整',
  `quantity` bigint NOT NULL DEFAULT 0 COMMENT '变动数量，正增负减',
  `before_stock` bigint NOT NULL DEFAULT 0 COMMENT '变动前库存',
  `after_stock` bigint NOT NULL DEFAULT 0 COMMENT '变动后库存',
  `reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '变动原因',
  `operator_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '操作人ID',
  `source_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '业务来源类型',
  `source_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '业务来源ID',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `inventory_logs_warehouse_id_index`(`warehouse_id` ASC) USING BTREE,
  INDEX `inventory_logs_sku_id_index`(`sku_id` ASC) USING BTREE,
  INDEX `inventory_logs_type_index`(`type` ASC) USING BTREE,
  INDEX `inventory_logs_created_at_index`(`created_at` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '库存变动日志表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of inventory_logs
-- ----------------------------

-- ----------------------------
-- Table structure for invoices
-- ----------------------------
DROP TABLE IF EXISTS `invoices`;
CREATE TABLE `invoices`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `invoice_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '发票号',
  `type` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '类型：1客户发票，2供应商发票',
  `target_id` bigint UNSIGNED NOT NULL COMMENT '关联对象ID',
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '发票抬头',
  `amount` bigint NOT NULL DEFAULT 0 COMMENT '金额',
  `file_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '发票文件地址',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：1待开具，2已开具，3已寄出',
  `applied_at` timestamp NULL DEFAULT NULL COMMENT '申请时间',
  `issued_at` timestamp NULL DEFAULT NULL COMMENT '开具时间',
  `sent_at` timestamp NULL DEFAULT NULL COMMENT '寄出时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `invoices_target_id_index`(`target_id` ASC) USING BTREE,
  INDEX `invoices_type_index`(`type` ASC) USING BTREE,
  INDEX `invoices_status_index`(`status` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '发票表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of invoices
-- ----------------------------

-- ----------------------------
-- Table structure for keywords
-- ----------------------------
DROP TABLE IF EXISTS `keywords`;
CREATE TABLE `keywords`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `keyword` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '关键词',
  `product_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '关联商品ID',
  `search_count` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '搜索次数',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `keywords_product_id_index`(`product_id` ASC) USING BTREE,
  INDEX `keywords_keyword_index`(`keyword` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '搜索关键词表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of keywords
-- ----------------------------

-- ----------------------------
-- Table structure for login_logs
-- ----------------------------
DROP TABLE IF EXISTS `login_logs`;
CREATE TABLE `login_logs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '用户ID',
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '登录账号',
  `ip` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'IP地址',
  `user_agent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '浏览器/客户端UA',
  `login_type` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '类型：1管理后台，2商家小程序，3司机小程序',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '结果：1成功，0失败',
  `fail_reason` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '失败原因',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '登录时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `login_logs_user_id_index`(`user_id` ASC) USING BTREE,
  INDEX `login_logs_username_index`(`username` ASC) USING BTREE,
  INDEX `login_logs_ip_index`(`ip` ASC) USING BTREE,
  INDEX `login_logs_created_at_index`(`created_at` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 17 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '登录日志表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of login_logs
-- ----------------------------
INSERT INTO `login_logs` VALUES (1, 1, 'seeding', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 1, NULL, '2026-07-27 09:41:38');
INSERT INTO `login_logs` VALUES (2, 1, 'seeding', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 1, NULL, '2026-07-27 09:43:02');
INSERT INTO `login_logs` VALUES (3, 1, 'seeding', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 1, NULL, '2026-07-27 09:45:27');
INSERT INTO `login_logs` VALUES (4, 1, 'seeding', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 1, 1, NULL, '2026-07-27 09:52:24');
INSERT INTO `login_logs` VALUES (5, 1, 'seeding', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 1, 1, NULL, '2026-07-28 00:48:20');
INSERT INTO `login_logs` VALUES (6, 1, 'seeding', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 1, NULL, '2026-07-28 00:52:04');
INSERT INTO `login_logs` VALUES (7, 1, 'seeding', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 1, 1, NULL, '2026-07-28 01:03:05');
INSERT INTO `login_logs` VALUES (8, 1, 'seeding', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 1, 1, NULL, '2026-07-28 03:13:00');
INSERT INTO `login_logs` VALUES (9, 1, 'seeding', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 1, 1, NULL, '2026-07-28 03:37:47');
INSERT INTO `login_logs` VALUES (10, 1, 'seeding', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 1, 1, NULL, '2026-07-28 03:40:27');
INSERT INTO `login_logs` VALUES (11, 1, 'seeding', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 1, NULL, '2026-07-28 03:42:56');
INSERT INTO `login_logs` VALUES (12, 1, 'seeding', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 1, 1, NULL, '2026-07-28 04:00:05');
INSERT INTO `login_logs` VALUES (13, 1, 'seeding', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 1, 1, NULL, '2026-07-28 04:05:03');
INSERT INTO `login_logs` VALUES (14, 1, 'seeding', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 1, 1, NULL, '2026-07-28 04:11:06');
INSERT INTO `login_logs` VALUES (15, 1, 'seeding', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 1, 1, NULL, '2026-07-28 04:14:47');
INSERT INTO `login_logs` VALUES (16, 1, 'seeding', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 1, 1, NULL, '2026-07-28 04:18:19');

-- ----------------------------
-- Table structure for loss_order_items
-- ----------------------------
DROP TABLE IF EXISTS `loss_order_items`;
CREATE TABLE `loss_order_items`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `loss_order_id` bigint UNSIGNED NOT NULL COMMENT '损耗单ID',
  `sku_id` bigint UNSIGNED NOT NULL COMMENT 'SKU ID',
  `loss_type` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '损耗类型：1存储腐坏，2称重失水，3过期报废，4加工损耗，5盘点差异，6其他',
  `quantity` bigint NOT NULL DEFAULT 0 COMMENT '损耗数量',
  `cost_price` bigint NOT NULL DEFAULT 0 COMMENT 'SKU成本价快照',
  `loss_amount` bigint NOT NULL DEFAULT 0 COMMENT '损耗金额',
  `responsible_party` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '责任方：1平台，2供应商',
  `supplier_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '供应商ID',
  `reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '明细损耗原因',
  `evidence_urls` json NULL COMMENT '凭证图片数组',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `loss_order_items_loss_order_id_index`(`loss_order_id` ASC) USING BTREE,
  INDEX `loss_order_items_sku_id_index`(`sku_id` ASC) USING BTREE,
  INDEX `loss_order_items_loss_type_index`(`loss_type` ASC) USING BTREE,
  INDEX `loss_order_items_supplier_id_index`(`supplier_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '损耗单明细表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of loss_order_items
-- ----------------------------

-- ----------------------------
-- Table structure for loss_orders
-- ----------------------------
DROP TABLE IF EXISTS `loss_orders`;
CREATE TABLE `loss_orders`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `loss_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '损耗单号',
  `warehouse_id` bigint UNSIGNED NOT NULL COMMENT '仓库ID',
  `total_amount` bigint NOT NULL DEFAULT 0 COMMENT '损耗总金额',
  `loss_type` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '主要损耗类型：1存储腐坏，2称重失水，3过期报废，4加工损耗，5盘点差异，6其他',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：1待审核，2已通过，3已执行，4已关闭，9已取消',
  `approval_status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '审核状态：1待审核，2已通过，3已拒绝',
  `applicant_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '申请人ID',
  `reviewer_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '审核人ID',
  `reviewed_at` timestamp NULL DEFAULT NULL COMMENT '审核时间',
  `executed_at` timestamp NULL DEFAULT NULL COMMENT '执行时间',
  `closed_at` timestamp NULL DEFAULT NULL COMMENT '关闭时间',
  `reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '损耗原因',
  `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `loss_orders_loss_no_unique`(`loss_no` ASC) USING BTREE,
  INDEX `loss_orders_warehouse_id_index`(`warehouse_id` ASC) USING BTREE,
  INDEX `loss_orders_loss_type_index`(`loss_type` ASC) USING BTREE,
  INDEX `loss_orders_status_index`(`status` ASC) USING BTREE,
  INDEX `loss_orders_approval_status_index`(`approval_status` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '损耗单主表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of loss_orders
-- ----------------------------

-- ----------------------------
-- Table structure for merchant_accounts
-- ----------------------------
DROP TABLE IF EXISTS `merchant_accounts`;
CREATE TABLE `merchant_accounts`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `merchant_id` bigint UNSIGNED NOT NULL COMMENT '商家ID',
  `balance` bigint NOT NULL DEFAULT 0 COMMENT '账户余额',
  `total_recharge` bigint NOT NULL DEFAULT 0 COMMENT '总充值',
  `total_consumption` bigint NOT NULL DEFAULT 0 COMMENT '总消费',
  `credit_limit` bigint NOT NULL DEFAULT 0 COMMENT '信用额度',
  `approval_status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '审核状态：1待审核，2已通过，3已拒绝（信用额度调整时有效）',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `merchant_accounts_merchant_id_unique`(`merchant_id` ASC) USING BTREE,
  INDEX `merchant_accounts_approval_status_index`(`approval_status` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '商家账户表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of merchant_accounts
-- ----------------------------
INSERT INTO `merchant_accounts` VALUES (1, 1, 0, 0, 0, 5000000, 2, '2026-07-27 09:23:35', '2026-07-27 09:23:35');
INSERT INTO `merchant_accounts` VALUES (2, 2, 0, 0, 0, 5000000, 2, '2026-07-27 09:23:35', '2026-07-27 09:23:35');
INSERT INTO `merchant_accounts` VALUES (3, 3, 0, 0, 0, 5000000, 2, '2026-07-27 09:23:35', '2026-07-27 09:23:35');
INSERT INTO `merchant_accounts` VALUES (4, 4, 0, 0, 0, 5000000, 2, '2026-07-27 09:23:35', '2026-07-27 09:23:35');
INSERT INTO `merchant_accounts` VALUES (5, 5, 0, 0, 0, 5000000, 2, '2026-07-27 09:23:35', '2026-07-27 09:23:35');

-- ----------------------------
-- Table structure for merchant_addresses
-- ----------------------------
DROP TABLE IF EXISTS `merchant_addresses`;
CREATE TABLE `merchant_addresses`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `merchant_id` bigint UNSIGNED NOT NULL COMMENT '商家ID',
  `contact_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '联系人',
  `contact_phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '联系电话',
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '收货地址',
  `is_default` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否默认地址：0否，1是',
  `sort` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `merchant_addresses_merchant_id_index`(`merchant_id` ASC) USING BTREE,
  INDEX `merchant_addresses_is_default_index`(`is_default` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '商家收货地址表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of merchant_addresses
-- ----------------------------

-- ----------------------------
-- Table structure for merchant_favorites
-- ----------------------------
DROP TABLE IF EXISTS `merchant_favorites`;
CREATE TABLE `merchant_favorites`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `merchant_id` bigint UNSIGNED NOT NULL COMMENT '商家ID',
  `sku_id` bigint UNSIGNED NOT NULL COMMENT 'SKU ID',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `merchant_favorites_merchant_id_sku_id_unique`(`merchant_id` ASC, `sku_id` ASC) USING BTREE,
  INDEX `merchant_favorites_sku_id_index`(`sku_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '商家收藏商品表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of merchant_favorites
-- ----------------------------

-- ----------------------------
-- Table structure for merchant_sku_visibility
-- ----------------------------
DROP TABLE IF EXISTS `merchant_sku_visibility`;
CREATE TABLE `merchant_sku_visibility`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `merchant_id` bigint UNSIGNED NOT NULL COMMENT '商家ID',
  `sku_id` bigint UNSIGNED NOT NULL COMMENT 'SKU ID',
  `is_visible` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否可见：0否，1是',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `merchant_sku_visibility_merchant_id_sku_id_unique`(`merchant_id` ASC, `sku_id` ASC) USING BTREE,
  INDEX `merchant_sku_visibility_sku_id_index`(`sku_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '商家SKU可见性表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of merchant_sku_visibility
-- ----------------------------

-- ----------------------------
-- Table structure for merchants
-- ----------------------------
DROP TABLE IF EXISTS `merchants`;
CREATE TABLE `merchants`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '关联登录用户ID',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '商家名称',
  `contact_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '联系人',
  `contact_phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '联系电话',
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '默认配送地址',
  `delivery_route_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '所属配送线路ID',
  `delivery_sort` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '配送顺序',
  `min_order_amount` bigint NOT NULL DEFAULT 0 COMMENT '起送价',
  `settlement_type` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '结算方式：1现结，2账期，3预付款',
  `credit_limit` bigint NOT NULL DEFAULT 0 COMMENT '信用额度',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：0禁用，1启用',
  `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `merchants_user_id_index`(`user_id` ASC) USING BTREE,
  INDEX `merchants_delivery_route_id_index`(`delivery_route_id` ASC) USING BTREE,
  INDEX `merchants_status_index`(`status` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '商家表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of merchants
-- ----------------------------
INSERT INTO `merchants` VALUES (1, NULL, '味之初餐饮店', '吴老板', '15800000001', '安徽省宿州市埇桥区人民路88号', NULL, 0, 0, 1, 0, 1, NULL, '2026-07-27 09:23:35', '2026-07-27 09:23:35', NULL);
INSERT INTO `merchants` VALUES (2, NULL, '鲜之味快餐店', '郑老板', '15800000002', '安徽省宿州市埇桥区淮海路120号', NULL, 0, 0, 1, 0, 1, NULL, '2026-07-27 09:23:35', '2026-07-27 09:23:35', NULL);
INSERT INTO `merchants` VALUES (3, NULL, '家常菜馆', '冯老板', '15800000003', '安徽省宿州市埇桥区汴河路56号', NULL, 0, 0, 1, 0, 1, NULL, '2026-07-27 09:23:35', '2026-07-27 09:23:35', NULL);
INSERT INTO `merchants` VALUES (4, NULL, '鑫鑫小吃店', '蒋老板', '15800000004', '安徽省宿州市埇桥区银河一路32号', NULL, 0, 0, 1, 0, 1, NULL, '2026-07-27 09:23:35', '2026-07-27 09:23:35', NULL);
INSERT INTO `merchants` VALUES (5, NULL, '老街坊饭店', '韩老板', '15800000005', '安徽省宿州市埇桥区胜利路18号', NULL, 0, 0, 1, 0, 1, NULL, '2026-07-27 09:23:35', '2026-07-27 09:23:35', NULL);

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 26 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of migrations
-- ----------------------------
INSERT INTO `migrations` VALUES (1, '2026_07_27_085827_create_users_and_permissions_tables', 1);
INSERT INTO `migrations` VALUES (2, '2026_07_27_085834_create_organization_tables', 1);
INSERT INTO `migrations` VALUES (3, '2026_07_27_085835_create_product_tables', 1);
INSERT INTO `migrations` VALUES (4, '2026_07_27_085836_create_sku_barcodes_suppliers_tables', 1);
INSERT INTO `migrations` VALUES (5, '2026_07_27_085837_create_purchase_tables', 1);
INSERT INTO `migrations` VALUES (6, '2026_07_27_085838_create_order_tables', 1);
INSERT INTO `migrations` VALUES (7, '2026_07_27_085839_create_inventory_tables', 1);
INSERT INTO `migrations` VALUES (8, '2026_07_27_085840_create_loss_tables', 1);
INSERT INTO `migrations` VALUES (9, '2026_07_27_085841_create_picking_tables', 1);
INSERT INTO `migrations` VALUES (10, '2026_07_27_085842_create_delivery_tables', 1);
INSERT INTO `migrations` VALUES (11, '2026_07_27_085843_create_discrepancy_tables', 1);
INSERT INTO `migrations` VALUES (12, '2026_07_27_085844_create_finance_tables', 1);
INSERT INTO `migrations` VALUES (13, '2026_07_27_085845_create_system_tables', 1);
INSERT INTO `migrations` VALUES (14, '2026_07_27_085846_create_wechat_tables', 1);
INSERT INTO `migrations` VALUES (15, '2026_07_27_085847_create_price_strategy_tables', 1);
INSERT INTO `migrations` VALUES (16, '2026_07_27_085848_create_return_tables', 1);
INSERT INTO `migrations` VALUES (17, '2026_07_27_085849_create_price_apportionment_tables', 1);
INSERT INTO `migrations` VALUES (18, '2026_07_27_085850_create_merchant_extension_tables', 1);
INSERT INTO `migrations` VALUES (19, '2026_07_27_085851_create_notification_tables', 1);
INSERT INTO `migrations` VALUES (20, '2026_07_27_085852_create_approval_tables', 1);
INSERT INTO `migrations` VALUES (21, '2026_07_27_091605_add_price_strategy_fields_to_order_items_and_purchase_order_items', 1);
INSERT INTO `migrations` VALUES (22, '2026_07_28_004921_enhance_system_configs_table', 2);
INSERT INTO `migrations` VALUES (23, '2026_07_28_024857_add_ui_settings_to_system_configs', 3);
INSERT INTO `migrations` VALUES (24, '2026_07_28_034505_add_footer_links_to_system_configs', 4);
INSERT INTO `migrations` VALUES (25, '2026_07_28_040854_remove_show_footer_config', 5);

-- ----------------------------
-- Table structure for model_has_permissions
-- ----------------------------
DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE `model_has_permissions`  (
  `permission_id` bigint UNSIGNED NOT NULL COMMENT '权限ID',
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '模型类型',
  `model_id` bigint UNSIGNED NOT NULL COMMENT '模型ID',
  PRIMARY KEY (`permission_id`, `model_id`, `model_type`) USING BTREE,
  INDEX `idx_model`(`model_id` ASC, `model_type` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '用户权限关联表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of model_has_permissions
-- ----------------------------

-- ----------------------------
-- Table structure for model_has_roles
-- ----------------------------
DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE `model_has_roles`  (
  `role_id` bigint UNSIGNED NOT NULL COMMENT '角色ID',
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '模型类型',
  `model_id` bigint UNSIGNED NOT NULL COMMENT '模型ID',
  PRIMARY KEY (`role_id`, `model_id`, `model_type`) USING BTREE,
  INDEX `idx_model`(`model_id` ASC, `model_type` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '用户角色关联表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of model_has_roles
-- ----------------------------
INSERT INTO `model_has_roles` VALUES (1, 'App\\Models\\User', 1);
INSERT INTO `model_has_roles` VALUES (2, 'App\\Models\\User', 2);
INSERT INTO `model_has_roles` VALUES (3, 'App\\Models\\User', 3);
INSERT INTO `model_has_roles` VALUES (4, 'App\\Models\\User', 4);
INSERT INTO `model_has_roles` VALUES (5, 'App\\Models\\User', 5);
INSERT INTO `model_has_roles` VALUES (6, 'App\\Models\\User', 6);
INSERT INTO `model_has_roles` VALUES (7, 'App\\Models\\User', 7);
INSERT INTO `model_has_roles` VALUES (8, 'App\\Models\\User', 8);
INSERT INTO `model_has_roles` VALUES (9, 'App\\Models\\User', 9);

-- ----------------------------
-- Table structure for notifications
-- ----------------------------
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '目标用户ID，NULL表示全站广播',
  `merchant_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '目标商家ID',
  `type` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '类型：1系统通知，2订单状态变更，3补货提醒，4库存预警，5账户变动',
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标题',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '内容',
  `data` json NULL COMMENT '扩展数据',
  `is_read` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否已读：0未读，1已读',
  `read_at` timestamp NULL DEFAULT NULL COMMENT '已读时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `notifications_user_id_index`(`user_id` ASC) USING BTREE,
  INDEX `notifications_merchant_id_index`(`merchant_id` ASC) USING BTREE,
  INDEX `notifications_type_index`(`type` ASC) USING BTREE,
  INDEX `notifications_is_read_index`(`is_read` ASC) USING BTREE,
  INDEX `notifications_created_at_index`(`created_at` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '通知/消息表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of notifications
-- ----------------------------
INSERT INTO `notifications` VALUES (1, 1, NULL, 4, '库存预警', '商品「有机牛奶」库存低于安全线，当前库存 5 件，安全线 20 件', NULL, 1, '2026-07-28 04:23:06', '2026-07-28 04:22:46', '2026-07-28 04:23:06');
INSERT INTO `notifications` VALUES (2, 1, NULL, 2, '订单状态变更', '订单 #20260728001 已由配送司机张三确认取货，预计 30 分钟送达', NULL, 1, '2026-07-28 04:23:15', '2026-07-28 04:22:46', '2026-07-28 04:23:15');
INSERT INTO `notifications` VALUES (3, 1, NULL, 3, '补货提醒', '商家「鲜果超市」有 3 个 SKU 库存不足，请及时补货', NULL, 1, '2026-07-28 04:23:15', '2026-07-28 04:22:46', '2026-07-28 04:23:15');
INSERT INTO `notifications` VALUES (4, 1, NULL, 5, '账户变动', '商家「鲜果超市」充值 5,000.00 到账，当前余额 12,350.00', NULL, 1, '2026-07-28 02:22:46', '2026-07-28 04:22:46', '2026-07-28 04:22:46');
INSERT INTO `notifications` VALUES (5, 1, NULL, 1, '系统通知', '系统将于今晚 23:00-23:30 进行维护升级，届时服务将短暂中断', NULL, 1, '2026-07-28 01:22:46', '2026-07-28 04:22:46', '2026-07-28 04:22:46');
INSERT INTO `notifications` VALUES (6, 1, NULL, 2, '订单状态变更', '订单 #20260728005 客户已签收，签收存证已生成', NULL, 1, '2026-07-27 04:22:46', '2026-07-28 04:22:46', '2026-07-28 04:22:46');
INSERT INTO `notifications` VALUES (7, 1, NULL, 4, '库存预警', '商品「进口牛排」库存低于安全线，当前库存 2 件，安全线 15 件', NULL, 1, '2026-07-27 04:22:46', '2026-07-28 04:22:46', '2026-07-28 04:22:46');

-- ----------------------------
-- Table structure for operation_logs
-- ----------------------------
DROP TABLE IF EXISTS `operation_logs`;
CREATE TABLE `operation_logs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '操作人ID',
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '操作人用户名',
  `method` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '请求方法',
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '请求路径',
  `ip` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'IP地址',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '操作内容',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `operation_logs_user_id_index`(`user_id` ASC) USING BTREE,
  INDEX `operation_logs_created_at_index`(`created_at` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '操作日志表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of operation_logs
-- ----------------------------

-- ----------------------------
-- Table structure for order_items
-- ----------------------------
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `order_id` bigint UNSIGNED NOT NULL COMMENT '订单ID',
  `sku_id` bigint UNSIGNED NOT NULL COMMENT 'SKU ID',
  `product_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '商品名称快照',
  `sku_specs` json NULL COMMENT '规格快照',
  `quantity` bigint NOT NULL DEFAULT 0 COMMENT '下单数量',
  `price` bigint NOT NULL DEFAULT 0 COMMENT '下单单价',
  `actual_quantity` bigint NOT NULL DEFAULT 0 COMMENT '实际称重数量',
  `actual_price` bigint NOT NULL DEFAULT 0 COMMENT '实际称重单价',
  `subtotal` bigint NOT NULL DEFAULT 0 COMMENT '小计金额',
  `actual_subtotal` bigint NOT NULL DEFAULT 0 COMMENT '实际小计金额',
  `strategy_price` bigint NOT NULL DEFAULT 0 COMMENT '改价/促销单价',
  `strategy_amount` bigint NOT NULL DEFAULT 0 COMMENT '改价/促销金额',
  `price_strategy_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '价格策略ID',
  `price_strategy_item_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '价格策略明细ID',
  `discrepancy_amount` bigint NOT NULL DEFAULT 0 COMMENT '差异金额',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：1正常，2待审核，3已调整',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `order_items_order_id_index`(`order_id` ASC) USING BTREE,
  INDEX `order_items_sku_id_index`(`sku_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '订单明细表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of order_items
-- ----------------------------

-- ----------------------------
-- Table structure for order_return_items
-- ----------------------------
DROP TABLE IF EXISTS `order_return_items`;
CREATE TABLE `order_return_items`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `order_return_id` bigint UNSIGNED NOT NULL COMMENT '售后退货单ID',
  `order_item_id` bigint UNSIGNED NOT NULL COMMENT '订单明细ID',
  `sku_id` bigint UNSIGNED NOT NULL COMMENT 'SKU ID',
  `quantity` bigint NOT NULL DEFAULT 0 COMMENT '退货数量',
  `price` bigint NOT NULL DEFAULT 0 COMMENT '退货单价',
  `amount` bigint NOT NULL DEFAULT 0 COMMENT '退货金额',
  `refund_amount` bigint NOT NULL DEFAULT 0 COMMENT '实际退款金额',
  `reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '明细原因',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `order_return_items_order_return_id_index`(`order_return_id` ASC) USING BTREE,
  INDEX `order_return_items_order_item_id_index`(`order_item_id` ASC) USING BTREE,
  INDEX `order_return_items_sku_id_index`(`sku_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '售后退货明细表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of order_return_items
-- ----------------------------

-- ----------------------------
-- Table structure for order_returns
-- ----------------------------
DROP TABLE IF EXISTS `order_returns`;
CREATE TABLE `order_returns`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `return_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '退货单号',
  `order_id` bigint UNSIGNED NOT NULL COMMENT '关联订单ID',
  `merchant_id` bigint UNSIGNED NOT NULL COMMENT '商家ID',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：1待审核，2已审核，3已退货，4退款完成，9取消',
  `total_amount` bigint NOT NULL DEFAULT 0 COMMENT '退货总金额',
  `refund_amount` bigint NOT NULL DEFAULT 0 COMMENT '实际退款金额',
  `reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '退货原因',
  `operator_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '经办人ID',
  `audited_by` bigint UNSIGNED NULL DEFAULT NULL COMMENT '审核人ID',
  `audited_at` timestamp NULL DEFAULT NULL COMMENT '审核时间',
  `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `order_returns_return_no_unique`(`return_no` ASC) USING BTREE,
  INDEX `order_returns_order_id_index`(`order_id` ASC) USING BTREE,
  INDEX `order_returns_merchant_id_index`(`merchant_id` ASC) USING BTREE,
  INDEX `order_returns_status_index`(`status` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '售后退货单表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of order_returns
-- ----------------------------

-- ----------------------------
-- Table structure for orders
-- ----------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `order_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '订单号',
  `merchant_id` bigint UNSIGNED NOT NULL COMMENT '商家ID',
  `delivery_route_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '配送线路ID',
  `batch` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '配送批次：1上午，2下午',
  `delivery_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '配送地址',
  `contact_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '收货联系人',
  `contact_phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '收货电话',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：1待拣货，2拣货中，3配送中，4已签收，5已锁定，9已取消',
  `total_amount` bigint NOT NULL DEFAULT 0 COMMENT '原始订单金额',
  `adjusted_amount` bigint NOT NULL DEFAULT 0 COMMENT '调整后金额',
  `final_amount` bigint NOT NULL DEFAULT 0 COMMENT '最终结算金额',
  `payment_status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '支付状态：1未支付，2已支付，3账期',
  `settlement_type` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '结算方式：1现结，2账期，3预付款',
  `is_locked` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否锁定：0否，1是',
  `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `orders_order_no_unique`(`order_no` ASC) USING BTREE,
  INDEX `orders_merchant_id_index`(`merchant_id` ASC) USING BTREE,
  INDEX `orders_delivery_route_id_index`(`delivery_route_id` ASC) USING BTREE,
  INDEX `orders_status_index`(`status` ASC) USING BTREE,
  INDEX `orders_batch_index`(`batch` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '订单表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of orders
-- ----------------------------

-- ----------------------------
-- Table structure for password_reset_tokens
-- ----------------------------
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens`  (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '邮箱',
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '令牌',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`email`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '密码重置令牌' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of password_reset_tokens
-- ----------------------------

-- ----------------------------
-- Table structure for permissions
-- ----------------------------
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '权限标识',
  `guard_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'web' COMMENT '守卫名称',
  `display_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '权限显示名称',
  `type` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '类型：1菜单，2按钮，3接口',
  `parent_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '父级权限ID',
  `route` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '路由/接口标识',
  `sort` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '菜单图标',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `permissions_name_guard_name_unique`(`name` ASC, `guard_name` ASC) USING BTREE,
  INDEX `permissions_parent_id_index`(`parent_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '权限表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of permissions
-- ----------------------------
INSERT INTO `permissions` VALUES (1, 'dashboard', 'web', '仪表盘', 1, 0, 'dashboard', 0, 'layout-dashboard', '2026-07-27 09:23:33', '2026-07-27 09:23:33');
INSERT INTO `permissions` VALUES (2, 'product.menu', 'web', '商品管理', 1, 0, NULL, 1, 'package', '2026-07-27 09:23:33', '2026-07-27 09:23:33');
INSERT INTO `permissions` VALUES (3, 'product.index', 'web', '商品列表', 1, 0, 'products.index', 10, NULL, '2026-07-27 09:23:33', '2026-07-27 09:23:33');
INSERT INTO `permissions` VALUES (4, 'order.menu', 'web', '订单管理', 1, 0, NULL, 2, 'shopping-cart', '2026-07-27 09:23:33', '2026-07-27 09:23:33');
INSERT INTO `permissions` VALUES (5, 'purchase.menu', 'web', '采购管理', 1, 0, NULL, 3, 'truck', '2026-07-27 09:23:33', '2026-07-27 09:23:33');
INSERT INTO `permissions` VALUES (6, 'finance.menu', 'web', '财务管理', 1, 0, NULL, 4, 'banknote', '2026-07-27 09:23:33', '2026-07-27 09:23:33');
INSERT INTO `permissions` VALUES (7, 'inventory.menu', 'web', '库存管理', 1, 0, NULL, 5, 'warehouse', '2026-07-27 09:23:33', '2026-07-27 09:23:33');
INSERT INTO `permissions` VALUES (8, 'delivery.menu', 'web', '物流配送', 1, 0, NULL, 6, 'delivery-truck', '2026-07-27 09:23:33', '2026-07-27 09:23:33');
INSERT INTO `permissions` VALUES (9, 'organization.menu', 'web', '组织管理', 1, 0, NULL, 7, 'building', '2026-07-27 09:23:33', '2026-07-27 09:23:33');
INSERT INTO `permissions` VALUES (10, 'system.menu', 'web', '系统管理', 1, 0, NULL, 8, 'settings', '2026-07-27 09:23:33', '2026-07-27 09:23:33');

-- ----------------------------
-- Table structure for personal_access_tokens
-- ----------------------------
DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '模型类型',
  `tokenable_id` bigint UNSIGNED NOT NULL COMMENT '模型ID',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Token名称',
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Token值',
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '能力列表',
  `last_used_at` timestamp NULL DEFAULT NULL COMMENT '最后使用时间',
  `expires_at` timestamp NULL DEFAULT NULL COMMENT '过期时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `personal_access_tokens_token_unique`(`token` ASC) USING BTREE,
  INDEX `personal_access_tokens_tokenable_id_tokenable_type_index`(`tokenable_id` ASC, `tokenable_type` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Sanctum Token表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of personal_access_tokens
-- ----------------------------

-- ----------------------------
-- Table structure for picking_task_items
-- ----------------------------
DROP TABLE IF EXISTS `picking_task_items`;
CREATE TABLE `picking_task_items`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `picking_task_id` bigint UNSIGNED NOT NULL COMMENT '拣货任务ID',
  `order_id` bigint UNSIGNED NOT NULL COMMENT '订单ID',
  `order_item_id` bigint UNSIGNED NOT NULL COMMENT '订单明细ID',
  `sku_id` bigint UNSIGNED NOT NULL COMMENT 'SKU ID',
  `required_quantity` bigint NOT NULL DEFAULT 0 COMMENT '需求数量',
  `picked_quantity` bigint NOT NULL DEFAULT 0 COMMENT '实际拣货数量',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：1待拣货，2已拣货，3差异',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `picking_task_items_picking_task_id_index`(`picking_task_id` ASC) USING BTREE,
  INDEX `picking_task_items_order_id_index`(`order_id` ASC) USING BTREE,
  INDEX `picking_task_items_sku_id_index`(`sku_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '拣货任务明细表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of picking_task_items
-- ----------------------------

-- ----------------------------
-- Table structure for picking_tasks
-- ----------------------------
DROP TABLE IF EXISTS `picking_tasks`;
CREATE TABLE `picking_tasks`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `task_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '任务编号',
  `warehouse_id` bigint UNSIGNED NOT NULL COMMENT '仓库ID',
  `picker_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '拣货员ID',
  `batch` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '配送批次：1上午，2下午',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：1待分配，2拣货中，3已完成',
  `started_at` timestamp NULL DEFAULT NULL COMMENT '开始时间',
  `completed_at` timestamp NULL DEFAULT NULL COMMENT '完成时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `picking_tasks_task_no_unique`(`task_no` ASC) USING BTREE,
  INDEX `picking_tasks_warehouse_id_index`(`warehouse_id` ASC) USING BTREE,
  INDEX `picking_tasks_picker_id_index`(`picker_id` ASC) USING BTREE,
  INDEX `picking_tasks_status_index`(`status` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '拣货任务表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of picking_tasks
-- ----------------------------

-- ----------------------------
-- Table structure for price_apportionments
-- ----------------------------
DROP TABLE IF EXISTS `price_apportionments`;
CREATE TABLE `price_apportionments`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `target_type` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '单据类型：1订单，2采购单',
  `target_id` bigint UNSIGNED NOT NULL COMMENT '单据ID',
  `target_item_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '单据明细ID',
  `apportion_type` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '均摊类型：1整单改价，2促销差价，3费用，4运费',
  `amount` bigint NOT NULL DEFAULT 0 COMMENT '均摊金额',
  `apportion_mode` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '均摊方式：1自动均摊，2手动均摊',
  `manual_amount` bigint NOT NULL DEFAULT 0 COMMENT '手动均摊金额',
  `operator_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '操作人ID',
  `approval_status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '审核状态：1待审核，2已通过，3已拒绝（手动均摊时有效，自动均摊默认2）',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `price_apportionments_target_type_index`(`target_type` ASC) USING BTREE,
  INDEX `price_apportionments_target_id_index`(`target_id` ASC) USING BTREE,
  INDEX `price_apportionments_target_item_id_index`(`target_item_id` ASC) USING BTREE,
  INDEX `price_apportionments_apportion_type_index`(`apportion_type` ASC) USING BTREE,
  INDEX `price_apportionments_approval_status_index`(`approval_status` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '价格/费用均摊记录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of price_apportionments
-- ----------------------------

-- ----------------------------
-- Table structure for price_change_logs
-- ----------------------------
DROP TABLE IF EXISTS `price_change_logs`;
CREATE TABLE `price_change_logs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `source_type` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '来源：1促销，2临时改价，3手动改价',
  `source_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '来源策略ID',
  `target_type` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '作用单据：1订单，2采购单，3应收，4应付',
  `target_id` bigint UNSIGNED NOT NULL COMMENT '单据ID',
  `target_item_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '单据明细ID',
  `original_price` bigint NOT NULL DEFAULT 0 COMMENT '改价前单价',
  `new_price` bigint NOT NULL DEFAULT 0 COMMENT '改价后单价',
  `quantity` bigint NOT NULL DEFAULT 0 COMMENT '数量',
  `amount_diff` bigint NOT NULL DEFAULT 0 COMMENT '金额差异',
  `operator_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '操作人ID',
  `role_ids` json NULL COMMENT '操作人角色ID数组',
  `reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '改价原因',
  `before_data` json NULL COMMENT '改价前数据',
  `after_data` json NULL COMMENT '改价后数据',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `price_change_logs_source_type_index`(`source_type` ASC) USING BTREE,
  INDEX `price_change_logs_source_id_index`(`source_id` ASC) USING BTREE,
  INDEX `price_change_logs_target_type_index`(`target_type` ASC) USING BTREE,
  INDEX `price_change_logs_target_id_index`(`target_id` ASC) USING BTREE,
  INDEX `price_change_logs_target_item_id_index`(`target_item_id` ASC) USING BTREE,
  INDEX `price_change_logs_operator_id_index`(`operator_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '改价/促销记录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of price_change_logs
-- ----------------------------

-- ----------------------------
-- Table structure for price_strategies
-- ----------------------------
DROP TABLE IF EXISTS `price_strategies`;
CREATE TABLE `price_strategies`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '策略名称',
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '策略编码',
  `type` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '类型：1促销，2临时改价',
  `target_type` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '作用对象：1供应商，2商家，3全部',
  `scope` tinyint UNSIGNED NOT NULL DEFAULT 3 COMMENT '作用范围：1采购，2销售，3通用',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：0禁用，1启用',
  `approval_status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '审核状态：1待审核，2已通过，3已拒绝',
  `start_at` timestamp NULL DEFAULT NULL COMMENT '生效开始时间',
  `end_at` timestamp NULL DEFAULT NULL COMMENT '生效结束时间',
  `created_by` bigint UNSIGNED NULL DEFAULT NULL COMMENT '创建人ID',
  `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `price_strategies_code_unique`(`code` ASC) USING BTREE,
  INDEX `price_strategies_type_index`(`type` ASC) USING BTREE,
  INDEX `price_strategies_target_type_index`(`target_type` ASC) USING BTREE,
  INDEX `price_strategies_status_index`(`status` ASC) USING BTREE,
  INDEX `price_strategies_approval_status_index`(`approval_status` ASC) USING BTREE,
  INDEX `price_strategies_start_at_end_at_index`(`start_at` ASC, `end_at` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '价格策略主表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of price_strategies
-- ----------------------------

-- ----------------------------
-- Table structure for price_strategy_items
-- ----------------------------
DROP TABLE IF EXISTS `price_strategy_items`;
CREATE TABLE `price_strategy_items`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `price_strategy_id` bigint UNSIGNED NOT NULL COMMENT '价格策略ID',
  `target_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '作用对象ID：supplier_id/merchant_id，0表示全部',
  `category_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '商品分类ID',
  `product_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '商品ID',
  `sku_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT 'SKU ID',
  `price_type` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '价格类型：1固定价，2折扣率，3成本加权',
  `price_value` bigint NOT NULL DEFAULT 0 COMMENT '固定价格',
  `discount_rate` int NOT NULL DEFAULT 10000 COMMENT '折扣率%',
  `cost_weight_rate` int NOT NULL DEFAULT 10000 COMMENT '成本加权率%',
  `min_quantity` bigint NOT NULL DEFAULT 0 COMMENT '最小起量',
  `effective_start_at` timestamp NULL DEFAULT NULL COMMENT '明细生效开始时间',
  `effective_end_at` timestamp NULL DEFAULT NULL COMMENT '明细生效结束时间',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：0禁用，1启用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `price_strategy_items_price_strategy_id_index`(`price_strategy_id` ASC) USING BTREE,
  INDEX `price_strategy_items_target_id_index`(`target_id` ASC) USING BTREE,
  INDEX `price_strategy_items_category_id_index`(`category_id` ASC) USING BTREE,
  INDEX `price_strategy_items_product_id_index`(`product_id` ASC) USING BTREE,
  INDEX `price_strategy_items_sku_id_index`(`sku_id` ASC) USING BTREE,
  INDEX `price_strategy_items_effective_start_at_effective_end_at_index`(`effective_start_at` ASC, `effective_end_at` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '价格策略明细表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of price_strategy_items
-- ----------------------------

-- ----------------------------
-- Table structure for product_images
-- ----------------------------
DROP TABLE IF EXISTS `product_images`;
CREATE TABLE `product_images`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `product_id` bigint UNSIGNED NOT NULL COMMENT '商品ID',
  `image_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '图片地址',
  `sort` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `product_images_product_id_index`(`product_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '商品图片表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of product_images
-- ----------------------------

-- ----------------------------
-- Table structure for product_tags
-- ----------------------------
DROP TABLE IF EXISTS `product_tags`;
CREATE TABLE `product_tags`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `product_id` bigint UNSIGNED NOT NULL COMMENT '商品ID',
  `tag_id` bigint UNSIGNED NOT NULL COMMENT '标签ID',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `product_tags_product_id_tag_id_unique`(`product_id` ASC, `tag_id` ASC) USING BTREE,
  INDEX `product_tags_tag_id_index`(`tag_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '商品标签关联表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of product_tags
-- ----------------------------

-- ----------------------------
-- Table structure for products
-- ----------------------------
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `category_id` bigint UNSIGNED NOT NULL COMMENT '分类ID',
  `supplier_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '默认供应商ID',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '商品名称',
  `cover` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '封面图',
  `unit` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '单位：斤/箱/份等',
  `is_weight_priced` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否称重改价：0否，1是',
  `stock_warning_value` bigint NOT NULL DEFAULT 0 COMMENT '库存预警值',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：0下架，1上架',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '商品详情',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `products_category_id_index`(`category_id` ASC) USING BTREE,
  INDEX `products_supplier_id_index`(`supplier_id` ASC) USING BTREE,
  INDEX `products_status_index`(`status` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '商品表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of products
-- ----------------------------
INSERT INTO `products` VALUES (1, 1, NULL, '大白菜', NULL, '斤', 1, 0, 1, NULL, '2026-07-27 09:23:35', '2026-07-27 09:23:35', NULL);
INSERT INTO `products` VALUES (2, 1, NULL, '土豆', NULL, '斤', 1, 0, 1, NULL, '2026-07-27 09:23:35', '2026-07-27 09:23:35', NULL);
INSERT INTO `products` VALUES (3, 1, NULL, '西红柿', NULL, '斤', 1, 0, 1, NULL, '2026-07-27 09:23:35', '2026-07-27 09:23:35', NULL);
INSERT INTO `products` VALUES (4, 3, NULL, '五花肉', NULL, '斤', 1, 0, 1, NULL, '2026-07-27 09:23:35', '2026-07-27 09:23:35', NULL);
INSERT INTO `products` VALUES (5, 4, NULL, '鲜虾', NULL, '斤', 1, 0, 1, NULL, '2026-07-27 09:23:35', '2026-07-27 09:23:35', NULL);
INSERT INTO `products` VALUES (6, 5, NULL, '金龙鱼大豆油', NULL, '桶', 0, 0, 1, NULL, '2026-07-27 09:23:35', '2026-07-27 09:23:35', NULL);

-- ----------------------------
-- Table structure for promotions
-- ----------------------------
DROP TABLE IF EXISTS `promotions`;
CREATE TABLE `promotions`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `type` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '类型：1主推商品，2主推品类',
  `target_id` bigint UNSIGNED NOT NULL COMMENT '目标ID',
  `sort` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `start_at` timestamp NULL DEFAULT NULL COMMENT '开始时间',
  `end_at` timestamp NULL DEFAULT NULL COMMENT '结束时间',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `promotions_type_index`(`type` ASC) USING BTREE,
  INDEX `promotions_target_id_index`(`target_id` ASC) USING BTREE,
  INDEX `promotions_status_index`(`status` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '运营主推表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of promotions
-- ----------------------------

-- ----------------------------
-- Table structure for purchase_items
-- ----------------------------
DROP TABLE IF EXISTS `purchase_items`;
CREATE TABLE `purchase_items`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `sku_id` bigint UNSIGNED NOT NULL COMMENT 'SKU ID',
  `quantity` bigint NOT NULL DEFAULT 0 COMMENT '待采数量',
  `source_type` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '来源：1订单汇总，2手工添加',
  `source_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '来源业务ID',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：1待生成采购单，2已生成采购单',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `purchase_items_sku_id_index`(`sku_id` ASC) USING BTREE,
  INDEX `purchase_items_status_index`(`status` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '待采清单表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of purchase_items
-- ----------------------------

-- ----------------------------
-- Table structure for purchase_order_items
-- ----------------------------
DROP TABLE IF EXISTS `purchase_order_items`;
CREATE TABLE `purchase_order_items`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `purchase_order_id` bigint UNSIGNED NOT NULL COMMENT '采购单ID',
  `sku_id` bigint UNSIGNED NOT NULL COMMENT 'SKU ID',
  `quantity` bigint NOT NULL DEFAULT 0 COMMENT '采购数量',
  `price` bigint NOT NULL DEFAULT 0 COMMENT '采购单价',
  `actual_quantity` bigint NOT NULL DEFAULT 0 COMMENT '实际入库数量',
  `actual_price` bigint NOT NULL DEFAULT 0 COMMENT '实际入库单价',
  `amount` bigint NOT NULL DEFAULT 0 COMMENT '金额',
  `actual_amount` bigint NOT NULL DEFAULT 0 COMMENT '实际金额',
  `strategy_price` bigint NOT NULL DEFAULT 0 COMMENT '改价/促销单价',
  `strategy_amount` bigint NOT NULL DEFAULT 0 COMMENT '改价/促销金额',
  `price_strategy_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '价格策略ID',
  `price_strategy_item_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '价格策略明细ID',
  `discrepancy_reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '入库差异原因',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `purchase_order_items_purchase_order_id_index`(`purchase_order_id` ASC) USING BTREE,
  INDEX `purchase_order_items_sku_id_index`(`sku_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '采购单明细表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of purchase_order_items
-- ----------------------------

-- ----------------------------
-- Table structure for purchase_orders
-- ----------------------------
DROP TABLE IF EXISTS `purchase_orders`;
CREATE TABLE `purchase_orders`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `order_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '采购单号',
  `supplier_id` bigint UNSIGNED NOT NULL COMMENT '供应商ID',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：1待接单，2备货中，3已发货，4已入库，5完成，9取消',
  `total_amount` bigint NOT NULL DEFAULT 0 COMMENT '总金额',
  `actual_amount` bigint NOT NULL DEFAULT 0 COMMENT '实际入库金额',
  `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `purchase_orders_order_no_unique`(`order_no` ASC) USING BTREE,
  INDEX `purchase_orders_supplier_id_index`(`supplier_id` ASC) USING BTREE,
  INDEX `purchase_orders_status_index`(`status` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '采购单表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of purchase_orders
-- ----------------------------

-- ----------------------------
-- Table structure for purchase_return_items
-- ----------------------------
DROP TABLE IF EXISTS `purchase_return_items`;
CREATE TABLE `purchase_return_items`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `purchase_return_id` bigint UNSIGNED NOT NULL COMMENT '采购退货单ID',
  `purchase_order_item_id` bigint UNSIGNED NOT NULL COMMENT '采购单明细ID',
  `sku_id` bigint UNSIGNED NOT NULL COMMENT 'SKU ID',
  `quantity` bigint NOT NULL DEFAULT 0 COMMENT '退货数量',
  `price` bigint NOT NULL DEFAULT 0 COMMENT '退货单价',
  `amount` bigint NOT NULL DEFAULT 0 COMMENT '退货金额',
  `actual_quantity` bigint NOT NULL DEFAULT 0 COMMENT '实际出库数量',
  `actual_price` bigint NOT NULL DEFAULT 0 COMMENT '实际出库单价',
  `actual_amount` bigint NOT NULL DEFAULT 0 COMMENT '实际出库金额',
  `reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '明细原因',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `purchase_return_items_purchase_return_id_index`(`purchase_return_id` ASC) USING BTREE,
  INDEX `purchase_return_items_purchase_order_item_id_index`(`purchase_order_item_id` ASC) USING BTREE,
  INDEX `purchase_return_items_sku_id_index`(`sku_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '采购退货明细表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of purchase_return_items
-- ----------------------------

-- ----------------------------
-- Table structure for purchase_returns
-- ----------------------------
DROP TABLE IF EXISTS `purchase_returns`;
CREATE TABLE `purchase_returns`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `return_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '退货单号',
  `purchase_order_id` bigint UNSIGNED NOT NULL COMMENT '关联采购单ID',
  `supplier_id` bigint UNSIGNED NOT NULL COMMENT '供应商ID',
  `warehouse_id` bigint UNSIGNED NOT NULL COMMENT '出库仓库ID',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：1待审核，2已审核，3已出库，4完成，9取消',
  `total_amount` bigint NOT NULL DEFAULT 0 COMMENT '退货总金额',
  `actual_amount` bigint NOT NULL DEFAULT 0 COMMENT '实际出库金额',
  `reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '退货原因',
  `operator_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '经办人ID',
  `audited_by` bigint UNSIGNED NULL DEFAULT NULL COMMENT '审核人ID',
  `audited_at` timestamp NULL DEFAULT NULL COMMENT '审核时间',
  `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `purchase_returns_return_no_unique`(`return_no` ASC) USING BTREE,
  INDEX `purchase_returns_purchase_order_id_index`(`purchase_order_id` ASC) USING BTREE,
  INDEX `purchase_returns_supplier_id_index`(`supplier_id` ASC) USING BTREE,
  INDEX `purchase_returns_status_index`(`status` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '采购退货单表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of purchase_returns
-- ----------------------------

-- ----------------------------
-- Table structure for receivable_payments
-- ----------------------------
DROP TABLE IF EXISTS `receivable_payments`;
CREATE TABLE `receivable_payments`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `receivable_id` bigint UNSIGNED NOT NULL COMMENT '应收账款ID',
  `amount` bigint NOT NULL DEFAULT 0 COMMENT '本次收款金额',
  `payment_method` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '收款方式：1余额扣款，2微信支付，3线下转账，4后台手工',
  `transaction_no` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '第三方交易号',
  `operator_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '操作人ID',
  `approval_status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '审核状态：1待审核，2已通过，3已拒绝',
  `evidence_urls` json NULL COMMENT '收款凭证图片数组',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `receivable_payments_receivable_id_index`(`receivable_id` ASC) USING BTREE,
  INDEX `receivable_payments_payment_method_index`(`payment_method` ASC) USING BTREE,
  INDEX `receivable_payments_created_at_index`(`created_at` ASC) USING BTREE,
  INDEX `receivable_payments_approval_status_index`(`approval_status` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '收款记录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of receivable_payments
-- ----------------------------

-- ----------------------------
-- Table structure for receivables
-- ----------------------------
DROP TABLE IF EXISTS `receivables`;
CREATE TABLE `receivables`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `receivable_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '应收单号',
  `order_id` bigint UNSIGNED NOT NULL COMMENT '订单ID',
  `merchant_id` bigint UNSIGNED NOT NULL COMMENT '商家ID',
  `original_amount` bigint NOT NULL DEFAULT 0 COMMENT '原始金额',
  `adjusted_amount` bigint NOT NULL DEFAULT 0 COMMENT '调整后金额',
  `discrepancy_amount` bigint NOT NULL DEFAULT 0 COMMENT '差异金额',
  `return_amount` bigint NOT NULL DEFAULT 0 COMMENT '售后退货扣减金额',
  `strategy_discount_amount` bigint NOT NULL DEFAULT 0 COMMENT '改价/促销折扣金额',
  `received_amount` bigint NOT NULL DEFAULT 0 COMMENT '已收金额（多次收款累计）',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：1未结算，2部分收款，3已结清，4争议中，5已办结',
  `settlement_type` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '结算方式：1现结，2账期，3预付款',
  `due_date` date NULL DEFAULT NULL COMMENT '到期日',
  `settled_at` timestamp NULL DEFAULT NULL COMMENT '结算时间',
  `closed_at` timestamp NULL DEFAULT NULL COMMENT '办结时间',
  `closed_by` bigint UNSIGNED NULL DEFAULT NULL COMMENT '办结操作人ID',
  `approval_status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '审核状态：1待审核，2已通过，3已拒绝（改价折扣调整时有效）',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `receivables_receivable_no_unique`(`receivable_no` ASC) USING BTREE,
  INDEX `receivables_order_id_index`(`order_id` ASC) USING BTREE,
  INDEX `receivables_merchant_id_index`(`merchant_id` ASC) USING BTREE,
  INDEX `receivables_status_index`(`status` ASC) USING BTREE,
  INDEX `receivables_approval_status_index`(`approval_status` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '应收账款表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of receivables
-- ----------------------------

-- ----------------------------
-- Table structure for recharges
-- ----------------------------
DROP TABLE IF EXISTS `recharges`;
CREATE TABLE `recharges`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `merchant_id` bigint UNSIGNED NOT NULL COMMENT '商家ID',
  `amount` bigint NOT NULL DEFAULT 0 COMMENT '充值金额',
  `payment_method` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '支付方式：1微信支付，2线下转账，3后台手工',
  `transaction_no` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '第三方交易号',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：1待确认，2成功，3失败',
  `approval_status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '审核状态：1待审核，2已通过，3已拒绝',
  `operator_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '操作人ID',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `recharges_merchant_id_index`(`merchant_id` ASC) USING BTREE,
  INDEX `recharges_transaction_no_index`(`transaction_no` ASC) USING BTREE,
  INDEX `recharges_status_index`(`status` ASC) USING BTREE,
  INDEX `recharges_approval_status_index`(`approval_status` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '充值记录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of recharges
-- ----------------------------

-- ----------------------------
-- Table structure for repurchase_template_items
-- ----------------------------
DROP TABLE IF EXISTS `repurchase_template_items`;
CREATE TABLE `repurchase_template_items`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `template_id` bigint UNSIGNED NOT NULL COMMENT '模板ID',
  `sku_id` bigint UNSIGNED NOT NULL COMMENT 'SKU ID',
  `quantity` bigint NOT NULL DEFAULT 0 COMMENT '数量',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `repurchase_template_items_template_id_index`(`template_id` ASC) USING BTREE,
  INDEX `repurchase_template_items_sku_id_index`(`sku_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '复购模板明细表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of repurchase_template_items
-- ----------------------------

-- ----------------------------
-- Table structure for repurchase_templates
-- ----------------------------
DROP TABLE IF EXISTS `repurchase_templates`;
CREATE TABLE `repurchase_templates`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `merchant_id` bigint UNSIGNED NOT NULL COMMENT '商家ID',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '模板名称',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `repurchase_templates_merchant_id_index`(`merchant_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '复购模板表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of repurchase_templates
-- ----------------------------

-- ----------------------------
-- Table structure for restock_reminders
-- ----------------------------
DROP TABLE IF EXISTS `restock_reminders`;
CREATE TABLE `restock_reminders`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `merchant_id` bigint UNSIGNED NOT NULL COMMENT '商家ID',
  `sku_id` bigint UNSIGNED NOT NULL COMMENT 'SKU ID',
  `threshold_quantity` bigint NOT NULL DEFAULT 0 COMMENT '触发提醒的库存阈值',
  `remind_cycle` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '提醒周期：1每日，2每周，3仅一次',
  `last_reminded_at` timestamp NULL DEFAULT NULL COMMENT '上次提醒时间',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：0禁用，1启用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `restock_reminders_merchant_id_sku_id_unique`(`merchant_id` ASC, `sku_id` ASC) USING BTREE,
  INDEX `restock_reminders_status_index`(`status` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '智能补货提醒规则表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of restock_reminders
-- ----------------------------

-- ----------------------------
-- Table structure for role_has_permissions
-- ----------------------------
DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE `role_has_permissions`  (
  `permission_id` bigint UNSIGNED NOT NULL COMMENT '权限ID',
  `role_id` bigint UNSIGNED NOT NULL COMMENT '角色ID',
  PRIMARY KEY (`permission_id`, `role_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '角色权限关联表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of role_has_permissions
-- ----------------------------
INSERT INTO `role_has_permissions` VALUES (1, 1);
INSERT INTO `role_has_permissions` VALUES (2, 1);
INSERT INTO `role_has_permissions` VALUES (3, 1);
INSERT INTO `role_has_permissions` VALUES (4, 1);
INSERT INTO `role_has_permissions` VALUES (5, 1);
INSERT INTO `role_has_permissions` VALUES (6, 1);
INSERT INTO `role_has_permissions` VALUES (7, 1);
INSERT INTO `role_has_permissions` VALUES (8, 1);
INSERT INTO `role_has_permissions` VALUES (9, 1);
INSERT INTO `role_has_permissions` VALUES (10, 1);

-- ----------------------------
-- Table structure for roles
-- ----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '角色标识',
  `guard_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'web' COMMENT '守卫名称',
  `display_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '角色显示名称',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '描述',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `roles_name_guard_name_unique`(`name` ASC, `guard_name` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '角色表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of roles
-- ----------------------------
INSERT INTO `roles` VALUES (1, 'super_admin', 'web', '超级管理员', '全部功能、系统配置、账号管理', '2026-07-27 09:23:29', '2026-07-27 09:23:29');
INSERT INTO `roles` VALUES (2, 'operator', 'web', '运营管理员', '商品、订单、商家、供应商管理', '2026-07-27 09:23:29', '2026-07-27 09:23:29');
INSERT INTO `roles` VALUES (3, 'operator_manager', 'web', '运营经理', '运营审核、商品/订单/价格策略审核确认', '2026-07-27 09:23:29', '2026-07-27 09:23:29');
INSERT INTO `roles` VALUES (4, 'finance', 'web', '财务人员', '应收、结算、发票、审计', '2026-07-27 09:23:29', '2026-07-27 09:23:29');
INSERT INTO `roles` VALUES (5, 'cashier', 'web', '出纳', '付款录入、收款录入、资金操作执行', '2026-07-27 09:23:29', '2026-07-27 09:23:29');
INSERT INTO `roles` VALUES (6, 'finance_manager', 'web', '财务经理', '财务审核、付款/收款/结算单据复核确认', '2026-07-27 09:23:29', '2026-07-27 09:23:29');
INSERT INTO `roles` VALUES (7, 'picker', 'web', '拣货员', '拣货任务、称重改价', '2026-07-27 09:23:29', '2026-07-27 09:23:29');
INSERT INTO `roles` VALUES (8, 'driver', 'web', '配送司机', '配送任务、轨迹、签收', '2026-07-27 09:23:29', '2026-07-27 09:23:29');
INSERT INTO `roles` VALUES (9, 'merchant', 'web', '商家', '小程序商家端', '2026-07-27 09:23:29', '2026-07-27 09:23:29');

-- ----------------------------
-- Table structure for settlement_payments
-- ----------------------------
DROP TABLE IF EXISTS `settlement_payments`;
CREATE TABLE `settlement_payments`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `settlement_id` bigint UNSIGNED NOT NULL COMMENT '供应商结算单ID',
  `amount` bigint NOT NULL DEFAULT 0 COMMENT '本次付款金额',
  `payment_method` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '付款方式：1银行转账，2线下现金，3后台手工',
  `transaction_no` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '第三方交易号',
  `operator_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '操作人ID',
  `approval_status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '审核状态：1待审核，2已通过，3已拒绝',
  `evidence_urls` json NULL COMMENT '付款凭证图片数组',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `settlement_payments_settlement_id_index`(`settlement_id` ASC) USING BTREE,
  INDEX `settlement_payments_payment_method_index`(`payment_method` ASC) USING BTREE,
  INDEX `settlement_payments_created_at_index`(`created_at` ASC) USING BTREE,
  INDEX `settlement_payments_approval_status_index`(`approval_status` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '付款记录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of settlement_payments
-- ----------------------------

-- ----------------------------
-- Table structure for signatures
-- ----------------------------
DROP TABLE IF EXISTS `signatures`;
CREATE TABLE `signatures`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `order_id` bigint UNSIGNED NOT NULL COMMENT '订单ID',
  `delivery_task_id` bigint UNSIGNED NOT NULL COMMENT '配送任务ID',
  `type` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '类型：1拍照签收，2电子签名，3质检照片',
  `image_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '图片/签名文件地址',
  `signer_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '签收人',
  `signed_at` timestamp NULL DEFAULT NULL COMMENT '签收时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `signatures_order_id_index`(`order_id` ASC) USING BTREE,
  INDEX `signatures_delivery_task_id_index`(`delivery_task_id` ASC) USING BTREE,
  INDEX `signatures_type_index`(`type` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '签收存证表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of signatures
-- ----------------------------

-- ----------------------------
-- Table structure for sku_barcodes
-- ----------------------------
DROP TABLE IF EXISTS `sku_barcodes`;
CREATE TABLE `sku_barcodes`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `sku_id` bigint UNSIGNED NOT NULL COMMENT 'SKU ID',
  `supplier_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '供应商ID，供应商条码时必填',
  `barcode_type` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '条码类型：1厂家条码，2供应商条码，3内部条码，4备用条码',
  `barcode_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '条码值',
  `is_default` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否默认条码：0否，1是',
  `is_enabled` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否启用：0禁用，1启用',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_sku_supplier_type_code`(`sku_id` ASC, `supplier_id` ASC, `barcode_type` ASC, `barcode_code` ASC) USING BTREE,
  INDEX `sku_barcodes_sku_id_index`(`sku_id` ASC) USING BTREE,
  INDEX `sku_barcodes_supplier_id_index`(`supplier_id` ASC) USING BTREE,
  INDEX `sku_barcodes_barcode_type_index`(`barcode_type` ASC) USING BTREE,
  INDEX `sku_barcodes_barcode_code_index`(`barcode_code` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'SKU条码表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sku_barcodes
-- ----------------------------

-- ----------------------------
-- Table structure for sku_suppliers
-- ----------------------------
DROP TABLE IF EXISTS `sku_suppliers`;
CREATE TABLE `sku_suppliers`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `sku_id` bigint UNSIGNED NOT NULL COMMENT 'SKU ID',
  `supplier_id` bigint UNSIGNED NOT NULL COMMENT '供应商ID',
  `is_default` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否默认供应商：0否，1是',
  `purchase_price` bigint NOT NULL DEFAULT 0 COMMENT '该供应商采购参考价',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否启用：0禁用，1启用',
  `sort` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_sku_supplier`(`sku_id` ASC, `supplier_id` ASC) USING BTREE,
  INDEX `sku_suppliers_supplier_id_index`(`supplier_id` ASC) USING BTREE,
  INDEX `sku_suppliers_status_index`(`status` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'SKU供应商关联表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sku_suppliers
-- ----------------------------

-- ----------------------------
-- Table structure for skus
-- ----------------------------
DROP TABLE IF EXISTS `skus`;
CREATE TABLE `skus`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `product_id` bigint UNSIGNED NOT NULL COMMENT '商品ID',
  `sku_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'SKU编码',
  `specs` json NULL COMMENT '规格属性',
  `purchase_price` bigint NOT NULL DEFAULT 0 COMMENT '采购参考价',
  `wholesale_price` bigint NOT NULL DEFAULT 0 COMMENT '批发销售价',
  `cost_price` bigint NOT NULL DEFAULT 0 COMMENT '财务成本价',
  `stock` bigint NOT NULL DEFAULT 0 COMMENT '当前库存冗余字段',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：0禁用，1启用',
  `approval_status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '审核状态：1待审核，2已通过，3已拒绝',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `skus_sku_code_unique`(`sku_code` ASC) USING BTREE,
  INDEX `skus_product_id_index`(`product_id` ASC) USING BTREE,
  INDEX `skus_status_index`(`status` ASC) USING BTREE,
  INDEX `skus_approval_status_index`(`approval_status` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'SKU规格表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of skus
-- ----------------------------
INSERT INTO `skus` VALUES (1, 1, 'SKU-0001', '[{\"label\": \"规格\", \"value\": \"斤\"}]', 8000, 9200, 0, 0, 1, 2, '2026-07-27 09:23:35', '2026-07-27 09:23:35', NULL);
INSERT INTO `skus` VALUES (2, 2, 'SKU-0002', '[{\"label\": \"规格\", \"value\": \"斤\"}]', 12000, 13799, 0, 0, 1, 2, '2026-07-27 09:23:35', '2026-07-27 09:23:35', NULL);
INSERT INTO `skus` VALUES (3, 3, 'SKU-0003', '[{\"label\": \"规格\", \"value\": \"斤\"}]', 25000, 28749, 0, 0, 1, 2, '2026-07-27 09:23:35', '2026-07-27 09:23:35', NULL);
INSERT INTO `skus` VALUES (4, 4, 'SKU-0004', '[{\"label\": \"规格\", \"value\": \"斤\"}]', 130000, 149500, 0, 0, 1, 2, '2026-07-27 09:23:35', '2026-07-27 09:23:35', NULL);
INSERT INTO `skus` VALUES (5, 5, 'SKU-0005', '[{\"label\": \"规格\", \"value\": \"斤\"}]', 350000, 402499, 0, 0, 1, 2, '2026-07-27 09:23:35', '2026-07-27 09:23:35', NULL);
INSERT INTO `skus` VALUES (6, 6, 'SKU-0006', '[{\"label\": \"规格\", \"value\": \"桶\"}]', 450000, 517499, 0, 0, 1, 2, '2026-07-27 09:23:35', '2026-07-27 09:23:35', NULL);

-- ----------------------------
-- Table structure for supplier_settlement_items
-- ----------------------------
DROP TABLE IF EXISTS `supplier_settlement_items`;
CREATE TABLE `supplier_settlement_items`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `supplier_settlement_id` bigint UNSIGNED NOT NULL COMMENT '结算单ID',
  `purchase_order_id` bigint UNSIGNED NOT NULL COMMENT '采购单ID',
  `amount` bigint NOT NULL DEFAULT 0 COMMENT '金额',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `supplier_settlement_items_supplier_settlement_id_index`(`supplier_settlement_id` ASC) USING BTREE,
  INDEX `supplier_settlement_items_purchase_order_id_index`(`purchase_order_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '结算单明细表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of supplier_settlement_items
-- ----------------------------

-- ----------------------------
-- Table structure for supplier_settlements
-- ----------------------------
DROP TABLE IF EXISTS `supplier_settlements`;
CREATE TABLE `supplier_settlements`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `settlement_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '结算单号',
  `supplier_id` bigint UNSIGNED NOT NULL COMMENT '供应商ID',
  `start_date` date NOT NULL COMMENT '结算周期开始',
  `end_date` date NOT NULL COMMENT '结算周期结束',
  `total_amount` bigint NOT NULL DEFAULT 0 COMMENT '汇总金额',
  `service_fee` bigint NOT NULL DEFAULT 0 COMMENT '服务费',
  `payable_amount` bigint NOT NULL DEFAULT 0 COMMENT '应付金额',
  `return_amount` bigint NOT NULL DEFAULT 0 COMMENT '采购退货扣减金额',
  `paid_amount` bigint NOT NULL DEFAULT 0 COMMENT '已付金额（多次付款累计）',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：1待结算，2部分付款，3已结清，4已办结',
  `settled_at` timestamp NULL DEFAULT NULL COMMENT '结算时间',
  `closed_at` timestamp NULL DEFAULT NULL COMMENT '办结时间',
  `closed_by` bigint UNSIGNED NULL DEFAULT NULL COMMENT '办结操作人ID',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `supplier_settlements_settlement_no_unique`(`settlement_no` ASC) USING BTREE,
  INDEX `supplier_settlements_supplier_id_index`(`supplier_id` ASC) USING BTREE,
  INDEX `supplier_settlements_status_index`(`status` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '供应商结算单表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of supplier_settlements
-- ----------------------------

-- ----------------------------
-- Table structure for suppliers
-- ----------------------------
DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE `suppliers`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '供应商名称',
  `contact_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '联系人',
  `contact_phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '联系电话',
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '地址',
  `bank_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '开户行',
  `bank_account` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '银行账号',
  `settlement_cycle` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '结算周期：1周结，2月结，3不定期',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：0禁用，1启用',
  `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `suppliers_status_index`(`status` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '供应商表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of suppliers
-- ----------------------------
INSERT INTO `suppliers` VALUES (1, '鲜源农业有限公司', '陈供应', '13900000001', '安徽省宿州市埇桥区农批市场A1', NULL, NULL, 1, 1, NULL, '2026-07-27 09:23:35', '2026-07-27 09:23:35', NULL);
INSERT INTO `suppliers` VALUES (2, '绿野蔬菜种植基地', '李蔬菜', '13900000002', '安徽省宿州市埇桥区农批市场B3', NULL, NULL, 1, 1, NULL, '2026-07-27 09:23:35', '2026-07-27 09:23:35', NULL);
INSERT INTO `suppliers` VALUES (3, '丰润肉业有限公司', '王肉业', '13900000003', '安徽省宿州市埇桥区肉联厂C2', NULL, NULL, 1, 1, NULL, '2026-07-27 09:23:35', '2026-07-27 09:23:35', NULL);
INSERT INTO `suppliers` VALUES (4, '海滨水产批发部', '赵水产', '13900000004', '安徽省宿州市埇桥区水产市场D5', NULL, NULL, 1, 1, NULL, '2026-07-27 09:23:35', '2026-07-27 09:23:35', NULL);
INSERT INTO `suppliers` VALUES (5, '恒达粮油贸易公司', '钱粮油', '13900000005', '安徽省宿州市埇桥区粮批市场E1', NULL, NULL, 1, 1, NULL, '2026-07-27 09:23:35', '2026-07-27 09:23:35', NULL);

-- ----------------------------
-- Table structure for system_configs
-- ----------------------------
DROP TABLE IF EXISTS `system_configs`;
CREATE TABLE `system_configs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `config_key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '配置键',
  `config_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '配置值',
  `default_value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '默认值（重置用）',
  `config_type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string' COMMENT '值类型：boolean/integer/decimal/string/enum/json',
  `config_group` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'basic' COMMENT '分组：basic/order/delivery/finance/inventory/audit',
  `label` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '中文显示名',
  `hint` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '输入提示',
  `options` json NULL COMMENT '枚举选项 [{\"label\":\"选项名\",\"value\":\"值\"},...]',
  `validation_rules` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '校验规则：min:0|max:999|required|integer',
  `sort_order` int NOT NULL DEFAULT 0 COMMENT '组内排序',
  `is_public` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否前端可读（无需登录）',
  `is_readonly` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否只读（代码写入，不允许管理后台改）',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '说明',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `system_configs_config_key_unique`(`config_key` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 25 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '系统配置表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of system_configs
-- ----------------------------
INSERT INTO `system_configs` VALUES (1, 'site_name', '洛阳配送服务平台', '本地速送服务平台', 'string', 'basic', '站点名称', NULL, NULL, 'required|max:50', 1, 0, 0, '站点名称', '2026-07-27 09:23:32', '2026-07-28 02:09:40');
INSERT INTO `system_configs` VALUES (2, 'contact_phone', '15690631151', '15690631151', 'string', 'basic', '客服电话', NULL, NULL, 'required|max:20', 2, 0, 0, '客服电话', '2026-07-27 09:23:32', '2026-07-27 09:23:32');
INSERT INTO `system_configs` VALUES (3, 'default_delivery_batch', '1', '1', 'enum', 'delivery', '默认配送批次', NULL, '[{\"label\": \"上午\", \"value\": \"1\"}, {\"label\": \"下午\", \"value\": \"2\"}]', NULL, 10, 0, 0, '默认配送批次：1上午，2下午', '2026-07-27 09:23:32', '2026-07-27 09:23:32');
INSERT INTO `system_configs` VALUES (4, 'weighing_diff_threshold', '20', '20', 'integer', 'inventory', '称重差异阈值（%）', '称重差异超过此百分比需人工确认', NULL, 'required|integer|min:1|max:100', 20, 0, 0, '称重差异阈值（百分比）', '2026-07-27 09:23:32', '2026-07-27 09:23:32');
INSERT INTO `system_configs` VALUES (5, 'audit_retention_days', '90', '90', 'integer', 'audit', '审计日志保留天数', '0=永久保留，1-180天，到期每日定时清理', NULL, 'required|integer|min:0|max:180', 50, 0, 0, '审计/日志保留天数：0=永久保留，1-180天，到期每日定时清理', '2026-07-27 09:23:32', '2026-07-27 09:23:32');
INSERT INTO `system_configs` VALUES (6, 'loss_approval_threshold', '200', '200', 'integer', 'audit', '损耗审批阈值（元）', '单张损耗单金额超过此值需运营经理审核，未超阈值直接执行', NULL, 'required|integer|min:0', 51, 0, 0, '损耗审批阈值（元）：单张损耗单金额超过此值需运营经理审核，未超阈值直接执行', '2026-07-27 09:23:32', '2026-07-27 09:23:32');
INSERT INTO `system_configs` VALUES (7, 'order_auto_confirm_hours', '24', '24', 'integer', 'order', '自动确认收货时长（小时）', '超过此时长未签收将自动确认', NULL, 'required|integer|min:1|max:168', 3, 0, 0, '订单配送完成后的自动签收等待时长', '2026-07-28 00:50:17', '2026-07-28 00:50:17');
INSERT INTO `system_configs` VALUES (8, 'min_delivery_amount', '0', '0', 'integer', 'order', '最低起送金额（元）', '0表示无限制', NULL, 'required|integer|min:0', 4, 1, 0, '商家下单金额门槛', '2026-07-28 00:50:17', '2026-07-28 00:50:17');
INSERT INTO `system_configs` VALUES (9, 'allow_merchant_self_order', '1', '1', 'boolean', 'order', '允许商家自助下单', '关闭后商家只能由运营代下单', NULL, 'required|boolean', 5, 1, 0, '商家端小程序是否允许自主下单', '2026-07-28 00:50:17', '2026-07-28 00:50:17');
INSERT INTO `system_configs` VALUES (10, 'delivery_timeout_minutes', '30', '30', 'integer', 'delivery', '配送超时标记时长（分钟）', '超过此时长未完成配送将标记为异常', NULL, 'required|integer|min:10|max:180', 11, 0, 0, '配送任务超时自动标记异常', '2026-07-28 00:50:17', '2026-07-28 00:50:17');
INSERT INTO `system_configs` VALUES (11, 'allow_driver_multi_task', '1', '1', 'boolean', 'delivery', '允许司机同时接多单', '关闭后司机同时只能执行一个配送任务', NULL, 'required|boolean', 12, 0, 0, '司机并发配送开关', '2026-07-28 00:50:17', '2026-07-28 00:50:17');
INSERT INTO `system_configs` VALUES (12, 'max_daily_recharge_amount', '50000', '50000', 'integer', 'finance', '单日最大充值金额（元）', '单商家每日充值累计上限', NULL, 'required|integer|min:1000', 20, 1, 0, '商家充值风控限额', '2026-07-28 00:50:17', '2026-07-28 00:50:17');
INSERT INTO `system_configs` VALUES (13, 'credit_limit_default', '5000', '5000', 'integer', 'finance', '新商家默认信用额度（元）', '新注册商家自动分配的信用额度', NULL, 'required|integer|min:0', 21, 0, 0, '新商家初始信用额度', '2026-07-28 00:50:17', '2026-07-28 00:50:17');
INSERT INTO `system_configs` VALUES (14, 'enable_weighing_auto_debit', '0', '0', 'boolean', 'finance', '称重差异自动扣款', '开启后称重差异在阈值内自动扣款，无需人工确认', NULL, 'required|boolean', 22, 0, 0, '称重差异处理方式：自动扣款或人工确认', '2026-07-28 00:50:17', '2026-07-28 00:50:17');
INSERT INTO `system_configs` VALUES (15, 'inventory_warning_enabled', '1', '1', 'boolean', 'inventory', '启用库存预警', '开启后低于预警值触发通知', NULL, 'required|boolean', 30, 0, 0, '库存预警检测开关', '2026-07-28 00:50:17', '2026-07-28 00:50:17');
INSERT INTO `system_configs` VALUES (16, 'inventory_warning_interval_minutes', '5', '5', 'integer', 'inventory', '库存预警检测频率（分钟）', '定时任务检测间隔', NULL, 'required|integer|min:1|max:60', 31, 0, 0, '库存预警定时检测周期', '2026-07-28 00:50:17', '2026-07-28 00:50:17');
INSERT INTO `system_configs` VALUES (17, 'max_upload_size_mb', '21', '20', 'integer', 'basic', '文件上传大小限制（MB）', '单文件上传最大体积', NULL, 'required|integer|min:1|max:100', 6, 0, 0, '管理后台和商家端文件上传限制', '2026-07-28 00:50:17', '2026-07-28 02:16:31');
INSERT INTO `system_configs` VALUES (18, 'ui_close_on_outside', '1', '1', 'boolean', 'ui', '点击旁边关闭通知', '开启后，点击通知面板外的区域将自动关闭通知菜单', NULL, NULL, 1, 1, 0, '控制点击通知 Drawer 外部区域时是否自动关闭面板。关闭此选项后，只能通过点击关闭按钮或按 ESC 键关闭通知面板。', '2026-07-28 02:49:23', '2026-07-28 03:32:41');
INSERT INTO `system_configs` VALUES (20, 'site_icp_number', '', '', 'string', 'basic', 'ICP 备案号', '网站 ICP 备案号，留空不显示，如：京ICP备2026XXXXX号', NULL, 'max:50', 7, 1, 0, '显示在页面底部的 ICP 备案号。留空则不显示备案信息。', '2026-07-28 03:09:01', '2026-07-28 03:09:01');
INSERT INTO `system_configs` VALUES (21, 'site_tech_stack_url', 'https://laravel.com', 'https://laravel.com', 'string', 'basic', '技术栈链接', '底部版权栏\"技术栈\"文字的跳转链接', NULL, 'url|max:255', 8, 1, 0, '点击底部版权栏中的技术栈文字时跳转的 URL。可指向项目介绍页或框架官网。', '2026-07-28 03:46:34', '2026-07-28 03:46:34');
INSERT INTO `system_configs` VALUES (22, 'site_developer_name', 'Seeding', 'Seeding', 'string', 'basic', '开发者名称', '底部版权栏显示的开发者名称', NULL, 'max:50', 9, 1, 0, '显示在页面底部版权栏中的开发者名称，如\"Seeding\"。留空则不显示开发者信息。', '2026-07-28 03:46:34', '2026-07-28 03:46:34');
INSERT INTO `system_configs` VALUES (23, 'site_developer_url', '', '', 'string', 'basic', '开发者链接', '底部版权栏\"开发者名称\"的跳转链接，留空则只显示文字不可点击', NULL, 'nullable|url|max:255', 10, 1, 0, '点击底部版权栏中的开发者名称时跳转的 URL。留空则开发者名称仅显示文字，不可点击。', '2026-07-28 03:46:34', '2026-07-28 03:46:34');
INSERT INTO `system_configs` VALUES (24, 'site_icp_url', 'https://beian.miit.gov.cn/', 'https://beian.miit.gov.cn/', 'string', 'basic', '备案号链接', '底部版权栏\"ICP备案号\"的跳转链接', NULL, 'url|max:255', 11, 1, 0, '点击底部版权栏中的备案号时跳转的 URL，默认指向工信部备案查询页。', '2026-07-28 03:46:34', '2026-07-28 03:46:34');

-- ----------------------------
-- Table structure for tags
-- ----------------------------
DROP TABLE IF EXISTS `tags`;
CREATE TABLE `tags`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标签名称',
  `sort` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `tags_name_unique`(`name` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '标签词库表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tags
-- ----------------------------

-- ----------------------------
-- Table structure for temperatures
-- ----------------------------
DROP TABLE IF EXISTS `temperatures`;
CREATE TABLE `temperatures`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `delivery_task_id` bigint UNSIGNED NOT NULL COMMENT '配送任务ID',
  `temperature` int NOT NULL DEFAULT 0 COMMENT '温度值',
  `recorded_at` timestamp NULL DEFAULT NULL COMMENT '记录时间',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `temperatures_delivery_task_id_index`(`delivery_task_id` ASC) USING BTREE,
  INDEX `temperatures_recorded_at_index`(`recorded_at` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '冷链温度记录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of temperatures
-- ----------------------------

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户名',
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'bcrypt加密密码',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '姓名',
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '手机号',
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '邮箱',
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '头像',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：0禁用，1启用',
  `last_login_at` timestamp NULL DEFAULT NULL COMMENT '最后登录时间',
  `email_verified_at` timestamp NULL DEFAULT NULL COMMENT '邮箱验证时间',
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '记住我令牌',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `users_username_unique`(`username` ASC) USING BTREE,
  UNIQUE INDEX `users_phone_unique`(`phone` ASC) USING BTREE,
  UNIQUE INDEX `users_email_unique`(`email` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '用户表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 'seeding', '$2y$12$jDUqr9NaRg/FmYov3LejBeXiDnX3I/u/Sz8OyW2daCAwQOKbSLq5u', '系统管理员', '15690631151', 'seeding@ihopeso.cn', NULL, 1, NULL, NULL, 'Any6CGserUiiDVsidSQFIxspDvx3RJsKcT7z5PKv0LZEnIajvMeyHZn0uQR6', '2026-07-27 09:23:29', '2026-07-27 09:23:29', NULL);
INSERT INTO `users` VALUES (2, 'operator1', '$2y$12$hbFgRTcNfeXeYdhyXq41n.U6MAGcOlFF01EwNpgjGl.EOcdFFXmqu', '张运营', '13800000001', 'operator@susong.test', NULL, 1, NULL, NULL, NULL, '2026-07-27 09:23:33', '2026-07-27 09:23:33', NULL);
INSERT INTO `users` VALUES (3, 'ops_manager', '$2y$12$RDDaBxhvu2UQJUW7ov2iq.6Ruzy4P7kVZl7HYSvBck.zARKtJse2e', '李运营经理', '13800000002', 'ops_manager@susong.test', NULL, 1, NULL, NULL, NULL, '2026-07-27 09:23:33', '2026-07-27 09:23:33', NULL);
INSERT INTO `users` VALUES (4, 'finance1', '$2y$12$78ndvs3ajwYQLzQXnuNcx.mzEEaaJAzF3RpzhkJvlpNxo8h7jtU/W', '王财务', '13800000003', 'finance@susong.test', NULL, 1, NULL, NULL, NULL, '2026-07-27 09:23:33', '2026-07-27 09:23:33', NULL);
INSERT INTO `users` VALUES (5, 'cashier1', '$2y$12$h.GqcHgsmJwSND8LD/Y6AeAjDpzE5HCX2KEqfa1.ppfrP.piHmPW2', '赵出纳', '13800000004', 'cashier@susong.test', NULL, 1, NULL, NULL, NULL, '2026-07-27 09:23:33', '2026-07-27 09:23:33', NULL);
INSERT INTO `users` VALUES (6, 'fin_manager', '$2y$12$pRe7quGZa4uHCRT.VDmwseHCL./gFIE6iTPdRnLdAk1FvseGxeW0e', '钱财务经理', '13800000005', 'finance_manager@susong.test', NULL, 1, NULL, NULL, NULL, '2026-07-27 09:23:33', '2026-07-27 09:23:33', NULL);
INSERT INTO `users` VALUES (7, 'picker1', '$2y$12$46n6.AoZO615W2VNk6KJi.ZS5jmwLN1nKXIcQbkj3pfUY2VlToMky', '孙拣货员', '13800000006', 'picker@susong.test', NULL, 1, NULL, NULL, NULL, '2026-07-27 09:23:33', '2026-07-27 09:23:33', NULL);
INSERT INTO `users` VALUES (8, 'driver1', '$2y$12$4BBXPnSoUYWVJBIbwjcc..t51PeSUspZHtVL.cU4H3ycquUuO3Z92', '周司机', '13800000007', 'driver@susong.test', NULL, 1, NULL, NULL, NULL, '2026-07-27 09:23:33', '2026-07-27 09:23:33', NULL);
INSERT INTO `users` VALUES (9, 'merchant1', '$2y$12$usCeuGixnwI.ZUvkp4FqkeG.RoKNYXZVRRP/7Uf/N0JtQ2HSMO086', '吴商家', '13800000008', 'merchant@susong.test', NULL, 1, NULL, NULL, NULL, '2026-07-27 09:23:33', '2026-07-27 09:23:33', NULL);

-- ----------------------------
-- Table structure for vehicles
-- ----------------------------
DROP TABLE IF EXISTS `vehicles`;
CREATE TABLE `vehicles`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `plate_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '车牌号',
  `vehicle_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '车辆类型',
  `is_cold_chain` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否冷链：0否，1是',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：0禁用，1启用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `vehicles_plate_number_unique`(`plate_number` ASC) USING BTREE,
  INDEX `vehicles_status_index`(`status` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '车辆表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of vehicles
-- ----------------------------
INSERT INTO `vehicles` VALUES (1, '皖LT0001', '1', 0, 1, '2026-07-27 09:23:35', '2026-07-27 09:23:35', NULL);
INSERT INTO `vehicles` VALUES (2, '皖LT0002', '1', 0, 1, '2026-07-27 09:23:35', '2026-07-27 09:23:35', NULL);

-- ----------------------------
-- Table structure for warehouses
-- ----------------------------
DROP TABLE IF EXISTS `warehouses`;
CREATE TABLE `warehouses`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '仓库名称',
  `type` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '类型：1总仓，2前置仓',
  `is_cold_chain` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否冷链：0否，1是',
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '地址',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `warehouses_status_index`(`status` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '仓库表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of warehouses
-- ----------------------------
INSERT INTO `warehouses` VALUES (1, '总仓-农批市场', 1, 0, '安徽省宿州市埇桥区农批市场内', 1, '2026-07-27 09:23:35', '2026-07-27 09:23:35', NULL);
INSERT INTO `warehouses` VALUES (2, '分仓-肉联厂', 2, 1, '安徽省宿州市埇桥区肉联厂内', 1, '2026-07-27 09:23:35', '2026-07-27 09:23:35', NULL);

-- ----------------------------
-- Table structure for wechat_users
-- ----------------------------
DROP TABLE IF EXISTS `wechat_users`;
CREATE TABLE `wechat_users`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT '关联系统用户ID',
  `openid` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '微信OpenID',
  `unionid` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '微信UnionID',
  `nickname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '昵称',
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '头像',
  `type` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '类型：1商家端，2司机端',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `wechat_users_openid_unique`(`openid` ASC) USING BTREE,
  INDEX `wechat_users_user_id_index`(`user_id` ASC) USING BTREE,
  INDEX `wechat_users_type_index`(`type` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '微信用户绑定表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of wechat_users
-- ----------------------------

SET FOREIGN_KEY_CHECKS = 1;
