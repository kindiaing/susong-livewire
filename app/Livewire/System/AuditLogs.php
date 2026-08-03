<?php

namespace App\Livewire\System;

use App\Models\AuditLog;
use Livewire\Component;
use Livewire\WithPagination;

class AuditLogs extends Component
{
    use WithPagination;

    public string $filterAction = '';
    public string $filterModelType = '';
    public string $filterOperator = '';
    public string $filterDateStart = '';
    public string $filterDateEnd = '';
    public int $detailId = 0;

    public function mount(): void
    {
        //
    }

    public function showDetail(int $id): void
    {
        $this->detailId = $id;
    }

    public function closeDetail(): void
    {
        $this->detailId = 0;
    }

    public function resetFilters(): void
    {
        $this->filterAction = '';
        $this->filterModelType = '';
        $this->filterOperator = '';
        $this->filterDateStart = '';
        $this->filterDateEnd = '';
        $this->resetPage();
    }

    public function render()
    {
        $query = AuditLog::with('operator')->orderBy('created_at', 'desc');

        if ($this->filterAction) {
            $query->where('action', $this->filterAction);
        }

        if ($this->filterModelType) {
            $query->where('model_type', $this->filterModelType);
        }

        if ($this->filterOperator) {
            $query->whereHas('operator', function ($q) {
                $q->where('username', 'like', "%{$this->filterOperator}%")
                  ->orWhere('name', 'like', "%{$this->filterOperator}%");
            });
        }

        if ($this->filterDateStart) {
            $query->where('created_at', '>=', $this->filterDateStart . ' 00:00:00');
        }

        if ($this->filterDateEnd) {
            $query->where('created_at', '<=', $this->filterDateEnd . ' 23:59:59');
        }

        $logs = $query->paginate(setting('per_page', 10));

        // 模型类型选项
        $modelTypes = AuditLog::select('model_type')
            ->distinct()
            ->orderBy('model_type')
            ->pluck('model_type');

        // 动作选项
        $actions = AuditLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        // 详情
        $detailLog = $this->detailId ? AuditLog::with('operator')->find($this->detailId) : null;

        return view('livewire.system.audit-logs', compact('logs', 'modelTypes', 'actions', 'detailLog'))
            ->layout('components.app-layout')
            ->title('审计日志');
    }
}
