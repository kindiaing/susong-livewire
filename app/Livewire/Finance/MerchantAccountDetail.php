<?php

namespace App\Livewire\Finance;

use App\Livewire\Traits\WithToast;
use App\Models\MerchantAccount;
use App\Models\Recharge;
use Livewire\Component;

class MerchantAccountDetail extends Component
{
    use WithToast;

    public MerchantAccount $account;

    public function mount(int $id): void
    {
        $this->loadAccount($id);
    }

    public function loadAccount(int $id): void
    {
        $this->account = MerchantAccount::with(['merchant', 'auditLogs.operator'])->findOrFail($id);
    }

    public function getAvailableBalanceProperty(): int
    {
        return $this->account->balance - $this->account->frozen_amount;
    }

    public function render()
    {
        $availableBalance = $this->availableBalance;

        // 最近充值记录
        $recentRecharges = Recharge::where('merchant_id', $this->account->merchant_id)
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        // 审计日志
        $auditLogs = $this->account->auditLogs()->orderByDesc('id')->take(50)->get();

        return view('livewire.finance.merchant-account-detail', compact(
            'availableBalance', 'recentRecharges', 'auditLogs'
        ))
            ->layout('components.app-layout')
            ->title("客户账户 {$this->account->merchant?->name}");
    }
}
