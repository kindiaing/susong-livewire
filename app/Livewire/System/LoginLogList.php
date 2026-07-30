<?php

namespace App\Livewire\System;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithRowSelection;
use App\Models\LoginLog;
use Livewire\Component;
use Livewire\WithPagination;

class LoginLogList extends Component
{
    use WithPagination;
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;

    protected string $modelClass = LoginLog::class;

    public string $search = '';
    public bool $showDeleteConfirm = false;
    public ?int $deletingId = null;

    public function mount(): void
    {
        $this->initColumnVisibility();
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

    public function closeColumnModal(): void
    {
        $this->showColumnModal = false;
    }

    public function closeExportModal(): void
    {
        $this->showExportModal = false;
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteConfirm = true;
    }

    public function delete(): void
    {
        LoginLog::findOrFail($this->deletingId)->delete();
        $this->dispatch('toast', message: '已删除', type: 'success');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function closeDeleteConfirm(): void
    {
        $this->showDeleteConfirm = false;
        $this->resetErrorBag();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->resetPage();
        $this->clearSelection();
    }

    public function render()
    {
        $query = LoginLog::orderBy('id', 'desc');

        if ($this->search) {
            $query->where('username', 'like', "%{$this->search}%");
        }

        $items = $query->paginate(20);

        return view('livewire.system.login-log-list', [
            'items' => $items,
            'allColumns' => $this->getAllColumns(),
            'selectedCount' => $this->getSelectedCount(),
        ])
            ->layout('components.app-layout')
            ->title('登录日志');
    }
}
