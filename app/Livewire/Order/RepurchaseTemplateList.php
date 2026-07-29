<?php

namespace App\Livewire\Order;

use App\Models\RepurchaseTemplate;
use Livewire\Component;
use Livewire\WithPagination;

class RepurchaseTemplateList extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    public int $formMerchantId = 0;
    public string $formName = '';
    public int $formStatus = 1;

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $tpl = RepurchaseTemplate::findOrFail($id);
        $this->editingId = $id;
        $this->formMerchantId = $tpl->merchant_id;
        $this->formName = $tpl->name;
        $this->formStatus = $tpl->status;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'formMerchantId' => 'required|integer|min:1',
            'formName' => 'required|string|max:50',
            'formStatus' => 'required|in:0,1',
        ]);

        $data = [
            'merchant_id' => $validated['formMerchantId'],
            'name' => $validated['formName'],
            'status' => $validated['formStatus'],
        ];

        if ($this->editingId) {
            RepurchaseTemplate::findOrFail($this->editingId)->update($data);
            $this->dispatch('toast', message: '复购模板已更新', type: 'success');
        } else {
            RepurchaseTemplate::create($data);
            $this->dispatch('toast', message: '复购模板已创建', type: 'success');
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
        RepurchaseTemplate::findOrFail($this->deletingId)->delete();
        $this->dispatch('toast', message: '复购模板已删除', type: 'success');
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
        $this->formMerchantId = 0;
        $this->formName = '';
        $this->formStatus = 1;
    }

    public function render()
    {
        $query = RepurchaseTemplate::with('merchant')->orderBy('id', 'desc');

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
        }

        $templates = $query->paginate(20);

        return view('livewire.order.repurchase-template-list', compact('templates'))
            ->layout('components.app-layout')
            ->title('复购模板');
    }
}
