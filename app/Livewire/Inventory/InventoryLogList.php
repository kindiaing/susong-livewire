<?php

namespace App\Livewire\Inventory;

use App\Models\InventoryLog;
use App\Models\Warehouse;
use Livewire\Component;
use Livewire\WithPagination;

class InventoryLogList extends Component
{
    use WithPagination;

    public string $search = '';
    public int $filterType = -1;
    public int $filterWarehouseId = 0;

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterType = -1;
        $this->filterWarehouseId = 0;
        $this->resetPage();
    }

    public function render()
    {
        $query = InventoryLog::with(['warehouse', 'sku.product', 'operator'])->orderBy('id', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('reason', 'like', "%{$this->search}%")
                    ->orWhereHas('sku', fn($sq) => $sq->where('sku_code', 'like', "%{$this->search}%"));
            });
        }

        if ($this->filterType >= 0) {
            $query->where('type', $this->filterType);
        }

        if ($this->filterWarehouseId > 0) {
            $query->where('warehouse_id', $this->filterWarehouseId);
        }

        $items = $query->paginate(20);
        $warehouses = Warehouse::enabled()->orderBy('name')->get();

        return view('livewire.inventory.inventory-log-list', compact('items', 'warehouses'))
            ->layout('components.app-layout')
            ->title('库存日志');
    }
}
