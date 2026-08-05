<?php

namespace App\Livewire\Order;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Sku;
use App\Services\PricingService;
use App\Livewire\Traits\WithMoneyConversion;
use App\Livewire\Traits\WithToast;
use Livewire\Component;

class OrderDetail extends Component
{
    use WithToast;
    use WithMoneyConversion;

    public Order $order;
    public $items;

    // 明细编辑
    public bool $showAddItemModal = false;
    public ?int $editingItemId = null;
    public int $formSkuId = 0;
    public int $formQuantity = 0;
    public string $formPrice = '';
    public string $formRemark = '';
    public bool $showDeleteItemConfirm = false;
    public ?int $deletingItemId = null;

    // 状态操作确认
    public bool $showConfirmModal = false;
    public string $confirmAction = '';
    public string $confirmTitle = '';

    public function mount(int $id): void
    {
        $this->loadOrder($id);
    }

    public function getIsSuperAdminProperty(): bool
    {
        return auth()->check() && auth()->user()->hasRole('super_admin');
    }

    public function loadOrder(int $id): void
    {
        $this->order = Order::with(['merchant', 'deliveryRoute', 'items.sku.product'])->findOrFail($id);
        $this->items = $this->order->items;
    }

    // ── 状态流转 ──

    public function confirmSubmit(): void
    {
        $this->confirmAction = 'submit';
        $this->confirmTitle = '确认提交此订单？';
        $this->showConfirmModal = true;
    }

    public function confirmPick(): void
    {
        $this->confirmAction = 'pick';
        $this->confirmTitle = '确认开始拣货？';
        $this->showConfirmModal = true;
    }

    public function confirmDeliver(): void
    {
        $this->confirmAction = 'deliver';
        $this->confirmTitle = '确认开始配送？';
        $this->showConfirmModal = true;
    }

    public function confirmSign(): void
    {
        $this->confirmAction = 'sign';
        $this->confirmTitle = '确认签收？';
        $this->showConfirmModal = true;
    }

    public function confirmCancel(): void
    {
        $this->confirmAction = 'cancel';
        $this->confirmTitle = '确认取消此订单？此操作不可撤销。';
        $this->showConfirmModal = true;
    }

    // ── 超管状态回退 ──

    public function confirmRollback(string $toStatus): void
    {
        $statusLabels = Order::statusMap();
        $this->confirmAction = 'rollback_' . $toStatus;
        $this->confirmTitle = "确认回退到【{$statusLabels[$toStatus]}】状态？";
        $this->showConfirmModal = true;
    }

    public function executeConfirm(): void
    {
        try {
            // 超管状态回退
            if (str_starts_with($this->confirmAction, 'rollback_')) {
                $toStatus = (int) substr($this->confirmAction, 9);
                $this->order->update(['status' => $toStatus]);
                $this->loadOrder($this->order->id);
                $this->showConfirmModal = false;
                $this->toastSuccess('状态已回退');
                return;
            }

            match ($this->confirmAction) {
                'submit' => $this->order->update(['status' => Order::STATUS_PICKING]),
                'pick' => $this->order->update(['status' => Order::STATUS_PICKING]),
                'deliver' => $this->order->update(['status' => Order::STATUS_DELIVERING]),
                'sign' => $this->order->update(['status' => Order::STATUS_SIGNED]),
                'cancel' => $this->order->update(['status' => Order::STATUS_CANCELLED]),
                default => null,
            };

            $this->loadOrder($this->order->id);
            $this->showConfirmModal = false;
            $this->toastSuccess('操作成功');
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function closeConfirmModal(): void
    {
        $this->showConfirmModal = false;
        $this->resetErrorBag();
    }

    // ── 明细编辑 ──

    public function openAddItemModal(): void
    {
        $this->resetAddItemForm();
        $this->editingItemId = null;
        $this->showAddItemModal = true;
    }

    public function openEditItemModal(int $itemId): void
    {
        $item = OrderItem::findOrFail($itemId);
        $this->editingItemId = $itemId;
        $this->formSkuId = $item->sku_id;
        $this->formQuantity = $item->quantity;
        $this->formPrice = $this->centsToYuan($item->price);
        $this->formRemark = $item->sku_specs ? (is_array($item->sku_specs) ? implode(',', $item->sku_specs) : '') : '';
        $this->showAddItemModal = true;
    }

    public function saveItem(): void
    {
        $this->validate([
            'formSkuId' => 'required|integer|min:1',
            'formQuantity' => 'required|integer|min:1',
            'formPrice' => 'required|numeric|min:0',
        ]);

        try {
            $sku = Sku::with('product')->findOrFail($this->formSkuId);
            $price = money_to_cents($this->formPrice);
            $subtotal = $price * $this->formQuantity;

            if ($this->editingItemId) {
                // 编辑模式
                $item = OrderItem::findOrFail($this->editingItemId);
                $item->update([
                    'sku_id' => $this->formSkuId,
                    'quantity' => $this->formQuantity,
                    'price' => $price,
                    'subtotal' => $subtotal,
                    'actual_quantity' => $this->formQuantity,
                    'actual_price' => $price,
                    'actual_subtotal' => $subtotal,
                ]);
                $this->toastSuccess('明细已更新');
            } else {
                // 新增模式
                OrderItem::create([
                    'order_id' => $this->order->id,
                    'sku_id' => $this->formSkuId,
                    'product_name' => $sku->product?->name ?? '',
                    'sku_specs' => $sku->specs ?? null,
                    'quantity' => $this->formQuantity,
                    'price' => $price,
                    'actual_quantity' => $this->formQuantity,
                    'actual_price' => $price,
                    'subtotal' => $subtotal,
                    'actual_subtotal' => $subtotal,
                    'strategy_price' => 0,
                    'strategy_amount' => 0,
                    'discrepancy_amount' => 0,
                    'status' => OrderItem::STATUS_NORMAL,
                ]);
                $this->toastSuccess('明细已添加');
            }

            // 重新计算订单总金额
            $this->recalculateTotal();
            $this->loadOrder($this->order->id);
            $this->showAddItemModal = false;
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function confirmDeleteItem(int $itemId): void
    {
        $this->deletingItemId = $itemId;
        $this->showDeleteItemConfirm = true;
    }

    public function deleteItem(): void
    {
        try {
            $item = OrderItem::findOrFail($this->deletingItemId);
            $item->delete();
            $this->recalculateTotal();
            $this->loadOrder($this->order->id);
            $this->showDeleteItemConfirm = false;
            $this->deletingItemId = null;
            $this->toastSuccess('明细已删除');
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function closeDeleteItemConfirm(): void
    {
        $this->showDeleteItemConfirm = false;
        $this->deletingItemId = null;
    }

    public function closeAddItemModal(): void
    {
        $this->showAddItemModal = false;
        $this->resetErrorBag();
    }

    /**
     * 通过 PricingService 自动取价
     */
    public function autoPrice(): void
    {
        $this->validateOnly('formSkuId', [
            'formSkuId' => 'required|integer|min:1',
        ]);

        try {
            $sku = Sku::findOrFail($this->formSkuId);
            $pricingService = app(PricingService::class);
            $result = $pricingService->calculate(
                $sku,
                'miniapp',
                $this->order->merchant_id,
            );
            $this->formPrice = $this->centsToYuan($result['price']);
        } catch (\Exception $e) {
            $this->toastError('取价失败：' . $e->getMessage());
        }
    }

    // ── 内部方法 ──

    private function resetAddItemForm(): void
    {
        $this->editingItemId = null;
        $this->formSkuId = 0;
        $this->formQuantity = 0;
        $this->formPrice = '';
        $this->formRemark = '';
    }

    /**
     * 重新计算订单总金额
     */
    private function recalculateTotal(): void
    {
        $total = OrderItem::where('order_id', $this->order->id)->sum('subtotal');
        $this->order->update([
            'total_amount' => $total,
            'adjusted_amount' => $total,
            'final_amount' => $total,
        ]);
    }

    public function render()
    {
        $skus = Sku::with('product')->orderBy('sku_code')->get();
        $isSuperAdmin = $this->isSuperAdmin;

        return view('livewire.order.order-detail', compact('skus', 'isSuperAdmin'))
            ->layout('components.app-layout')
            ->title("订单 {$this->order->order_no}");
    }
}
