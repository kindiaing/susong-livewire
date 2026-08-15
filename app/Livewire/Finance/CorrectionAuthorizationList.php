<?php

namespace App\Livewire\Finance;

use App\Models\CorrectionAuthorization;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithMoneyConversion;
use App\Livewire\Traits\WithToast;
use App\Livewire\Traits\WithListCrud;
use Livewire\Component;
use Livewire\WithPagination;

class CorrectionAuthorizationList extends Component
{
    use WithPagination;
    use WithRowSelection, WithColumnVisibility, WithExcelExport, WithExcelImport;
    use WithMoneyConversion;
    use WithToast;
    use WithListCrud;

    protected string $modelClass = CorrectionAuthorization::class;

    public string $search = '';

    // 新增表单
    public string $formType = 'other';
    public string $formReason = '';
    public string $formAmount = '';

    public static array $statusMap = [
        1 => '待审核', 2 => '已通过', 3 => '已拒绝',
    ];

    public static array $statusColorMap = [
        1 => 'yellow', 2 => 'green', 3 => 'red',
    ];

    public static array $typeMap = [
        'balance' => '余额更正', 'credit' => '信用更正', 'order' => '订单更正', 'other' => '其他',
    ];

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'type', 'label' => '更正类型', 'sortable' => true, 'exportable' => true],
            ['key' => 'reason', 'label' => '更正原因', 'sortable' => false, 'exportable' => true],
            ['key' => 'amount', 'label' => '更正金额', 'sortable' => true, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => true, 'exportable' => true],
            ['key' => 'operator', 'label' => '操作人', 'sortable' => false, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getDefaultColumns(): array
    {
        return ['type', 'reason', 'amount', 'status', 'created_at'];
    }

    public function getExportQuery()
    {
        return CorrectionAuthorization::with('operator')
            ->when($this->search, function ($q) {
                $q->where('reason', 'like', "%{$this->search}%");
            })
            ->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '授权更正_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return CorrectionAuthorization::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '更正类型' => 'type',
            '更正原因' => 'reason',
        ];
    }

    public function getPageIds(): array
    {
        return CorrectionAuthorization::orderBy('id', 'desc')->limit(20)->pluck('id')->toArray();
    }

    public function getImportMoneyFields(): array
    {
        return ['amount'];
    }

    public function save(): void
    {
        $this->validate([
            'formType' => 'required|string|in:balance,credit,order,other',
            'formReason' => 'required|string|max:255',
            'formAmount' => 'required|numeric|min:0.01',
        ]);

        CorrectionAuthorization::create([
            'type' => $this->formType,
            'reason' => $this->formReason,
            'amount' => money_to_cents($this->formAmount),
            'operator_id' => auth()->id(),
            'status' => CorrectionAuthorization::STATUS_PENDING,
        ]);

        $this->toastSuccess('更正申请已提交');
        $this->showModal = false;
        $this->resetForm();
    }

    public function approve(int $id): void
    {
        $item = CorrectionAuthorization::findOrFail($id);
        if ($item->status !== CorrectionAuthorization::STATUS_PENDING) {
            $this->toastError('仅待审核状态可审核');
            return;
        }
        $item->update([
            'status' => CorrectionAuthorization::STATUS_APPROVED,
            'authorized_at' => now(),
        ]);
        $this->toastSuccess('已通过审核');
    }

    public function reject(int $id): void
    {
        $item = CorrectionAuthorization::findOrFail($id);
        if ($item->status !== CorrectionAuthorization::STATUS_PENDING) {
            $this->toastError('仅待审核状态可操作');
            return;
        }
        $item->update(['status' => CorrectionAuthorization::STATUS_REJECTED]);
        $this->toastSuccess('已拒绝');
    }

    public function delete(): void
    {
        CorrectionAuthorization::findOrFail($this->deletingId)->delete();
        $this->toastSuccess('已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->formType = 'other';
        $this->formReason = '';
        $this->formAmount = '';
    }

    public function render()
    {
        $query = CorrectionAuthorization::with('operator')->orderBy('id', 'desc');

        if ($this->search) {
            $query->where('reason', 'like', "%{$this->search}%");
        }

        $items = $query->paginate(setting('per_page', 10));
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();
        $typeMap = self::$typeMap;
        $statusMap = self::$statusMap;
        $statusColorMap = self::$statusColorMap;

        return view('livewire.finance.correction-authorization-list', compact('items', 'allColumns', 'selectedCount', 'typeMap', 'statusMap', 'statusColorMap'))
            ->layout('components.app-layout')
            ->title('授权更正');
    }
}
