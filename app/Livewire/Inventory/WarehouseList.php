<?php

namespace App\Livewire\Inventory;

use App\Models\Warehouse;
use Livewire\Component;
use Livewire\WithPagination;

class WarehouseList extends Component
{
    use WithPagination;

    public string $search = '';
    public int $filterType = -1;
    public int $filterStatus = -1;
    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    public string $formName = '';
    public int $formType = 1;
    public int $formIsColdChain = 0;
    public string $formAddress = '';
    public int $formStatus = 1;

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $item = Warehouse::findOrFail($id);
        $this->editingId = $id;
        $this->formName = $item->name;
        $this->formType = $item->type;
        $this->formIsColdChain = $item->is_cold_chain;
        $this->formAddress = $item->address ?? '';
        $this->formStatus = $item->status;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'formName' => 'required|string|max:50',
            'formType' => 'required|in:1,2',
            'formIsColdChain' => 'required|in:0,1',
            'formAddress' => 'nullable|string|max:255',
            'formStatus' => 'required|in:0,1',
        ]);

        $data = [
            'name' => $validated['formName'],
            'type' => $validated['formType'],
            'is_cold_chain' => $validated['formIsColdChain'],
            'address' => $validated['formAddress'],
            'status' => $validated['formStatus'],
        ];

        if ($this->editingId) {
            $item = Warehouse::findOrFail($this->editingId);
            $item->update($data);
            $this->dispatch('toast', message: '仓库已更新', type: 'success');
        } else {
            Warehouse::create($data);
            $this->dispatch('toast', message: '仓库已创建', type: 'success');
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
        $item = Warehouse::findOrFail($this->deletingId);
        $item->delete();
        $this->dispatch('toast', message: '仓库已删除', type: 'success');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterType = -1;
        $this->filterStatus = -1;
        $this->resetPage();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->formName = '';
        $this->formType = 1;
        $this->formIsColdChain = 0;
        $this->formAddress = '';
        $this->formStatus = 1;
    }

    public function render()
    {
        $query = Warehouse::orderBy('id', 'desc');

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
        }

        if ($this->filterType >= 0) {
            $query->where('type', $this->filterType);
        }

        if ($this->filterStatus >= 0) {
            $query->where('status', $this->filterStatus);
        }

        $items = $query->paginate(20);

        return view('livewire.inventory.warehouse-list', compact('items'))
            ->layout('components.app-layout')
            ->title('仓库管理');
    }
}
