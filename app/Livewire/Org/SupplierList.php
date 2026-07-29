<?php

namespace App\Livewire\Org;

use App\Models\Supplier;
use Livewire\Component;
use Livewire\WithPagination;

class SupplierList extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    public string $formName = '';
    public string $formContactName = '';
    public string $formContactPhone = '';
    public string $formAddress = '';
    public string $formBankName = '';
    public string $formBankAccount = '';
    public int $formSettlementCycle = 1;
    public int $formStatus = 1;
    public string $formRemark = '';

    public function mount(): void
    {
        //
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $supplier = Supplier::findOrFail($id);
        $this->editingId = $id;
        $this->formName = $supplier->name;
        $this->formContactName = $supplier->contact_name ?? '';
        $this->formContactPhone = $supplier->contact_phone ?? '';
        $this->formAddress = $supplier->address ?? '';
        $this->formBankName = $supplier->bank_name ?? '';
        $this->formBankAccount = $supplier->bank_account ?? '';
        $this->formSettlementCycle = $supplier->settlement_cycle;
        $this->formStatus = $supplier->status;
        $this->formRemark = $supplier->remark ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'formName' => 'required|string|max:100',
            'formContactName' => 'required|string|max:50',
            'formContactPhone' => 'required|string|max:20',
            'formAddress' => 'required|string|max:255',
            'formBankName' => 'required|string|max:100',
            'formBankAccount' => 'required|string|max:50',
            'formSettlementCycle' => 'required|in:1,2',
            'formStatus' => 'required|in:1,2',
            'formRemark' => 'nullable|string|max:500',
        ]);

        $data = [
            'name' => $validated['formName'],
            'contact_name' => $validated['formContactName'],
            'contact_phone' => $validated['formContactPhone'],
            'address' => $validated['formAddress'],
            'bank_name' => $validated['formBankName'],
            'bank_account' => $validated['formBankAccount'],
            'settlement_cycle' => $validated['formSettlementCycle'],
            'status' => $validated['formStatus'],
            'remark' => $validated['formRemark'],
        ];

        if ($this->editingId) {
            $supplier = Supplier::findOrFail($this->editingId);
            $supplier->update($data);
            $this->dispatch('toast', message: '供应商已更新', type: 'success');
        } else {
            Supplier::create($data);
            $this->dispatch('toast', message: '供应商已创建', type: 'success');
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
        $supplier = Supplier::findOrFail($this->deletingId);
        $supplier->delete();
        $this->dispatch('toast', message: '供应商已删除', type: 'success');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->formName = '';
        $this->formContactName = '';
        $this->formContactPhone = '';
        $this->formAddress = '';
        $this->formBankName = '';
        $this->formBankAccount = '';
        $this->formSettlementCycle = 1;
        $this->formStatus = 1;
        $this->formRemark = '';
    }

    public function render()
    {
        $query = Supplier::orderBy('id', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('contact_name', 'like', "%{$this->search}%")
                    ->orWhere('contact_phone', 'like', "%{$this->search}%");
            });
        }

        $suppliers = $query->paginate(20);

        return view('livewire.org.supplier-list', compact('suppliers'))
            ->layout('components.app-layout')
            ->title('供应商管理');
    }
}
