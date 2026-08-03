<?php

namespace App\Livewire\Finance;

use App\Models\Promotion;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithToast;
use App\Livewire\Traits\WithListCrud;
use Livewire\Component;
use Livewire\WithPagination;

class PromotionActivityList extends Component
{
    use WithPagination;
    use WithRowSelection, WithColumnVisibility, WithExcelExport, WithExcelImport;
    use WithToast;
    use WithListCrud;

    protected string $modelClass = Promotion::class;

    public string $search = '';

    protected function getModalPropertyName(): string
    {
        return 'showCreateModal';
    }

    // 创建弹窗
    public bool $showCreateModal = false;
    public string $createFormName = '';
    public int $createFormType = 1;
    public string $createFormStartAt = '';
    public string $createFormEndAt = '';

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    /**
     * 打开创建弹窗
     */
    public function openCreateModal(): void
    {
        $this->resetCreateForm();
        $this->showCreateModal = true;
    }

    /**
     * 关闭创建弹窗
     */
    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->resetErrorBag();
    }

    /**
     * 创建促销活动
     */
    public function create(): void
    {
        $this->validate([
            'createFormName' => 'required|max:100',
            'createFormType' => 'required|integer|in:1,2,3,4,5,6,7,8',
            'createFormStartAt' => 'required|date',
            'createFormEndAt' => 'required|date|after:createFormStartAt',
        ], [], [
            'createFormName' => '活动名称',
            'createFormType' => '活动类型',
            'createFormStartAt' => '开始时间',
            'createFormEndAt' => '结束时间',
        ]);

        Promotion::create([
            'name' => $this->createFormName,
            'promo_type' => $this->createFormType,
            'start_at' => $this->createFormStartAt,
            'end_at' => $this->createFormEndAt,
            'status' => Promotion::STATUS_DISABLED,
            'created_by' => auth()->id(),
        ]);

        $this->closeCreateModal();
        $this->toastSuccess('促销活动已创建');
    }

    /**
     * 切换状态
     */
    public function toggleStatus(int $id): void
    {
        $promotion = Promotion::findOrFail($id);
        $promotion->status = $promotion->status === Promotion::STATUS_ENABLED
            ? Promotion::STATUS_DISABLED
            : Promotion::STATUS_ENABLED;
        $promotion->save();

        $this->toastSuccess($promotion->status ? '已启用' : '已禁用');
    }

    /**
     * 批量删除
     */
    public function batchDelete(): void
    {
        $ids = $this->getSelectedIds();
        if (empty($ids)) {
            $this->toastWarning('请先选择要删除的记录');
            return;
        }

        Promotion::whereIn('id', $ids)->delete();
        $this->clearSelection();
        $this->toastSuccess('已批量删除 ' . count($ids) . ' 条记录');
    }

    public function delete(): void
    {
        Promotion::findOrFail($this->deletingId)->delete();
        $this->toastSuccess('已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'name', 'label' => '活动名称', 'sortable' => true, 'exportable' => true],
            ['key' => 'promo_type', 'label' => '活动类型', 'sortable' => true, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => true, 'exportable' => true],
            ['key' => 'start_at', 'label' => '开始时间', 'sortable' => true, 'exportable' => true],
            ['key' => 'end_at', 'label' => '结束时间', 'sortable' => true, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getDefaultColumns(): array
    {
        return ['name', 'promo_type', 'status', 'start_at', 'end_at', 'created_at'];
    }

    public function getExportQuery()
    {
        return Promotion::orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '促销活动_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return Promotion::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '活动名称' => 'name',
            '活动类型' => 'promo_type',
        ];
    }

    public function getPageIds(): array
    {
        return Promotion::orderBy('id', 'desc')->limit(20)->pluck('id')->toArray();
    }

    public function render()
    {
        $query = Promotion::orderBy('id', 'desc');

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
        }

        $items = $query->paginate(setting('per_page', 10));
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();
        $typeMap = Promotion::typeMap();

        return view('livewire.finance.promotion-activity-list', compact('items', 'allColumns', 'selectedCount', 'typeMap'))
            ->layout('components.app-layout')
            ->title('促销活动');
    }

    protected function resetCreateForm(): void
    {
        $this->createFormName = '';
        $this->createFormType = 1;
        $this->createFormStartAt = '';
        $this->createFormEndAt = '';
        $this->resetErrorBag();
    }
}
