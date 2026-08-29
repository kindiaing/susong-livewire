<?php

namespace App\Livewire\Finance;

use App\Livewire\Traits\WithToast;
use App\Models\SettlementPayment;
use App\Models\SupplierSettlement;
use App\Services\NotificationService;
use App\Services\SupplierSettlementService;
use Livewire\Component;

class SupplierSettlementDetail extends Component
{
    use WithToast;

    public SupplierSettlement $settlement;

    // 付款弹窗
    public bool $showPaymentModal = false;

    public string $paymentAmount = '';

    public string $paymentRemark = '';

    // 办结弹窗
    public bool $showCloseModal = false;

    public function mount(int $id): void
    {
        $this->loadSettlement($id);
    }

    public function loadSettlement(int $id): void
    {
        $this->settlement = SupplierSettlement::with(['supplier', 'items.purchaseOrder', 'payments', 'closedBy', 'auditLogs.operator'])
            ->findOrFail($id);
    }

    public function getRemainingAmountProperty(): int
    {
        return max(0, $this->settlement->payable_amount - $this->settlement->paid_amount);
    }

    public function openPaymentModal(): void
    {
        if (!in_array($this->settlement->status, [SupplierSettlement::STATUS_PENDING, SupplierSettlement::STATUS_PARTIAL])) {
            $this->toastError('仅待结算/部分付款状态可操作');

            return;
        }
        $this->paymentAmount = '';
        $this->paymentRemark = '';
        $this->showPaymentModal = true;
    }

    public function closePaymentModal(): void
    {
        $this->showPaymentModal = false;
        $this->paymentAmount = '';
        $this->paymentRemark = '';
    }

    public function submitPayment(): void
    {
        $this->validate([
            'paymentAmount' => 'required|numeric|min:0.01',
        ]);

        $cents = money_to_cents($this->paymentAmount);
        if ($cents <= 0) {
            $this->toastError('付款金额必须大于0');

            return;
        }

        try {
            $payment = app(SupplierSettlementService::class)->recordPayment(
                settlementId: $this->settlement->id,
                amount: $cents,
                paymentMethod: SettlementPayment::METHOD_MANUAL,
                remark: $this->paymentRemark ?: null,
            );

            $needsApproval = $payment->approval_status === SettlementPayment::APPROVAL_PENDING;
            if ($needsApproval) {
                $this->toastSuccess('付款已登记，待审核');
            } else {
                $this->loadSettlement($this->settlement->id);
                $newStatus = $this->settlement->status;
                if ($newStatus === SupplierSettlement::STATUS_SETTLED) {
                    app(NotificationService::class)->settlementCompleted(
                        auth()->id(),
                        $this->settlement->settlement_no,
                        money_format($this->settlement->payable_amount),
                    );
                }
                $this->toastSuccess($newStatus === SupplierSettlement::STATUS_SETTLED ? '结算已结清' : '部分付款已确认');
            }
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());

            return;
        }

        $this->showPaymentModal = false;
        $this->paymentAmount = '';
        $this->paymentRemark = '';
        $this->loadSettlement($this->settlement->id);
    }

    public function openCloseModal(): void
    {
        if ($this->settlement->status === SupplierSettlement::STATUS_CLOSED) {
            $this->toastError('已办结不可重复操作');

            return;
        }
        $this->showCloseModal = true;
    }

    public function closeCloseModal(): void
    {
        $this->showCloseModal = false;
    }

    public function submitClose(): void
    {
        $this->settlement->update([
            'status' => SupplierSettlement::STATUS_CLOSED,
            'closed_at' => now(),
            'closed_by' => auth()->id(),
        ]);

        $this->loadSettlement($this->settlement->id);
        $this->showCloseModal = false;
        $this->toastSuccess('已办结');
    }

    public function render()
    {
        $paymentMethodMap = SettlementPayment::paymentMethodMap();
        $remainingAmount = $this->remainingAmount;
        $auditLogs = $this->settlement->auditLogs()->orderByDesc('id')->take(50)->get();

        return view('livewire.finance.supplier-settlement-detail', compact(
            'paymentMethodMap', 'remainingAmount', 'auditLogs'
        ))
            ->layout('components.app-layout')
            ->title("结算详情 {$this->settlement->settlement_no}");
    }
}
