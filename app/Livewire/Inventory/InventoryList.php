<?php

namespace App\Livewire\Inventory;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Warehouse;
use App\Models\Sku;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithToast;
use App\Livewire\Traits\WithListCrud;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

class InventoryList extends Component
{
    use WithPagination;
    use WithRowSelection, WithColumnVisibility, WithExcelExport, WithExcelImport;
    use WithToast;
    use WithListCrud;

    protected string $modelClass = Inventory::class;

    public string $search = '';
    public int $filterWarehouseId = 0;
    public array $filterCategoryIds = [];

    /** 缓存分类树，避免多次查询 */
    protected ?\Illuminate\Database\Eloquent\Collection $categoryTreeCache = null;

    /**
     * 分类勾选联动：勾选父→全选子，取消父→全取消子，勾选子→补选父
     */
    public function toggleCategoryFilter(int $categoryId): void
    {
        $tree = $this->getCategoryTree();
        $allIds = $this->flattenCategoryIds($tree);
        $selected = $this->filterCategoryIds;
        $isCurrentlySelected = in_array($categoryId, $selected);

        // 找到该分类的所有后代ID（含自身）
        $descendantIds = $this->getDescendantIds($tree, $categoryId);

        if ($isCurrentlySelected) {
            // 取消：移除自身及所有后代
            $selected = array_values(array_diff($selected, $descendantIds));
        } else {
            // 选中：添加自身及所有后代
            $selected = array_values(array_unique(array_merge($selected, $descendantIds)));
            // 同时补选所有祖先
            $ancestorIds = $this->getAncestorIds($tree, $categoryId);
            $selected = array_values(array_unique(array_merge($selected, $ancestorIds)));
        }

        $this->filterCategoryIds = $selected;
        $this->resetPage();
    }

    /**
     * 计算各分类的三态信息，供 Blade 渲染 indeterminate
     */
    #[Computed]
    public function categoryCheckStates(): array
    {
        $tree = $this->getCategoryTree();
        $selected = $this->filterCategoryIds;
        $states = [];
        $this->computeCheckStates($tree, $selected, $states);
        return $states;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterWarehouseId = 0;
        $this->filterCategoryIds = [];
        $this->resetPage();
    }

    // ── 分类树辅助方法 ──

    private function getCategoryTree()
    {
        if ($this->categoryTreeCache === null) {
            $this->categoryTreeCache = Category::getTree();
        }
        return $this->categoryTreeCache;
    }

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    /** 递归获取分类树下所有ID */
    private function flattenCategoryIds($nodes): array
    {
        $ids = [];
        foreach ($nodes as $node) {
            $ids[] = $node->id;
            if ($node->children->isNotEmpty()) {
                $ids = array_merge($ids, $this->flattenCategoryIds($node->children));
            }
        }
        return $ids;
    }

    /** 获取某分类及所有后代的ID */
    private function getDescendantIds($nodes, int $targetId): array
    {
        foreach ($nodes as $node) {
            if ($node->id === $targetId) {
                return $this->flattenCategoryIds(collect([$node]));
            }
            if ($node->children->isNotEmpty()) {
                $result = $this->getDescendantIds($node->children, $targetId);
                if (!empty($result)) return $result;
            }
        }
        return [];
    }

    /** 获取某分类的所有祖先ID */
    private function getAncestorIds($nodes, int $targetId, array $path = []): array
    {
        foreach ($nodes as $node) {
            if ($node->id === $targetId) {
                return array_column($path, 'id');
            }
            if ($node->children->isNotEmpty()) {
                $result = $this->getAncestorIds($node->children, $targetId, array_merge($path, [['id' => $node->id]]));
                if (!empty($result)) return $result;
            }
        }
        return [];
    }

    /** 递归计算三态：checked / indeterminate / unchecked */
    private function computeCheckStates($nodes, array $selected, array &$states): void
    {
        foreach ($nodes as $node) {
            $childIds = $this->flattenCategoryIds($node->children);
            $isSelfChecked = in_array($node->id, $selected);

            if (empty($childIds)) {
                // 叶子节点：只有选/未选
                $states[$node->id] = $isSelfChecked ? 'checked' : 'unchecked';
            } else {
                // 有子节点：计算子选中数
                $selectedChildren = count(array_intersect($childIds, $selected));
                $totalChildren = count($childIds);

                if ($selectedChildren === 0 && !$isSelfChecked) {
                    $states[$node->id] = 'unchecked';
                } elseif ($selectedChildren === $totalChildren && ($isSelfChecked || $totalChildren > 0)) {
                    $states[$node->id] = 'checked';
                } else {
                    $states[$node->id] = 'indeterminate';
                }
            }

            if ($node->children->isNotEmpty()) {
                $this->computeCheckStates($node->children, $selected, $states);
            }
        }
    }

    public function openEditModal(int $id): void
    {
        $item = Inventory::findOrFail($id);
        $this->editingId = $id;
        $this->formWarehouseId = $item->warehouse_id;
        $this->formSkuId = $item->sku_id;
        $this->formTotalStock = $item->total_stock;
        $this->formLockedStock = $item->locked_stock;
        $this->formAvailableStock = $item->available_stock;
        $this->formBatchNo = $item->batch_no ?? '';
        $this->formExpiryDate = $item->expiry_date ? $item->expiry_date->format('Y-m-d') : '';
        $this->formWarningValue = $item->warning_value;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'formWarehouseId' => 'required|integer|exists:warehouses,id',
            'formSkuId' => 'required|integer|exists:skus,id',
            'formTotalStock' => 'required|integer|min:0',
            'formLockedStock' => 'required|integer|min:0',
            'formAvailableStock' => 'required|integer|min:0',
            'formBatchNo' => 'nullable|string|max:50',
            'formExpiryDate' => 'nullable|date',
            'formWarningValue' => 'required|integer|min:0',
        ]);

        $data = [
            'warehouse_id' => $validated['formWarehouseId'],
            'sku_id' => $validated['formSkuId'],
            'total_stock' => $validated['formTotalStock'],
            'locked_stock' => $validated['formLockedStock'],
            'available_stock' => $validated['formAvailableStock'],
            'batch_no' => $validated['formBatchNo'] ?: null,
            'expiry_date' => $validated['formExpiryDate'] ?: null,
            'warning_value' => $validated['formWarningValue'],
        ];

        if ($this->editingId) {
            $item = Inventory::findOrFail($this->editingId);
            $item->update($data);
            $this->toastSuccess('库存已更新');
        } else {
            Inventory::create($data);
            $this->toastSuccess('库存已创建');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(): void
    {
        $item = Inventory::findOrFail($this->deletingId);
        $item->delete();
        $this->toastSuccess('库存记录已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->formWarehouseId = 0;
        $this->formSkuId = 0;
        $this->formTotalStock = 0;
        $this->formLockedStock = 0;
        $this->formAvailableStock = 0;
        $this->formBatchNo = '';
        $this->formExpiryDate = '';
        $this->formWarningValue = 0;
    }

    public function getDefaultColumns(): array
    {
        return ['warehouse_id', 'sku_id', 'total_stock', 'locked_stock', 'available_stock', 'batch_no', 'expiry_date', 'warning_value', 'created_at'];
    }

    public function getExportRowCallback(): callable
    {
        return function ($row) {
            return [
                'id' => $row->id,
                'warehouse_id' => $row->warehouse?->name ?? '',
                'sku_id' => $row->sku?->sku_code ?? '',
                'total_stock' => $row->total_stock,
                'locked_stock' => $row->locked_stock,
                'available_stock' => $row->available_stock,
                'batch_no' => $row->batch_no ?? '',
                'expiry_date' => $row->expiry_date ? $row->expiry_date->format('Y-m-d') : '',
                'warning_value' => $row->warning_value,
                'created_at' => $row->created_at?->format('Y-m-d H:i:s'),
            ];
        };
    }

    public function getImportUniqueBy(): array
    {
        return ['sku_id'];
    }

    public function getImportRequiredFields(): array
    {
        return ['仓库ID', 'SKU ID'];
    }

    public function getImportValueMap(): array
    {
        return [];
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'warehouse_id', 'label' => '仓库', 'sortable' => true, 'exportable' => true],
            ['key' => 'sku_id', 'label' => 'SKU', 'sortable' => true, 'exportable' => true],
            ['key' => 'total_stock', 'label' => '总库存', 'sortable' => true, 'exportable' => true],
            ['key' => 'locked_stock', 'label' => '锁定库存', 'sortable' => true, 'exportable' => true],
            ['key' => 'available_stock', 'label' => '可用库存', 'sortable' => true, 'exportable' => true],
            ['key' => 'batch_no', 'label' => '批次号', 'sortable' => true, 'exportable' => true],
            ['key' => 'expiry_date', 'label' => '有效期', 'sortable' => true, 'exportable' => true],
            ['key' => 'warning_value', 'label' => '预警值', 'sortable' => true, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return Inventory::with(['warehouse', 'sku.product'])->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '实时库存_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return Inventory::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '仓库ID' => 'warehouse_id',
            'SKU ID' => 'sku_id',
            '总库存' => 'total_stock',
            '锁定库存' => 'locked_stock',
            '可用库存' => 'available_stock',
            '批次号' => 'batch_no',
            '预警值' => 'warning_value',
        ];
    }

    public function getPageIds(): array
    {
        return Inventory::orderBy('id', 'desc')->limit(20)->pluck('id')->toArray();
    }

    public function render()
    {
        $query = Inventory::with(['warehouse', 'sku.product'])->orderBy('id', 'desc');

        if ($this->search) {
            $query->whereHas('sku', function ($q) {
                $q->where('sku_code', 'like', "%{$this->search}%")
                    ->orWhereHas('product', fn($pq) => $pq->where('name', 'like', "%{$this->search}%"));
            });
        }

        if ($this->filterWarehouseId > 0) {
            $query->where('warehouse_id', $this->filterWarehouseId);
        }

        if (!empty($this->filterCategoryIds)) {
            $query->whereHas('sku.product', function ($q) {
                $q->whereIn('category_id', $this->filterCategoryIds);
            });
        }

        $items = $query->paginate(setting('per_page', 10));
        $warehouses = Warehouse::enabled()->get();
        $categoryTree = Category::getTree();
        $skus = Sku::with('product')->orderBy('sku_code')->get();
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();

        return view('livewire.inventory.inventory-list', compact('items', 'warehouses', 'categoryTree', 'skus', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('实时库存');
    }
}
