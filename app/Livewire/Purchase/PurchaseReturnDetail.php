<?php

namespace App\Livewire\Purchase;

use App\Models\PurchaseReturn;
use App\Services\PurchaseReturnService;
use App\Livewire\Traits\WithToast;
use Livewire\Component;

class PurchaseReturnDetail extends Component
{
    use WithToast;

    public PurchaseReturn $return;
    public $items;

    // 状态操作确认
    public bool $showConfirmModal = false;
    public string $confirmAction = '';
    public string $confirmTitle = '';

    public function mount(int $id): void
    {
        $this->loadReturn($id);
    }

    public function loadReturn(int $id): void
    {
        $this->return = PurchaseReturn::with(['purchaseOrder', 'supplier', 'warehouse', 'operator', 'auditor', 'items.sku.product'])->findOrFail($id);
        $this->items = $this->return->items;
    }

    // ── 状态流转 ──

    public function confirmApprove(): void
    {
        $this->confirmAction = 'approve';
        $this->confirmTitle = '确认审核通过此退货单？';
        $this->showConfirmModal = true;
    }

    public function confirmShip(): void
    {
        $this->confirmAction = 'ship';
        $this->confirmTitle = '确认标记为已出库？出库将扣减库存。';
        $this->showConfirmModal = true;
    }

    public function confirmComplete(): void
    {
        $this->confirmAction = 'complete';
        $this->confirmTitle = '确认完成此退货单？';
        $this->showConfirmModal = true;
    }

    public function confirmCancel(): void
    {
        $this->confirmAction = 'cancel';
        $this->confirmTitle = '确认取消此退货单？此操作不可撤销。';
        $this->showConfirmModal = true;
    }

    public function executeConfirm(): void
    {
        try {
            $service = app(PurchaseReturnService::class);

            match ($this->confirmAction) {
                'approve' => $service->approve($this->return),
                'ship' => $service->ship($this->return),
                'complete' => $service->complete($this->return),
                'cancel' => $service->cancel($this->return),
                default => null,
            };

            $this->loadReturn($this->return->id);
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

    public function render()
    {
        return view('livewire.purchase.purchase-return-detail')
            ->layout('components.app-layout')
            ->title("退货单 {$this->return->return_no}");
    }
}
