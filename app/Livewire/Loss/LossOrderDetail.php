<?php

namespace App\Livewire\Loss;

use App\Livewire\Traits\WithToast;
use App\Models\LossOrder;
use App\Services\LossOrderService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LossOrderDetail extends Component
{
    use WithToast;

    public LossOrder $lossOrder;

    // 审核弹窗
    public bool $showApproveConfirm = false;

    public function mount(int $id): void
    {
        $this->loadLossOrder($id);
    }

    public function loadLossOrder(int $id): void
    {
        $this->lossOrder = LossOrder::with(['warehouse', 'items.sku', 'items.supplier', 'applicant', 'reviewer', 'auditLogs.operator'])
            ->findOrFail($id);
    }

    public function confirmApprove(): void
    {
        $this->showApproveConfirm = true;
    }

    public function closeApproveConfirm(): void
    {
        $this->showApproveConfirm = false;
        $this->resetErrorBag();
    }

    public function approve(): void
    {
        if ($this->lossOrder->approval_status !== LossOrder::APPROVAL_PENDING) {
            $this->toastError('该单不在待审核状态');
            $this->showApproveConfirm = false;

            return;
        }

        $this->lossOrder->update([
            'approval_status' => LossOrder::APPROVAL_APPROVED,
            'status' => LossOrder::STATUS_APPROVED,
            'reviewer_id' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $this->loadLossOrder($this->lossOrder->id);
        $this->showApproveConfirm = false;
        $this->toastSuccess('损耗单已审核通过');
    }

    public function reject(): void
    {
        if ($this->lossOrder->approval_status !== LossOrder::APPROVAL_PENDING) {
            $this->toastError('该单不在待审核状态');
            $this->showApproveConfirm = false;

            return;
        }

        $this->lossOrder->update([
            'approval_status' => LossOrder::APPROVAL_REJECTED,
            'status' => LossOrder::STATUS_CANCELLED,
            'reviewer_id' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $this->loadLossOrder($this->lossOrder->id);
        $this->showApproveConfirm = false;
        $this->toastSuccess('损耗单已拒绝');
    }

    public function execute(): void
    {
        try {
            app(LossOrderService::class)->execute(
                LossOrder::findOrFail($this->lossOrder->id)
            );
            $this->loadLossOrder($this->lossOrder->id);
            $this->toastSuccess('损耗单已执行，库存已扣减');
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function close(): void
    {
        $purchaseTraceClosable = $this->lossOrder->status === LossOrder::STATUS_APPROVED
            && $this->lossOrder->source_type === 'purchase_order';

        if ($this->lossOrder->status !== LossOrder::STATUS_EXECUTED && !$purchaseTraceClosable) {
            $this->toastError('只有已执行的单据可关闭');

            return;
        }

        $this->lossOrder->update([
            'status' => LossOrder::STATUS_CLOSED,
            'closed_at' => now(),
        ]);

        $this->loadLossOrder($this->lossOrder->id);
        $this->toastSuccess('损耗单已关闭');
    }

    public function render()
    {
        $statusMap = LossOrder::statusMap();
        $statusColorMap = [
            LossOrder::STATUS_PENDING => 'yellow',
            LossOrder::STATUS_APPROVED => 'blue',
            LossOrder::STATUS_EXECUTED => 'green',
            LossOrder::STATUS_CLOSED => 'gray',
            LossOrder::STATUS_CANCELLED => 'red',
        ];
        $approvalStatusMap = LossOrder::approvalStatusMap();
        $approvalColorMap = [
            LossOrder::APPROVAL_PENDING => 'yellow',
            LossOrder::APPROVAL_APPROVED => 'green',
            LossOrder::APPROVAL_REJECTED => 'red',
        ];
        $typeMap = LossOrder::typeMap();
        $sourceTypeMap = LossOrder::sourceTypeMap();
        $responsiblePartyMap = \App\Models\LossOrderItem::responsiblePartyMap();

        return view('livewire.loss.loss-order-detail', compact(
            'statusMap', 'statusColorMap', 'approvalStatusMap', 'approvalColorMap',
            'typeMap', 'sourceTypeMap', 'responsiblePartyMap'
        ))
            ->layout('components.app-layout')
            ->title("损耗单详情 {$this->lossOrder->loss_no}");
    }
}
