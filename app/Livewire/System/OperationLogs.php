<?php

namespace App\Livewire\System;

use App\Models\OperationLog;
use Livewire\Component;
use Livewire\WithPagination;

class OperationLogs extends Component
{
    use WithPagination;

    public string $filterMethod = '';
    public string $filterUsername = '';
    public string $filterPath = '';
    public string $filterDateStart = '';
    public string $filterDateEnd = '';

    public function mount(): void
    {
        // 默认显示今天的日志
    }

    public function resetFilters(): void
    {
        $this->filterMethod = '';
        $this->filterUsername = '';
        $this->filterPath = '';
        $this->filterDateStart = '';
        $this->filterDateEnd = '';
        $this->resetPage();
    }

    public function render()
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

        $logs = $query->paginate(20);

        return view('livewire.system.operation-logs', compact('logs'))
            ->layout('components.app-layout')
            ->title('操作日志');
    }
}
