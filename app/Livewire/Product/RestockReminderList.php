<?php

namespace App\Livewire\Product;

use App\Models\Merchant;
use App\Models\RestockReminder;
use App\Models\Sku;
use Livewire\Component;
use Livewire\WithPagination;

class RestockReminderList extends Component
{
    use WithPagination;

    public string $search = '';
    public int $filterStatus = -1;
    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    public int $formMerchantId = 0;
    public int $formSkuId = 0;
    public int $formThresholdQuantity = 0;
    public int $formRemindCycle = 1;
    public int $formStatus = 1;

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $item = RestockReminder::findOrFail($id);
        $this->editingId = $id;
        $this->formMerchantId = $item->merchant_id;
        $this->formSkuId = $item->sku_id;
        $this->formThresholdQuantity = $item->threshold_quantity;
        $this->formRemindCycle = $item->remind_cycle;
        $this->formStatus = $item->status;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'formMerchantId' => 'required|integer|exists:merchants,id',
            'formSkuId' => 'required|integer|exists:skus,id',
            'formThresholdQuantity' => 'required|integer|min:0',
            'formRemindCycle' => 'required|in:1,2,3',
            'formStatus' => 'required|in:0,1',
        ]);

        $data = [
            'merchant_id' => $validated['formMerchantId'],
            'sku_id' => $validated['formSkuId'],
            'threshold_quantity' => $validated['formThresholdQuantity'],
            'remind_cycle' => $validated['formRemindCycle'],
            'status' => $validated['formStatus'],
        ];

        if ($this->editingId) {
            $item = RestockReminder::findOrFail($this->editingId);
            $item->update($data);
            $this->dispatch('toast', message: '补货提醒已更新', type: 'success');
        } else {
            RestockReminder::create($data);
            $this->dispatch('toast', message: '补货提醒已创建', type: 'success');
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
        $item = RestockReminder::findOrFail($this->deletingId);
        $item->delete();
        $this->dispatch('toast', message: '补货提醒已删除', type: 'success');
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
        $this->formMerchantId = 0;
        $this->formSkuId = 0;
        $this->formThresholdQuantity = 0;
        $this->formRemindCycle = 1;
        $this->formStatus = 1;
    }

    public function render()
    {
        $query = RestockReminder::with(['merchant', 'sku'])->orderBy('id', 'desc');

        if ($this->search) {
            $query->whereHas('merchant', fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->orWhereHas('sku', fn($q) => $q->where('sku_code', 'like', "%{$this->search}%"));
        }

        if ($this->filterStatus >= 0) {
            $query->where('status', $this->filterStatus);
        }

        $items = $query->paginate(20);
        $merchants = Merchant::orderBy('name')->get();
        $skus = Sku::orderBy('sku_code')->get();

        return view('livewire.product.restock-reminder-list', compact('items', 'merchants', 'skus'))
            ->layout('components.app-layout')
            ->title('补货提醒');
    }
}
