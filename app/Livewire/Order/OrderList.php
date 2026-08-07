<?php

namespace App\Livewire\Order;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Livewire\Traits\WithListCrud;
use App\Models\AuditLog;
use App\Models\DeliveryRoute;
use App\Models\Merchant;
use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;

class OrderList extends Component
{
    use WithPagination;
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithToast;
    use WithListCrud;

    protected string $modelClass = Order::class;

    public string $search = '';
    public int $filterStatus = -1;
    public int $filterPaymentStatus = -1;

    // 创建表单：基础字段
    public int $formMerchantId = 0;
    public string $formOrderDate = '';
    public string $formDeliveryDate = '';
    public string $formRemark = '';

    // 创建表单：配送字段
    public int $formSettlementType = 1;
    public int $formDeliveryRouteId = 0;
    public int $formBatch = 1;
    public string $formContactName = '';
    public string $formContactPhone = '';
    public string $formDeliveryAddress = '';

    public static array $statusMap = [
        1 => '待拣货', 2 => '拣货中', 3 => '配送中',
        4 => '已签收', 5 => '已锁定', 9 => '已取消',
    ];

    public static array $statusColorMap = [
        1 => 'yellow', 2 => 'blue', 3 => 'orange',
        4 => 'green', 5 => 'gray', 9 => 'red',
    ];

    public static array $paymentStatusMap = [
        1 => '未支付', 2 => '已支付', 3 => '账期',
    ];

    public static array $paymentStatusColorMap = [
        1 => 'yellow', 2 => 'green', 3 => 'blue',
    ];

    public static array $settlementTypeMap = [
        1 => '现结', 2 => '账期', 3 => '预付款',
    ];

    public static array $settlementTypeColorMap = [
        1 => 'blue', 2 => 'purple', 3 => 'green',
    ];

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function getDefaultColumns(): array
    {
        return ['order_no', 'merchant_id', 'status', 'total_amount', 'order_date'];
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'order_no', 'label' => '订单号', 'sortable' => true, 'exportable' => true],
            ['key' => 'merchant_id', 'label' => '商家', 'sortable' => false, 'exportable' => true],
            ['key' => 'total_amount', 'label' => '总金额', 'sortable' => true, 'exportable' => true],
            ['key' => 'status', 'label' => '订单状态', 'sortable' => true, 'exportable' => true],
            ['key' => 'payment_status', 'label' => '支付状态', 'sortable' => true, 'exportable' => true],
            ['key' => 'settlement_type', 'label' => '结算方式', 'sortable' => false, 'exportable' => true],
            ['key' => 'order_date', 'label' => '单据日期', 'sortable' => true, 'exportable' => true],
            ['key' => 'delivery_date', 'label' => '收货日期', 'sortable' => true, 'exportable' => true],
            ['key' => 'contact_name', 'label' => '联系人', 'sortable' => false, 'exportable' => true],
            ['key' => 'remark', 'label' => '备注', 'sortable' => false, 'exportable' => false],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return $this->buildQuery();
    }

    public function getExportFileName(): string
    {
        return '客户订单_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return Order::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '订单号' => 'order_no',
            '商家ID' => 'merchant_id',
            '总金额(分)' => 'total_amount',
            '结算方式' => 'settlement_type',
            '备注' => 'remark',
        ];
    }

    public function getImportUniqueBy(): array
    {
        return ['order_no'];
    }

    public function getExportRowCallback(): callable
    {
        return function (Order $row) {
            return [
                'id' => $row->id,
                'order_no' => $row->order_no,
                'merchant_id' => $row->merchant?->name ?? '',
                'total_amount' => money_format($row->total_amount, false),
                'status' => self::$statusMap[$row->status] ?? '',
                'payment_status' => self::$paymentStatusMap[$row->payment_status] ?? '',
                'settlement_type' => self::$settlementTypeMap[$row->settlement_type] ?? '',
                'order_date' => $row->order_date?->format('Y-m-d'),
                'delivery_date' => $row->delivery_date?->format('Y-m-d'),
                'contact_name' => $row->contact_name ?? '',
                'remark' => $row->remark ?? '',
                'created_at' => $row->created_at?->format('Y-m-d H:i:s'),
            ];
        };
    }

    public function getPageIds(): array
    {
        return $this->buildQuery()->forPage($this->page, 20)->pluck('id')->toArray();
    }

    public function openEditModal(int $id): void
    {
        $order = Order::findOrFail($id);
        $this->editingId = $id;
        $this->formRemark = $order->remark ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        if ($this->editingId) {
            $validated = $this->validate([
                'formRemark' => 'nullable|string|max:500',
            ]);
            Order::findOrFail($this->editingId)->update(['remark' => $validated['formRemark']]);
            $this->toastSuccess('订单已更新');
        } else {
            $validated = $this->validate([
                'formMerchantId' => 'required|integer|exists:merchants,id',
                'formSettlementType' => 'required|integer|in:1,2,3',
                'formDeliveryRouteId' => 'nullable|integer|exists:delivery_routes,id',
                'formBatch' => 'required|integer|in:1,2',
                'formContactName' => 'nullable|string|max:50',
                'formContactPhone' => 'nullable|string|max:20',
                'formDeliveryAddress' => 'nullable|string|max:255',
                'formOrderDate' => 'nullable|date',
                'formDeliveryDate' => 'nullable|date',
                'formRemark' => 'nullable|string|max:500',
            ]);

            $order = Order::create([
                'order_no' => Order::generateOrderNo(),
                'merchant_id' => $validated['formMerchantId'],
                'settlement_type' => $validated['formSettlementType'],
                'delivery_route_id' => $validated['formDeliveryRouteId'] ?: null,
                'batch' => $validated['formBatch'],
                'contact_name' => $validated['formContactName'] ?: null,
                'contact_phone' => $validated['formContactPhone'] ?: null,
                'delivery_address' => $validated['formDeliveryAddress'] ?: null,
                'order_date' => $validated['formOrderDate'] ?? null,
                'delivery_date' => $validated['formDeliveryDate'] ?? null,
                'remark' => $validated['formRemark'],
                'status' => Order::STATUS_PICKING_WAIT,
                'total_amount' => 0,
                'adjusted_amount' => 0,
                'final_amount' => 0,
                'payment_status' => Order::PAYMENT_UNPAID,
            ]);

            AuditLog::log(
                modelType: Order::class,
                modelId: $order->id,
                action: 'create',
                afterData: $order->toArray(),
            );

            $this->toastSuccess('订单已创建');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmOrder(int $id): void
    {
        $order = Order::findOrFail($id);
        if ($order->status != Order::STATUS_PICKING_WAIT) {
            $this->toastError('只有待拣货订单可确认');
            return;
        }
        $oldStatus = $order->status;
        $order->update(['status' => Order::STATUS_PICKING]);

        AuditLog::log(
            modelType: Order::class,
            modelId: $order->id,
            action: 'status_change',
            beforeData: ['status' => $oldStatus],
            afterData: ['status' => Order::STATUS_PICKING],
        );

        $this->toastSuccess('订单已确认');
    }

    public function cancelOrder(int $id): void
    {
        $order = Order::findOrFail($id);
        if (in_array($order->status, [Order::STATUS_SIGNED, Order::STATUS_LOCKED, Order::STATUS_CANCELLED])) {
            $this->toastError('当前状态不可取消');
            return;
        }
        $oldStatus = $order->status;
        $order->update(['status' => Order::STATUS_CANCELLED]);

        AuditLog::log(
            modelType: Order::class,
            modelId: $order->id,
            action: 'cancel',
            beforeData: ['status' => $oldStatus],
            afterData: ['status' => Order::STATUS_CANCELLED],
        );

        $this->toastSuccess('订单已取消');
    }

    public function completeOrder(int $id): void
    {
        $order = Order::findOrFail($id);
        if ($order->status != Order::STATUS_DELIVERING) {
            $this->toastError('只有配送中订单可完成');
            return;
        }
        $oldStatus = $order->status;
        $order->update(['status' => Order::STATUS_SIGNED]);

        AuditLog::log(
            modelType: Order::class,
            modelId: $order->id,
            action: 'status_change',
            beforeData: ['status' => $oldStatus],
            afterData: ['status' => Order::STATUS_SIGNED],
        );

        $this->toastSuccess('订单已完成');
    }

    public function delete(): void
    {
        $order = Order::findOrFail($this->deletingId);
        if (in_array($order->status, [Order::STATUS_SIGNED, Order::STATUS_LOCKED])) {
            $this->toastError('已签收或已锁定订单不可删除');
            $this->showDeleteConfirm = false;
            return;
        }
        $order->delete();
        $this->toastSuccess('订单已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function batchDelete(): void
    {
        $ids = $this->selectedIds;
        if (empty($ids)) {
            $this->toastError('请先选择要删除的订单');
            return;
        }
        $protected = Order::whereIn('id', $ids)
            ->whereIn('status', [Order::STATUS_SIGNED, Order::STATUS_LOCKED])
            ->count();
        if ($protected > 0) {
            $this->toastError("{$protected} 条已签收/已锁定订单不可删除，已跳过");
        }
        $deletableIds = Order::whereIn('id', $ids)
            ->whereNotIn('status', [Order::STATUS_SIGNED, Order::STATUS_LOCKED])
            ->pluck('id')
            ->toArray();
        if (!empty($deletableIds)) {
            Order::whereIn('id', $deletableIds)->delete();
            $this->toastSuccess('已删除 ' . count($deletableIds) . ' 条订单');
        }
        $this->clearSelection();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterStatus = -1;
        $this->filterPaymentStatus = -1;
        $this->resetPage();
    }

    public function getBatchActions(): array
    {
        return [
            ['label' => '批量删除', 'method' => 'batchDelete', 'color' => 'bg-red-600 hover:bg-red-700'],
        ];
    }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->formMerchantId = 0;
        $this->formOrderDate = '';
        $this->formDeliveryDate = '';
        $this->formRemark = '';
        $this->formSettlementType = 1;
        $this->formDeliveryRouteId = 0;
        $this->formBatch = 1;
        $this->formContactName = '';
        $this->formContactPhone = '';
        $this->formDeliveryAddress = '';
    }

    private function buildQuery()
    {
        $query = Order::with('merchant')->orderBy('id', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('order_no', 'like', "%{$this->search}%")
                    ->orWhereHas('merchant', function ($mq) {
                        $mq->where('name', 'like', "%{$this->search}%");
                    });
            });
        }

        if ($this->filterStatus >= 1) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterPaymentStatus >= 1) {
            $query->where('payment_status', $this->filterPaymentStatus);
        }

        return $query;
    }

    public function render()
    {
        $orders = $this->buildQuery()->paginate(setting('per_page', 10));
        $merchants = Merchant::orderBy('name')->get();
        $routes = DeliveryRoute::orderBy('name')->get();

        return view('livewire.order.order-list', [
            'orders' => $orders,
            'merchants' => $merchants,
            'routes' => $routes,
            'allColumns' => $this->getAllColumns(),
            'selectedCount' => $this->getSelectedCount(),
            'batchActions' => $this->getBatchActions(),
        ])
            ->layout('components.app-layout')
            ->title('客户订单');
    }
}
