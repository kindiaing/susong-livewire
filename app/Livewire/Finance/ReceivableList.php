<?php

namespace App\Livewire\Finance;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithMoneyConversion;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Livewire\Traits\WithListCrud;
use App\Models\AuditLog;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\Receivable;
use App\Models\ReceivablePayment;
use App\Services\NotificationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ReceivableList extends Component
{
    use WithPagination;
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithMoneyConversion;
    use WithToast;
    use WithListCrud;

    protected string $modelClass = Receivable::class;

    public string $search = '';

    // 筛选条件
    public int $filterStatus = -1;

    public int $filterSettlementType = -1;

    public string $filterDateStart = '';

    public string $filterDateEnd = '';

    // 新增应收大弹窗（订单多选 → 按商家拆分生成汇总单）
    public int $createMerchantId = 0;

    /** @var array<int, int> */
    public array $createSelectedOrderIds = [];

    public bool $showCreateModal = false;

    // 编辑弹窗（仅到期日/备注）
    public string $formDueDate = '';

    public string $formRemark = '';

    // 部分收款弹窗
    public bool $showReceiveModal = false;
    public int $receiveId = 0;
    public string $receiveAmount = '';
    public string $receiveRemark = '';

    // 详情弹窗
    public bool $showDetailModal = false;
    public ?int $detailId = null;

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'order', 'label' => '订单号', 'sortable' => false, 'exportable' => true],
            ['key' => 'merchant', 'label' => '商家', 'sortable' => false, 'exportable' => true],
            ['key' => 'original_amount', 'label' => '应收金额', 'sortable' => true, 'exportable' => true, 'type' => 'money'],
            ['key' => 'received_amount', 'label' => '已收金额', 'sortable' => true, 'exportable' => true, 'type' => 'money'],
            ['key' => 'status', 'label' => '状态', 'sortable' => false, 'exportable' => true],
            ['key' => 'remark', 'label' => '备注', 'sortable' => false, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return $this->applyFilters(Receivable::query()->with(['order', 'merchant']))
            ->orderBy('id', 'desc');
    }

    /**
     * 导出行回调：关联列转为可读文本（汇总单 order_id 为空容错）
     */
    public function getExportRowCallback(): callable
    {
        return function (Receivable $row) {
            return [
                'id' => $row->id,
                'order' => $row->order->order_no ?? '',
                'merchant' => $row->merchant->name ?? '',
                'original_amount' => $row->original_amount,
                'received_amount' => $row->received_amount,
                'status' => Receivable::statusMap()[$row->status] ?? '',
                'remark' => $row->remark ?? '',
                'created_at' => $row->created_at?->format('Y-m-d H:i:s'),
            ];
        };
    }

    public function getExportFileName(): string
    {
        return '应收账款_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return Receivable::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '订单ID' => 'order_id',
            '商家ID' => 'merchant_id',
            '金额(元)' => 'original_amount',
        ];
    }

    public function getPageIds(): array
    {
        return $this->getExportQuery()->forPage($this->getPage(), 20)->pluck('id')->toArray();
    }

    public function getImportMoneyFields(): array
    {
        return ['original_amount'];
    }

    public function save(): void
    {
        if ($this->editingId) {
            $this->saveEdit();
        } else {
            $this->saveCreate();
        }
    }

    /**
     * 编辑：仅未结算状态可改到期日/备注
     */
    protected function saveEdit(): void
    {
        $item = Receivable::findOrFail($this->editingId);
        if (! $item->canEdit()) {
            $this->toastError('当前状态不可编辑，仅未结算状态可编辑');
            $this->showModal = false;
            $this->resetForm();

            return;
        }
        $validated = $this->validate([
            'formDueDate' => 'nullable|date',
            'formRemark' => 'nullable|string|max:500',
        ]);
        $item->update([
            'due_date' => $validated['formDueDate'] ?: null,
            'remark' => $validated['formRemark'] ?: null,
        ]);
        $this->toastSuccess('应收账款已更新');
        $this->showModal = false;
        $this->resetForm();
    }

    /**
     * 创建：从已签收且未生成应收单的订单中多选，按商家拆分生成汇总单
     *
     * - 每个商家一张汇总单：order_id 为空，original/adjusted = 组内订单 adjusted_amount 之和
     * - 备注 remark 自动拼接订单明细（单号+金额）
     * - 全程事务包裹，任一商家失败整体回滚
     */
    protected function saveCreate(): void
    {
        $this->validate([
            'createMerchantId' => 'nullable|integer|min:0',
            'createSelectedOrderIds' => 'required|array|min:1',
        ]);

        $orders = Order::with('merchant')
            ->whereIn('id', $this->createSelectedOrderIds)
            ->where('status', Order::STATUS_SIGNED)
            ->whereDoesntHave('receivable')
            ->get();

        if ($orders->isEmpty()) {
            $this->toastError('所选订单不满足条件（需已签收且未生成应收单），请刷新后重试');

            return;
        }

        $createdNos = DB::transaction(function () use ($orders) {
            $created = [];

            // 按商家分组，每个商家一张汇总单
            foreach ($orders->groupBy('merchant_id') as $merchantId => $group) {
                $orderList = $group->sortBy('id')->values();

                // 汇总金额口径：各订单调整后金额（adjusted_amount）
                $total = $orderList->sum(fn (Order $o) => (int) ($o->adjusted_amount ?: $o->total_amount));

                // 备注自动拼接订单明细：单号 ¥金额，用 / 分隔
                $detail = $orderList
                    ->map(fn (Order $o) => $o->order_no.' '.money_format((int) ($o->adjusted_amount ?: $o->total_amount), false))
                    ->implode(' / ');

                $first = $orderList->first();
                $settlementType = (int) ($first->settlement_type ?: Receivable::SETTLEMENT_CASH);

                // 账期结算：到期日 = 当天 + 账期天数（系统配置 credit_period_days，默认 15）
                $dueDate = null;
                if ($settlementType === Receivable::SETTLEMENT_CREDIT) {
                    $creditDays = (int) setting('credit_period_days', 15);
                    $dueDate = now()->addDays($creditDays)->toDateString();
                }

                $receivable = Receivable::create([
                    'receivable_no' => generate_sequence_no('RC', 'receivables', 'receivable_no'),
                    'order_id' => null,
                    'merchant_id' => $merchantId,
                    'original_amount' => $total,
                    'adjusted_amount' => $total,
                    'received_amount' => 0,
                    'status' => Receivable::STATUS_UNSETTLED,
                    'settlement_type' => $settlementType,
                    'due_date' => $dueDate,
                    'approval_status' => Receivable::APPROVAL_APPROVED,
                    'remark' => $detail,
                ]);

                AuditLog::log(
                    modelType: Receivable::class,
                    modelId: $receivable->id,
                    action: 'create_summary',
                    beforeData: null,
                    afterData: [
                        'receivable_no' => $receivable->receivable_no,
                        'merchant_id' => $merchantId,
                        'order_ids' => $orderList->pluck('id')->toArray(),
                        'order_nos' => $orderList->pluck('order_no')->toArray(),
                        'original_amount' => $total,
                        'adjusted_amount' => $total,
                        'settlement_type' => $settlementType,
                    ],
                );

                $created[] = $receivable->receivable_no;
            }

            return $created;
        });

        $count = count($createdNos);
        $this->toastSuccess($count > 1
            ? '已按商家生成 '.$count.' 张汇总应收单'
            : '应收汇总单已创建：'.$createdNos[0]);

        $this->closeCreateModal();
    }

    public function openCreateModal(): void
    {
        $this->createMerchantId = 0;
        $this->createSelectedOrderIds = [];
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->createMerchantId = 0;
        $this->createSelectedOrderIds = [];
        $this->resetErrorBag();
    }

    /**
     * 更新商家筛选时重新加载候选订单，并清空已选
     */
    public function updatedCreateMerchantId(): void
    {
        $this->createSelectedOrderIds = [];
    }

    /**
     * 候选订单：已签收 + 未生成过应收单，可按商家筛选
     *
     * Livewire 计算属性（blade/组件内访问 $this->candidateOrders）
     *
     * @return array<int, array{id: int, order_no: string, merchant_id: int, merchant_name: string, adjusted_amount: int, delivery_date: ?string}>
     */
    public function getCandidateOrdersProperty(): array
    {
        $query = Order::query()
            ->with('merchant')
            ->where('status', Order::STATUS_SIGNED)
            ->whereDoesntHave('receivable')
            ->orderBy('merchant_id')
            ->orderBy('id');

        if ($this->createMerchantId > 0) {
            $query->where('merchant_id', $this->createMerchantId);
        }

        return $query->get()
            ->map(function (Order $o): array {
                $name = null;
                if ($o->merchant) {
                    $name = $o->merchant->name;
                }

                return [
                    'id' => $o->id,
                    'order_no' => $o->order_no,
                    'merchant_id' => $o->merchant_id,
                    'merchant_name' => $name ?? '-',
                    'adjusted_amount' => (int) ($o->adjusted_amount ?: $o->total_amount),
                    'delivery_date' => $o->delivery_date?->format('Y-m-d'),
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * 已选订单金额合计（厘）
     */
    public function getCreateSelectedTotalProperty(): int
    {
        /** @var array<int, array{id: int, order_no: string, merchant_id: int, merchant_name: string, adjusted_amount: int, delivery_date: ?string}> $candidates */
        $candidates = $this->getCandidateOrdersProperty();

        return collect($candidates)
            ->whereIn('id', $this->createSelectedOrderIds)
            ->sum('adjusted_amount');
    }

    public function toggleCreateOrder(int $orderId): void
    {
        $idx = array_search($orderId, $this->createSelectedOrderIds);
        if ($idx !== false) {
            unset($this->createSelectedOrderIds[$idx]);
            $this->createSelectedOrderIds = array_values($this->createSelectedOrderIds);
        } else {
            $this->createSelectedOrderIds[] = $orderId;
        }
    }

    public function selectAllCreateOrders(): void
    {
        $candidates = $this->getCandidateOrdersProperty();
        $this->createSelectedOrderIds = array_column($candidates, 'id');
    }

    public function deselectAllCreateOrders(): void
    {
        $this->createSelectedOrderIds = [];
    }

    public function openEditModal(int $id): void
    {
        $item = Receivable::findOrFail($id);
        if (! $item->canEdit()) {
            $this->toastError('当前状态不可编辑，仅未结算状态可编辑');

            return;
        }
        $this->editingId = $id;
        $this->formDueDate = $item->due_date?->format('Y-m-d') ?? '';
        $this->formRemark = $item->remark ?? '';
        $this->showModal = true;
    }

    public function openReceiveModal(int $id): void
    {
        $item = Receivable::findOrFail($id);
        if (in_array($item->status, [Receivable::STATUS_SETTLED, Receivable::STATUS_CLOSED])) {
            $this->toastError('已结清/已办结不可收款');
            return;
        }
        $this->receiveId = $id;
        $this->receiveAmount = '';
        $this->receiveRemark = '';
        $this->showReceiveModal = true;
    }

    public function submitReceive(): void
    {
        $this->validate([
            'receiveAmount' => 'required|numeric|min:0.01',
        ]);

        $item = Receivable::findOrFail($this->receiveId);
        $cents = money_to_cents($this->receiveAmount);

        if ($cents <= 0) {
            $this->toastError('收款金额必须大于0');
            return;
        }

        // 超额校验：累计已收 + 本次收款 不得超过应收余额
        $remaining = $item->adjusted_amount - $item->received_amount;
        if ($cents > $remaining) {
            $this->toastError('收款金额超出应收余额 ' . money_format($remaining) . ' 元');
            return;
        }

        $newReceived = $item->received_amount + $cents;
        $newStatus = $newReceived >= $item->adjusted_amount ? Receivable::STATUS_SETTLED : Receivable::STATUS_PARTIAL;

        $item->update([
            'received_amount' => $newReceived,
            'status' => $newStatus,
            'settled_at' => $newStatus === Receivable::STATUS_SETTLED ? now() : null,
        ]);

        // 创建收款记录
        ReceivablePayment::create([
            'receivable_id' => $item->id,
            'amount' => $cents,
            'payment_method' => ReceivablePayment::METHOD_MANUAL,
            'operator_id' => auth()->id(),
            'approval_status' => ReceivablePayment::APPROVAL_APPROVED,
            'remark' => $this->receiveRemark ?: null,
        ]);

        $this->toastSuccess($newStatus === Receivable::STATUS_SETTLED ? '已结清' : '部分收款已确认');

        // 通知当前用户：收款确认
        if ($newStatus === Receivable::STATUS_SETTLED) {
            app(NotificationService::class)->receivableCollected(
                auth()->id(),
                $item->receivable_no,
                money_format($item->adjusted_amount, false),
            );
        }

        $this->showReceiveModal = false;
        $this->receiveId = 0;
    }

    public function openDetailModal(int $id): void
    {
        $this->detailId = $id;
        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->detailId = null;
    }

    public function closeReceiveModal(): void
    {
        $this->showReceiveModal = false;
        $this->receiveId = 0;
        $this->receiveAmount = '';
        $this->receiveRemark = '';
    }

    public function confirmReceived(int $id): void
    {
        $item = Receivable::findOrFail($id);
        if ($item->status === Receivable::STATUS_SETTLED) {
            $this->toastError('该账款已结清');
            return;
        }
        $item->update([
            'received_amount' => $item->adjusted_amount,
            'status' => Receivable::STATUS_SETTLED,
            'settled_at' => now(),
        ]);
        $this->toastSuccess('已确认收款');
    }

    public function delete(): void
    {
        Receivable::findOrFail($this->deletingId)->delete();
        $this->toastSuccess('应收账款已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->formDueDate = '';
        $this->formRemark = '';
    }

    /**
     * 应用筛选条件（列表/导出共用）
     */
    /**
     * 应用筛选条件（列表/导出共用）
     *
     * @param Builder<Receivable> $query
     * @return Builder<Receivable>
     */
    private function applyFilters(Builder $query): Builder
    {
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('order', fn ($oq) => $oq->where('order_no', 'like', "%{$this->search}%"))
                    ->orWhereHas('merchant', fn ($mq) => $mq->where('name', 'like', "%{$this->search}%"));
            });
        }

        if ($this->filterStatus > 0) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterSettlementType > 0) {
            $query->where('settlement_type', $this->filterSettlementType);
        }

        // 按到期日筛选
        if ($this->filterDateStart !== '') {
            $query->whereDate('due_date', '>=', $this->filterDateStart);
        }
        if ($this->filterDateEnd !== '') {
            $query->whereDate('due_date', '<=', $this->filterDateEnd);
        }

        return $query;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterStatus = -1;
        $this->filterSettlementType = -1;
        $this->filterDateStart = '';
        $this->filterDateEnd = '';
        $this->resetPage();
        $this->clearSelection();
    }

    public function render()
    {
        $query = $this->applyFilters(Receivable::query())->with(['order', 'merchant'])->orderBy('id', 'desc');

        $items = $query->paginate(setting('per_page', 10));
        $merchants = Merchant::orderBy('name')->get();
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();
        $detailItem = $this->detailId ? Receivable::with(['order', 'merchant', 'payments'])->find($this->detailId) : null;

        return view('livewire.finance.receivable-list', compact('items', 'merchants', 'allColumns', 'selectedCount', 'detailItem'))
            ->layout('components.app-layout')
            ->title('应收账款');
    }
}
