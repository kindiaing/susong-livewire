<?php

namespace App\Livewire\System;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithRowSelection;
use App\Models\OperationLog;
use Livewire\Component;
use Livewire\WithPagination;

class OperationLogs extends Component
{
    use WithPagination;
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;

    protected string $modelClass = OperationLog::class;

    public string $filterMethod = '';
    public string $filterUsername = '';
    public string $filterPath = '';
    public string $filterDateStart = '';
    public string $filterDateEnd = '';

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'user_id', 'label' => '用户ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'module', 'label' => '模块', 'sortable' => false, 'exportable' => true],
            ['key' => 'action', 'label' => '操作', 'sortable' => false, 'exportable' => true],
            ['key' => 'description', 'label' => '描述', 'sortable' => false, 'exportable' => true],
            ['key' => 'ip', 'label' => 'IP', 'sortable' => false, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return $this->buildQuery();
    }

    public function getExportFileName(): string
    {
        return '操作日志_' . now()->format('Ymd_His');
    }

    public function getPageIds(): array
    {
        return $this->buildQuery()->forPage($this->page, 20)->pluck('id')->toArray();
    }

    public function closeColumnModal(): void
    {
        $this->showColumnModal = false;
    }

    public function closeExportModal(): void
    {
        $this->showExportModal = false;
    }

    public function resetFilters(): void
    {
        $this->filterMethod = '';
        $this->filterUsername = '';
        $this->filterPath = '';
        $this->filterDateStart = '';
        $this->filterDateEnd = '';
        $this->resetPage();
        $this->clearSelection();
    }

    private function buildQuery()
    {
        $query = OperationLog::with('user')->orderBy('created_at', 'desc');

        if ($this->filterMethod) {
            $query->where('method', $this->filterMethod);
        }

        if ($this->filterUsername) {
            $query->where('username', 'like', "%{$this->filterUsername}%");
        }

        if ($this->filterPath) {
            $query->where('path', 'like', "%{$this->filterPath}%");
        }

        if ($this->filterDateStart) {
            $query->where('created_at', '>=', $this->filterDateStart . ' 00:00:00');
        }

        if ($this->filterDateEnd) {
            $query->where('created_at', '<=', $this->filterDateEnd . ' 23:59:59');
        }

        return $query;
    }

    public function render()
    {
        $logs = $this->buildQuery()->paginate(setting('per_page', 10));

        return view('livewire.system.operation-logs', [
            'logs' => $logs,
            'allColumns' => $this->getAllColumns(),
            'selectedCount' => $this->getSelectedCount(),
        ])
            ->layout('components.app-layout')
            ->title('操作日志');
    }
}
