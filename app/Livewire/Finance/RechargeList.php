<?php

namespace App\Livewire\Finance;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithMoneyConversion;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Livewire\Traits\WithListCrud;
use App\Models\Merchant;
use App\Models\Recharge;
use App\Services\NotificationService;
use Livewire\Component;
use Livewire\WithPagination;

class RechargeList extends Component
{
    use WithPagination;
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithMoneyConversion;
    use WithToast;
    use WithListCrud;

    protected string $modelClass = Recharge::class;

    public string $search = '';

    public int $formMerchantId = 0;
    public string $formAmount = '';
    public int $formPaymentMethod = 1;
    public string $formNote = '';

    // 详情弹窗
    public bool $showDetailModal = false;
    public ?int $detailId = null;

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
        return Recharge::with('merchant')
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('transaction_no', 'like', "%{$this->search}%")
                        ->orWhereHas('merchant', fn($mq) => $mq->where('name', 'like', "%{$this->search}%"));
                });
            })
            ->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '客户充值_' . now()->format('Ymd_His');
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
        if ($item->status !== 1) {
            $this->toastError('仅待确认状态可编辑');
            return;
        }
        $this->editingId = $id;
        $this->formMerchantId = $item->merchant_id;
        $this->formAmount = $this->centsToYuan($item->amount);
        $this->formPaymentMethod = $item->payment_method;
        $this->formNote = $item->note ?? '';
        $this->showModal = true;
    }

    public function openDetailModal(int $id): void
    {
        $this->detailId = $id;
        $this->showDetailModal = true;
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
        $item = Recharge::findOrFail($id);
        if ($item->status !== 1) {
            $this->toastError('当前状态不可审核');
            return;
        }
        $item->update(['status' => 2]);

        // 通知商家：充值到账
        if ($item->merchant_id) {
            app(NotificationService::class)->rechargeApproved(
                $item->merchant_id,
                $item->transaction_no,
                money_format($item->amount),
            );
        }

        $this->toastSuccess('充值已确认成功');
    }

    public function reject(int $id): void
    {
        $item = Recharge::findOrFail($id);
        if ($item->status !== 1) {
            $this->toastError('当前状态不可拒绝');
            return;
        }
        $item->update(['status' => 3]);
        $this->toastSuccess('充值已拒绝');
    }

    public function confirmArrival(int $id): void
    {
        $item = Recharge::findOrFail($id);
        if ($item->status !== 2) {
            $this->toastError('仅成功状态可确认到账');
            return;
        }
        $item->update(['status' => 2, 'remark' => ($item->remark ? $item->remark . "\n" : '') . '已确认到账 ' . now()->format('Y-m-d H:i')]);
        $this->toastSuccess('充值已确认到账');
    }

    public function delete(): void
    {
        Recharge::findOrFail($this->deletingId)->delete();
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
        $query = Recharge::with('merchant')->orderBy('id', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('transaction_no', 'like', "%{$this->search}%")
                    ->orWhereHas('merchant', fn($mq) => $mq->where('name', 'like', "%{$this->search}%"));
            });
        }

        $items = $query->paginate(setting('per_page', 10));
        $merchants = Merchant::orderBy('name')->get();
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();
        $statusMap = self::$statusMap;
        $statusColorMap = self::$statusColorMap;
        $paymentMethodMap = self::$paymentMethodMap;
        $detailItem = $this->detailId ? Recharge::with('merchant')->find($this->detailId) : null;

        return view('livewire.finance.recharge-list', compact('items', 'merchants', 'allColumns', 'selectedCount', 'statusMap', 'statusColorMap', 'paymentMethodMap', 'detailItem'))
            ->layout('components.app-layout')
            ->title('客户充值');
    }
}
