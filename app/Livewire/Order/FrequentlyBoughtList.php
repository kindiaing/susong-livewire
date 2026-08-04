<?php

namespace App\Livewire\Order;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithListCrud;
use App\Livewire\Traits\WithRowSelection;
use App\Models\FrequentlyBought;
use Livewire\Component;
use Livewire\WithPagination;

class FrequentlyBoughtList extends Component
{
    use WithPagination;
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithListCrud;

    protected string $modelClass = FrequentlyBought::class;

    public string $search = '';

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'merchant', 'label' => '商家', 'sortable' => false, 'exportable' => true],
            ['key' => 'product', 'label' => '商品', 'sortable' => false, 'exportable' => true],
            ['key' => 'buy_count', 'label' => '购买次数', 'sortable' => true, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return $this->buildQuery();
    }

    public function getExportFileName(): string
    {
        return '常购清单_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return FrequentlyBought::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '商家ID' => 'merchant_id',
            '商品ID' => 'product_id',
            '购买次数' => 'buy_count',
        ];
    }

    public function getPageIds(): array
    {
        return $this->buildQuery()->forPage($this->page, 20)->pluck('id')->toArray();
    }

    private function buildQuery()
    {
        $query = FrequentlyBought::with(['merchant', 'sku.product'])->orderBy('buy_count', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('merchant', function ($mq) {
                    $mq->where('name', 'like', "%{$this->search}%");
                })->orWhereHas('sku', function ($sq) {
                    $sq->where('sku_code', 'like', "%{$this->search}%");
                });
            });
        }

        return $query;
    }

    public function render()
    {
        $items = $this->buildQuery()->paginate(setting('per_page', 10));

        return view('livewire.order.frequently-bought-list', [
            'items' => $items,
            'allColumns' => $this->getAllColumns(),
            'selectedCount' => $this->getSelectedCount(),
        ])
            ->layout('components.app-layout')
            ->title('常购清单');
    }
}
