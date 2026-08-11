<?php

namespace App\Livewire\Delivery;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Livewire\Traits\WithListCrud;
use App\Models\DeliveryTask;
use App\Models\DeliveryTaskDetail;
use App\Models\DeliveryTaskSequence;
use App\Models\DeliveryRoute;
use App\Models\DeliveryRouteStop;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\Order;
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

    // 编辑表单字段
    public int $formDriverId = 0;
    public int $formVehicleId = 0;
    public int $formBatch = 1;
    public string $formRemark = '';

    // 生成任务弹窗
    public bool $showGenerateModal = false;
    public int $genRouteId = 0;
    public string $genDeliveryDate = '';
    public int $genDriverId = 0;
    public int $genVehicleId = 0;
    public int $genBatch = 1;
    public string $genRemark = '';
    public array $genSelectedOrderIds = [];
    public array $pendingOrders = [];

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
        $this->genDeliveryDate = now()->format('Y-m-d');
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

    // ========== 编辑弹窗 ==========

    public function openEditModal(int $id): void
    {
        $item = DeliveryTask::findOrFail($id);
        $this->editingId = $id;
        $this->formDriverId = $item->driver_id ?? 0;
        $this->formVehicleId = $item->vehicle_id ?? 0;
        $this->formBatch = $item->batch;
        $this->formRemark = $item->remark ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
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
        $this->showModal = false;
        $this->resetForm();
    }

    // ========== 生成任务弹窗 ==========

    public function openGenerateModal(): void
    {
        $this->showGenerateModal = true;
        $this->genRouteId = 0;
        $this->genDeliveryDate = now()->format('Y-m-d');
        $this->genDriverId = 0;
        $this->genVehicleId = 0;
        $this->genBatch = 1;
        $this->genRemark = '';
        $this->genSelectedOrderIds = [];
        $this->pendingOrders = [];
    }

    public function closeGenerateModal(): void
    {
        $this->showGenerateModal = false;
        $this->genSelectedOrderIds = [];
        $this->pendingOrders = [];
    }

    public function updatedGenRouteId(): void
    {
        $this->loadPendingOrders();
        $this->genSelectedOrderIds = [];

        // 自动带入线路默认司机/车辆
        if ($this->genRouteId > 0) {
            $route = DeliveryRoute::find($this->genRouteId);
            if ($route) {
                $this->genDriverId = $route->default_driver_id ?? 0;
                $this->genVehicleId = $route->default_vehicle_id ?? 0;
            }
        }
    }

    public function updatedGenDeliveryDate(): void
    {
        $this->loadPendingOrders();
        $this->genSelectedOrderIds = [];
    }

    private function loadPendingOrders(): void
    {
        if ($this->genRouteId <= 0 || empty($this->genDeliveryDate)) {
            $this->pendingOrders = [];
            return;
        }

        // 获取该线路下的商家ID
        $routeMerchantIds = DeliveryRouteStop::where('route_id', $this->genRouteId)
            ->active()
            ->pluck('merchant_id')
            ->toArray();

        // 查找待配送的订单：状态=待拣货/拣货中，送达日期匹配，商家属于该线路
        $orders = Order::whereIn('merchant_id', $routeMerchantIds)
            ->where('delivery_date', $this->genDeliveryDate)
            ->whereIn('status', [Order::STATUS_PICKING_WAIT, Order::STATUS_PICKING])
            ->whereNotIn('id', function ($q) {
                // 排除已被其他配送任务包含的订单
                $q->select('order_id')
                    ->from('delivery_task_details')
                    ->whereNotNull('order_id')
                    ->where('status', '!=', DeliveryTaskDetail::STATUS_CANCELLED);
            })
            ->with(['merchant', 'items'])
            ->orderBy('merchant_id')
            ->get();

        $this->pendingOrders = $orders->map(function ($order) {
            $productSummary = $order->items->take(3)
                ->map(fn($item) => $item->sku?->product?->name ?? '商品')
                ->implode('、');
            if ($order->items->count() > 3) {
                $productSummary .= ' 等' . $order->items->count() . '项';
            }

            return [
                'id' => $order->id,
                'order_no' => $order->order_no,
                'merchant_id' => $order->merchant_id,
                'merchant_name' => $order->merchant?->name ?? '-',
                'merchant_address' => $order->delivery_address ?: ($order->merchant?->address ?? ''),
                'order_date' => $order->order_date?->format('Y-m-d'),
                'delivery_date' => $order->delivery_date?->format('Y-m-d'),
                'product_summary' => $productSummary,
                'total_quantity' => $order->items->sum('quantity'),
                'total_amount' => $order->final_amount,
            ];
        })->values()->toArray();
    }

    public function toggleGenOrder(int $orderId): void
    {
        $idx = array_search($orderId, $this->genSelectedOrderIds);
        if ($idx !== false) {
            unset($this->genSelectedOrderIds[$idx]);
            $this->genSelectedOrderIds = array_values($this->genSelectedOrderIds);
        } else {
            $this->genSelectedOrderIds[] = $orderId;
        }
    }

    public function selectAllGenOrders(): void
    {
        $this->genSelectedOrderIds = array_column($this->pendingOrders, 'id');
    }

    public function deselectAllGenOrders(): void
    {
        $this->genSelectedOrderIds = [];
    }

    public function generateTask(): void
    {
        $validated = $this->validate([
            'genRouteId' => 'required|integer|min:1',
            'genDeliveryDate' => 'required|date',
            'genDriverId' => 'nullable|integer',
            'genVehicleId' => 'nullable|integer',
            'genBatch' => 'required|in:1,2',
            'genRemark' => 'nullable|string|max:500',
        ]);

        if (empty($this->genSelectedOrderIds)) {
            $this->toastError('请至少选择一个待配送订单');
            return;
        }

        $route = DeliveryRoute::findOrFail($validated['genRouteId']);
        $taskNo = DeliveryTask::generateTaskNo($route->code, $validated['genDeliveryDate']);

        // 创建配送任务
        $task = DeliveryTask::create([
            'task_no' => $taskNo,
            'route_id' => $validated['genRouteId'],
            'delivery_date' => $validated['genDeliveryDate'],
            'generated_at' => now(),
            'driver_id' => $validated['genDriverId'] ?: null,
            'vehicle_id' => $validated['genVehicleId'] ?: null,
            'batch' => $validated['genBatch'],
            'status' => $validated['genDriverId'] ? DeliveryTask::STATUS_ASSIGNED : DeliveryTask::STATUS_PENDING,
            'planned_start_time' => $route->departure_time
                ? $validated['genDeliveryDate'] . ' ' . $route->departure_time->format('H:i:s')
                : null,
            'remark' => $validated['genRemark'],
        ]);

        // 获取线路商家顺序（用于生成顺序表）
        $routeStops = DeliveryRouteStop::where('route_id', $task->route_id)
            ->active()
            ->ordered()
            ->get()
            ->keyBy('merchant_id');

        // 加载选中的订单
        $orders = Order::with(['merchant', 'items'])
            ->whereIn('id', $this->genSelectedOrderIds)
            ->get();

        // 按商家分组创建明细
        $merchantGroups = $orders->groupBy('merchant_id');
        $totalOrders = 0;
        $hasUrgent = 0;
        $hasImportant = 0;

        $detailIdMap = []; // merchant_id => [detail_ids]

        foreach ($merchantGroups as $merchantId => $merchantOrders) {
            $merchant = $merchantOrders->first()->merchant;
            $detailIds = [];

            foreach ($merchantOrders as $order) {
                $productSummary = $order->items->take(3)
                    ->map(fn($item) => $item->sku?->product?->name ?? '商品')
                    ->implode('、');
                if ($order->items->count() > 3) {
                    $productSummary .= ' 等' . $order->items->count() . '项';
                }

                $detail = DeliveryTaskDetail::create([
                    'task_id' => $task->id,
                    'order_id' => $order->id,
                    'merchant_id' => $merchantId,
                    'merchant_name' => $merchant?->name,
                    'merchant_address' => $order->delivery_address ?: ($merchant?->address ?? ''),
                    'order_date' => $order->order_date,
                    'delivery_date' => $order->delivery_date,
                    'product_summary' => $productSummary,
                    'total_quantity' => $order->items->sum('quantity'),
                    'total_weight' => null,
                    'source_type' => DeliveryTaskDetail::SOURCE_ORDER,
                    'source_id' => $order->id,
                    'status' => DeliveryTaskDetail::STATUS_PENDING,
                ]);

                $detailIds[] = $detail->id;
                $totalOrders++;
            }

            $detailIdMap[$merchantId] = $detailIds;
        }

        // 按 delivery_route_stops 顺序生成顺序表
        $seqNo = 1;
        foreach ($routeStops as $merchantId => $stop) {
            if (!isset($detailIdMap[$merchantId])) {
                continue; // 该商家没有待配送订单，跳过
            }

            DeliveryTaskSequence::create([
                'task_id' => $task->id,
                'task_detail_ids' => $detailIdMap[$merchantId],
                'merchant_id' => $merchantId,
                'merchant_name' => $stop->merchant?->name ?? '',
                'merchant_address' => $stop->address ?? '',
                'latitude' => $stop->latitude,
                'longitude' => $stop->longitude,
                'base_sequence_no' => $stop->sequence_no,
                'sequence_no' => $seqNo,
                'status' => DeliveryTaskSequence::STATUS_PENDING,
            ]);

            $seqNo++;
        }

        // 也为不在线路中的商家创建顺序表（排在最后）
        foreach ($detailIdMap as $merchantId => $detailIds) {
            if ($routeStops->has($merchantId)) {
                continue; // 已处理
            }
            $merchant = $orders->firstWhere('merchant_id', $merchantId)?->merchant;
            DeliveryTaskSequence::create([
                'task_id' => $task->id,
                'task_detail_ids' => $detailIds,
                'merchant_id' => $merchantId,
                'merchant_name' => $merchant?->name ?? '',
                'merchant_address' => $merchant?->address ?? '',
                'base_sequence_no' => 9999,
                'sequence_no' => $seqNo,
                'status' => DeliveryTaskSequence::STATUS_PENDING,
            ]);
            $seqNo++;
        }

        // 更新任务统计
        $task->update([
            'total_stops' => $seqNo - 1,
            'total_orders' => $totalOrders,
            'has_urgent' => $hasUrgent,
            'has_important' => $hasImportant,
        ]);

        // 更新订单状态为配送中
        Order::whereIn('id', $this->genSelectedOrderIds)
            ->where('status', Order::STATUS_PICKING)
            ->update(['status' => Order::STATUS_DELIVERING]);

        $this->showGenerateModal = false;
        $this->genSelectedOrderIds = [];
        $this->pendingOrders = [];
        $this->toastSuccess("配送任务 {$taskNo} 已生成，含 {$task->total_stops} 个点位、{$totalOrders} 张单据");
    }

    // ========== 状态操作 ==========

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
        $this->formDriverId = 0;
        $this->formVehicleId = 0;
        $this->formBatch = 1;
        $this->formRemark = '';
    }

    // ========== 列配置 ==========

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
