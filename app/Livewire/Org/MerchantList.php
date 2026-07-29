<?php

namespace App\Livewire\Org;

use App\Models\DeliveryRoute;
use App\Models\Merchant;
use Livewire\Component;
use Livewire\WithPagination;

class MerchantList extends Component
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
    public int $formDeliveryRouteId = 0;
    public int $formDeliverySort = 0;
    public int $formMinOrderAmount = 0;
    public int $formSettlementType = 1;
    public int $formCreditLimit = 0;
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
        $merchant = Merchant::findOrFail($id);
        $this->editingId = $id;
        $this->formName = $merchant->name;
        $this->formContactName = $merchant->contact_name ?? '';
        $this->formContactPhone = $merchant->contact_phone ?? '';
        $this->formAddress = $merchant->address ?? '';
        $this->formDeliveryRouteId = $merchant->delivery_route_id ?? 0;
        $this->formDeliverySort = $merchant->delivery_sort ?? 0;
        $this->formMinOrderAmount = $merchant->min_order_amount ?? 0;
        $this->formSettlementType = $merchant->settlement_type;
        $this->formCreditLimit = $merchant->credit_limit ?? 0;
        $this->formStatus = $merchant->status;
        $this->formRemark = $merchant->remark ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'formName' => 'required|string|max:100',
            'formContactName' => 'required|string|max:50',
            'formContactPhone' => 'required|string|max:20',
            'formAddress' => 'required|string|max:255',
            'formDeliveryRouteId' => 'nullable|integer|exists:delivery_routes,id',
            'formDeliverySort' => 'nullable|integer|min:0',
            'formMinOrderAmount' => 'required|integer|min:0',
            'formSettlementType' => 'required|in:1,2,3',
            'formCreditLimit' => 'required|integer|min:0',
            'formStatus' => 'required|in:1,2',
            'formRemark' => 'nullable|string|max:500',
        ]);

        $data = [
            'name' => $validated['formName'],
            'contact_name' => $validated['formContactName'],
            'contact_phone' => $validated['formContactPhone'],
            'address' => $validated['formAddress'],
            'delivery_route_id' => $validated['formDeliveryRouteId'] ?: null,
            'delivery_sort' => $validated['formDeliverySort'],
            'min_order_amount' => $validated['formMinOrderAmount'],
            'settlement_type' => $validated['formSettlementType'],
            'credit_limit' => $validated['formCreditLimit'],
            'status' => $validated['formStatus'],
            'remark' => $validated['formRemark'],
        ];

        if ($this->editingId) {
            $merchant = Merchant::findOrFail($this->editingId);
            $merchant->update($data);
            $this->dispatch('toast', message: '商家已更新', type: 'success');
        } else {
            Merchant::create($data);
            $this->dispatch('toast', message: '商家已创建', type: 'success');
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
        $merchant = Merchant::findOrFail($this->deletingId);
        $merchant->delete();
        $this->dispatch('toast', message: '商家已删除', type: 'success');
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
        $this->formDeliveryRouteId = 0;
        $this->formDeliverySort = 0;
        $this->formMinOrderAmount = 0;
        $this->formSettlementType = 1;
        $this->formCreditLimit = 0;
        $this->formStatus = 1;
        $this->formRemark = '';
    }

    public function render()
    {
        $query = Merchant::with('deliveryRoute')->orderBy('id', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('contact_name', 'like', "%{$this->search}%")
                    ->orWhere('contact_phone', 'like', "%{$this->search}%");
            });
        }

        $merchants = $query->paginate(20);
        $routes = DeliveryRoute::enabled()->ordered()->get();

        return view('livewire.org.merchant-list', compact('merchants', 'routes'))
            ->layout('components.app-layout')
            ->title('商家管理');
    }
}
