<?php

namespace App\Livewire\Delivery;

use App\Models\Signature;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithToast;
use Livewire\Component;
use Livewire\WithPagination;

class SignatureList extends Component
{
    use WithPagination;
    use WithRowSelection, WithColumnVisibility, WithExcelExport, WithExcelImport;
    use WithToast;

    protected string $modelClass = Signature::class;

    public string $search = '';
    public bool $showDeleteConfirm = false;
    public ?int $deletingId = null;

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteConfirm = true;
    }

    public function delete(): void
    {
        Signature::findOrFail($this->deletingId)->delete();
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
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'signer_name', 'label' => '签收人', 'sortable' => true, 'exportable' => true],
            ['key' => 'signer_type', 'label' => '签收人类型', 'sortable' => true, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => true, 'exportable' => true],
            ['key' => 'signed_at', 'label' => '签收时间', 'sortable' => true, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return Signature::orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '签收存证_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return Signature::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '签收人' => 'signer_name',
            '签收人类型' => 'signer_type',
        ];
    }

    public function getPageIds(): array
    {
        return Signature::orderBy('id', 'desc')->limit(20)->pluck('id')->toArray();
    }

    public function render()
    {
        $query = Signature::orderBy('id', 'desc');

        if ($this->search) {
            $query->where('signer_name', 'like', "%{$this->search}%");
        }

        $items = $query->paginate(setting('per_page', 10));
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();

        return view('livewire.delivery.signature-list', compact('items', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('签收存证');
    }
}
