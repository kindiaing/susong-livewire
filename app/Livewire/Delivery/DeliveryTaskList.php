<?php

namespace App\Livewire\Delivery;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Livewire\Traits\WithListCrud;
use App\Models\DeliveryTask;
use App\Models\DeliveryRoute;
use App\Models\Driver;
use App\Models\Vehicle;
use Livewire\Component;
use Livewire\WithPagination;

class DeliveryTaskList extends Component
{
    use WithPagination;
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithToast;
    use WithListCrud;

    protected string $modelClass = DeliveryTask::class;

    public string $search = '';
    public int $filterStatus = 0;
    public int $filterRouteId = 0;
    public string $filterDeliveryDate = '';

    // 表单字段
    public int $formRouteId = 0;
    public int $formDriverId = 0;
    public int $formVehicleId = 0;
    public string $formDeliveryDate = '';
    public int $formBatch = 1;
    public string $formRemark = '';

    public static array $statusMap = [
        1 => '待配送', 2 => '已分配', 3 => '配送中',
        4 => '暂停', 5 => '已完成', 6 => '已取消',
    ];

    public static array $statusColorMap = [
        1 => 'yellow', 2 => 'blue', 3 => 'orange',
        4 => 'gray', 5 => 'green', 6 => 'red',
    ];

    public static array $batchMap = [
        1 => '上午', 2 => '下午',
    ];

    // 下拉数据
    public array $routeOptions = [];
    public array $driverOptions = [];
    public array $vehicleOptions = [];

    public function mount(): void
    {
        $this->initColumnVisibility();
        $this->loadOptions();
    }

    private function loadOptions(): void
    {
        $this->routeOptions = DeliveryRoute::enabled()
            ->ordered()
            ->get(['id', 'name', 'code'])
            ->mapWithKeys(fn($r) => [$r->id => $r->code . ' ' . $r->name])
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
        $item = DeliveryTask::findOrFail($id);
        $this->editingId = $id;
        $this->formRouteId = $item->route_id;
        $this->formDriverId = $item->driver_id ?? 0;
        $this->formVehicleId = $item->vehicle_id ?? 0;
        $this->formDeliveryDate = $item->delivery_date?->format('Y-m-d') ?? '';
        $this->formBatch = $item->batch;
        $this->formRemark = $item->remark ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        if ($this->editingId) {
            $validated = $this->validate([
                'formDriverId' => 'nullable|integer',
                'formVehicleId' => 'nullable|integer',
                'formBatch' => 'required|in:1,2',
                'formRemark' => 'nullable|string|max:500',
            ]);

            DeliveryTask::findOrFail($this->editingId)->update([
                'driver_id' => $validated['formDriverId'] ?: null,
                'vehicle_id' => $validated['formVehicleId'] ?: null,
                'batch' => $validated['formBatch'],
                'remark' => $validated['formRemark'],
            ]);

            $this->toastSuccess('配送任务已更新');
        } else {
            $validated = $this->validate([
                'formRouteId' => 'required|integer|min:1',
                'formDriverId' => 'nullable|integer',
                'formVehicleId' => 'nullable|integer',
                'formDeliveryDate' => 'required|date',
                'formBatch' => 'required|in:1,2',
                'formRemark' => 'nullable|string|max:500',
            ]);

            $route = DeliveryRoute::findOrFail($validated['formRouteId']);
            $taskNo = DeliveryTask::generateTaskNo($route->code, $validated['formDeliveryDate']);

            DeliveryTask::create([
                'task_no' => $taskNo,
                'route_id' => $validated['formRouteId'],
                'delivery_date' => $validated['formDeliveryDate'],
                'generated_at' => now(),
                'driver_id' => $validated['formDriverId'] ?: null,
                'vehicle_id' => $validated['formVehicleId'] ?: null,
                'batch' => $validated['formBatch'],
                'status' => DeliveryTask::STATUS_PENDING,
                'remark' => $validated['formRemark'],
            ]);

            $this->toastSuccess('配送任务已创建');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function startDelivery(int $id): void
    {
        $task = DeliveryTask::findOrFail($id);
        if (!in_array($task->status, [DeliveryTask::STATUS_PENDING, DeliveryTask::STATUS_ASSIGNED])) {
            $this->toastError('仅待配送/已分配任务可开始');
            return;
        }
        $task->update([
            'status' => DeliveryTask::STATUS_IN_PROGRESS,
            'actual_start_time' => now(),
        ]);
        $this->toastSuccess('已开始配送');
    }

    public function completeDelivery(int $id): void
    {
        $task = DeliveryTask::findOrFail($id);
        if ($task->status !== DeliveryTask::STATUS_IN_PROGRESS) {
            $this->toastError('仅配送中任务可完成');
            return;
        }
        $task->update([
            'status' => DeliveryTask::STATUS_COMPLETED,
            'actual_complete_time' => now(),
        ]);
        $this->toastSuccess('配送已完成');
    }

    public function cancelDelivery(int $id): void
    {
        $task = DeliveryTask::findOrFail($id);
        if (!$task->canTransitionTo(DeliveryTask::STATUS_CANCELLED)) {
            $this->toastError('当前状态不允许取消');
            return;
        }
        $task->update(['status' => DeliveryTask::STATUS_CANCELLED]);
        $this->toastSuccess('配送任务已取消');
    }

    public function delete(): void
    {
        DeliveryTask::findOrFail($this->deletingId)->delete();
        $this->toastSuccess('配送任务已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterStatus = 0;
        $this->filterRouteId = 0;
        $this->filterDeliveryDate = '';
        $this->resetPage();
    }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->formRouteId = 0;
        $this->formDriverId = 0;
        $this->formVehicleId = 0;
        $this->formDeliveryDate = '';
        $this->formBatch = 1;
        $this->formRemark = '';
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'task_no', 'label' => '任务编号', 'sortable' => true, 'exportable' => true],
            ['key' => 'route', 'label' => '线路', 'sortable' => false, 'exportable' => true],
            ['key' => 'driver', 'label' => '司机', 'sortable' => false, 'exportable' => true],
            ['key' => 'vehicle', 'label' => '车辆', 'sortable' => false, 'exportable' => true],
            ['key' => 'delivery_date', 'label' => '送达日期', 'sortable' => true, 'exportable' => true],
            ['key' => 'batch', 'label' => '批次', 'sortable' => true, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => true, 'exportable' => true],
            ['key' => 'total_stops', 'label' => '总点位', 'sortable' => true, 'exportable' => true],
            ['key' => 'completed_stops', 'label' => '已完成', 'sortable' => true, 'exportable' => true],
            ['key' => 'has_urgent', 'label' => '加急', 'sortable' => false, 'exportable' => true],
            ['key' => 'has_important', 'label' => '重要', 'sortable' => false, 'exportable' => true],
            ['key' => 'actual_start_time', 'label' => '开始时间', 'sortable' => true, 'exportable' => true],
            ['key' => 'actual_complete_time', 'label' => '完成时间', 'sortable' => true, 'exportable' => true],
            ['key' => 'remark', 'label' => '备注', 'sortable' => false, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getDefaultColumns(): array
    {
        return ['task_no', 'route', 'driver', 'delivery_date', 'status', 'total_stops', 'completed_stops'];
    }

    public function getExportQuery()
    {
        return $this->applyFilters(DeliveryTask::with(['deliveryRoute', 'driver', 'vehicle']))->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '配送任务_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return DeliveryTask::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '任务编号' => 'task_no',
            '线路ID' => 'route_id',
            '送达日期' => 'delivery_date',
            '司机ID' => 'driver_id',
            '车辆ID' => 'vehicle_id',
            '批次' => 'batch',
        ];
    }

    public function getPageIds(): array
    {
        return $this->applyFilters(DeliveryTask::query())
            ->forPage($this->getPage(), setting('per_page', 10))
            ->pluck('id')
            ->toArray();
    }

    private function applyFilters($query)
    {
        return $query
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('task_no', 'like', "%{$this->search}%")
                        ->orWhereHas('deliveryRoute', function ($rq) {
                            $rq->where('name', 'like', "%{$this->search}%")
                                ->orWhere('code', 'like', "%{$this->search}%");
                        })
                        ->orWhereHas('driver', function ($dq) {
                            $dq->where('name', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->filterStatus >= 1, function ($q) {
                $q->where('status', $this->filterStatus);
            })
            ->when($this->filterRouteId > 0, function ($q) {
                $q->where('route_id', $this->filterRouteId);
            })
            ->when($this->filterDeliveryDate, function ($q) {
                $q->where('delivery_date', $this->filterDeliveryDate);
            });
    }

    private function items()
    {
        return $this->applyFilters(DeliveryTask::with(['deliveryRoute', 'driver', 'vehicle']))
            ->orderBy('id', 'desc')
            ->paginate(setting('per_page', 10));
    }

    public function render()
    {
        $items = $this->items();
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();
        $routeOptions = $this->routeOptions;
        $driverOptions = $this->driverOptions;
        $vehicleOptions = $this->vehicleOptions;
        $filterRouteId = $this->filterRouteId;

        return view('livewire.delivery.delivery-task-list', compact(
            'items', 'allColumns', 'selectedCount', 'routeOptions', 'driverOptions', 'vehicleOptions', 'filterRouteId'
        ))
            ->layout('components.app-layout')
            ->title('配送任务');
    }
}
