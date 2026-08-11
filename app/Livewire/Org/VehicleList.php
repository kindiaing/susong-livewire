<?php

namespace App\Livewire\Org;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Livewire\Traits\WithListCrud;
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
    use WithListCrud;

    protected string $modelClass = Vehicle::class;

    public string $search = '';

    public string $formPlateNumber = '';
    public string $formName = '';
    public string $formType = 'van';
    public string $formCapacityKg = '';
    public string $formCapacityVolume = '';
    public int $formIsColdChain = 0;
    public int $formStatus = 1;
    public string $formRemark = '';

    public ?int $filterStatus = null;
    public ?int $filterIsColdChain = null;

    public static array $typeMap = [
        'van' => '厢式货车',
        'truck' => '卡车',
        'refrigerated' => '冷藏车',
        'motorcycle' => '三轮摩托车',
    ];

    public static array $statusMap = [
        1 => '可用',
        2 => '维修中',
        3 => '报废',
    ];

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function openEditModal(int $id): void
    {
        $vehicle = Vehicle::findOrFail($id);
        $this->editingId = $id;
        $this->formPlateNumber = $vehicle->plate_number;
        $this->formName = $vehicle->name ?? '';
        $this->formType = $vehicle->type ?? 'van';
        $this->formCapacityKg = $vehicle->capacity_kg ?? '';
        $this->formCapacityVolume = $vehicle->capacity_volume ?? '';
        $this->formIsColdChain = $vehicle->is_cold_chain;
        $this->formStatus = $vehicle->status;
        $this->formRemark = $vehicle->remark ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $rules = [
            'formPlateNumber' => 'required|string|max:20|unique:vehicles,plate_number',
            'formName' => 'nullable|string|max:50',
            'formType' => 'required|in:van,truck,refrigerated,motorcycle',
            'formCapacityKg' => 'nullable|numeric',
            'formCapacityVolume' => 'nullable|numeric',
            'formIsColdChain' => 'required|in:0,1',
            'formStatus' => 'required|in:1,2,3',
            'formRemark' => 'nullable|string|max:500',
        ];

        if ($this->editingId) {
            $rules['formPlateNumber'] = 'required|string|max:20|unique:vehicles,plate_number,' . $this->editingId;
        }

        $validated = $this->validate($rules);

        $data = [
            'plate_number' => $validated['formPlateNumber'],
            'name' => $validated['formName'],
            'type' => $validated['formType'],
            'capacity_kg' => $validated['formCapacityKg'] ?: null,
            'capacity_volume' => $validated['formCapacityVolume'] ?: null,
            'is_cold_chain' => $validated['formIsColdChain'],
            'status' => $validated['formStatus'],
            'remark' => $validated['formRemark'],
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

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->formPlateNumber = '';
        $this->formName = '';
        $this->formType = 'van';
        $this->formCapacityKg = '';
        $this->formCapacityVolume = '';
        $this->formIsColdChain = 0;
        $this->formStatus = 1;
        $this->formRemark = '';
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true, 'width' => '60px'],
            ['key' => 'plate_number', 'label' => '车牌号', 'sortable' => true, 'exportable' => true, 'width' => '120px'],
            ['key' => 'name', 'label' => '车辆名称', 'sortable' => false, 'exportable' => true, 'width' => '1fr'],
            ['key' => 'type', 'label' => '类型', 'sortable' => false, 'exportable' => true, 'width' => '100px'],
            ['key' => 'capacity_kg', 'label' => '载重(kg)', 'sortable' => false, 'exportable' => true, 'width' => '90px'],
            ['key' => 'is_cold_chain', 'label' => '冷链', 'sortable' => false, 'exportable' => true, 'width' => '80px'],
            ['key' => 'status', 'label' => '状态', 'sortable' => false, 'exportable' => true, 'width' => '80px'],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true, 'width' => '150px'],
        ];
    }

    public function getDefaultColumns(): array
    {
        return ['plate_number', 'name', 'type', 'status', 'created_at'];
    }

    public function getExportQuery()
    {
        return $this->applyFilters(Vehicle::query())->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '车辆_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return Vehicle::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '车牌号' => 'plate_number',
            '车辆名称' => 'name',
            '类型' => 'type',
            '载重' => 'capacity_kg',
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
                '可用' => 1,
                '维修中' => 2,
                '报废' => 3,
                '1' => 1,
                '2' => 2,
                '3' => 3,
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
                    ->orWhere('name', 'like', "%{$this->search}%")
                    ->orWhere('type', 'like', "%{$this->search}%");
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

        $vehicles = $query->paginate(setting('per_page', 10));
        $allColumns = $this->getAllColumns();
        $selectedCount = count($this->selectedIds);
        $typeMap = self::$typeMap;
        $statusMap = self::$statusMap;

        return view('livewire.org.vehicle-list', compact('vehicles', 'allColumns', 'selectedCount', 'typeMap', 'statusMap'))
            ->layout('components.app-layout')
            ->title('车辆管理');
    }
}