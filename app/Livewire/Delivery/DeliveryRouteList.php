<?php

namespace App\Livewire\Delivery;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Livewire\Traits\WithListCrud;
use App\Models\DeliveryRoute;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\Warehouse;
use Livewire\Component;
use Livewire\WithPagination;

class DeliveryRouteList extends Component
{
    use WithPagination;
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithToast;
    use WithListCrud;

    protected string $modelClass = DeliveryRoute::class;

    public string $search = '';
    public int $filterStatus = -1;

    // 表单字段
    public string $formName = '';
    public string $formCode = '';
    public int $formWarehouseId = 0;
    public int $formDefaultDriverId = 0;
    public int $formDefaultVehicleId = 0;
    public string $formColor = '#3b82f6';
    public string $formDepartureTime = '';
    public ?int $formEstimatedDuration = null;
    public ?string $formEstimatedDistance = null;
    public string $formDescription = '';
    public int $formSort = 0;
    public int $formStatus = 1;
    public string $formRemark = '';

    // 下拉数据
    public array $warehouseOptions = [];
    public array $driverOptions = [];
    public array $vehicleOptions = [];

    public function mount(): void
    {
        $this->initColumnVisibility();
        $this->loadOptions();
    }

    private function loadOptions(): void
    {
        $this->warehouseOptions = Warehouse::enabled()
            ->get(['id', 'name'])
            ->mapWithKeys(fn($w) => [$w->id => $w->name])
            ->toArray();

        $this->driverOptions = Driver::enabled()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->mapWithKeys(fn($d) => [$d->id => $d->name])
            ->toArray();

        $this->vehicleOptions = Vehicle::active()
            ->orderBy('plate_number')
            ->get(['id', 'plate_number'])
            ->mapWithKeys(fn($v) => [$v->id => $v->plate_number])
            ->toArray();
    }

    public function openEditModal(int $id): void
    {
        $route = DeliveryRoute::findOrFail($id);
        $this->editingId = $id;
        $this->formName = $route->name;
        $this->formCode = $route->code ?? '';
        $this->formWarehouseId = $route->warehouse_id ?? 0;
        $this->formDefaultDriverId = $route->default_driver_id ?? 0;
        $this->formDefaultVehicleId = $route->default_vehicle_id ?? 0;
        $this->formColor = $route->color ?: '#3b82f6';
        $this->formDepartureTime = $route->departure_time ? $route->departure_time->format('H:i') : '';
        $this->formEstimatedDuration = $route->estimated_duration;
        $this->formEstimatedDistance = $route->estimated_distance;
        $this->formDescription = $route->description ?? '';
        $this->formSort = $route->sort ?? 0;
        $this->formStatus = $route->status;
        $this->formRemark = $route->remark ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'formName' => 'required|string|max:100',
            'formCode' => 'nullable|string|max:50',
            'formWarehouseId' => 'nullable|integer',
            'formDefaultDriverId' => 'nullable|integer',
            'formDefaultVehicleId' => 'nullable|integer',
            'formColor' => 'nullable|string|max:20',
            'formDepartureTime' => 'nullable|string',
            'formEstimatedDuration' => 'nullable|integer|min:0',
            'formEstimatedDistance' => 'nullable|numeric|min:0',
            'formDescription' => 'nullable|string|max:255',
            'formSort' => 'nullable|integer|min:0',
            'formStatus' => 'required|in:0,1',
            'formRemark' => 'nullable|string|max:500',
        ]);

        $data = [
            'name' => $validated['formName'],
            'code' => $validated['formCode'] ?: null,
            'warehouse_id' => $validated['formWarehouseId'] ?: null,
            'default_driver_id' => $validated['formDefaultDriverId'] ?: null,
            'default_vehicle_id' => $validated['formDefaultVehicleId'] ?: null,
            'color' => $validated['formColor'],
            'departure_time' => $validated['formDepartureTime'] ?: null,
            'estimated_duration' => $validated['formEstimatedDuration'],
            'estimated_distance' => $validated['formEstimatedDistance'],
            'description' => $validated['formDescription'],
            'sort' => $validated['formSort'],
            'status' => $validated['formStatus'],
            'remark' => $validated['formRemark'],
        ];

        if ($this->editingId) {
            $route = DeliveryRoute::findOrFail($this->editingId);
            $route->update($data);
            $this->toastSuccess('配送线路已更新');
        } else {
            DeliveryRoute::create($data);
            $this->toastSuccess('配送线路已创建');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(): void
    {
        $route = DeliveryRoute::findOrFail($this->deletingId);

        // 检查是否有关联的配送任务
        if ($route->deliveryTasks()->exists()) {
            $this->toastError('该线路下存在配送任务，无法删除');
            $this->showDeleteConfirm = false;
            $this->deletingId = null;
            return;
        }

        $route->delete();
        $this->toastSuccess('配送线路已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->formName = '';
        $this->formCode = '';
        $this->formWarehouseId = 0;
        $this->formDefaultDriverId = 0;
        $this->formDefaultVehicleId = 0;
        $this->formColor = '#3b82f6';
        $this->formDepartureTime = '';
        $this->formEstimatedDuration = null;
        $this->formEstimatedDistance = null;
        $this->formDescription = '';
        $this->formSort = 0;
        $this->formStatus = 1;
        $this->formRemark = '';
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true, 'width' => '60px'],
            ['key' => 'code', 'label' => '编码', 'sortable' => true, 'exportable' => true, 'width' => '100px'],
            ['key' => 'warehouse', 'label' => '出发仓库', 'sortable' => false, 'exportable' => false, 'width' => '120px'],
            ['key' => 'default_driver', 'label' => '默认司机', 'sortable' => false, 'exportable' => false, 'width' => '100px'],
            ['key' => 'default_vehicle', 'label' => '默认车辆', 'sortable' => false, 'exportable' => false, 'width' => '100px'],
            ['key' => 'color', 'label' => '颜色', 'sortable' => false, 'exportable' => false, 'width' => '60px'],
            ['key' => 'departure_time', 'label' => '出发时间', 'sortable' => false, 'exportable' => true, 'width' => '80px'],
            ['key' => 'stops_count', 'label' => '点位数', 'sortable' => true, 'exportable' => true, 'width' => '80px'],
            ['key' => 'status', 'label' => '状态', 'sortable' => false, 'exportable' => true, 'width' => '80px'],
            ['key' => 'sort', 'label' => '排序', 'sortable' => true, 'exportable' => true, 'width' => '60px'],
            ['key' => 'remark', 'label' => '备注', 'sortable' => false, 'exportable' => true, 'width' => '150px'],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true, 'width' => '140px'],
        ];
    }

    public function getDefaultColumns(): array
    {
        return ['code', 'default_driver', 'stops_count', 'status', 'departure_time'];
    }

    public function getExportQuery()
    {
        return DeliveryRoute::with(['warehouse', 'defaultDriver', 'defaultVehicle', 'stops'])
            ->withCount('stops')
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('name', 'like', "%{$this->search}%")
                        ->orWhere('code', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%");
                });
            })
            ->ordered();
    }

    public function getExportFileName(): string
    {
        return '配送线路_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return DeliveryRoute::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '线路名' => 'name',
            '编码' => 'code',
            '排序' => 'sort',
            '状态' => 'status',
        ];
    }

    public function getImportUniqueBy(): array
    {
        return ['name'];
    }

    public function getImportRequiredFields(): array
    {
        return ['线路名'];
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
        return $this->getExportQuery()
            ->forPage($this->getPage(), setting('per_page', 10))
            ->pluck('id')
            ->toArray();
    }

    private function items()
    {
        return DeliveryRoute::with(['defaultDriver', 'defaultVehicle', 'warehouse'])
            ->withCount('stops')
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('name', 'like', "%{$this->search}%")
                        ->orWhere('code', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterStatus >= 0, function ($q) {
                $q->where('status', $this->filterStatus);
            })
            ->ordered()
            ->paginate(setting('per_page', 10));
    }

    public function render()
    {
        $items = $this->items();
        $allColumns = $this->getAllColumns();
        $selectedCount = count($this->selectedIds);
        $warehouseOptions = $this->warehouseOptions;
        $driverOptions = $this->driverOptions;
        $vehicleOptions = $this->vehicleOptions;

        return view('livewire.delivery.delivery-route-list', compact(
            'items', 'allColumns', 'selectedCount', 'warehouseOptions', 'driverOptions', 'vehicleOptions'
        ))
            ->layout('components.app-layout')
            ->title('配送线路管理');
    }
}
