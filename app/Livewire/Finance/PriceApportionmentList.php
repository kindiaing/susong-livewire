<?php

namespace App\Livewire\Finance;

use App\Models\PriceApportionment;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithMoneyConversion;
use App\Livewire\Traits\WithToast;
use App\Livewire\Traits\WithListCrud;
use Livewire\Component;
use Livewire\WithPagination;

class PriceApportionmentList extends Component
{
    use WithPagination;
    use WithRowSelection, WithColumnVisibility, WithExcelExport, WithExcelImport;
    use WithMoneyConversion;
    use WithToast;
    use WithListCrud;

    protected string $modelClass = PriceApportionment::class;

    public string $search = '';

    // 新增表单
    public int $formTargetType = 1;
    public int $formApportionType = 1;
    public string $formAmount = '';
    public int $formApportionMode = 1;

    public static array $approvalStatusMap = [
        1 => '待审核', 2 => '已通过', 3 => '已拒绝',
    ];

    public static array $approvalStatusColorMap = [
        1 => 'yellow', 2 => 'green', 3 => 'red',
    ];

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'apportion_type', 'label' => '均摊类型', 'sortable' => true, 'exportable' => true],
            ['key' => 'target_type', 'label' => '单据类型', 'sortable' => true, 'exportable' => true],
            ['key' => 'amount', 'label' => '均摊金额', 'sortable' => true, 'exportable' => true],
            ['key' => 'apportion_mode', 'label' => '均摊方式', 'sortable' => true, 'exportable' => true],
            ['key' => 'approval_status', 'label' => '审核状态', 'sortable' => true, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getDefaultColumns(): array
    {
        return ['apportion_type', 'amount', 'approval_status', 'created_at'];
    }

    public function getExportQuery()
    {
        return PriceApportionment::with('operator')
            ->when($this->search, function ($q) {
                $q->where('amount', 'like', "%{$this->search}%");
            })
            ->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '费用均摊_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return PriceApportionment::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '均摊类型' => 'apportion_type',
            '均摊金额(元)' => 'amount',
        ];
    }

    public function getPageIds(): array
    {
        return PriceApportionment::orderBy('id', 'desc')->limit(20)->pluck('id')->toArray();
    }

    public function getImportMoneyFields(): array
    {
        return ['amount'];
    }

    public function save(): void
    {
        $this->validate([
            'formTargetType' => 'required|integer|in:1,2',
            'formApportionType' => 'required|integer|in:1,2,3,4',
            'formAmount' => 'required|numeric|min:0.01',
            'formApportionMode' => 'required|integer|in:1,2',
        ]);

        $apportionMode = $this->formApportionMode;
        $approvalStatus = $apportionMode === PriceApportionment::MODE_AUTO
            ? PriceApportionment::APPROVAL_APPROVED
            : PriceApportionment::APPROVAL_PENDING;

        PriceApportionment::create([
            'target_type' => $this->formTargetType,
            'apportion_type' => $this->formApportionType,
            'amount' => money_to_cents($this->formAmount),
            'apportion_mode' => $apportionMode,
            'manual_amount' => $apportionMode === PriceApportionment::MODE_MANUAL ? money_to_cents($this->formAmount) : 0,
            'operator_id' => auth()->id(),
            'approval_status' => $approvalStatus,
        ]);

        $this->toastSuccess('均摊记录已创建');
        $this->showModal = false;
        $this->resetForm();
    }

    public function approve(int $id): void
    {
        $item = PriceApportionment::findOrFail($id);
        if ($item->approval_status !== PriceApportionment::APPROVAL_PENDING) {
            $this->toastError('仅待审核状态可审核');
            return;
        }
        $item->update(['approval_status' => PriceApportionment::APPROVAL_APPROVED]);
        $this->toastSuccess('已通过审核');
    }

    public function reject(int $id): void
    {
        $item = PriceApportionment::findOrFail($id);
        if ($item->approval_status !== PriceApportionment::APPROVAL_PENDING) {
            $this->toastError('仅待审核状态可操作');
            return;
        }
        $item->update(['approval_status' => PriceApportionment::APPROVAL_REJECTED]);
        $this->toastSuccess('已拒绝');
    }

    public function delete(): void
    {
        PriceApportionment::findOrFail($this->deletingId)->delete();
        $this->toastSuccess('已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->formTargetType = 1;
        $this->formApportionType = 1;
        $this->formAmount = '';
        $this->formApportionMode = 1;
    }

    public function render()
    {
        $query = PriceApportionment::with('operator')->orderBy('id', 'desc');

        if ($this->search) {
            $query->where('amount', 'like', "%{$this->search}%");
        }

        $items = $query->paginate(setting('per_page', 10));
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();
        $apportionTypeMap = PriceApportionment::apportionTypeMap();
        $targetTypeMap = PriceApportionment::targetTypeMap();
        $approvalStatusMap = self::$approvalStatusMap;
        $approvalStatusColorMap = self::$approvalStatusColorMap;
        $modeMap = [1 => '自动均摊', 2 => '手动均摊'];

        return view('livewire.finance.price-apportionment-list', compact('items', 'allColumns', 'selectedCount', 'apportionTypeMap', 'targetTypeMap', 'approvalStatusMap', 'approvalStatusColorMap', 'modeMap'))
            ->layout('components.app-layout')
            ->title('费用均摊');
    }
}
