-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: livewire
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `approval_type_configs`
--

DROP TABLE IF EXISTS `approval_type_configs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `approval_type_configs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `type_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '审核类型编码（唯一）',
  `type_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '审核类型名称',
  `module_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '所属模块名称',
  `risk_level` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'P1' COMMENT '风险等级：P0/P1',
  `enabled` tinyint NOT NULL DEFAULT '0' COMMENT '是否启用审核：0关闭，1开启',
  `applicant_role_id` bigint unsigned NOT NULL COMMENT '申请人角色ID',
  `reviewer_role_id` bigint unsigned NOT NULL COMMENT '审核人角色ID',
  `sort_order` int NOT NULL DEFAULT '0' COMMENT '显示排序',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '审核节点说明',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `approval_type_configs_type_code_unique` (`type_code`),
  KEY `approval_type_configs_enabled_index` (`enabled`),
  KEY `approval_type_configs_reviewer_role_id_index` (`reviewer_role_id`),
  KEY `approval_type_configs_applicant_role_id_index` (`applicant_role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='审核类型配置表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `approval_type_configs`
--

LOCK TABLES `approval_type_configs` WRITE;
/*!40000 ALTER TABLE `approval_type_configs` DISABLE KEYS */;
INSERT INTO `approval_type_configs` VALUES (1,'manual_recharge','后台手工充值','财务对账','P0',0,2,6,1,'运营管理员为商家手动充值','2026-07-27 20:30:16','2026-07-27 20:50:21'),(2,'supplier_payment','供应商付款录入','财务对账','P0',0,5,6,2,'出纳录入供应商付款记录','2026-07-27 20:30:16','2026-07-27 20:53:12'),(3,'customer_receipt','客户收款录入','财务对账','P0',1,5,6,3,'出纳录入客户收款记录','2026-07-27 20:30:16','2026-07-27 20:30:16'),(4,'credit_limit','信用额度调整','商家管理','P0',1,2,6,4,'修改商家信用额度','2026-07-27 20:30:16','2026-07-27 20:30:16'),(5,'price_strategy','价格策略创建/修改','价格策略','P0',1,2,6,5,'创建或修改促销/临时改价策略','2026-07-27 20:30:16','2026-07-27 20:30:16'),(6,'manual_apportion','手动均摊调整','费用均摊','P0',1,4,3,6,'手动修改费用均摊金额','2026-07-27 20:30:16','2026-07-27 20:30:16'),(7,'diff_refund_deduct','差异退款/扣款决策','差异处理','P0',1,2,6,7,'差异处理决策为退款或扣款','2026-07-27 20:30:16','2026-07-27 20:30:16'),(8,'sku_price_change','SKU 批发价修改(>15%)','商品管理','P1',1,2,3,8,'修改SKU批发销售价幅度>15%','2026-07-27 20:30:16','2026-07-27 20:30:16'),(9,'receivable_adjust','应收改价折扣调整','财务对账','P0',1,2,6,9,'改价/促销导致应收金额调整','2026-07-27 20:30:16','2026-07-27 20:30:16'),(10,'recharge_confirm','商家充值确认','财务对账','P0',1,2,6,10,'商家微信/线下充值待确认','2026-07-27 20:30:16','2026-07-27 20:30:16'),(11,'purchase_return','采购退货','平台统采','P0',0,2,6,11,'采购退货审批','2026-07-27 20:30:16','2026-07-27 20:53:00'),(12,'after_sale_return','售后退货退款','客户直采','P0',0,2,6,12,'售后退货退款审批','2026-07-27 20:30:16','2026-07-27 20:30:16'),(13,'auth_correction','单据授权更正','财务对账','P0',0,4,6,13,'解锁已锁定数据允许更正','2026-07-27 20:30:16','2026-07-27 20:30:16'),(14,'weighing_price','称重改价(≤20%)','客户直采','P1',0,7,3,14,'称重改价金额生效','2026-07-27 20:30:16','2026-07-27 20:30:16'),(15,'purchase_warehouse','采购入库确认','平台统采','P1',0,2,3,15,'入库确认触发库存联动','2026-07-27 20:30:16','2026-07-27 20:30:16'),(16,'supplier_bank_edit','供应商银行信息修改','组织主体','P1',0,2,6,16,'银行收付款信息生效','2026-07-27 20:30:16','2026-07-27 20:30:16'),(17,'manual_close','手动办结','财务对账','P1',0,4,6,17,'单据办结锁定','2026-07-27 20:30:16','2026-07-27 20:30:16'),(18,'sku_price_minor','SKU小幅改价(≤15%)','商品管理','P1',0,2,3,18,'小幅改价生效','2026-07-27 20:30:16','2026-07-27 20:30:16'),(19,'loss_order','损耗单审批','损耗管理','P1',1,2,3,19,'损耗金额超过审批阈值时需运营经理审核','2026-07-27 20:30:16','2026-07-27 20:30:16');
/*!40000 ALTER TABLE `approval_type_configs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `approvals`
--

DROP TABLE IF EXISTS `approvals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `approvals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `approval_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '审核类型编码，关联 approval_type_configs.type_code',
  `target_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '关联单据类型',
  `target_id` bigint unsigned NOT NULL COMMENT '关联单据ID',
  `applicant_id` bigint unsigned NOT NULL COMMENT '申请人ID',
  `applicant_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '申请人姓名',
  `before_data` json DEFAULT NULL COMMENT '操作前数据快照',
  `after_data` json DEFAULT NULL COMMENT '操作后数据快照',
  `amount` bigint DEFAULT NULL COMMENT '涉及金额',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：1待审核，2已通过，3已拒绝，4已撤回',
  `reviewer_id` bigint unsigned DEFAULT NULL COMMENT '审核人ID',
  `reviewer_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '审核人姓名',
  `review_remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '审核备注（拒绝原因等）',
  `reviewed_at` timestamp NULL DEFAULT NULL COMMENT '审核时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `approvals_approval_type_index` (`approval_type`),
  KEY `approvals_target_type_target_id_index` (`target_type`,`target_id`),
  KEY `approvals_applicant_id_index` (`applicant_id`),
  KEY `approvals_status_index` (`status`),
  KEY `approvals_reviewer_id_index` (`reviewer_id`),
  KEY `approvals_created_at_index` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='审批记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `approvals`
--

LOCK TABLES `approvals` WRITE;
/*!40000 ALTER TABLE `approvals` DISABLE KEYS */;
INSERT INTO `approvals` VALUES (1,'manual_recharge','TestOrder',100,1,'seeding',NULL,'\"{\\\"amount\\\":15000}\"',15000,1,NULL,NULL,NULL,NULL,'2026-07-27 20:51:09','2026-07-27 20:51:09');
/*!40000 ALTER TABLE `approvals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `model_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '模型类型',
  `model_id` bigint unsigned NOT NULL COMMENT '模型ID',
  `action` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '操作动作',
  `before_data` json DEFAULT NULL COMMENT '修改前数据',
  `after_data` json DEFAULT NULL COMMENT '修改后数据',
  `operator_id` bigint unsigned DEFAULT NULL COMMENT '操作人ID',
  `ip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '操作人IP地址',
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '浏览器/客户端UA',
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '操作原因',
  `relation_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '关联类型',
  `relation_id` bigint unsigned DEFAULT NULL COMMENT '关联ID',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `audit_logs_model_type_model_id_index` (`model_type`,`model_id`),
  KEY `audit_logs_operator_id_index` (`operator_id`),
  KEY `audit_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='审计日志表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (1,'ApprovalTypeConfig',1,'update','{\"enabled\": 1}','{\"enabled\": 0}',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','关闭审核节点',NULL,NULL,'2026-07-27 20:44:04'),(2,'ApprovalTypeConfig',1,'update','{\"enabled\": 0}','{\"enabled\": 1}',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','启用审核节点',NULL,NULL,'2026-07-27 20:44:38'),(3,'ApprovalTypeConfig',1,'update','{\"enabled\": 1}','{\"enabled\": 0}',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','关闭审核节点',NULL,NULL,'2026-07-27 20:50:21'),(4,'ApprovalTypeConfig',11,'update','{\"enabled\": 0}','{\"enabled\": 1}',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','启用审核节点',NULL,NULL,'2026-07-27 20:52:58'),(5,'ApprovalTypeConfig',11,'update','{\"enabled\": 1}','{\"enabled\": 0}',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','关闭审核节点',NULL,NULL,'2026-07-27 20:53:00'),(6,'ApprovalTypeConfig',2,'update','{\"enabled\": 1}','{\"enabled\": 0}',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','关闭审核节点',NULL,NULL,'2026-07-27 20:53:12');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `banners`
--

DROP TABLE IF EXISTS `banners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `banners` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标题',
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '图片地址',
  `link_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '跳转链接',
  `sort` int unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：0禁用，1启用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `banners_status_index` (`status`),
  KEY `banners_sort_index` (`sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='轮播广告表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banners`
--

LOCK TABLES `banners` WRITE;
/*!40000 ALTER TABLE `banners` DISABLE KEYS */;
/*!40000 ALTER TABLE `banners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cart_items`
--

DROP TABLE IF EXISTS `cart_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `cart_id` bigint unsigned NOT NULL COMMENT '购物车ID',
  `sku_id` bigint unsigned NOT NULL COMMENT 'SKU ID',
  `quantity` bigint NOT NULL DEFAULT '0' COMMENT '数量',
  `price` bigint NOT NULL DEFAULT '0' COMMENT '加入时单价',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cart_items_cart_id_index` (`cart_id`),
  KEY `cart_items_sku_id_index` (`sku_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='购物车明细表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart_items`
--

LOCK TABLES `cart_items` WRITE;
/*!40000 ALTER TABLE `cart_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `cart_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carts`
--

DROP TABLE IF EXISTS `carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `merchant_id` bigint unsigned NOT NULL COMMENT '商家ID',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `carts_merchant_id_unique` (`merchant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='购物车表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carts`
--

LOCK TABLES `carts` WRITE;
/*!40000 ALTER TABLE `carts` DISABLE KEYS */;
/*!40000 ALTER TABLE `carts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `parent_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '父级分类ID，0为根节点',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '分类名称',
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '图标',
  `sort` int unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：0禁用，1启用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categories_parent_id_index` (`parent_id`),
  KEY `categories_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品分类表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,0,'蔬菜',NULL,1,1,'2026-07-27 20:30:18','2026-07-27 20:30:18',NULL),(2,0,'水果',NULL,2,1,'2026-07-27 20:30:18','2026-07-27 20:30:18',NULL),(3,0,'肉类',NULL,3,1,'2026-07-27 20:30:18','2026-07-27 20:30:18',NULL),(4,0,'水产',NULL,4,1,'2026-07-27 20:30:18','2026-07-27 20:30:18',NULL),(5,0,'粮油',NULL,5,1,'2026-07-27 20:30:18','2026-07-27 20:30:18',NULL),(6,0,'调料',NULL,6,1,'2026-07-27 20:30:18','2026-07-27 20:30:18',NULL),(7,0,'豆制品',NULL,7,1,'2026-07-27 20:30:18','2026-07-27 20:30:18',NULL),(8,0,'冷冻食品',NULL,8,1,'2026-07-27 20:30:18','2026-07-27 20:30:18',NULL);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `correction_authorizations`
--

DROP TABLE IF EXISTS `correction_authorizations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `correction_authorizations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `order_id` bigint unsigned NOT NULL COMMENT '订单ID',
  `operator_id` bigint unsigned NOT NULL COMMENT '授权人ID',
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '更正原因',
  `before_data` json DEFAULT NULL COMMENT '修改前数据',
  `after_data` json DEFAULT NULL COMMENT '修改后数据',
  `authorized_at` timestamp NULL DEFAULT NULL COMMENT '授权时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `correction_authorizations_order_id_index` (`order_id`),
  KEY `correction_authorizations_operator_id_index` (`operator_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='单据授权更正表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `correction_authorizations`
--

LOCK TABLES `correction_authorizations` WRITE;
/*!40000 ALTER TABLE `correction_authorizations` DISABLE KEYS */;
/*!40000 ALTER TABLE `correction_authorizations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_routes`
--

DROP TABLE IF EXISTS `delivery_routes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_routes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '线路名称',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '描述',
  `sort` int unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `delivery_routes_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='配送线路表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_routes`
--

LOCK TABLES `delivery_routes` WRITE;
/*!40000 ALTER TABLE `delivery_routes` DISABLE KEYS */;
INSERT INTO `delivery_routes` VALUES (1,'城区北线','人民路-淮海路-汴河路北侧',0,1,'2026-07-27 20:30:18','2026-07-27 20:30:18',NULL),(2,'城区南线','银河路-胜利路-宿怀路南侧',0,1,'2026-07-27 20:30:18','2026-07-27 20:30:18',NULL);
/*!40000 ALTER TABLE `delivery_routes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_task_orders`
--

DROP TABLE IF EXISTS `delivery_task_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_task_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `delivery_task_id` bigint unsigned NOT NULL COMMENT '配送任务ID',
  `order_id` bigint unsigned NOT NULL COMMENT '订单ID',
  `delivery_sort` int unsigned NOT NULL DEFAULT '0' COMMENT '配送顺序',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：1待配送，2已送达',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `delivery_task_orders_delivery_task_id_index` (`delivery_task_id`),
  KEY `delivery_task_orders_order_id_index` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='配送任务订单关联表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_task_orders`
--

LOCK TABLES `delivery_task_orders` WRITE;
/*!40000 ALTER TABLE `delivery_task_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `delivery_task_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_tasks`
--

DROP TABLE IF EXISTS `delivery_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_tasks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `task_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '任务编号',
  `delivery_route_id` bigint unsigned NOT NULL COMMENT '线路ID',
  `driver_id` bigint unsigned DEFAULT NULL COMMENT '司机ID',
  `vehicle_id` bigint unsigned DEFAULT NULL COMMENT '车辆ID',
  `batch` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '配送批次：1上午，2下午',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：1待配送，2配送中，3任务完成',
  `planned_at` timestamp NULL DEFAULT NULL COMMENT '计划配送时间',
  `started_at` timestamp NULL DEFAULT NULL COMMENT '开始时间',
  `completed_at` timestamp NULL DEFAULT NULL COMMENT '完成时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `delivery_tasks_task_no_unique` (`task_no`),
  KEY `delivery_tasks_delivery_route_id_index` (`delivery_route_id`),
  KEY `delivery_tasks_driver_id_index` (`driver_id`),
  KEY `delivery_tasks_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='配送任务表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_tasks`
--

LOCK TABLES `delivery_tasks` WRITE;
/*!40000 ALTER TABLE `delivery_tasks` DISABLE KEYS */;
/*!40000 ALTER TABLE `delivery_tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_tracks`
--

DROP TABLE IF EXISTS `delivery_tracks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_tracks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `delivery_task_id` bigint unsigned NOT NULL COMMENT '配送任务ID',
  `driver_id` bigint unsigned NOT NULL COMMENT '司机ID',
  `latitude` int NOT NULL DEFAULT '0' COMMENT '纬度',
  `longitude` int NOT NULL DEFAULT '0' COMMENT '经度',
  `location_desc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '位置描述',
  `reported_at` timestamp NULL DEFAULT NULL COMMENT '上报时间',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `delivery_tracks_delivery_task_id_index` (`delivery_task_id`),
  KEY `delivery_tracks_driver_id_index` (`driver_id`),
  KEY `delivery_tracks_reported_at_index` (`reported_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='配送轨迹表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_tracks`
--

LOCK TABLES `delivery_tracks` WRITE;
/*!40000 ALTER TABLE `delivery_tracks` DISABLE KEYS */;
/*!40000 ALTER TABLE `delivery_tracks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `discrepancies`
--

DROP TABLE IF EXISTS `discrepancies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `discrepancies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `discrepancy_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '差异单号',
  `order_id` bigint unsigned NOT NULL COMMENT '关联订单ID',
  `order_item_id` bigint unsigned DEFAULT NULL COMMENT '关联订单明细ID',
  `stage` tinyint unsigned NOT NULL COMMENT '差异环节：1拣货，2配送，3实收',
  `type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '差异类型：1少收，2拒收，3残次，4其他',
  `expected_quantity` bigint NOT NULL DEFAULT '0' COMMENT '预期数量',
  `actual_quantity` bigint NOT NULL DEFAULT '0' COMMENT '实际数量',
  `quantity_diff` bigint NOT NULL DEFAULT '0' COMMENT '数量差异',
  `amount_diff` bigint NOT NULL DEFAULT '0' COMMENT '金额差异',
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '差异原因',
  `evidence_urls` json DEFAULT NULL COMMENT '凭证图片数组',
  `responsible_party` tinyint unsigned DEFAULT NULL COMMENT '责任方：1供应商，2平台，3司机，4商家',
  `decision` tinyint unsigned DEFAULT NULL COMMENT '处理决策：1补货，2退款，3扣款，4报损，5不计',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：1待处理，2处理中，3已处理，4已关闭，5争议中',
  `handler_id` bigint unsigned DEFAULT NULL COMMENT '处理人ID',
  `handled_at` timestamp NULL DEFAULT NULL COMMENT '处理时间',
  `is_amount_adjusted` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '是否已调整金额',
  `approval_status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '审核状态：1待审核，2已通过，3已拒绝（决策为退款/扣款时有效）',
  `remark` text COLLATE utf8mb4_unicode_ci COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `discrepancies_discrepancy_no_unique` (`discrepancy_no`),
  KEY `discrepancies_order_id_index` (`order_id`),
  KEY `discrepancies_order_item_id_index` (`order_item_id`),
  KEY `discrepancies_stage_index` (`stage`),
  KEY `discrepancies_status_index` (`status`),
  KEY `discrepancies_approval_status_index` (`approval_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='差异单表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `discrepancies`
--

LOCK TABLES `discrepancies` WRITE;
/*!40000 ALTER TABLE `discrepancies` DISABLE KEYS */;
/*!40000 ALTER TABLE `discrepancies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `driver_vehicles`
--

DROP TABLE IF EXISTS `driver_vehicles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `driver_vehicles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `driver_id` bigint unsigned NOT NULL COMMENT '司机ID',
  `vehicle_id` bigint unsigned NOT NULL COMMENT '车辆ID',
  `is_default` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '是否默认车辆',
  `bound_at` timestamp NULL DEFAULT NULL COMMENT '绑定时间',
  `unbound_at` timestamp NULL DEFAULT NULL COMMENT '解绑时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `driver_vehicles_driver_id_index` (`driver_id`),
  KEY `driver_vehicles_vehicle_id_index` (`vehicle_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='司机车辆绑定表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `driver_vehicles`
--

LOCK TABLES `driver_vehicles` WRITE;
/*!40000 ALTER TABLE `driver_vehicles` DISABLE KEYS */;
INSERT INTO `driver_vehicles` VALUES (1,1,1,0,NULL,NULL,'2026-07-27 20:30:18','2026-07-27 20:30:18'),(2,2,2,0,NULL,NULL,'2026-07-27 20:30:18','2026-07-27 20:30:18');
/*!40000 ALTER TABLE `driver_vehicles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `drivers`
--

DROP TABLE IF EXISTS `drivers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `drivers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` bigint unsigned DEFAULT NULL COMMENT '关联登录用户ID',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '姓名',
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '手机号',
  `id_card` varchar(18) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '身份证号',
  `online_status` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '在线状态：0离线，1在线',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：0禁用，1启用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `drivers_phone_unique` (`phone`),
  KEY `drivers_user_id_index` (`user_id`),
  KEY `drivers_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='司机表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `drivers`
--

LOCK TABLES `drivers` WRITE;
/*!40000 ALTER TABLE `drivers` DISABLE KEYS */;
INSERT INTO `drivers` VALUES (1,NULL,'周师傅','13700000001',NULL,0,1,'2026-07-27 20:30:18','2026-07-27 20:30:18',NULL),(2,NULL,'马师傅','13700000002',NULL,0,1,'2026-07-27 20:30:18','2026-07-27 20:30:18',NULL);
/*!40000 ALTER TABLE `drivers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `frequently_bought`
--

DROP TABLE IF EXISTS `frequently_bought`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `frequently_bought` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `merchant_id` bigint unsigned NOT NULL COMMENT '商家ID',
  `sku_id` bigint unsigned NOT NULL COMMENT 'SKU ID',
  `buy_count` int unsigned NOT NULL DEFAULT '0' COMMENT '购买次数',
  `last_buy_at` timestamp NULL DEFAULT NULL COMMENT '最近购买时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `frequently_bought_merchant_id_sku_id_unique` (`merchant_id`,`sku_id`),
  KEY `frequently_bought_sku_id_index` (`sku_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='常购清单表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `frequently_bought`
--

LOCK TABLES `frequently_bought` WRITE;
/*!40000 ALTER TABLE `frequently_bought` DISABLE KEYS */;
/*!40000 ALTER TABLE `frequently_bought` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory`
--

DROP TABLE IF EXISTS `inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `warehouse_id` bigint unsigned NOT NULL COMMENT '仓库ID',
  `sku_id` bigint unsigned NOT NULL COMMENT 'SKU ID',
  `total_stock` bigint NOT NULL DEFAULT '0' COMMENT '总库存',
  `locked_stock` bigint NOT NULL DEFAULT '0' COMMENT '锁定库存',
  `available_stock` bigint NOT NULL DEFAULT '0' COMMENT '可用库存',
  `batch_no` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '入库批次号',
  `expiry_date` date DEFAULT NULL COMMENT '效期',
  `warning_value` bigint NOT NULL DEFAULT '0' COMMENT '预警值',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inventory_warehouse_id_sku_id_batch_no_unique` (`warehouse_id`,`sku_id`,`batch_no`),
  KEY `inventory_sku_id_index` (`sku_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='实时库存表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory`
--

LOCK TABLES `inventory` WRITE;
/*!40000 ALTER TABLE `inventory` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_logs`
--

DROP TABLE IF EXISTS `inventory_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `warehouse_id` bigint unsigned NOT NULL COMMENT '仓库ID',
  `sku_id` bigint unsigned NOT NULL COMMENT 'SKU ID',
  `type` tinyint unsigned NOT NULL COMMENT '变动类型：1入库，2出库，3调拨，4报损，5报溢，6调整',
  `quantity` bigint NOT NULL DEFAULT '0' COMMENT '变动数量，正增负减',
  `before_stock` bigint NOT NULL DEFAULT '0' COMMENT '变动前库存',
  `after_stock` bigint NOT NULL DEFAULT '0' COMMENT '变动后库存',
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '变动原因',
  `operator_id` bigint unsigned DEFAULT NULL COMMENT '操作人ID',
  `source_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '业务来源类型',
  `source_id` bigint unsigned DEFAULT NULL COMMENT '业务来源ID',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `inventory_logs_warehouse_id_index` (`warehouse_id`),
  KEY `inventory_logs_sku_id_index` (`sku_id`),
  KEY `inventory_logs_type_index` (`type`),
  KEY `inventory_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='库存变动日志表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_logs`
--

LOCK TABLES `inventory_logs` WRITE;
/*!40000 ALTER TABLE `inventory_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `invoice_no` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '发票号',
  `type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '类型：1客户发票，2供应商发票',
  `target_id` bigint unsigned NOT NULL COMMENT '关联对象ID',
  `title` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '发票抬头',
  `amount` bigint NOT NULL DEFAULT '0' COMMENT '金额',
  `file_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '发票文件地址',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：1待开具，2已开具，3已寄出',
  `applied_at` timestamp NULL DEFAULT NULL COMMENT '申请时间',
  `issued_at` timestamp NULL DEFAULT NULL COMMENT '开具时间',
  `sent_at` timestamp NULL DEFAULT NULL COMMENT '寄出时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoices_target_id_index` (`target_id`),
  KEY `invoices_type_index` (`type`),
  KEY `invoices_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='发票表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `keywords`
--

DROP TABLE IF EXISTS `keywords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `keywords` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `keyword` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '关键词',
  `product_id` bigint unsigned DEFAULT NULL COMMENT '关联商品ID',
  `search_count` int unsigned NOT NULL DEFAULT '0' COMMENT '搜索次数',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `keywords_product_id_index` (`product_id`),
  KEY `keywords_keyword_index` (`keyword`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='搜索关键词表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `keywords`
--

LOCK TABLES `keywords` WRITE;
/*!40000 ALTER TABLE `keywords` DISABLE KEYS */;
/*!40000 ALTER TABLE `keywords` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_logs`
--

DROP TABLE IF EXISTS `login_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `login_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` bigint unsigned DEFAULT NULL COMMENT '用户ID',
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '登录账号',
  `ip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'IP地址',
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '浏览器/客户端UA',
  `login_type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '类型：1管理后台，2商家小程序，3司机小程序',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '结果：1成功，0失败',
  `fail_reason` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '失败原因',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '登录时间',
  PRIMARY KEY (`id`),
  KEY `login_logs_user_id_index` (`user_id`),
  KEY `login_logs_username_index` (`username`),
  KEY `login_logs_ip_index` (`ip`),
  KEY `login_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='登录日志表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_logs`
--

LOCK TABLES `login_logs` WRITE;
/*!40000 ALTER TABLE `login_logs` DISABLE KEYS */;
INSERT INTO `login_logs` VALUES (1,1,'seeding','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0',1,1,NULL,'2026-07-27 23:17:30'),(2,1,'seeding','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0',1,1,NULL,'2026-07-28 01:19:22'),(3,1,'seeding','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',1,1,NULL,'2026-07-28 17:13:17'),(4,1,'seeding','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',1,1,NULL,'2026-07-28 17:13:58'),(5,1,'seeding','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0',1,1,NULL,'2026-07-28 17:16:18'),(6,1,'seeding','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',1,1,NULL,'2026-07-28 17:38:21'),(7,1,'seeding','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0',1,1,NULL,'2026-07-28 17:43:28'),(8,1,'seeding','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0',1,1,NULL,'2026-07-28 17:53:26'),(9,1,'seeding','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',1,1,NULL,'2026-07-28 17:53:37');
/*!40000 ALTER TABLE `login_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `loss_order_items`
--

DROP TABLE IF EXISTS `loss_order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `loss_order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `loss_order_id` bigint unsigned NOT NULL COMMENT '损耗单ID',
  `sku_id` bigint unsigned NOT NULL COMMENT 'SKU ID',
  `loss_type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '损耗类型：1存储腐坏，2称重失水，3过期报废，4加工损耗，5盘点差异，6其他',
  `quantity` bigint NOT NULL DEFAULT '0' COMMENT '损耗数量',
  `cost_price` bigint NOT NULL DEFAULT '0' COMMENT 'SKU成本价快照',
  `loss_amount` bigint NOT NULL DEFAULT '0' COMMENT '损耗金额',
  `responsible_party` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '责任方：1平台，2供应商',
  `supplier_id` bigint unsigned DEFAULT NULL COMMENT '供应商ID',
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '明细损耗原因',
  `evidence_urls` json DEFAULT NULL COMMENT '凭证图片数组',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `loss_order_items_loss_order_id_index` (`loss_order_id`),
  KEY `loss_order_items_sku_id_index` (`sku_id`),
  KEY `loss_order_items_loss_type_index` (`loss_type`),
  KEY `loss_order_items_supplier_id_index` (`supplier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='损耗单明细表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loss_order_items`
--

LOCK TABLES `loss_order_items` WRITE;
/*!40000 ALTER TABLE `loss_order_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `loss_order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `loss_orders`
--

DROP TABLE IF EXISTS `loss_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `loss_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `loss_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '损耗单号',
  `warehouse_id` bigint unsigned NOT NULL COMMENT '仓库ID',
  `total_amount` bigint NOT NULL DEFAULT '0' COMMENT '损耗总金额',
  `loss_type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '主要损耗类型：1存储腐坏，2称重失水，3过期报废，4加工损耗，5盘点差异，6其他',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：1待审核，2已通过，3已执行，4已关闭，9已取消',
  `approval_status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '审核状态：1待审核，2已通过，3已拒绝',
  `applicant_id` bigint unsigned DEFAULT NULL COMMENT '申请人ID',
  `reviewer_id` bigint unsigned DEFAULT NULL COMMENT '审核人ID',
  `reviewed_at` timestamp NULL DEFAULT NULL COMMENT '审核时间',
  `executed_at` timestamp NULL DEFAULT NULL COMMENT '执行时间',
  `closed_at` timestamp NULL DEFAULT NULL COMMENT '关闭时间',
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '损耗原因',
  `remark` text COLLATE utf8mb4_unicode_ci COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `loss_orders_loss_no_unique` (`loss_no`),
  KEY `loss_orders_warehouse_id_index` (`warehouse_id`),
  KEY `loss_orders_loss_type_index` (`loss_type`),
  KEY `loss_orders_status_index` (`status`),
  KEY `loss_orders_approval_status_index` (`approval_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='损耗单主表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loss_orders`
--

LOCK TABLES `loss_orders` WRITE;
/*!40000 ALTER TABLE `loss_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `loss_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `merchant_accounts`
--

DROP TABLE IF EXISTS `merchant_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `merchant_accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `merchant_id` bigint unsigned NOT NULL COMMENT '商家ID',
  `balance` bigint NOT NULL DEFAULT '0' COMMENT '账户余额',
  `total_recharge` bigint NOT NULL DEFAULT '0' COMMENT '总充值',
  `total_consumption` bigint NOT NULL DEFAULT '0' COMMENT '总消费',
  `credit_limit` bigint NOT NULL DEFAULT '0' COMMENT '信用额度',
  `approval_status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '审核状态：1待审核，2已通过，3已拒绝（信用额度调整时有效）',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `merchant_accounts_merchant_id_unique` (`merchant_id`),
  KEY `merchant_accounts_approval_status_index` (`approval_status`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商家账户表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `merchant_accounts`
--

LOCK TABLES `merchant_accounts` WRITE;
/*!40000 ALTER TABLE `merchant_accounts` DISABLE KEYS */;
INSERT INTO `merchant_accounts` VALUES (1,1,0,0,0,5000000,2,'2026-07-27 20:30:18','2026-07-27 20:30:18'),(2,2,0,0,0,5000000,2,'2026-07-27 20:30:18','2026-07-27 20:30:18'),(3,3,0,0,0,5000000,2,'2026-07-27 20:30:18','2026-07-27 20:30:18'),(4,4,0,0,0,5000000,2,'2026-07-27 20:30:18','2026-07-27 20:30:18'),(5,5,0,0,0,5000000,2,'2026-07-27 20:30:18','2026-07-27 20:30:18');
/*!40000 ALTER TABLE `merchant_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `merchant_addresses`
--

DROP TABLE IF EXISTS `merchant_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `merchant_addresses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `merchant_id` bigint unsigned NOT NULL COMMENT '商家ID',
  `contact_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '联系人',
  `contact_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '联系电话',
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '收货地址',
  `is_default` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '是否默认地址：0否，1是',
  `sort` int unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `merchant_addresses_merchant_id_index` (`merchant_id`),
  KEY `merchant_addresses_is_default_index` (`is_default`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商家收货地址表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `merchant_addresses`
--

LOCK TABLES `merchant_addresses` WRITE;
/*!40000 ALTER TABLE `merchant_addresses` DISABLE KEYS */;
/*!40000 ALTER TABLE `merchant_addresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `merchant_favorites`
--

DROP TABLE IF EXISTS `merchant_favorites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `merchant_favorites` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `merchant_id` bigint unsigned NOT NULL COMMENT '商家ID',
  `sku_id` bigint unsigned NOT NULL COMMENT 'SKU ID',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `merchant_favorites_merchant_id_sku_id_unique` (`merchant_id`,`sku_id`),
  KEY `merchant_favorites_sku_id_index` (`sku_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商家收藏商品表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `merchant_favorites`
--

LOCK TABLES `merchant_favorites` WRITE;
/*!40000 ALTER TABLE `merchant_favorites` DISABLE KEYS */;
/*!40000 ALTER TABLE `merchant_favorites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `merchant_sku_visibility`
--

DROP TABLE IF EXISTS `merchant_sku_visibility`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `merchant_sku_visibility` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `merchant_id` bigint unsigned NOT NULL COMMENT '商家ID',
  `sku_id` bigint unsigned NOT NULL COMMENT 'SKU ID',
  `is_visible` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '是否可见：0否，1是',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `merchant_sku_visibility_merchant_id_sku_id_unique` (`merchant_id`,`sku_id`),
  KEY `merchant_sku_visibility_sku_id_index` (`sku_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商家SKU可见性表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `merchant_sku_visibility`
--

LOCK TABLES `merchant_sku_visibility` WRITE;
/*!40000 ALTER TABLE `merchant_sku_visibility` DISABLE KEYS */;
/*!40000 ALTER TABLE `merchant_sku_visibility` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `merchants`
--

DROP TABLE IF EXISTS `merchants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `merchants` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` bigint unsigned DEFAULT NULL COMMENT '关联登录用户ID',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '商家名称',
  `contact_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '联系人',
  `contact_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '联系电话',
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '默认配送地址',
  `delivery_route_id` bigint unsigned DEFAULT NULL COMMENT '所属配送线路ID',
  `delivery_sort` int unsigned NOT NULL DEFAULT '0' COMMENT '配送顺序',
  `min_order_amount` bigint NOT NULL DEFAULT '0' COMMENT '起送价',
  `settlement_type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '结算方式：1现结，2账期，3预付款',
  `credit_limit` bigint NOT NULL DEFAULT '0' COMMENT '信用额度',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：0禁用，1启用',
  `remark` text COLLATE utf8mb4_unicode_ci COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `merchants_user_id_index` (`user_id`),
  KEY `merchants_delivery_route_id_index` (`delivery_route_id`),
  KEY `merchants_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商家表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `merchants`
--

LOCK TABLES `merchants` WRITE;
/*!40000 ALTER TABLE `merchants` DISABLE KEYS */;
INSERT INTO `merchants` VALUES (1,NULL,'味之初餐饮店','吴老板','15800000001','安徽省宿州市埇桥区人民路88号',NULL,0,0,1,0,1,NULL,'2026-07-27 20:30:18','2026-07-27 20:30:18',NULL),(2,NULL,'鲜之味快餐店','郑老板','15800000002','安徽省宿州市埇桥区淮海路120号',NULL,0,0,1,0,1,NULL,'2026-07-27 20:30:18','2026-07-27 20:30:18',NULL),(3,NULL,'家常菜馆','冯老板','15800000003','安徽省宿州市埇桥区汴河路56号',NULL,0,0,1,0,1,NULL,'2026-07-27 20:30:18','2026-07-27 20:30:18',NULL),(4,NULL,'鑫鑫小吃店','蒋老板','15800000004','安徽省宿州市埇桥区银河一路32号',NULL,0,0,1,0,1,NULL,'2026-07-27 20:30:18','2026-07-27 20:30:18',NULL),(5,NULL,'老街坊饭店','韩老板','15800000005','安徽省宿州市埇桥区胜利路18号',NULL,0,0,1,0,1,NULL,'2026-07-27 20:30:18','2026-07-27 20:30:18',NULL);
/*!40000 ALTER TABLE `merchants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2026_07_27_000001_create_users_and_permissions_tables',1),(2,'2026_07_27_000002_create_organization_tables',1),(3,'2026_07_27_000003_create_product_tables',1),(4,'2026_07_27_000004_create_sku_barcodes_suppliers_tables',1),(5,'2026_07_27_000005_create_purchase_tables',1),(6,'2026_07_27_000006_create_order_tables',1),(7,'2026_07_27_000007_create_inventory_tables',1),(8,'2026_07_27_000008_create_loss_tables',1),(9,'2026_07_27_000009_create_picking_tables',1),(10,'2026_07_27_000010_create_delivery_tables',1),(11,'2026_07_27_000011_create_discrepancy_tables',1),(12,'2026_07_27_000012_create_finance_tables',1),(13,'2026_07_27_000013_create_system_tables',1),(14,'2026_07_27_000014_create_wechat_tables',1),(15,'2026_07_27_000015_create_price_strategy_tables',1),(16,'2026_07_27_000016_create_return_tables',1),(17,'2026_07_27_000017_create_price_apportionment_tables',1),(18,'2026_07_27_000018_create_merchant_extension_tables',1),(19,'2026_07_27_000019_create_notification_tables',1),(20,'2026_07_27_000020_create_approval_tables',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL COMMENT '权限ID',
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '模型类型',
  `model_id` bigint unsigned NOT NULL COMMENT '模型ID',
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `idx_model` (`model_id`,`model_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户权限关联表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL COMMENT '角色ID',
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '模型类型',
  `model_id` bigint unsigned NOT NULL COMMENT '模型ID',
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `idx_model` (`model_id`,`model_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户角色关联表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',1),(2,'App\\Models\\User',2),(3,'App\\Models\\User',3),(4,'App\\Models\\User',4),(5,'App\\Models\\User',5),(6,'App\\Models\\User',6),(7,'App\\Models\\User',7),(8,'App\\Models\\User',8),(9,'App\\Models\\User',9);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` bigint unsigned DEFAULT NULL COMMENT '目标用户ID，NULL表示全站广播',
  `merchant_id` bigint unsigned DEFAULT NULL COMMENT '目标商家ID',
  `type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '类型：1系统通知，2订单状态变更，3补货提醒，4库存预警，5账户变动',
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标题',
  `content` text COLLATE utf8mb4_unicode_ci COMMENT '内容',
  `data` json DEFAULT NULL COMMENT '扩展数据',
  `is_read` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '是否已读：0未读，1已读',
  `read_at` timestamp NULL DEFAULT NULL COMMENT '已读时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_user_id_index` (`user_id`),
  KEY `notifications_merchant_id_index` (`merchant_id`),
  KEY `notifications_type_index` (`type`),
  KEY `notifications_is_read_index` (`is_read`),
  KEY `notifications_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='通知/消息表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `operation_logs`
--

DROP TABLE IF EXISTS `operation_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `operation_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` bigint unsigned DEFAULT NULL COMMENT '操作人ID',
  `username` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '操作人用户名',
  `method` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '请求方法',
  `path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '请求路径',
  `ip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'IP地址',
  `content` text COLLATE utf8mb4_unicode_ci COMMENT '操作内容',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `operation_logs_user_id_index` (`user_id`),
  KEY `operation_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='操作日志表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `operation_logs`
--

LOCK TABLES `operation_logs` WRITE;
/*!40000 ALTER TABLE `operation_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `operation_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `order_id` bigint unsigned NOT NULL COMMENT '订单ID',
  `sku_id` bigint unsigned NOT NULL COMMENT 'SKU ID',
  `product_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '商品名称快照',
  `sku_specs` json DEFAULT NULL COMMENT '规格快照',
  `quantity` bigint NOT NULL DEFAULT '0' COMMENT '下单数量',
  `price` bigint NOT NULL DEFAULT '0' COMMENT '下单单价',
  `actual_quantity` bigint NOT NULL DEFAULT '0' COMMENT '实际称重数量',
  `actual_price` bigint NOT NULL DEFAULT '0' COMMENT '实际称重单价',
  `subtotal` bigint NOT NULL DEFAULT '0' COMMENT '小计金额',
  `actual_subtotal` bigint NOT NULL DEFAULT '0' COMMENT '实际小计金额',
  `strategy_price` bigint NOT NULL DEFAULT '0' COMMENT '改价/促销单价',
  `strategy_amount` bigint NOT NULL DEFAULT '0' COMMENT '改价/促销金额',
  `price_strategy_id` bigint unsigned DEFAULT NULL COMMENT '价格策略ID',
  `price_strategy_item_id` bigint unsigned DEFAULT NULL COMMENT '价格策略明细ID',
  `discrepancy_amount` bigint NOT NULL DEFAULT '0' COMMENT '差异金额',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：1正常，2待审核，3已调整',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_index` (`order_id`),
  KEY `order_items_sku_id_index` (`sku_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='订单明细表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_return_items`
--

DROP TABLE IF EXISTS `order_return_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_return_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `order_return_id` bigint unsigned NOT NULL COMMENT '售后退货单ID',
  `order_item_id` bigint unsigned NOT NULL COMMENT '订单明细ID',
  `sku_id` bigint unsigned NOT NULL COMMENT 'SKU ID',
  `quantity` bigint NOT NULL DEFAULT '0' COMMENT '退货数量',
  `price` bigint NOT NULL DEFAULT '0' COMMENT '退货单价',
  `amount` bigint NOT NULL DEFAULT '0' COMMENT '退货金额',
  `refund_amount` bigint NOT NULL DEFAULT '0' COMMENT '实际退款金额',
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '明细原因',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_return_items_order_return_id_index` (`order_return_id`),
  KEY `order_return_items_order_item_id_index` (`order_item_id`),
  KEY `order_return_items_sku_id_index` (`sku_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='售后退货明细表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_return_items`
--

LOCK TABLES `order_return_items` WRITE;
/*!40000 ALTER TABLE `order_return_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_return_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_returns`
--

DROP TABLE IF EXISTS `order_returns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_returns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `return_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '退货单号',
  `order_id` bigint unsigned NOT NULL COMMENT '关联订单ID',
  `merchant_id` bigint unsigned NOT NULL COMMENT '商家ID',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：1待审核，2已审核，3已退货，4退款完成，9取消',
  `total_amount` bigint NOT NULL DEFAULT '0' COMMENT '退货总金额',
  `refund_amount` bigint NOT NULL DEFAULT '0' COMMENT '实际退款金额',
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '退货原因',
  `operator_id` bigint unsigned DEFAULT NULL COMMENT '经办人ID',
  `audited_by` bigint unsigned DEFAULT NULL COMMENT '审核人ID',
  `audited_at` timestamp NULL DEFAULT NULL COMMENT '审核时间',
  `remark` text COLLATE utf8mb4_unicode_ci COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_returns_return_no_unique` (`return_no`),
  KEY `order_returns_order_id_index` (`order_id`),
  KEY `order_returns_merchant_id_index` (`merchant_id`),
  KEY `order_returns_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='售后退货单表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_returns`
--

LOCK TABLES `order_returns` WRITE;
/*!40000 ALTER TABLE `order_returns` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_returns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `order_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '订单号',
  `merchant_id` bigint unsigned NOT NULL COMMENT '商家ID',
  `delivery_route_id` bigint unsigned DEFAULT NULL COMMENT '配送线路ID',
  `batch` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '配送批次：1上午，2下午',
  `delivery_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '配送地址',
  `contact_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '收货联系人',
  `contact_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '收货电话',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：1待拣货，2拣货中，3配送中，4已签收，5已锁定，9已取消',
  `total_amount` bigint NOT NULL DEFAULT '0' COMMENT '原始订单金额',
  `adjusted_amount` bigint NOT NULL DEFAULT '0' COMMENT '调整后金额',
  `final_amount` bigint NOT NULL DEFAULT '0' COMMENT '最终结算金额',
  `payment_status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '支付状态：1未支付，2已支付，3账期',
  `settlement_type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '结算方式：1现结，2账期，3预付款',
  `is_locked` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '是否锁定：0否，1是',
  `remark` text COLLATE utf8mb4_unicode_ci COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_no_unique` (`order_no`),
  KEY `orders_merchant_id_index` (`merchant_id`),
  KEY `orders_delivery_route_id_index` (`delivery_route_id`),
  KEY `orders_status_index` (`status`),
  KEY `orders_batch_index` (`batch`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='订单表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '邮箱',
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '令牌',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='密码重置令牌';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '权限标识',
  `guard_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'web' COMMENT '守卫名称',
  `display_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '权限显示名称',
  `type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '类型：1菜单，2按钮，3接口',
  `parent_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '父级权限ID',
  `route` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '路由/接口标识',
  `sort` int unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '菜单图标',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`),
  KEY `permissions_parent_id_index` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='权限表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'dashboard','web','仪表盘',1,0,'dashboard',0,'layout-dashboard','2026-07-27 20:30:16','2026-07-27 20:30:16'),(2,'product.menu','web','商品管理',1,0,NULL,1,'package','2026-07-27 20:30:16','2026-07-27 20:30:16'),(3,'product.index','web','商品列表',1,0,'products.index',10,NULL,'2026-07-27 20:30:16','2026-07-27 20:30:16'),(4,'order.menu','web','订单管理',1,0,NULL,2,'shopping-cart','2026-07-27 20:30:16','2026-07-27 20:30:16'),(5,'purchase.menu','web','采购管理',1,0,NULL,3,'truck','2026-07-27 20:30:16','2026-07-27 20:30:16'),(6,'finance.menu','web','财务管理',1,0,NULL,4,'banknote','2026-07-27 20:30:16','2026-07-27 20:30:16'),(7,'inventory.menu','web','库存管理',1,0,NULL,5,'warehouse','2026-07-27 20:30:16','2026-07-27 20:30:16'),(8,'delivery.menu','web','物流配送',1,0,NULL,6,'delivery-truck','2026-07-27 20:30:16','2026-07-27 20:30:16'),(9,'organization.menu','web','组织管理',1,0,NULL,7,'building','2026-07-27 20:30:16','2026-07-27 20:30:16'),(10,'system.menu','web','系统管理',1,0,NULL,8,'settings','2026-07-27 20:30:16','2026-07-27 20:30:16');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '模型类型',
  `tokenable_id` bigint unsigned NOT NULL COMMENT '模型ID',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Token名称',
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Token值',
  `abilities` text COLLATE utf8mb4_unicode_ci COMMENT '能力列表',
  `last_used_at` timestamp NULL DEFAULT NULL COMMENT '最后使用时间',
  `expires_at` timestamp NULL DEFAULT NULL COMMENT '过期时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_id_tokenable_type_index` (`tokenable_id`,`tokenable_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sanctum Token表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `picking_task_items`
--

DROP TABLE IF EXISTS `picking_task_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `picking_task_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `picking_task_id` bigint unsigned NOT NULL COMMENT '拣货任务ID',
  `order_id` bigint unsigned NOT NULL COMMENT '订单ID',
  `order_item_id` bigint unsigned NOT NULL COMMENT '订单明细ID',
  `sku_id` bigint unsigned NOT NULL COMMENT 'SKU ID',
  `required_quantity` bigint NOT NULL DEFAULT '0' COMMENT '需求数量',
  `picked_quantity` bigint NOT NULL DEFAULT '0' COMMENT '实际拣货数量',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：1待拣货，2已拣货，3差异',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `picking_task_items_picking_task_id_index` (`picking_task_id`),
  KEY `picking_task_items_order_id_index` (`order_id`),
  KEY `picking_task_items_sku_id_index` (`sku_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='拣货任务明细表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `picking_task_items`
--

LOCK TABLES `picking_task_items` WRITE;
/*!40000 ALTER TABLE `picking_task_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `picking_task_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `picking_tasks`
--

DROP TABLE IF EXISTS `picking_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `picking_tasks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `task_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '任务编号',
  `warehouse_id` bigint unsigned NOT NULL COMMENT '仓库ID',
  `picker_id` bigint unsigned DEFAULT NULL COMMENT '拣货员ID',
  `batch` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '配送批次：1上午，2下午',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：1待分配，2拣货中，3已完成',
  `started_at` timestamp NULL DEFAULT NULL COMMENT '开始时间',
  `completed_at` timestamp NULL DEFAULT NULL COMMENT '完成时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `picking_tasks_task_no_unique` (`task_no`),
  KEY `picking_tasks_warehouse_id_index` (`warehouse_id`),
  KEY `picking_tasks_picker_id_index` (`picker_id`),
  KEY `picking_tasks_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='拣货任务表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `picking_tasks`
--

LOCK TABLES `picking_tasks` WRITE;
/*!40000 ALTER TABLE `picking_tasks` DISABLE KEYS */;
/*!40000 ALTER TABLE `picking_tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `price_apportionments`
--

DROP TABLE IF EXISTS `price_apportionments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `price_apportionments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `target_type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '单据类型：1订单，2采购单',
  `target_id` bigint unsigned NOT NULL COMMENT '单据ID',
  `target_item_id` bigint unsigned DEFAULT NULL COMMENT '单据明细ID',
  `apportion_type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '均摊类型：1整单改价，2促销差价，3费用，4运费',
  `amount` bigint NOT NULL DEFAULT '0' COMMENT '均摊金额',
  `apportion_mode` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '均摊方式：1自动均摊，2手动均摊',
  `manual_amount` bigint NOT NULL DEFAULT '0' COMMENT '手动均摊金额',
  `operator_id` bigint unsigned DEFAULT NULL COMMENT '操作人ID',
  `approval_status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '审核状态：1待审核，2已通过，3已拒绝（手动均摊时有效，自动均摊默认2）',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `price_apportionments_target_type_index` (`target_type`),
  KEY `price_apportionments_target_id_index` (`target_id`),
  KEY `price_apportionments_target_item_id_index` (`target_item_id`),
  KEY `price_apportionments_apportion_type_index` (`apportion_type`),
  KEY `price_apportionments_approval_status_index` (`approval_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='价格/费用均摊记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `price_apportionments`
--

LOCK TABLES `price_apportionments` WRITE;
/*!40000 ALTER TABLE `price_apportionments` DISABLE KEYS */;
/*!40000 ALTER TABLE `price_apportionments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `price_change_logs`
--

DROP TABLE IF EXISTS `price_change_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `price_change_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `source_type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '来源：1促销，2临时改价，3手动改价',
  `source_id` bigint unsigned DEFAULT NULL COMMENT '来源策略ID',
  `target_type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '作用单据：1订单，2采购单，3应收，4应付',
  `target_id` bigint unsigned NOT NULL COMMENT '单据ID',
  `target_item_id` bigint unsigned DEFAULT NULL COMMENT '单据明细ID',
  `original_price` bigint NOT NULL DEFAULT '0' COMMENT '改价前单价',
  `new_price` bigint NOT NULL DEFAULT '0' COMMENT '改价后单价',
  `quantity` bigint NOT NULL DEFAULT '0' COMMENT '数量',
  `amount_diff` bigint NOT NULL DEFAULT '0' COMMENT '金额差异',
  `operator_id` bigint unsigned DEFAULT NULL COMMENT '操作人ID',
  `role_ids` json DEFAULT NULL COMMENT '操作人角色ID数组',
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '改价原因',
  `before_data` json DEFAULT NULL COMMENT '改价前数据',
  `after_data` json DEFAULT NULL COMMENT '改价后数据',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `price_change_logs_source_type_index` (`source_type`),
  KEY `price_change_logs_source_id_index` (`source_id`),
  KEY `price_change_logs_target_type_index` (`target_type`),
  KEY `price_change_logs_target_id_index` (`target_id`),
  KEY `price_change_logs_target_item_id_index` (`target_item_id`),
  KEY `price_change_logs_operator_id_index` (`operator_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='改价/促销记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `price_change_logs`
--

LOCK TABLES `price_change_logs` WRITE;
/*!40000 ALTER TABLE `price_change_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `price_change_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `price_strategies`
--

DROP TABLE IF EXISTS `price_strategies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `price_strategies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '策略名称',
  `code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '策略编码',
  `type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '类型：1促销，2临时改价',
  `target_type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '作用对象：1供应商，2商家，3全部',
  `scope` tinyint unsigned NOT NULL DEFAULT '3' COMMENT '作用范围：1采购，2销售，3通用',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：0禁用，1启用',
  `approval_status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '审核状态：1待审核，2已通过，3已拒绝',
  `start_at` timestamp NULL DEFAULT NULL COMMENT '生效开始时间',
  `end_at` timestamp NULL DEFAULT NULL COMMENT '生效结束时间',
  `created_by` bigint unsigned DEFAULT NULL COMMENT '创建人ID',
  `remark` text COLLATE utf8mb4_unicode_ci COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `price_strategies_code_unique` (`code`),
  KEY `price_strategies_type_index` (`type`),
  KEY `price_strategies_target_type_index` (`target_type`),
  KEY `price_strategies_status_index` (`status`),
  KEY `price_strategies_approval_status_index` (`approval_status`),
  KEY `price_strategies_start_at_end_at_index` (`start_at`,`end_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='价格策略主表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `price_strategies`
--

LOCK TABLES `price_strategies` WRITE;
/*!40000 ALTER TABLE `price_strategies` DISABLE KEYS */;
/*!40000 ALTER TABLE `price_strategies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `price_strategy_items`
--

DROP TABLE IF EXISTS `price_strategy_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `price_strategy_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `price_strategy_id` bigint unsigned NOT NULL COMMENT '价格策略ID',
  `target_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '作用对象ID：supplier_id/merchant_id，0表示全部',
  `category_id` bigint unsigned DEFAULT NULL COMMENT '商品分类ID',
  `product_id` bigint unsigned DEFAULT NULL COMMENT '商品ID',
  `sku_id` bigint unsigned DEFAULT NULL COMMENT 'SKU ID',
  `price_type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '价格类型：1固定价，2折扣率，3成本加权',
  `price_value` bigint NOT NULL DEFAULT '0' COMMENT '固定价格',
  `discount_rate` int NOT NULL DEFAULT '10000' COMMENT '折扣率%',
  `cost_weight_rate` int NOT NULL DEFAULT '10000' COMMENT '成本加权率%',
  `min_quantity` bigint NOT NULL DEFAULT '0' COMMENT '最小起量',
  `effective_start_at` timestamp NULL DEFAULT NULL COMMENT '明细生效开始时间',
  `effective_end_at` timestamp NULL DEFAULT NULL COMMENT '明细生效结束时间',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：0禁用，1启用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `price_strategy_items_price_strategy_id_index` (`price_strategy_id`),
  KEY `price_strategy_items_target_id_index` (`target_id`),
  KEY `price_strategy_items_category_id_index` (`category_id`),
  KEY `price_strategy_items_product_id_index` (`product_id`),
  KEY `price_strategy_items_sku_id_index` (`sku_id`),
  KEY `price_strategy_items_effective_start_at_effective_end_at_index` (`effective_start_at`,`effective_end_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='价格策略明细表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `price_strategy_items`
--

LOCK TABLES `price_strategy_items` WRITE;
/*!40000 ALTER TABLE `price_strategy_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `price_strategy_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_images`
--

DROP TABLE IF EXISTS `product_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_images` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `product_id` bigint unsigned NOT NULL COMMENT '商品ID',
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '图片地址',
  `sort` int unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_images_product_id_index` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品图片表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_images`
--

LOCK TABLES `product_images` WRITE;
/*!40000 ALTER TABLE `product_images` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_tags`
--

DROP TABLE IF EXISTS `product_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_tags` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `product_id` bigint unsigned NOT NULL COMMENT '商品ID',
  `tag_id` bigint unsigned NOT NULL COMMENT '标签ID',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_tags_product_id_tag_id_unique` (`product_id`,`tag_id`),
  KEY `product_tags_tag_id_index` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品标签关联表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_tags`
--

LOCK TABLES `product_tags` WRITE;
/*!40000 ALTER TABLE `product_tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `category_id` bigint unsigned NOT NULL COMMENT '分类ID',
  `supplier_id` bigint unsigned DEFAULT NULL COMMENT '默认供应商ID',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '商品名称',
  `cover` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '封面图',
  `unit` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '单位：斤/箱/份等',
  `is_weight_priced` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '是否称重改价：0否，1是',
  `stock_warning_value` bigint NOT NULL DEFAULT '0' COMMENT '库存预警值',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：0下架，1上架',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT '商品详情',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `products_category_id_index` (`category_id`),
  KEY `products_supplier_id_index` (`supplier_id`),
  KEY `products_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,1,NULL,'大白菜',NULL,'斤',1,0,1,NULL,'2026-07-27 20:30:18','2026-07-27 20:30:18',NULL),(2,1,NULL,'土豆',NULL,'斤',1,0,1,NULL,'2026-07-27 20:30:18','2026-07-27 20:30:18',NULL),(3,1,NULL,'西红柿',NULL,'斤',1,0,1,NULL,'2026-07-27 20:30:18','2026-07-27 20:30:18',NULL),(4,3,NULL,'五花肉',NULL,'斤',1,0,1,NULL,'2026-07-27 20:30:18','2026-07-27 20:30:18',NULL),(5,4,NULL,'鲜虾',NULL,'斤',1,0,1,NULL,'2026-07-27 20:30:18','2026-07-27 20:30:18',NULL),(6,5,NULL,'金龙鱼大豆油',NULL,'桶',0,0,1,NULL,'2026-07-27 20:30:18','2026-07-27 20:30:18',NULL);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promotions`
--

DROP TABLE IF EXISTS `promotions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `promotions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '类型：1主推商品，2主推品类',
  `target_id` bigint unsigned NOT NULL COMMENT '目标ID',
  `sort` int unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `start_at` timestamp NULL DEFAULT NULL COMMENT '开始时间',
  `end_at` timestamp NULL DEFAULT NULL COMMENT '结束时间',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `promotions_type_index` (`type`),
  KEY `promotions_target_id_index` (`target_id`),
  KEY `promotions_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='运营主推表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promotions`
--

LOCK TABLES `promotions` WRITE;
/*!40000 ALTER TABLE `promotions` DISABLE KEYS */;
/*!40000 ALTER TABLE `promotions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_items`
--

DROP TABLE IF EXISTS `purchase_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `sku_id` bigint unsigned NOT NULL COMMENT 'SKU ID',
  `quantity` bigint NOT NULL DEFAULT '0' COMMENT '待采数量',
  `source_type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '来源：1订单汇总，2手工添加',
  `source_id` bigint unsigned DEFAULT NULL COMMENT '来源业务ID',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：1待生成采购单，2已生成采购单',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_items_sku_id_index` (`sku_id`),
  KEY `purchase_items_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='待采清单表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_items`
--

LOCK TABLES `purchase_items` WRITE;
/*!40000 ALTER TABLE `purchase_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_order_items`
--

DROP TABLE IF EXISTS `purchase_order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `purchase_order_id` bigint unsigned NOT NULL COMMENT '采购单ID',
  `sku_id` bigint unsigned NOT NULL COMMENT 'SKU ID',
  `quantity` bigint NOT NULL DEFAULT '0' COMMENT '采购数量',
  `price` bigint NOT NULL DEFAULT '0' COMMENT '采购单价',
  `actual_quantity` bigint NOT NULL DEFAULT '0' COMMENT '实际入库数量',
  `actual_price` bigint NOT NULL DEFAULT '0' COMMENT '实际入库单价',
  `amount` bigint NOT NULL DEFAULT '0' COMMENT '金额',
  `actual_amount` bigint NOT NULL DEFAULT '0' COMMENT '实际金额',
  `strategy_price` bigint NOT NULL DEFAULT '0' COMMENT '改价/促销单价',
  `strategy_amount` bigint NOT NULL DEFAULT '0' COMMENT '改价/促销金额',
  `price_strategy_id` bigint unsigned DEFAULT NULL COMMENT '价格策略ID',
  `price_strategy_item_id` bigint unsigned DEFAULT NULL COMMENT '价格策略明细ID',
  `discrepancy_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '入库差异原因',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_order_items_purchase_order_id_index` (`purchase_order_id`),
  KEY `purchase_order_items_sku_id_index` (`sku_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='采购单明细表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_order_items`
--

LOCK TABLES `purchase_order_items` WRITE;
/*!40000 ALTER TABLE `purchase_order_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_orders`
--

DROP TABLE IF EXISTS `purchase_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `order_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '采购单号',
  `supplier_id` bigint unsigned NOT NULL COMMENT '供应商ID',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：1待接单，2备货中，3已发货，4已入库，5完成，9取消',
  `total_amount` bigint NOT NULL DEFAULT '0' COMMENT '总金额',
  `actual_amount` bigint NOT NULL DEFAULT '0' COMMENT '实际入库金额',
  `remark` text COLLATE utf8mb4_unicode_ci COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchase_orders_order_no_unique` (`order_no`),
  KEY `purchase_orders_supplier_id_index` (`supplier_id`),
  KEY `purchase_orders_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='采购单表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_orders`
--

LOCK TABLES `purchase_orders` WRITE;
/*!40000 ALTER TABLE `purchase_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_return_items`
--

DROP TABLE IF EXISTS `purchase_return_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_return_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `purchase_return_id` bigint unsigned NOT NULL COMMENT '采购退货单ID',
  `purchase_order_item_id` bigint unsigned NOT NULL COMMENT '采购单明细ID',
  `sku_id` bigint unsigned NOT NULL COMMENT 'SKU ID',
  `quantity` bigint NOT NULL DEFAULT '0' COMMENT '退货数量',
  `price` bigint NOT NULL DEFAULT '0' COMMENT '退货单价',
  `amount` bigint NOT NULL DEFAULT '0' COMMENT '退货金额',
  `actual_quantity` bigint NOT NULL DEFAULT '0' COMMENT '实际出库数量',
  `actual_price` bigint NOT NULL DEFAULT '0' COMMENT '实际出库单价',
  `actual_amount` bigint NOT NULL DEFAULT '0' COMMENT '实际出库金额',
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '明细原因',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_return_items_purchase_return_id_index` (`purchase_return_id`),
  KEY `purchase_return_items_purchase_order_item_id_index` (`purchase_order_item_id`),
  KEY `purchase_return_items_sku_id_index` (`sku_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='采购退货明细表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_return_items`
--

LOCK TABLES `purchase_return_items` WRITE;
/*!40000 ALTER TABLE `purchase_return_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_return_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_returns`
--

DROP TABLE IF EXISTS `purchase_returns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_returns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `return_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '退货单号',
  `purchase_order_id` bigint unsigned NOT NULL COMMENT '关联采购单ID',
  `supplier_id` bigint unsigned NOT NULL COMMENT '供应商ID',
  `warehouse_id` bigint unsigned NOT NULL COMMENT '出库仓库ID',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：1待审核，2已审核，3已出库，4完成，9取消',
  `total_amount` bigint NOT NULL DEFAULT '0' COMMENT '退货总金额',
  `actual_amount` bigint NOT NULL DEFAULT '0' COMMENT '实际出库金额',
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '退货原因',
  `operator_id` bigint unsigned DEFAULT NULL COMMENT '经办人ID',
  `audited_by` bigint unsigned DEFAULT NULL COMMENT '审核人ID',
  `audited_at` timestamp NULL DEFAULT NULL COMMENT '审核时间',
  `remark` text COLLATE utf8mb4_unicode_ci COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchase_returns_return_no_unique` (`return_no`),
  KEY `purchase_returns_purchase_order_id_index` (`purchase_order_id`),
  KEY `purchase_returns_supplier_id_index` (`supplier_id`),
  KEY `purchase_returns_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='采购退货单表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_returns`
--

LOCK TABLES `purchase_returns` WRITE;
/*!40000 ALTER TABLE `purchase_returns` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_returns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `receivable_payments`
--

DROP TABLE IF EXISTS `receivable_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `receivable_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `receivable_id` bigint unsigned NOT NULL COMMENT '应收账款ID',
  `amount` bigint NOT NULL DEFAULT '0' COMMENT '本次收款金额',
  `payment_method` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '收款方式：1余额扣款，2微信支付，3线下转账，4后台手工',
  `transaction_no` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '第三方交易号',
  `operator_id` bigint unsigned DEFAULT NULL COMMENT '操作人ID',
  `approval_status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '审核状态：1待审核，2已通过，3已拒绝',
  `evidence_urls` json DEFAULT NULL COMMENT '收款凭证图片数组',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `receivable_payments_receivable_id_index` (`receivable_id`),
  KEY `receivable_payments_payment_method_index` (`payment_method`),
  KEY `receivable_payments_created_at_index` (`created_at`),
  KEY `receivable_payments_approval_status_index` (`approval_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='收款记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `receivable_payments`
--

LOCK TABLES `receivable_payments` WRITE;
/*!40000 ALTER TABLE `receivable_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `receivable_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `receivables`
--

DROP TABLE IF EXISTS `receivables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `receivables` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `receivable_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '应收单号',
  `order_id` bigint unsigned NOT NULL COMMENT '订单ID',
  `merchant_id` bigint unsigned NOT NULL COMMENT '商家ID',
  `original_amount` bigint NOT NULL DEFAULT '0' COMMENT '原始金额',
  `adjusted_amount` bigint NOT NULL DEFAULT '0' COMMENT '调整后金额',
  `discrepancy_amount` bigint NOT NULL DEFAULT '0' COMMENT '差异金额',
  `return_amount` bigint NOT NULL DEFAULT '0' COMMENT '售后退货扣减金额',
  `strategy_discount_amount` bigint NOT NULL DEFAULT '0' COMMENT '改价/促销折扣金额',
  `received_amount` bigint NOT NULL DEFAULT '0' COMMENT '已收金额（多次收款累计）',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：1未结算，2部分收款，3已结清，4争议中，5已办结',
  `settlement_type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '结算方式：1现结，2账期，3预付款',
  `due_date` date DEFAULT NULL COMMENT '到期日',
  `settled_at` timestamp NULL DEFAULT NULL COMMENT '结算时间',
  `closed_at` timestamp NULL DEFAULT NULL COMMENT '办结时间',
  `closed_by` bigint unsigned DEFAULT NULL COMMENT '办结操作人ID',
  `approval_status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '审核状态：1待审核，2已通过，3已拒绝（改价折扣调整时有效）',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `receivables_receivable_no_unique` (`receivable_no`),
  KEY `receivables_order_id_index` (`order_id`),
  KEY `receivables_merchant_id_index` (`merchant_id`),
  KEY `receivables_status_index` (`status`),
  KEY `receivables_approval_status_index` (`approval_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='应收账款表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `receivables`
--

LOCK TABLES `receivables` WRITE;
/*!40000 ALTER TABLE `receivables` DISABLE KEYS */;
/*!40000 ALTER TABLE `receivables` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recharges`
--

DROP TABLE IF EXISTS `recharges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recharges` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `merchant_id` bigint unsigned NOT NULL COMMENT '商家ID',
  `amount` bigint NOT NULL DEFAULT '0' COMMENT '充值金额',
  `payment_method` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '支付方式：1微信支付，2线下转账，3后台手工',
  `transaction_no` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '第三方交易号',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：1待确认，2成功，3失败',
  `approval_status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '审核状态：1待审核，2已通过，3已拒绝',
  `operator_id` bigint unsigned DEFAULT NULL COMMENT '操作人ID',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `recharges_merchant_id_index` (`merchant_id`),
  KEY `recharges_transaction_no_index` (`transaction_no`),
  KEY `recharges_status_index` (`status`),
  KEY `recharges_approval_status_index` (`approval_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='充值记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recharges`
--

LOCK TABLES `recharges` WRITE;
/*!40000 ALTER TABLE `recharges` DISABLE KEYS */;
/*!40000 ALTER TABLE `recharges` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `repurchase_template_items`
--

DROP TABLE IF EXISTS `repurchase_template_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `repurchase_template_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `template_id` bigint unsigned NOT NULL COMMENT '模板ID',
  `sku_id` bigint unsigned NOT NULL COMMENT 'SKU ID',
  `quantity` bigint NOT NULL DEFAULT '0' COMMENT '数量',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `repurchase_template_items_template_id_index` (`template_id`),
  KEY `repurchase_template_items_sku_id_index` (`sku_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='复购模板明细表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `repurchase_template_items`
--

LOCK TABLES `repurchase_template_items` WRITE;
/*!40000 ALTER TABLE `repurchase_template_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `repurchase_template_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `repurchase_templates`
--

DROP TABLE IF EXISTS `repurchase_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `repurchase_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `merchant_id` bigint unsigned NOT NULL COMMENT '商家ID',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '模板名称',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `repurchase_templates_merchant_id_index` (`merchant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='复购模板表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `repurchase_templates`
--

LOCK TABLES `repurchase_templates` WRITE;
/*!40000 ALTER TABLE `repurchase_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `repurchase_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `restock_reminders`
--

DROP TABLE IF EXISTS `restock_reminders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `restock_reminders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `merchant_id` bigint unsigned NOT NULL COMMENT '商家ID',
  `sku_id` bigint unsigned NOT NULL COMMENT 'SKU ID',
  `threshold_quantity` bigint NOT NULL DEFAULT '0' COMMENT '触发提醒的库存阈值',
  `remind_cycle` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '提醒周期：1每日，2每周，3仅一次',
  `last_reminded_at` timestamp NULL DEFAULT NULL COMMENT '上次提醒时间',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：0禁用，1启用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `restock_reminders_merchant_id_sku_id_unique` (`merchant_id`,`sku_id`),
  KEY `restock_reminders_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='智能补货提醒规则表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `restock_reminders`
--

LOCK TABLES `restock_reminders` WRITE;
/*!40000 ALTER TABLE `restock_reminders` DISABLE KEYS */;
/*!40000 ALTER TABLE `restock_reminders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL COMMENT '权限ID',
  `role_id` bigint unsigned NOT NULL COMMENT '角色ID',
  PRIMARY KEY (`permission_id`,`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='角色权限关联表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
INSERT INTO `role_has_permissions` VALUES (1,1),(2,1),(3,1),(4,1),(5,1),(6,1),(7,1),(8,1),(9,1),(10,1);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '角色标识',
  `guard_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'web' COMMENT '守卫名称',
  `display_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '角色显示名称',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '描述',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='角色表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'super_admin','web','超级管理员','全部功能、系统配置、账号管理','2026-07-27 20:30:12','2026-07-27 20:30:12'),(2,'operator','web','运营管理员','商品、订单、商家、供应商管理','2026-07-27 20:30:12','2026-07-27 20:30:12'),(3,'operator_manager','web','运营经理','运营审核、商品/订单/价格策略审核确认','2026-07-27 20:30:12','2026-07-27 20:30:12'),(4,'finance','web','财务人员','应收、结算、发票、审计','2026-07-27 20:30:12','2026-07-27 20:30:12'),(5,'cashier','web','出纳','付款录入、收款录入、资金操作执行','2026-07-27 20:30:12','2026-07-27 20:30:12'),(6,'finance_manager','web','财务经理','财务审核、付款/收款/结算单据复核确认','2026-07-27 20:30:12','2026-07-27 20:30:12'),(7,'picker','web','拣货员','拣货任务、称重改价','2026-07-27 20:30:12','2026-07-27 20:30:12'),(8,'driver','web','配送司机','配送任务、轨迹、签收','2026-07-27 20:30:12','2026-07-27 20:30:12'),(9,'merchant','web','商家','小程序商家端','2026-07-27 20:30:12','2026-07-27 20:30:12');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settlement_payments`
--

DROP TABLE IF EXISTS `settlement_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settlement_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `settlement_id` bigint unsigned NOT NULL COMMENT '供应商结算单ID',
  `amount` bigint NOT NULL DEFAULT '0' COMMENT '本次付款金额',
  `payment_method` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '付款方式：1银行转账，2线下现金，3后台手工',
  `transaction_no` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '第三方交易号',
  `operator_id` bigint unsigned DEFAULT NULL COMMENT '操作人ID',
  `approval_status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '审核状态：1待审核，2已通过，3已拒绝',
  `evidence_urls` json DEFAULT NULL COMMENT '付款凭证图片数组',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `settlement_payments_settlement_id_index` (`settlement_id`),
  KEY `settlement_payments_payment_method_index` (`payment_method`),
  KEY `settlement_payments_created_at_index` (`created_at`),
  KEY `settlement_payments_approval_status_index` (`approval_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='付款记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settlement_payments`
--

LOCK TABLES `settlement_payments` WRITE;
/*!40000 ALTER TABLE `settlement_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `settlement_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `signatures`
--

DROP TABLE IF EXISTS `signatures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `signatures` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `order_id` bigint unsigned NOT NULL COMMENT '订单ID',
  `delivery_task_id` bigint unsigned NOT NULL COMMENT '配送任务ID',
  `type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '类型：1拍照签收，2电子签名，3质检照片',
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '图片/签名文件地址',
  `signer_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '签收人',
  `signed_at` timestamp NULL DEFAULT NULL COMMENT '签收时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `signatures_order_id_index` (`order_id`),
  KEY `signatures_delivery_task_id_index` (`delivery_task_id`),
  KEY `signatures_type_index` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='签收存证表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `signatures`
--

LOCK TABLES `signatures` WRITE;
/*!40000 ALTER TABLE `signatures` DISABLE KEYS */;
/*!40000 ALTER TABLE `signatures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sku_barcodes`
--

DROP TABLE IF EXISTS `sku_barcodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sku_barcodes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `sku_id` bigint unsigned NOT NULL COMMENT 'SKU ID',
  `supplier_id` bigint unsigned DEFAULT NULL COMMENT '供应商ID，供应商条码时必填',
  `barcode_type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '条码类型：1厂家条码，2供应商条码，3内部条码，4备用条码',
  `barcode_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '条码值',
  `is_default` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '是否默认条码：0否，1是',
  `is_enabled` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '是否启用：0禁用，1启用',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_sku_supplier_type_code` (`sku_id`,`supplier_id`,`barcode_type`,`barcode_code`),
  KEY `sku_barcodes_sku_id_index` (`sku_id`),
  KEY `sku_barcodes_supplier_id_index` (`supplier_id`),
  KEY `sku_barcodes_barcode_type_index` (`barcode_type`),
  KEY `sku_barcodes_barcode_code_index` (`barcode_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='SKU条码表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sku_barcodes`
--

LOCK TABLES `sku_barcodes` WRITE;
/*!40000 ALTER TABLE `sku_barcodes` DISABLE KEYS */;
/*!40000 ALTER TABLE `sku_barcodes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sku_suppliers`
--

DROP TABLE IF EXISTS `sku_suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sku_suppliers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `sku_id` bigint unsigned NOT NULL COMMENT 'SKU ID',
  `supplier_id` bigint unsigned NOT NULL COMMENT '供应商ID',
  `is_default` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '是否默认供应商：0否，1是',
  `purchase_price` bigint NOT NULL DEFAULT '0' COMMENT '该供应商采购参考价',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '是否启用：0禁用，1启用',
  `sort` int unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_sku_supplier` (`sku_id`,`supplier_id`),
  KEY `sku_suppliers_supplier_id_index` (`supplier_id`),
  KEY `sku_suppliers_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='SKU供应商关联表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sku_suppliers`
--

LOCK TABLES `sku_suppliers` WRITE;
/*!40000 ALTER TABLE `sku_suppliers` DISABLE KEYS */;
/*!40000 ALTER TABLE `sku_suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `skus`
--

DROP TABLE IF EXISTS `skus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `skus` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `product_id` bigint unsigned NOT NULL COMMENT '商品ID',
  `sku_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'SKU编码',
  `specs` json DEFAULT NULL COMMENT '规格属性',
  `purchase_price` bigint NOT NULL DEFAULT '0' COMMENT '采购参考价',
  `wholesale_price` bigint NOT NULL DEFAULT '0' COMMENT '批发销售价',
  `cost_price` bigint NOT NULL DEFAULT '0' COMMENT '财务成本价',
  `stock` bigint NOT NULL DEFAULT '0' COMMENT '当前库存冗余字段',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：0禁用，1启用',
  `approval_status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '审核状态：1待审核，2已通过，3已拒绝',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `skus_sku_code_unique` (`sku_code`),
  KEY `skus_product_id_index` (`product_id`),
  KEY `skus_status_index` (`status`),
  KEY `skus_approval_status_index` (`approval_status`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='SKU规格表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `skus`
--

LOCK TABLES `skus` WRITE;
/*!40000 ALTER TABLE `skus` DISABLE KEYS */;
INSERT INTO `skus` VALUES (1,1,'SKU-0001','[{\"label\": \"规格\", \"value\": \"斤\"}]',8000,9200,0,0,1,2,'2026-07-27 20:30:18','2026-07-27 20:30:18',NULL),(2,2,'SKU-0002','[{\"label\": \"规格\", \"value\": \"斤\"}]',12000,13799,0,0,1,2,'2026-07-27 20:30:18','2026-07-27 20:30:18',NULL),(3,3,'SKU-0003','[{\"label\": \"规格\", \"value\": \"斤\"}]',25000,28749,0,0,1,2,'2026-07-27 20:30:18','2026-07-27 20:30:18',NULL),(4,4,'SKU-0004','[{\"label\": \"规格\", \"value\": \"斤\"}]',130000,149500,0,0,1,2,'2026-07-27 20:30:18','2026-07-27 20:30:18',NULL),(5,5,'SKU-0005','[{\"label\": \"规格\", \"value\": \"斤\"}]',350000,402499,0,0,1,2,'2026-07-27 20:30:18','2026-07-27 20:30:18',NULL),(6,6,'SKU-0006','[{\"label\": \"规格\", \"value\": \"桶\"}]',450000,517499,0,0,1,2,'2026-07-27 20:30:18','2026-07-27 20:30:18',NULL);
/*!40000 ALTER TABLE `skus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supplier_settlement_items`
--

DROP TABLE IF EXISTS `supplier_settlement_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_settlement_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `supplier_settlement_id` bigint unsigned NOT NULL COMMENT '结算单ID',
  `purchase_order_id` bigint unsigned NOT NULL COMMENT '采购单ID',
  `amount` bigint NOT NULL DEFAULT '0' COMMENT '金额',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `supplier_settlement_items_supplier_settlement_id_index` (`supplier_settlement_id`),
  KEY `supplier_settlement_items_purchase_order_id_index` (`purchase_order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='结算单明细表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier_settlement_items`
--

LOCK TABLES `supplier_settlement_items` WRITE;
/*!40000 ALTER TABLE `supplier_settlement_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `supplier_settlement_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supplier_settlements`
--

DROP TABLE IF EXISTS `supplier_settlements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_settlements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `settlement_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '结算单号',
  `supplier_id` bigint unsigned NOT NULL COMMENT '供应商ID',
  `start_date` date NOT NULL COMMENT '结算周期开始',
  `end_date` date NOT NULL COMMENT '结算周期结束',
  `total_amount` bigint NOT NULL DEFAULT '0' COMMENT '汇总金额',
  `service_fee` bigint NOT NULL DEFAULT '0' COMMENT '服务费',
  `payable_amount` bigint NOT NULL DEFAULT '0' COMMENT '应付金额',
  `return_amount` bigint NOT NULL DEFAULT '0' COMMENT '采购退货扣减金额',
  `paid_amount` bigint NOT NULL DEFAULT '0' COMMENT '已付金额（多次付款累计）',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：1待结算，2部分付款，3已结清，4已办结',
  `settled_at` timestamp NULL DEFAULT NULL COMMENT '结算时间',
  `closed_at` timestamp NULL DEFAULT NULL COMMENT '办结时间',
  `closed_by` bigint unsigned DEFAULT NULL COMMENT '办结操作人ID',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `supplier_settlements_settlement_no_unique` (`settlement_no`),
  KEY `supplier_settlements_supplier_id_index` (`supplier_id`),
  KEY `supplier_settlements_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='供应商结算单表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier_settlements`
--

LOCK TABLES `supplier_settlements` WRITE;
/*!40000 ALTER TABLE `supplier_settlements` DISABLE KEYS */;
/*!40000 ALTER TABLE `supplier_settlements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suppliers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '供应商名称',
  `contact_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '联系人',
  `contact_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '联系电话',
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '地址',
  `bank_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '开户行',
  `bank_account` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '银行账号',
  `settlement_cycle` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '结算周期：1周结，2月结，3不定期',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：0禁用，1启用',
  `remark` text COLLATE utf8mb4_unicode_ci COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `suppliers_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='供应商表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES (1,'鲜源农业有限公司','陈供应','13900000001','安徽省宿州市埇桥区农批市场A1',NULL,NULL,1,1,NULL,'2026-07-27 20:30:18','2026-07-27 20:30:18',NULL),(2,'绿野蔬菜种植基地','李蔬菜','13900000002','安徽省宿州市埇桥区农批市场B3',NULL,NULL,1,1,NULL,'2026-07-27 20:30:18','2026-07-27 20:30:18',NULL),(3,'丰润肉业有限公司','王肉业','13900000003','安徽省宿州市埇桥区肉联厂C2',NULL,NULL,1,1,NULL,'2026-07-27 20:30:18','2026-07-27 20:30:18',NULL),(4,'海滨水产批发部','赵水产','13900000004','安徽省宿州市埇桥区水产市场D5',NULL,NULL,1,1,NULL,'2026-07-27 20:30:18','2026-07-27 20:30:18',NULL),(5,'恒达粮油贸易公司','钱粮油','13900000005','安徽省宿州市埇桥区粮批市场E1',NULL,NULL,1,1,NULL,'2026-07-27 20:30:18','2026-07-27 20:30:18',NULL);
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_configs`
--

DROP TABLE IF EXISTS `system_configs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_configs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `config_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '配置键',
  `config_value` text COLLATE utf8mb4_unicode_ci COMMENT '配置值',
  `default_value` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '默认值（重置用）',
  `config_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string' COMMENT '值类型：boolean/integer/decimal/string/enum/json',
  `config_group` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'basic' COMMENT '分组：basic/order/delivery/finance/inventory/audit/ui',
  `label` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '中文显示名',
  `hint` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '输入提示',
  `options` json DEFAULT NULL COMMENT '枚举选项 [{"label":"选项名","value":"值"},...]',
  `validation_rules` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '校验规则：min:0|max:999|required|integer',
  `sort_order` int NOT NULL DEFAULT '0' COMMENT '组内排序',
  `is_public` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否前端可读（无需登录）',
  `is_readonly` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否只读（代码写入，不允许管理后台改）',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '说明',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `system_configs_config_key_unique` (`config_key`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='系统配置表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_configs`
--

LOCK TABLES `system_configs` WRITE;
/*!40000 ALTER TABLE `system_configs` DISABLE KEYS */;
INSERT INTO `system_configs` VALUES (1,'site_name','本地速送服务平台','本地速送服务平台','string','basic','站点名称',NULL,NULL,'required|max:50',1,0,0,'站点名称','2026-07-27 20:30:15','2026-07-27 20:30:15'),(2,'contact_phone','15690631151','15690631151','string','basic','客服电话',NULL,NULL,'required|max:20',2,0,0,'客服电话','2026-07-27 20:30:15','2026-07-27 20:30:15'),(3,'max_upload_size_mb','20','20','integer','basic','文件上传大小限制（MB）','单文件上传最大体积',NULL,'required|integer|min:1|max:100',6,0,0,'管理后台和商家端文件上传限制','2026-07-27 20:30:15','2026-07-27 20:30:15'),(4,'site_icp_number','豫ICP备19036797号-1','','string','basic','ICP 备案号','网站 ICP 备案号，留空不显示',NULL,'max:50',7,1,0,'显示在页面底部的 ICP 备案号','2026-07-27 20:30:15','2026-07-27 22:04:41'),(5,'site_tech_stack_url','https://laravel.com','https://laravel.com','string','basic','技术栈链接','底部版权栏\"技术栈\"文字的跳转链接',NULL,'url|max:255',8,1,0,'点击底部版权栏中的技术栈文字时跳转的 URL','2026-07-27 20:30:15','2026-07-27 20:30:15'),(6,'site_developer_name','Seeding','Seeding','string','basic','开发者名称','底部版权栏显示的开发者名称',NULL,'max:50',9,1,0,'显示在页面底部版权栏中的开发者名称','2026-07-27 20:30:15','2026-07-27 20:30:15'),(7,'site_developer_url','https://www.ihopeso.cn','','string','basic','开发者链接','底部版权栏\"开发者名称\"的跳转链接，留空不可点击',NULL,'nullable|url|max:255',10,1,0,'点击底部版权栏中的开发者名称时跳转的 URL','2026-07-27 20:30:15','2026-07-27 22:03:46'),(8,'site_icp_url','https://beian.miit.gov.cn/','https://beian.miit.gov.cn/','string','basic','备案号链接','底部版权栏\"ICP备案号\"的跳转链接',NULL,'url|max:255',11,1,0,'点击底部版权栏中的备案号时跳转的 URL','2026-07-27 20:30:15','2026-07-27 20:30:15'),(9,'order_auto_confirm_hours','24','24','integer','order','自动确认收货时长（小时）','超过此时长未签收将自动确认',NULL,'required|integer|min:1|max:168',3,0,0,'订单配送完成后的自动签收等待时长','2026-07-27 20:30:15','2026-07-27 20:30:15'),(10,'min_delivery_amount','0','0','integer','order','最低起送金额（元）','0表示无限制',NULL,'required|integer|min:0',4,1,0,'商家下单金额门槛','2026-07-27 20:30:15','2026-07-27 20:30:15'),(11,'allow_merchant_self_order','1','1','boolean','order','允许商家自助下单','关闭后商家只能由运营代下单',NULL,'required|boolean',5,1,0,'商家端小程序是否允许自主下单','2026-07-27 20:30:15','2026-07-27 20:30:15'),(12,'default_delivery_batch','1','1','enum','delivery','默认配送批次',NULL,'[{\"label\": \"上午\", \"value\": \"1\"}, {\"label\": \"下午\", \"value\": \"2\"}]',NULL,10,0,0,'默认配送批次：1上午，2下午','2026-07-27 20:30:15','2026-07-27 20:30:15'),(13,'delivery_timeout_minutes','30','30','integer','delivery','配送超时标记时长（分钟）','超过此时长未完成配送将标记为异常',NULL,'required|integer|min:10|max:180',11,0,0,'配送任务超时自动标记异常','2026-07-27 20:30:15','2026-07-27 20:30:15'),(14,'allow_driver_multi_task','1','1','boolean','delivery','允许司机同时接多单','关闭后司机同时只能执行一个配送任务',NULL,'required|boolean',12,0,0,'司机并发配送开关','2026-07-27 20:30:15','2026-07-27 20:30:15'),(15,'max_daily_recharge_amount','50000','50000','integer','finance','单日最大充值金额（元）','单商家每日充值累计上限',NULL,'required|integer|min:1000',20,1,0,'商家充值风控限额','2026-07-27 20:30:15','2026-07-27 20:30:15'),(16,'credit_limit_default','5000','5000','integer','finance','新商家默认信用额度（元）','新注册商家自动分配的信用额度',NULL,'required|integer|min:0',21,0,0,'新商家初始信用额度','2026-07-27 20:30:15','2026-07-27 20:30:15'),(17,'enable_weighing_auto_debit','0','0','boolean','finance','称重差异自动扣款','开启后称重差异在阈值内自动扣款，无需人工确认',NULL,'required|boolean',22,0,0,'称重差异处理方式','2026-07-27 20:30:15','2026-07-27 20:30:15'),(18,'weighing_diff_threshold','20','20','integer','inventory','称重差异阈值（%）','称重差异超过此百分比需人工确认',NULL,'required|integer|min:1|max:100',20,0,0,'称重差异阈值（百分比）','2026-07-27 20:30:15','2026-07-27 20:30:15'),(19,'inventory_warning_enabled','1','1','boolean','inventory','启用库存预警','开启后低于预警值触发通知',NULL,'required|boolean',30,0,0,'库存预警检测开关','2026-07-27 20:30:15','2026-07-27 20:30:15'),(20,'inventory_warning_interval_minutes','5','5','integer','inventory','库存预警检测频率（分钟）','定时任务检测间隔',NULL,'required|integer|min:1|max:60',31,0,0,'库存预警定时检测周期','2026-07-27 20:30:15','2026-07-27 20:30:15'),(21,'audit_retention_days','90','90','integer','audit','审计日志保留天数','0=永久保留，1-180天，到期每日定时清理',NULL,'required|integer|min:0|max:180',50,0,0,'审计/日志保留天数','2026-07-27 20:30:15','2026-07-27 20:30:15'),(22,'loss_approval_threshold','200','200','integer','audit','损耗审批阈值（元）','单张损耗单金额超过此值需运营经理审核',NULL,'required|integer|min:0',51,0,0,'损耗审批阈值（元）','2026-07-27 20:30:15','2026-07-27 20:30:15'),(23,'ui_close_on_outside','1','1','boolean','ui','点击旁边关闭通知','开启后，点击通知面板外的区域将自动关闭通知菜单',NULL,NULL,1,1,0,'控制点击通知 Drawer 外部区域时是否自动关闭面板','2026-07-27 20:30:15','2026-07-28 18:12:01');
/*!40000 ALTER TABLE `system_configs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tags` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标签名称',
  `sort` int unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tags_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='标签词库表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tags`
--

LOCK TABLES `tags` WRITE;
/*!40000 ALTER TABLE `tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `temperatures`
--

DROP TABLE IF EXISTS `temperatures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `temperatures` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `delivery_task_id` bigint unsigned NOT NULL COMMENT '配送任务ID',
  `temperature` int NOT NULL DEFAULT '0' COMMENT '温度值',
  `recorded_at` timestamp NULL DEFAULT NULL COMMENT '记录时间',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `temperatures_delivery_task_id_index` (`delivery_task_id`),
  KEY `temperatures_recorded_at_index` (`recorded_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='冷链温度记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `temperatures`
--

LOCK TABLES `temperatures` WRITE;
/*!40000 ALTER TABLE `temperatures` DISABLE KEYS */;
/*!40000 ALTER TABLE `temperatures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户名',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'bcrypt加密密码',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '姓名',
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '手机号',
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '邮箱',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '头像',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：0禁用，1启用',
  `last_login_at` timestamp NULL DEFAULT NULL COMMENT '最后登录时间',
  `email_verified_at` timestamp NULL DEFAULT NULL COMMENT '邮箱验证时间',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '记住我令牌',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_phone_unique` (`phone`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'seeding','$2y$12$jSKTOWPifzZvdqRR8TEpWeEgfMzKP37heVgVNwfuTSJTrFDRvEs9G','系统管理员','15690631151','seeding@ihopeso.cn',NULL,1,NULL,NULL,'LlRra5OgE71NotMwINotkk1PYxK5fAhYmprsYcmEsAuXFOp2yCQeNdUSYdaY','2026-07-27 20:30:12','2026-07-27 20:30:12',NULL),(2,'operator1','$2y$12$f3IENqeZzZYJtFYgeCgwjuM/ROJDp5k5rgWYYTrveaj6cd5h6G0ty','张运营','13800000001','operator@susong.test',NULL,1,NULL,NULL,NULL,'2026-07-27 20:30:16','2026-07-27 20:30:16',NULL),(3,'ops_manager','$2y$12$js2yJUgIRhQxVAkt.BKS8uYywoA9LFEbk4cRfaVFoAGtvS7DnDTLi','李运营经理','13800000002','ops_manager@susong.test',NULL,1,NULL,NULL,NULL,'2026-07-27 20:30:16','2026-07-27 20:30:16',NULL),(4,'finance1','$2y$12$.WKXhkV6lPXUsMYULD3pquDMcbDTdzyKfuE6RgbmLQO1BZlNbjFd6','王财务','13800000003','finance@susong.test',NULL,1,NULL,NULL,NULL,'2026-07-27 20:30:16','2026-07-27 20:30:16',NULL),(5,'cashier1','$2y$12$nLX8vKdHY2yJriH.lB3vAecXilfyRaPQgtS/ysDnx9OBLbYmFa0I6','赵出纳','13800000004','cashier@susong.test',NULL,1,NULL,NULL,NULL,'2026-07-27 20:30:16','2026-07-27 20:30:16',NULL),(6,'fin_manager','$2y$12$wPavuoVV.TU2QyYl1ExQ..m5Q4n3dHYh0PfYgnayl6I1CyP0kNZHW','钱财务经理','13800000005','finance_manager@susong.test',NULL,1,NULL,NULL,NULL,'2026-07-27 20:30:16','2026-07-27 20:30:16',NULL),(7,'picker1','$2y$12$svolnqEJwEPrS6cdNlfjtuz5XaPnbTNziMZlBDP2Efc3APNCgsaKO','孙拣货员','13800000006','picker@susong.test',NULL,1,NULL,NULL,NULL,'2026-07-27 20:30:16','2026-07-27 20:30:16',NULL),(8,'driver1','$2y$12$x/WqS8/ZdTAXolv6bQaWQOIRgcRBfIL2GbCupaBA62ilBvFknwRTK','周司机','13800000007','driver@susong.test',NULL,1,NULL,NULL,NULL,'2026-07-27 20:30:16','2026-07-27 20:30:16',NULL),(9,'merchant1','$2y$12$8DH5pLSBo1P7/ojaJZ9QpOuiPcHz1viX5spAj4pA7VFNm8vxiEOme','吴商家','13800000008','merchant@susong.test',NULL,1,NULL,NULL,NULL,'2026-07-27 20:30:16','2026-07-27 20:30:16',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicles`
--

DROP TABLE IF EXISTS `vehicles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehicles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `plate_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '车牌号',
  `vehicle_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '车辆类型',
  `is_cold_chain` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '是否冷链：0否，1是',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：0禁用，1启用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vehicles_plate_number_unique` (`plate_number`),
  KEY `vehicles_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='车辆表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicles`
--

LOCK TABLES `vehicles` WRITE;
/*!40000 ALTER TABLE `vehicles` DISABLE KEYS */;
INSERT INTO `vehicles` VALUES (1,'皖LT0001','1',0,1,'2026-07-27 20:30:18','2026-07-27 20:30:18',NULL),(2,'皖LT0002','1',0,1,'2026-07-27 20:30:18','2026-07-27 20:30:18',NULL);
/*!40000 ALTER TABLE `vehicles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `warehouses`
--

DROP TABLE IF EXISTS `warehouses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `warehouses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '仓库名称',
  `type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '类型：1总仓，2前置仓',
  `is_cold_chain` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '是否冷链：0否，1是',
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '地址',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `warehouses_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='仓库表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `warehouses`
--

LOCK TABLES `warehouses` WRITE;
/*!40000 ALTER TABLE `warehouses` DISABLE KEYS */;
INSERT INTO `warehouses` VALUES (1,'总仓-农批市场',1,0,'安徽省宿州市埇桥区农批市场内',1,'2026-07-27 20:30:18','2026-07-27 20:30:18',NULL),(2,'分仓-肉联厂',2,1,'安徽省宿州市埇桥区肉联厂内',1,'2026-07-27 20:30:18','2026-07-27 20:30:18',NULL);
/*!40000 ALTER TABLE `warehouses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wechat_users`
--

DROP TABLE IF EXISTS `wechat_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wechat_users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` bigint unsigned DEFAULT NULL COMMENT '关联系统用户ID',
  `openid` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '微信OpenID',
  `unionid` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '微信UnionID',
  `nickname` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '昵称',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '头像',
  `type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '类型：1商家端，2司机端',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `wechat_users_openid_unique` (`openid`),
  KEY `wechat_users_user_id_index` (`user_id`),
  KEY `wechat_users_type_index` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='微信用户绑定表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wechat_users`
--

LOCK TABLES `wechat_users` WRITE;
/*!40000 ALTER TABLE `wechat_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `wechat_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'livewire'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-29 10:15:01
