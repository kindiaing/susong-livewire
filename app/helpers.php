<?php

/**
 * 金额格式化：整数分 → 元（带千分位）
 * 例：23000 → ¥230.00
 */
function money_format(int|float|null $cents, bool $withSymbol = true): string
{
    if ($cents === null) {
        return $withSymbol ? '¥0.00' : '0.00';
    }
    $yuan = number_format($cents / 100, 2, '.', ',');
    return $withSymbol ? '¥' . $yuan : $yuan;
}

/**
 * 状态颜色映射（通用）
 */
function status_badge(int $status, string $type = 'default'): string
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
        default => [1 => ['启用', 'bg-green-100 text-green-700'], 0 => ['禁用', 'bg-gray-100 text-gray-600']],
    };

    $item = $map[$status] ?? ['未知', 'bg-gray-100 text-gray-600'];
    return '<span class="inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium ' . $item[1] . '">' . $item[0] . '</span>';
}
