<?php

namespace App\Services;

use App\Models\PriceChangeLog;
use App\Models\Promotion;
use App\Models\PromotionMemberDiscount;
use App\Models\Sku;
use App\Models\StoreSkuPrice;
use Illuminate\Support\Facades\Auth;

/**
 * 取价服务 — A+E+混合模式
 *
 * 支持两种取价模式：
 * - 最低价模式（lowest）：所有启用的固定价来源取最低值，再叠乘会员折扣率
 * - 命中即止模式（first_hit）：按优先级遍历，第一个有值的来源即最终价
 *
 * 最终售价必须通过限价校验（min_sale_price / max_sale_price 硬约束）
 */
class PricingService
{
    /**
     * 取价来源常量
     */
    public const SOURCE_PROMOTION = 'promotion';
    public const SOURCE_STORE = 'store';
    public const SOURCE_MEMBER = 'member';
    public const SOURCE_CHANNEL = 'channel';
    public const SOURCE_RETAIL = 'retail';

    /**
     * 来源中文名映射
     */
    public static function sourceLabelMap(): array
    {
        return [
            self::SOURCE_PROMOTION => '促销活动',
            self::SOURCE_STORE => '门店差异化',
            self::SOURCE_MEMBER => '会员折扣',
            self::SOURCE_CHANNEL => '渠道基准价',
            self::SOURCE_RETAIL => '标准零售价',
        ];
    }

    /**
     * 来源描述映射（用于取价日志）
     */
    public static function sourceDescriptionMap(): array
    {
        return [
            self::SOURCE_PROMOTION => '促销活动价',
            self::SOURCE_STORE => '门店差异化价',
            self::SOURCE_MEMBER => '会员折扣价',
            self::SOURCE_CHANNEL => '渠道基准价',
            self::SOURCE_RETAIL => '标准零售价',
        ];
    }

    /**
     * 计算最终售价（核心方法）
     *
     * @param Sku $sku 商品SKU
     * @param string $channel 渠道：offline/miniapp/delivery
     * @param int|null $storeId 门店ID（门店差异化价需要）
     * @param int $memberLevel 会员等级：1普通，2银卡，3金卡，4钻石
     * @param int $quantity 购买数量（用于限购校验）
     * @return array{price: int, source: string, details: array}
     */
    public function calculate(Sku $sku, string $channel = 'miniapp', ?int $storeId = null, int $memberLevel = 1, int $quantity = 1): array
    {
        $mode = setting('pricing_mode', 'lowest');
        $sourceEnabled = setting('pricing_source_enabled', [
            'promotion' => true,
            'store' => true,
            'member' => true,
            'channel' => true,
            'retail' => true,
        ]);

        // 确保 sourceEnabled 是数组（从 JSON 配置读取时可能是字符串）
        if (is_string($sourceEnabled)) {
            $sourceEnabled = json_decode($sourceEnabled, true) ?? [];
        }

        if ($mode === 'lowest') {
            $result = $this->calculateLowestPrice($sku, $channel, $storeId, $memberLevel, $quantity, $sourceEnabled);
        } else {
            $result = $this->calculateFirstHitPrice($sku, $channel, $storeId, $memberLevel, $quantity, $sourceEnabled);
        }

        // 第3步：限价校验（硬约束，不可跳过）
        $clampedPrice = $this->clampToLimits($result['price'], $sku);
        $result['price_clamped'] = ($clampedPrice !== $result['price']);
        $result['price'] = $clampedPrice;

        return $result;
    }

    /**
     * 最低价模式：固定价来源取最低，再叠乘会员折扣
     */
    protected function calculateLowestPrice(Sku $sku, string $channel, ?int $storeId, int $memberLevel, int $quantity, array $sourceEnabled): array
    {
        $details = [];
        $fixedPrices = [];

        // 收集固定价来源
        if ($sourceEnabled['retail'] ?? true) {
            $retailPrice = $sku->retail_price;
            $details['retail'] = $retailPrice;
            $fixedPrices['retail'] = $retailPrice;
        }

        if ($sourceEnabled['channel'] ?? true) {
            $channelPrice = $sku->getChannelPrice($channel);
            $details['channel'] = $channelPrice;
            if ($channelPrice > 0) {
                $fixedPrices['channel'] = $channelPrice;
            }
        }

        if ($sourceEnabled['store'] ?? true && $storeId) {
            $storePrice = $this->getStorePrice($sku, $storeId, $memberLevel);
            $details['store'] = $storePrice;
            if ($storePrice > 0) {
                $fixedPrices['store'] = $storePrice;
            }
        }

        if ($sourceEnabled['promotion'] ?? true) {
            $promoPrice = $this->getPromotionPrice($sku, $quantity);
            $details['promotion'] = $promoPrice;
            if ($promoPrice > 0) {
                $fixedPrices['promotion'] = $promoPrice;
            }
        }

        // 取最低固定价
        $basePrice = empty($fixedPrices) ? $sku->retail_price : min($fixedPrices);
        $hitSource = empty($fixedPrices) ? 'retail' : array_search($basePrice, $fixedPrices);

        $result = [
            'price' => $basePrice,
            'source' => $hitSource ?: 'retail',
            'mode' => 'lowest',
            'details' => $details,
            'member_applied' => false,
            'member_discount_rate' => null,
        ];

        // 叠乘会员折扣（最低价模式下会员折扣永远在固定价之后）
        if ($sourceEnabled['member'] ?? true) {
            $memberDiscount = $this->getMemberDiscountRate($memberLevel);
            if ($memberDiscount !== null && $memberDiscount < 10000) {
                $result['price'] = (int) round($basePrice * $memberDiscount / 10000);
                $result['member_applied'] = true;
                $result['member_discount_rate'] = $memberDiscount;
                $result['details']['member'] = $memberDiscount;
            }
        }

        return $result;
    }

    /**
     * 命中即止模式：按优先级遍历，第一个有值的来源即最终价
     */
    protected function calculateFirstHitPrice(Sku $sku, string $channel, ?int $storeId, int $memberLevel, int $quantity, array $sourceEnabled): array
    {
        $priority = setting('pricing_priority', ['promotion', 'store', 'member', 'channel', 'retail']);
        if (is_string($priority)) {
            $priority = json_decode($priority, true) ?? ['promotion', 'store', 'member', 'channel', 'retail'];
        }

        $details = [];
        $allPrices = [
            'promotion' => fn() => $this->getPromotionPrice($sku, $quantity),
            'store' => fn() => $storeId ? $this->getStorePrice($sku, $storeId, $memberLevel) : 0,
            'member' => fn() => $this->getMemberDiscountPrice($sku, $channel, $memberLevel),
            'channel' => fn() => $sku->getChannelPrice($channel),
            'retail' => fn() => $sku->retail_price,
        ];

        foreach ($priority as $source) {
            if (!($sourceEnabled[$source] ?? true)) {
                continue;
            }

            if (!isset($allPrices[$source])) {
                continue;
            }

            $price = $allPrices[$source]();
            $details[$source] = $price;

            if ($price > 0) {
                return [
                    'price' => $price,
                    'source' => $source,
                    'mode' => 'first_hit',
                    'details' => $details,
                    'member_applied' => ($source === 'member'),
                    'member_discount_rate' => ($source === 'member') ? $this->getMemberDiscountRate($memberLevel) : null,
                ];
            }
        }

        // 兜底：标准零售价
        return [
            'price' => $sku->retail_price,
            'source' => 'retail',
            'mode' => 'first_hit',
            'details' => $details,
            'member_applied' => false,
            'member_discount_rate' => null,
        ];
    }

    /**
     * 获取促销活动价
     */
    protected function getPromotionPrice(Sku $sku, int $quantity = 1): int
    {
        $promotionSku = $sku->promotionSkus()
            ->whereHas('promotion', function ($q) {
                $q->where('status', Promotion::STATUS_ENABLED)
                    ->where('approval_status', Promotion::APPROVAL_APPROVED)
                    ->where('start_at', '<=', now())
                    ->where('end_at', '>=', now());
            })
            ->where('status', 1)
            ->orderByDesc('discount_rate') // 优先取折扣力度最大的
            ->first();

        if (!$promotionSku) {
            return 0;
        }

        // 固定价
        if ($promotionSku->price_type === 1 && $promotionSku->fixed_price > 0) {
            return $promotionSku->fixed_price;
        }

        // 折扣率
        if ($promotionSku->price_type === 2 && $promotionSku->discount_rate > 0) {
            return (int) round($sku->retail_price * $promotionSku->discount_rate / 10000);
        }

        return 0;
    }

    /**
     * 获取门店差异化价
     */
    protected function getStorePrice(Sku $sku, int $storeId, int $memberLevel = 0): int
    {
        $storePrice = StoreSkuPrice::where('sku_id', $sku->id)
            ->where('store_id', $storeId)
            ->enabled()
            ->effective()
            ->first();

        if (!$storePrice) {
            return 0;
        }

        // 限定会员等级（0=不限定）
        if ($storePrice->member_level > 0 && $storePrice->member_level !== $memberLevel) {
            return 0;
        }

        return match ($storePrice->adjust_mode) {
            StoreSkuPrice::ADJUST_FIXED_AMOUNT => $sku->retail_price + $storePrice->adjust_value,
            StoreSkuPrice::ADJUST_PERCENTAGE => (int) round($sku->retail_price * $storePrice->adjust_value / 10000),
            StoreSkuPrice::ADJUST_OVERRIDE => $storePrice->adjust_value,
            default => 0,
        };
    }

    /**
     * 获取会员折扣率（万分比）
     * 返回 null 表示无折扣，返回 <10000 的值表示有折扣
     */
    protected function getMemberDiscountRate(int $memberLevel): ?int
    {
        // 优先查限时活动折扣
        $discount = PromotionMemberDiscount::where('member_level', $memberLevel)
            ->where('status', PromotionMemberDiscount::STATUS_ENABLED)
            ->whereHas('promotion', function ($q) {
                $q->where('status', Promotion::STATUS_ENABLED)
                    ->where('approval_status', Promotion::APPROVAL_APPROVED)
                    ->where('start_at', '<=', now())
                    ->where('end_at', '>=', now());
            })
            ->orderByDesc('discount_rate') // 折扣率越小越优惠，取最低
            ->first();

        // 其次查常驻折扣
        if (!$discount) {
            $discount = PromotionMemberDiscount::where('member_level', $memberLevel)
                ->where('status', PromotionMemberDiscount::STATUS_ENABLED)
                ->where('is_permanent', 1)
                ->first();
        }

        if (!$discount || $discount->discount_rate >= 10000) {
            return null; // 无折扣
        }

        return $discount->discount_rate;
    }

    /**
     * 获取会员折扣后的价格（命中即止模式用）
     */
    protected function getMemberDiscountPrice(Sku $sku, string $channel, int $memberLevel): int
    {
        $discountRate = $this->getMemberDiscountRate($memberLevel);
        if ($discountRate === null) {
            return 0;
        }

        $basePrice = $sku->getChannelPrice($channel);
        return (int) round($basePrice * $discountRate / 10000);
    }

    /**
     * 限价校验（硬约束）
     */
    protected function clampToLimits(int $price, Sku $sku): int
    {
        if ($sku->min_sale_price > 0 && $price < $sku->min_sale_price) {
            return $sku->min_sale_price;
        }
        if ($sku->max_sale_price > 0 && $price > $sku->max_sale_price) {
            return $sku->max_sale_price;
        }
        return $price;
    }

    /**
     * 记录改价日志
     *
     * @param Sku $sku
     * @param int $originalPrice 改价前单价（厘）
     * @param int $newPrice 改价后单价（厘）
     * @param int $sourceType 来源类型（PriceChangeLog::SOURCE_*）
     * @param int|null $sourceId 来源ID
     * @param int $targetType 作用单据类型
     * @param int|null $targetId 单据ID
     * @param int|null $targetItemId 单据明细ID
     * @param int $quantity 数量
     * @param string|null $reason 改价原因
     */
    public function logPriceChange(
        Sku $sku,
        int $originalPrice,
        int $newPrice,
        int $sourceType,
        ?int $sourceId = null,
        int $targetType = PriceChangeLog::TARGET_ORDER,
        ?int $targetId = null,
        ?int $targetItemId = null,
        int $quantity = 1,
        ?string $reason = null,
    ): PriceChangeLog {
        return PriceChangeLog::create([
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'target_item_id' => $targetItemId,
            'original_price' => $originalPrice,
            'new_price' => $newPrice,
            'quantity' => $quantity,
            'amount_diff' => ($newPrice - $originalPrice) * $quantity,
            'operator_id' => Auth::id(),
            'role_ids' => Auth::user()?->roles->pluck('id')->toArray() ?? [],
            'reason' => $reason,
            'before_data' => ['sku_id' => $sku->id, 'retail_price' => $sku->retail_price],
            'after_data' => ['source' => $sourceType, 'new_price' => $newPrice],
        ]);
    }
}
