<?php

namespace App\Livewire\Finance;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithMoneyConversion;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Models\Merchant;
use App\Models\Recharge;
use Livewire\Component;
use Livewire\WithPagination;

class RechargeList extends Component
{
    use WithPagination;
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithMoneyConversion;
    use WithToast;

    protected string $modelClass = Recharge::class;

    public string $search = '';
    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    public int $formMerchantId = 0;
    public string $formAmount = '';
    public int $formPaymentMethod = 0;
    public string $formNote = '';

    public static array $statusMap = [
        0 => '待审核',
        1 => '已通过',
        2 => '已拒绝',
        3 => '已到账',
    ];

    public static array $paymentMethodMap = [
        0 => '银行转账',
        1 => '微信支付',
        2 => '支付宝',
        3 => '现金',
    ];

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'merchant', 'label' => '商家', 'sortable' => false, 'exportable' => true],
            ['key' => 'transaction_no', 'label' => '交易号', 'sortable' => true, 'exportable' => true],
            ['key' => 'amount', 'label' => '金额', 'sortable' => true, 'exportable' => true],
            ['key' => 'payment_method', 'label' => '支付方式', 'sortable' => false, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => false, 'exportable' => true],
            ['key' => 'note', 'label' => '备注', 'sortable' => false, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return Recharge::with('merchant')
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('transaction_no', 'like', "%{$this->search}%")
                        ->orWhereHas('merchant', fn($mq) => $mq->where('name', 'like', "%{$this->search}%"));
                });
            })
            ->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '客户充值_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return Recharge::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '商家ID' => 'merchant_id',
            '金额(元)' => 'amount',
            '支付方式' => 'payment_method',
            '备注' => 'note',
        ];
    }

    public function getPageIds(): array
    {
        return $this->getExportQuery()->forPage($this->getPage(), 20)->pluck('id')->toArray();
    }

    public function getImportMoneyFields(): array
    {
        return ['amount'];
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'formMerchantId' => 'required|integer|exists:merchants,id',
            'formAmount' => 'required|numeric|min:0.01',
            'formPaymentMethod' => 'required|integer|in:0,1,2,3',
        ]);

        Recharge::create([
            'merchant_id' => $this->formMerchantId,
            'transaction_no' => 'RC' . now()->format('YmdHis') . str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT),
            'amount' => money_to_cents($this->formAmount),
            'payment_method' => $this->formPaymentMethod,
            'status' => 0,
            'note' => $this->formNote ?: null,
        ]);

        $this->toastSuccess('充值记录已创建');
        $this->showModal = false;
        $this->resetForm();
    }

    public function approve(int $id): void
    {
        $item = Recharge::findOrFail($id);
        if ($item->status !== 0) {
            $this->toastError('当前状态不可审核');
            return;
        }
        $item->update(['status' => 1]);
        $this->toastSuccess('充值已审核通过');
    }

    public function reject(int $id): void
    {
        $item = Recharge::findOrFail($id);
        if ($item->status !== 0) {
            $this->toastError('当前状态不可拒绝');
            return;
        }
        $item->update(['status' => 2]);
        $this->toastSuccess('充值已拒绝');
    }

    public function confirmArrival(int $id): void
    {
        $item = Recharge::findOrFail($id);
        if ($item->status !== 1) {
            $this->toastError('仅已通过状态可确认到账');
            return;
        }
        $item->update(['status' => 3]);
        $this->toastSuccess('充值已确认到账');
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteConfirm = true;
    }

    public function delete(): void
    {
        Recharge::findOrFail($this->deletingId)->delete();
        $this->toastSuccess('充值记录已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->resetPage();
        $this->clearSelection();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetErrorBag();
        $this->resetForm();
    }

    public function closeDeleteConfirm(): void
    {
        $this->showDeleteConfirm = false;
        $this->resetErrorBag();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->formMerchantId = 0;
        $this->formAmount = '';
        $this->formPaymentMethod = 0;
        $this->formNote = '';
    }

    public function render()
    {
        $query = Recharge::with('merchant')->orderBy('id', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('transaction_no', 'like', "%{$this->search}%")
                    ->orWhereHas('merchant', fn($mq) => $mq->where('name', 'like', "%{$this->search}%"));
            });
        }

        $items = $query->paginate(setting('per_page', 10));
        $merchants = Merchant::orderBy('name')->get();
        $allColumns = $this->getAllColumns();

        return view('livewire.finance.recharge-list', compact('items', 'merchants', 'allColumns'))
            ->layout('components.app-layout')
            ->title('客户充值');
    }
}
