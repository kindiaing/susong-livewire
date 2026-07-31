<?php

namespace App\Livewire\Org;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Models\Vehicle;
use Livewire\Component;
use Livewire\WithPagination;

class VehicleList extends Component
{
    use WithPagination;
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithToast;

    protected string $modelClass = Vehicle::class;

    public string $search = '';
    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    public string $formPlateNumber = '';
    public string $formVehicleType = '';
    public int $formIsColdChain = 0;
    public int $formStatus = 1;

    public ?int $filterStatus = null;
    public ?int $filterIsColdChain = null;

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
        $vehicle = Vehicle::findOrFail($id);
        $this->editingId = $id;
        $this->formPlateNumber = $vehicle->plate_number;
        $this->formVehicleType = $vehicle->vehicle_type ?? '';
        $this->formIsColdChain = $vehicle->is_cold_chain;
        $this->formStatus = $vehicle->status;
        $this->showModal = true;
    }

    public function save(): void
    {
        $rules = [
            'formPlateNumber' => 'required|string|max:20|unique:vehicles,plate_number',
            'formVehicleType' => 'nullable|string|max:50',
            'formIsColdChain' => 'required|in:0,1',
            'formStatus' => 'required|in:0,1',
        ];

        if ($this->editingId) {
            $rules['formPlateNumber'] = 'required|string|max:20|unique:vehicles,plate_number,' . $this->editingId;
        }

        $validated = $this->validate($rules);

        $data = [
            'plate_number' => $validated['formPlateNumber'],
            'vehicle_type' => $validated['formVehicleType'],
            'is_cold_chain' => $validated['formIsColdChain'],
            'status' => $validated['formStatus'],
        ];

        if ($this->editingId) {
            $vehicle = Vehicle::findOrFail($this->editingId);
            $vehicle->update($data);
            $this->toastSuccess('车辆已更新');
        } else {
            Vehicle::create($data);
            $this->toastSuccess('车辆已创建');
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
        $vehicle = Vehicle::findOrFail($this->deletingId);
        $vehicle->delete();
        $this->toastSuccess('车辆已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterStatus = null;
        $this->filterIsColdChain = null;
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

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->formPlateNumber = '';
        $this->formVehicleType = '';
        $this->formIsColdChain = 0;
        $this->formStatus = 1;
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true, 'width' => '60px'],
            ['key' => 'plate_number', 'label' => '车牌号', 'sortable' => true, 'exportable' => true, 'width' => '120px'],
            ['key' => 'type', 'label' => '类型', 'sortable' => false, 'exportable' => true, 'width' => '1fr'],
            ['key' => 'is_cold_chain', 'label' => '冷链', 'sortable' => false, 'exportable' => true, 'width' => '80px'],
            ['key' => 'status', 'label' => '状态', 'sortable' => false, 'exportable' => true, 'width' => '80px'],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true, 'width' => '150px'],
        ];
    }

    public function getDefaultColumns(): array
    {
        return ['plate_number', 'type', 'status', 'created_at'];
    }

    public function getExportQuery()
    {
        return $this->applyFilters(Vehicle::query())->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '车辆_'.now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return Vehicle::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '车牌号' => 'plate_number',
            '类型' => 'vehicle_type',
            '冷链' => 'is_cold_chain',
            '状态' => 'status',
        ];
    }

    public function getImportUniqueBy(): array
    {
        return ['plate_number'];
    }

    public function getImportRequiredFields(): array
    {
        return ['车牌号', '状态'];
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
            'is_cold_chain' => [
                '是' => 1,
                '否' => 0,
                '1' => 1,
                '0' => 0,
            ],
        ];
    }

    public function getPageIds(): array
    {
        return $this->getExportQuery()->forPage($this->getPage(), 10)->pluck('id')->toArray();
    }

    private function applyFilters($query)
    {
        return $query->when($this->search, function ($q) {
            $q->where(function ($q2) {
                $q2->where('plate_number', 'like', "%{$this->search}%")
                    ->orWhere('vehicle_type', 'like', "%{$this->search}%");
            });
        })->when($this->filterStatus !== null, function ($q) {
            $q->where('status', $this->filterStatus);
        })->when($this->filterIsColdChain !== null, function ($q) {
            $q->where('is_cold_chain', $this->filterIsColdChain);
        });
    }

    public function render()
    {
        $query = $this->applyFilters(Vehicle::query())->orderBy('id', 'desc');

        $vehicles = $query->paginate(10);
        $allColumns = $this->getAllColumns();
        $selectedCount = count($this->selectedIds);

        return view('livewire.org.vehicle-list', compact('vehicles', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('车辆管理');
    }
}
