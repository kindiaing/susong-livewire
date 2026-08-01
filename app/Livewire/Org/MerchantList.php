<?php

namespace App\Livewire\Org;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithMoneyConversion;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
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
    use WithMoneyConversion;
    use WithToast;

    protected string $modelClass = Merchant::class;

    public string $search = '';
    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    public ?int $filterStatus = null;
    public ?int $filterSettlementType = null;
    public ?int $filterRouteId = null;

    public string $formName = '';
    public string $formContactName = '';
    public string $formContactPhone = '';
    public string $formAddress = '';
    public int $formDeliveryRouteId = 0;
    public int $formDeliverySort = 0;
    public string $formMinOrderAmount = '';
    public int $formSettlementType = 1;
    public string $formCreditLimit = '';
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
        $this->formMinOrderAmount = $this->centsToYuan($merchant->min_order_amount);
        $this->formSettlementType = $merchant->settlement_type;
        $this->formCreditLimit = $this->centsToYuan($merchant->credit_limit);
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
            'formMinOrderAmount' => 'required|numeric|min:0',
            'formSettlementType' => 'required|in:1,2,3',
            'formCreditLimit' => 'required|numeric|min:0',
            'formStatus' => 'required|in:0,1',
            'formRemark' => 'nullable|string|max:500',
        ]);

        $data = [
            'name' => $validated['formName'],
            'contact_name' => $validated['formContactName'],
            'contact_phone' => $validated['formContactPhone'],
            'address' => $validated['formAddress'],
            'delivery_route_id' => $validated['formDeliveryRouteId'] ?: null,
            'delivery_sort' => $validated['formDeliverySort'],
            'min_order_amount' => money_to_cents($validated['formMinOrderAmount']),
            'settlement_type' => $validated['formSettlementType'],
            'credit_limit' => money_to_cents($validated['formCreditLimit']),
            'status' => $validated['formStatus'],
            'remark' => $validated['formRemark'],
        ];

        if ($this->editingId) {
            $merchant = Merchant::findOrFail($this->editingId);
            $merchant->update($data);
            $this->toastSuccess('商家已更新');
        } else {
            Merchant::create($data);
            $this->toastSuccess('商家已创建');
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
        $this->toastSuccess('商家已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterStatus = null;
        $this->filterSettlementType = null;
        $this->filterRouteId = null;
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
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true, 'width' => '60px'],
            ['key' => 'name', 'label' => '名称', 'sortable' => true, 'exportable' => true, 'width' => '1fr'],
            ['key' => 'contact_name', 'label' => '联系人', 'sortable' => false, 'exportable' => true, 'width' => '100px'],
            ['key' => 'contact_phone', 'label' => '联系电话', 'sortable' => false, 'exportable' => true, 'width' => '120px'],
            ['key' => 'settlement_type', 'label' => '结算方式', 'sortable' => false, 'exportable' => true, 'width' => '80px'],
            ['key' => 'min_order_amount', 'label' => '起送额', 'sortable' => false, 'exportable' => true, 'type' => 'money', 'width' => '80px'],
            ['key' => 'credit_limit', 'label' => '信用额度', 'sortable' => false, 'exportable' => true, 'type' => 'money', 'width' => '100px'],
            ['key' => 'status', 'label' => '状态', 'sortable' => false, 'exportable' => true, 'width' => '80px'],
            ['key' => 'address', 'label' => '地址', 'sortable' => false, 'exportable' => true, 'width' => '180px'],
            ['key' => 'note', 'label' => '备注', 'sortable' => false, 'exportable' => true, 'width' => '180px'],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true, 'width' => '150px'],
        ];
    }

    public function getDefaultColumns(): array
    {
        return ['name', 'contact_name', 'contact_phone', 'status', 'created_at'];
    }

    public function getExportQuery()
    {
        return $this->applyFilters(Merchant::query())->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '商家_'.now()->format('Ymd_His');
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

    public function getImportUniqueBy(): array
    {
        return ['name'];
    }

    public function getImportRequiredFields(): array
    {
        return ['名称', '联系人', '联系电话', '地址', '状态'];
    }

    public function getImportValueMap(): array
    {
        return [
            'status' => [
                '启用' => 1,
                '禁用' => 0,
                '1' => 1,
                '0' => 0,
            ],
        ];
    }

    public function getPageIds(): array
    {
        return $this->getExportQuery()->forPage($this->getPage(), 10)->pluck('id')->toArray();
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
        $this->formMinOrderAmount = '';
        $this->formSettlementType = 1;
        $this->formCreditLimit = '';
        $this->formStatus = 1;
        $this->formRemark = '';
    }

    private function applyFilters($query)
    {
        return $query->when($this->search, function ($q) {
            $q->where(function ($q2) {
                $q2->where('name', 'like', "%{$this->search}%")
                    ->orWhere('contact_name', 'like', "%{$this->search}%")
                    ->orWhere('contact_phone', 'like', "%{$this->search}%");
            });
        })->when($this->filterStatus !== null, function ($q) {
            $q->where('status', $this->filterStatus);
        })->when($this->filterSettlementType !== null, function ($q) {
            $q->where('settlement_type', $this->filterSettlementType);
        })->when($this->filterRouteId !== null, function ($q) {
            $q->where('delivery_route_id', $this->filterRouteId);
        });
    }

    public function render()
    {
        $query = $this->applyFilters(Merchant::with('deliveryRoute'))->orderBy('id', 'desc');

        $merchants = $query->paginate(setting('per_page', 10));
        $routes = DeliveryRoute::enabled()->ordered()->get();
        $allColumns = $this->getAllColumns();
        $selectedCount = count($this->selectedIds);

        return view('livewire.org.merchant-list', compact('merchants', 'routes', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('商家管理');
    }
}
