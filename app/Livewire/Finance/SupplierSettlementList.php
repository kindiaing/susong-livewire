<?php

namespace App\Livewire\Finance;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithMoneyConversion;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Livewire\Traits\WithListCrud;
use App\Models\PurchaseOrder;
use App\Models\SettlementPayment;
use App\Models\Supplier;
use App\Models\SupplierSettlement;
use Livewire\Component;
use Livewire\WithPagination;

class SupplierSettlementList extends Component
{
    use WithPagination;
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithMoneyConversion;
    use WithToast;
    use WithListCrud;

    protected string $modelClass = SupplierSettlement::class;

    public string $search = '';

    public int $formSupplierId = 0;
    public int $formPurchaseOrderId = 0;
    public string $formAmount = '';
    public string $formSettlementDate = '';
    public string $formNote = '';

    // 部分付款弹窗
    public bool $showPaymentModal = false;
    public int $paymentSettlementId = 0;
    public string $paymentAmount = '';
    public string $paymentRemark = '';

    // 详情弹窗
    public bool $showDetailModal = false;
    public ?int $detailId = null;

    public static array $statusMap = [
        1 => '待结算', 2 => '部分付款', 3 => '已结清', 4 => '已办结',
    ];

    public static array $statusColorMap = [
        1 => 'yellow', 2 => 'blue', 3 => 'green', 4 => 'gray',
    ];

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'supplier', 'label' => '供应商', 'sortable' => false, 'exportable' => true],
            ['key' => 'purchaseOrder', 'label' => '采购单', 'sortable' => false, 'exportable' => true],
            ['key' => 'settlement_no', 'label' => '结算单号', 'sortable' => true, 'exportable' => true],
            ['key' => 'payable_amount', 'label' => '应付金额', 'sortable' => true, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => false, 'exportable' => true],
            ['key' => 'settlement_date', 'label' => '结算日期', 'sortable' => true, 'exportable' => true],
            ['key' => 'note', 'label' => '备注', 'sortable' => false, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return SupplierSettlement::with(['supplier', 'purchaseOrder'])
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('settlement_no', 'like', "%{$this->search}%")
                        ->orWhereHas('supplier', fn($sq) => $sq->where('name', 'like', "%{$this->search}%"));
                });
            })
            ->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '供应商结算_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return SupplierSettlement::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '供应商ID' => 'supplier_id',
            '采购单ID' => 'purchase_order_id',
            '金额(元)' => 'payable_amount',
            '结算日期' => 'settlement_date',
            '备注' => 'note',
        ];
    }

    public function getPageIds(): array
    {
        return $this->getExportQuery()->forPage($this->getPage(), 20)->pluck('id')->toArray();
    }

    public function getImportMoneyFields(): array
    {
        return ['payable_amount'];
    }

    public function save(): void
    {
        $this->validate([
            'formSupplierId' => 'required|integer|exists:suppliers,id',
            'formPurchaseOrderId' => 'required|integer|exists:purchase_orders,id',
            'formAmount' => 'required|numeric|min:0.01',
            'formSettlementDate' => 'required|date',
        ]);

        SupplierSettlement::create([
            'supplier_id' => $this->formSupplierId,
            'purchase_order_id' => $this->formPurchaseOrderId,
            'settlement_no' => generate_sequence_no('SS', 'supplier_settlements', 'settlement_no'),
            'payable_amount' => money_to_cents($this->formAmount),
            'total_amount' => money_to_cents($this->formAmount),
            'status' => 1,
            'settlement_date' => $this->formSettlementDate,
            'note' => $this->formNote ?: null,
        ]);

        $this->toastSuccess('结算记录已创建');
        $this->showModal = false;
        $this->resetForm();
    }

    public function openPartialPayment(int $id): void
    {
        $item = SupplierSettlement::findOrFail($id);
        if ($item->status !== 1 && $item->status !== 2) {
            $this->toastError('仅待结算/部分付款状态可操作');
            return;
        }
        $this->paymentSettlementId = $id;
        $this->paymentAmount = '';
        $this->paymentRemark = '';
        $this->showPaymentModal = true;
    }

    public function submitPartialPayment(): void
    {
        $this->validate([
            'paymentAmount' => 'required|numeric|min:0.01',
        ]);

        $item = SupplierSettlement::findOrFail($this->paymentSettlementId);
        $cents = money_to_cents($this->paymentAmount);

        if ($cents <= 0) {
            $this->toastError('付款金额必须大于0');
            return;
        }

        $newPaidAmount = $item->paid_amount + $cents;
        $newStatus = $newPaidAmount >= $item->payable_amount ? 3 : 2;

        $item->update([
            'paid_amount' => $newPaidAmount,
            'status' => $newStatus,
            'settled_at' => $newStatus === 3 ? now() : null,
        ]);

        // 同时创建付款记录
        SettlementPayment::create([
            'settlement_id' => $item->id,
            'amount' => $cents,
            'payment_method' => SettlementPayment::METHOD_MANUAL,
            'operator_id' => auth()->id(),
            'approval_status' => SettlementPayment::APPROVAL_APPROVED,
            'remark' => $this->paymentRemark ?: null,
        ]);

        $this->toastSuccess($newStatus === 3 ? '结算已结清' : '部分付款已确认');
        $this->showPaymentModal = false;
        $this->paymentSettlementId = 0;
        $this->paymentAmount = '';
        $this->paymentRemark = '';
    }

    public function openDetailModal(int $id): void
    {
        $this->detailId = $id;
        $this->showDetailModal = true;
    }

    public function confirmPayment(int $id): void
    {
        $item = SupplierSettlement::findOrFail($id);
        if ($item->status !== 1) {
            $this->toastError('仅待结算状态可确认付款');
            return;
        }
        $item->update(['status' => 3]);
        $this->toastSuccess('结算已确认付款');
    }

    public function delete(): void
    {
        SupplierSettlement::findOrFail($this->deletingId)->delete();
        $this->toastSuccess('结算记录已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->formSupplierId = 0;
        $this->formPurchaseOrderId = 0;
        $this->formAmount = '';
        $this->formSettlementDate = '';
        $this->formNote = '';
    }

    public function render()
    {
        $query = SupplierSettlement::with(['supplier', 'purchaseOrder'])->orderBy('id', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('settlement_no', 'like', "%{$this->search}%")
                    ->orWhereHas('supplier', fn($sq) => $sq->where('name', 'like', "%{$this->search}%"));
            });
        }

        $items = $query->paginate(setting('per_page', 10));
        $suppliers = Supplier::orderBy('name')->get();
        $purchaseOrders = PurchaseOrder::orderBy('id', 'desc')->get();
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();
        $statusMap = self::$statusMap;
        $statusColorMap = self::$statusColorMap;
        $detailItem = $this->detailId ? SupplierSettlement::with(['supplier', 'purchaseOrder', 'payments'])->find($this->detailId) : null;

        return view('livewire.finance.supplier-settlement-list', compact('items', 'suppliers', 'purchaseOrders', 'allColumns', 'selectedCount', 'statusMap', 'statusColorMap', 'detailItem'))
            ->layout('components.app-layout')
            ->title('供应商结算');
    }
}
