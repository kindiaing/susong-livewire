<?php

namespace App\Livewire\Finance;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithListCrud;
use App\Livewire\Traits\WithMoneyConversion;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Models\SettlementPayment;
use App\Models\Supplier;
use App\Models\SupplierSettlement;
use App\Services\NotificationService;
use App\Services\SupplierSettlementService;
use Livewire\Component;
use Livewire\WithPagination;

class SupplierSettlementList extends Component
{
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithListCrud;
    use WithMoneyConversion;
    use WithPagination;
    use WithRowSelection;
    use WithToast;

    protected string $modelClass = SupplierSettlement::class;

    public string $search = '';

    public int $formSupplierId = 0;

    public string $formStartDate = '';

    public string $formEndDate = '';

    public string $formServiceFee = '';

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
            ['key' => 'settlement_no', 'label' => '结算单号', 'sortable' => true, 'exportable' => true],
            ['key' => 'date_range', 'label' => '结算周期', 'sortable' => false, 'exportable' => true],
            ['key' => 'total_amount', 'label' => '汇总金额', 'sortable' => true, 'exportable' => true],
            ['key' => 'return_amount', 'label' => '退货扣减', 'sortable' => true, 'exportable' => true],
            ['key' => 'service_fee', 'label' => '服务费', 'sortable' => true, 'exportable' => true],
            ['key' => 'payable_amount', 'label' => '应付金额', 'sortable' => true, 'exportable' => true],
            ['key' => 'paid_amount', 'label' => '已付金额', 'sortable' => true, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => false, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return SupplierSettlement::with('supplier')
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('settlement_no', 'like', "%{$this->search}%")
                        ->orWhereHas('supplier', fn ($sq) => $sq->where('name', 'like', "%{$this->search}%"));
                });
            })
            ->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '供应商结算_'.now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return SupplierSettlement::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '供应商ID' => 'supplier_id',
            '结算周期开始' => 'start_date',
            '结算周期结束' => 'end_date',
            '应付金额(元)' => 'payable_amount',
            '汇总金额(元)' => 'total_amount',
        ];
    }

    public function getPageIds(): array
    {
        return $this->getExportQuery()->forPage($this->getPage(), 20)->pluck('id')->toArray();
    }

    public function getImportMoneyFields(): array
    {
        return ['payable_amount', 'total_amount'];
    }

    /**
     * 生成结算单：按周期自动汇总已入库采购单实际金额，扣减采购退货与服务费
     */
    public function save(): void
    {
        $this->validate([
            'formSupplierId' => 'required|integer|min:1|exists:suppliers,id',
            'formStartDate' => 'required|date',
            'formEndDate' => 'required|date|after_or_equal:formStartDate',
            'formServiceFee' => 'nullable|numeric|min:0',
        ]);

        try {
            $settlement = app(SupplierSettlementService::class)->generate(
                supplierId: $this->formSupplierId,
                startDate: $this->formStartDate,
                endDate: $this->formEndDate,
                serviceFee: money_to_cents($this->formServiceFee ?: '0'),
            );
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());

            return;
        }

        $this->toastSuccess("结算单 {$settlement->settlement_no} 已生成：汇总 {$settlement->items->count()} 张采购单，应付 ".money_format($settlement->payable_amount));
        $this->showModal = false;
        $this->resetForm();
    }

    public function openPartialPayment(int $id): void
    {
        $item = SupplierSettlement::findOrFail($id);
        if ($item->status !== SupplierSettlement::STATUS_PENDING && $item->status !== SupplierSettlement::STATUS_PARTIAL) {
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

        $remaining = $item->payable_amount - $item->paid_amount;
        if ($cents > $remaining) {
            $this->toastError('付款金额超出剩余应付 '.money_format($remaining));

            return;
        }

        $newPaidAmount = $item->paid_amount + $cents;
        $newStatus = $newPaidAmount >= $item->payable_amount
            ? SupplierSettlement::STATUS_SETTLED
            : SupplierSettlement::STATUS_PARTIAL;

        $item->update([
            'paid_amount' => $newPaidAmount,
            'status' => $newStatus,
            'settled_at' => $newStatus === SupplierSettlement::STATUS_SETTLED ? now() : null,
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

        $this->toastSuccess($newStatus === SupplierSettlement::STATUS_SETTLED ? '结算已结清' : '部分付款已确认');

        // 通知当前用户：结算状态变更
        if ($newStatus === SupplierSettlement::STATUS_SETTLED) {
            app(NotificationService::class)->settlementCompleted(
                auth()->id(),
                $item->settlement_no,
                money_format($item->payable_amount),
            );
        }

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

    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->detailId = null;
    }

    public function closePaymentModal(): void
    {
        $this->showPaymentModal = false;
        $this->paymentSettlementId = 0;
        $this->paymentAmount = '';
        $this->paymentRemark = '';
    }

    public function delete(): void
    {
        $item = SupplierSettlement::findOrFail($this->deletingId);

        if ($item->status !== SupplierSettlement::STATUS_PENDING) {
            $this->toastError('仅待结算状态的结算单可删除');
            $this->showDeleteConfirm = false;
            $this->deletingId = null;

            return;
        }

        $item->items()->delete();
        $item->delete();
        $this->toastSuccess('结算记录已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->formSupplierId = 0;
        $this->formStartDate = '';
        $this->formEndDate = '';
        $this->formServiceFee = '';
    }

    public function render()
    {
        $query = SupplierSettlement::with('supplier')->orderBy('id', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('settlement_no', 'like', "%{$this->search}%")
                    ->orWhereHas('supplier', fn ($sq) => $sq->where('name', 'like', "%{$this->search}%"));
            });
        }

        $items = $query->paginate(setting('per_page', 10));
        $suppliers = Supplier::orderBy('name')->get();
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();
        $statusMap = self::$statusMap;
        $statusColorMap = self::$statusColorMap;
        $detailItem = $this->detailId
            ? SupplierSettlement::with(['supplier', 'items.purchaseOrder', 'payments'])->find($this->detailId)
            : null;

        return view('livewire.finance.supplier-settlement-list', compact('items', 'suppliers', 'allColumns', 'selectedCount', 'statusMap', 'statusColorMap', 'detailItem'))
            ->layout('components.app-layout')
            ->title('供应商结算');
    }
}
