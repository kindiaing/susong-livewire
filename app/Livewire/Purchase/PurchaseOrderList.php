<?php

namespace App\Livewire\Purchase;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Livewire\Component;
use Livewire\WithPagination;

class PurchaseOrderList extends Component
{
    use WithPagination;

    public string $search = '';
    public int $filterStatus = -1;
    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    public int $formSupplierId = 0;
    public string $formRemark = '';

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $order = PurchaseOrder::findOrFail($id);
        $this->editingId = $id;
        $this->formSupplierId = $order->supplier_id;
        $this->formRemark = $order->remark ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'formSupplierId' => 'required|integer|min:1',
            'formRemark' => 'nullable|string|max:500',
        ]);

        $data = [
            'supplier_id' => $validated['formSupplierId'],
            'remark' => $validated['formRemark'] ?: null,
        ];

        if ($this->editingId) {
            PurchaseOrder::findOrFail($this->editingId)->update($data);
            $this->dispatch('toast', message: '采购单已更新', type: 'success');
        } else {
            $data['order_no'] = 'PO' . date('YmdHis') . str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
            $data['status'] = PurchaseOrder::STATUS_PENDING;
            PurchaseOrder::create($data);
            $this->dispatch('toast', message: '采购单已创建', type: 'success');
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
        PurchaseOrder::findOrFail($this->deletingId)->delete();
        $this->dispatch('toast', message: '采购单已删除', type: 'success');
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
        $this->formRemark = '';
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

        $orders = $query->paginate(20);
        $suppliers = Supplier::orderBy('name')->get();

        return view('livewire.purchase.purchase-order-list', compact('orders', 'suppliers'))
            ->layout('components.app-layout')
            ->title('采购单管理');
    }
}
