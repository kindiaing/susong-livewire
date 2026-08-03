<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * 商家可见性配置模型
 *
 * 支持两种配置维度：
 * - 商品级 (target_type=product)：控制商家对整件商品（含所有SKU）的可见性
 * - SKU级 (target_type=sku)：控制商家对单个SKU的可见性
 *
 * 查询优先级：SKU级 > 商品级 > 默认可见
 *
 * @property int $id
 * @property int $merchant_id 商家ID
 * @property string $target_type 配置类型：product=商品级，sku=SKU级
 * @property int|null $product_id 商品ID（商品级配置时使用）
 * @property int|null $sku_id SKU ID（SKU级配置时使用）
 * @property int $is_visible 是否可见：0否，1是
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class MerchantSkuVisibility extends Model
{
    protected $table = 'merchant_sku_visibility';

    // 配置类型常量
    public const TARGET_TYPE_PRODUCT = 'product';
    public const TARGET_TYPE_SKU = 'sku';

    protected $fillable = [
        'merchant_id',
        'target_type',
        'product_id',
        'sku_id',
        'is_visible',
    ];

    protected function casts(): array
    {
        return [
            'merchant_id' => 'integer',
            'product_id' => 'integer',
            'sku_id' => 'integer',
            'is_visible' => 'integer',
        ];
    }

    /**
     * 配置类型映射
     */
    public static function targetTypeMap(): array
    {
        return [
            self::TARGET_TYPE_PRODUCT => '商品级',
            self::TARGET_TYPE_SKU => 'SKU级',
        ];
    }

    /**
     * 关联商家
     */
    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    /**
     * 关联商品（商品级配置时使用）
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * 关联 SKU（SKU级配置时使用）
     */
    public function sku()
    {
        return $this->belongsTo(Sku::class);
    }

    /**
     * 获取目标名称（商品名或SKU编码）
     */
    public function getTargetNameAttribute(): string
    {
        if ($this->target_type === self::TARGET_TYPE_PRODUCT) {
            return $this->product?->name ?? '-';
        }
        return ($this->sku?->product?->name ?? '-') . ' / ' . ($this->sku?->sku_code ?? '-');
    }

    /**
     * 作用域：可见
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', 1);
    }

    /**
     * 作用域：按商家筛选
     */
    public function scopeByMerchant($query, int $merchantId)
    {
        return $query->where('merchant_id', $merchantId);
    }

    /**
     * 作用域：按商品级筛选
     */
    public function scopeProductLevel($query)
    {
        return $query->where('target_type', self::TARGET_TYPE_PRODUCT);
    }

    /**
     * 作用域：按SKU级筛选
     */
    public function scopeSkuLevel($query)
    {
        return $query->where('target_type', self::TARGET_TYPE_SKU);
    }

    /**
     * 判断商家是否可见某SKU（查询优先级：SKU级 > 商品级 > 默认可见）
     */
    public static function isSkuVisible(int $merchantId, int $skuId): bool
    {
        $sku = Sku::find($skuId);
        if (! $sku) return false;

        // 1. 先查SKU级配置
        $skuConfig = static::where('merchant_id', $merchantId)
            ->where('target_type', self::TARGET_TYPE_SKU)
            ->where('sku_id', $skuId)
            ->first();
        if ($skuConfig) return (bool) $skuConfig->is_visible;

        // 2. 再查商品级配置
        $productConfig = static::where('merchant_id', $merchantId)
            ->where('target_type', self::TARGET_TYPE_PRODUCT)
            ->where('product_id', $sku->product_id)
            ->first();
        if ($productConfig) return (bool) $productConfig->is_visible;

        // 3. 默认可见
        return true;
    }
}
