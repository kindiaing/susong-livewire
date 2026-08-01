<?php

namespace App\Livewire\Org;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Models\Supplier;
use Livewire\Component;
use Livewire\WithPagination;

class SupplierList extends Component
{
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithPagination;
    use WithRowSelection;
    use WithToast;

    protected string $modelClass = Supplier::class;

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

    public ?int $filterStatus = null;

    public ?int $filterSettlementCycle = null;

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
            'formContactName' => 'nullable|string|max:50',
            'formContactPhone' => 'nullable|string|max:20',
            'formAddress' => 'nullable|string|max:255',
            'formBankName' => 'nullable|string|max:100',
            'formBankAccount' => 'nullable|string|max:50',
            'formSettlementCycle' => 'required|in:1,2,3',
            'formStatus' => 'required|in:0,1',
            'formRemark' => 'nullable|string|max:500',
        ]);

        $data = [
            'name' => $validated['formName'],
            'contact_name' => $validated['formContactName'],
            'contact_phone' => $validated['formContactPhone'],
            'address' => $validated['formAddress'],
            'bank_name' => $validated['formBankName'] ?: null,
            'bank_account' => $validated['formBankAccount'] ?: null,
            'settlement_cycle' => $validated['formSettlementCycle'],
            'status' => $validated['formStatus'],
            'remark' => $validated['formRemark'],
        ];

        if ($this->editingId) {
            $supplier = Supplier::findOrFail($this->editingId);
            $supplier->update($data);
            $this->toastSuccess('供应商已更新');
        } else {
            Supplier::create($data);
            $this->toastSuccess('供应商已创建');
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
        $this->toastSuccess('供应商已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function toggleStatus(int $id): void
    {
        $supplier = Supplier::findOrFail($id);

        $supplier->status = $supplier->status === Supplier::STATUS_ENABLED
            ? Supplier::STATUS_DISABLED
            : Supplier::STATUS_ENABLED;

        $supplier->save();

        $this->toastSuccess($supplier->status === Supplier::STATUS_ENABLED ? '供应商已启用' : '供应商已禁用');
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterStatus = null;
        $this->filterSettlementCycle = null;
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
            ['key' => 'contact_name', 'label' => '联系人', 'sortable' => false, 'exportable' => true, 'width' => '120px'],
            ['key' => 'contact_phone', 'label' => '联系电话', 'sortable' => false, 'exportable' => true, 'width' => '120px'],
            ['key' => 'settlement_cycle', 'label' => '结算周期', 'sortable' => false, 'exportable' => true, 'width' => '90px'],
            ['key' => 'status', 'label' => '状态', 'sortable' => false, 'exportable' => true, 'width' => '80px'],
            ['key' => 'bank_name', 'label' => '开户银行', 'sortable' => false, 'exportable' => true, 'width' => '120px'],
            ['key' => 'bank_account', 'label' => '银行账号', 'sortable' => false, 'exportable' => true, 'width' => '140px'],
            ['key' => 'address', 'label' => '地址', 'sortable' => false, 'exportable' => true, 'width' => '180px'],
            ['key' => 'remark', 'label' => '备注', 'sortable' => false, 'exportable' => true, 'width' => '180px'],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true, 'width' => '150px'],
        ];
    }

    public function getDefaultColumns(): array
    {
        return ['name', 'contact_name', 'contact_phone', 'settlement_cycle', 'status', 'bank_name', 'bank_account', 'created_at'];
    }

    public function getExportRowCallback(): callable
    {
        return function (Supplier $row) {
            return [
                'id' => $row->id,
                'name' => $row->name,
                'contact_name' => $row->contact_name ?? '',
                'contact_phone' => $row->contact_phone ?? '',
                'settlement_cycle' => Supplier::settlementCycleMap()[$row->settlement_cycle] ?? '',
                'status' => Supplier::statusMap()[$row->status] ?? '',
                'bank_name' => $row->bank_name ?? '',
                'bank_account' => $row->bank_account ?? '',
                'address' => $row->address ?? '',
                'remark' => $row->remark ?? '',
                'created_at' => $row->created_at?->format('Y-m-d H:i:s'),
            ];
        };
    }

    public function getImportUniqueBy(): array
    {
        return ['name'];
    }

    public function getImportRequiredFields(): array
    {
        return ['名称', '结算周期', '状态'];
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
            'settlement_cycle' => [
                '周结' => 1,
                '月结' => 2,
                '不定期' => 3,
                '1' => 1,
                '2' => 2,
                '3' => 3,
            ],
        ];
    }

    public function getExportQuery()
    {
        return $this->applyFilters(Supplier::query())->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '供应商_'.now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return Supplier::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '名称' => 'name',
            '联系人' => 'contact_name',
            '联系电话' => 'contact_phone',
            '地址' => 'address',
            '开户银行' => 'bank_name',
            '银行账号' => 'bank_account',
            '结算周期' => 'settlement_cycle',
            '状态' => 'status',
            '备注' => 'remark',
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
        $this->formBankName = '';
        $this->formBankAccount = '';
        $this->formSettlementCycle = 1;
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
        })->when($this->filterStatus, function ($q) {
            $q->where('status', $this->filterStatus);
        })->when($this->filterSettlementCycle, function ($q) {
            $q->where('settlement_cycle', $this->filterSettlementCycle);
        });
    }

    public function render()
    {
        $query = $this->applyFilters(Supplier::query())->orderBy('id', 'desc');

        $suppliers = $query->paginate(10);
        $allColumns = $this->getAllColumns();
        $selectedCount = count($this->selectedIds);

        return view('livewire.org.supplier-list', compact('suppliers', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('供应商管理');
    }
}
