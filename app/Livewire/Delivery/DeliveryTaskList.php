<?php

namespace App\Livewire\Delivery;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Livewire\Traits\WithListCrud;
use App\Models\DeliveryTask;
use App\Models\Driver;
use App\Models\Order;
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

    // 表单字段
    public int $formOrderId = 0;
    public int $formDriverId = 0;
    public int $formVehicleId = 0;
    public string $formDeliveryDate = '';
    public string $formNote = '';

    public static array $statusMap = [
        1 => '待配送', 2 => '配送中', 3 => '任务完成',
    ];

    public static array $statusColorMap = [
        1 => 'yellow', 2 => 'blue', 3 => 'green',
    ];

    // 下拉数据
    public array $orderOptions = [];
    public array $driverOptions = [];
    public array $vehicleOptions = [];

    public function mount(): void
    {
        $this->initColumnVisibility();
        $this->loadOptions();
    }

    private function loadOptions(): void
    {
        $this->orderOptions = Order::orderBy('id', 'desc')
            ->limit(100)
            ->get(['id', 'order_no'])
            ->mapWithKeys(fn($o) => [$o->id => $o->order_no])
            ->toArray();

        $this->driverOptions = Driver::enabled()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->mapWithKeys(fn($d) => [$d->id => $d->name])
            ->toArray();

        $this->vehicleOptions = Vehicle::enabled()
            ->orderBy('plate_number')
            ->get(['id', 'plate_number'])
            ->mapWithKeys(fn($v) => [$v->id => $v->plate_number])
            ->toArray();
    }

    public function openEditModal(int $id): void
    {
        $item = DeliveryTask::findOrFail($id);
        $this->editingId = $id;
        $this->formOrderId = $item->order_id ?? 0;
        $this->formDriverId = $item->driver_id;
        $this->formVehicleId = $item->vehicle_id;
        $this->formDeliveryDate = $item->planned_at?->format('Y-m-d') ?? '';
        $this->formNote = $item->note ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        if ($this->editingId) {
            $validated = $this->validate([
                'formNote' => 'nullable|string|max:500',
            ]);

            DeliveryTask::findOrFail($this->editingId)->update([
                'note' => $validated['formNote'],
            ]);

            $this->toastSuccess('配送任务已更新');
        } else {
            $validated = $this->validate([
                'formOrderId' => 'required|integer|min:1',
                'formDriverId' => 'required|integer|min:1',
                'formVehicleId' => 'required|integer|min:1',
                'formDeliveryDate' => 'required|date',
            ]);

            DeliveryTask::create([
                'order_id' => $validated['formOrderId'],
                'driver_id' => $validated['formDriverId'],
                'vehicle_id' => $validated['formVehicleId'],
                'planned_at' => $validated['formDeliveryDate'],
                'status' => 1,
            ]);

            $this->toastSuccess('配送任务已创建');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function startDelivery(int $id): void
    {
        $task = DeliveryTask::findOrFail($id);
        if ($task->status !== 1) {
            $this->toastError('仅待配送任务可开始');
            return;
        }
        $task->update(['status' => 2, 'started_at' => now()]);
        $this->toastSuccess('已开始配送');
    }

    public function completeDelivery(int $id): void
    {
        $task = DeliveryTask::findOrFail($id);
        if ($task->status !== 2) {
            $this->toastError('仅配送中任务可完成');
            return;
        }
        $task->update(['status' => 3, 'completed_at' => now()]);
        $this->toastSuccess('配送已完成');
    }

    public function markAbnormal(int $id): void
    {
        $task = DeliveryTask::findOrFail($id);
        if ($task->status !== 2) {
            $this->toastError('仅配送中任务可标记异常');
            return;
        }
        $task->update(['note' => ($task->note ? $task->note . "\n" : '') . '[异常] ' . now()->format('H:i') . ' 配送异常']);
        $this->toastSuccess('已标记异常');
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
        $this->filterStatus = -1;
        $this->resetPage();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->formOrderId = 0;
        $this->formDriverId = 0;
        $this->formVehicleId = 0;
        $this->formDeliveryDate = '';
        $this->formNote = '';
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'order_no', 'label' => '订单号', 'sortable' => false, 'exportable' => true],
            ['key' => 'driver', 'label' => '司机', 'sortable' => false, 'exportable' => true],
            ['key' => 'vehicle', 'label' => '车辆', 'sortable' => false, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => true, 'exportable' => true],
            ['key' => 'delivery_date', 'label' => '配送日期', 'sortable' => true, 'exportable' => true],
            ['key' => 'started_at', 'label' => '开始时间', 'sortable' => true, 'exportable' => true],
            ['key' => 'completed_at', 'label' => '完成时间', 'sortable' => true, 'exportable' => true],
            ['key' => 'note', 'label' => '备注', 'sortable' => false, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return DeliveryTask::with(['order', 'driver', 'vehicle'])->orderBy('id', 'desc');
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
            '司机ID' => 'driver_id',
            '车辆ID' => 'vehicle_id',
            '配送日期' => 'planned_at',
        ];
    }

    public function getPageIds(): array
    {
        return $this->items()->pluck('id')->toArray();
    }

    private function items()
    {
        $query = DeliveryTask::with(['order', 'driver', 'vehicle'])
            ->orderBy('id', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('order', function ($oq) {
                    $oq->where('order_no', 'like', "%{$this->search}%");
                })->orWhereHas('driver', function ($dq) {
                    $dq->where('name', 'like', "%{$this->search}%");
                });
            });
        }

        if ($this->filterStatus >= 1) {
            $query->where('status', $this->filterStatus);
        }

        return $query->paginate(setting('per_page', 10));
    }

    public function render()
    {
        $items = $this->items();
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();

        return view('livewire.delivery.delivery-task-list', compact('items', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('配送任务');
    }
}
