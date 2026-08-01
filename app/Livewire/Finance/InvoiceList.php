<?php

namespace App\Livewire\Finance;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithMoneyConversion;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Models\Invoice;
use App\Models\Merchant;
use Livewire\Component;
use Livewire\WithPagination;

class InvoiceList extends Component
{
    use WithPagination;
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithMoneyConversion;
    use WithToast;

    protected string $modelClass = Invoice::class;

    public string $search = '';
    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    public int $formMerchantId = 0;
    public string $formAmount = '';
    public int $formType = 1;
    public string $formTitle = '';
    public string $formTaxNo = '';

    public static array $statusMap = [
        1 => '待开具', 2 => '已开具', 3 => '已寄出',
    ];

    public static array $statusColorMap = [
        1 => 'yellow', 2 => 'green', 3 => 'blue',
    ];

    public static array $typeMap = [
        1 => '客户发票', 2 => '供应商发票',
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
            ['key' => 'invoice_no', 'label' => '发票号', 'sortable' => true, 'exportable' => true],
            ['key' => 'amount', 'label' => '金额', 'sortable' => true, 'exportable' => true],
            ['key' => 'type', 'label' => '类型', 'sortable' => false, 'exportable' => true],
            ['key' => 'title', 'label' => '抬头', 'sortable' => false, 'exportable' => true],
            ['key' => 'tax_no', 'label' => '税号', 'sortable' => false, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => false, 'exportable' => true],
            ['key' => 'issued_at', 'label' => '开具时间', 'sortable' => true, 'exportable' => true],
            ['key' => 'note', 'label' => '备注', 'sortable' => false, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return Invoice::with('merchant')
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('invoice_no', 'like', "%{$this->search}%")
                        ->orWhereHas('merchant', fn($mq) => $mq->where('name', 'like', "%{$this->search}%"));
                });
            })
            ->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '发票管理_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return Invoice::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '商家ID' => 'merchant_id',
            '金额(元)' => 'amount',
            '类型' => 'type',
            '抬头' => 'title',
            '税号' => 'tax_no',
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
            'formType' => 'required|integer|in:1,2',
            'formTitle' => 'required|string|max:200',
            'formTaxNo' => 'required|string|max:50',
        ]);

        Invoice::create([
            'merchant_id' => $this->formMerchantId,
            'invoice_no' => 'INV' . now()->format('YmdHis') . str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT),
            'amount' => money_to_cents($this->formAmount),
            'type' => $this->formType,
            'title' => $this->formTitle,
            'tax_no' => $this->formTaxNo,
            'status' => 1,
        ]);

        $this->toastSuccess('发票已创建');
        $this->showModal = false;
        $this->resetForm();
    }

    public function issue(int $id): void
    {
        $item = Invoice::findOrFail($id);
        if ($item->status !== 1) {
            $this->toastError('仅待开具状态可操作');
            return;
        }
        $item->update([
            'status' => 2,
            'issued_at' => now()->toDateString(),
        ]);
        $this->toastSuccess('发票已开具');
    }

    public function send(int $id): void
    {
        $item = Invoice::findOrFail($id);
        if ($item->status !== 2) {
            $this->toastError('仅已开具状态可寄出');
            return;
        }
        $item->update(['status' => 3]);
        $this->toastSuccess('发票已寄出');
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteConfirm = true;
    }

    public function delete(): void
    {
        Invoice::findOrFail($this->deletingId)->delete();
        $this->toastSuccess('发票已删除');
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
        $this->formType = 1;
        $this->formTitle = '';
        $this->formTaxNo = '';
    }

    public function render()
    {
        $query = Invoice::with('merchant')->orderBy('id', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('invoice_no', 'like', "%{$this->search}%")
                    ->orWhereHas('merchant', fn($mq) => $mq->where('name', 'like', "%{$this->search}%"));
            });
        }

        $items = $query->paginate(setting('per_page', 10));
        $merchants = Merchant::orderBy('name')->get();
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();

        return view('livewire.finance.invoice-list', compact('items', 'merchants', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('发票管理');
    }
}
