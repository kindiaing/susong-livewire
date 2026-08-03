<?php

namespace App\Livewire\System;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Livewire\Traits\WithListCrud;
use App\Models\LoginLog;
use Livewire\Component;
use Livewire\WithPagination;

class LoginLogList extends Component
{
    use WithPagination;
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithToast;
    use WithListCrud;

    protected string $modelClass = LoginLog::class;

    public string $search = '';

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function getDefaultColumns(): array
    {
        return ['user_id', 'ip', 'user_agent', 'created_at'];
    }

    public function getExportRowCallback(): callable
    {
        return function ($row) {
            return [
                'id' => $row->id,
                'user_id' => $row->user_id,
                'ip' => $row->ip ?? '',
                'user_agent' => $row->user_agent ?? '',
                'created_at' => $row->created_at?->format('Y-m-d H:i:s'),
            ];
        };
    }

    public function getImportModelClass(): string
    {
        return LoginLog::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '用户ID' => 'user_id',
            'IP' => 'ip',
            'UA' => 'user_agent',
        ];
    }

    public function getImportUniqueBy(): array
    {
        return ['id'];
    }

    public function getImportRequiredFields(): array
    {
        return ['用户ID'];
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'user_id', 'label' => '用户ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'ip', 'label' => 'IP', 'sortable' => false, 'exportable' => true],
            ['key' => 'user_agent', 'label' => 'UA', 'sortable' => false, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return LoginLog::when($this->search, function ($q) {
            $q->where('username', 'like', "%{$this->search}%");
        })->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '登录日志_' . now()->format('Ymd_His');
    }

    public function getPageIds(): array
    {
        return $this->getExportQuery()->forPage($this->page, 20)->pluck('id')->toArray();
    }

    public function delete(): void
    {
        LoginLog::findOrFail($this->deletingId)->delete();
        $this->toastSuccess('已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function render()
    {
        $query = LoginLog::orderBy('id', 'desc');

        if ($this->search) {
            $query->where('username', 'like', "%{$this->search}%");
        }

        $items = $query->paginate(setting('per_page', 10));

        return view('livewire.system.login-log-list', [
            'items' => $items,
            'allColumns' => $this->getAllColumns(),
            'selectedCount' => $this->getSelectedCount(),
        ])
            ->layout('components.app-layout')
            ->title('登录日志');
    }
}
