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

    // 执行付款弹窗
    public bool $showExecuteModal = false;

    public int $executePaymentId = 0;

    public int $executePaymentMethod = SettlementPayment::METHOD_MANUAL;

    public string $executeTransactionNo = '';

    public string $executeRemark = '';

    /** @var array<int, string> 付款凭证图片URL列表（非必填） */
    public array $executeEvidenceUrls = [];

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
        abort_unless(auth()->user()?->can('finance.supplier-settlement.pay'), 403, '无供应商结算付款权限');

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
        abort_unless(auth()->user()?->can('finance.supplier-settlement.pay'), 403, '无供应商结算付款权限');

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
                        money_format($this->settlement->payable_amount, false),
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

    /**
     * 打开执行付款弹窗（仅已通过待执行的付款记录）
     */
    public function openExecuteModal(int $paymentId): void
    {
        abort_unless(auth()->user()?->can('finance.supplier-settlement.pay'), 403, '无供应商结算付款权限');

        $payment = SettlementPayment::findOrFail($paymentId);
        if ($payment->settlement_id !== $this->settlement->id) {
            $this->toastError('付款记录不属于当前结算单');

            return;
        }
        if ($payment->approval_status !== SettlementPayment::APPROVAL_APPROVED) {
            $this->toastError('仅已通过待执行的付款记录可执行');

            return;
        }

        $this->executePaymentId = $paymentId;
        $this->executePaymentMethod = SettlementPayment::METHOD_MANUAL;
        $this->executeTransactionNo = '';
        $this->executeRemark = '';
        $this->executeEvidenceUrls = [];
        $this->showExecuteModal = true;
    }

    public function closeExecuteModal(): void
    {
        $this->showExecuteModal = false;
        $this->executePaymentId = 0;
        $this->executeTransactionNo = '';
        $this->executeRemark = '';
        $this->executeEvidenceUrls = [];
    }

    /**
     * 出纳执行付款：审核通过的付款记录真正生效，累计已付金额
     */
    public function submitExecutePayment(): void
    {
        abort_unless(auth()->user()?->can('finance.supplier-settlement.pay'), 403, '无供应商结算付款权限');

        if ($this->executePaymentId <= 0) {
            $this->toastError('请选择要执行的付款记录');

            return;
        }

        $evidenceJson = $this->executeEvidenceUrls
            ? (json_encode(array_values($this->executeEvidenceUrls), JSON_UNESCAPED_UNICODE) ?: null)
            : null;

        try {
            $payment = app(SupplierSettlementService::class)->executePayment(
                paymentId: $this->executePaymentId,
                paymentMethod: $this->executePaymentMethod,
                transactionNo: $this->executeTransactionNo ?: null,
                remark: $this->executeRemark ?: null,
                evidenceUrls: $evidenceJson,
            );

            $this->loadSettlement($this->settlement->id);

            $settled = $this->settlement->status === SupplierSettlement::STATUS_SETTLED;
            if ($settled && ($userId = (int) auth()->id())) {
                app(NotificationService::class)->settlementCompleted(
                    $userId,
                    $this->settlement->settlement_no,
                    money_format($this->settlement->payable_amount, false),
                );
            }
            $this->toastSuccess($settled ? '付款已执行，结算已结清' : '付款已执行');
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());

            return;
        }

        $this->showExecuteModal = false;
        $this->executePaymentId = 0;
        $this->executeTransactionNo = '';
        $this->executeRemark = '';
        $this->executeEvidenceUrls = [];
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

        $approvalColors = [
            SettlementPayment::APPROVAL_PENDING => 'bg-orange-100 text-orange-700',
            SettlementPayment::APPROVAL_APPROVED => 'bg-blue-100 text-blue-700',
            SettlementPayment::APPROVAL_REJECTED => 'bg-gray-100 text-gray-600',
            SettlementPayment::APPROVAL_EXECUTED => 'bg-green-100 text-green-700',
        ];

        return view('livewire.finance.supplier-settlement-detail', compact(
            'paymentMethodMap', 'remainingAmount', 'auditLogs', 'approvalColors'
        ))
            ->layout('components.app-layout')
            ->title("结算详情 {$this->settlement->settlement_no}");
    }
}
