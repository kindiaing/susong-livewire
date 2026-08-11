<?php

/**
 * 菜单配置文件
 *
 * 权限名采用三级格式：模块.页面.动作
 * - 模块级：org / user / product / purchase / order / inventory / delivery / loss / finance / price / system
 * - 页面级：org.supplier / org.merchant / user.manage 等（对应菜单项）
 * - 按钮级：org.supplier.create / org.supplier.edit / org.supplier.delete 等
 *
 * 每个菜单项的 permission 字段对应页面级权限（.view），
 * 渲染时用 @can($item['permission']) 过滤不可见菜单。
 */

return [
    [
        'key'   => 'product',
        'label' => '商品管理',
        'icon'  => 'cube',
        'children' => [
            ['key' => 'product.category',  'label' => '分类管理',   'route' => 'categories',         'permission' => 'product.category.view',  'description' => '商品分类层级树'],
            ['key' => 'product.product',   'label' => '商品管理',   'route' => 'products',           'permission' => 'product.product.view',   'description' => '商品信息与图片维护'],
            ['key' => 'product.sku',       'label' => 'SKU 管理',  'route' => 'skus',               'permission' => 'product.product.view',   'description' => '规格、价格、库存单位'],
            ['key' => 'product.visibility', 'label' => '可见性配置', 'route' => 'merchant-sku-visibility', 'permission' => 'product.visibility.view', 'description' => '商家可见SKU配置'],
            ['key' => 'product.keyword',   'label' => '关键词标签', 'route' => 'keywords',           'permission' => 'product.keyword.view',   'description' => '商品搜索与标签'],
            ['key' => 'product.barcode',   'label' => '条码管理',   'route' => 'sku-barcodes',       'permission' => 'product.product.view',   'description' => 'SKU条码绑定'],
            ['key' => 'product.supplier',  'label' => '一品多供',   'route' => 'sku-suppliers',      'permission' => 'product.product.view',   'description' => '多供应商供应关系'],
            ['key' => 'product.tag',        'label' => '标签管理',   'route' => 'tags',               'permission' => 'product.tag.view',       'description' => '商品标签分组管理'],
        ],
    ],
    [
        'key'   => 'purchase',
        'label' => '采购管理',
        'icon'  => 'cart',
        'children' => [
            ['key' => 'purchase.item',  'label' => '待采清单',   'route' => 'purchase-items',    'permission' => 'purchase.purchase-order.view',  'description' => '采购需求汇总'],
            ['key' => 'purchase.order', 'label' => '采购单管理', 'route' => 'purchase-orders',   'permission' => 'purchase.purchase-order.view',  'description' => '采购订单创建与跟踪'],
            ['key' => 'purchase.return','label' => '采购退货',   'route' => 'purchase-returns',  'permission' => 'purchase.purchase-return.view', 'description' => '退货给供应商'],
            ['key' => 'purchase.restock-reminder', 'label' => '补货提醒',   'route' => 'restock-reminders', 'permission' => 'purchase.restock-reminder.view', 'description' => '库存预警与补货建议'],
        ],
    ],
    [
        'key'   => 'order',
        'label' => '订单管理',
        'icon'  => 'clipboard',
        'children' => [
            ['key' => 'order.order',     'label' => '客户订单',   'route' => 'orders',              'permission' => 'order.order.view',       'description' => '商家下单记录'],
            ['key' => 'order.cart',      'label' => '购物车',     'route' => 'carts',               'permission' => 'order.cart.view',        'description' => '商家选购暂存'],
            ['key' => 'order.frequent',  'label' => '常购清单',   'route' => 'frequently-bought',    'permission' => 'order.order.view',       'description' => '商家频繁购买记录'],
            ['key' => 'order.repurchase','label' => '复购模板',   'route' => 'repurchase-templates', 'permission' => 'order.order.view',       'description' => '一键快速复购'],
            ['key' => 'order.return',    'label' => '售后退货',   'route' => 'order-returns',        'permission' => 'order.order-return.view',    'description' => '客户退货处理'],
        ],
    ],
    [
        'key'   => 'delivery',
        'label' => '配送管理',
        'icon'  => 'truck',
        'children' => [
            ['key' => 'delivery.route',  'label' => '配送线路',   'route' => 'delivery-routes',      'permission' => 'delivery.route.view',      'description' => '线路规划与商家排序'],
            ['key' => 'delivery.task',   'label' => '配送任务',   'route' => 'delivery-tasks',       'permission' => 'delivery.delivery-task.view', 'description' => '司机配送调度'],
            ['key' => 'delivery.signature','label' => '签收存证', 'route' => 'signatures',            'permission' => 'delivery.signature.view',    'description' => '签收照片与温度记录'],
            ['key' => 'delivery.discrepancy','label' => '差异处理', 'route' => 'discrepancies',      'permission' => 'delivery.discrepancy.view',   'description' => '配送差异与短少处理'],
            ['key' => 'delivery.temperature', 'label' => '温度记录', 'route' => 'temperatures',     'permission' => 'delivery.temperature.view',    'description' => '冷链温度监控记录'],
        ],
    ],
    [
        'key'   => 'inventory',
        'label' => '库存拣货',
        'icon'  => 'chart-bar',
        'children' => [
            ['key' => 'inventory.warehouse', 'label' => '仓库管理', 'route' => 'warehouses',    'permission' => 'inventory.warehouse.view',    'description' => '仓库信息与分区'],
            ['key' => 'inventory.stock',     'label' => '实时库存', 'route' => 'inventories',   'permission' => 'inventory.inventory.view',    'description' => '各仓库SKU存量'],
            ['key' => 'inventory.log',       'label' => '库存日志', 'route' => 'inventory-logs', 'permission' => 'inventory.inventory-log.view', 'description' => '出入库变动记录'],
            ['key' => 'inventory.picking',  'label' => '拣货任务', 'route' => 'picking-tasks', 'permission' => 'inventory.warehouse.view',     'description' => '拣货分配与执行'],
        ],
    ],
    [
        'key'   => 'finance',
        'label' => '财务管理',
        'icon'  => 'banknotes',
        'children' => [
            ['key' => 'finance.account',       'label' => '客户账户',   'route' => 'merchant-accounts',       'permission' => 'finance.recharge.view',            'description' => '商家账户余额与额度'],
            ['key' => 'finance.recharge',      'label' => '客户充值',   'route' => 'recharges',               'permission' => 'finance.recharge.view',            'description' => '充值记录与审核'],
            ['key' => 'finance.settlement',    'label' => '供应商结算', 'route' => 'supplier-settlements',     'permission' => 'finance.supplier-settlement.view', 'description' => '采购结算与付款'],
            ['key' => 'finance.receivable',    'label' => '应收账款',   'route' => 'receivables',             'permission' => 'finance.receivable.view',         'description' => '客户欠款与收款'],
            ['key' => 'finance.invoice',       'label' => '发票管理',   'route' => 'invoices',                'permission' => 'finance.invoice.view',             'description' => '开票记录与审核'],
            ['key' => 'finance.correction',    'label' => '授权更正',   'route' => 'correction-authorizations','permission' => 'finance.recharge.view',            'description' => '账目更正审批'],
            ['key' => 'finance.price-strategy','label' => '促销活动',   'route' => 'promotion-activities',   'permission' => 'price.promotion.view',             'description' => '促销活动与优惠规则'],
            ['key' => 'finance.promotion-settings','label' => '促销设置',   'route' => 'promotion-settings',   'permission' => 'price.promotion.view',             'description' => '促销/商家价格/会员折扣配置'],
            ['key' => 'finance.loss',          'label' => '损耗管理',   'route' => 'loss-orders',             'permission' => 'loss.loss-order.view',             'description' => '损耗记录与审核'],
        ],
    ],
    [
        'key'   => 'user',
        'label' => '用户权限',
        'icon'  => 'users',
        'children' => [
            ['key' => 'user.manage',     'label' => '用户管理', 'route' => 'users',       'permission' => 'user.user.view',       'description' => '用户CRUD、重置密码、禁用启用'],
            ['key' => 'user.role',       'label' => '角色管理', 'route' => 'roles',       'permission' => 'user.role.view',       'description' => '9个系统角色、权限配置'],
            ['key' => 'user.permission', 'label' => '权限管理', 'route' => 'permissions', 'permission' => 'user.permission.view',  'description' => '模块/页面/按钮级权限控制'],
        ],
    ],
    [
        'key'   => 'org',
        'label' => '组织主体',
        'icon'  => 'building',
        'children' => [
            ['key' => 'org.supplier', 'label' => '供应商管理', 'route' => 'suppliers',      'permission' => 'org.supplier.view', 'description' => '供应商信息与资质'],
            ['key' => 'org.merchant', 'label' => '商家管理',   'route' => 'merchants',      'permission' => 'org.merchant.view', 'description' => '商家信息与账户'],
            ['key' => 'org.driver',   'label' => '司机管理',   'route' => 'drivers',        'permission' => 'org.driver.view',   'description' => '司机信息与排班'],
            ['key' => 'org.vehicle',  'label' => '车辆管理',   'route' => 'vehicles',        'permission' => 'org.vehicle.view',  'description' => '车辆信息与司机绑定'],
        ],
    ],
    [
        'key'   => 'system',
        'label' => '系统管理',
        'icon'  => 'adjustments-horizontal',
        'children' => [
            ['key' => 'system.config',   'label' => '系统配置', 'route' => 'settings',       'permission' => 'system.system-config.view', 'description' => '基础/订单/配送/库存等系统配置'],
            ['key' => 'system.finance-settings', 'label' => '财务配置', 'route' => 'finance-settings', 'permission' => 'system.system-config.view', 'description' => '取价配置/财务风控/金额精度/费用均摊'],
            ['key' => 'system.banner',   'label' => '轮播广告', 'route' => 'banners',        'permission' => 'system.banner.view',        'description' => '首页轮播图管理'],
            ['key' => 'system.operation','label' => '操作日志', 'route' => 'operation-logs',  'permission' => 'system.audit-log.view',     'description' => '按操作人/时间/模块筛选'],
            ['key' => 'system.audit-settings', 'label' => '审计设置', 'route' => 'audit-settings', 'permission' => 'system.system-config.view', 'description' => '审计开关/日志保留策略'],
            ['key' => 'system.audit',    'label' => '审计日志', 'route' => 'audit-logs',      'permission' => 'system.audit-log.view',     'description' => '敏感操作审计记录'],
            ['key' => 'system.approval', 'label' => '审核管理', 'route' => 'approval-config', 'permission' => 'system.system-config.view',  'description' => '19个审核节点开关与列表'],
            ['key' => 'system.approval-list', 'label' => '审核列表', 'route' => 'approvals',   'permission' => 'system.system-config.view',  'description' => '待审核与已审核记录'],
            ['key' => 'system.price-log','label' => '改价记录', 'route' => 'price-change-logs','permission' => 'price.price-change-log.view','description' => '价格变更历史'],
            ['key' => 'system.login-log','label' => '登录日志', 'route' => 'login-logs',      'permission' => 'system.login-log.view',     'description' => '用户登录记录'],
            ['key' => 'system.wechat',   'label' => '微信用户', 'route' => 'wechat-users',    'permission' => 'system.wechat-user.view',   'description' => '小程序用户绑定'],
        ],
    ],
];
