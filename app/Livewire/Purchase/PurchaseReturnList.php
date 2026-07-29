<?php

namespace App\Livewire\Purchase;

use App\Models\PurchaseReturn;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Warehouse;
use Livewire\Component;
use Livewire\WithPagination;

class PurchaseReturnList extends Component
{
    use WithPagination;

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

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->formPurchaseOrderId = 0;
        $this->formSupplierId = 0;
        $this->formWarehouseId = 0;
        $this->formReason = '';
        $this->formRemark = '';
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

        return view('livewire.purchase.purchase-return-list', compact('items', 'purchaseOrders', 'suppliers', 'warehouses'))
            ->layout('components.app-layout')
            ->title('采购退货');
    }
}
