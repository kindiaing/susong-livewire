<?php

namespace App\Livewire\Finance;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithConfirmAction;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithListCrud;
use App\Livewire\Traits\WithMoneyConversion;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Models\AuditLog;
use App\Models\Merchant;
use App\Models\MerchantAccount;
use App\Models\Recharge;
use App\Services\NotificationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class RechargeList extends Component
{
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithListCrud;
    use WithMoneyConversion;
    use WithPagination;
    use WithRowSelection;
    use WithToast;
    use WithConfirmAction;

    protected string $modelClass = Recharge::class;

    public string $search = '';

    // 筛选条件
    public int $filterStatus = -1;

    public int $filterPaymentMethod = -1;

    public string $filterDateStart = '';

    public string $filterDateEnd = '';

    public int $formMerchantId = 0;

    public string $formAmount = '';

    public int $formPaymentMethod = 1;

    public string $formNote = '';

    public static array $statusMap = [
        1 => '待确认', 2 => '成功', 3 => '失败',
    ];

    public static array $statusColorMap = [
        1 => 'yellow', 2 => 'green', 3 => 'red',
    ];

    public static array $paymentMethodMap = [
        1 => '微信支付', 2 => '线下转账', 3 => '后台手工',
    ];

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'merchant', 'label' => '商家', 'sortable' => false, 'exportable' => true],
            ['key' => 'transaction_no', 'label' => '交易号', 'sortable' => true, 'exportable' => true],
            ['key' => 'amount', 'label' => '金额', 'sortable' => true, 'exportable' => true],
            ['key' => 'payment_method', 'label' => '支付方式', 'sortable' => false, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => false, 'exportable' => true],
            ['key' => 'note', 'label' => '备注', 'sortable' => false, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return $this->applyFilters(Recharge::query()->with('merchant'))
            ->orderBy('id', 'desc');
    }

    /**
     * 应用筛选条件（列表/导出共用）
     */
    /**
     * 应用筛选条件（列表/导出共用）
     *
     * @param Builder<Recharge> $query
     * @return Builder<Recharge>
     */
    private function applyFilters(Builder $query): Builder
    {
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('transaction_no', 'like', "%{$this->search}%")
                    ->orWhereHas('merchant', fn ($mq) => $mq->where('name', 'like', "%{$this->search}%"));
            });
        }

        if ($this->filterStatus > 0) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterPaymentMethod > 0) {
            $query->where('payment_method', $this->filterPaymentMethod);
        }

        if ($this->filterDateStart !== '') {
            $query->whereDate('created_at', '>=', $this->filterDateStart);
        }
        if ($this->filterDateEnd !== '') {
            $query->whereDate('created_at', '<=', $this->filterDateEnd);
        }

        return $query;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterStatus = -1;
        $this->filterPaymentMethod = -1;
        $this->filterDateStart = '';
        $this->filterDateEnd = '';
        $this->resetPage();
    }

    public function getExportFileName(): string
    {
        return '客户充值_'.now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return Recharge::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '商家ID' => 'merchant_id',
            '金额(元)' => 'amount',
            '支付方式' => 'payment_method',
            '备注' => 'note',
        ];
    }

    public function getPageIds(): array
    {
        return $this->getExportQuery()->forPage($this->getPage(), 20)->pluck('id')->toArray();
    }

    public function getImportMoneyFields(): array
    {
        return ['amount'];
    }

    public function openEditModal(int $id): void
    {
        $item = Recharge::findOrFail($id);
        if (! $item->canEdit()) {
            $this->toastError('当前状态不可编辑，需管理员回退状态后才能修改');

            return;
        }
        $this->editingId = $id;
        $this->editingStatusLabel = self::$statusMap[$item->status] ?? '';
        $this->editingStatusColor = self::$statusColorMap[$item->status] ?? 'yellow';
        $this->formMerchantId = $item->merchant_id;
        $this->formAmount = $this->centsToYuan($item->amount);
        $this->formPaymentMethod = $item->payment_method;
        $this->formNote = $item->note ?? '';
        $this->showModal = true;
    }

    public function closeDetailModal(): void
    {
        // 已弃用，详情改为独立页面
    }

    public function save(): void
    {
        $this->validate([
            'formMerchantId' => 'required|integer|exists:merchants,id',
            'formAmount' => 'required|numeric|min:0.01',
            'formPaymentMethod' => 'required|integer|in:1,2,3',
        ]);

        if ($this->editingId) {
            $item = Recharge::findOrFail($this->editingId);
            if (! $item->canEdit()) {
                $this->toastError('当前状态不可编辑，需管理员回退状态后才能修改');
                $this->showModal = false;
                $this->resetForm();

                return;
            }
            $item->update([
                'merchant_id' => $this->formMerchantId,
                'amount' => money_to_cents($this->formAmount),
                'payment_method' => $this->formPaymentMethod,
                'note' => $this->formNote ?: null,
            ]);
            $this->toastSuccess('充值记录已更新');
        } else {
            Recharge::create([
                'merchant_id' => $this->formMerchantId,
                'transaction_no' => generate_sequence_no('RC', 'recharges', 'transaction_no'),
                'amount' => money_to_cents($this->formAmount),
                'payment_method' => $this->formPaymentMethod,
                'status' => 1,
                'note' => $this->formNote ?: null,
            ]);
            $this->toastSuccess('充值记录已创建');
        }
        $this->showModal = false;
        $this->resetForm();
    }

    public function approve(int $id): void
    {
        abort_unless(auth()->user()?->can('finance.recharge.approve'), 403, '无审核权限');
        $item = Recharge::findOrFail($id);
        if ($item->status !== 1) {
            $this->toastError('当前状态不可审核');

            return;
        }

        DB::transaction(function () use ($item) {
            $item->update([
                'status' => 2,
                'approval_status' => Recharge::APPROVAL_APPROVED,
            ]);

            // 入账：更新商家账户余额与总充值（无账户则创建）
            $account = MerchantAccount::firstOrCreate(
                ['merchant_id' => $item->merchant_id],
                ['balance' => 0, 'total_recharge' => 0, 'total_consumption' => 0]
            );
            $account->increment('balance', $item->amount);
            $account->increment('total_recharge', $item->amount);

            AuditLog::log(
                modelType: Recharge::class,
                modelId: $item->id,
                action: 'approve',
                beforeData: ['status' => 1],
                afterData: [
                    'status' => 2,
                    'amount' => $item->amount,
                    'balance_after' => $account->balance,
                ],
            );
        });

        // 通知商家：充值到账
        if ($item->merchant_id) {
            app(NotificationService::class)->rechargeApproved(
                $item->merchant_id,
                $item->transaction_no,
                money_format($item->amount),
            );
        }

        $this->toastSuccess('充值已确认成功，金额已入账');
    }

    public function reject(int $id): void
    {
        abort_unless(auth()->user()?->can('finance.recharge.approve'), 403, '无审核权限');
        $item = Recharge::findOrFail($id);
        if ($item->status !== 1) {
            $this->toastError('当前状态不可拒绝');

            return;
        }
        $item->update(['status' => 3, 'approval_status' => Recharge::APPROVAL_REJECTED]);
        $this->toastSuccess('充值已拒绝');
    }

    public function confirmArrival(int $id): void
    {
        $item = Recharge::findOrFail($id);
        if ($item->status !== 2) {
            $this->toastError('仅成功状态可确认到账');

            return;
        }
        $item->update(['status' => 2, 'remark' => ($item->remark ? $item->remark."\n" : '').'已确认到账 '.now()->format('Y-m-d H:i')]);
        $this->toastSuccess('充值已确认到账');
    }

    public function delete(): void
    {
        $item = Recharge::findOrFail($this->deletingId);
        if (! $item->canEdit()) {
            $this->toastError('当前状态不可删除，仅待确认状态的充值记录可删除');
            $this->showDeleteConfirm = false;
            $this->deletingId = null;

            return;
        }
        $item->delete();
        $this->toastSuccess('充值记录已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->formMerchantId = 0;
        $this->formAmount = '';
        $this->formPaymentMethod = 1;
        $this->formNote = '';
    }

    public function render()
    {
        $query = $this->applyFilters(Recharge::query())->with('merchant')->orderBy('id', 'desc');

        $items = $query->paginate(setting('per_page', 10));
        $selectedMerchant = $this->formMerchantId ? Merchant::find($this->formMerchantId) : null;
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();
        $statusMap = self::$statusMap;
        $statusColorMap = self::$statusColorMap;
        $paymentMethodMap = self::$paymentMethodMap;
        return view('livewire.finance.recharge-list', compact('items', 'selectedMerchant', 'allColumns', 'selectedCount', 'statusMap', 'statusColorMap', 'paymentMethodMap'))
            ->layout('components.app-layout')
            ->title('客户充值');
    }

    /**
     * 商家服务端搜索（searchable-select 调用）
     */
    public function searchMerchantOptions(string $keyword = ''): array
    {
        $keyword = trim($keyword);
        $query = Merchant::query();
        if ($keyword !== '') {
            $query->where('name', 'like', "%{$keyword}%");
        }

        return $query->orderBy('name')->limit(20)->get(['id', 'name'])
            ->map(fn ($m) => ['value' => $m->id, 'label' => $m->name])
            ->toArray();
    }
}
