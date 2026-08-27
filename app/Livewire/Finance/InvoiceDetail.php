<?php

namespace App\Livewire\Finance;

use App\Livewire\Traits\WithToast;
use App\Models\Invoice;
use Livewire\Component;

class InvoiceDetail extends Component
{
    use WithToast;

    public Invoice $invoice;

    public function mount(int $id): void
    {
        $this->loadInvoice($id);
    }

    public function loadInvoice(int $id): void
    {
        $this->invoice = Invoice::with(['merchant', 'supplier'])->findOrFail($id);
    }

    public function issue(): void
    {
        if ($this->invoice->status !== Invoice::STATUS_PENDING) {
            $this->toastError('仅待开具状态可操作');

            return;
        }

        $this->invoice->update([
            'status' => Invoice::STATUS_ISSUED,
            'issued_at' => now(),
        ]);

        $this->loadInvoice($this->invoice->id);
        $this->toastSuccess('发票已开具');
    }

    public function send(): void
    {
        if ($this->invoice->status !== Invoice::STATUS_ISSUED) {
            $this->toastError('仅已开具状态可寄出');

            return;
        }

        $this->invoice->update([
            'status' => Invoice::STATUS_SENT,
            'sent_at' => now(),
        ]);

        $this->loadInvoice($this->invoice->id);
        $this->toastSuccess('发票已寄出');
    }

    public function render()
    {
        $statusMap = Invoice::statusMap();
        $statusColorMap = [
            Invoice::STATUS_PENDING => 'yellow',
            Invoice::STATUS_ISSUED => 'green',
            Invoice::STATUS_SENT => 'blue',
        ];
        $typeMap = Invoice::typeMap();

        return view('livewire.finance.invoice-detail', compact(
            'statusMap', 'statusColorMap', 'typeMap'
        ))
            ->layout('components.app-layout')
            ->title("发票详情 {$this->invoice->invoice_no}");
    }
}
