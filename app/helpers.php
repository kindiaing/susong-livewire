<?php

/**
 * 金额格式化：厘 → 显示字符串
 *
 * 三层分离架构：存储层（厘）→ 计算层（厘）→ 显示层（可配置精度+舍入）
 * 汇总金额永远是后端厘级求和的结果，不是前端把显示值加起来。
 *
 * @param int|float|null $cents      厘值（数据库存储单位）
 * @param bool           $withSymbol 是否带¥符号
 * @param string|null    $module     模块名（null=用全局默认舍入），如 'order'/'purchase'/'recharge'
 * @return string
 *
 * 调用方式完全向后兼容：
 *   money_format(8000)                → ¥8.00（全局2位，默认四舍五入）
 *   money_format(8000, false)         → 8.00（不带¥）
 *   money_format(8000, true, 'order') → 按订单模块配置的舍入方式显示
 */
function money_format(int|float|null $cents, bool $withSymbol = true, ?string $module = null): string
{
    $precision = setting('money.display_precision', 2);

    if ($cents === null) {
        $zero = number_format(0, $precision, '.', ',');
        return $withSymbol ? '¥' . $zero : $zero;
    }

    $roundMode = $module
        ? setting("money.{$module}_round_mode", setting('money.default_round_mode', 'round'))
        : setting('money.default_round_mode', 'round');

    $yuan = money_round($cents, $precision, $roundMode);

    return $withSymbol
        ? '¥' . number_format($yuan, $precision, '.', ',')
        : number_format($yuan, $precision, '.', '');
}

/**
 * 金额舍入：厘 → 元（浮点），按精度和舍入模式处理
 *
 * 舍入模式枚举：
 *   round      — 四舍五入（¥3.145 → ¥3.15）
 *   round_up   — 向上取整（¥3.141 → ¥3.15）
 *   round_down — 向下取整（¥3.149 → ¥3.14）
 *   truncate   — 截断抹零（2位精度下同 round_down，3位精度下直接截断）
 *
 * @param int|float $cents     厘值
 * @param int       $precision 显示精度（2=分级，3=厘级）
 * @param string    $mode      舍入模式：round/round_up/round_down/truncate
 * @return float 元值
 */
function money_round(int|float $cents, int $precision = 2, string $mode = 'round'): float
{
    $yuan = $cents / 1000;
    $factor = pow(10, $precision);

    return match ($mode) {
        'round'      => round($yuan, $precision),
        'round_up'   => ceil($yuan * $factor) / $factor,
        'round_down' => floor($yuan * $factor) / $factor,
        'truncate'   => floor($yuan * $factor) / $factor,
        default      => round($yuan, $precision),
    };
}

/**
 * 金额反格式化：元（字符串/浮点） → 整数厘
 *
 * 注意：输入端始终接受元，输出端始终是厘，不受精度配置影响。
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
 * 称重数量格式化：厘斤 → 斤显示字符串
 *
 * 称重数量精度与金额显示精度是两个独立维度，可分别配置。
 *
 * @param int|float|null $liangin   厘斤值（数据库存储单位）
 * @param bool           $withUnit  是否带"斤"单位
 * @param int|null       $precision 精度（null=用系统配置 money.weighing_precision）
 * @return string
 */
function weight_format(int|float|null $liangin, bool $withUnit = false, ?int $precision = null): string
{
    if ($liangin === null) {
        return $withUnit ? '0斤' : '0';
    }
    $p = $precision ?? setting('money.weighing_precision', 3);
    $jin = $liangin / 1000;
    $str = number_format($jin, $p, '.', '');
    return $withUnit ? $str . '斤' : $str;
}

/**
 * 称重数量反格式化：斤 → 厘斤（存储）
 *
 * 例：2.13 → 2130, 0.5 → 500
 */
function weight_to_liangin(string|float|null $jin): int
{
    if ($jin === null || $jin === '') {
        return 0;
    }
    return (int) round((float) $jin * 1000);
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
            1 => ['待拣货', 'bg-yellow-100 text-yellow-700'],
            2 => ['拣货中', 'bg-blue-100 text-blue-700'],
            3 => ['配送中', 'bg-orange-100 text-orange-700'],
            4 => ['已签收', 'bg-green-100 text-green-700'],
            5 => ['已锁定', 'bg-gray-100 text-gray-600'],
            9 => ['已取消', 'bg-red-100 text-red-700'],
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
            9 => ['已作废', 'bg-gray-100 text-gray-600'],
        ],
        'purchase_item' => [1 => ['待生成', 'bg-yellow-100 text-yellow-700'], 2 => ['已生成', 'bg-green-100 text-green-700']],
        'purchase_order' => [
            1 => ['待接单', 'bg-yellow-100 text-yellow-700'],
            2 => ['备货中', 'bg-blue-100 text-blue-700'],
            3 => ['已发货', 'bg-orange-100 text-orange-700'],
            4 => ['已入库', 'bg-green-100 text-green-700'],
            5 => ['完成', 'bg-green-100 text-green-700'],
            9 => ['已作废', 'bg-gray-100 text-gray-600'],
        ],
        'purchase_return' => [
            1 => ['待审核', 'bg-yellow-100 text-yellow-700'],
            2 => ['已审核', 'bg-blue-100 text-blue-700'],
            3 => ['已出库', 'bg-orange-100 text-orange-700'],
            4 => ['完成', 'bg-green-100 text-green-700'],
            9 => ['已作废', 'bg-gray-100 text-gray-600'],
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
