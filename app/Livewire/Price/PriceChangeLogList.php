<?php

namespace App\Livewire\Price;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Livewire\Traits\WithListCrud;
use App\Models\PriceChangeLog;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class PriceChangeLogList extends Component
{
    use WithPagination;
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithToast;
    use WithListCrud;

    protected string $modelClass = PriceChangeLog::class;

    public string $search = '';

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function getDefaultColumns(): array
    {
        return ['source_type', 'target_type', 'original_price', 'new_price', 'amount_diff', 'operator_id', 'created_at'];
    }

    public function getExportRowCallback(): callable
    {
        return function ($row) {
            return [
                'id' => $row->id,
                'source_type' => PriceChangeLog::sourceTypeMap()[$row->source_type] ?? '',
                'target_type' => $row->target_label,
                'original_price' => money_format($row->original_price, false),
                'new_price' => money_format($row->new_price, false),
                'quantity' => $row->quantity,
                'amount_diff' => money_format($row->amount_diff, false),
                'reason' => $row->reason ?? '',
                'operator_id' => $row->operator?->name ?? '',
                'created_at' => $row->created_at?->format('Y-m-d H:i:s'),
            ];
        };
    }

    public function getImportModelClass(): string
    {
        return PriceChangeLog::class;
    }

    public function getImportColumnMap(): array
    {
        return [];
    }

    public function getImportRequiredFields(): array
    {
        return [];
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'source_type', 'label' => '来源', 'sortable' => true, 'exportable' => true],
            ['key' => 'target_type', 'label' => '作用目标', 'sortable' => true, 'exportable' => true],
            ['key' => 'original_price', 'label' => '改价前单价', 'sortable' => true, 'exportable' => true, 'type' => 'money'],
            ['key' => 'new_price', 'label' => '改价后单价', 'sortable' => true, 'exportable' => true, 'type' => 'money'],
            ['key' => 'quantity', 'label' => '数量', 'sortable' => true, 'exportable' => true],
            ['key' => 'amount_diff', 'label' => '金额差异', 'sortable' => true, 'exportable' => true, 'type' => 'money'],
            ['key' => 'reason', 'label' => '改价原因', 'sortable' => false, 'exportable' => true],
            ['key' => 'operator_id', 'label' => '操作人', 'sortable' => true, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return $this->applyFilters(PriceChangeLog::query()->with('operator'))
            ->orderBy('id', 'desc');
    }

    /**
     * 应用筛选条件（列表/导出共用）
     *
     * @param Builder<PriceChangeLog> $query
     * @return Builder<PriceChangeLog>
     */
    private function applyFilters(Builder $query): Builder
    {
        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('reason', 'like', "%{$this->search}%")
                    ->orWhereHas('operator', fn ($oq) => $oq->where('name', 'like', "%{$this->search}%"));
            });
        }

        return $query;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    public function getExportFileName(): string
    {
        return '改价记录_'.now()->format('Ymd_His');
    }

    public function getImportUniqueBy(): array
    {
        return ['id'];
    }

    public function getPageIds(): array
    {
        return $this->getExportQuery()->forPage($this->page, 20)->pluck('id')->toArray();
    }

    public function delete(): void
    {
        PriceChangeLog::findOrFail($this->deletingId)->delete();
        $this->toastSuccess('已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function render()
    {
        $items = $this->applyFilters(PriceChangeLog::query()->with('operator'))
            ->orderBy('id', 'desc')
            ->paginate(setting('per_page', 10));
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();

        return view('livewire.price.price-change-log-list', compact('items', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('改价记录');
    }
}
