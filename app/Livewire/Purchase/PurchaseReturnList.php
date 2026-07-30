<?php

namespace App\Livewire\Purchase;

use App\Models\PurchaseReturn;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use Livewire\Component;
use Livewire\WithPagination;

class PurchaseReturnList extends Component
{
    use WithPagination;
    use WithRowSelection, WithColumnVisibility, WithExcelExport, WithExcelImport;

    protected string $modelClass = PurchaseReturn::class;

    public string $search = '';
    public int $filterStatus = -1;
    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    public int $formPurchaseOrderId = 0;
    public int $formSupplierId = 0;
    public int $formWarehouseId = 0;
    public string $formReason = '';
    public string $formRemark = '';

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $item = PurchaseReturn::findOrFail($id);
        $this->editingId = $id;
        $this->formPurchaseOrderId = $item->purchase_order_id;
        $this->formSupplierId = $item->supplier_id;
        $this->formWarehouseId = $item->warehouse_id;
        $this->formReason = $item->reason ?? '';
        $this->formRemark = $item->remark ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'formPurchaseOrderId' => 'required|integer|exists:purchase_orders,id',
            'formSupplierId' => 'required|integer|exists:suppliers,id',
            'formWarehouseId' => 'required|integer|exists:warehouses,id',
            'formReason' => 'required|string|max:255',
            'formRemark' => 'nullable|string|max:500',
        ]);

        $data = [
            'purchase_order_id' => $validated['formPurchaseOrderId'],
            'supplier_id' => $validated['formSupplierId'],
            'warehouse_id' => $validated['formWarehouseId'],
            'reason' => $validated['formReason'],
            'remark' => $validated['formRemark'],
            'status' => PurchaseReturn::STATUS_PENDING,
            'total_amount' => 0,
            'actual_amount' => 0,
        ];

        if ($this->editingId) {
            $item = PurchaseReturn::findOrFail($this->editingId);
            unset($data['status'], $data['total_amount'], $data['actual_amount']);
            $item->update($data);
            $this->dispatch('toast', message: '退货单已更新', type: 'success');
        } else {
            $data['return_no'] = 'PR' . date('YmdHis') . str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
            PurchaseReturn::create($data);
            $this->dispatch('toast', message: '退货单已创建', type: 'success');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteConfirm = true;
    }

    public function delete(): void
    {
        $item = PurchaseReturn::findOrFail($this->deletingId);
        $item->delete();
        $this->dispatch('toast', message: '退货单已删除', type: 'success');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterStatus = -1;
        $this->resetPage();
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
        $this->formPurchaseOrderId = 0;
        $this->formSupplierId = 0;
        $this->formWarehouseId = 0;
        $this->formReason = '';
        $this->formRemark = '';
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'return_no', 'label' => '退货单号', 'sortable' => true, 'exportable' => true],
            ['key' => 'purchase_order_id', 'label' => '采购单', 'sortable' => true, 'exportable' => true],
            ['key' => 'supplier_id', 'label' => '供应商', 'sortable' => true, 'exportable' => true],
            ['key' => 'warehouse_id', 'label' => '仓库', 'sortable' => true, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => true, 'exportable' => true],
            ['key' => 'total_amount', 'label' => '总金额', 'sortable' => true, 'exportable' => true],
            ['key' => 'reason', 'label' => '退货原因', 'sortable' => false, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return PurchaseReturn::with(['supplier', 'warehouse'])->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '采购退货_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return PurchaseReturn::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '采购单ID' => 'purchase_order_id',
            '供应商ID' => 'supplier_id',
            '仓库ID' => 'warehouse_id',
            '退货原因' => 'reason',
        ];
    }

    public function getPageIds(): array
    {
        return PurchaseReturn::orderBy('id', 'desc')->limit(20)->pluck('id')->toArray();
    }

    public function render()
    {
        $query = PurchaseReturn::with(['supplier', 'warehouse'])->orderBy('id', 'desc');

        if ($this->search) {
            $query->where('return_no', 'like', "%{$this->search}%");
        }

        if ($this->filterStatus >= 0) {
            $query->where('status', $this->filterStatus);
        }

        $items = $query->paginate(20);
        $purchaseOrders = PurchaseOrder::orderBy('id', 'desc')->limit(50)->get();
        $suppliers = Supplier::enabled()->orderBy('name')->get();
        $warehouses = Warehouse::enabled()->orderBy('name')->get();
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();

        return view('livewire.purchase.purchase-return-list', compact('items', 'purchaseOrders', 'suppliers', 'warehouses', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('采购退货');
    }
}
