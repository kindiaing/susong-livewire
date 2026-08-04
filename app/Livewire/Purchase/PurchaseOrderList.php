<?php

namespace App\Livewire\Purchase;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithToast;
use App\Livewire\Traits\WithListCrud;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class PurchaseOrderList extends Component
{
    use WithPagination;
    use WithRowSelection, WithColumnVisibility, WithExcelExport, WithExcelImport;
    use WithToast;
    use WithListCrud;

    protected string $modelClass = PurchaseOrder::class;

    public string $search = '';
    public int $filterStatus = -1;

    public int $formSupplierId = 0;
    public string $formPurchaseDate = '';
    public string $formRemark = '';

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function openEditModal(int $id): void
    {
        $order = PurchaseOrder::findOrFail($id);
        $this->editingId = $id;
        $this->formSupplierId = $order->supplier_id;
        $this->formPurchaseDate = $order->purchase_date?->format('Y-m-d') ?? '';
        $this->formRemark = $order->remark ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'formSupplierId' => 'required|integer|min:1',
            'formPurchaseDate' => 'nullable|date',
            'formRemark' => 'nullable|string|max:500',
        ]);

        $data = [
            'supplier_id' => $validated['formSupplierId'],
            'purchase_date' => $validated['formPurchaseDate'] ?: null,
            'remark' => $validated['formRemark'] ?: null,
        ];

        if ($this->editingId) {
            PurchaseOrder::findOrFail($this->editingId)->update($data);
            $this->toastSuccess('采购单已更新');
        } else {
            $data['order_no'] = PurchaseOrder::generateOrderNo();
            $data['status'] = PurchaseOrder::STATUS_PENDING;
            $data['operator_id'] = Auth::id();
            $data['ordered_at'] = now();
            PurchaseOrder::create($data);
            $this->toastSuccess('采购单已创建');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(): void
    {
        PurchaseOrder::findOrFail($this->deletingId)->delete();
        $this->toastSuccess('采购单已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterStatus = -1;
        $this->resetPage();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->formSupplierId = 0;
        $this->formPurchaseDate = '';
        $this->formRemark = '';
    }

    public function getDefaultColumns(): array
    {
        return ['order_no', 'supplier_id', 'purchase_date', 'status', 'total_amount'];
    }

    public function getExportRowCallback(): callable
    {
        return function ($row) {
            return [
                'id' => $row->id,
                'order_no' => $row->order_no,
                'supplier_id' => $row->supplier?->name ?? '',
                'status' => $row->status,
                'total_amount' => money_format($row->total_amount, false),
                'remark' => $row->remark ?? '',
                'created_at' => $row->created_at?->format('Y-m-d H:i:s'),
            ];
        };
    }

    public function getImportUniqueBy(): array
    {
        return ['order_no'];
    }

    public function getImportRequiredFields(): array
    {
        return ['供应商ID'];
    }

    public function getImportValueMap(): array
    {
        return [];
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'order_no', 'label' => '采购单号', 'sortable' => true, 'exportable' => true],
            ['key' => 'supplier_id', 'label' => '供应商', 'sortable' => true, 'exportable' => true],
            ['key' => 'purchase_date', 'label' => '采购日期', 'sortable' => true, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => true, 'exportable' => true],
            ['key' => 'total_amount', 'label' => '总金额', 'sortable' => true, 'exportable' => true, 'type' => 'money'],
            ['key' => 'actual_amount', 'label' => '实际金额', 'sortable' => true, 'exportable' => true, 'type' => 'money'],
            ['key' => 'remark', 'label' => '备注', 'sortable' => false, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return PurchaseOrder::with('supplier')->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '采购单_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return PurchaseOrder::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '采购单号' => 'order_no',
            '供应商ID' => 'supplier_id',
            '备注' => 'remark',
        ];
    }

    public function getPageIds(): array
    {
        return PurchaseOrder::orderBy('id', 'desc')->limit(20)->pluck('id')->toArray();
    }

    public function render()
    {
        $query = PurchaseOrder::with('supplier')->orderBy('id', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('order_no', 'like', "%{$this->search}%")
                    ->orWhereHas('supplier', function ($sq) {
                        $sq->where('name', 'like', "%{$this->search}%");
                    });
            });
        }

        if ($this->filterStatus > 0) {
            $query->where('status', $this->filterStatus);
        }

        $orders = $query->paginate(setting('per_page', 10));
        $suppliers = Supplier::orderBy('name')->get();
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();

        return view('livewire.purchase.purchase-order-list', compact('orders', 'suppliers', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('采购单管理');
    }
}
