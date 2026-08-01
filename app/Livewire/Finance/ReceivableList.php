<?php

namespace App\Livewire\Finance;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithMoneyConversion;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\Receivable;
use Livewire\Component;
use Livewire\WithPagination;

class ReceivableList extends Component
{
    use WithPagination;
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithMoneyConversion;
    use WithToast;

    protected string $modelClass = Receivable::class;

    public string $search = '';
    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    public int $formOrderId = 0;
    public int $formMerchantId = 0;
    public string $formAmount = '';

    public static array $statusMap = [
        0 => '未收',
        1 => '已收',
        2 => '部分收款',
    ];

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'order', 'label' => '订单号', 'sortable' => false, 'exportable' => true],
            ['key' => 'merchant', 'label' => '商家', 'sortable' => false, 'exportable' => true],
            ['key' => 'amount', 'label' => '应收金额', 'sortable' => true, 'exportable' => true],
            ['key' => 'received_amount', 'label' => '已收金额', 'sortable' => true, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => false, 'exportable' => true],
            ['key' => 'note', 'label' => '备注', 'sortable' => false, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return Receivable::with(['order', 'merchant'])
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->orWhereHas('order', fn($oq) => $oq->where('order_no', 'like', "%{$this->search}%"))
                        ->orWhereHas('merchant', fn($mq) => $mq->where('name', 'like', "%{$this->search}%"));
                });
            })
            ->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '应收账款_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return Receivable::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '订单ID' => 'order_id',
            '商家ID' => 'merchant_id',
            '金额(元)' => 'amount',
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
            'formOrderId' => 'required|integer|exists:orders,id',
            'formMerchantId' => 'required|integer|exists:merchants,id',
            'formAmount' => 'required|numeric|min:0.01',
        ]);

        Receivable::create([
            'order_id' => $this->formOrderId,
            'merchant_id' => $this->formMerchantId,
            'amount' => money_to_cents($this->formAmount),
            'received_amount' => 0,
            'status' => 0,
        ]);

        $this->toastSuccess('应收账款已创建');
        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmReceived(int $id): void
    {
        $item = Receivable::findOrFail($id);
        if ($item->status === 1) {
            $this->toastError('该账款已全额收款');
            return;
        }
        $item->update([
            'received_amount' => $item->amount,
            'status' => 1,
        ]);
        $this->toastSuccess('已确认收款');
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteConfirm = true;
    }

    public function delete(): void
    {
        Receivable::findOrFail($this->deletingId)->delete();
        $this->toastSuccess('应收账款已删除');
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
        $this->formOrderId = 0;
        $this->formMerchantId = 0;
        $this->formAmount = '';
    }

    public function render()
    {
        $query = Receivable::with(['order', 'merchant'])->orderBy('id', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('order', fn($oq) => $oq->where('order_no', 'like', "%{$this->search}%"))
                    ->orWhereHas('merchant', fn($mq) => $mq->where('name', 'like', "%{$this->search}%"));
            });
        }

        $items = $query->paginate(setting('per_page', 10));
        $merchants = Merchant::orderBy('name')->get();
        $orders = Order::orderBy('id', 'desc')->get();
        $allColumns = $this->getAllColumns();

        return view('livewire.finance.receivable-list', compact('items', 'merchants', 'orders', 'allColumns'))
            ->layout('components.app-layout')
            ->title('应收账款');
    }
}
