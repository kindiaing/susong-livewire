<?php

namespace App\Livewire\Price;

use App\Livewire\Traits\WithConfirmAction;
use App\Livewire\Traits\WithMoneyConversion;
use App\Livewire\Traits\WithToast;
use App\Models\Merchant;
use App\Models\Promotion;
use App\Models\PromotionBundle;
use App\Models\PromotionBundleItem;
use App\Models\PromotionClearance;
use App\Models\PromotionCoupon;
use App\Models\PromotionFlashSale;
use App\Models\PromotionFullReduction;
use App\Models\PromotionGroupBuy;
use App\Models\PromotionMemberDiscount;
use App\Models\PromotionSku;
use App\Models\StoreSkuPrice;
use App\Models\Sku;
use Livewire\Component;
use Livewire\WithPagination;

class PromotionSettings extends Component
{
    use WithPagination;
    use WithToast;
    use WithConfirmAction;
    use WithMoneyConversion;

    // ── 左侧导航 ──
    public string $activeGroup = 'promotion';

    // ── 促销活动 ──
    public string $promoSearch = '';
    public bool $showPromoCreateModal = false;
    public string $promoFormName = '';
    public int $promoFormType = 1;
    public string $promoFormStartAt = '';
    public string $promoFormEndAt = '';

    // ── 商家价格 ──
    public string $storePriceSearch = '';
    public ?int $storePriceMerchantFilter = null;
    public bool $showStorePriceCreateModal = false;
    public ?int $storePriceFormMerchantId = null;
    public ?int $storePriceFormSkuId = null;
    public int $storePriceFormPriceType = 1;
    public int $storePriceFormAdjustMode = 1;
    public int $storePriceFormAdjustValue = 0;
    public string $storePriceFormEffectiveAt = '';
    public string $storePriceFormExpireAt = '';
    public ?int $editingStorePriceId = null;

    // ── 会员折扣 ──
    public bool $showMemberDiscountModal = false;
    public int $memberDiscountFormLevel = 1;
    public int $memberDiscountFormRate = 10000;
    public int $memberDiscountFormIsPermanent = 1;
    public ?int $memberDiscountFormPromotionId = null;
    public ?int $editingMemberDiscountId = null;

    // ── 活动明细（按活动类型维护子表明细）──
    public string $detailSearch = '';
    public ?int $detailPromotionId = null;

    // 活动商品（promotion_skus，普通促销取价数据源）
    public bool $showDetailSkuModal = false;
    public ?int $editingDetailSkuId = null;
    public ?int $detailSkuFormSkuId = null;
    public int $detailSkuFormPriceType = 1;
    public string $detailSkuFormFixedPrice = '';
    public string $detailSkuFormDiscountRate = '';
    public int $detailSkuFormMaxQuantity = 0;
    public int $detailSkuFormMaxPerCustomer = 0;
    public int $detailSkuFormStockLimit = 0;

    // 满减阶梯
    public bool $showFullReductionModal = false;
    public ?int $editingFullReductionId = null;
    public int $fullReductionFormThresholdType = 1;
    public string $fullReductionFormThresholdAmount = '';
    public int $fullReductionFormReductionType = 1;
    public string $fullReductionFormReductionAmount = '';
    public string $fullReductionFormDiscountRate = '';
    public ?int $fullReductionFormGiftSkuId = null;
    public int $fullReductionFormGiftQuantity = 0;
    public int $fullReductionFormIsStacked = 0;

    // 优惠券
    public bool $showCouponModal = false;
    public ?int $editingCouponId = null;
    public string $couponFormCouponCode = '';
    public string $couponFormName = '';
    public int $couponFormType = 1;
    public string $couponFormThresholdAmount = '';
    public string $couponFormReductionAmount = '';
    public string $couponFormDiscountRate = '';
    public string $couponFormMaxDiscount = '';
    public int $couponFormTotalQuantity = 0;
    public int $couponFormPerUserLimit = 1;
    public int $couponFormValidDays = 30;

    // 组合套餐（含套餐明细行）
    public bool $showBundleModal = false;
    public ?int $editingBundleId = null;
    public string $bundleFormName = '';
    public string $bundleFormPrice = '';
    public int $bundleFormQuantity = 1;
    /**
     * 组合套餐明细行（含 sku_id/quantity）
     *
     * @var array<int, array{sku_id: int|string|null, quantity: int|string}>
     */
    public array $bundleFormItems = [];

    // 秒杀
    public bool $showFlashSaleModal = false;
    public ?int $editingFlashSaleId = null;
    public string $flashSaleFormPrice = '';
    public int $flashSaleFormTotalStock = 0;
    public int $flashSaleFormPerUserLimit = 1;
    public string $flashSaleFormStartAt = '';
    public string $flashSaleFormEndAt = '';

    // 拼团
    public bool $showGroupBuyModal = false;
    public ?int $editingGroupBuyId = null;
    public string $groupBuyFormPrice = '';
    public int $groupBuyFormMinSize = 2;
    public int $groupBuyFormMaxSize = 10;
    public int $groupBuyFormTimeLimit = 1440;
    public int $groupBuyFormVirtualJoin = 0;

    // 清仓临期
    public bool $showClearanceModal = false;
    public ?int $editingClearanceId = null;
    public int $clearanceFormType = 1;
    public string $clearanceFormExpiryDate = '';
    public string $clearanceFormDiscountRate = '';
    public string $clearanceFormFixedPrice = '';

    public function mount(): void
    {
        // 支持从促销活动列表跳转：/promotion-settings?group=detail&promotion_id=X
        $this->activeGroup = (string) request()->query('group', 'promotion');
        if (! array_key_exists($this->activeGroup, ['promotion' => 1, 'store' => 1, 'member' => 1, 'detail' => 1])) {
            $this->activeGroup = 'promotion';
        }
        $detailPromotionId = (int) request()->query('promotion_id', 0);
        if ($detailPromotionId > 0) {
            $this->detailPromotionId = $detailPromotionId;
        }
        $this->initPromotionSettings();
    }

    protected function initPromotionSettings(): void {}

    // ══════════════════════════════════════
    //  导航切换
    // ══════════════════════════════════════

    public function setActiveGroup(string $group): void
    {
        $this->activeGroup = $group;
        $this->resetPage();
    }

    // ══════════════════════════════════════
    //  促销活动 CRUD
    // ══════════════════════════════════════

    public function openPromoCreateModal(): void
    {
        $this->resetPromoForm();
        $this->showPromoCreateModal = true;
    }

    public function closePromoCreateModal(): void
    {
        $this->showPromoCreateModal = false;
        $this->resetErrorBag();
    }

    public function createPromotion(): void
    {
        $this->validate([
            'promoFormName'    => 'required|max:100',
            'promoFormType'    => 'required|integer|in:1,2,3,4,5,6,7,8',
            'promoFormStartAt' => 'required|date',
            'promoFormEndAt'   => 'required|date|after:promoFormStartAt',
        ], [], [
            'promoFormName'    => '活动名称',
            'promoFormType'    => '活动类型',
            'promoFormStartAt' => '开始时间',
            'promoFormEndAt'   => '结束时间',
        ]);

        Promotion::create([
            'name'       => $this->promoFormName,
            'promo_type' => $this->promoFormType,
            'start_at'   => $this->promoFormStartAt,
            'end_at'     => $this->promoFormEndAt,
            'status'     => Promotion::STATUS_DISABLED,
            'created_by' => auth()->id(),
        ]);

        $this->closePromoCreateModal();
        $this->toastSuccess('促销活动已创建');
    }

    public function togglePromoStatus(int $id): void
    {
        $promo = Promotion::findOrFail($id);
        $promo->status = $promo->status === Promotion::STATUS_ENABLED
            ? Promotion::STATUS_DISABLED
            : Promotion::STATUS_ENABLED;
        $promo->save();
        $this->toastSuccess($promo->status ? '已启用' : '已禁用');
    }

    public function deletePromotion(int $id): void
    {
        abort_unless(auth()->user()?->can('price.promotion.delete'), 403, '无删除权限');
        Promotion::findOrFail($id)->delete();
        $this->toastSuccess('促销活动已删除');
    }

    // ══════════════════════════════════════
    //  商家价格 CRUD
    // ══════════════════════════════════════

    public function openStorePriceCreateModal(): void
    {
        $this->resetStorePriceForm();
        $this->showStorePriceCreateModal = true;
    }

    public function closeStorePriceCreateModal(): void
    {
        $this->showStorePriceCreateModal = false;
        $this->resetErrorBag();
    }

    public function createStorePrice(): void
    {
        $this->validate([
            'storePriceFormMerchantId'  => 'required|integer|exists:merchants,id',
            'storePriceFormSkuId'       => 'required|integer|exists:skus,id',
            'storePriceFormPriceType'   => 'required|integer|in:1,2,3',
            'storePriceFormAdjustMode'  => 'required|integer|in:1,2,3',
            'storePriceFormAdjustValue' => 'required|integer',
            'storePriceFormEffectiveAt' => 'nullable|date',
            'storePriceFormExpireAt'    => 'nullable|date|after:storePriceFormEffectiveAt',
        ], [], [
            'storePriceFormMerchantId'  => '商家',
            'storePriceFormSkuId'       => 'SKU',
            'storePriceFormPriceType'   => '价格类型',
            'storePriceFormAdjustMode'  => '调整方式',
            'storePriceFormAdjustValue' => '调整值',
            'storePriceFormEffectiveAt' => '生效时间',
            'storePriceFormExpireAt'    => '失效时间',
        ]);

        StoreSkuPrice::create([
            'store_id'      => $this->storePriceFormMerchantId,
            'sku_id'        => $this->storePriceFormSkuId,
            'price_type'    => $this->storePriceFormPriceType,
            'adjust_mode'   => $this->storePriceFormAdjustMode,
            'adjust_value'  => $this->storePriceFormAdjustValue,
            'effective_at'  => $this->storePriceFormEffectiveAt ?: null,
            'expire_at'     => $this->storePriceFormExpireAt ?: null,
            'status'        => StoreSkuPrice::STATUS_ENABLED,
        ]);

        $this->closeStorePriceCreateModal();
        $this->toastSuccess('商家价格已创建');
    }

    public function editStorePrice(int $id): void
    {
        $sp = StoreSkuPrice::findOrFail($id);
        $this->editingStorePriceId = $id;
        $this->storePriceFormMerchantId = $sp->store_id;
        $this->storePriceFormSkuId = $sp->sku_id;
        $this->storePriceFormPriceType = $sp->price_type;
        $this->storePriceFormAdjustMode = $sp->adjust_mode;
        $this->storePriceFormAdjustValue = $sp->adjust_value;
        $this->storePriceFormEffectiveAt = $sp->effective_at ? $sp->effective_at->format('Y-m-d\TH:i') : '';
        $this->storePriceFormExpireAt = $sp->expire_at ? $sp->expire_at->format('Y-m-d\TH:i') : '';
        $this->showStorePriceCreateModal = true;
    }

    public function updateStorePrice(): void
    {
        $this->validate([
            'storePriceFormMerchantId'  => 'required|integer|exists:merchants,id',
            'storePriceFormSkuId'       => 'required|integer|exists:skus,id',
            'storePriceFormPriceType'   => 'required|integer|in:1,2,3',
            'storePriceFormAdjustMode'  => 'required|integer|in:1,2,3',
            'storePriceFormAdjustValue' => 'required|integer',
            'storePriceFormEffectiveAt' => 'nullable|date',
            'storePriceFormExpireAt'    => 'nullable|date|after:storePriceFormEffectiveAt',
        ]);

        $sp = StoreSkuPrice::findOrFail($this->editingStorePriceId);
        $sp->update([
            'store_id'      => $this->storePriceFormMerchantId,
            'sku_id'        => $this->storePriceFormSkuId,
            'price_type'    => $this->storePriceFormPriceType,
            'adjust_mode'   => $this->storePriceFormAdjustMode,
            'adjust_value'  => $this->storePriceFormAdjustValue,
            'effective_at'  => $this->storePriceFormEffectiveAt ?: null,
            'expire_at'     => $this->storePriceFormExpireAt ?: null,
        ]);

        $this->closeStorePriceCreateModal();
        $this->editingStorePriceId = null;
        $this->toastSuccess('商家价格已更新');
    }

    public function saveStorePrice(): void
    {
        if ($this->editingStorePriceId) {
            $this->updateStorePrice();
        } else {
            $this->createStorePrice();
        }
    }

    public function toggleStorePriceStatus(int $id): void
    {
        $sp = StoreSkuPrice::findOrFail($id);
        $sp->status = $sp->status === StoreSkuPrice::STATUS_ENABLED
            ? StoreSkuPrice::STATUS_DISABLED
            : StoreSkuPrice::STATUS_ENABLED;
        $sp->save();
        $this->toastSuccess($sp->status ? '已启用' : '已禁用');
    }

    public function deleteStorePrice(int $id): void
    {
        abort_unless(auth()->user()?->can('price.promotion.delete'), 403, '无删除权限');
        StoreSkuPrice::findOrFail($id)->delete();
        $this->toastSuccess('商家价格已删除');
    }

    // ══════════════════════════════════════
    //  会员折扣 CRUD
    // ══════════════════════════════════════

    public function openMemberDiscountModal(): void
    {
        $this->resetMemberDiscountForm();
        $this->showMemberDiscountModal = true;
    }

    public function closeMemberDiscountModal(): void
    {
        $this->showMemberDiscountModal = false;
        $this->resetErrorBag();
    }

    public function createMemberDiscount(): void
    {
        $this->validate([
            'memberDiscountFormLevel'      => 'required|integer|in:1,2,3,4',
            'memberDiscountFormRate'       => 'required|integer|min:1|max:10000',
            'memberDiscountFormIsPermanent' => 'required|integer|in:0,1',
            'memberDiscountFormPromotionId' => 'nullable|integer|exists:promotions,id',
        ], [], [
            'memberDiscountFormLevel'      => '会员等级',
            'memberDiscountFormRate'       => '折扣率',
            'memberDiscountFormIsPermanent' => '折扣类型',
            'memberDiscountFormPromotionId' => '关联活动',
        ]);

        PromotionMemberDiscount::create([
            'promotion_id'  => $this->memberDiscountFormPromotionId ?: 0,
            'member_level'  => $this->memberDiscountFormLevel,
            'discount_rate' => $this->memberDiscountFormRate,
            'is_permanent'  => $this->memberDiscountFormIsPermanent,
            'status'        => PromotionMemberDiscount::STATUS_ENABLED,
        ]);

        $this->closeMemberDiscountModal();
        $this->toastSuccess('会员折扣已创建');
    }

    public function editMemberDiscount(int $id): void
    {
        $md = PromotionMemberDiscount::findOrFail($id);
        $this->editingMemberDiscountId = $id;
        $this->memberDiscountFormPromotionId = $md->promotion_id > 0 ? $md->promotion_id : null;
        $this->memberDiscountFormLevel = $md->member_level;
        $this->memberDiscountFormRate = $md->discount_rate;
        $this->memberDiscountFormIsPermanent = $md->is_permanent;
        $this->showMemberDiscountModal = true;
    }

    public function updateMemberDiscount(): void
    {
        $this->validate([
            'memberDiscountFormLevel'      => 'required|integer|in:1,2,3,4',
            'memberDiscountFormRate'       => 'required|integer|min:1|max:10000',
            'memberDiscountFormIsPermanent' => 'required|integer|in:0,1',
            'memberDiscountFormPromotionId' => 'nullable|integer|exists:promotions,id',
        ]);

        $md = PromotionMemberDiscount::findOrFail($this->editingMemberDiscountId);
        $md->update([
            'promotion_id'  => $this->memberDiscountFormPromotionId ?: 0,
            'member_level'  => $this->memberDiscountFormLevel,
            'discount_rate' => $this->memberDiscountFormRate,
            'is_permanent'  => $this->memberDiscountFormIsPermanent,
        ]);

        $this->closeMemberDiscountModal();
        $this->editingMemberDiscountId = null;
        $this->toastSuccess('会员折扣已更新');
    }

    public function saveMemberDiscount(): void
    {
        if ($this->editingMemberDiscountId) {
            $this->updateMemberDiscount();
        } else {
            $this->createMemberDiscount();
        }
    }

    public function toggleMemberDiscountStatus(int $id): void
    {
        $md = PromotionMemberDiscount::findOrFail($id);
        $md->status = $md->status === PromotionMemberDiscount::STATUS_ENABLED
            ? PromotionMemberDiscount::STATUS_DISABLED
            : PromotionMemberDiscount::STATUS_ENABLED;
        $md->save();
        $this->toastSuccess($md->status ? '已启用' : '已禁用');
    }

    public function deleteMemberDiscount(int $id): void
    {
        abort_unless(auth()->user()?->can('price.promotion.delete'), 403, '无删除权限');
        PromotionMemberDiscount::findOrFail($id)->delete();
        $this->toastSuccess('会员折扣已删除');
    }

    // ══════════════════════════════════════
    //  活动明细：公共
    // ══════════════════════════════════════

    public function setActiveDetailPromotion(?int $promotionId): void
    {
        $this->detailPromotionId = $promotionId;
        $this->resetErrorBag();
    }

    /**
     * 当前选中活动的类型常量
     */
    public function getDetailPromotionTypeProperty(): int
    {
        return $this->detailPromotionId
            ? (int) (Promotion::find($this->detailPromotionId)->promo_type ?? 0)
            : 0;
    }

    // ══════════════════════════════════════
    //  活动明细：活动商品（promotion_skus，普通促销/清仓临期取价数据源）
    // ══════════════════════════════════════

    public function openDetailSkuModal(): void
    {
        $this->resetDetailSkuForm();
        $this->showDetailSkuModal = true;
    }

    public function closeDetailSkuModal(): void
    {
        $this->showDetailSkuModal = false;
        $this->resetErrorBag();
    }

    public function editDetailSku(int $id): void
    {
        $ps = PromotionSku::findOrFail($id);
        $this->editingDetailSkuId = $id;
        $this->detailSkuFormSkuId = $ps->sku_id;
        $this->detailSkuFormPriceType = $ps->price_type;
        $this->detailSkuFormFixedPrice = $ps->fixed_price > 0 ? $this->centsToYuan($ps->fixed_price) : '';
        $this->detailSkuFormDiscountRate = $ps->discount_rate > 0 && $ps->discount_rate !== 10000 ? (string) ($ps->discount_rate / 100) : '';
        $this->detailSkuFormMaxQuantity = $ps->max_quantity;
        $this->detailSkuFormMaxPerCustomer = $ps->max_per_customer;
        $this->detailSkuFormStockLimit = $ps->stock_limit;
        $this->showDetailSkuModal = true;
    }

    public function saveDetailSku(): void
    {
        $this->validate([
            'detailSkuFormSkuId'          => 'required|integer|exists:skus,id',
            'detailSkuFormPriceType'      => 'required|integer|in:1,2',
            'detailSkuFormFixedPrice'     => 'nullable|numeric|min:0',
            'detailSkuFormDiscountRate'   => 'nullable|numeric|min:0|max:100',
            'detailSkuFormMaxQuantity'    => 'nullable|integer|min:0',
            'detailSkuFormMaxPerCustomer' => 'nullable|integer|min:0',
            'detailSkuFormStockLimit'     => 'nullable|integer|min:0',
        ], [], [
            'detailSkuFormSkuId'          => 'SKU',
            'detailSkuFormPriceType'      => '定价方式',
            'detailSkuFormFixedPrice'     => '促销价',
            'detailSkuFormDiscountRate'   => '折扣率',
        ]);

        if ($this->detailSkuFormPriceType === 1 && $this->detailSkuFormFixedPrice === '') {
            $this->addError('detailSkuFormFixedPrice', '固定价模式下促销价必填');
            return;
        }
        if ($this->detailSkuFormPriceType === 2 && $this->detailSkuFormDiscountRate === '') {
            $this->addError('detailSkuFormDiscountRate', '折扣率模式下折扣率必填');
            return;
        }

        $data = [
            'promotion_id'     => $this->detailPromotionId,
            'sku_id'           => $this->detailSkuFormSkuId,
            'price_type'       => $this->detailSkuFormPriceType,
            'fixed_price'      => money_to_cents($this->detailSkuFormFixedPrice ?: '0'),
            'discount_rate'    => $this->detailSkuFormDiscountRate !== '' ? (int) round((float) $this->detailSkuFormDiscountRate * 100) : 10000,
            'max_quantity'     => $this->detailSkuFormMaxQuantity,
            'max_per_customer' => $this->detailSkuFormMaxPerCustomer,
            'stock_limit'      => $this->detailSkuFormStockLimit,
            'status'           => PromotionSku::STATUS_ENABLED,
        ];

        if ($this->editingDetailSkuId) {
            $ps = PromotionSku::findOrFail($this->editingDetailSkuId);
            $ps->update($data);
            $this->toastSuccess('活动商品已更新');
        } else {
            // 同活动同 SKU 只允许一条生效明细
            $exists = PromotionSku::where('promotion_id', $this->detailPromotionId)
                ->where('sku_id', $this->detailSkuFormSkuId)
                ->where('id', '!=', $this->editingDetailSkuId ?? 0)
                ->exists();
            if ($exists) {
                $this->addError('detailSkuFormSkuId', '该 SKU 已在此活动中，请直接编辑');
                return;
            }
            PromotionSku::create($data);
            $this->toastSuccess('活动商品已添加');
        }

        $this->closeDetailSkuModal();
        $this->editingDetailSkuId = null;
    }

    public function toggleDetailSkuStatus(int $id): void
    {
        $ps = PromotionSku::findOrFail($id);
        $ps->status = $ps->status === PromotionSku::STATUS_ENABLED
            ? PromotionSku::STATUS_DISABLED
            : PromotionSku::STATUS_ENABLED;
        $ps->save();
        $this->toastSuccess($ps->status ? '已启用' : '已禁用');
    }

    public function deleteDetailSku(int $id): void
    {
        PromotionSku::findOrFail($id)->delete();
        $this->toastSuccess('活动商品已移除');
    }

    protected function resetDetailSkuForm(): void
    {
        $this->editingDetailSkuId = null;
        $this->detailSkuFormSkuId = null;
        $this->detailSkuFormPriceType = 1;
        $this->detailSkuFormFixedPrice = '';
        $this->detailSkuFormDiscountRate = '';
        $this->detailSkuFormMaxQuantity = 0;
        $this->detailSkuFormMaxPerCustomer = 0;
        $this->detailSkuFormStockLimit = 0;
        $this->resetErrorBag();
    }

    // ══════════════════════════════════════
    //  活动明细：满减阶梯
    // ══════════════════════════════════════

    public function openFullReductionModal(): void
    {
        $this->resetFullReductionForm();
        $this->showFullReductionModal = true;
    }

    public function closeFullReductionModal(): void
    {
        $this->showFullReductionModal = false;
        $this->resetErrorBag();
    }

    public function editFullReduction(int $id): void
    {
        $fr = PromotionFullReduction::findOrFail($id);
        $this->editingFullReductionId = $id;
        $this->fullReductionFormThresholdType = $fr->threshold_type;
        $this->fullReductionFormThresholdAmount = (string) $fr->threshold_amount;
        $this->fullReductionFormReductionType = $fr->reduction_type;
        $this->fullReductionFormReductionAmount = $fr->reduction_amount > 0 ? $this->centsToYuan($fr->reduction_amount) : '';
        $this->fullReductionFormDiscountRate = $fr->discount_rate > 0 && $fr->discount_rate !== 10000 ? (string) ($fr->discount_rate / 100) : '';
        $this->fullReductionFormGiftSkuId = $fr->gift_sku_id;
        $this->fullReductionFormGiftQuantity = $fr->gift_quantity;
        $this->fullReductionFormIsStacked = $fr->is_stacked;
        $this->showFullReductionModal = true;
    }

    public function saveFullReduction(): void
    {
        $this->validate([
            'fullReductionFormThresholdType'    => 'required|integer|in:1,2',
            'fullReductionFormThresholdAmount'  => 'required|numeric|min:0',
            'fullReductionFormReductionType'    => 'required|integer|in:1,2,3',
            'fullReductionFormReductionAmount'  => 'nullable|numeric|min:0',
            'fullReductionFormDiscountRate'     => 'nullable|numeric|min:0|max:100',
            'fullReductionFormGiftSkuId'        => 'nullable|integer|exists:skus,id',
            'fullReductionFormGiftQuantity'     => 'nullable|integer|min:0',
        ], [], [
            'fullReductionFormThresholdType'    => '门槛类型',
            'fullReductionFormThresholdAmount'  => '门槛值',
            'fullReductionFormReductionType'    => '减免方式',
            'fullReductionFormReductionAmount'  => '减免金额',
            'fullReductionFormDiscountRate'     => '折扣率',
        ]);

        if ($this->fullReductionFormReductionType === 1 && $this->fullReductionFormReductionAmount === '') {
            $this->addError('fullReductionFormReductionAmount', '固定减方式下减免金额必填');
            return;
        }
        if ($this->fullReductionFormReductionType === 2 && $this->fullReductionFormDiscountRate === '') {
            $this->addError('fullReductionFormDiscountRate', '折扣方式下折扣率必填');
            return;
        }
        if ($this->fullReductionFormReductionType === 3 && ! $this->fullReductionFormGiftSkuId) {
            $this->addError('fullReductionFormGiftSkuId', '赠品方式下赠品SKU必选');
            return;
        }

        $data = [
            'promotion_id'     => $this->detailPromotionId,
            'threshold_type'   => $this->fullReductionFormThresholdType,
            'threshold_amount' => $this->fullReductionFormThresholdType === 1
                ? money_to_cents($this->fullReductionFormThresholdAmount ?: '0')
                : (int) $this->fullReductionFormThresholdAmount,
            'reduction_type'   => $this->fullReductionFormReductionType,
            'reduction_amount' => money_to_cents($this->fullReductionFormReductionAmount ?: '0'),
            'discount_rate'    => $this->fullReductionFormDiscountRate !== '' ? (int) round((float) $this->fullReductionFormDiscountRate * 100) : 10000,
            'gift_sku_id'      => $this->fullReductionFormGiftSkuId,
            'gift_quantity'    => $this->fullReductionFormGiftQuantity,
            'is_stacked'       => $this->fullReductionFormIsStacked,
        ];

        if ($this->editingFullReductionId) {
            PromotionFullReduction::findOrFail($this->editingFullReductionId)->update($data);
            $this->toastSuccess('满减阶梯已更新');
        } else {
            PromotionFullReduction::create($data);
            $this->toastSuccess('满减阶梯已添加');
        }

        $this->closeFullReductionModal();
        $this->editingFullReductionId = null;
    }

    public function toggleFullReductionStatus(int $id): void
    {
        // 满减表无 status 字段，用删除替代启用/禁用
        $this->deleteFullReduction($id);
    }

    public function deleteFullReduction(int $id): void
    {
        PromotionFullReduction::findOrFail($id)->delete();
        $this->toastSuccess('满减阶梯已删除');
    }

    protected function resetFullReductionForm(): void
    {
        $this->editingFullReductionId = null;
        $this->fullReductionFormThresholdType = 1;
        $this->fullReductionFormThresholdAmount = '';
        $this->fullReductionFormReductionType = 1;
        $this->fullReductionFormReductionAmount = '';
        $this->fullReductionFormDiscountRate = '';
        $this->fullReductionFormGiftSkuId = null;
        $this->fullReductionFormGiftQuantity = 0;
        $this->fullReductionFormIsStacked = 0;
        $this->resetErrorBag();
    }

    // ══════════════════════════════════════
    //  活动明细：优惠券
    // ══════════════════════════════════════

    public function openCouponModal(): void
    {
        $this->resetCouponForm();
        $this->showCouponModal = true;
    }

    public function closeCouponModal(): void
    {
        $this->showCouponModal = false;
        $this->resetErrorBag();
    }

    public function editCoupon(int $id): void
    {
        $coupon = PromotionCoupon::findOrFail($id);
        $this->editingCouponId = $id;
        $this->couponFormCouponCode = $coupon->coupon_code;
        $this->couponFormName = $coupon->name;
        $this->couponFormType = $coupon->coupon_type;
        $this->couponFormThresholdAmount = $coupon->threshold_amount > 0 ? $this->centsToYuan($coupon->threshold_amount) : '';
        $this->couponFormReductionAmount = $coupon->reduction_amount > 0 ? $this->centsToYuan($coupon->reduction_amount) : '';
        $this->couponFormDiscountRate = $coupon->discount_rate > 0 && $coupon->discount_rate !== 10000 ? (string) ($coupon->discount_rate / 100) : '';
        $this->couponFormMaxDiscount = $coupon->max_discount > 0 ? $this->centsToYuan($coupon->max_discount) : '';
        $this->couponFormTotalQuantity = $coupon->total_quantity;
        $this->couponFormPerUserLimit = $coupon->per_user_limit;
        $this->couponFormValidDays = $coupon->valid_days;
        $this->showCouponModal = true;
    }

    public function saveCoupon(): void
    {
        $this->validate([
            'couponFormCouponCode'      => 'required|string|max:50',
            'couponFormName'            => 'required|string|max:100',
            'couponFormType'            => 'required|integer|in:1,2,3,4',
            'couponFormThresholdAmount' => 'nullable|numeric|min:0',
            'couponFormReductionAmount' => 'nullable|numeric|min:0',
            'couponFormDiscountRate'    => 'nullable|numeric|min:0|max:100',
            'couponFormMaxDiscount'     => 'nullable|numeric|min:0',
            'couponFormTotalQuantity'   => 'required|integer|min:0',
            'couponFormPerUserLimit'    => 'required|integer|min:1',
            'couponFormValidDays'       => 'required|integer|min:1',
        ], [], [
            'couponFormCouponCode'      => '优惠券编码',
            'couponFormName'            => '优惠券名称',
            'couponFormType'            => '优惠券类型',
        ]);

        if (in_array($this->couponFormType, [1, 3], true) && $this->couponFormReductionAmount === '') {
            $this->addError('couponFormReductionAmount', '满减券/抵扣券必须填写抵扣金额');
            return;
        }
        if ($this->couponFormType === 2 && $this->couponFormDiscountRate === '') {
            $this->addError('couponFormDiscountRate', '折扣券必须填写折扣率');
            return;
        }

        $data = [
            'promotion_id'     => $this->detailPromotionId,
            'coupon_code'      => $this->couponFormCouponCode,
            'name'             => $this->couponFormName,
            'coupon_type'      => $this->couponFormType,
            'threshold_amount' => money_to_cents($this->couponFormThresholdAmount ?: '0'),
            'reduction_amount' => money_to_cents($this->couponFormReductionAmount ?: '0'),
            'discount_rate'    => $this->couponFormDiscountRate !== '' ? (int) round((float) $this->couponFormDiscountRate * 100) : 10000,
            'max_discount'     => money_to_cents($this->couponFormMaxDiscount ?: '0'),
            'total_quantity'   => $this->couponFormTotalQuantity,
            'per_user_limit'   => $this->couponFormPerUserLimit,
            'valid_days'       => $this->couponFormValidDays,
            'status'           => PromotionCoupon::STATUS_ENABLED,
        ];

        if ($this->editingCouponId) {
            PromotionCoupon::findOrFail($this->editingCouponId)->update($data);
            $this->toastSuccess('优惠券已更新');
        } else {
            PromotionCoupon::create($data);
            $this->toastSuccess('优惠券已添加');
        }

        $this->closeCouponModal();
        $this->editingCouponId = null;
    }

    public function toggleCouponStatus(int $id): void
    {
        $coupon = PromotionCoupon::findOrFail($id);
        $coupon->status = $coupon->status === PromotionCoupon::STATUS_ENABLED
            ? PromotionCoupon::STATUS_DISABLED
            : PromotionCoupon::STATUS_ENABLED;
        $coupon->save();
        $this->toastSuccess($coupon->status ? '已启用' : '已禁用');
    }

    public function deleteCoupon(int $id): void
    {
        PromotionCoupon::findOrFail($id)->delete();
        $this->toastSuccess('优惠券已删除');
    }

    protected function resetCouponForm(): void
    {
        $this->editingCouponId = null;
        $this->couponFormCouponCode = '';
        $this->couponFormName = '';
        $this->couponFormType = 1;
        $this->couponFormThresholdAmount = '';
        $this->couponFormReductionAmount = '';
        $this->couponFormDiscountRate = '';
        $this->couponFormMaxDiscount = '';
        $this->couponFormTotalQuantity = 0;
        $this->couponFormPerUserLimit = 1;
        $this->couponFormValidDays = 30;
        $this->resetErrorBag();
    }

    // ══════════════════════════════════════
    //  活动明细：组合套餐
    // ══════════════════════════════════════

    public function openBundleModal(): void
    {
        $this->resetBundleForm();
        $this->showBundleModal = true;
    }

    public function closeBundleModal(): void
    {
        $this->showBundleModal = false;
        $this->resetErrorBag();
    }

    public function addBundleItemRow(): void
    {
        $this->bundleFormItems[] = ['sku_id' => null, 'quantity' => 1];
    }

    public function removeBundleItemRow(int $index): void
    {
        unset($this->bundleFormItems[$index]);
        $this->bundleFormItems = array_values($this->bundleFormItems);
    }

    public function editBundle(int $id): void
    {
        $bundle = PromotionBundle::with('items')->findOrFail($id);
        $this->editingBundleId = $id;
        $this->bundleFormName = $bundle->bundle_name;
        $this->bundleFormPrice = $bundle->bundle_price > 0 ? $this->centsToYuan($bundle->bundle_price) : '';
        $this->bundleFormQuantity = $bundle->bundle_quantity;
        $this->bundleFormItems = $bundle->items
            ->sortBy('sort')
            ->map(fn ($item) => ['sku_id' => $item->sku_id, 'quantity' => $item->quantity])
            ->values()
            ->toArray();
        $this->showBundleModal = true;
    }

    public function saveBundle(): void
    {
        $this->validate([
            'bundleFormName'    => 'required|string|max:100',
            'bundleFormPrice'   => 'required|numeric|min:0',
            'bundleFormQuantity' => 'required|integer|min:1',
            'bundleFormItems'   => 'required|array|min:1',
            'bundleFormItems.*.sku_id' => 'required|integer|exists:skus,id',
            'bundleFormItems.*.quantity' => 'required|integer|min:1',
        ], [], [
            'bundleFormName'    => '套餐名称',
            'bundleFormPrice'   => '套餐价',
            'bundleFormItems'   => '套餐明细',
        ]);

        $originalTotal = 0;
        foreach ($this->bundleFormItems as $row) {
            $sku = Sku::find((int) $row['sku_id']);
            $originalTotal += ($sku->retail_price ?? 0) * max(1, (int) $row['quantity']);
        }

        $data = [
            'promotion_id'    => $this->detailPromotionId,
            'bundle_name'     => $this->bundleFormName,
            'bundle_price'    => money_to_cents($this->bundleFormPrice ?: '0'),
            'original_total'  => $originalTotal,
            'bundle_quantity' => $this->bundleFormQuantity,
            'status'          => PromotionBundle::STATUS_ENABLED,
        ];

        if ($this->editingBundleId) {
            $bundle = PromotionBundle::findOrFail($this->editingBundleId);
            $bundle->update($data);
            PromotionBundleItem::where('bundle_id', $bundle->id)->delete();
            $this->toastSuccess('套餐已更新');
        } else {
            $bundle = PromotionBundle::create($data);
            $this->toastSuccess('套餐已创建');
        }

        foreach ($this->bundleFormItems as $i => $row) {
            PromotionBundleItem::create([
                'bundle_id' => $bundle->id,
                'sku_id'    => (int) $row['sku_id'],
                'quantity'  => max(1, (int) $row['quantity']),
                'sort'      => $i,
            ]);
        }

        $this->closeBundleModal();
        $this->editingBundleId = null;
    }

    public function toggleBundleStatus(int $id): void
    {
        $bundle = PromotionBundle::findOrFail($id);
        $bundle->status = $bundle->status === PromotionBundle::STATUS_ENABLED
            ? PromotionBundle::STATUS_DISABLED
            : PromotionBundle::STATUS_ENABLED;
        $bundle->save();
        $this->toastSuccess($bundle->status ? '已启用' : '已禁用');
    }

    public function deleteBundle(int $id): void
    {
        $bundle = PromotionBundle::findOrFail($id);
        PromotionBundleItem::where('bundle_id', $bundle->id)->delete();
        $bundle->delete();
        $this->toastSuccess('套餐已删除');
    }

    protected function resetBundleForm(): void
    {
        $this->editingBundleId = null;
        $this->bundleFormName = '';
        $this->bundleFormPrice = '';
        $this->bundleFormQuantity = 1;
        $this->bundleFormItems = [['sku_id' => null, 'quantity' => 1]];
        $this->resetErrorBag();
    }

    // ══════════════════════════════════════
    //  活动明细：秒杀
    // ══════════════════════════════════════

    public function openFlashSaleModal(): void
    {
        $this->resetFlashSaleForm();
        $this->showFlashSaleModal = true;
    }

    public function closeFlashSaleModal(): void
    {
        $this->showFlashSaleModal = false;
        $this->resetErrorBag();
    }

    public function editFlashSale(int $id): void
    {
        $fs = PromotionFlashSale::findOrFail($id);
        $this->editingFlashSaleId = $id;
        $this->flashSaleFormPrice = $fs->flash_price > 0 ? $this->centsToYuan($fs->flash_price) : '';
        $this->flashSaleFormTotalStock = $fs->total_stock;
        $this->flashSaleFormPerUserLimit = $fs->per_user_limit;
        $this->flashSaleFormStartAt = $fs->flash_start_at?->format('Y-m-d\TH:i') ?? '';
        $this->flashSaleFormEndAt = $fs->flash_end_at?->format('Y-m-d\TH:i') ?? '';
        $this->showFlashSaleModal = true;
    }

    public function saveFlashSale(): void
    {
        $this->validate([
            'flashSaleFormPrice'       => 'required|numeric|min:0',
            'flashSaleFormTotalStock'  => 'required|integer|min:1',
            'flashSaleFormPerUserLimit' => 'required|integer|min:1',
            'flashSaleFormStartAt'     => 'nullable|date',
            'flashSaleFormEndAt'       => 'nullable|date|after:flashSaleFormStartAt',
        ], [], [
            'flashSaleFormPrice'       => '秒杀价',
            'flashSaleFormTotalStock'  => '秒杀总库存',
        ]);

        $data = [
            'promotion_id'    => $this->detailPromotionId,
            'flash_price'     => money_to_cents($this->flashSaleFormPrice ?: '0'),
            'total_stock'     => $this->flashSaleFormTotalStock,
            'per_user_limit'  => $this->flashSaleFormPerUserLimit,
            'flash_start_at'  => $this->flashSaleFormStartAt ?: null,
            'flash_end_at'    => $this->flashSaleFormEndAt ?: null,
            'status'          => PromotionFlashSale::STATUS_ENABLED,
        ];

        if ($this->editingFlashSaleId) {
            PromotionFlashSale::findOrFail($this->editingFlashSaleId)->update($data);
            $this->toastSuccess('秒杀场次已更新');
        } else {
            PromotionFlashSale::create($data);
            $this->toastSuccess('秒杀场次已创建');
        }

        $this->closeFlashSaleModal();
        $this->editingFlashSaleId = null;
    }

    public function toggleFlashSaleStatus(int $id): void
    {
        $fs = PromotionFlashSale::findOrFail($id);
        $fs->status = $fs->status === PromotionFlashSale::STATUS_ENABLED
            ? PromotionFlashSale::STATUS_DISABLED
            : PromotionFlashSale::STATUS_ENABLED;
        $fs->save();
        $this->toastSuccess($fs->status ? '已启用' : '已禁用');
    }

    public function deleteFlashSale(int $id): void
    {
        PromotionFlashSale::findOrFail($id)->delete();
        $this->toastSuccess('秒杀场次已删除');
    }

    protected function resetFlashSaleForm(): void
    {
        $this->editingFlashSaleId = null;
        $this->flashSaleFormPrice = '';
        $this->flashSaleFormTotalStock = 0;
        $this->flashSaleFormPerUserLimit = 1;
        $this->flashSaleFormStartAt = '';
        $this->flashSaleFormEndAt = '';
        $this->resetErrorBag();
    }

    // ══════════════════════════════════════
    //  活动明细：拼团
    // ══════════════════════════════════════

    public function openGroupBuyModal(): void
    {
        $this->resetGroupBuyForm();
        $this->showGroupBuyModal = true;
    }

    public function closeGroupBuyModal(): void
    {
        $this->showGroupBuyModal = false;
        $this->resetErrorBag();
    }

    public function editGroupBuy(int $id): void
    {
        $gb = PromotionGroupBuy::findOrFail($id);
        $this->editingGroupBuyId = $id;
        $this->groupBuyFormPrice = $gb->group_price > 0 ? $this->centsToYuan($gb->group_price) : '';
        $this->groupBuyFormMinSize = $gb->min_group_size;
        $this->groupBuyFormMaxSize = $gb->max_group_size;
        $this->groupBuyFormTimeLimit = $gb->time_limit;
        $this->groupBuyFormVirtualJoin = $gb->virtual_join;
        $this->showGroupBuyModal = true;
    }

    public function saveGroupBuy(): void
    {
        $this->validate([
            'groupBuyFormPrice'       => 'required|numeric|min:0',
            'groupBuyFormMinSize'     => 'required|integer|min:2',
            'groupBuyFormMaxSize'     => 'required|integer|min:2|gt:groupBuyFormMinSize',
            'groupBuyFormTimeLimit'   => 'required|integer|min:1',
            'groupBuyFormVirtualJoin' => 'required|integer|in:0,1',
        ], [], [
            'groupBuyFormPrice'       => '拼团价',
        ]);

        $data = [
            'promotion_id'   => $this->detailPromotionId,
            'group_price'    => money_to_cents($this->groupBuyFormPrice ?: '0'),
            'min_group_size' => $this->groupBuyFormMinSize,
            'max_group_size' => $this->groupBuyFormMaxSize,
            'time_limit'     => $this->groupBuyFormTimeLimit,
            'virtual_join'   => $this->groupBuyFormVirtualJoin,
            'status'         => PromotionGroupBuy::STATUS_ENABLED,
        ];

        if ($this->editingGroupBuyId) {
            PromotionGroupBuy::findOrFail($this->editingGroupBuyId)->update($data);
            $this->toastSuccess('拼团规则已更新');
        } else {
            PromotionGroupBuy::create($data);
            $this->toastSuccess('拼团规则已创建');
        }

        $this->closeGroupBuyModal();
        $this->editingGroupBuyId = null;
    }

    public function toggleGroupBuyStatus(int $id): void
    {
        $gb = PromotionGroupBuy::findOrFail($id);
        $gb->status = $gb->status === PromotionGroupBuy::STATUS_ENABLED
            ? PromotionGroupBuy::STATUS_DISABLED
            : PromotionGroupBuy::STATUS_ENABLED;
        $gb->save();
        $this->toastSuccess($gb->status ? '已启用' : '已禁用');
    }

    public function deleteGroupBuy(int $id): void
    {
        PromotionGroupBuy::findOrFail($id)->delete();
        $this->toastSuccess('拼团规则已删除');
    }

    protected function resetGroupBuyForm(): void
    {
        $this->editingGroupBuyId = null;
        $this->groupBuyFormPrice = '';
        $this->groupBuyFormMinSize = 2;
        $this->groupBuyFormMaxSize = 10;
        $this->groupBuyFormTimeLimit = 1440;
        $this->groupBuyFormVirtualJoin = 0;
        $this->resetErrorBag();
    }

    // ══════════════════════════════════════
    //  活动明细：清仓临期
    // ══════════════════════════════════════

    public function openClearanceModal(): void
    {
        $this->resetClearanceForm();
        $this->showClearanceModal = true;
    }

    public function closeClearanceModal(): void
    {
        $this->showClearanceModal = false;
        $this->resetErrorBag();
    }

    public function editClearance(int $id): void
    {
        $cl = PromotionClearance::findOrFail($id);
        $this->editingClearanceId = $id;
        $this->clearanceFormType = $cl->clearance_type;
        $this->clearanceFormExpiryDate = $cl->expiry_date !== null ? $cl->expiry_date->format('Y-m-d') : '';
        $this->clearanceFormDiscountRate = $cl->discount_rate > 0 && $cl->discount_rate !== 10000 ? (string) ($cl->discount_rate / 100) : '';
        $this->clearanceFormFixedPrice = $cl->fixed_price > 0 ? $this->centsToYuan($cl->fixed_price) : '';
        $this->showClearanceModal = true;
    }

    public function saveClearance(): void
    {
        $this->validate([
            'clearanceFormType'         => 'required|integer|in:1,2',
            'clearanceFormExpiryDate'   => 'nullable|date',
            'clearanceFormDiscountRate' => 'nullable|numeric|min:0|max:100',
            'clearanceFormFixedPrice'   => 'nullable|numeric|min:0',
        ], [], [
            'clearanceFormType'         => '类型',
        ]);

        if ($this->clearanceFormType === 2 && $this->clearanceFormExpiryDate === '') {
            $this->addError('clearanceFormExpiryDate', '临期类型必须填写临期截止日期');
            return;
        }
        if ($this->clearanceFormFixedPrice === '' && $this->clearanceFormDiscountRate === '') {
            $this->addError('clearanceFormFixedPrice', '清仓价与折扣率至少填一项');
            return;
        }

        $data = [
            'promotion_id'   => $this->detailPromotionId,
            'clearance_type' => $this->clearanceFormType,
            'expiry_date'    => $this->clearanceFormExpiryDate ?: null,
            'discount_rate'  => $this->clearanceFormDiscountRate !== '' ? (int) round((float) $this->clearanceFormDiscountRate * 100) : 10000,
            'fixed_price'    => money_to_cents($this->clearanceFormFixedPrice ?: '0'),
            'status'         => PromotionClearance::STATUS_ENABLED,
        ];

        if ($this->editingClearanceId) {
            PromotionClearance::findOrFail($this->editingClearanceId)->update($data);
            $this->toastSuccess('清仓规则已更新');
        } else {
            PromotionClearance::create($data);
            $this->toastSuccess('清仓规则已创建');
        }

        $this->closeClearanceModal();
        $this->editingClearanceId = null;
    }

    public function toggleClearanceStatus(int $id): void
    {
        $cl = PromotionClearance::findOrFail($id);
        $cl->status = $cl->status === PromotionClearance::STATUS_ENABLED
            ? PromotionClearance::STATUS_DISABLED
            : PromotionClearance::STATUS_ENABLED;
        $cl->save();
        $this->toastSuccess($cl->status ? '已启用' : '已禁用');
    }

    public function deleteClearance(int $id): void
    {
        PromotionClearance::findOrFail($id)->delete();
        $this->toastSuccess('清仓规则已删除');
    }

    protected function resetClearanceForm(): void
    {
        $this->editingClearanceId = null;
        $this->clearanceFormType = 1;
        $this->clearanceFormExpiryDate = '';
        $this->clearanceFormDiscountRate = '';
        $this->clearanceFormFixedPrice = '';
        $this->resetErrorBag();
    }

    // ══════════════════════════════════════
    //  活动明细：SKU 服务端搜索（searchable-select）
    // ══════════════════════════════════════

    /**
     * SKU 服务端搜索（编码/商品名，label 含商品名，无法复用 build_search_options）
     *
     * @return array{options: list<array{value: int, label: string}>, hasMore: bool, page: int}
     */
    public function searchDetailSkuOptions(string $keyword = '', int $page = 1): array
    {
        $keyword = trim($keyword);
        $query = Sku::with('product')->enabled();
        if ($keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->where('sku_code', 'like', "%{$keyword}%")
                    ->orWhereHas('product', fn ($pq) => $pq->where('name', 'like', "%{$keyword}%"));
            });
        }

        $perPage = 20;
        $page = max(1, $page);
        $rows = $query->orderBy('sku_code')
            ->limit($perPage + 1)
            ->offset(($page - 1) * $perPage)
            ->get(['id', 'sku_code', 'product_id']);

        $hasMore = $rows->count() > $perPage;
        $rows = $rows->take($perPage);

        $options = [];
        foreach ($rows as $s) {
            $options[] = [
                'value' => (int) $s->id,
                'label' => $s->sku_code.' - '.($s->product->name ?? ''),
            ];
        }

        return [
            'options' => $options,
            'hasMore' => $hasMore,
            'page' => $page,
        ];
    }

    // ══════════════════════════════════════
    //  重置表单
    // ══════════════════════════════════════

    protected function resetPromoForm(): void
    {
        $this->promoFormName = '';
        $this->promoFormType = 1;
        $this->promoFormStartAt = '';
        $this->promoFormEndAt = '';
        $this->resetErrorBag();
    }

    protected function resetStorePriceForm(): void
    {
        $this->editingStorePriceId = null;
        $this->storePriceFormMerchantId = null;
        $this->storePriceFormSkuId = null;
        $this->storePriceFormPriceType = 1;
        $this->storePriceFormAdjustMode = 1;
        $this->storePriceFormAdjustValue = 0;
        $this->storePriceFormEffectiveAt = '';
        $this->storePriceFormExpireAt = '';
        $this->resetErrorBag();
    }

    protected function resetMemberDiscountForm(): void
    {
        $this->editingMemberDiscountId = null;
        $this->memberDiscountFormPromotionId = null;
        $this->memberDiscountFormLevel = 1;
        $this->memberDiscountFormRate = 10000;
        $this->memberDiscountFormIsPermanent = 1;
        $this->resetErrorBag();
    }

    // ══════════════════════════════════════
    //  Render
    // ══════════════════════════════════════

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render()
    {
        $navGroups = [
            'promotion'  => '促销活动',
            'detail'     => '活动明细',
            'store'      => '商家价格',
            'member'     => '会员折扣',
        ];

        // 促销活动列表
        $promotions = Promotion::orderBy('id', 'desc');
        if ($this->promoSearch) {
            $promotions->where('name', 'like', "%{$this->promoSearch}%");
        }
        $promoItems = $promotions->paginate(10, ['*'], 'promoPage');

        // 商家价格列表
        $storePrices = StoreSkuPrice::with(['sku.product'])->orderBy('id', 'desc');
        if ($this->storePriceMerchantFilter) {
            $storePrices->where('store_id', $this->storePriceMerchantFilter);
        }
        if ($this->storePriceSearch) {
            $storePrices->whereHas('sku', function ($q) {
                $q->where('sku_code', 'like', "%{$this->storePriceSearch}%");
            });
        }
        $storePriceItems = $storePrices->paginate(10, ['*'], 'storePricePage');

        // 会员折扣列表
        $memberDiscounts = PromotionMemberDiscount::with('promotion')->orderBy('id', 'desc')->paginate(10, ['*'], 'memberPage');

        // 活动明细：选中活动的子表明单
        $detailPromotion = $this->detailPromotionId ? Promotion::find($this->detailPromotionId) : null;
        $detailSkus = collect();
        $fullReductions = collect();
        $coupons = collect();
        $bundles = collect();
        $flashSales = collect();
        $groupBuys = collect();
        $clearances = collect();
        if ($detailPromotion) {
            $detailPromotion->load(['skus.sku.product', 'fullReductions.giftSku.product', 'bundles.items.sku.product', 'flashSales', 'groupBuys', 'clearances']);
            $detailSkus = $detailPromotion->skus;
            $fullReductions = $detailPromotion->fullReductions;
            $coupons = $detailPromotion->coupons;
            $bundles = $detailPromotion->bundles;
            $flashSales = $detailPromotion->flashSales;
            $groupBuys = $detailPromotion->groupBuys;
            $clearances = $detailPromotion->clearances;
        }
        $detailPromotions = Promotion::orderBy('id', 'desc')->get();

        // 会员折扣关联活动回显
        $memberDiscountPromotionOptions = Promotion::orderBy('id', 'desc')->get()
            ->map(fn ($p) => ['value' => $p->id, 'label' => $p->name])
            ->toArray();

        // 选中商家回显
        $storePriceFormMerchant = $this->storePriceFormMerchantId ? Merchant::find($this->storePriceFormMerchantId) : null;
        $storePriceFilterMerchantName = $this->storePriceMerchantFilter ? (string) Merchant::find($this->storePriceMerchantFilter)->name : '';

        // 选中活动的类型（供视图分支）
        $detailPromoType = $detailPromotion !== null ? $detailPromotion->promo_type : 0;

        // 类型映射
        $promoTypeMap = Promotion::typeMap();
        $priceTypeMap = StoreSkuPrice::priceTypeMap();
        $adjustModeMap = StoreSkuPrice::adjustModeMap();
        $memberLevelMap = PromotionMemberDiscount::memberLevelMap();
        $detailPromoTypeMap = PromotionSku::priceTypeMap();
        $secondPriceTypeMap = PromotionSku::secondPriceTypeMap();
        $couponTypeMap = PromotionCoupon::couponTypeMap();
        $clearanceTypeMap = PromotionClearance::clearanceTypeMap();

        return view('livewire.price.promotion-settings', compact(
            'navGroups', 'promoItems', 'storePriceItems', 'memberDiscounts',
            'storePriceFormMerchant', 'storePriceFilterMerchantName', 'promoTypeMap', 'priceTypeMap', 'adjustModeMap', 'memberLevelMap',
            'detailPromotions', 'detailPromotion', 'detailPromoType', 'detailSkus', 'fullReductions', 'coupons', 'bundles', 'flashSales', 'groupBuys', 'clearances',
            'detailPromoTypeMap', 'secondPriceTypeMap', 'couponTypeMap', 'clearanceTypeMap', 'memberDiscountPromotionOptions'
        ))->layout('components.app-layout')->title('促销设置');
    }

    /**
     * 商家服务端搜索（searchable-select 调用，分页协议）
     *
     * @return array{options: list<array{value: int, label: string}>, hasMore: bool, page: int}
     */
    public function searchMerchantOptions(string $keyword = '', int $page = 1): array
    {
        $keyword = trim($keyword);
        $query = Merchant::enabled();
        if ($keyword !== '') {
            $query->where('name', 'like', "%{$keyword}%");
        }

        return build_search_options($query->orderBy('name'), $page);
    }
}
