<?php

namespace App\Livewire\Merchant;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Models\MerchantAddress;
use Livewire\Component;
use Livewire\WithPagination;

class MerchantAddressList extends Component
{
    use WithPagination;
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithToast;

    protected string $modelClass = MerchantAddress::class;

    public string $search = '';
    public bool $showDeleteConfirm = false;
    public ?int $deletingId = null;

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function getDefaultColumns(): array
    {
        return ['merchant', 'contact_name', 'contact_phone', 'address', 'is_default', 'created_at'];
    }

    public function getExportRowCallback(): callable
    {
        return function ($row) {
            return [
                'id' => $row->id,
                'merchant' => $row->merchant?->name ?? '',
                'contact_name' => $row->contact_name ?? '',
                'contact_phone' => $row->contact_phone ?? '',
                'address' => $row->address ?? '',
                'is_default' => $row->is_default,
                'created_at' => $row->created_at?->format('Y-m-d H:i:s'),
            ];
        };
    }

    public function getImportUniqueBy(): array
    {
        return ['id'];
    }

    public function getImportRequiredFields(): array
    {
        return ['商家ID', '联系人', '地址'];
    }

    public function getImportValueMap(): array
    {
        return [
            'is_default' => ['否' => 0, '是' => 1],
        ];
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'merchant', 'label' => '商家', 'sortable' => false, 'exportable' => true],
            ['key' => 'contact_name', 'label' => '联系人', 'sortable' => false, 'exportable' => true],
            ['key' => 'contact_phone', 'label' => '联系电话', 'sortable' => false, 'exportable' => true],
            ['key' => 'address', 'label' => '地址', 'sortable' => false, 'exportable' => true],
            ['key' => 'is_default', 'label' => '默认', 'sortable' => false, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return MerchantAddress::with('merchant')
            ->when($this->search, function ($q) {
                $q->where('address', 'like', "%{$this->search}%");
            })->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '商家地址_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return MerchantAddress::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '商家ID' => 'merchant_id',
            '联系人' => 'contact_name',
            '联系电话' => 'contact_phone',
            '地址' => 'address',
            '默认' => 'is_default',
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
        MerchantAddress::findOrFail($this->deletingId)->delete();
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
        $query = MerchantAddress::with('merchant')->orderBy('id', 'desc');

        if ($this->search) {
            $query->where('address', 'like', "%{$this->search}%");
        }

        $items = $query->paginate(setting('per_page', 10));

        return view('livewire.merchant.merchant-address-list', [
            'items' => $items,
            'allColumns' => $this->getAllColumns(),
            'selectedCount' => $this->getSelectedCount(),
        ])
            ->layout('components.app-layout')
            ->title('商家地址');
    }
}
