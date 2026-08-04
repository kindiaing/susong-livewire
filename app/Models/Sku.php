<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * SKU 规格模型
 *
 * @property int $id
 * @property int $product_id 商品ID
 * @property string $sku_code SKU编码
 * @property array|null $specs 规格属性
 * @property int $purchase_price 标准采购价（厘）
 * @property int $cost_price 加权平均成本价（厘）
 * @property int $min_purchase_price 最低采购限价（厘）
 * @property int $list_price 吊牌价（厘）
 * @property int $retail_price 标准零售价（厘）
 * @property int $wholesale_price 批发团购价（厘）
 * @property int $employee_price 员工内部价（厘）
 * @property int $offline_price 门店基准价（厘）
 * @property int $miniapp_price 小程序基准价（厘）
 * @property int $delivery_price 配送基准价（厘）
 * @property int $min_sale_price 最低销售限价（厘）
 * @property int $max_sale_price 最高销售限价（厘）
 * @property int $stock 当前库存
 * @property int $status 状态：0禁用，1启用
 * @property int $approval_status 审核状态：1待审核，2已通过，3已拒绝
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Sku extends Model
{
    use SoftDeletes;

    // 状态常量
    public const STATUS_DISABLED = 0;
    public const STATUS_ENABLED = 1;

    // 审核状态常量
    public const APPROVAL_PENDING = 1;
    public const APPROVAL_APPROVED = 2;
    public const APPROVAL_REJECTED = 3;

    protected $fillable = [
        'product_id',
        'sku_code',
        'specs',
        'purchase_price',
        'cost_price',
        'min_purchase_price',
        'list_price',
        'retail_price',
        'wholesale_price',
        'employee_price',
        'offline_price',
        'miniapp_price',
        'delivery_price',
        'min_sale_price',
        'max_sale_price',
        'stock',
        'status',
        'approval_status',
    ];

    protected function casts(): array
    {
        return [
            'product_id' => 'integer',
            'specs' => 'array',
            'purchase_price' => 'integer',
            'cost_price' => 'integer',
            'min_purchase_price' => 'integer',
            'list_price' => 'integer',
            'retail_price' => 'integer',
            'wholesale_price' => 'integer',
            'employee_price' => 'integer',
            'offline_price' => 'integer',
            'miniapp_price' => 'integer',
            'delivery_price' => 'integer',
            'min_sale_price' => 'integer',
            'max_sale_price' => 'integer',
            'stock' => 'integer',
            'status' => 'integer',
            'approval_status' => 'integer',
        ];
    }

    /**
     * 状态映射
     */
    public static function statusMap(): array
    {
        return [
            self::STATUS_ENABLED => '启用',
            self::STATUS_DISABLED => '禁用',
        ];
    }

    /**
     * 审核状态映射
     */
    public static function approvalStatusMap(): array
    {
        return [
            self::APPROVAL_PENDING => '待审核',
            self::APPROVAL_APPROVED => '已通过',
            self::APPROVAL_REJECTED => '已拒绝',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusMap()[$this->status] ?? '未知';
    }

    public function getApprovalStatusLabelAttribute(): string
    {
        return self::approvalStatusMap()[$this->approval_status] ?? '未知';
    }

    /**
     * 关联商品
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * 关联条码
     */
    public function barcodes()
    {
        return $this->hasMany(SkuBarcode::class);
    }

    /**
     * 关联供应商（一品多供）
     */
    public function skuSuppliers()
    {
        return $this->hasMany(SkuSupplier::class);
    }

    /**
     * 关联供应商（多对多，通过 sku_suppliers 中间表）
     */
    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'sku_suppliers', 'sku_id', 'supplier_id')
            ->withPivot(['is_default', 'purchase_price', 'status', 'sort'])
            ->withTimestamps();
    }

    /**
     * 关联商家可见性
     */
    public function merchantVisibilities()
    {
        return $this->hasMany(MerchantSkuVisibility::class);
    }

    /**
     * 作用域：启用
     */
    public function scopeEnabled($query)
    {
        return $query->where('status', self::STATUS_ENABLED);
    }

    /**
     * 作用域：禁用
     */
    public function scopeDisabled($query)
    {
        return $query->where('status', self::STATUS_DISABLED);
    }

    /**
     * 作用域：待审核
     */
    public function scopePendingApproval($query)
    {
        return $query->where('approval_status', self::APPROVAL_PENDING);
    }

    /**
     * 作用域：已通过审核
     */
    public function scopeApproved($query)
    {
        return $query->where('approval_status', self::APPROVAL_APPROVED);
    }

    /**
     * 作用域：按商品筛选
     */
    public function scopeByProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }
}
