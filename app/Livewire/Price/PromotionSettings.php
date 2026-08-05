<?php

namespace App\Livewire\Price;

use App\Livewire\Traits\WithToast;
use App\Models\Merchant;
use App\Models\Promotion;
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
    public ?int $editingMemberDiscountId = null;

    public function mount(): void
    {
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
        ], [], [
            'memberDiscountFormLevel'      => '会员等级',
            'memberDiscountFormRate'       => '折扣率',
            'memberDiscountFormIsPermanent' => '折扣类型',
        ]);

        PromotionMemberDiscount::create([
            'promotion_id'  => 0,
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
        ]);

        $md = PromotionMemberDiscount::findOrFail($this->editingMemberDiscountId);
        $md->update([
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
        PromotionMemberDiscount::findOrFail($id)->delete();
        $this->toastSuccess('会员折扣已删除');
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
        $this->memberDiscountFormLevel = 1;
        $this->memberDiscountFormRate = 10000;
        $this->memberDiscountFormIsPermanent = 1;
        $this->resetErrorBag();
    }

    // ══════════════════════════════════════
    //  Render
    // ══════════════════════════════════════

    public function render()
    {
        $navGroups = [
            'promotion'  => '促销活动',
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
        $memberDiscounts = PromotionMemberDiscount::orderBy('id', 'desc')->paginate(10, ['*'], 'memberPage');

        // 商家列表（筛选用）
        $merchants = Merchant::enabled()->orderBy('name')->get();

        // 类型映射
        $promoTypeMap = Promotion::typeMap();
        $priceTypeMap = StoreSkuPrice::priceTypeMap();
        $adjustModeMap = StoreSkuPrice::adjustModeMap();
        $memberLevelMap = PromotionMemberDiscount::memberLevelMap();

        return view('livewire.price.promotion-settings', compact(
            'navGroups', 'promoItems', 'storePriceItems', 'memberDiscounts',
            'merchants', 'promoTypeMap', 'priceTypeMap', 'adjustModeMap', 'memberLevelMap'
        ))->layout('components.app-layout')->title('促销设置');
    }
}
