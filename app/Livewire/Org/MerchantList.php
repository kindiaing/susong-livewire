<?php

namespace App\Livewire\Org;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithRowSelection;
use App\Models\DeliveryRoute;
use App\Models\Merchant;
use Livewire\Component;
use Livewire\WithPagination;

class MerchantList extends Component
{
    use WithPagination;
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;

    protected string $modelClass = Merchant::class;

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
        $this->initColumnVisibility();
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
        $this->clearSelection();
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

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'name', 'label' => '名称', 'sortable' => true, 'exportable' => true],
            ['key' => 'contact_person', 'label' => '联系人', 'sortable' => false, 'exportable' => true],
            ['key' => 'contact_phone', 'label' => '联系电话', 'sortable' => false, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => false, 'exportable' => true],
            ['key' => 'address', 'label' => '地址', 'sortable' => false, 'exportable' => true],
            ['key' => 'note', 'label' => '备注', 'sortable' => false, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return Merchant::when($this->search, function ($q) {
            $q->where(function ($q2) {
                $q2->where('name', 'like', "%{$this->search}%")
                    ->orWhere('contact_name', 'like', "%{$this->search}%")
                    ->orWhere('contact_phone', 'like', "%{$this->search}%");
            });
        })->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '商家_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return Merchant::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '名称' => 'name',
            '联系人' => 'contact_name',
            '联系电话' => 'contact_phone',
            '地址' => 'address',
            '状态' => 'status',
            '备注' => 'remark',
        ];
    }

    public function getPageIds(): array
    {
        return $this->getExportQuery()->forPage($this->getPage(), 20)->pluck('id')->toArray();
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
        $allColumns = $this->getAllColumns();
        $selectedCount = count($this->selectedIds);

        return view('livewire.org.merchant-list', compact('merchants', 'routes', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('商家管理');
    }
}
