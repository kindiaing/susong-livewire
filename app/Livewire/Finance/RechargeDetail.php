<?php

namespace App\Livewire\Finance;

use App\Livewire\Traits\WithToast;
use App\Models\AuditLog;
use App\Models\MerchantAccount;
use App\Models\Recharge;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class RechargeDetail extends Component
{
    use WithToast;

    public Recharge $recharge;

    public function mount(int $id): void
    {
        $this->loadRecharge($id);
    }

    public function loadRecharge(int $id): void
    {
        $this->recharge = Recharge::with(['merchant', 'operator'])->findOrFail($id);
    }

    public function approve(): void
    {
        if ($this->recharge->status !== Recharge::STATUS_PENDING) {
            $this->toastError('当前状态不可审核');

            return;
        }

        DB::transaction(function () {
            $this->recharge->update([
                'status' => Recharge::STATUS_SUCCESS,
                'approval_status' => Recharge::APPROVAL_APPROVED,
                'operator_id' => auth()->id(),
            ]);

            $account = MerchantAccount::firstOrCreate(
                ['merchant_id' => $this->recharge->merchant_id],
                ['balance' => 0, 'total_recharge' => 0, 'total_consumption' => 0]
            );
            $account->increment('balance', $this->recharge->amount);
            $account->increment('total_recharge', $this->recharge->amount);

            AuditLog::log(
                modelType: Recharge::class,
                modelId: $this->recharge->id,
                action: 'approve',
                beforeData: ['status' => Recharge::STATUS_PENDING],
                afterData: [
                    'status' => Recharge::STATUS_SUCCESS,
                    'amount' => $this->recharge->amount,
                    'balance_after' => $account->balance,
                ],
            );
        });

        if ($this->recharge->merchant_id) {
            app(NotificationService::class)->rechargeApproved(
                $this->recharge->merchant_id,
                $this->recharge->transaction_no,
                money_format($this->recharge->amount),
            );
        }

        $this->loadRecharge($this->recharge->id);
        $this->toastSuccess('充值已确认成功，金额已入账');
    }

    public function reject(): void
    {
        if ($this->recharge->status !== Recharge::STATUS_PENDING) {
            $this->toastError('当前状态不可拒绝');

            return;
        }

        $this->recharge->update([
            'status' => Recharge::STATUS_FAILED,
            'approval_status' => Recharge::APPROVAL_REJECTED,
            'operator_id' => auth()->id(),
        ]);

        $this->loadRecharge($this->recharge->id);
        $this->toastSuccess('充值已拒绝');
    }

    public function confirmArrival(): void
    {
        if ($this->recharge->status !== Recharge::STATUS_SUCCESS) {
            $this->toastError('仅成功状态可确认到账');

            return;
        }

        $this->recharge->update([
            'remark' => ($this->recharge->remark ? $this->recharge->remark . "\n" : '') . '已确认到账 ' . now()->format('Y-m-d H:i'),
        ]);

        $this->loadRecharge($this->recharge->id);
        $this->toastSuccess('充值已确认到账');
    }

    public function render()
    {
        $statusMap = Recharge::statusMap();
        $statusColorMap = [
            Recharge::STATUS_PENDING => 'yellow',
            Recharge::STATUS_SUCCESS => 'green',
            Recharge::STATUS_FAILED => 'red',
        ];
        $paymentMethodMap = Recharge::paymentMethodMap();

        return view('livewire.finance.recharge-detail', compact(
            'statusMap', 'statusColorMap', 'paymentMethodMap'
        ))
            ->layout('components.app-layout')
            ->title("充值详情 {$this->recharge->transaction_no}");
    }
}
