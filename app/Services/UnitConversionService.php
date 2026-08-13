<?php

namespace App\Services;

use App\Models\Unit;
use App\Models\UnitConversion;
use Illuminate\Support\Facades\Cache;

/**
 * 单位换算服务
 *
 * 核心逻辑：
 * - 每个 SKU 严格单链路换算（箱→件→包），不可分叉
 * - parent_conversion_id 串联链路关系
 * - 所有业务数量统一用 base_unit（最小单位）存储
 * - 显示时通过 formatHuman() 转为"2箱1件10包"格式
 *
 * 链路结构示例：
 *   箱(1) → 件(ratio=6) → 包(ratio=10)
 *   数据库记录：
 *     id=1: sku_id=5, from=箱, to=件, ratio=6, parent=null（链路起点）
 *     id=2: sku_id=5, from=件, to=包, ratio=10, parent=1（链路下游）
 */
class UnitConversionService
{
    /**
     * 缓存前缀
     */
    private const CACHE_PREFIX = 'unit_conversion_chain:';
    private const CACHE_TTL = 3600; // 1小时

    /**
     * 获取指定 SKU 的完整换算链路（从大到小）
     *
     * 返回格式：[
     *   ['unit_id' => 1, 'unit_name' => '箱', 'ratio' => 1, 'total_ratio' => 60],
     *   ['unit_id' => 2, 'unit_name' => '件', 'ratio' => 6, 'total_ratio' => 10],
     *   ['unit_id' => 3, 'unit_name' => '包', 'ratio' => 10, 'total_ratio' => 1],
     * ]
     *
     * total_ratio 表示 1 个该单位 = N 个 base_unit
     * 链路中最小单位（最末端）的 total_ratio = 1
     *
     * @param int $skuId
     * @return array 换算链路，从大到小排列
     */
    public function getChain(int $skuId): array
    {
        return Cache::remember(
            self::CACHE_PREFIX . $skuId,
            self::CACHE_TTL,
            function () use ($skuId) {
                return $this->buildChain($skuId);
            }
        );
    }

    /**
     * 构建换算链路
     */
    private function buildChain(int $skuId): array
    {
        $conversions = UnitConversion::where('sku_id', $skuId)
            ->where('status', UnitConversion::STATUS_ENABLED)
            ->orderBy('sort')
            ->get();

        if ($conversions->isEmpty()) {
            return [];
        }

        // 找到链路起点：parent_conversion_id 为 null 的记录
        $startConversion = $conversions->first(fn($c) => $c->parent_conversion_id === null);

        if (!$startConversion) {
            // 如果没有明确起点，取第一条
            $startConversion = $conversions->first();
        }

        // 沿链路向下遍历，构建有序链路
        $chain = [];
        $currentConversion = $startConversion;

        // 先加入起点的大单位
        $fromUnit = Unit::find($currentConversion->from_unit_id);
        if ($fromUnit) {
            $chain[] = [
                'unit_id' => $fromUnit->id,
                'unit_name' => $fromUnit->name,
                'ratio' => 1, // 自身到自身的比率
                'conversion_ratio' => null, // 到下一级的换算比
            ];
        }

        // 沿链路遍历
        while ($currentConversion) {
            $toUnit = Unit::find($currentConversion->to_unit_id);
            if ($toUnit) {
                $chain[] = [
                    'unit_id' => $toUnit->id,
                    'unit_name' => $toUnit->name,
                    'ratio' => $currentConversion->ratio,
                    'conversion_ratio' => $currentConversion->ratio, // 到上一级的换算比
                ];
            }

            // 找下一个（child）
            $nextConversion = $conversions->first(
                fn($c) => $c->parent_conversion_id === $currentConversion->id
            );
            $currentConversion = $nextConversion;
        }

        // 计算 total_ratio：1 个该单位 = N 个 base_unit
        $chain = $this->calculateTotalRatios($chain);

        return $chain;
    }

    /**
     * 计算每个单位到 base_unit 的总换算系数
     *
     * 从链路末端（最小单位）向上累乘
     */
    private function calculateTotalRatios(array $chain): array
    {
        if (empty($chain)) {
            return $chain;
        }

        // 最小单位的 total_ratio = 1
        $chain[count($chain) - 1]['total_ratio'] = 1;

        // 从倒数第二个向上计算
        for ($i = count($chain) - 2; $i >= 0; $i--) {
            // 当前单位的 total_ratio = 下一级的 ratio × 下一级的 total_ratio
            $nextRatio = $chain[$i + 1]['ratio'];
            $nextTotalRatio = $chain[$i + 1]['total_ratio'];
            $chain[$i]['total_ratio'] = $nextRatio * $nextTotalRatio;
        }

        // 第一个单位的 ratio 设为 total_ratio（自身就是最大的）
        // chain[0] 的 ratio 本来是 1（自身），但 conversion_ratio 记录的是到下一级的比

        return $chain;
    }

    /**
     * 将 base_unit 数量转换为人类可读格式
     *
     * 示例：130包 → "2箱1件10包"
     * 示例：60包 → "1箱"
     * 示例：7包 → "7包"
     * 示例：0包 → "0"
     *
     * @param int $skuId SKU ID
     * @param int $baseQuantity base_unit 数量
     * @return string 人类可读格式
     */
    public function formatHuman(int $skuId, int $baseQuantity): string
    {
        if ($baseQuantity <= 0) {
            return (string) $baseQuantity;
        }

        $chain = $this->getChain($skuId);

        if (empty($chain)) {
            // 没有换算关系，直接返回数字
            return (string) $baseQuantity;
        }

        $remaining = $baseQuantity;
        $parts = [];

        // 从大到小逐级整除
        foreach ($chain as $level) {
            $totalRatio = $level['total_ratio'];
            if ($totalRatio <= 0) {
                continue;
            }

            $count = intdiv($remaining, $totalRatio);
            $remaining = $remaining % $totalRatio;

            if ($count > 0) {
                $parts[] = $count . $level['unit_name'];
            }
        }

        // 如果全整除了，parts 不为空；如果有余数但链路结束，不忽略
        // 实际上 base_unit 是链路最末端，total_ratio=1，所以 remaining 最终一定为 0

        return $parts ? implode('', $parts) : '0';
    }

    /**
     * 将指定单位的数量转换为 base_unit 数量
     *
     * 示例：2箱 → 120包（假设 1箱=6件, 1件=10包）
     * 示例：5件 → 50包
     * 示例：7包 → 7
     *
     * @param int $skuId SKU ID
     * @param int $unitId 选择的单位 ID
     * @param int $quantity 选择的单位数量
     * @return int base_unit 数量
     */
    public function convertToBase(int $skuId, int $unitId, int $quantity): int
    {
        if ($quantity <= 0) {
            return 0;
        }

        $chain = $this->getChain($skuId);

        if (empty($chain)) {
            // 没有换算关系，直接返回
            return $quantity;
        }

        // 在链路中找到对应单位
        foreach ($chain as $level) {
            if ($level['unit_id'] === $unitId) {
                return $quantity * $level['total_ratio'];
            }
        }

        // 未找到换算关系，直接返回（视为 base_unit）
        return $quantity;
    }

    /**
     * 从 base_unit 数量反向转换到指定单位的数量
     *
     * 示例：120包 → 2（单位=箱）
     * 示例：50包 → 5（单位=件）
     * 注意：只返回整数部分，余数忽略
     *
     * @param int $skuId SKU ID
     * @param int $unitId 目标单位 ID
     * @param int $baseQuantity base_unit 数量
     * @return int 在目标单位下的整数数量
     */
    public function convertFromBase(int $skuId, int $unitId, int $baseQuantity): int
    {
        if ($baseQuantity <= 0) {
            return 0;
        }

        $chain = $this->getChain($skuId);

        foreach ($chain as $level) {
            if ($level['unit_id'] === $unitId) {
                return intdiv($baseQuantity, $level['total_ratio']);
            }
        }

        return $baseQuantity;
    }

    /**
     * 任意两个单位之间的换算
     *
     * @param int $skuId SKU ID
     * @param int $fromUnitId 源单位 ID
     * @param int $toUnitId 目标单位 ID
     * @param int $quantity 源单位数量
     * @return float 目标单位数量（可能不是整数）
     */
    public function convert(int $skuId, int $fromUnitId, int $toUnitId, int $quantity): float
    {
        if ($fromUnitId === $toUnitId) {
            return (float) $quantity;
        }

        $chain = $this->getChain($skuId);

        $fromTotalRatio = null;
        $toTotalRatio = null;

        foreach ($chain as $level) {
            if ($level['unit_id'] === $fromUnitId) {
                $fromTotalRatio = $level['total_ratio'];
            }
            if ($level['unit_id'] === $toUnitId) {
                $toTotalRatio = $level['total_ratio'];
            }
        }

        if ($fromTotalRatio === null || $toTotalRatio === null || $toTotalRatio === 0) {
            return (float) $quantity;
        }

        // 先转 base_unit 再转目标单位
        $baseQuantity = $quantity * $fromTotalRatio;

        return $baseQuantity / $toTotalRatio;
    }

    /**
     * 获取 SKU 的可选单位列表（用于下单/采购页面下拉选择）
     *
     * 返回格式：[
     *   ['unit_id' => 1, 'unit_name' => '箱', 'total_ratio' => 60],
     *   ['unit_id' => 2, 'unit_name' => '件', 'total_ratio' => 10],
     *   ['unit_id' => 3, 'unit_name' => '包', 'total_ratio' => 1],
     * ]
     *
     * @param int $skuId SKU ID
     * @return array
     */
    public function getAvailableUnits(int $skuId): array
    {
        return $this->getChain($skuId);
    }

    /**
     * 格式化显示：下单数量 + 单位 → 带换算的显示
     *
     * 示例：2箱 → "2箱（120包）"
     * 示例：7包 → "7包"
     *
     * @param int $skuId SKU ID
     * @param int $unitId 选择的单位 ID
     * @param int $unitQuantity 选择的单位数量
     * @return string 格式化显示
     */
    public function formatWithConversion(int $skuId, int $unitId, int $unitQuantity): string
    {
        $chain = $this->getChain($skuId);
        $unitName = '';
        $isBaseUnit = false;

        foreach ($chain as $level) {
            if ($level['unit_id'] === $unitId) {
                $unitName = $level['unit_name'];
                $isBaseUnit = ($level['total_ratio'] === 1);
                break;
            }
        }

        if (empty($unitName)) {
            return (string) $unitQuantity;
        }

        $display = $unitQuantity . $unitName;

        // 如果不是 base_unit，括号内显示 base_unit 数量
        if (!$isBaseUnit && $unitQuantity > 0) {
            $baseQty = $this->convertToBase($skuId, $unitId, $unitQuantity);
            // 找到 base_unit 名称
            $baseUnitName = '';
            foreach ($chain as $level) {
                if ($level['total_ratio'] === 1) {
                    $baseUnitName = $level['unit_name'];
                    break;
                }
            }
            if ($baseUnitName) {
                $display .= "（{$baseQty}{$baseUnitName}）";
            }
        }

        return $display;
    }

    /**
     * 清除指定 SKU 的换算缓存
     */
    public function clearCache(int $skuId): void
    {
        Cache::forget(self::CACHE_PREFIX . $skuId);
    }

    /**
     * 清除所有换算缓存
     */
    public function clearAllCache(): void
    {
        // 简单处理：增加全局前缀版本号即可
        // 但这里用扫描方式也行，因为 SKU 数量不会特别大
        $keys = Cache::get(self::CACHE_PREFIX . 'all_keys', []);
        foreach ($keys as $skuId) {
            Cache::forget(self::CACHE_PREFIX . $skuId);
        }
    }

    /**
     * 验证换算链路是否有效（无循环引用、无断裂）
     *
     * @param int $skuId SKU ID
     * @return array ['valid' => bool, 'error' => string|null]
     */
    public function validateChain(int $skuId): array
    {
        $conversions = UnitConversion::where('sku_id', $skuId)->get();

        if ($conversions->isEmpty()) {
            return ['valid' => true, 'error' => null];
        }

        // 检查是否有起点
        $startPoints = $conversions->filter(fn($c) => $c->parent_conversion_id === null);
        if ($startPoints->count() === 0) {
            return ['valid' => false, 'error' => '链路无起点，每条链路需要一个 parent_conversion_id 为 null 的根节点'];
        }
        if ($startPoints->count() > 1) {
            return ['valid' => false, 'error' => '链路有多个起点，严格单链路只能有一个根节点'];
        }

        // 沿链路遍历，检测循环和断裂
        $visited = [];
        $current = $startPoints->first();

        while ($current) {
            if (in_array($current->id, $visited)) {
                return ['valid' => false, 'error' => "链路存在循环引用（conversion_id={$current->id}）"];
            }
            $visited[] = $current->id;

            $child = $conversions->first(fn($c) => $c->parent_conversion_id === $current->id);
            $current = $child;
        }

        // 检查是否所有节点都在链路中（无游离节点）
        $allIds = $conversions->pluck('id')->toArray();
        $unvisited = array_diff($allIds, $visited);
        if (count($unvisited) > 0) {
            return ['valid' => false, 'error' => '链路存在游离节点，不在主链路中（id: ' . implode(',', $unvisited) . '）'];
        }

        return ['valid' => true, 'error' => null];
    }
}
