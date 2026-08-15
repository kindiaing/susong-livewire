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
INSERT INTO `approval_type_configs` VALUES (1,'manual_recharge','后台手工充值','财务对账','P0',1,2,6,1,'运营专员为商家手动充值','2026-08-12 21:05:32','2026-08-12 21:05:32'),(2,'supplier_payment','供应商付款录入','财务对账','P0',1,5,6,2,'出纳录入供应商付款记录','2026-08-12 21:05:32','2026-08-12 21:05:32'),(3,'customer_receipt','客户收款录入','财务对账','P0',1,5,6,3,'出纳录入客户收款记录','2026-08-12 21:05:32','2026-08-12 21:05:32'),(4,'credit_limit','信用额度调整','商家管理','P0',1,2,6,4,'修改商家信用额度','2026-08-12 21:05:32','2026-08-12 21:05:32'),(5,'price_strategy','价格策略创建/修改','价格策略','P0',1,2,6,5,'创建或修改促销/临时改价策略','2026-08-12 21:05:32','2026-08-12 21:05:32'),(6,'manual_apportion','手动均摊调整','费用均摊','P0',1,4,3,6,'手动修改费用均摊金额','2026-08-12 21:05:32','2026-08-12 21:05:32'),(7,'diff_refund_deduct','差异退款/扣款决策','差异处理','P0',1,2,6,7,'差异处理决策为退款或扣款','2026-08-12 21:05:32','2026-08-12 21:05:32'),(8,'sku_price_change','SKU 批发价修改(>15%)','商品管理','P1',1,2,3,8,'修改SKU批发销售价幅度>15%','2026-08-12 21:05:32','2026-08-12 21:05:32'),(9,'receivable_adjust','应收改价折扣调整','财务对账','P0',1,2,6,9,'改价/促销导致应收金额调整','2026-08-12 21:05:32','2026-08-12 21:05:32'),(10,'recharge_confirm','商家充值确认','财务对账','P0',1,2,6,10,'商家微信/线下充值待确认','2026-08-12 21:05:32','2026-08-12 21:05:32'),(11,'purchase_return','采购退货','平台统采','P0',0,2,6,11,'采购退货审批','2026-08-12 21:05:32','2026-08-12 21:05:32'),(12,'after_sale_return','售后退货退款','客户直采','P0',0,2,6,12,'售后退货退款审批','2026-08-12 21:05:32','2026-08-12 21:05:32'),(13,'auth_correction','单据授权更正','财务对账','P0',0,4,6,13,'解锁已锁定数据允许更正','2026-08-12 21:05:32','2026-08-12 21:05:32'),(14,'weighing_price','称重改价(≤20%)','客户直采','P1',0,7,3,14,'称重改价金额生效','2026-08-12 21:05:32','2026-08-12 21:05:32'),(15,'purchase_warehouse','采购入库确认','平台统采','P1',0,2,3,15,'入库确认触发库存联动','2026-08-12 21:05:32','2026-08-12 21:05:32'),(16,'supplier_bank_edit','供应商银行信息修改','组织主体','P1',0,2,6,16,'银行收付款信息生效','2026-08-12 21:05:32','2026-08-12 21:05:32'),(17,'manual_close','手动办结','财务对账','P1',0,4,6,17,'单据办结锁定','2026-08-12 21:05:32','2026-08-12 21:05:32'),(18,'sku_price_minor','SKU小幅改价(≤15%)','商品管理','P1',0,2,3,18,'小幅改价生效','2026-08-12 21:05:32','2026-08-12 21:05:32'),(19,'loss_order','损耗单审批','损耗管理','P1',1,2,3,19,'损耗金额超过审批阈值时需运营经理审核','2026-08-12 21:05:32','2026-08-12 21:05:32');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='审批记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `approvals`
--

LOCK TABLES `approvals` WRITE;
/*!40000 ALTER TABLE `approvals` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='审计日志表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (1,'App\\Models\\PurchaseOrder',3,'submit','{\"status\": 1, \"status_label\": \"待接单\"}','{\"status\": 2, \"status_label\": \"备货中\"}',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0',NULL,'purchase_order',3,'2026-08-12 22:15:28'),(2,'App\\Models\\PurchaseOrder',4,'submit','{\"status\": 1, \"status_label\": \"待接单\"}','{\"status\": 2, \"status_label\": \"备货中\"}',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0',NULL,'purchase_order',4,'2026-08-12 22:15:40'),(3,'App\\Models\\PurchaseOrder',4,'ship','{\"status\": 2, \"status_label\": \"备货中\"}','{\"status\": 3, \"status_label\": \"已发货\"}',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0',NULL,'purchase_order',4,'2026-08-12 22:15:45'),(4,'App\\Models\\PurchaseOrder',3,'ship','{\"status\": 2, \"status_label\": \"备货中\"}','{\"status\": 3, \"status_label\": \"已发货\"}',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0',NULL,'purchase_order',3,'2026-08-12 22:15:54'),(5,'App\\Models\\PurchaseOrder',3,'stock_in','{\"status\": 3, \"status_label\": \"已发货\"}','{\"status\": 4, \"status_label\": \"已入库\"}',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0',NULL,'purchase_order',3,'2026-08-12 22:16:06'),(6,'App\\Models\\PurchaseOrder',3,'complete','{\"status\": 4, \"status_label\": \"已入库\"}','{\"status\": 5, \"status_label\": \"完成\"}',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0',NULL,'purchase_order',3,'2026-08-12 22:16:12'),(7,'App\\Models\\PurchaseOrder',4,'stock_in','{\"status\": 3, \"status_label\": \"已发货\"}','{\"status\": 4, \"status_label\": \"已入库\"}',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0',NULL,'purchase_order',4,'2026-08-12 22:16:25'),(8,'App\\Models\\PurchaseOrder',4,'complete','{\"status\": 4, \"status_label\": \"已入库\"}','{\"status\": 5, \"status_label\": \"完成\"}',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0',NULL,'purchase_order',4,'2026-08-12 22:16:29');
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='轮播广告表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banners`
--

LOCK TABLES `banners` WRITE;
/*!40000 ALTER TABLE `banners` DISABLE KEYS */;
INSERT INTO `banners` VALUES (1,'新鲜蔬菜每日直达','/uploads/banners/veg-fresh.jpg','/products?category=1',1,1,'2026-08-12 21:11:51','2026-08-12 21:11:51',NULL),(2,'海鲜专区限时特惠','/uploads/banners/seafood-sale.jpg','/products?category=4',2,1,'2026-08-12 21:11:51','2026-08-12 21:11:51',NULL),(3,'新用户充值满赠','/uploads/banners/recharge-gift.jpg','/recharges',3,1,'2026-08-12 21:11:51','2026-08-12 21:11:51',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='购物车明细表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart_items`
--

LOCK TABLES `cart_items` WRITE;
/*!40000 ALTER TABLE `cart_items` DISABLE KEYS */;
INSERT INTO `cart_items` VALUES (1,1,1,2000,9200,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(2,1,2,1000,13800,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(3,2,4,500,149500,'2026-08-12 21:11:50','2026-08-12 21:11:50');
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='购物车表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carts`
--

LOCK TABLES `carts` WRITE;
/*!40000 ALTER TABLE `carts` DISABLE KEYS */;
INSERT INTO `carts` VALUES (1,1,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(2,2,'2026-08-12 21:11:50','2026-08-12 21:11:50');
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
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品分类表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,0,'蔬菜',NULL,1,1,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(2,0,'水果',NULL,2,1,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(3,0,'肉类',NULL,3,1,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(4,0,'水产',NULL,4,1,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(5,0,'粮油',NULL,5,1,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(6,0,'调料',NULL,6,1,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(7,0,'豆制品',NULL,7,1,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(8,0,'冷冻食品',NULL,8,1,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(9,1,'叶菜类',NULL,1,1,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(10,1,'根茎类',NULL,2,1,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(11,1,'瓜果类',NULL,3,1,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(12,1,'菌菇类',NULL,4,1,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(13,2,'热带水果',NULL,1,1,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(14,2,'温带水果',NULL,2,1,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(15,2,'浆果类',NULL,3,1,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(16,3,'猪肉',NULL,1,1,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(17,3,'牛肉',NULL,2,1,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(18,3,'羊肉',NULL,3,1,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(19,3,'禽类',NULL,4,1,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(20,4,'鱼类',NULL,1,1,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(21,4,'虾蟹类',NULL,2,1,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(22,4,'贝类',NULL,3,1,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(23,5,'大米',NULL,1,1,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(24,5,'食用油',NULL,2,1,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(25,5,'面粉',NULL,3,1,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(26,6,'酱油醋',NULL,1,1,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(27,6,'香辛料',NULL,2,1,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(28,6,'调味酱',NULL,3,1,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(29,7,'豆腐类',NULL,1,1,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(30,7,'豆干类',NULL,2,1,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(31,7,'腐竹类',NULL,3,1,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(32,8,'速冻面点',NULL,1,1,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(33,8,'冷冻肉类',NULL,2,1,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(34,8,'冷冻水产',NULL,3,1,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='单据授权更正表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `correction_authorizations`
--

LOCK TABLES `correction_authorizations` WRITE;
/*!40000 ALTER TABLE `correction_authorizations` DISABLE KEYS */;
INSERT INTO `correction_authorizations` VALUES (1,1,3,'订单金额需要调整','{\"final_amount\": 23000}','{\"final_amount\": 19000}','2026-08-12 21:11:51','2026-08-12 21:11:51','2026-08-12 21:11:51');
/*!40000 ALTER TABLE `correction_authorizations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_arrival_logs`
--

DROP TABLE IF EXISTS `delivery_arrival_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_arrival_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `task_id` bigint unsigned NOT NULL COMMENT '配送任务ID',
  `sequence_id` bigint unsigned DEFAULT NULL COMMENT '关联的配送顺序表ID',
  `merchant_id` bigint unsigned NOT NULL COMMENT '商家ID',
  `event_type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '事件类型：arrival=到达 departure=离开 delivered=送达 skipped=跳过 gps_enter=进入围栏 gps_leave=离开围栏',
  `event_time` timestamp NOT NULL COMMENT '事件发生时间',
  `gps_latitude` decimal(10,8) DEFAULT NULL COMMENT '纬度',
  `gps_longitude` decimal(11,8) DEFAULT NULL COMMENT '经度',
  `gps_accuracy` decimal(8,2) DEFAULT NULL COMMENT '精度（米）',
  `source` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'driver' COMMENT '来源：driver=司机 gps_auto=自动 system=系统 admin=后台',
  `operator_id` bigint unsigned DEFAULT NULL COMMENT '操作人ID',
  `extra_data` json DEFAULT NULL COMMENT '额外数据',
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `delivery_arrival_logs_task_id_index` (`task_id`),
  KEY `delivery_arrival_logs_merchant_id_index` (`merchant_id`),
  KEY `delivery_arrival_logs_event_time_index` (`event_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='配送抵达时间流水：每次到达、离开、送达的不可变记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_arrival_logs`
--

LOCK TABLES `delivery_arrival_logs` WRITE;
/*!40000 ALTER TABLE `delivery_arrival_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `delivery_arrival_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_note_items`
--

DROP TABLE IF EXISTS `delivery_note_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_note_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `delivery_note_id` bigint unsigned NOT NULL COMMENT '送货单ID',
  `sku_id` bigint unsigned NOT NULL COMMENT 'SKU ID',
  `sku_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'SKU名称（冗余）',
  `unit` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '单位',
  `quantity` bigint NOT NULL DEFAULT '0' COMMENT '应送数量',
  `picked_quantity` bigint NOT NULL DEFAULT '0' COMMENT '实际分货数量',
  `order_id` bigint unsigned DEFAULT NULL COMMENT '来源订单ID',
  `order_no` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '来源订单编号',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：1待分货，2已分货，3差异',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `delivery_note_items_delivery_note_id_index` (`delivery_note_id`),
  KEY `delivery_note_items_sku_id_index` (`sku_id`),
  KEY `delivery_note_items_order_id_index` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='送货单明细表：按SKU拆分，每条带来源订单号';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_note_items`
--

LOCK TABLES `delivery_note_items` WRITE;
/*!40000 ALTER TABLE `delivery_note_items` DISABLE KEYS */;
INSERT INTO `delivery_note_items` VALUES (1,1,1,'大白菜','斤',2000,2000,1,'ORD-20260728-001',2,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(2,1,2,'土豆','斤',500,500,1,'ORD-20260728-001',2,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(3,2,4,'五花肉','斤',500,500,2,'ORD-20260728-002',2,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(4,3,5,'鲜虾','斤',1000,0,NULL,NULL,1,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(5,4,2,'土豆','斤',800,0,NULL,NULL,1,'2026-08-12 21:11:50','2026-08-12 21:11:50');
/*!40000 ALTER TABLE `delivery_note_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_notes`
--

DROP TABLE IF EXISTS `delivery_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_notes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `note_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '送货单编号，如：DN-E01-20260810-001',
  `task_id` bigint unsigned NOT NULL COMMENT '所属配送任务ID',
  `merchant_id` bigint unsigned NOT NULL COMMENT '商家ID',
  `merchant_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '商家名称（冗余）',
  `merchant_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '配送地址（冗余）',
  `delivery_date` date NOT NULL COMMENT '送达日期',
  `order_ids` json DEFAULT NULL COMMENT '关联订单ID数组',
  `order_nos` json DEFAULT NULL COMMENT '关联订单编号数组（识别来源）',
  `product_summary` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '商品摘要',
  `total_quantity` bigint NOT NULL DEFAULT '0' COMMENT '应送总数量',
  `total_weight` decimal(10,2) DEFAULT NULL COMMENT '总重量（kg）',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：1待分货，2已分货，3已签收，4已取消',
  `delivered_at` timestamp NULL DEFAULT NULL COMMENT '实际分货/送达时间',
  `delivery_method` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '确认方式：manual=手工 scan=扫码 signature=签名',
  `remark` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `delivery_notes_note_no_unique` (`note_no`),
  KEY `delivery_notes_task_id_index` (`task_id`),
  KEY `delivery_notes_merchant_id_index` (`merchant_id`),
  KEY `delivery_notes_delivery_date_index` (`delivery_date`),
  KEY `delivery_notes_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='送货单主表：按商户维度，每商户一张，司机到店分货依据';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_notes`
--

LOCK TABLES `delivery_notes` WRITE;
/*!40000 ALTER TABLE `delivery_notes` DISABLE KEYS */;
INSERT INTO `delivery_notes` VALUES (1,'DN-E01-20260810-001',0,1,'味之初餐饮店','安徽省宿州市埇桥区人民路88号','2026-08-10','[1]','[\"ORD-20260728-001\"]','大白菜、土豆',2500,NULL,3,'2026-08-11 23:30:00','signature',NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(2,'DN-E01-20260810-002',0,3,'家常菜馆','安徽省宿州市埇桥区汴河路56号','2026-08-10','[2]','[\"ORD-20260728-002\"]','五花肉',500,NULL,2,'2026-08-12 00:00:00','manual','等待签收确认','2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(3,'DN-E01-20260811-001',0,2,'鲜之味快餐店','安徽省宿州市埇桥区淮海路120号','2026-08-11','[]','[]','鲜虾',1000,NULL,1,NULL,NULL,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(4,'DN-E02-20260811-001',0,4,'鑫鑫小吃店','安徽省宿州市埇桥区银河一路32号','2026-08-11','[]','[]','土豆',800,NULL,1,NULL,NULL,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL);
/*!40000 ALTER TABLE `delivery_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_route_stops`
--

DROP TABLE IF EXISTS `delivery_route_stops`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_route_stops` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `route_id` bigint unsigned NOT NULL COMMENT '所属线路ID',
  `merchant_id` bigint unsigned NOT NULL COMMENT '商家ID',
  `sequence_no` int unsigned NOT NULL COMMENT '顺序号，拖拽排序即改此字段；1,2,3...连续',
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '配送地址（冗余）',
  `latitude` decimal(10,8) DEFAULT NULL COMMENT '纬度',
  `longitude` decimal(11,8) DEFAULT NULL COMMENT '经度',
  `default_service_time` int unsigned NOT NULL DEFAULT '10' COMMENT '默认停留时间（分钟）',
  `is_active` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '是否启用：0停用 1启用',
  `remark` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `delivery_route_stops_route_id_sequence_no_unique` (`route_id`,`sequence_no`),
  UNIQUE KEY `delivery_route_stops_route_id_merchant_id_unique` (`route_id`,`merchant_id`),
  KEY `delivery_route_stops_route_id_index` (`route_id`),
  KEY `delivery_route_stops_route_id_sequence_no_index` (`route_id`,`sequence_no`),
  KEY `delivery_route_stops_merchant_id_index` (`merchant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='线路明细 — 商家列表。拖拽排序通过修改 sequence_no 实现';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_route_stops`
--

LOCK TABLES `delivery_route_stops` WRITE;
/*!40000 ALTER TABLE `delivery_route_stops` DISABLE KEYS */;
INSERT INTO `delivery_route_stops` VALUES (1,1,1,1,'安徽省宿州市埇桥区人民路88号',NULL,NULL,15,1,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(2,1,2,2,'安徽省宿州市埇桥区淮海路120号',NULL,NULL,15,1,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(3,1,3,3,'安徽省宿州市埇桥区汴河路56号',NULL,NULL,15,1,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(4,2,4,1,'安徽省宿州市埇桥区银河一路32号',NULL,NULL,15,1,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(5,2,5,2,'安徽省宿州市埇桥区胜利路18号',NULL,NULL,15,1,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50');
/*!40000 ALTER TABLE `delivery_route_stops` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_routes`
--

DROP TABLE IF EXISTS `delivery_routes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_routes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '线路名称，如：城东1号线',
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '线路编码，如：E01',
  `warehouse_id` bigint unsigned DEFAULT NULL COMMENT '出发仓库ID',
  `default_driver_id` bigint unsigned DEFAULT NULL COMMENT '默认司机（用户ID）',
  `default_vehicle_id` bigint unsigned DEFAULT NULL COMMENT '默认车辆ID',
  `color` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#3B82F6' COMMENT '地图显示颜色',
  `departure_time` time NOT NULL DEFAULT '06:00:00' COMMENT '默认出发时间',
  `estimated_duration` int unsigned DEFAULT NULL COMMENT '预计总时长（分钟）',
  `estimated_distance` decimal(8,2) DEFAULT NULL COMMENT '预计总里程（公里）',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '描述',
  `sort` int unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：0停用，1启用',
  `remark` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `delivery_routes_code_unique` (`code`),
  KEY `delivery_routes_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='配送线路定义表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_routes`
--

LOCK TABLES `delivery_routes` WRITE;
/*!40000 ALTER TABLE `delivery_routes` DISABLE KEYS */;
INSERT INTO `delivery_routes` VALUES (1,'城区北线','E01',1,1,1,'#3B82F6','06:00:00',120,45.50,'人民路-淮海路-汴河路北侧',1,1,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(2,'城区南线','E02',1,2,2,'#10B981','06:30:00',100,38.20,'银河路-胜利路-宿怀路南侧',2,1,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL);
/*!40000 ALTER TABLE `delivery_routes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_task_details`
--

DROP TABLE IF EXISTS `delivery_task_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_task_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `task_id` bigint unsigned NOT NULL COMMENT '所属配送任务ID',
  `order_id` bigint unsigned DEFAULT NULL COMMENT '关联的原始订单ID',
  `merchant_id` bigint unsigned NOT NULL COMMENT '商家ID',
  `merchant_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '商家名称（冗余）',
  `merchant_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '配送地址（冗余）',
  `order_date` date DEFAULT NULL COMMENT '下单日期',
  `delivery_date` date NOT NULL COMMENT '送达日期',
  `product_summary` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '商品摘要',
  `total_quantity` decimal(10,2) DEFAULT NULL COMMENT '总数量',
  `total_weight` decimal(10,2) DEFAULT NULL COMMENT '总重量（kg）',
  `source_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'order' COMMENT '来源类型：order=订单 direct=直配单 merge=合并单',
  `source_id` bigint unsigned DEFAULT NULL COMMENT '来源单据ID',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：1待配送 2配送中 3已送达 4已取消',
  `delivered_at` timestamp NULL DEFAULT NULL COMMENT '实际送达时间',
  `delivery_method` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '配送方式：manual=手工 scan=扫码 photo=拍照 signature=签名',
  `delivery_photos` json DEFAULT NULL COMMENT '配送照片[{url, taken_at}]',
  `delivery_remark` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '配送备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `delivery_task_details_task_id_index` (`task_id`),
  KEY `delivery_task_details_merchant_id_index` (`merchant_id`),
  KEY `delivery_task_details_delivery_date_index` (`delivery_date`),
  KEY `delivery_task_details_order_date_index` (`order_date`),
  KEY `delivery_task_details_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='配送任务明细表：运营从单据池勾选的每张单据';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_task_details`
--

LOCK TABLES `delivery_task_details` WRITE;
/*!40000 ALTER TABLE `delivery_task_details` DISABLE KEYS */;
INSERT INTO `delivery_task_details` VALUES (1,1,1,1,'味之初餐饮店','安徽省宿州市埇桥区人民路88号','2026-08-12','2026-08-13','大白菜、土豆',2500.00,NULL,'order',1,3,'2026-08-11 23:25:00',NULL,NULL,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(2,1,2,3,'家常菜馆','安徽省宿州市埇桥区汴河路56号','2026-08-12','2026-08-13','五花肉',500.00,NULL,'order',2,3,'2026-08-11 23:50:00',NULL,NULL,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50');
/*!40000 ALTER TABLE `delivery_task_details` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='配送任务订单关联表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_task_orders`
--

LOCK TABLES `delivery_task_orders` WRITE;
/*!40000 ALTER TABLE `delivery_task_orders` DISABLE KEYS */;
INSERT INTO `delivery_task_orders` VALUES (1,1,1,1,2,'2026-08-07 00:38:40','2026-08-07 00:38:40');
/*!40000 ALTER TABLE `delivery_task_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_task_sequences`
--

DROP TABLE IF EXISTS `delivery_task_sequences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_task_sequences` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `task_id` bigint unsigned NOT NULL COMMENT '所属配送任务ID',
  `task_detail_ids` json NOT NULL COMMENT '本商家在本任务中的所有明细ID数组',
  `merchant_id` bigint unsigned NOT NULL COMMENT '商家ID',
  `merchant_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '商家名称（冗余）',
  `merchant_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '地址（冗余）',
  `latitude` decimal(10,8) DEFAULT NULL COMMENT '纬度',
  `longitude` decimal(11,8) DEFAULT NULL COMMENT '经度',
  `base_sequence_no` int unsigned NOT NULL COMMENT '来自线路的原始顺序号，永不变',
  `sequence_no` int unsigned NOT NULL COMMENT '本次任务中的实际顺序号（1,2,3...）',
  `estimated_arrival` timestamp NULL DEFAULT NULL COMMENT '预计到达时间',
  `estimated_departure` timestamp NULL DEFAULT NULL COMMENT '预计离开时间',
  `actual_arrival` timestamp NULL DEFAULT NULL COMMENT '实际到达时间',
  `actual_departure` timestamp NULL DEFAULT NULL COMMENT '实际离开时间',
  `actual_delivered_at` timestamp NULL DEFAULT NULL COMMENT '实际送达/签收时间',
  `is_urgent` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '是否加急：0否 1是',
  `urgent_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '加急原因',
  `is_important` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '是否重要：0否 1是',
  `important_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '重要原因',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：1待配送 2配送中 3已到达 4已送达 5已跳过 6失败',
  `delivery_method` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '确认方式',
  `delivery_photos` json DEFAULT NULL COMMENT '配送照片',
  `signature_image` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '签名图片URL',
  `gps_latitude` decimal(10,8) DEFAULT NULL COMMENT '送达时纬度',
  `gps_longitude` decimal(11,8) DEFAULT NULL COMMENT '送达时经度',
  `skip_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '跳过原因',
  `fail_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '失败原因',
  `remark` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `delivery_task_sequences_task_id_index` (`task_id`),
  KEY `delivery_task_sequences_task_id_sequence_no_index` (`task_id`,`sequence_no`),
  KEY `delivery_task_sequences_task_id_base_sequence_no_index` (`task_id`,`base_sequence_no`),
  KEY `delivery_task_sequences_status_index` (`status`),
  KEY `delivery_task_sequences_is_urgent_index` (`is_urgent`),
  KEY `delivery_task_sequences_is_important_index` (`is_important`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='配送顺序表：按线路 sequence_no 自动生成，加急/重要标记不改变顺序';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_task_sequences`
--

LOCK TABLES `delivery_task_sequences` WRITE;
/*!40000 ALTER TABLE `delivery_task_sequences` DISABLE KEYS */;
INSERT INTO `delivery_task_sequences` VALUES (1,1,'[1]',1,'味之初餐饮店','安徽省宿州市埇桥区人民路88号',NULL,NULL,1,1,NULL,NULL,'2026-08-11 23:15:00','2026-08-11 23:30:00','2026-08-11 23:25:00',0,NULL,0,NULL,4,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(2,1,'[2]',3,'家常菜馆','安徽省宿州市埇桥区汴河路56号',NULL,NULL,3,2,NULL,NULL,'2026-08-11 23:40:00','2026-08-11 23:55:00','2026-08-11 23:50:00',0,NULL,0,NULL,4,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50');
/*!40000 ALTER TABLE `delivery_task_sequences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_tasks`
--

DROP TABLE IF EXISTS `delivery_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_tasks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `task_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '任务编号，如：T-E01-20260810-001',
  `route_id` bigint unsigned NOT NULL COMMENT '所属线路ID',
  `delivery_date` date NOT NULL COMMENT '送达日期',
  `generated_at` timestamp NULL DEFAULT NULL COMMENT '任务生成时间',
  `driver_id` bigint unsigned DEFAULT NULL COMMENT '分配司机ID',
  `vehicle_id` bigint unsigned DEFAULT NULL COMMENT '分配车辆ID',
  `batch` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '配送批次：1上午，2下午',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：1待配送 2已分配 3配送中 4暂停 5已完成 6已取消',
  `planned_start_time` timestamp NULL DEFAULT NULL COMMENT '计划出发时间',
  `actual_start_time` timestamp NULL DEFAULT NULL COMMENT '实际出发时间',
  `actual_complete_time` timestamp NULL DEFAULT NULL COMMENT '实际完成时间',
  `total_stops` int unsigned NOT NULL DEFAULT '0' COMMENT '总配送商家数',
  `completed_stops` int unsigned NOT NULL DEFAULT '0' COMMENT '已完成商家数',
  `skipped_stops` int unsigned NOT NULL DEFAULT '0' COMMENT '跳过商家数',
  `total_orders` int unsigned NOT NULL DEFAULT '0' COMMENT '关联单据总数',
  `has_urgent` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '是否包含加急：0否 1是',
  `has_important` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '是否包含重要：0否 1是',
  `remark` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `delivery_tasks_task_no_unique` (`task_no`),
  KEY `delivery_tasks_route_id_index` (`route_id`),
  KEY `delivery_tasks_delivery_date_index` (`delivery_date`),
  KEY `delivery_tasks_route_id_delivery_date_index` (`route_id`,`delivery_date`),
  KEY `delivery_tasks_driver_id_index` (`driver_id`),
  KEY `delivery_tasks_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='配送任务表：运营按需勾选单据生成';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_tasks`
--

LOCK TABLES `delivery_tasks` WRITE;
/*!40000 ALTER TABLE `delivery_tasks` DISABLE KEYS */;
INSERT INTO `delivery_tasks` VALUES (1,'T-E01-20260810-001',1,'2026-08-10','2026-08-12 21:11:50',1,1,1,5,'2026-08-11 22:00:00','2026-08-11 22:05:00','2026-08-12 00:30:00',2,2,0,2,0,0,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50');
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='配送轨迹表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_tracks`
--

LOCK TABLES `delivery_tracks` WRITE;
/*!40000 ALTER TABLE `delivery_tracks` DISABLE KEYS */;
INSERT INTO `delivery_tracks` VALUES (1,1,1,33720000,116970000,'农批市场出发','2026-08-11 22:05:00','2026-08-12 21:11:50'),(2,1,1,33721000,116975000,'人民路中段','2026-08-11 22:30:00','2026-08-12 21:11:50');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='差异单表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `discrepancies`
--

LOCK TABLES `discrepancies` WRITE;
/*!40000 ALTER TABLE `discrepancies` DISABLE KEYS */;
INSERT INTO `discrepancies` VALUES (1,'DIS-20260810-001',1,1,2,1,500,480,-20,-13800,'运输途中少件',NULL,1,2,3,NULL,'2026-08-12 21:11:51',1,2,NULL,'2026-08-12 21:11:51','2026-08-12 21:11:51',NULL);
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
INSERT INTO `driver_vehicles` VALUES (1,1,1,1,'2026-08-12 21:11:50',NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(2,2,2,1,'2026-08-12 21:11:50',NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50');
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
  `remark` text COLLATE utf8mb4_unicode_ci COMMENT '备注',
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
INSERT INTO `drivers` VALUES (1,NULL,'周师傅','13700000001','342201199001011234',1,1,'北线司机，5年驾龄','2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(2,NULL,'马师傅','13700000002','342201199203025678',1,1,'南线司机，3年驾龄','2026-08-12 21:11:50','2026-08-12 21:11:50',NULL);
/*!40000 ALTER TABLE `drivers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `featured_promotions`
--

DROP TABLE IF EXISTS `featured_promotions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `featured_promotions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '类型：1主推商品，2主推品类',
  `target_id` bigint unsigned NOT NULL COMMENT '目标ID',
  `sort` int unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `start_at` timestamp NULL DEFAULT NULL COMMENT '开始时间',
  `end_at` timestamp NULL DEFAULT NULL COMMENT '结束时间',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：0禁用，1启用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `featured_promotions_type_index` (`type`),
  KEY `featured_promotions_target_id_index` (`target_id`),
  KEY `featured_promotions_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='运营主推表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `featured_promotions`
--

LOCK TABLES `featured_promotions` WRITE;
/*!40000 ALTER TABLE `featured_promotions` DISABLE KEYS */;
INSERT INTO `featured_promotions` VALUES (1,1,1,1,'2026-08-12 21:11:51','2026-09-12 21:11:51',1,'2026-08-12 21:11:51','2026-08-12 21:11:51',NULL),(2,2,1,2,'2026-08-12 21:11:51','2026-09-12 21:11:51',1,'2026-08-12 21:11:51','2026-08-12 21:11:51',NULL);
/*!40000 ALTER TABLE `featured_promotions` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='常购清单表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `frequently_bought`
--

LOCK TABLES `frequently_bought` WRITE;
/*!40000 ALTER TABLE `frequently_bought` DISABLE KEYS */;
INSERT INTO `frequently_bought` VALUES (1,1,1,15,'2026-08-12 21:11:50','2026-08-12 21:11:50','2026-08-12 21:11:50'),(2,1,2,12,'2026-08-12 21:11:50','2026-08-12 21:11:50','2026-08-12 21:11:50'),(3,1,3,8,'2026-08-12 21:11:50','2026-08-12 21:11:50','2026-08-12 21:11:50');
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='实时库存表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory`
--

LOCK TABLES `inventory` WRITE;
/*!40000 ALTER TABLE `inventory` DISABLE KEYS */;
INSERT INTO `inventory` VALUES (1,1,1,50000,0,50000,'B20260701001',NULL,5000,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(2,1,2,30000,0,30000,'B20260701002',NULL,3000,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(3,1,3,20000,0,20000,'B20260701003',NULL,2000,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(4,2,4,15000,0,15000,'B20260701004',NULL,2000,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(5,2,5,8000,0,8000,'B20260701005',NULL,1000,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(6,1,6,100,0,100,'B20260701006',NULL,10,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(7,1,5,1000,0,1000,'IN20260813061558',NULL,0,'2026-08-12 22:16:06','2026-08-12 22:16:06'),(8,2,4,55,0,55,'IN20260813061621',NULL,0,'2026-08-12 22:16:25','2026-08-12 22:16:25');
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='库存变动日志表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_logs`
--

LOCK TABLES `inventory_logs` WRITE;
/*!40000 ALTER TABLE `inventory_logs` DISABLE KEYS */;
INSERT INTO `inventory_logs` VALUES (1,1,1,1,50000,0,50000,'采购入库',NULL,'purchase_order',1,'2026-08-12 21:11:50'),(2,1,1,2,-5000,50000,45000,'订单出库',NULL,'order',1,'2026-08-12 21:11:50'),(3,1,5,1,1000,0,1000,'采购单 PO202608130615061594 入库',1,'purchase_order',3,'2026-08-12 22:16:06'),(4,2,4,1,55,0,55,'采购单 PO202608130615065447 入库',1,'purchase_order',4,'2026-08-12 22:16:25');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='发票表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
INSERT INTO `invoices` VALUES (1,'INV-20260728-001',1,1,'味之初餐饮店',23000,'/uploads/invoices/demo-inv-001.pdf',2,'2026-08-12 21:11:51','2026-08-12 21:11:51',NULL,'2026-08-12 21:11:51','2026-08-12 21:11:51');
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='搜索关键词表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `keywords`
--

LOCK TABLES `keywords` WRITE;
/*!40000 ALTER TABLE `keywords` DISABLE KEYS */;
INSERT INTO `keywords` VALUES (1,'白菜',NULL,128,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(2,'土豆',NULL,95,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(3,'五花肉',NULL,67,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(4,'大豆油',NULL,42,'2026-08-12 21:11:50','2026-08-12 21:11:50');
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='登录日志表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_logs`
--

LOCK TABLES `login_logs` WRITE;
/*!40000 ALTER TABLE `login_logs` DISABLE KEYS */;
INSERT INTO `login_logs` VALUES (1,1,'seeding','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/126.0',1,1,NULL,'2026-08-12 21:11:51'),(2,2,'superadmin','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/126.0',1,1,NULL,'2026-08-12 21:11:51'),(3,3,'operator1','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/126.0',1,1,NULL,'2026-08-12 21:11:51'),(4,4,'ops_manager','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/126.0',1,1,NULL,'2026-08-12 21:11:51'),(5,5,'finance1','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/126.0',1,1,NULL,'2026-08-12 21:11:51'),(6,NULL,'unknown','192.168.1.100','Mozilla/5.0',1,0,'用户不存在','2026-08-12 21:11:51'),(7,1,'seeding','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36',1,1,NULL,'2026-08-12 21:14:10'),(8,1,'seeding','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0',1,1,NULL,'2026-08-12 22:08:44');
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
  `purchase_order_item_id` bigint unsigned DEFAULT NULL COMMENT '采购单明细ID（入库差异来源）',
  `purchase_order_id` bigint unsigned DEFAULT NULL COMMENT '采购单ID（入库差异来源）',
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
  KEY `loss_order_items_supplier_id_index` (`supplier_id`),
  KEY `loss_order_items_purchase_order_item_id_index` (`purchase_order_item_id`),
  KEY `loss_order_items_purchase_order_id_index` (`purchase_order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='损耗单明细表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loss_order_items`
--

LOCK TABLES `loss_order_items` WRITE;
/*!40000 ALTER TABLE `loss_order_items` DISABLE KEYS */;
INSERT INTO `loss_order_items` VALUES (1,1,NULL,NULL,1,2,800,8000,6400,1,NULL,'失水减重',NULL,'2026-08-12 21:11:51','2026-08-12 21:11:51');
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
  `source_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '来源类型：purchase_order=采购入库差异',
  `source_id` bigint unsigned DEFAULT NULL COMMENT '来源业务ID（如采购单ID）',
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
  KEY `loss_orders_approval_status_index` (`approval_status`),
  KEY `loss_orders_source_type_source_id_index` (`source_type`,`source_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='损耗单主表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loss_orders`
--

LOCK TABLES `loss_orders` WRITE;
/*!40000 ALTER TABLE `loss_orders` DISABLE KEYS */;
INSERT INTO `loss_orders` VALUES (1,'LOSS-20260728-001',NULL,NULL,1,6400,2,3,2,3,3,'2026-08-12 21:11:51','2026-08-12 21:11:51',NULL,'蔬菜称重失水损耗',NULL,'2026-08-12 21:11:51','2026-08-12 21:11:51',NULL);
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
INSERT INTO `merchant_accounts` VALUES (1,1,0,0,0,5000000,2,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(2,2,0,0,0,10000000,2,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(3,3,0,0,0,5000000,2,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(4,4,0,0,0,3000000,2,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(5,5,0,0,0,5000000,2,'2026-08-12 21:11:50','2026-08-12 21:11:50');
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商家收货地址表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `merchant_addresses`
--

LOCK TABLES `merchant_addresses` WRITE;
/*!40000 ALTER TABLE `merchant_addresses` DISABLE KEYS */;
INSERT INTO `merchant_addresses` VALUES (1,1,'吴老板','15800000001','安徽省宿州市埇桥区人民路88号',1,0,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(2,2,'郑老板','15800000002','安徽省宿州市埇桥区淮海路120号',1,0,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(3,3,'冯老板','15800000003','安徽省宿州市埇桥区汴河路56号',1,0,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(4,4,'蒋老板','15800000004','安徽省宿州市埇桥区银河一路32号',1,0,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(5,5,'韩老板','15800000005','安徽省宿州市埇桥区胜利路18号',1,0,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商家收藏商品表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `merchant_favorites`
--

LOCK TABLES `merchant_favorites` WRITE;
/*!40000 ALTER TABLE `merchant_favorites` DISABLE KEYS */;
INSERT INTO `merchant_favorites` VALUES (1,1,1,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(2,1,4,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(3,1,5,'2026-08-12 21:11:50','2026-08-12 21:11:50');
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
  `target_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sku' COMMENT '配置类型：product=商品级，sku=SKU级',
  `product_id` bigint unsigned DEFAULT NULL COMMENT '商品ID（商品级配置时使用）',
  `sku_id` bigint unsigned DEFAULT NULL COMMENT 'SKU ID（SKU级配置时使用）',
  `is_visible` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '是否可见：0否，1是',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_merchant_visibility` (`merchant_id`,`target_type`,`product_id`,`sku_id`),
  KEY `merchant_sku_visibility_product_id_index` (`product_id`),
  KEY `merchant_sku_visibility_sku_id_index` (`sku_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商家可见性配置表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `merchant_sku_visibility`
--

LOCK TABLES `merchant_sku_visibility` WRITE;
/*!40000 ALTER TABLE `merchant_sku_visibility` DISABLE KEYS */;
INSERT INTO `merchant_sku_visibility` VALUES (1,1,'product',4,NULL,0,'2026-08-12 22:09:27','2026-08-12 22:09:31');
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
  `latitude` decimal(10,7) DEFAULT NULL COMMENT '纬度（GCJ-02坐标系）',
  `longitude` decimal(10,7) DEFAULT NULL COMMENT '经度（GCJ-02坐标系）',
  `coordinate_type` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'gcj02' COMMENT '坐标系类型：gcj02/wgs84/bd09',
  `geohash` varchar(12) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Geohash编码（就近查询优化）',
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
  KEY `merchants_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商家表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `merchants`
--

LOCK TABLES `merchants` WRITE;
/*!40000 ALTER TABLE `merchants` DISABLE KEYS */;
INSERT INTO `merchants` VALUES (1,NULL,'味之初餐饮店','吴老板','15800000001','安徽省宿州市埇桥区人民路88号',33.6361230,116.9638280,'gcj02','wtck9f2h',50000,1,5000000,1,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(2,NULL,'鲜之味快餐店','郑老板','15800000002','安徽省宿州市埇桥区淮海路120号',33.6384560,116.9653120,'gcj02','wtck9f4p',30000,2,10000000,1,'账期客户','2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(3,NULL,'家常菜馆','冯老板','15800000003','安徽省宿州市埇桥区汴河路56号',33.6407890,116.9710950,'gcj02','wtck9f7q',0,1,0,1,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(4,NULL,'鑫鑫小吃店','蒋老板','15800000004','安徽省宿州市埇桥区银河一路32号',33.6421120,116.9687630,'gcj02','wtck9f5x',20000,3,3000000,1,'预付款客户','2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(5,NULL,'老街坊饭店','韩老板','15800000005','安徽省宿州市埇桥区胜利路18号',33.6345670,116.9605410,'gcj02','wtck9f1z',0,1,0,1,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=401 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (381,'2026_07_27_000001_create_users_and_permissions_tables',1),(382,'2026_07_27_000002_create_organization_tables',1),(383,'2026_07_27_000003_create_product_tables',1),(384,'2026_07_27_000004_create_sku_barcodes_suppliers_tables',1),(385,'2026_07_27_000005_create_purchase_tables',1),(386,'2026_07_27_000006_create_order_tables',1),(387,'2026_07_27_000007_create_inventory_tables',1),(388,'2026_07_27_000008_create_loss_tables',1),(389,'2026_07_27_000009_create_picking_tables',1),(390,'2026_07_27_000010_create_delivery_tables',1),(391,'2026_07_27_000011_create_discrepancy_tables',1),(392,'2026_07_27_000012_create_finance_tables',1),(393,'2026_07_27_000013_create_system_tables',1),(394,'2026_07_27_000014_create_wechat_tables',1),(395,'2026_07_27_000015_create_price_strategy_tables',1),(396,'2026_07_27_000016_create_return_tables',1),(397,'2026_07_27_000017_create_price_apportionment_tables',1),(398,'2026_07_27_000018_create_merchant_extension_tables',1),(399,'2026_07_27_000019_create_notification_tables',1),(400,'2026_07_27_000020_create_approval_tables',1);
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
INSERT INTO `model_has_permissions` VALUES (1,'App\\Models\\User',1),(2,'App\\Models\\User',1),(3,'App\\Models\\User',1),(4,'App\\Models\\User',1),(5,'App\\Models\\User',1),(6,'App\\Models\\User',1),(7,'App\\Models\\User',1),(8,'App\\Models\\User',1),(9,'App\\Models\\User',1),(10,'App\\Models\\User',1),(11,'App\\Models\\User',1),(12,'App\\Models\\User',1),(13,'App\\Models\\User',1),(14,'App\\Models\\User',1),(15,'App\\Models\\User',1),(16,'App\\Models\\User',1),(17,'App\\Models\\User',1),(18,'App\\Models\\User',1),(19,'App\\Models\\User',1),(20,'App\\Models\\User',1),(21,'App\\Models\\User',1),(22,'App\\Models\\User',1),(23,'App\\Models\\User',1),(24,'App\\Models\\User',1),(25,'App\\Models\\User',1),(26,'App\\Models\\User',1),(27,'App\\Models\\User',1),(28,'App\\Models\\User',1),(29,'App\\Models\\User',1),(30,'App\\Models\\User',1),(31,'App\\Models\\User',1),(32,'App\\Models\\User',1),(33,'App\\Models\\User',1),(34,'App\\Models\\User',1),(35,'App\\Models\\User',1),(36,'App\\Models\\User',1),(37,'App\\Models\\User',1),(38,'App\\Models\\User',1),(39,'App\\Models\\User',1),(40,'App\\Models\\User',1),(41,'App\\Models\\User',1),(42,'App\\Models\\User',1),(43,'App\\Models\\User',1),(44,'App\\Models\\User',1),(45,'App\\Models\\User',1),(46,'App\\Models\\User',1),(47,'App\\Models\\User',1),(48,'App\\Models\\User',1),(49,'App\\Models\\User',1),(50,'App\\Models\\User',1),(51,'App\\Models\\User',1),(52,'App\\Models\\User',1),(53,'App\\Models\\User',1),(54,'App\\Models\\User',1),(55,'App\\Models\\User',1),(56,'App\\Models\\User',1),(57,'App\\Models\\User',1),(58,'App\\Models\\User',1),(59,'App\\Models\\User',1),(60,'App\\Models\\User',1),(61,'App\\Models\\User',1),(62,'App\\Models\\User',1),(63,'App\\Models\\User',1),(64,'App\\Models\\User',1),(65,'App\\Models\\User',1),(66,'App\\Models\\User',1),(67,'App\\Models\\User',1),(68,'App\\Models\\User',1),(69,'App\\Models\\User',1),(70,'App\\Models\\User',1),(71,'App\\Models\\User',1),(72,'App\\Models\\User',1),(73,'App\\Models\\User',1),(74,'App\\Models\\User',1),(75,'App\\Models\\User',1),(76,'App\\Models\\User',1),(77,'App\\Models\\User',1),(78,'App\\Models\\User',1),(79,'App\\Models\\User',1),(80,'App\\Models\\User',1),(81,'App\\Models\\User',1),(82,'App\\Models\\User',1),(83,'App\\Models\\User',1),(84,'App\\Models\\User',1),(85,'App\\Models\\User',1),(86,'App\\Models\\User',1),(87,'App\\Models\\User',1),(88,'App\\Models\\User',1),(89,'App\\Models\\User',1),(90,'App\\Models\\User',1),(91,'App\\Models\\User',1),(92,'App\\Models\\User',1),(93,'App\\Models\\User',1),(94,'App\\Models\\User',1),(95,'App\\Models\\User',1),(96,'App\\Models\\User',1),(97,'App\\Models\\User',1),(98,'App\\Models\\User',1),(99,'App\\Models\\User',1),(100,'App\\Models\\User',1),(101,'App\\Models\\User',1),(102,'App\\Models\\User',1),(103,'App\\Models\\User',1),(104,'App\\Models\\User',1),(105,'App\\Models\\User',1),(106,'App\\Models\\User',1),(107,'App\\Models\\User',1),(108,'App\\Models\\User',1),(109,'App\\Models\\User',1),(110,'App\\Models\\User',1),(111,'App\\Models\\User',1),(112,'App\\Models\\User',1),(113,'App\\Models\\User',1),(114,'App\\Models\\User',1),(115,'App\\Models\\User',1),(116,'App\\Models\\User',1),(117,'App\\Models\\User',1),(118,'App\\Models\\User',1),(119,'App\\Models\\User',1),(120,'App\\Models\\User',1),(121,'App\\Models\\User',1),(122,'App\\Models\\User',1),(123,'App\\Models\\User',1),(124,'App\\Models\\User',1),(125,'App\\Models\\User',1),(126,'App\\Models\\User',1),(127,'App\\Models\\User',1),(128,'App\\Models\\User',1),(129,'App\\Models\\User',1),(130,'App\\Models\\User',1),(131,'App\\Models\\User',1),(132,'App\\Models\\User',1),(133,'App\\Models\\User',1),(134,'App\\Models\\User',1),(135,'App\\Models\\User',1),(136,'App\\Models\\User',1),(137,'App\\Models\\User',1),(138,'App\\Models\\User',1),(139,'App\\Models\\User',1),(140,'App\\Models\\User',1),(141,'App\\Models\\User',1),(142,'App\\Models\\User',1),(143,'App\\Models\\User',1),(144,'App\\Models\\User',1),(145,'App\\Models\\User',1),(146,'App\\Models\\User',1),(147,'App\\Models\\User',1),(148,'App\\Models\\User',1),(149,'App\\Models\\User',1),(150,'App\\Models\\User',1),(151,'App\\Models\\User',1),(152,'App\\Models\\User',1),(153,'App\\Models\\User',1),(154,'App\\Models\\User',1),(155,'App\\Models\\User',1),(156,'App\\Models\\User',1),(157,'App\\Models\\User',1),(158,'App\\Models\\User',1),(159,'App\\Models\\User',1),(160,'App\\Models\\User',1),(161,'App\\Models\\User',1),(162,'App\\Models\\User',1),(163,'App\\Models\\User',1),(164,'App\\Models\\User',1),(165,'App\\Models\\User',1),(166,'App\\Models\\User',1),(167,'App\\Models\\User',1),(168,'App\\Models\\User',1),(169,'App\\Models\\User',1),(170,'App\\Models\\User',1),(171,'App\\Models\\User',1),(172,'App\\Models\\User',1),(173,'App\\Models\\User',1),(174,'App\\Models\\User',1),(175,'App\\Models\\User',1),(176,'App\\Models\\User',1),(177,'App\\Models\\User',1),(178,'App\\Models\\User',1),(179,'App\\Models\\User',1),(180,'App\\Models\\User',1),(181,'App\\Models\\User',1),(182,'App\\Models\\User',1),(183,'App\\Models\\User',1),(184,'App\\Models\\User',1),(185,'App\\Models\\User',1),(186,'App\\Models\\User',1),(187,'App\\Models\\User',1),(188,'App\\Models\\User',1),(189,'App\\Models\\User',1),(190,'App\\Models\\User',1),(191,'App\\Models\\User',1),(192,'App\\Models\\User',1),(193,'App\\Models\\User',1),(194,'App\\Models\\User',1),(195,'App\\Models\\User',1),(196,'App\\Models\\User',1),(197,'App\\Models\\User',1),(198,'App\\Models\\User',1),(199,'App\\Models\\User',1),(200,'App\\Models\\User',1),(201,'App\\Models\\User',1),(202,'App\\Models\\User',1),(203,'App\\Models\\User',1);
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
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',1),(1,'App\\Models\\User',2),(2,'App\\Models\\User',3),(3,'App\\Models\\User',4),(4,'App\\Models\\User',5),(5,'App\\Models\\User',6),(6,'App\\Models\\User',7),(7,'App\\Models\\User',8),(8,'App\\Models\\User',9),(9,'App\\Models\\User',10);
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='操作日志表';
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
  `quantity` bigint NOT NULL DEFAULT '0' COMMENT '下单数量（base_unit 最小单位）',
  `unit_id` bigint unsigned DEFAULT NULL COMMENT '下单时选择的单位ID',
  `unit_quantity` bigint NOT NULL DEFAULT '0' COMMENT '下单时选择的单位数量（如选"箱"输入2，此字段=2）',
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='订单明细表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,1,1,'大白菜','[{\"label\": \"规格\", \"value\": \"斤\"}]',2000,NULL,0,9200,2100,9200,18400000,19320000,0,0,NULL,NULL,920000,1,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(2,1,2,'土豆','[{\"label\": \"规格\", \"value\": \"斤\"}]',500,NULL,0,13800,480,13800,6900000,6624000,0,0,NULL,NULL,276000,1,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(3,2,4,'五花肉','[{\"label\": \"规格\", \"value\": \"斤\"}]',500,NULL,0,149000,0,0,74500000,0,0,0,NULL,NULL,74500000,1,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(4,3,5,'鲜虾','[{\"label\": \"规格\", \"value\": \"斤\"}]',1000,NULL,0,4025000,0,0,4025000000,0,0,0,NULL,NULL,4025000000,1,'2026-08-12 21:11:50','2026-08-12 21:11:50');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='售后退货明细表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_return_items`
--

LOCK TABLES `order_return_items` WRITE;
/*!40000 ALTER TABLE `order_return_items` DISABLE KEYS */;
INSERT INTO `order_return_items` VALUES (1,1,2,2,500,13800,6900000,0,'品质不达标','2026-08-12 21:11:50','2026-08-12 21:11:50');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='售后退货单表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_returns`
--

LOCK TABLES `order_returns` WRITE;
/*!40000 ALTER TABLE `order_returns` DISABLE KEYS */;
INSERT INTO `order_returns` VALUES (1,'OR-20260729-001',1,1,1,6900,0,'土豆质量问题退货',3,NULL,NULL,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL);
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
  `delivery_route_id` bigint unsigned DEFAULT NULL COMMENT '配送线路ID（已废弃，线路信息通过配送任务获取）',
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
  `order_date` date DEFAULT NULL COMMENT '单据日期',
  `delivery_date` date DEFAULT NULL COMMENT '收货日期',
  `remark` text COLLATE utf8mb4_unicode_ci COMMENT '备注',
  `is_supplement` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '是否补单：0否，1是',
  `supplement_for` bigint unsigned DEFAULT NULL COMMENT '补单关联的原订单ID',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_no_unique` (`order_no`),
  KEY `orders_merchant_id_index` (`merchant_id`),
  KEY `orders_delivery_route_id_index` (`delivery_route_id`),
  KEY `orders_status_index` (`status`),
  KEY `orders_batch_index` (`batch`),
  KEY `orders_order_date_index` (`order_date`),
  KEY `orders_delivery_date_index` (`delivery_date`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='订单表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,'ORD-20260728-001',1,NULL,1,'安徽省宿州市埇桥区人民路88号','吴老板','15800000001',4,23000,23000,23000,2,1,0,'2026-08-12','2026-08-13',NULL,0,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(2,'ORD-20260728-002',3,NULL,1,'安徽省宿州市埇桥区汴河路56号','冯老板','15800000003',2,74500,74500,0,1,2,0,'2026-08-12','2026-08-13',NULL,0,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(3,'ORD-20260729-001',2,NULL,2,'安徽省宿州市埇桥区淮海路120号','郑老板','15800000002',1,4025000,4025000,0,1,3,0,'2026-08-13','2026-08-14',NULL,0,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=204 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='权限表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'dashboard','web','仪表盘',1,0,'dashboard',0,'layout-dashboard','2026-08-12 21:05:32','2026-08-12 21:05:32'),(2,'user','web','用户权限',1,0,NULL,1,'users','2026-08-12 21:05:32','2026-08-12 21:05:32'),(3,'user.user','web','用户管理',2,2,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(4,'user.user.view','web','用户管理（查看）',3,3,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(5,'user.user.create','web','创建用户',3,3,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(6,'user.user.edit','web','编辑用户',3,3,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(7,'user.user.delete','web','删除用户',3,3,NULL,3,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(8,'user.user.toggle','web','启用/禁用',3,3,NULL,4,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(9,'user.user.reset-password','web','重置密码',3,3,NULL,5,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(10,'user.user.assign-role','web','分配角色',3,3,NULL,6,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(11,'user.role','web','角色管理',2,2,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(12,'user.role.view','web','角色管理（查看）',3,11,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(13,'user.role.create','web','创建角色',3,11,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(14,'user.role.edit','web','编辑角色',3,11,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(15,'user.role.delete','web','删除角色',3,11,NULL,3,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(16,'user.role.assign-permission','web','分配权限',3,11,NULL,4,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(17,'user.permission','web','权限管理',2,2,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(18,'user.permission.view','web','权限管理（查看）',3,17,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(19,'user.permission.create','web','创建权限',3,17,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(20,'user.permission.edit','web','编辑权限',3,17,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(21,'user.permission.delete','web','删除权限',3,17,NULL,3,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(22,'user.permission.assign-role','web','分配角色',3,17,NULL,4,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(23,'org','web','组织管理',1,0,NULL,2,'building','2026-08-12 21:05:32','2026-08-12 21:05:32'),(24,'org.supplier','web','供应商管理',2,23,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(25,'org.supplier.view','web','供应商管理（查看）',3,24,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(26,'org.supplier.create','web','创建供应商',3,24,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(27,'org.supplier.edit','web','编辑供应商',3,24,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(28,'org.supplier.delete','web','删除供应商',3,24,NULL,3,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(29,'org.merchant','web','商家管理',2,23,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(30,'org.merchant.view','web','商家管理（查看）',3,29,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(31,'org.merchant.create','web','创建商家',3,29,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(32,'org.merchant.edit','web','编辑商家',3,29,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(33,'org.merchant.delete','web','删除商家',3,29,NULL,3,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(34,'org.driver','web','司机管理',2,23,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(35,'org.driver.view','web','司机管理（查看）',3,34,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(36,'org.driver.create','web','创建司机',3,34,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(37,'org.driver.edit','web','编辑司机',3,34,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(38,'org.driver.delete','web','删除司机',3,34,NULL,3,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(39,'org.vehicle','web','车辆管理',2,23,NULL,3,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(40,'org.vehicle.view','web','车辆管理（查看）',3,39,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(41,'org.vehicle.create','web','创建车辆',3,39,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(42,'org.vehicle.edit','web','编辑车辆',3,39,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(43,'org.vehicle.delete','web','删除车辆',3,39,NULL,3,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(44,'product','web','商品管理',1,0,NULL,3,'package','2026-08-12 21:05:32','2026-08-12 21:05:32'),(45,'product.product','web','商品列表',2,44,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(46,'product.product.view','web','商品列表（查看）',3,45,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(47,'product.product.create','web','创建商品',3,45,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(48,'product.product.edit','web','编辑商品',3,45,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(49,'product.product.delete','web','删除商品',3,45,NULL,3,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(50,'product.category','web','分类管理',2,44,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(51,'product.category.view','web','分类管理（查看）',3,50,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(52,'product.category.create','web','创建分类',3,50,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(53,'product.category.edit','web','编辑分类',3,50,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(54,'product.category.delete','web','删除分类',3,50,NULL,3,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(55,'product.tag','web','标签管理',2,44,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(56,'product.tag.view','web','标签管理（查看）',3,55,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(57,'product.tag.create','web','创建标签',3,55,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(58,'product.tag.edit','web','编辑标签',3,55,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(59,'product.tag.delete','web','删除标签',3,55,NULL,3,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(60,'product.keyword','web','关键词管理',2,44,NULL,3,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(61,'product.keyword.view','web','关键词管理（查看）',3,60,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(62,'product.keyword.create','web','创建关键词',3,60,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(63,'product.keyword.edit','web','编辑关键词',3,60,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(64,'product.keyword.delete','web','删除关键词',3,60,NULL,3,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(65,'product.visibility','web','可见性配置',2,44,NULL,4,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(66,'product.visibility.view','web','可见性配置（查看）',3,65,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(67,'product.visibility.create','web','创建可见性配置',3,65,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(68,'product.visibility.edit','web','编辑可见性配置',3,65,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(69,'product.visibility.delete','web','删除可见性配置',3,65,NULL,3,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(70,'purchase','web','采购管理',1,0,NULL,4,'truck','2026-08-12 21:05:32','2026-08-12 21:05:32'),(71,'purchase.purchase-order','web','采购单',2,70,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(72,'purchase.purchase-order.view','web','采购单（查看）',3,71,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(73,'purchase.purchase-order.create','web','创建采购单',3,71,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(74,'purchase.purchase-order.edit','web','编辑采购单',3,71,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(75,'purchase.purchase-order.delete','web','删除采购单',3,71,NULL,3,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(76,'purchase.purchase-order.submit','web','提交审核',3,71,NULL,4,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(77,'purchase.purchase-order.approve','web','审核采购单',3,71,NULL,5,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(78,'purchase.purchase-return','web','采购退货',2,70,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(79,'purchase.purchase-return.view','web','采购退货（查看）',3,78,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(80,'purchase.purchase-return.create','web','创建退货',3,78,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(81,'purchase.purchase-return.edit','web','编辑退货',3,78,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(82,'purchase.purchase-return.delete','web','删除退货',3,78,NULL,3,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(83,'purchase.restock-reminder','web','补货提醒',2,70,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(84,'purchase.restock-reminder.view','web','补货提醒（查看）',3,83,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(85,'purchase.restock-reminder.create','web','创建提醒',3,83,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(86,'purchase.restock-reminder.edit','web','编辑提醒',3,83,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(87,'purchase.restock-reminder.delete','web','删除提醒',3,83,NULL,3,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(88,'order','web','订单管理',1,0,NULL,5,'shopping-cart','2026-08-12 21:05:32','2026-08-12 21:05:32'),(89,'order.order','web','订单列表',2,88,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(90,'order.order.view','web','订单列表（查看）',3,89,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(91,'order.order.create','web','创建订单',3,89,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(92,'order.order.edit','web','编辑订单',3,89,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(93,'order.order.cancel','web','取消订单',3,89,NULL,3,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(94,'order.order.lock','web','锁定订单',3,89,NULL,4,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(95,'order.order.change-price','web','改价',3,89,NULL,5,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(96,'order.order.delete','web','删除订单',3,89,NULL,6,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(97,'order.cart','web','购物车',2,88,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(98,'order.cart.view','web','购物车（查看）',3,97,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(99,'order.order-return','web','退货管理',2,88,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(100,'order.order-return.view','web','退货管理（查看）',3,99,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(101,'order.order-return.create','web','创建退货',3,99,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(102,'order.order-return.edit','web','编辑退货',3,99,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(103,'order.order-return.delete','web','删除退货',3,99,NULL,3,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(104,'inventory','web','库存管理',1,0,NULL,6,'warehouse','2026-08-12 21:05:32','2026-08-12 21:05:32'),(105,'inventory.warehouse','web','仓库管理',2,104,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(106,'inventory.warehouse.view','web','仓库管理（查看）',3,105,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(107,'inventory.warehouse.create','web','创建仓库',3,105,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(108,'inventory.warehouse.edit','web','编辑仓库',3,105,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(109,'inventory.warehouse.delete','web','删除仓库',3,105,NULL,3,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(110,'inventory.inventory','web','库存列表',2,104,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(111,'inventory.inventory.view','web','库存列表（查看）',3,110,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(112,'inventory.inventory-log','web','库存日志',2,104,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(113,'inventory.inventory-log.view','web','库存日志（查看）',3,112,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(114,'inventory.picking-task','web','拣货任务',2,104,NULL,3,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(115,'inventory.picking-task.view','web','拣货任务（查看）',3,114,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(116,'inventory.picking-task.create','web','生成拣货单',3,114,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(117,'inventory.picking-task.edit','web','编辑拣货任务',3,114,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(118,'inventory.picking-task.delete','web','删除拣货任务',3,114,NULL,3,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(119,'delivery','web','物流配送',1,0,NULL,7,'delivery-truck','2026-08-12 21:05:32','2026-08-12 21:05:32'),(120,'delivery.route','web','配送线路',2,119,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(121,'delivery.route.view','web','配送线路（查看）',3,120,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(122,'delivery.route.create','web','创建线路',3,120,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(123,'delivery.route.edit','web','编辑线路',3,120,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(124,'delivery.route.delete','web','删除线路',3,120,NULL,3,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(125,'delivery.route.stop-manage','web','管理商家排序',3,120,NULL,4,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(126,'delivery.delivery-task','web','配送任务',2,119,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(127,'delivery.delivery-task.view','web','配送任务（查看）',3,126,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(128,'delivery.delivery-task.assign','web','分配任务',3,126,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(129,'delivery.delivery-task.update','web','更新状态',3,126,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(130,'delivery.delivery-note','web','送货单',2,119,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(131,'delivery.delivery-note.view','web','送货单（查看）',3,130,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(132,'delivery.delivery-note.deliver','web','确认分货',3,130,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(133,'delivery.delivery-note.sign','web','确认签收',3,130,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(134,'delivery.delivery-note.cancel','web','取消送货单',3,130,NULL,3,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(135,'delivery.signature','web','签收管理',2,119,NULL,3,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(136,'delivery.signature.view','web','签收管理（查看）',3,135,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(137,'delivery.discrepancy','web','差异处理',2,119,NULL,4,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(138,'delivery.discrepancy.view','web','差异处理（查看）',3,137,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(139,'delivery.discrepancy.restock','web','补货',3,137,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(140,'delivery.discrepancy.refund','web','退款',3,137,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(141,'delivery.discrepancy.writeoff','web','报损',3,137,NULL,3,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(142,'delivery.temperature','web','温度记录',2,119,NULL,5,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(143,'delivery.temperature.view','web','温度记录（查看）',3,142,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(144,'delivery.vehicle-issue','web','车辆故障',2,119,NULL,6,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(145,'delivery.vehicle-issue.view','web','车辆故障（查看）',3,144,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(146,'delivery.vehicle-issue.create','web','创建故障',3,144,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(147,'delivery.vehicle-issue.edit','web','编辑故障',3,144,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(148,'delivery.vehicle-issue.delete','web','删除故障',3,144,NULL,3,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(149,'loss','web','损耗管理',1,0,NULL,8,'exclamation-triangle','2026-08-12 21:05:32','2026-08-12 21:05:32'),(150,'loss.loss-order','web','损耗单',2,149,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(151,'loss.loss-order.view','web','损耗单（查看）',3,150,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(152,'loss.loss-order.create','web','创建损耗单',3,150,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(153,'loss.loss-order.edit','web','编辑损耗单',3,150,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(154,'loss.loss-order.approve','web','审核损耗单',3,150,NULL,3,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(155,'loss.loss-order.execute','web','执行损耗',3,150,NULL,4,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(156,'loss.loss-order.close','web','关闭损耗单',3,150,NULL,5,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(157,'finance','web','财务管理',1,0,NULL,9,'banknote','2026-08-12 21:05:32','2026-08-12 21:05:32'),(158,'finance.recharge','web','充值管理',2,157,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(159,'finance.recharge.view','web','充值管理（查看）',3,158,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(160,'finance.recharge.create','web','创建充值',3,158,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(161,'finance.recharge.approve','web','审核充值',3,158,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(162,'finance.supplier-settlement','web','供应商结算',2,157,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(163,'finance.supplier-settlement.view','web','供应商结算（查看）',3,162,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(164,'finance.supplier-settlement.create','web','创建结算',3,162,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(165,'finance.supplier-settlement.pay','web','付款',3,162,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(166,'finance.receivable','web','应收管理',2,157,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(167,'finance.receivable.view','web','应收管理（查看）',3,166,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(168,'finance.receivable.collect','web','收款',3,166,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(169,'finance.receivable.approve','web','审核应收',3,166,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(170,'finance.invoice','web','发票管理',2,157,NULL,3,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(171,'finance.invoice.view','web','发票管理（查看）',3,170,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(172,'finance.invoice.create','web','创建发票',3,170,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(173,'finance.invoice.issue','web','开具发票',3,170,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(174,'finance.invoice.send','web','寄出发票',3,170,NULL,3,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(175,'price','web','价格管理',1,0,NULL,10,'calculator','2026-08-12 21:05:32','2026-08-12 21:05:32'),(176,'price.promotion','web','促销活动',2,175,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(177,'price.promotion.view','web','促销活动（查看）',3,176,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(178,'price.promotion.create','web','创建活动',3,176,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(179,'price.promotion.edit','web','编辑活动',3,176,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(180,'price.promotion.approve','web','审核活动',3,176,NULL,3,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(181,'price.promotion.toggle','web','启用/禁用',3,176,NULL,4,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(182,'price.pricing-config','web','取价配置',2,175,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(183,'price.pricing-config.view','web','取价配置（查看）',3,182,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(184,'price.pricing-config.edit','web','编辑取价配置',3,182,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(185,'price.price-change-log','web','改价记录',2,175,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(186,'price.price-change-log.view','web','改价记录（查看）',3,185,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(187,'price.price-apportionment','web','费用均摊',2,175,NULL,3,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(188,'price.price-apportionment.view','web','费用均摊（查看）',3,187,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(189,'system','web','系统管理',1,0,NULL,11,'settings','2026-08-12 21:05:32','2026-08-12 21:05:32'),(190,'system.system-config','web','系统配置',2,189,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(191,'system.system-config.view','web','系统配置（查看）',3,190,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(192,'system.system-config.edit','web','编辑配置',3,190,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(193,'system.audit-log','web','审计日志',2,189,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(194,'system.audit-log.view','web','审计日志（查看）',3,193,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(195,'system.login-log','web','登录日志',2,189,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(196,'system.login-log.view','web','登录日志（查看）',3,195,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(197,'system.banner','web','轮播管理',2,189,NULL,3,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(198,'system.banner.view','web','轮播管理（查看）',3,197,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(199,'system.banner.create','web','创建轮播',3,197,NULL,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(200,'system.banner.edit','web','编辑轮播',3,197,NULL,2,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(201,'system.banner.delete','web','删除轮播',3,197,NULL,3,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(202,'system.wechat-user','web','微信用户',2,189,NULL,4,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(203,'system.wechat-user.view','web','微信用户（查看）',3,202,NULL,0,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32');
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
  `merchant_id` bigint unsigned DEFAULT NULL COMMENT '商家ID（方便按商家分组汇总）',
  `required_quantity` bigint NOT NULL DEFAULT '0' COMMENT '需求数量',
  `picked_quantity` bigint NOT NULL DEFAULT '0' COMMENT '实际拣货数量',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：1待拣货，2已拣货，3差异',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `picking_task_items_picking_task_id_index` (`picking_task_id`),
  KEY `picking_task_items_order_id_index` (`order_id`),
  KEY `picking_task_items_sku_id_index` (`sku_id`),
  KEY `picking_task_items_merchant_id_index` (`merchant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='拣货任务明细表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `picking_task_items`
--

LOCK TABLES `picking_task_items` WRITE;
/*!40000 ALTER TABLE `picking_task_items` DISABLE KEYS */;
INSERT INTO `picking_task_items` VALUES (1,1,1,1,1,1,2000,2000,2,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(2,1,1,2,2,1,500,500,2,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(3,2,2,3,4,3,500,300,3,'2026-08-12 21:11:50','2026-08-12 21:11:50');
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
  `route_id` bigint unsigned DEFAULT NULL COMMENT '所属配送线路ID',
  `delivery_date` date DEFAULT NULL COMMENT '送达日期',
  `picker_id` bigint unsigned DEFAULT NULL COMMENT '拣货员ID',
  `batch` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '配送批次：1上午，2下午',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：1待分配，2拣货中，3已完成',
  `total_skus` int unsigned NOT NULL DEFAULT '0' COMMENT 'SKU种数汇总',
  `total_quantity` bigint NOT NULL DEFAULT '0' COMMENT '总数量汇总',
  `started_at` timestamp NULL DEFAULT NULL COMMENT '开始时间',
  `completed_at` timestamp NULL DEFAULT NULL COMMENT '完成时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `picking_tasks_task_no_unique` (`task_no`),
  KEY `picking_tasks_warehouse_id_index` (`warehouse_id`),
  KEY `picking_tasks_route_id_index` (`route_id`),
  KEY `picking_tasks_delivery_date_index` (`delivery_date`),
  KEY `picking_tasks_route_id_delivery_date_index` (`route_id`,`delivery_date`),
  KEY `picking_tasks_picker_id_index` (`picker_id`),
  KEY `picking_tasks_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='拣货任务表（按线路生成，含SKU汇总）';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `picking_tasks`
--

LOCK TABLES `picking_tasks` WRITE;
/*!40000 ALTER TABLE `picking_tasks` DISABLE KEYS */;
INSERT INTO `picking_tasks` VALUES (1,'PK-E01-20260810-001',1,1,'2026-08-10',8,1,3,2,2500,'2026-08-11 21:00:00','2026-08-11 21:45:00','2026-08-12 21:11:50','2026-08-12 21:11:50'),(2,'PK-E01-20260811-001',1,1,'2026-08-11',8,1,2,1,500,'2026-08-12 21:00:00',NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(3,'PK-E02-20260811-001',1,2,'2026-08-11',NULL,1,1,0,0,NULL,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='价格/费用均摊记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `price_apportionments`
--

LOCK TABLES `price_apportionments` WRITE;
/*!40000 ALTER TABLE `price_apportionments` DISABLE KEYS */;
INSERT INTO `price_apportionments` VALUES (1,1,1,NULL,3,5000,1,0,3,2,'2026-08-12 21:11:51','2026-08-12 21:11:51');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='改价/促销记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `price_change_logs`
--

LOCK TABLES `price_change_logs` WRITE;
/*!40000 ALTER TABLE `price_change_logs` DISABLE KEYS */;
INSERT INTO `price_change_logs` VALUES (1,3,NULL,1,1,NULL,9200,9000,2000,-4000,3,NULL,'老客户蔬菜优惠折扣',NULL,NULL,'2026-08-12 21:11:51');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='价格策略主表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `price_strategies`
--

LOCK TABLES `price_strategies` WRITE;
/*!40000 ALTER TABLE `price_strategies` DISABLE KEYS */;
INSERT INTO `price_strategies` VALUES (1,'老客户蔬菜优惠','PS-VIP-001',1,2,2,1,2,'2026-08-12 21:11:51','2026-11-12 21:11:51',3,'老客户蔬菜类9折优惠','2026-08-12 21:11:51','2026-08-12 21:11:51',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='价格策略明细表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `price_strategy_items`
--

LOCK TABLES `price_strategy_items` WRITE;
/*!40000 ALTER TABLE `price_strategy_items` DISABLE KEYS */;
INSERT INTO `price_strategy_items` VALUES (1,1,1,1,NULL,NULL,2,0,9000,10000,0,NULL,NULL,1,'2026-08-12 21:11:51','2026-08-12 21:11:51');
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品图片表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_images`
--

LOCK TABLES `product_images` WRITE;
/*!40000 ALTER TABLE `product_images` DISABLE KEYS */;
INSERT INTO `product_images` VALUES (1,1,'/uploads/products/baicai-1.jpg',1,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(2,1,'/uploads/products/baicai-2.jpg',2,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(3,4,'/uploads/products/wuhua-1.jpg',1,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(4,5,'/uploads/products/xianxia-1.jpg',1,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(5,5,'/uploads/products/xianxia-2.jpg',2,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(6,5,'/uploads/products/xianxia-3.jpg',3,'2026-08-12 21:11:50','2026-08-12 21:11:50');
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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品标签关联表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_tags`
--

LOCK TABLES `product_tags` WRITE;
/*!40000 ALTER TABLE `product_tags` DISABLE KEYS */;
INSERT INTO `product_tags` VALUES (1,1,1,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(2,1,2,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(3,1,3,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(4,1,5,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(5,4,1,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(6,4,2,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(7,4,3,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(8,4,5,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(9,5,1,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(10,5,2,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(11,5,3,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(12,5,5,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(13,6,1,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(14,6,2,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(15,6,3,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(16,6,5,'2026-08-12 21:11:50','2026-08-12 21:11:50');
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
INSERT INTO `products` VALUES (1,1,NULL,'大白菜',NULL,'斤',1,0,1,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(2,1,NULL,'土豆',NULL,'斤',1,0,1,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(3,1,NULL,'西红柿',NULL,'斤',1,0,1,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(4,3,NULL,'五花肉',NULL,'斤',1,0,1,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(5,4,NULL,'鲜虾',NULL,'斤',1,0,1,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(6,5,NULL,'金龙鱼大豆油',NULL,'桶',0,0,1,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promotion_bundle_items`
--

DROP TABLE IF EXISTS `promotion_bundle_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `promotion_bundle_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `bundle_id` bigint unsigned NOT NULL COMMENT '套餐ID',
  `sku_id` bigint unsigned NOT NULL COMMENT 'SKU ID',
  `quantity` int unsigned NOT NULL DEFAULT '1' COMMENT '数量',
  `sort` int unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `promotion_bundle_items_bundle_id_index` (`bundle_id`),
  KEY `promotion_bundle_items_sku_id_index` (`sku_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='套餐明细表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promotion_bundle_items`
--

LOCK TABLES `promotion_bundle_items` WRITE;
/*!40000 ALTER TABLE `promotion_bundle_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `promotion_bundle_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promotion_bundles`
--

DROP TABLE IF EXISTS `promotion_bundles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `promotion_bundles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `promotion_id` bigint unsigned NOT NULL COMMENT '促销活动ID',
  `bundle_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '套餐名称',
  `bundle_price` bigint NOT NULL DEFAULT '0' COMMENT '套餐价（厘）',
  `original_total` bigint NOT NULL DEFAULT '0' COMMENT '原价合计（厘）',
  `bundle_quantity` int unsigned NOT NULL DEFAULT '1' COMMENT '每组最低件数',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：0禁用，1启用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `promotion_bundles_promotion_id_index` (`promotion_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='组合套餐表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promotion_bundles`
--

LOCK TABLES `promotion_bundles` WRITE;
/*!40000 ALTER TABLE `promotion_bundles` DISABLE KEYS */;
/*!40000 ALTER TABLE `promotion_bundles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promotion_clearances`
--

DROP TABLE IF EXISTS `promotion_clearances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `promotion_clearances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `promotion_id` bigint unsigned NOT NULL COMMENT '促销活动ID',
  `clearance_type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '类型：1清仓，2临期',
  `expiry_date` date DEFAULT NULL COMMENT '临期截止日期',
  `discount_rate` int NOT NULL DEFAULT '10000' COMMENT '折扣率（万分比）',
  `fixed_price` bigint NOT NULL DEFAULT '0' COMMENT '清仓固定价（厘）',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：0禁用，1启用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `promotion_clearances_promotion_id_index` (`promotion_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='清仓临期活动表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promotion_clearances`
--

LOCK TABLES `promotion_clearances` WRITE;
/*!40000 ALTER TABLE `promotion_clearances` DISABLE KEYS */;
/*!40000 ALTER TABLE `promotion_clearances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promotion_coupons`
--

DROP TABLE IF EXISTS `promotion_coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `promotion_coupons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `promotion_id` bigint unsigned NOT NULL COMMENT '促销活动ID',
  `coupon_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '优惠券编码',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '优惠券名称',
  `coupon_type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '类型：1满减券，2折扣券，3抵扣券，4运费券',
  `threshold_amount` bigint NOT NULL DEFAULT '0' COMMENT '使用门槛金额（厘）',
  `reduction_amount` bigint NOT NULL DEFAULT '0' COMMENT '抵扣金额（厘）',
  `discount_rate` int NOT NULL DEFAULT '10000' COMMENT '折扣率（万分比）',
  `max_discount` bigint NOT NULL DEFAULT '0' COMMENT '最大优惠上限（厘）',
  `total_quantity` int unsigned NOT NULL DEFAULT '0' COMMENT '发放总量',
  `claimed_quantity` int unsigned NOT NULL DEFAULT '0' COMMENT '已领取数量',
  `used_quantity` int unsigned NOT NULL DEFAULT '0' COMMENT '已使用数量',
  `per_user_limit` int unsigned NOT NULL DEFAULT '1' COMMENT '每人限领',
  `valid_days` int unsigned NOT NULL DEFAULT '30' COMMENT '领取后有效天数',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：0禁用，1启用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `promotion_coupons_promotion_id_index` (`promotion_id`),
  KEY `promotion_coupons_coupon_code_index` (`coupon_code`),
  KEY `promotion_coupons_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='优惠券表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promotion_coupons`
--

LOCK TABLES `promotion_coupons` WRITE;
/*!40000 ALTER TABLE `promotion_coupons` DISABLE KEYS */;
/*!40000 ALTER TABLE `promotion_coupons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promotion_flash_sales`
--

DROP TABLE IF EXISTS `promotion_flash_sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `promotion_flash_sales` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `promotion_id` bigint unsigned NOT NULL COMMENT '促销活动ID',
  `flash_price` bigint NOT NULL DEFAULT '0' COMMENT '秒杀价（厘）',
  `total_stock` int unsigned NOT NULL DEFAULT '0' COMMENT '秒杀总库存',
  `sold_stock` int unsigned NOT NULL DEFAULT '0' COMMENT '已售库存',
  `per_user_limit` int unsigned NOT NULL DEFAULT '1' COMMENT '每人限购',
  `flash_start_at` timestamp NULL DEFAULT NULL COMMENT '秒杀开始时间',
  `flash_end_at` timestamp NULL DEFAULT NULL COMMENT '秒杀结束时间',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：0禁用，1启用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `promotion_flash_sales_promotion_id_index` (`promotion_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='秒杀活动表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promotion_flash_sales`
--

LOCK TABLES `promotion_flash_sales` WRITE;
/*!40000 ALTER TABLE `promotion_flash_sales` DISABLE KEYS */;
/*!40000 ALTER TABLE `promotion_flash_sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promotion_full_reductions`
--

DROP TABLE IF EXISTS `promotion_full_reductions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `promotion_full_reductions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `promotion_id` bigint unsigned NOT NULL COMMENT '促销活动ID',
  `threshold_type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '门槛类型：1按金额，2按件数',
  `threshold_amount` bigint NOT NULL DEFAULT '0' COMMENT '门槛金额/件数',
  `reduction_type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '减免方式：1固定减，2折扣率，3赠品',
  `reduction_amount` bigint NOT NULL DEFAULT '0' COMMENT '减免金额（厘）',
  `discount_rate` int NOT NULL DEFAULT '10000' COMMENT '折扣率（万分比）',
  `gift_sku_id` bigint unsigned DEFAULT NULL COMMENT '赠品SKU ID',
  `gift_quantity` int unsigned NOT NULL DEFAULT '0' COMMENT '赠品数量',
  `is_stacked` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '是否可叠加：0否，1是',
  `sort` int unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `promotion_full_reductions_promotion_id_index` (`promotion_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='满减活动表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promotion_full_reductions`
--

LOCK TABLES `promotion_full_reductions` WRITE;
/*!40000 ALTER TABLE `promotion_full_reductions` DISABLE KEYS */;
/*!40000 ALTER TABLE `promotion_full_reductions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promotion_group_buys`
--

DROP TABLE IF EXISTS `promotion_group_buys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `promotion_group_buys` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `promotion_id` bigint unsigned NOT NULL COMMENT '促销活动ID',
  `group_price` bigint NOT NULL DEFAULT '0' COMMENT '拼团价（厘）',
  `min_group_size` int unsigned NOT NULL DEFAULT '2' COMMENT '最少成团人数',
  `max_group_size` int unsigned NOT NULL DEFAULT '10' COMMENT '最多拼团人数',
  `time_limit` int unsigned NOT NULL DEFAULT '1440' COMMENT '拼团时限（分钟）',
  `virtual_join` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '虚拟凑团：0否，1是',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：0禁用，1启用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `promotion_group_buys_promotion_id_index` (`promotion_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='拼团活动表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promotion_group_buys`
--

LOCK TABLES `promotion_group_buys` WRITE;
/*!40000 ALTER TABLE `promotion_group_buys` DISABLE KEYS */;
/*!40000 ALTER TABLE `promotion_group_buys` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promotion_member_discounts`
--

DROP TABLE IF EXISTS `promotion_member_discounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `promotion_member_discounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `promotion_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '促销活动ID（0=全局常驻规则）',
  `member_level` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '会员等级：1普通，2银卡，3金卡，4钻石',
  `discount_rate` int NOT NULL DEFAULT '10000' COMMENT '折扣率（万分比，9500=95折）',
  `is_permanent` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '是否常驻：0否，1是',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：0禁用，1启用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `promotion_member_discounts_promotion_id_index` (`promotion_id`),
  KEY `promotion_member_discounts_member_level_index` (`member_level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='会员等级折扣表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promotion_member_discounts`
--

LOCK TABLES `promotion_member_discounts` WRITE;
/*!40000 ALTER TABLE `promotion_member_discounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `promotion_member_discounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promotion_skus`
--

DROP TABLE IF EXISTS `promotion_skus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `promotion_skus` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `promotion_id` bigint unsigned NOT NULL COMMENT '促销活动ID',
  `sku_id` bigint unsigned NOT NULL COMMENT 'SKU ID',
  `price_type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '定价方式：1固定价，2折扣率',
  `fixed_price` bigint NOT NULL DEFAULT '0' COMMENT '促销固定单价（厘）',
  `discount_rate` int NOT NULL DEFAULT '10000' COMMENT '折扣率（万分比）',
  `second_price_type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '第二件定价：1无，2固定价，3折扣率',
  `second_fixed_price` bigint NOT NULL DEFAULT '0' COMMENT '第二件固定单价（厘）',
  `second_discount_rate` int NOT NULL DEFAULT '10000' COMMENT '第二件折扣率（万分比）',
  `max_quantity` int unsigned NOT NULL DEFAULT '0' COMMENT '限购数量',
  `max_per_customer` int unsigned NOT NULL DEFAULT '0' COMMENT '每人限购',
  `stock_limit` int unsigned NOT NULL DEFAULT '0' COMMENT '活动库存限量',
  `sort` int unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：0禁用，1启用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `promotion_skus_promotion_id_index` (`promotion_id`),
  KEY `promotion_skus_sku_id_index` (`sku_id`),
  KEY `promotion_skus_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='活动商品明细表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promotion_skus`
--

LOCK TABLES `promotion_skus` WRITE;
/*!40000 ALTER TABLE `promotion_skus` DISABLE KEYS */;
/*!40000 ALTER TABLE `promotion_skus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promotion_stores`
--

DROP TABLE IF EXISTS `promotion_stores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `promotion_stores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `promotion_id` bigint unsigned NOT NULL COMMENT '促销活动ID',
  `store_id` bigint unsigned NOT NULL COMMENT '门店/商家ID',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_promo_store` (`promotion_id`,`store_id`),
  KEY `promotion_stores_promotion_id_index` (`promotion_id`),
  KEY `promotion_stores_store_id_index` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='活动-门店关联表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promotion_stores`
--

LOCK TABLES `promotion_stores` WRITE;
/*!40000 ALTER TABLE `promotion_stores` DISABLE KEYS */;
/*!40000 ALTER TABLE `promotion_stores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promotions`
--

DROP TABLE IF EXISTS `promotions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `promotions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '活动名称',
  `promo_type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '促销类型：1普通促销，2满减，3优惠券，4组合套餐，5清仓临期，6拼团，7秒杀，8会员折扣',
  `promo_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '活动编码',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT '活动描述',
  `scope_type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '适用范围：1全场，2指定分类，3指定商品',
  `start_at` timestamp NULL DEFAULT NULL COMMENT '开始时间',
  `end_at` timestamp NULL DEFAULT NULL COMMENT '结束时间',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：0禁用，1启用',
  `approval_status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '审核状态：1待审核，2已通过，3已拒绝',
  `created_by` bigint unsigned DEFAULT NULL COMMENT '创建人ID',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `promotions_promo_code_unique` (`promo_code`),
  KEY `promotions_promo_type_index` (`promo_type`),
  KEY `promotions_status_index` (`status`),
  KEY `promotions_approval_status_index` (`approval_status`),
  KEY `promotions_start_at_end_at_index` (`start_at`,`end_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='促销活动主表';
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
  `supplier_id` bigint unsigned DEFAULT NULL COMMENT '供应商ID',
  `quantity` bigint NOT NULL DEFAULT '0' COMMENT '待采数量',
  `expected_price` bigint NOT NULL DEFAULT '0' COMMENT '预估成本价（厘）',
  `source_type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '来源：1订单汇总，2手工添加',
  `source_id` bigint unsigned DEFAULT NULL COMMENT '来源业务ID',
  `purchase_order_id` bigint unsigned DEFAULT NULL COMMENT '关联采购单ID',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：1待生成采购单，2已生成采购单',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_items_sku_id_index` (`sku_id`),
  KEY `purchase_items_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='待采清单表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_items`
--

LOCK TABLES `purchase_items` WRITE;
/*!40000 ALTER TABLE `purchase_items` DISABLE KEYS */;
INSERT INTO `purchase_items` VALUES (1,5,5,1000,350000,1,NULL,3,2,'2026-08-12 21:11:50','2026-08-12 22:15:06'),(2,4,4,55,130000,1,NULL,4,2,'2026-08-12 22:14:44','2026-08-12 22:15:06');
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
  `quantity` bigint NOT NULL DEFAULT '0' COMMENT '采购数量（base_unit 最小单位）',
  `unit_id` bigint unsigned DEFAULT NULL COMMENT '采购时选择的单位ID',
  `unit_quantity` bigint NOT NULL DEFAULT '0' COMMENT '采购时选择的单位数量（如选"箱"输入10，此字段=10）',
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
  `discrepancy_quantity` bigint NOT NULL DEFAULT '0' COMMENT '入库差异数量（采购数量-实际入库数量）',
  `returned_quantity` bigint NOT NULL DEFAULT '0' COMMENT '已退货数量',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '明细备注',
  `loss_order_id` bigint unsigned DEFAULT NULL COMMENT '关联损耗单ID',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_order_items_purchase_order_id_index` (`purchase_order_id`),
  KEY `purchase_order_items_sku_id_index` (`sku_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='采购单明细表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_order_items`
--

LOCK TABLES `purchase_order_items` WRITE;
/*!40000 ALTER TABLE `purchase_order_items` DISABLE KEYS */;
INSERT INTO `purchase_order_items` VALUES (1,1,1,50000,NULL,0,8000,50000,8000,400000,400000,0,0,NULL,NULL,NULL,0,0,NULL,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(2,1,2,3000,NULL,0,12000,2800,12000,36000,33600,0,0,NULL,NULL,'运输损耗5%',0,0,NULL,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(3,1,3,2000,NULL,0,25000,2000,25000,50000,50000,0,0,NULL,NULL,NULL,0,0,NULL,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(4,2,4,2000,NULL,0,130000,0,0,260000,0,0,0,NULL,NULL,NULL,0,0,NULL,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(5,3,5,1000,NULL,0,350000,1000,350000,350000,350000,0,0,NULL,NULL,'',0,0,NULL,NULL,'2026-08-12 22:15:06','2026-08-12 22:16:06'),(6,4,4,55,NULL,0,130000,55,130000,7150,7150,0,0,NULL,NULL,'',0,0,NULL,NULL,'2026-08-12 22:15:06','2026-08-12 22:16:25');
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
  `purchase_date` date DEFAULT NULL COMMENT '采购日期',
  `order_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '采购单号',
  `supplier_id` bigint unsigned NOT NULL COMMENT '供应商ID',
  `warehouse_id` bigint unsigned DEFAULT NULL COMMENT '入库目标仓库',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：1待接单，2备货中，3已发货，4已入库，5完成，9取消',
  `return_status` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '退货状态：0无退货，1部分退货，2全部退货',
  `total_amount` bigint NOT NULL DEFAULT '0' COMMENT '总金额',
  `actual_amount` bigint NOT NULL DEFAULT '0' COMMENT '实际入库金额',
  `operator_id` bigint unsigned DEFAULT NULL COMMENT '经办人',
  `approved_by` bigint unsigned DEFAULT NULL COMMENT '审核人ID',
  `approved_at` timestamp NULL DEFAULT NULL COMMENT '审核时间',
  `ordered_at` timestamp NULL DEFAULT NULL COMMENT '下单时间',
  `shipped_at` timestamp NULL DEFAULT NULL COMMENT '发货时间',
  `stocked_at` timestamp NULL DEFAULT NULL COMMENT '入库时间',
  `completed_at` timestamp NULL DEFAULT NULL COMMENT '完成时间',
  `cancelled_at` timestamp NULL DEFAULT NULL COMMENT '取消时间',
  `cancel_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '取消原因',
  `remark` text COLLATE utf8mb4_unicode_ci COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchase_orders_order_no_unique` (`order_no`),
  KEY `purchase_orders_supplier_id_index` (`supplier_id`),
  KEY `purchase_orders_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='采购单表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_orders`
--

LOCK TABLES `purchase_orders` WRITE;
/*!40000 ALTER TABLE `purchase_orders` DISABLE KEYS */;
INSERT INTO `purchase_orders` VALUES (1,NULL,'PO-20260725-001',3,NULL,5,0,450000,440000,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'日常蔬菜采购','2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(2,NULL,'PO-20260728-002',4,NULL,2,0,260000,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'肉类补货','2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(3,NULL,'PO202608130615061594',5,1,5,0,350000,350000,1,NULL,NULL,'2026-08-12 22:15:06','2026-08-12 22:15:54','2026-08-12 22:16:06','2026-08-12 22:16:12',NULL,NULL,NULL,'2026-08-12 22:15:06','2026-08-12 22:16:12',NULL),(4,NULL,'PO202608130615065447',4,2,5,0,7150,7150,1,NULL,NULL,'2026-08-12 22:15:06','2026-08-12 22:15:45','2026-08-12 22:16:25','2026-08-12 22:16:29',NULL,NULL,NULL,'2026-08-12 22:15:06','2026-08-12 22:16:29',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='采购退货明细表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_return_items`
--

LOCK TABLES `purchase_return_items` WRITE;
/*!40000 ALTER TABLE `purchase_return_items` DISABLE KEYS */;
INSERT INTO `purchase_return_items` VALUES (1,1,2,2,200,12000,2400000,0,0,0,'运输损耗5%','2026-08-12 21:11:50','2026-08-12 21:11:50');
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
  `shipped_at` timestamp NULL DEFAULT NULL COMMENT '出库时间',
  `completed_at` timestamp NULL DEFAULT NULL COMMENT '完成时间',
  `cancelled_at` timestamp NULL DEFAULT NULL COMMENT '取消时间',
  `remark` text COLLATE utf8mb4_unicode_ci COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchase_returns_return_no_unique` (`return_no`),
  KEY `purchase_returns_purchase_order_id_index` (`purchase_order_id`),
  KEY `purchase_returns_supplier_id_index` (`supplier_id`),
  KEY `purchase_returns_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='采购退货单表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_returns`
--

LOCK TABLES `purchase_returns` WRITE;
/*!40000 ALTER TABLE `purchase_returns` DISABLE KEYS */;
INSERT INTO `purchase_returns` VALUES (1,'PR-20260728-001',1,3,1,2,33600,0,'土豆运输损耗退货',3,4,'2026-08-12 21:11:50',NULL,NULL,NULL,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='收款记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `receivable_payments`
--

LOCK TABLES `receivable_payments` WRITE;
/*!40000 ALTER TABLE `receivable_payments` DISABLE KEYS */;
INSERT INTO `receivable_payments` VALUES (1,1,23000,1,'RP-20260728-001',6,2,NULL,'余额扣款','2026-08-12 21:11:51','2026-08-12 21:11:51');
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='应收账款表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `receivables`
--

LOCK TABLES `receivables` WRITE;
/*!40000 ALTER TABLE `receivables` DISABLE KEYS */;
INSERT INTO `receivables` VALUES (1,'RCV-20260728-001',1,1,23000,23000,0,0,0,23000,3,1,NULL,'2026-08-12 21:11:51',NULL,NULL,2,'2026-08-12 21:11:51','2026-08-12 21:11:51'),(2,'RCV-20260728-002',2,3,74500,74500,0,0,0,0,1,2,'2026-08-28',NULL,NULL,NULL,2,'2026-08-12 21:11:51','2026-08-12 21:11:51');
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='充值记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recharges`
--

LOCK TABLES `recharges` WRITE;
/*!40000 ALTER TABLE `recharges` DISABLE KEYS */;
INSERT INTO `recharges` VALUES (1,1,5000000,3,'RCH-20260720-001',2,2,3,'示例充值','2026-08-12 21:11:51','2026-08-12 21:11:51'),(2,3,3000000,2,'RCH-20260722-001',2,2,3,'示例充值','2026-08-12 21:11:51','2026-08-12 21:11:51'),(3,2,10000000,3,'RCH-20260725-001',1,1,3,'示例充值','2026-08-12 21:11:51','2026-08-12 21:11:51');
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='复购模板明细表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `repurchase_template_items`
--

LOCK TABLES `repurchase_template_items` WRITE;
/*!40000 ALTER TABLE `repurchase_template_items` DISABLE KEYS */;
INSERT INTO `repurchase_template_items` VALUES (1,1,1,2000,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(2,1,2,1000,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(3,1,3,500,'2026-08-12 21:11:50','2026-08-12 21:11:50');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='复购模板表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `repurchase_templates`
--

LOCK TABLES `repurchase_templates` WRITE;
/*!40000 ALTER TABLE `repurchase_templates` DISABLE KEYS */;
INSERT INTO `repurchase_templates` VALUES (1,1,'日常蔬菜采购',1,'2026-08-12 21:11:50','2026-08-12 21:11:50');
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='智能补货提醒规则表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `restock_reminders`
--

LOCK TABLES `restock_reminders` WRITE;
/*!40000 ALTER TABLE `restock_reminders` DISABLE KEYS */;
INSERT INTO `restock_reminders` VALUES (1,1,1,1000,1,NULL,1,'2026-08-12 21:11:51','2026-08-12 21:11:51'),(2,1,4,200,2,NULL,1,'2026-08-12 21:11:51','2026-08-12 21:11:51'),(3,1,5,100,1,NULL,1,'2026-08-12 21:11:51','2026-08-12 21:11:51');
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
INSERT INTO `role_has_permissions` VALUES (1,1),(1,2),(1,3),(1,4),(1,5),(1,6),(1,7),(1,8),(1,9),(2,1),(3,1),(3,2),(3,3),(4,1),(4,2),(4,3),(5,1),(6,1),(7,1),(8,1),(9,1),(10,1),(11,1),(12,1),(13,1),(14,1),(15,1),(16,1),(17,1),(18,1),(19,1),(20,1),(21,1),(22,1),(23,1),(24,1),(24,2),(24,3),(25,1),(25,2),(25,3),(26,1),(26,2),(26,3),(27,1),(27,2),(27,3),(28,1),(29,1),(29,2),(29,3),(30,1),(30,2),(30,3),(31,1),(31,2),(31,3),(32,1),(32,2),(32,3),(33,1),(34,1),(34,2),(34,3),(35,1),(35,2),(35,3),(36,1),(36,2),(36,3),(37,1),(37,2),(37,3),(38,1),(39,1),(39,2),(39,3),(40,1),(40,2),(40,3),(41,1),(41,2),(41,3),(42,1),(42,2),(42,3),(43,1),(44,1),(45,1),(45,2),(45,3),(46,1),(46,2),(46,3),(47,1),(47,2),(47,3),(48,1),(48,2),(48,3),(49,1),(50,1),(50,2),(50,3),(51,1),(51,2),(51,3),(52,1),(52,2),(52,3),(53,1),(53,2),(53,3),(54,1),(55,1),(55,2),(55,3),(56,1),(56,2),(56,3),(57,1),(57,2),(57,3),(58,1),(58,2),(58,3),(59,1),(60,1),(60,2),(60,3),(61,1),(61,2),(61,3),(62,1),(62,2),(62,3),(63,1),(63,2),(63,3),(64,1),(65,1),(65,2),(65,3),(66,1),(66,2),(66,3),(67,1),(68,1),(69,1),(70,1),(71,1),(71,2),(71,3),(71,7),(72,1),(72,2),(72,3),(72,7),(73,1),(74,1),(75,1),(76,1),(77,1),(77,3),(78,1),(78,2),(78,3),(79,1),(79,2),(79,3),(80,1),(81,1),(82,1),(83,1),(83,2),(83,3),(84,1),(84,2),(84,3),(85,1),(86,1),(87,1),(88,1),(89,1),(89,2),(89,3),(89,8),(89,9),(90,1),(90,2),(90,3),(90,8),(90,9),(91,1),(91,2),(91,3),(92,1),(92,2),(92,3),(93,1),(93,2),(93,3),(94,1),(94,2),(94,3),(95,1),(95,2),(95,3),(96,1),(96,2),(96,3),(97,1),(97,2),(97,3),(97,9),(98,1),(99,1),(99,2),(99,3),(99,9),(100,1),(100,2),(100,3),(100,9),(101,1),(101,2),(101,3),(102,1),(102,2),(102,3),(103,1),(104,1),(105,1),(105,2),(105,3),(105,7),(106,1),(106,2),(106,3),(106,7),(107,1),(108,1),(109,1),(110,1),(110,2),(110,3),(110,7),(111,1),(111,2),(111,3),(111,7),(112,1),(112,2),(112,3),(112,7),(113,1),(113,2),(113,3),(113,7),(114,1),(114,7),(115,1),(115,7),(116,1),(116,7),(117,1),(117,7),(118,1),(119,1),(120,1),(120,2),(120,3),(120,8),(121,1),(121,2),(121,3),(121,8),(122,1),(122,2),(122,3),(123,1),(123,2),(123,3),(124,1),(125,1),(125,2),(125,3),(126,1),(126,2),(126,3),(126,8),(127,1),(127,2),(127,3),(127,8),(128,1),(128,3),(129,1),(129,3),(129,8),(130,1),(130,2),(130,3),(130,8),(131,1),(131,2),(131,3),(131,8),(132,1),(132,3),(132,8),(133,1),(133,3),(133,8),(134,1),(135,1),(135,2),(135,3),(135,8),(135,9),(136,1),(136,2),(136,3),(136,8),(136,9),(137,1),(137,2),(137,3),(137,8),(138,1),(138,2),(138,3),(138,8),(139,1),(139,3),(140,1),(140,3),(141,1),(141,3),(142,1),(142,2),(142,3),(142,8),(143,1),(143,2),(143,3),(143,8),(144,1),(144,2),(144,3),(144,8),(145,1),(145,2),(145,3),(145,8),(146,1),(146,2),(146,3),(147,1),(147,2),(147,3),(147,8),(148,1),(148,3),(149,1),(150,1),(150,2),(150,3),(150,7),(151,1),(151,2),(151,3),(151,7),(152,1),(152,2),(152,3),(153,1),(154,1),(154,3),(155,1),(155,3),(156,1),(156,3),(157,1),(158,1),(158,4),(158,5),(158,6),(158,9),(159,1),(159,4),(159,5),(159,6),(159,9),(160,1),(160,4),(160,5),(160,6),(161,1),(161,6),(162,1),(162,4),(162,5),(162,6),(163,1),(163,4),(163,5),(163,6),(164,1),(164,4),(164,6),(165,1),(165,5),(165,6),(166,1),(166,4),(166,5),(166,6),(167,1),(167,4),(167,5),(167,6),(168,1),(168,4),(168,5),(168,6),(169,1),(169,6),(170,1),(170,4),(170,5),(170,6),(170,9),(171,1),(171,4),(171,5),(171,6),(171,9),(172,1),(172,4),(172,6),(173,1),(173,4),(173,6),(174,1),(174,6),(175,1),(176,1),(176,2),(176,3),(177,1),(177,2),(177,3),(178,1),(178,2),(178,3),(179,1),(179,2),(179,3),(180,1),(180,3),(181,1),(182,1),(182,3),(183,1),(183,3),(184,1),(185,1),(185,2),(185,3),(185,4),(185,6),(186,1),(186,2),(186,3),(186,4),(186,6),(187,1),(187,3),(187,4),(187,6),(188,1),(188,3),(188,4),(188,6),(189,1),(190,1),(191,1),(192,1),(193,1),(193,3),(193,4),(193,6),(194,1),(194,3),(194,4),(194,6),(195,1),(195,6),(196,1),(196,6),(197,1),(197,2),(197,3),(198,1),(198,2),(198,3),(199,1),(199,2),(199,3),(200,1),(200,2),(200,3),(201,1),(202,1),(203,1);
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
INSERT INTO `roles` VALUES (1,'super_admin','web','超级管理员','全部功能、系统配置、账号管理','2026-08-12 21:05:26','2026-08-12 21:05:26'),(2,'operator','web','运营专员','商品、订单、商家、供应商管理','2026-08-12 21:05:26','2026-08-12 21:05:26'),(3,'operator_manager','web','运营经理','运营审核、商品/订单/价格策略审核确认','2026-08-12 21:05:26','2026-08-12 21:05:26'),(4,'finance','web','财务专员','应收、结算、发票、审计','2026-08-12 21:05:26','2026-08-12 21:05:26'),(5,'cashier','web','出纳','付款录入、收款录入、资金操作执行','2026-08-12 21:05:26','2026-08-12 21:05:26'),(6,'finance_manager','web','财务经理','财务审核、付款/收款/结算单据复核确认','2026-08-12 21:05:26','2026-08-12 21:05:26'),(7,'picker','web','拣货员','拣货任务、称重改价','2026-08-12 21:05:26','2026-08-12 21:05:26'),(8,'driver','web','配送司机','配送任务、轨迹、签收','2026-08-12 21:05:26','2026-08-12 21:05:26'),(9,'merchant','web','商家','小程序商家端','2026-08-12 21:05:26','2026-08-12 21:05:26');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='付款记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settlement_payments`
--

LOCK TABLES `settlement_payments` WRITE;
/*!40000 ALTER TABLE `settlement_payments` DISABLE KEYS */;
INSERT INTO `settlement_payments` VALUES (1,1,435000,1,'PAY-20260728-001',6,2,NULL,'银行转账付款','2026-08-12 21:11:51','2026-08-12 21:11:51');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='签收存证表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `signatures`
--

LOCK TABLES `signatures` WRITE;
/*!40000 ALTER TABLE `signatures` DISABLE KEYS */;
INSERT INTO `signatures` VALUES (1,1,1,1,'/uploads/signatures/demo-sign-001.jpg','吴老板','2026-08-11 23:20:00','2026-08-12 21:11:50','2026-08-12 21:11:50');
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='SKU条码表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sku_barcodes`
--

LOCK TABLES `sku_barcodes` WRITE;
/*!40000 ALTER TABLE `sku_barcodes` DISABLE KEYS */;
INSERT INTO `sku_barcodes` VALUES (1,1,3,3,'6901234500001',1,1,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(2,2,3,3,'6901234500002',1,1,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(3,3,2,1,'6901234500003',1,1,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(4,4,4,1,'6901234500004',1,1,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(5,5,5,1,'6901234500005',1,1,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(6,6,6,1,'6901234500006',1,1,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='SKU供应商关联表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sku_suppliers`
--

LOCK TABLES `sku_suppliers` WRITE;
/*!40000 ALTER TABLE `sku_suppliers` DISABLE KEYS */;
INSERT INTO `sku_suppliers` VALUES (1,1,3,1,8000,1,0,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(2,2,3,1,12000,1,0,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(3,3,2,1,25000,1,0,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(4,4,4,1,130000,1,0,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(5,5,5,1,350000,1,0,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(6,6,6,1,450000,1,0,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL);
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
  `purchase_price` bigint NOT NULL DEFAULT '0' COMMENT '标准采购价（厘）',
  `cost_price` bigint NOT NULL DEFAULT '0' COMMENT '加权平均成本价（厘）',
  `min_purchase_price` bigint NOT NULL DEFAULT '0' COMMENT '最低采购限价（厘）',
  `list_price` bigint NOT NULL DEFAULT '0' COMMENT '吊牌价（厘）',
  `retail_price` bigint NOT NULL DEFAULT '0' COMMENT '标准零售价（厘）',
  `wholesale_price` bigint NOT NULL DEFAULT '0' COMMENT '批发团购价（厘）',
  `employee_price` bigint NOT NULL DEFAULT '0' COMMENT '员工内部价（厘）',
  `offline_price` bigint NOT NULL DEFAULT '0' COMMENT '门店基准价（厘）',
  `miniapp_price` bigint NOT NULL DEFAULT '0' COMMENT '小程序基准价（厘）',
  `delivery_price` bigint NOT NULL DEFAULT '0' COMMENT '配送基准价（厘）',
  `min_sale_price` bigint NOT NULL DEFAULT '0' COMMENT '最低销售限价（厘）',
  `max_sale_price` bigint NOT NULL DEFAULT '0' COMMENT '最高销售限价（厘）',
  `stock` bigint NOT NULL DEFAULT '0' COMMENT '当前库存冗余字段',
  `base_unit_id` bigint unsigned DEFAULT NULL COMMENT '最小计量单位ID（关联 units 表）',
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
INSERT INTO `skus` VALUES (1,1,'SKU-0001','[{\"label\": \"规格\", \"value\": \"斤\"}]',8000,0,0,0,0,9200,0,0,0,0,0,0,0,3,1,2,'2026-08-12 21:11:50','2026-08-12 21:25:46',NULL),(2,2,'SKU-0002','[{\"label\": \"规格\", \"value\": \"斤\"}]',12000,0,0,0,0,13799,0,0,0,0,0,0,0,NULL,1,2,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(3,3,'SKU-0003','[{\"label\": \"规格\", \"value\": \"斤\"}]',25000,0,0,0,0,28749,0,0,0,0,0,0,0,NULL,1,2,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(4,4,'SKU-0004','[{\"label\": \"规格\", \"value\": \"斤\"}]',130000,0,0,0,0,149500,0,0,0,0,0,0,0,NULL,1,2,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(5,5,'SKU-0005','[{\"label\": \"规格\", \"value\": \"斤\"}]',350000,0,0,0,0,402499,0,0,0,0,0,0,0,NULL,1,2,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(6,6,'SKU-0006','[{\"label\": \"规格\", \"value\": \"桶\"}]',450000,0,0,0,0,517499,0,0,0,0,0,0,0,NULL,1,2,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL);
/*!40000 ALTER TABLE `skus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `store_sku_prices`
--

DROP TABLE IF EXISTS `store_sku_prices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_sku_prices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `store_id` bigint unsigned NOT NULL COMMENT '门店ID',
  `sku_id` bigint unsigned NOT NULL COMMENT 'SKU ID',
  `price_type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '价格类型：1零售价上浮下调，2独立零售价，3会员价覆盖',
  `adjust_mode` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '调整方式：1固定金额，2百分比，3直接覆盖',
  `adjust_value` bigint NOT NULL DEFAULT '0' COMMENT '调整值（金额=厘，百分比=万分比）',
  `member_level` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '会员等级：0不限定，1普通，2银卡，3金卡，4钻石',
  `effective_at` timestamp NULL DEFAULT NULL COMMENT '生效时间',
  `expire_at` timestamp NULL DEFAULT NULL COMMENT '失效时间',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：0禁用，1启用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `store_sku_prices_store_id_sku_id_index` (`store_id`,`sku_id`),
  KEY `store_sku_prices_status_index` (`status`),
  KEY `store_sku_prices_effective_at_expire_at_index` (`effective_at`,`expire_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='门店差异化价格表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `store_sku_prices`
--

LOCK TABLES `store_sku_prices` WRITE;
/*!40000 ALTER TABLE `store_sku_prices` DISABLE KEYS */;
/*!40000 ALTER TABLE `store_sku_prices` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='结算单明细表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier_settlement_items`
--

LOCK TABLES `supplier_settlement_items` WRITE;
/*!40000 ALTER TABLE `supplier_settlement_items` DISABLE KEYS */;
INSERT INTO `supplier_settlement_items` VALUES (1,1,1,440000,'2026-08-12 21:11:51','2026-08-12 21:11:51');
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='供应商结算单表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier_settlements`
--

LOCK TABLES `supplier_settlements` WRITE;
/*!40000 ALTER TABLE `supplier_settlements` DISABLE KEYS */;
INSERT INTO `supplier_settlements` VALUES (1,'SS-20260728-001',3,'2026-07-01','2026-07-28',440000,5000,435000,0,435000,3,'2026-08-12 21:11:51',NULL,NULL,'2026-08-12 21:11:51','2026-08-12 21:11:51'),(2,'SS-20260728-002',4,'2026-07-01','2026-07-28',260000,3000,257000,0,0,1,NULL,NULL,NULL,'2026-08-12 21:11:51','2026-08-12 21:11:51');
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='供应商表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES (1,'默认供应商','-','-',NULL,NULL,NULL,2,1,NULL,'2026-08-12 21:05:32','2026-08-12 21:05:32',NULL),(2,'鲜源农业有限公司','陈供应','13900000001','安徽省宿州市埇桥区农批市场A1','中国工商银行宿州分行','1302000109200100001',1,1,'蔬菜水果类主力供应商','2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(3,'绿野蔬菜种植基地','李蔬菜','13900000002','安徽省宿州市埇桥区农批市场B3','中国农业银行宿州分行','1302000109200100002',1,1,'叶菜类专供','2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(4,'丰润肉业有限公司','王肉业','13900000003','安徽省宿州市埇桥区肉联厂C2','中国建设银行宿州分行','1302000109200100003',2,1,'猪牛肉类供应商','2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(5,'海滨水产批发部','赵水产','13900000004','安徽省宿州市埇桥区水产市场D5','中国银行宿州分行','1302000109200100004',1,1,'水产海鲜类','2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(6,'恒达粮油贸易公司','钱粮油','13900000005','安徽省宿州市埇桥区粮批市场E1','中国邮政储蓄银行宿州分行','1302000109200100005',3,1,'粮油干货类','2026-08-12 21:11:50','2026-08-12 21:11:50',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='系统配置表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_configs`
--

LOCK TABLES `system_configs` WRITE;
/*!40000 ALTER TABLE `system_configs` DISABLE KEYS */;
INSERT INTO `system_configs` VALUES (1,'site_name','本地速送服务平台','本地速送服务平台','string','basic','站点名称',NULL,NULL,'required|max:50',1,0,0,'站点名称','2026-08-12 21:05:30','2026-08-12 21:05:30'),(2,'contact_phone','15690631151','15690631151','string','basic','客服电话',NULL,NULL,'required|max:20',2,0,0,'客服电话','2026-08-12 21:05:30','2026-08-12 21:05:30'),(3,'max_upload_size_mb','20','20','integer','basic','文件上传大小限制（MB）','单文件上传最大体积',NULL,'required|integer|min:1|max:100',6,0,0,'管理后台和商家端文件上传限制','2026-08-12 21:05:30','2026-08-12 21:05:30'),(4,'site_icp_number','','','string','basic','ICP 备案号','网站 ICP 备案号，留空不显示',NULL,'max:50',7,1,0,'显示在页面底部的 ICP 备案号','2026-08-12 21:05:30','2026-08-12 21:05:30'),(5,'site_tech_stack_url','https://laravel.com','https://laravel.com','string','basic','技术栈链接','底部版权栏\"技术栈\"文字的跳转链接',NULL,'url|max:255',8,1,0,'点击底部版权栏中的技术栈文字时跳转的 URL','2026-08-12 21:05:30','2026-08-12 21:05:30'),(6,'site_developer_name','Seeding','Seeding','string','basic','开发者名称','底部版权栏显示的开发者名称',NULL,'max:50',9,1,0,'显示在页面底部版权栏中的开发者名称','2026-08-12 21:05:30','2026-08-12 21:05:30'),(7,'site_developer_url','','','string','basic','开发者链接','底部版权栏\"开发者名称\"的跳转链接，留空不可点击',NULL,'nullable|url|max:255',10,1,0,'点击底部版权栏中的开发者名称时跳转的 URL','2026-08-12 21:05:30','2026-08-12 21:05:30'),(8,'site_icp_url','https://beian.miit.gov.cn/','https://beian.miit.gov.cn/','string','basic','备案号链接','底部版权栏\"ICP备案号\"的跳转链接',NULL,'url|max:255',11,1,0,'点击底部版权栏中的备案号时跳转的 URL','2026-08-12 21:05:30','2026-08-12 21:05:30'),(9,'order_auto_confirm_hours','24','24','integer','order','自动确认收货时长（小时）','超过此时长未签收将自动确认',NULL,'required|integer|min:1|max:168',3,0,0,'订单配送完成后的自动签收等待时长','2026-08-12 21:05:30','2026-08-12 21:05:30'),(10,'min_delivery_amount','0','0','integer','order','最低起送金额（元）','0表示无限制',NULL,'required|integer|min:0',4,1,0,'商家下单金额门槛','2026-08-12 21:05:30','2026-08-12 21:05:30'),(11,'allow_merchant_self_order','1','1','boolean','order','允许商家自助下单','关闭后商家只能由运营代下单',NULL,'required|boolean',5,1,0,'商家端小程序是否允许自主下单','2026-08-12 21:05:30','2026-08-12 21:05:30'),(12,'default_delivery_batch','1','1','enum','delivery','默认配送批次',NULL,'[{\"label\": \"上午\", \"value\": \"1\"}, {\"label\": \"下午\", \"value\": \"2\"}]',NULL,10,0,0,'默认配送批次：1上午，2下午','2026-08-12 21:05:30','2026-08-12 21:05:30'),(13,'delivery_timeout_minutes','30','30','integer','delivery','配送超时标记时长（分钟）','超过此时长未完成配送将标记为异常',NULL,'required|integer|min:10|max:180',11,0,0,'配送任务超时自动标记异常','2026-08-12 21:05:30','2026-08-12 21:05:30'),(14,'allow_driver_multi_task','1','1','boolean','delivery','允许司机同时接多单','关闭后司机同时只能执行一个配送任务',NULL,'required|boolean',12,0,0,'司机并发配送开关','2026-08-12 21:05:30','2026-08-12 21:05:30'),(15,'max_daily_recharge_amount','50000','50000','integer','finance','单日最大充值金额（元）','单商家每日充值累计上限',NULL,'required|integer|min:1000',20,1,0,'商家充值风控限额','2026-08-12 21:05:30','2026-08-12 21:05:30'),(16,'credit_limit_default','5000','5000','integer','finance','新商家默认信用额度（元）','新注册商家自动分配的信用额度',NULL,'required|integer|min:0',21,0,0,'新商家初始信用额度','2026-08-12 21:05:30','2026-08-12 21:05:30'),(17,'enable_weighing_auto_debit','0','0','boolean','finance','称重差异自动扣款','开启后称重差异在阈值内自动扣款，无需人工确认',NULL,'required|boolean',22,0,0,'称重差异处理方式','2026-08-12 21:05:30','2026-08-12 21:05:30'),(18,'weighing_diff_threshold','20','20','integer','inventory','称重差异阈值（%）','称重差异超过此百分比需人工确认',NULL,'required|integer|min:1|max:100',20,0,0,'称重差异阈值（百分比）','2026-08-12 21:05:30','2026-08-12 21:05:30'),(19,'inventory_warning_enabled','1','1','boolean','inventory','启用库存预警','开启后低于预警值触发通知',NULL,'required|boolean',30,0,0,'库存预警检测开关','2026-08-12 21:05:30','2026-08-12 21:05:30'),(20,'inventory_warning_interval_minutes','5','5','integer','inventory','库存预警检测频率（分钟）','定时任务检测间隔',NULL,'required|integer|min:1|max:60',31,0,0,'库存预警定时检测周期','2026-08-12 21:05:30','2026-08-12 21:05:30'),(21,'stockin_auto_create_loss','1','1','boolean','inventory','入库差异自动创建损耗单','开启后采购入库差异自动生成损耗单，关闭则需手动创建',NULL,'required|boolean',32,0,0,'采购入库数量少于采购数量时，自动创建损耗单扣减差异库存','2026-08-12 21:05:30','2026-08-12 21:05:30'),(22,'audit_retention_days','90','90','integer','audit','审计日志保留天数','0=永久保留，1-180天，到期每日定时清理',NULL,'required|integer|min:0|max:180',50,0,0,'审计/日志保留天数','2026-08-12 21:05:30','2026-08-12 21:05:30'),(23,'loss_approval_threshold','200','200','integer','audit','损耗审批阈值（元）','单张损耗单金额超过此值需运营经理审核',NULL,'required|integer|min:0',51,0,0,'损耗审批阈值（元）','2026-08-12 21:05:30','2026-08-12 21:05:30'),(24,'audit_purchase_order','1','1','boolean','audit','采购单状态审计','开启后采购单状态变更将记录审计日志',NULL,'required|boolean',52,0,0,'采购单状态变更审计开关','2026-08-12 21:05:30','2026-08-12 21:05:30'),(25,'audit_purchase_return','1','1','boolean','audit','采购退货状态审计','开启后退货单状态变更将记录审计日志',NULL,'required|boolean',53,0,0,'采购退货单状态变更审计开关','2026-08-12 21:05:30','2026-08-12 21:05:30'),(26,'audit_loss_order','1','1','boolean','audit','损耗单状态审计','开启后损耗单状态变更将记录审计日志',NULL,'required|boolean',54,0,0,'损耗单状态变更审计开关','2026-08-12 21:05:30','2026-08-12 21:05:30'),(27,'audit_price_change','1','1','boolean','audit','价格变更审计','开启后SKU价格变更将记录审计日志',NULL,'required|boolean',55,0,0,'SKU价格变更审计开关','2026-08-12 21:05:30','2026-08-12 21:05:30'),(28,'pricing_mode','lowest','lowest','enum','finance','取价模式','lowest=最低价模式，first_hit=命中即止模式','[{\"label\": \"最低价模式\", \"value\": \"lowest\"}, {\"label\": \"命中即止模式\", \"value\": \"first_hit\"}]','required|in:lowest,first_hit',40,0,0,'系统计算商品售价的策略','2026-08-12 21:05:30','2026-08-12 21:05:30'),(29,'pricing_source_enabled','{\"promotion\":true,\"store\":true,\"member\":true,\"channel\":true,\"retail\":true}','{\"promotion\":true,\"store\":true,\"member\":true,\"channel\":true,\"retail\":true}','json','finance','取价来源开关','关闭某个来源后，该来源不参与取价计算',NULL,'required|json',41,0,0,'各取价来源的启用/关闭状态','2026-08-12 21:05:30','2026-08-12 21:05:30'),(30,'pricing_priority','[\"promotion\",\"store\",\"member\",\"channel\",\"retail\"]','[\"promotion\",\"store\",\"member\",\"channel\",\"retail\"]','json','finance','取价优先级排序','仅命中即止模式下生效，按排序号从小到大排列',NULL,'required|json',42,0,0,'命中即止模式下的取价优先级顺序','2026-08-12 21:05:30','2026-08-12 21:05:30'),(31,'ui_close_on_outside','1','1','boolean','ui','点击旁边关闭通知','开启后，点击通知面板外的区域将自动关闭通知菜单',NULL,NULL,1,1,0,'控制点击通知 Drawer 外部区域时是否自动关闭面板','2026-08-12 21:05:30','2026-08-12 21:05:30'),(32,'per_page','10','10','integer','ui','列表每页条数','管理后台列表页默认每页显示条数','[{\"label\": \"10条/页\", \"value\": \"10\"}, {\"label\": \"15条/页\", \"value\": \"15\"}, {\"label\": \"20条/页\", \"value\": \"20\"}, {\"label\": \"50条/页\", \"value\": \"50\"}]','required|integer|in:10,15,20,50',2,0,0,'列表页分页条数，全局生效','2026-08-12 21:05:30','2026-08-12 21:05:30'),(33,'ui_category_tree_expanded','0','0','boolean','ui','分类树默认展开','开启后进入分类页面时自动展开所有节点；关闭则默认折叠',NULL,'required|boolean',3,1,0,'分类树展开状态系统默认值，用户可在界面设置中覆盖','2026-08-12 21:05:30','2026-08-12 21:05:30'),(34,'money.display_precision','2','2','enum','money','金额显示精度','全局金额显示保留几位小数','[{\"label\": \"2位（分）\", \"value\": \"2\"}, {\"label\": \"3位（厘）\", \"value\": \"3\"}]','required|in:2,3',1,0,0,'所有 money_format() 输出的小数位数，2=精确到分，3=精确到厘','2026-08-12 21:05:30','2026-08-12 21:05:30'),(35,'money.weighing_precision','3','3','enum','money','称重数量精度','称重数量输入框接受几位小数','[{\"label\": \"2位（0.05斤）\", \"value\": \"2\"}, {\"label\": \"3位（0.001斤）\", \"value\": \"3\"}]','required|in:2,3',2,0,0,'称重数量录入精度，2=普通秤，3=精密秤（克级）','2026-08-12 21:05:30','2026-08-12 21:05:30'),(36,'money.default_round_mode','round','round','enum','money','全局默认舍入方式','未单独设置的模块将使用此舍入方式','[{\"label\": \"四舍五入（round）\", \"value\": \"round\"}, {\"label\": \"向上取整（round_up）\", \"value\": \"round_up\"}, {\"label\": \"向下取整（round_down）\", \"value\": \"round_down\"}, {\"label\": \"截断抹零（truncate）\", \"value\": \"truncate\"}]','required|in:round,round_up,round_down,truncate',3,0,0,'全局舍入模式，各模块可单独覆盖','2026-08-12 21:05:30','2026-08-12 21:05:30'),(37,'money.order_round_mode','round','round','enum','money','订单模块舍入','消费者标准四舍五入','[{\"label\": \"四舍五入\", \"value\": \"round\"}, {\"label\": \"向上取整\", \"value\": \"round_up\"}, {\"label\": \"向下取整\", \"value\": \"round_down\"}, {\"label\": \"截断抹零\", \"value\": \"truncate\"}]','required|in:round,round_up,round_down,truncate',4,0,0,'订单模块金额舍入方式，推荐四舍五入','2026-08-12 21:05:30','2026-08-12 21:05:30'),(38,'money.purchase_round_mode','truncate','truncate','enum','money','采购模块舍入','采购方不利零头，默认截断','[{\"label\": \"四舍五入\", \"value\": \"round\"}, {\"label\": \"向上取整\", \"value\": \"round_up\"}, {\"label\": \"向下取整\", \"value\": \"round_down\"}, {\"label\": \"截断抹零\", \"value\": \"truncate\"}]','required|in:round,round_up,round_down,truncate',5,0,0,'采购模块金额舍入方式，推荐截断抹零','2026-08-12 21:05:30','2026-08-12 21:05:30'),(39,'money.recharge_round_mode','round_up','round_up','enum','money','充值模块舍入','充值向上取整，保护平台','[{\"label\": \"四舍五入\", \"value\": \"round\"}, {\"label\": \"向上取整\", \"value\": \"round_up\"}, {\"label\": \"向下取整\", \"value\": \"round_down\"}, {\"label\": \"截断抹零\", \"value\": \"truncate\"}]','required|in:round,round_up,round_down,truncate',6,0,0,'充值模块金额舍入方式，推荐向上取整','2026-08-12 21:05:30','2026-08-12 21:05:30'),(40,'money.settlement_round_mode','round','round','enum','money','结算模块舍入','财务标准四舍五入','[{\"label\": \"四舍五入\", \"value\": \"round\"}, {\"label\": \"向上取整\", \"value\": \"round_up\"}, {\"label\": \"向下取整\", \"value\": \"round_down\"}, {\"label\": \"截断抹零\", \"value\": \"truncate\"}]','required|in:round,round_up,round_down,truncate',7,0,0,'供应商结算/应收模块金额舍入方式，推荐四舍五入','2026-08-12 21:05:30','2026-08-12 21:05:30'),(41,'money.price_round_mode','round','round','enum','money','取价/促销舍入','价格展示标准四舍五入','[{\"label\": \"四舍五入\", \"value\": \"round\"}, {\"label\": \"向上取整\", \"value\": \"round_up\"}, {\"label\": \"向下取整\", \"value\": \"round_down\"}, {\"label\": \"截断抹零\", \"value\": \"truncate\"}]','required|in:round,round_up,round_down,truncate',8,0,0,'取价/促销模块金额舍入方式，推荐四舍五入','2026-08-12 21:05:30','2026-08-12 21:05:30'),(42,'money.inventory_round_mode','truncate','truncate','enum','money','库存损耗舍入','损耗截断抹零，避免虚增','[{\"label\": \"四舍五入\", \"value\": \"round\"}, {\"label\": \"向上取整\", \"value\": \"round_up\"}, {\"label\": \"向下取整\", \"value\": \"round_down\"}, {\"label\": \"截断抹零\", \"value\": \"truncate\"}]','required|in:round,round_up,round_down,truncate',9,0,0,'库存/损耗模块金额舍入方式，推荐截断抹零','2026-08-12 21:05:30','2026-08-12 21:05:30'),(43,'money.invoice_round_mode','round','round','enum','money','发票模块舍入','税务合规四舍五入','[{\"label\": \"四舍五入\", \"value\": \"round\"}, {\"label\": \"向上取整\", \"value\": \"round_up\"}, {\"label\": \"向下取整\", \"value\": \"round_down\"}, {\"label\": \"截断抹零\", \"value\": \"truncate\"}]','required|in:round,round_up,round_down,truncate',10,0,0,'发票模块金额舍入方式，推荐四舍五入','2026-08-12 21:05:30','2026-08-12 21:05:30');
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='标签词库表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tags`
--

LOCK TABLES `tags` WRITE;
/*!40000 ALTER TABLE `tags` DISABLE KEYS */;
INSERT INTO `tags` VALUES (1,'热销',1,1,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(2,'新品',2,1,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(3,'特价',3,1,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(4,'冷链',4,1,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(5,'应季',5,1,'2026-08-12 21:11:50','2026-08-12 21:11:50'),(6,'已停用标签',99,0,'2026-08-12 21:11:50','2026-08-12 21:11:50');
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='冷链温度记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `temperatures`
--

LOCK TABLES `temperatures` WRITE;
/*!40000 ALTER TABLE `temperatures` DISABLE KEYS */;
INSERT INTO `temperatures` VALUES (1,1,-180,'2026-08-11 22:10:00','2026-08-12 21:11:51'),(2,1,-150,'2026-08-11 23:00:00','2026-08-12 21:11:51');
/*!40000 ALTER TABLE `temperatures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unit_conversions`
--

DROP TABLE IF EXISTS `unit_conversions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `unit_conversions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `sku_id` bigint unsigned NOT NULL COMMENT 'SKU ID',
  `from_unit_id` bigint unsigned NOT NULL COMMENT '大单位ID（如"箱"）',
  `to_unit_id` bigint unsigned NOT NULL COMMENT '小单位ID（如"件"）',
  `ratio` bigint unsigned NOT NULL COMMENT '换算系数：1 from_unit = ratio to_unit',
  `parent_conversion_id` bigint unsigned DEFAULT NULL COMMENT '上级换算ID（链路关系：箱→件 的 ID 会作为 件→包 的 parent）',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：0禁用，1启用',
  `sort` int unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_sku_unit_conversion` (`sku_id`,`from_unit_id`,`to_unit_id`),
  KEY `unit_conversions_sku_id_index` (`sku_id`),
  KEY `unit_conversions_from_unit_id_index` (`from_unit_id`),
  KEY `unit_conversions_to_unit_id_index` (`to_unit_id`),
  KEY `unit_conversions_parent_conversion_id_index` (`parent_conversion_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='单位换算关系表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unit_conversions`
--

LOCK TABLES `unit_conversions` WRITE;
/*!40000 ALTER TABLE `unit_conversions` DISABLE KEYS */;
INSERT INTO `unit_conversions` VALUES (5,1,1,2,6,NULL,1,0,'2026-08-12 21:25:46','2026-08-12 21:25:46'),(6,1,2,3,10,5,1,1,'2026-08-12 21:25:46','2026-08-12 21:25:46');
/*!40000 ALTER TABLE `unit_conversions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `units`
--

DROP TABLE IF EXISTS `units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `units` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '单位名称：箱/件/包/斤/桶等',
  `symbol` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '单位简称/符号',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：0禁用，1启用',
  `sort` int unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `units_name_unique` (`name`),
  KEY `units_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='单位主数据表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `units`
--

LOCK TABLES `units` WRITE;
/*!40000 ALTER TABLE `units` DISABLE KEYS */;
INSERT INTO `units` VALUES (1,'箱','X',1,1,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(2,'件','J',1,2,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(3,'包','B',1,3,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(4,'斤','JIN',1,4,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(5,'桶','T',1,5,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(6,'袋','D',1,6,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(7,'盒','H',1,7,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(8,'瓶','P',1,8,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(9,'个','G',1,9,'2026-08-12 21:05:32','2026-08-12 21:05:32'),(10,'条','TIA',1,10,'2026-08-12 21:05:32','2026-08-12 21:05:32');
/*!40000 ALTER TABLE `units` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_preferences`
--

DROP TABLE IF EXISTS `user_preferences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_preferences` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` bigint unsigned NOT NULL COMMENT '用户ID',
  `pref_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '偏好键（如 col_vis_Org_SupplierList）',
  `pref_value` json DEFAULT NULL COMMENT '偏好值（JSON）',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_preferences_user_id_pref_key_unique` (`user_id`,`pref_key`),
  KEY `user_preferences_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户偏好表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_preferences`
--

LOCK TABLES `user_preferences` WRITE;
/*!40000 ALTER TABLE `user_preferences` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_preferences` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'seeding','$2y$12$Qln9rVyRr0qAGAEjOcMYN.DJbvnFkEs9Pd8Y0z6kceKzfQZbdBi6y','系统管理员','15690631151','seeding@ihopeso.cn',NULL,1,'2026-08-12 22:08:44',NULL,'vVa5HPKkOCt9YvZkJJRwD9eJ4AunOd0Vh1QZBmXkY1eo2ahLDqMUlllhud26','2026-08-12 21:05:27','2026-08-12 22:08:44',NULL),(2,'superadmin','$2y$12$K88urQD2pJWemP5w6jDFy.c6Pq8lt7LRoFITauhtdKqRwQm8bDPD2','超级管理员','13800000000','superadmin@susong.test',NULL,1,NULL,NULL,NULL,'2026-08-12 21:11:48','2026-08-12 21:11:48',NULL),(3,'operator1','$2y$12$KYr76uiBJn4nKHZ1FV2AZO0EoYIQumaq6GWoyuQ1IOPv7oGSidWUW','张运营','13800000001','operator@susong.test',NULL,1,NULL,NULL,NULL,'2026-08-12 21:11:49','2026-08-12 21:11:49',NULL),(4,'ops_manager','$2y$12$k/7x4rDLr7leM1WQfPV1HexCyECgt/Kk3yEZPoHFCvqy.//biLRAK','李运营经理','13800000002','ops_manager@susong.test',NULL,1,NULL,NULL,NULL,'2026-08-12 21:11:49','2026-08-12 21:11:49',NULL),(5,'finance1','$2y$12$gvVNvQJ5kZGVrSiNU5RMJe8JeyzolDtrvMq9Z7WLtSFTtYpJ3mCca','王财务','13800000003','finance@susong.test',NULL,1,NULL,NULL,NULL,'2026-08-12 21:11:49','2026-08-12 21:11:49',NULL),(6,'cashier1','$2y$12$qkOSGALBAv/5T6bdqr26zerQYFZAmaC0fzg9hU0f8DsdAbxhvNPwa','赵出纳','13800000004','cashier@susong.test',NULL,1,NULL,NULL,NULL,'2026-08-12 21:11:49','2026-08-12 21:11:49',NULL),(7,'fin_manager','$2y$12$0EQzP0VYcj/7l8hZDBPttOC7MmZgFVOwX34Jci7w/GmE8Ua9tyoLm','钱财务经理','13800000005','finance_manager@susong.test',NULL,1,NULL,NULL,NULL,'2026-08-12 21:11:49','2026-08-12 21:11:49',NULL),(8,'picker1','$2y$12$rUdcUQI9rujcD7mqPwKfg.wY6uSM4TJVTGkcf2HOZO8L1BbjCX/ai','孙拣货员','13800000006','picker@susong.test',NULL,1,NULL,NULL,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(9,'driver1','$2y$12$e3X7d2ajJbQ2b1QerLq96eiJ9IiBSSmpro9Lz95XftMUb7hzt.SeK','周司机','13800000007','driver@susong.test',NULL,1,NULL,NULL,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(10,'merchant1','$2y$12$bswae0f13HqrZIq2yR5iWeHtJBdfl9MV/9AmSVY1pspRv5FYERjV2','吴商家','13800000008','merchant@susong.test',NULL,1,NULL,NULL,NULL,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicle_issues`
--

DROP TABLE IF EXISTS `vehicle_issues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehicle_issues` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `vehicle_id` bigint unsigned NOT NULL COMMENT '车辆ID',
  `task_id` bigint unsigned DEFAULT NULL COMMENT '关联任务ID',
  `issue_type` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '故障类型：breakdown=抛锚 accident=事故 tire=轮胎 battery=电瓶 engine=发动机 other=其他',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '描述',
  `photos` json DEFAULT NULL COMMENT '故障照片',
  `reported_at` timestamp NULL DEFAULT NULL COMMENT '上报时间',
  `reported_by` bigint unsigned DEFAULT NULL COMMENT '上报人ID',
  `resolved_at` timestamp NULL DEFAULT NULL COMMENT '解决时间',
  `resolved_by` bigint unsigned DEFAULT NULL COMMENT '处理人ID',
  `impact_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '影响类型',
  `impact_desc` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '影响描述',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：1处理中 2已解决 3已关闭',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehicle_issues_vehicle_id_index` (`vehicle_id`),
  KEY `vehicle_issues_task_id_index` (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='车辆故障记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicle_issues`
--

LOCK TABLES `vehicle_issues` WRITE;
/*!40000 ALTER TABLE `vehicle_issues` DISABLE KEYS */;
/*!40000 ALTER TABLE `vehicle_issues` ENABLE KEYS */;
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
  `name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '车辆名称',
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'van' COMMENT '类型：van=厢式货车 truck=卡车 refrigerated=冷藏车 motorcycle=三轮摩托车',
  `capacity_kg` decimal(10,2) DEFAULT NULL COMMENT '载重（公斤）',
  `capacity_volume` decimal(8,2) DEFAULT NULL COMMENT '容积（立方米）',
  `is_cold_chain` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '是否冷链：0否，1是',
  `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：1可用，2维修中，3报废',
  `last_maintenance_date` date DEFAULT NULL COMMENT '上次保养日期',
  `next_maintenance_date` date DEFAULT NULL COMMENT '下次保养日期',
  `remark` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注',
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
INSERT INTO `vehicles` VALUES (1,'皖LT0001','冷藏车1号','refrigerated',2000.00,12.50,1,1,NULL,NULL,'北线专用冷藏车','2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(2,'皖LT0002','厢式货车1号','van',3000.00,18.00,0,1,NULL,NULL,'南线专用厢式货车','2026-08-12 21:11:50','2026-08-12 21:11:50',NULL);
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
  `sort` int unsigned NOT NULL DEFAULT '0' COMMENT '排序号',
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
INSERT INTO `warehouses` VALUES (1,'总仓-农批市场',1,0,'安徽省宿州市埇桥区农批市场内',1,1,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL),(2,'分仓-肉联厂',2,1,'安徽省宿州市埇桥区肉联厂内',2,1,'2026-08-12 21:11:50','2026-08-12 21:11:50',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='微信用户绑定表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wechat_users`
--

LOCK TABLES `wechat_users` WRITE;
/*!40000 ALTER TABLE `wechat_users` DISABLE KEYS */;
INSERT INTO `wechat_users` VALUES (1,10,'o_demo_merchant_001',NULL,'吴商家',NULL,1,1,'2026-08-12 21:11:51','2026-08-12 21:11:51'),(2,9,'o_demo_driver_001',NULL,'周司机',NULL,2,1,'2026-08-12 21:11:51','2026-08-12 21:11:51');
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

-- Dump completed on 2026-08-13 14:27:29
