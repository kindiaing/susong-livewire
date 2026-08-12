<?php

namespace App\Livewire\Inventory;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithListCrud;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Models\DeliveryNote;
use App\Models\DeliveryNoteItem;
use App\Models\DeliveryRoute;
use App\Models\DeliveryRouteStop;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PickingTask;
use App\Models\PickingTaskItem;
use App\Models\User;
use App\Models\Warehouse;
use Livewire\Component;
use Livewire\WithPagination;

class PickingTaskList extends Component
{
    use WithPagination;
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;
    use WithToast;
    use WithListCrud;

    protected string $modelClass = PickingTask::class;

    public string $search = '';
    public int $filterStatus = 0;
    public int $filterRouteId = 0;
    public string $filterDeliveryDate = '';

    // 编辑弹窗
    public int $formWarehouseId = 0;
    public int $formPickerId = 0;
    public string $formRemark = '';

    // 生成拣货总单弹窗
    public bool $showGenerateModal = false;
    public int $genRouteId = 0;
    public string $genDeliveryDate = '';
    public string $genWarehouseName = '';
    public array $genSelectedOrderIds = [];
    public array $pendingOrders = [];

    // 选项数据
    public array $routeOptions = [];
    public array $warehouseOptions = [];
    public array $pickerOptions = [];

    public static array $statusMap = [
        1 => '待分配', 2 => '拣货中', 3 => '已完成',
    ];

    public static array $statusColorMap = [
        1 => 'orange', 2 => 'blue', 3 => 'green',
    ];

    public function mount(): void
    {
        $this->initColumnVisibility();

        $this->routeOptions = DeliveryRoute::enabled()
            ->ordered()
            ->get(['id', 'name', 'code'])
            ->mapWithKeys(fn($r) => [$r->id => $r->code . ' ' . $r->name])
            ->toArray();

        $this->warehouseOptions = Warehouse::enabled()
            ->get(['id', 'name'])
            ->mapWithKeys(fn($w) => [$w->id => $w->name])
            ->toArray();

        $this->pickerOptions = User::role('picker')
            ->where('status', 1)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->mapWithKeys(fn($u) => [$u->id => $u->name])
            ->toArray();

        $this->genDeliveryDate = now()->format('Y-m-d');
    }

    // ========== 生成拣货总单弹窗 ==========

    public function openGenerateModal(): void
    {
        $this->showGenerateModal = true;
        $this->genRouteId = 0;
        $this->genDeliveryDate = now()->format('Y-m-d');
        $this->genWarehouseName = '';
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
        $this->genWarehouseName = '';
        if ($this->genRouteId > 0) {
            $route = DeliveryRoute::with('warehouse')->find($this->genRouteId);
            $this->genWarehouseName = $route?->warehouse?->name ?? '';
        }
        $this->loadPendingOrders();
        $this->genSelectedOrderIds = [];
    }

    public function updatedGenDeliveryDate(): void
    {
        $this->loadPendingOrders();
        $this->genSelectedOrderIds = [];
    }

    public function loadPendingOrders(): void
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

        if (empty($routeMerchantIds)) {
            $this->pendingOrders = [];
            return;
        }

        // 查找待拣货的订单：商家属于该线路 + 送达日期匹配 + 状态=待拣货
        $orders = Order::whereIn('merchant_id', $routeMerchantIds)
            ->where('delivery_date', $this->genDeliveryDate)
            ->where('status', Order::STATUS_PICKING_WAIT)
            ->whereNotIn('id', function ($q) {
                // 排除已被拣货任务包含且拣货任务未取消的订单
                $q->select('order_id')
                    ->from('picking_task_items')
                    ->join('picking_tasks', 'picking_task_items.picking_task_id', '=', 'picking_tasks.id')
                    ->where('picking_tasks.status', '!=', 0);
            })
            ->with(['merchant', 'items'])
            ->orderBy('merchant_id')
            ->get();

        $this->pendingOrders = $orders->map(function ($order) {
            $productSummary = $order->items->take(3)
                ->map(fn($item) => $item->product_name ?: ($item->sku?->product?->name ?? '商品'))
                ->implode('、');
            if ($order->items->count() > 3) {
                $productSummary .= ' 等' . $order->items->count() . '项';
            }

            return [
                'id' => $order->id,
                'order_no' => $order->order_no,
                'merchant_id' => $order->merchant_id,
                'merchant_name' => $order->merchant?->name ?? '-',
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

    public function generatePickingTask(): void
    {
        $this->validate([
            'genRouteId' => 'required|integer|min:1',
            'genDeliveryDate' => 'required|date',
        ]);

        if (empty($this->genSelectedOrderIds)) {
            $this->toastError('请至少选择一个待拣货订单');
            return;
        }

        $route = DeliveryRoute::findOrFail($this->genRouteId);
        $warehouseId = $route->warehouse_id ?? 0;

        if ($warehouseId <= 0) {
            $this->toastError('该线路未配置出发仓库');
            return;
        }

        // 生成拣货任务编号
        $taskNo = self::generateTaskNo($route->code, $this->genDeliveryDate);

        // 加载选中订单的 items
        $orders = Order::with(['merchant', 'items.sku.product'])
            ->whereIn('id', $this->genSelectedOrderIds)
            ->get();

        // 统计 SKU 种数和总数量
        $skuSet = [];
        $totalQuantity = 0;
        $allItems = [];

        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $skuSet[$item->sku_id] = true;
                $totalQuantity += $item->quantity;
                $allItems[] = [
                    'order' => $order,
                    'orderItem' => $item,
                ];
            }
        }

        // 创建拣货任务
        $pickingTask = PickingTask::create([
            'task_no' => $taskNo,
            'warehouse_id' => $warehouseId,
            'route_id' => $this->genRouteId,
            'delivery_date' => $this->genDeliveryDate,
            'status' => PickingTask::STATUS_PENDING,
            'total_skus' => count($skuSet),
            'total_quantity' => $totalQuantity,
        ]);

        // 创建拣货任务明细
        foreach ($allItems as $entry) {
            /** @var Order $order */
            $order = $entry['order'];
            /** @var OrderItem $orderItem */
            $orderItem = $entry['orderItem'];

            PickingTaskItem::create([
                'picking_task_id' => $pickingTask->id,
                'order_id' => $order->id,
                'order_item_id' => $orderItem->id,
                'sku_id' => $orderItem->sku_id,
                'merchant_id' => $order->merchant_id,
                'required_quantity' => $orderItem->quantity,
                'status' => PickingTaskItem::STATUS_PENDING,
            ]);
        }

        // 按商户分组生成送货单
        $merchantGroups = $orders->groupBy('merchant_id');

        // 查询当天该线路已有的最大送货单序号，避免唯一约束冲突
        $dateStr = date('Ymd', strtotime($this->genDeliveryDate));
        $notePrefix = "DN-{$route->code}-{$dateStr}-";
        $lastNote = DeliveryNote::where('note_no', 'like', $notePrefix . '%')
            ->orderBy('id', 'desc')
            ->first();
        $merchantSeq = $lastNote ? ((int) substr($lastNote->note_no, -3)) + 1 : 1;

        foreach ($merchantGroups as $merchantId => $merchantOrders) {
            $merchant = $merchantOrders->first()->merchant;
            $noteNo = self::generateNoteNo($route->code, $this->genDeliveryDate, $merchantSeq);
            $merchantSeq++;

            // 收集该商户下所有订单的商品
            $merchantOrderIds = $merchantOrders->pluck('id')->toArray();
            $merchantOrderNos = $merchantOrders->pluck('order_no')->toArray();

            // 按SKU汇总同一商户下所有订单的SKU
            $skuAggregation = [];
            $productNames = [];

            foreach ($merchantOrders as $order) {
                foreach ($order->items as $item) {
                    $skuId = $item->sku_id;
                    if (!isset($skuAggregation[$skuId])) {
                        $skuAggregation[$skuId] = [
                            'sku_id' => $skuId,
                            'sku_name' => $item->product_name ?: ($item->sku?->product?->name ?? '商品'),
                            'unit' => $item->sku?->unit ?? '',
                            'quantity' => 0,
                            'order_id' => $order->id,
                            'order_no' => $order->order_no,
                        ];
                        $productNames[] = $skuAggregation[$skuId]['sku_name'];
                    }
                    $skuAggregation[$skuId]['quantity'] += $item->quantity;
                }
            }

            $merchantTotalQuantity = array_sum(array_column($skuAggregation, 'quantity'));

            // 商品摘要
            $summarySlice = array_slice($productNames, 0, 3);
            $productSummary = implode('、', $summarySlice);
            if (count($productNames) > 3) {
                $productSummary .= ' 等' . count($productNames) . '项';
            }

            // 创建送货单
            $deliveryNote = DeliveryNote::create([
                'note_no' => $noteNo,
                'task_id' => 0,
                'merchant_id' => $merchantId,
                'merchant_name' => $merchant?->name ?? '',
                'merchant_address' => $merchantOrders->first()->delivery_address ?: ($merchant?->address ?? ''),
                'delivery_date' => $this->genDeliveryDate,
                'order_ids' => $merchantOrderIds,
                'order_nos' => $merchantOrderNos,
                'product_summary' => $productSummary,
                'total_quantity' => $merchantTotalQuantity,
                'status' => DeliveryNote::STATUS_PENDING,
            ]);

            // 创建送货单明细
            foreach ($skuAggregation as $skuData) {
                DeliveryNoteItem::create([
                    'delivery_note_id' => $deliveryNote->id,
                    'sku_id' => $skuData['sku_id'],
                    'sku_name' => $skuData['sku_name'],
                    'unit' => $skuData['unit'],
                    'quantity' => $skuData['quantity'],
                    'picked_quantity' => 0,
                    'order_id' => $skuData['order_id'],
                    'order_no' => $skuData['order_no'],
                    'status' => DeliveryNoteItem::STATUS_PENDING,
                ]);
            }
        }

        $orderCount = count($this->genSelectedOrderIds);
        $merchantCount = $merchantGroups->count();

        // 更新订单状态从待拣货(1)到拣货中(2)
        Order::whereIn('id', $this->genSelectedOrderIds)
            ->where('status', Order::STATUS_PICKING_WAIT)
            ->update(['status' => Order::STATUS_PICKING]);

        $this->showGenerateModal = false;
        $this->genSelectedOrderIds = [];
        $this->pendingOrders = [];
        $this->toastSuccess("拣货总单 {$taskNo} 已生成，含 {$orderCount} 张订单、{$merchantCount} 张送货单");
    }

    // ========== 编辑弹窗 ==========

    public function openEditModal(int $id): void
    {
        $item = PickingTask::findOrFail($id);
        $this->editingId = $id;
        $this->formWarehouseId = $item->warehouse_id ?? 0;
        $this->formPickerId = $item->picker_id ?? 0;
        $this->formRemark = $item->remark ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'formWarehouseId' => 'nullable|integer',
            'formPickerId' => 'nullable|integer',
            'formRemark' => 'nullable|string|max:500',
        ]);

        PickingTask::findOrFail($this->editingId)->update([
            'warehouse_id' => $validated['formWarehouseId'] ?: null,
            'picker_id' => $validated['formPickerId'] ?: null,
            'remark' => $validated['formRemark'],
        ]);

        $this->toastSuccess('拣货任务已更新');
        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(): void
    {
        $task = PickingTask::findOrFail($this->deletingId);

        if ($task->status !== PickingTask::STATUS_PENDING) {
            $this->toastError('仅待分配状态的拣货任务可删除');
            $this->showDeleteConfirm = false;
            $this->deletingId = null;
            return;
        }

        $task->delete();
        $this->toastSuccess('拣货任务已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->formWarehouseId = 0;
        $this->formPickerId = 0;
        $this->formRemark = '';
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterStatus = 0;
        $this->filterRouteId = 0;
        $this->filterDeliveryDate = '';
        $this->resetPage();
    }

    // ========== 列配置 ==========

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'task_no', 'label' => '任务编号', 'sortable' => true, 'exportable' => true],
            ['key' => 'route', 'label' => '配送线路', 'sortable' => false, 'exportable' => true],
            ['key' => 'warehouse', 'label' => '仓库', 'sortable' => false, 'exportable' => true],
            ['key' => 'picker', 'label' => '拣货员', 'sortable' => false, 'exportable' => true],
            ['key' => 'delivery_date', 'label' => '送达日期', 'sortable' => true, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => true, 'exportable' => true],
            ['key' => 'total_skus', 'label' => 'SKU种数', 'sortable' => true, 'exportable' => true],
            ['key' => 'total_quantity', 'label' => '总数量', 'sortable' => true, 'exportable' => true],
            ['key' => 'started_at', 'label' => '开始时间', 'sortable' => true, 'exportable' => true],
            ['key' => 'completed_at', 'label' => '完成时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getDefaultColumns(): array
    {
        return ['task_no', 'route', 'status', 'delivery_date', 'total_skus'];
    }

    public function getExportQuery()
    {
        return $this->applyFilters(PickingTask::with(['deliveryRoute', 'warehouse', 'picker']))->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '拣货任务_' . now()->format('Ymd_His');
    }

    public function getPageIds(): array
    {
        return $this->applyFilters(PickingTask::query())
            ->forPage($this->getPage(), setting('per_page', 10))
            ->pluck('id')
            ->toArray();
    }

    // ========== 编号生成 ==========

    public static function generateTaskNo(string $routeCode, string $date): string
    {
        $dateStr = date('Ymd', strtotime($date));
        $prefix = "PK-{$routeCode}-{$dateStr}-";

        $lastTask = PickingTask::where('task_no', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastTask) {
            $lastSeq = (int) substr($lastTask->task_no, -3);
            $nextSeq = $lastSeq + 1;
        } else {
            $nextSeq = 1;
        }

        return $prefix . str_pad((string) $nextSeq, 3, '0', STR_PAD_LEFT);
    }

    public static function generateNoteNo(string $routeCode, string $date, int $merchantSeq): string
    {
        $dateStr = date('Ymd', strtotime($date));

        return "DN-{$routeCode}-{$dateStr}-" . str_pad((string) $merchantSeq, 3, '0', STR_PAD_LEFT);
    }

    // ========== 查询与渲染 ==========

    private function applyFilters($query)
    {
        return $query
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('task_no', 'like', "%{$this->search}%")
                        ->orWhereHas('deliveryRoute', function ($rq) {
                            $rq->where('name', 'like', "%{$this->search}%")
                                ->orWhere('code', 'like', "%{$this->search}%");
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
        return $this->applyFilters(PickingTask::with(['deliveryRoute', 'warehouse', 'picker']))
            ->orderBy('id', 'desc')
            ->paginate(setting('per_page', 10));
    }

    public function render()
    {
        $items = $this->items();
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();

        return view('livewire.inventory.picking-task-list', compact(
            'items', 'allColumns', 'selectedCount'
        ))
            ->layout('components.app-layout')
            ->title('拣货任务');
    }
}
