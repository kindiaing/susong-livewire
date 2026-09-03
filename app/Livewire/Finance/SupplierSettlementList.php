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
use Illuminate\Support\Facades\Redirect;
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

    public string $formEditServiceFee = '';

    // 部分付款弹窗
    public bool $showPaymentModal = false;

    public int $paymentSettlementId = 0;

    public string $paymentAmount = '';

    public string $paymentRemark = '';

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true, 'width' => '50px'],
            ['key' => 'supplier', 'label' => '供应商', 'sortable' => false, 'exportable' => true, 'width' => '100px'],
            ['key' => 'settlement_no', 'label' => '结算单号', 'sortable' => true, 'exportable' => true, 'width' => '140px'],
            ['key' => 'date_range', 'label' => '结算周期', 'sortable' => false, 'exportable' => true, 'width' => '160px'],
            ['key' => 'total_amount', 'label' => '汇总金额', 'sortable' => true, 'exportable' => true, 'width' => '100px'],
            ['key' => 'return_amount', 'label' => '退货扣减', 'sortable' => true, 'exportable' => true, 'width' => '90px'],
            ['key' => 'service_fee', 'label' => '服务费', 'sortable' => true, 'exportable' => true, 'width' => '80px'],
            ['key' => 'payable_amount', 'label' => '应付金额', 'sortable' => true, 'exportable' => true, 'width' => '100px'],
            ['key' => 'paid_amount', 'label' => '已付金额', 'sortable' => true, 'exportable' => true, 'width' => '100px'],
            ['key' => 'proxy_payment', 'label' => '代付', 'sortable' => true, 'exportable' => true, 'width' => '80px'],
            ['key' => 'status', 'label' => '状态', 'sortable' => false, 'exportable' => true, 'width' => '70px'],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true, 'width' => '120px'],
        ];
    }

    public function getDefaultColumns(): array
    {
        return ['supplier', 'date_range', 'total_amount', 'return_amount', 'service_fee', 'payable_amount', 'paid_amount', 'proxy_payment', 'status'];
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

    public function getImportRequiredFields(): array
    {
        return ['供应商ID', '结算周期开始', '结算周期结束'];
    }

    public function openEditModal(int $id): void
    {
        $item = SupplierSettlement::findOrFail($id);
        if (! $item->canEdit()) {
            $this->toastError('仅待结算状态可编辑');

            return;
        }
        $this->editingId = $id;
        $this->formEditServiceFee = $this->centsToYuan($item->service_fee);
        $this->showModal = true;
    }

    public function saveEdit(): void
    {
        $item = SupplierSettlement::findOrFail($this->editingId);
        if (! $item->canEdit()) {
            $this->toastError('仅待结算状态可编辑');
            $this->showModal = false;
            $this->resetForm();

            return;
        }

        $validated = $this->validate([
            'formEditServiceFee' => 'nullable|numeric|min:0',
        ]);

        $newFee = money_to_cents($validated['formEditServiceFee'] ?: '0');
        $item->update([
            'service_fee' => $newFee,
            'payable_amount' => $item->total_amount - $item->return_amount - $newFee,
        ]);

        $this->toastSuccess('结算单已更新');
        $this->showModal = false;
        $this->resetForm();
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
        abort_unless(auth()->user()?->can('finance.supplier-settlement.pay'), 403, '无供应商结算付款权限');

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
            // 统一走审批流：审核开启时仅登记待审核记录（不动结算单），关闭时直接生效
            $payment = app(SupplierSettlementService::class)->recordPayment(
                settlementId: $this->paymentSettlementId,
                amount: $cents,
                paymentMethod: SettlementPayment::METHOD_MANUAL,
                remark: $this->paymentRemark ?: null,
            );

            if ($payment->approval_status === SettlementPayment::APPROVAL_PENDING) {
                $this->toastSuccess('付款已登记，待审核');
            } else {
                $settlement = SupplierSettlement::find($this->paymentSettlementId);
                $settled = $settlement && $settlement->status === SupplierSettlement::STATUS_SETTLED;
                if ($settled) {
                    app(NotificationService::class)->settlementCompleted(
                        auth()->id(),
                        $settlement->settlement_no,
                        money_format($settlement->payable_amount),
                    );
                }
                $this->toastSuccess($settled ? '结算已结清' : '部分付款已确认');
            }
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());

            return;
        }

        $this->showPaymentModal = false;
        $this->paymentSettlementId = 0;
        $this->paymentAmount = '';
        $this->paymentRemark = '';
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
        $this->formEditServiceFee = '';
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

        // 当页汇总
        $pageTotals = [
            'total_amount'    => $items->sum('total_amount'),
            'return_amount'   => $items->sum('return_amount'),
            'service_fee'     => $items->sum('service_fee'),
            'payable_amount'  => $items->sum('payable_amount'),
            'paid_amount'     => $items->sum('paid_amount'),
            'proxy_payment'   => $items->sum('proxy_payment'),
        ];

        // 全部汇总（与列表同一查询条件，不分页）
        $baseQuery = SupplierSettlement::query()->when($this->search, function ($q) {
            $q->where(function ($q2) {
                $q2->where('settlement_no', 'like', "%{$this->search}%")
                    ->orWhereHas('supplier', fn ($sq) => $sq->where('name', 'like', "%{$this->search}%"));
            });
        });
        $grandTotals = [
            'total_amount'    => (clone $baseQuery)->sum('total_amount'),
            'return_amount'   => (clone $baseQuery)->sum('return_amount'),
            'service_fee'     => (clone $baseQuery)->sum('service_fee'),
            'payable_amount'  => (clone $baseQuery)->sum('payable_amount'),
            'paid_amount'     => (clone $baseQuery)->sum('paid_amount'),
            'proxy_payment'   => (clone $baseQuery)->sum('proxy_payment'),
        ];

        return view('livewire.finance.supplier-settlement-list', compact('items', 'suppliers', 'allColumns', 'selectedCount', 'pageTotals', 'grandTotals'))
            ->layout('components.app-layout')
            ->title('供应商结算');
    }
}
