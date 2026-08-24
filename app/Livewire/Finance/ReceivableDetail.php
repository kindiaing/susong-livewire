<?php

namespace App\Livewire\Finance;

use App\Livewire\Traits\WithToast;
use App\Models\Receivable;
use App\Models\ReceivablePayment;
use App\Services\ReceivableService;
use Livewire\Component;

class ReceivableDetail extends Component
{
    use WithToast;

    public Receivable $receivable;

    // 收款弹窗
    public bool $showReceiveModal = false;

    public string $receiveAmount = '';

    public string $receiveRemark = '';

    // 办结弹窗
    public bool $showCloseModal = false;

    public string $closeRemark = '';

    public function mount(int $id): void
    {
        $this->loadReceivable($id);
    }

    public function loadReceivable(int $id): void
    {
        $this->receivable = Receivable::with(['order', 'merchant', 'payments.operator', 'closedByUser'])
            ->findOrFail($id);
    }

    public function getIsOverdueProperty(): bool
    {
        return $this->receivable->due_date
            && $this->receivable->due_date < \Carbon\Carbon::today()
            && !in_array($this->receivable->status, [Receivable::STATUS_SETTLED, Receivable::STATUS_CLOSED]);
    }

    public function getRemainingAmountProperty(): int
    {
        return max(0, $this->receivable->adjusted_amount - $this->receivable->received_amount);
    }

    public function openReceiveModal(): void
    {
        if (in_array($this->receivable->status, [Receivable::STATUS_SETTLED, Receivable::STATUS_CLOSED])) {
            $this->toastError('已结清/已办结不可收款');

            return;
        }
        $this->receiveAmount = '';
        $this->receiveRemark = '';
        $this->showReceiveModal = true;
    }

    public function submitReceive(): void
    {
        $this->validate([
            'receiveAmount' => 'required|numeric|min:0.01',
        ]);

        $cents = money_to_cents($this->receiveAmount);
        if ($cents <= 0) {
            $this->toastError('收款金额必须大于0');

            return;
        }

        $remaining = $this->receivable->adjusted_amount - $this->receivable->received_amount;
        if ($cents > $remaining) {
            $this->toastError('收款金额超出应收余额 ' . money_format($remaining) . ' 元');

            return;
        }

        $newReceived = $this->receivable->received_amount + $cents;
        $newStatus = $newReceived >= $this->receivable->adjusted_amount
            ? Receivable::STATUS_SETTLED
            : Receivable::STATUS_PARTIAL;

        $this->receivable->update([
            'received_amount' => $newReceived,
            'status' => $newStatus,
            'settled_at' => $newStatus === Receivable::STATUS_SETTLED ? now() : null,
        ]);

        ReceivablePayment::create([
            'receivable_id' => $this->receivable->id,
            'amount' => $cents,
            'payment_method' => ReceivablePayment::METHOD_MANUAL,
            'operator_id' => auth()->id(),
            'approval_status' => ReceivablePayment::APPROVAL_APPROVED,
            'remark' => $this->receiveRemark ?: null,
        ]);

        $this->loadReceivable($this->receivable->id);
        $this->showReceiveModal = false;
        $this->toastSuccess($newStatus === Receivable::STATUS_SETTLED ? '已结清' : '部分收款已确认');
    }

    public function closeReceiveModal(): void
    {
        $this->showReceiveModal = false;
        $this->receiveAmount = '';
        $this->receiveRemark = '';
    }

    public function openCloseModal(): void
    {
        if (in_array($this->receivable->status, [Receivable::STATUS_CLOSED])) {
            $this->toastError('已办结不可重复操作');

            return;
        }
        $this->closeRemark = '';
        $this->showCloseModal = true;
    }

    public function closeCloseModal(): void
    {
        $this->showCloseModal = false;
        $this->closeRemark = '';
    }

    public function submitClose(): void
    {
        $this->receivable->update([
            'status' => Receivable::STATUS_CLOSED,
            'closed_at' => now(),
            'closed_by' => auth()->id(),
            'remark' => $this->closeRemark ?: $this->receivable->remark,
        ]);

        $this->loadReceivable($this->receivable->id);
        $this->showCloseModal = false;
        $this->toastSuccess('已办结');
    }

    public function render()
    {
        $statusMap = Receivable::statusMap();
        $settlementMap = [
            Receivable::SETTLEMENT_CASH => '现结',
            Receivable::SETTLEMENT_CREDIT => '账期',
            Receivable::SETTLEMENT_PREPAID => '预付款',
        ];
        $paymentMethodMap = ReceivablePayment::paymentMethodMap();
        $isOverdue = $this->isOverdue;
        $remainingAmount = $this->remainingAmount;

        return view('livewire.finance.receivable-detail', compact(
            'statusMap', 'settlementMap', 'paymentMethodMap', 'isOverdue', 'remainingAmount'
        ))
            ->layout('components.app-layout')
            ->title("应收详情 {$this->receivable->receivable_no}");
    }
}
