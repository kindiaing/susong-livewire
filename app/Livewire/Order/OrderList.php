<?php

namespace App\Livewire\Order;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Livewire\Traits\WithListCrud;
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
    public int $filterStatus = 0;
    public int $filterPaymentStatus = 0;

    public int $formMerchantId = 0;
    public string $formNote = '';

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

    public static array $paymentMethodMap = [
        'wechat' => '微信支付', 'alipay' => '支付宝',
        'cash' => '现金', 'credit' => '账期',
    ];

    public static array $deliveryTypeMap = [
        'standard' => '标准配送', 'express' => '加急配送', 'self_pickup' => '自提',
    ];

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'order_no', 'label' => '订单号', 'sortable' => true, 'exportable' => true],
            ['key' => 'merchant_id', 'label' => '商家', 'sortable' => false, 'exportable' => true],
            ['key' => 'total_amount', 'label' => '总金额', 'sortable' => true, 'exportable' => true],
            ['key' => 'status', 'label' => '订单状态', 'sortable' => true, 'exportable' => true],
            ['key' => 'payment_method', 'label' => '支付方式', 'sortable' => false, 'exportable' => true],
            ['key' => 'payment_status', 'label' => '支付状态', 'sortable' => true, 'exportable' => true],
            ['key' => 'delivery_type', 'label' => '配送类型', 'sortable' => false, 'exportable' => true],
            ['key' => 'note', 'label' => '备注', 'sortable' => false, 'exportable' => false],
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
            '支付方式' => 'payment_method',
            '支付状态' => 'payment_status',
            '配送类型' => 'delivery_type',
            '备注' => 'note',
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
                'payment_method' => self::$paymentMethodMap[$row->payment_method] ?? $row->payment_method ?? '',
                'payment_status' => self::$paymentStatusMap[$row->payment_status] ?? '',
                'delivery_type' => self::$deliveryTypeMap[$row->delivery_type] ?? $row->delivery_type ?? '',
                'note' => $row->note ?? '',
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
        $this->formNote = $order->note ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        if ($this->editingId) {
            $validated = $this->validate([
                'formNote' => 'nullable|string|max:500',
            ]);
            Order::findOrFail($this->editingId)->update(['note' => $validated['formNote']]);
            $this->toastSuccess('订单已更新');
        } else {
            $validated = $this->validate([
                'formMerchantId' => 'required|integer|exists:merchants,id',
                'formNote' => 'nullable|string|max:500',
            ]);
            Order::create([
                'order_no' => 'ORD' . date('YmdHis') . str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT),
                'merchant_id' => $validated['formMerchantId'],
                'note' => $validated['formNote'],
                'status' => 1,
                'total_amount' => 0,
                'payment_status' => 1,
            ]);
            $this->toastSuccess('订单已创建');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmOrder(int $id): void
    {
        $order = Order::findOrFail($id);
        if ($order->status != 1) {
            $this->toastError('只有待拣货订单可确认');
            return;
        }
        $order->update(['status' => 2]);
        $this->toastSuccess('订单已确认');
    }

    public function cancelOrder(int $id): void
    {
        $order = Order::findOrFail($id);
        if (in_array($order->status, [4, 5, 9])) {
            $this->toastError('当前状态不可取消');
            return;
        }
        $order->update(['status' => 9]);
        $this->toastSuccess('订单已取消');
    }

    public function completeOrder(int $id): void
    {
        $order = Order::findOrFail($id);
        if ($order->status != 3) {
            $this->toastError('只有配送中订单可完成');
            return;
        }
        $order->update(['status' => 4]);
        $this->toastSuccess('订单已完成');
    }

    public function delete(): void
    {
        Order::findOrFail($this->deletingId)->delete();
        $this->toastSuccess('订单已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterStatus = 0;
        $this->filterPaymentStatus = 0;
        $this->resetPage();
    }

    public function getBatchActions(): array
    {
        return [
            ['label' => '批量删除', 'method' => 'batchDelete', 'color' => 'bg-red-600 hover:bg-red-700'],
        ];
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->formMerchantId = 0;
        $this->formNote = '';
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

        return view('livewire.order.order-list', [
            'orders' => $orders,
            'merchants' => $merchants,
            'allColumns' => $this->getAllColumns(),
            'selectedCount' => $this->getSelectedCount(),
            'batchActions' => $this->getBatchActions(),
        ])
            ->layout('components.app-layout')
            ->title('客户订单');
    }
}
