<?php

/**
 * 金额格式化：整数厘 → 元（带千分位）
 * 例：8000 → ¥8.00
 */
function money_format(int|float|null $cents, bool $withSymbol = true): string
{
    if ($cents === null) {
        return $withSymbol ? '¥0.00' : '0.00';
    }
    $yuan = number_format($cents / 1000, 2, '.', ',');
    return $withSymbol ? '¥' . $yuan : $yuan;
}

/**
 * 金额反格式化：元（字符串/浮点） → 整数厘
 * 例：'8.00' → 8000, 130.5 → 130500
 */
function money_to_cents(string|float|null $yuan): int
{
    if ($yuan === null || $yuan === '') {
        return 0;
    }
    return (int) round((float) $yuan * 1000);
}

/**
 * 获取系统配置值（全局助手函数）
 * 例：setting('per_page', 10) → 10
 */
function setting(string $key, mixed $default = null): mixed
{
    return \App\Support\Setting::get($key, $default);
}

/**
 * 状态 Badge 渲染（通用）
 *
 * @param int        $status 状态值
 * @param string     $type   映射类型：active/order/payment/audit/driver_online/cold_chain/visibility/loss_approval/loss_status/purchase_item/purchase_return/sku_approval/target_type/inventory_log
 * @param string     $label  自定义文字（覆盖映射中的默认文字）
 * @return string
 */
function status_badge(int $status, string $type = 'default', string $label = ''): string
{
    $map = match ($type) {
        'active' => [1 => ['启用', 'bg-green-100 text-green-700'], 0 => ['禁用', 'bg-gray-100 text-gray-600']],
        'order' => [
            0 => ['待确认', 'bg-yellow-100 text-yellow-700'],
            1 => ['已确认', 'bg-blue-100 text-blue-700'],
            2 => ['配送中', 'bg-indigo-100 text-indigo-700'],
            3 => ['已完成', 'bg-green-100 text-green-700'],
            4 => ['已取消', 'bg-gray-100 text-gray-600'],
            5 => ['已退款', 'bg-red-100 text-red-700'],
        ],
        'payment' => [
            0 => ['待支付', 'bg-yellow-100 text-yellow-700'],
            1 => ['已支付', 'bg-green-100 text-green-700'],
            2 => ['支付失败', 'bg-red-100 text-red-700'],
        ],
        'audit' => [
            0 => ['待审核', 'bg-yellow-100 text-yellow-700'],
            1 => ['已通过', 'bg-green-100 text-green-700'],
            2 => ['已拒绝', 'bg-red-100 text-red-700'],
        ],
        'driver_online' => [1 => ['在线', 'bg-blue-100 text-blue-700'], 0 => ['离线', 'bg-gray-100 text-gray-600']],
        'cold_chain' => [1 => ['冷链', 'bg-blue-100 text-blue-700'], 0 => ['非冷链', 'bg-gray-100 text-gray-600']],
        'visibility' => [1 => ['可见', 'bg-green-100 text-green-700'], 0 => ['不可见', 'bg-gray-100 text-gray-600']],
        'target_type' => ['product' => ['商品级', 'bg-blue-100 text-blue-700'], 'sku' => ['SKU级', 'bg-purple-100 text-purple-700']],
        'loss_approval' => [
            1 => ['待审核', 'bg-yellow-100 text-yellow-700'],
            2 => ['已通过', 'bg-green-100 text-green-700'],
            3 => ['已拒绝', 'bg-red-100 text-red-700'],
        ],
        'loss_status' => [
            1 => ['待处理', 'bg-yellow-100 text-yellow-700'],
            2 => ['处理中', 'bg-yellow-100 text-yellow-700'],
            3 => ['已入库', 'bg-blue-100 text-blue-700'],
            4 => ['已出库', 'bg-blue-100 text-blue-700'],
            9 => ['已关闭', 'bg-gray-100 text-gray-600'],
        ],
        'purchase_item' => [1 => ['待生成', 'bg-yellow-100 text-yellow-700'], 2 => ['已生成', 'bg-green-100 text-green-700']],
        'purchase_return' => [
            1 => ['待审核', 'bg-yellow-100 text-yellow-700'],
            2 => ['审核中', 'bg-blue-100 text-blue-700'],
            3 => ['处理中', 'bg-blue-100 text-blue-700'],
            4 => ['已完成', 'bg-green-100 text-green-700'],
            9 => ['已取消', 'bg-gray-100 text-gray-600'],
        ],
        'sku_approval' => [
            1 => ['待审核', 'bg-yellow-100 text-yellow-700'],
            2 => ['已通过', 'bg-green-100 text-green-700'],
            3 => ['已拒绝', 'bg-red-100 text-red-700'],
        ],
        'inventory_log' => [
            'in'  => ['入库', 'bg-green-100 text-green-700'],
            'out' => ['出库', 'bg-red-100 text-red-700'],
        ],
        default => [1 => ['启用', 'bg-green-100 text-green-700'], 0 => ['禁用', 'bg-gray-100 text-gray-600']],
    };

    $item = $map[$status] ?? ['未知', 'bg-gray-100 text-gray-600'];
    $text = $label !== '' ? $label : $item[0];
    return '<span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium ' . $item[1] . '">' . $text . '</span>';
}
