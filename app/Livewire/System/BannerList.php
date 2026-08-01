<?php

namespace App\Livewire\System;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Models\Banner;
use Livewire\Component;
use Livewire\WithPagination;

class BannerList extends Component
{
    use WithPagination;
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithToast;

    protected string $modelClass = Banner::class;

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
            ['key' => 'title', 'label' => '标题', 'sortable' => false, 'exportable' => true],
            ['key' => 'image_path', 'label' => '图片', 'sortable' => false, 'exportable' => false],
            ['key' => 'link_url', 'label' => '链接', 'sortable' => false, 'exportable' => true],
            ['key' => 'sort', 'label' => '排序', 'sortable' => true, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => false, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return Banner::when($this->search, function ($q) {
            $q->where('title', 'like', "%{$this->search}%");
        })->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '轮播广告_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return Banner::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '标题' => 'title',
            '图片路径' => 'image_path',
            '链接' => 'link_url',
            '排序' => 'sort',
            '状态' => 'status',
        ];
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

    public function closeImportModal(): void
    {
        $this->showImportModal = false;
        $this->importFile = null;
        $this->importMessage = '';
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteConfirm = true;
    }

    public function delete(): void
    {
        Banner::findOrFail($this->deletingId)->delete();
        $this->toastSuccess('已删除');
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
        $query = Banner::orderBy('id', 'desc');

        if ($this->search) {
            $query->where('title', 'like', "%{$this->search}%");
        }

        $items = $query->paginate(setting('per_page', 10));

        return view('livewire.system.banner-list', [
            'items' => $items,
            'allColumns' => $this->getAllColumns(),
            'selectedCount' => $this->getSelectedCount(),
        ])
            ->layout('components.app-layout')
            ->title('轮播广告');
    }
}
