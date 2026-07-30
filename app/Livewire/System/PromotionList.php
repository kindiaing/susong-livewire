<?php

namespace App\Livewire\System;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithRowSelection;
use App\Models\Promotion;
use Livewire\Component;
use Livewire\WithPagination;

class PromotionList extends Component
{
    use WithPagination;
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;

    protected string $modelClass = Promotion::class;

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
            ['key' => 'product', 'label' => '商品', 'sortable' => false, 'exportable' => true],
            ['key' => 'title', 'label' => '标题', 'sortable' => false, 'exportable' => true],
            ['key' => 'sort', 'label' => '排序', 'sortable' => true, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => false, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return Promotion::with('product')
            ->when($this->search, function ($q) {
                $q->where('title', 'like', "%{$this->search}%");
            })->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '运营主推_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return Promotion::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '商品ID' => 'product_id',
            '标题' => 'title',
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
        Promotion::findOrFail($this->deletingId)->delete();
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
        $query = Promotion::with('product')->orderBy('id', 'desc');

        if ($this->search) {
            $query->where('title', 'like', "%{$this->search}%");
        }

        $items = $query->paginate(20);

        return view('livewire.system.promotion-list', [
            'items' => $items,
            'allColumns' => $this->getAllColumns(),
            'selectedCount' => $this->getSelectedCount(),
        ])
            ->layout('components.app-layout')
            ->title('运营主推');
    }
}
