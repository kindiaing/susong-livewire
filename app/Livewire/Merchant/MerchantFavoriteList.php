<?php

namespace App\Livewire\Merchant;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Livewire\Traits\WithListCrud;
use App\Models\MerchantFavorite;
use Livewire\Component;
use Livewire\WithPagination;

class MerchantFavoriteList extends Component
{
    use WithPagination;
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithToast;
    use WithListCrud;

    protected string $modelClass = MerchantFavorite::class;

    public string $search = '';

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function getDefaultColumns(): array
    {
        return ['merchant', 'sku', 'created_at'];
    }

    public function getExportRowCallback(): callable
    {
        return function ($row) {
            return [
                'id' => $row->id,
                'merchant' => $row->merchant?->name ?? '',
                'product' => $row->sku?->name ?? '',
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
        return ['商家ID', 'SKU ID'];
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'merchant', 'label' => '商家', 'sortable' => false, 'exportable' => true],
            ['key' => 'sku', 'label' => '商品', 'sortable' => false, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return MerchantFavorite::with(['merchant', 'sku'])
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->whereHas('merchant', function ($mq) {
                        $mq->where('name', 'like', "%{$this->search}%");
                    })->orWhereHas('sku', function ($pq) {
                        $pq->where('name', 'like', "%{$this->search}%");
                    });
                });
            })->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '商家收藏_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return MerchantFavorite::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '商家ID' => 'merchant_id',
            'SKU ID' => 'sku_id',
        ];
    }

    public function getPageIds(): array
    {
        return $this->getExportQuery()->forPage($this->page, 20)->pluck('id')->toArray();
    }

    public function delete(): void
    {
        MerchantFavorite::findOrFail($this->deletingId)->delete();
        $this->toastSuccess('已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function render()
    {
        $query = MerchantFavorite::with(['merchant', 'sku'])->orderBy('id', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('merchant', function ($mq) {
                    $mq->where('name', 'like', "%{$this->search}%");
                })->orWhereHas('sku', function ($pq) {
                    $pq->where('name', 'like', "%{$this->search}%");
                });
            });
        }

        $items = $query->paginate(setting('per_page', 10));

        return view('livewire.merchant.merchant-favorite-list', [
            'items' => $items,
            'allColumns' => $this->getAllColumns(),
            'selectedCount' => $this->getSelectedCount(),
        ])
            ->layout('components.app-layout')
            ->title('商家收藏');
    }
}
