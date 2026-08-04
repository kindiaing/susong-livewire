<?php

namespace App\Livewire\Product;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Livewire\Traits\WithListCrud;
use App\Models\Category;
use App\Models\UserPreference;
use App\Support\Setting;
use Livewire\Component;

class CategoryList extends Component
{
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithToast;
    use WithListCrud;

    protected string $modelClass = Category::class;

    public string $search = '';

    public ?int $editingId = null;

    // 树形展开状态
    public array $expandedIds = [];

    // 删除提示信息
    public string $deleteWarning = '';

    public string $formParentId = '0';
    public string $formName = '';
    public string $formIcon = '';
    public int $formSort = 0;
    public int $formStatus = 1;

    public function mount(): void
    {
        $this->initColumnVisibility();

        // 优先级：用户偏好 > 系统配置 > false
        $userId = auth()->id();
        $userPref = UserPreference::getPreference($userId, 'category_tree_expanded');
        if ($userPref !== null) {
            $expanded = (bool) $userPref;
        } else {
            $expanded = (bool) Setting::get('ui_category_tree_expanded', false);
        }

        if ($expanded) {
            $this->expandedIds = Category::pluck('id')->map(fn($id) => (string) $id)->toArray();
        }
    }

    /**
     * 切换展开/折叠
     */
    public function toggleExpand(int $id): void
    {
        $idStr = (string) $id;
        if (in_array($idStr, $this->expandedIds)) {
            $this->expandedIds = array_values(array_diff($this->expandedIds, [$idStr]));
        } else {
            $this->expandedIds[] = $idStr;
        }
    }

    /**
     * 全部展开
     */
    public function expandAll(): void
    {
        $this->expandedIds = Category::pluck('id')->map(fn($id) => (string) $id)->toArray();
        UserPreference::setPreference(auth()->id(), 'category_tree_expanded', true);
    }

    /**
     * 全部折叠
     */
    public function collapseAll(): void
    {
        $this->expandedIds = [];
        UserPreference::setPreference(auth()->id(), 'category_tree_expanded', false);
    }

    public function openEditModal(int $id): void
    {
        $category = Category::findOrFail($id);
        $this->editingId = $id;
        $this->formParentId = (string) $category->parent_id;
        $this->formName = $category->name;
        $this->formIcon = $category->icon ?? '';
        $this->formSort = $category->sort;
        $this->formStatus = $category->status;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'formParentId' => 'required|integer',
            'formName' => 'required|string|max:50',
            'formIcon' => 'nullable|string|max:255',
            'formSort' => 'required|integer|min:0',
            'formStatus' => 'required|in:0,1',
        ]);

        // 不允许把分类设为自己的子分类（编辑时）
        if ($this->editingId && (int) $validated['formParentId'] === $this->editingId) {
            $this->addError('formParentId', '不能将分类设为自身的子分类');
            return;
        }

        // 不允许把分类设为自己的后代子分类
        if ($this->editingId) {
            $descendantIds = Category::findOrFail($this->editingId)->getAllChildrenIds();
            if (in_array((int) $validated['formParentId'], $descendantIds)) {
                $this->addError('formParentId', '不能将分类移到自身后代分类下');
                return;
            }
        }

        $data = [
            'parent_id' => (int) $validated['formParentId'],
            'name' => $validated['formName'],
            'icon' => $validated['formIcon'] ?: null,
            'sort' => $validated['formSort'],
            'status' => $validated['formStatus'],
        ];

        if ($this->editingId) {
            Category::findOrFail($this->editingId)->update($data);
            $this->toastSuccess('分类已更新');
        } else {
            Category::create($data);
            $this->toastSuccess('分类已创建');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $category = Category::findOrFail($id);
        $warnings = [];

        // 检查是否有子分类
        $childCount = $category->children()->count();
        if ($childCount > 0) {
            $warnings[] = "该分类下有 {$childCount} 个子分类";
        }

        // 检查是否有关联商品
        $productCount = $category->products()->count();
        if ($productCount > 0) {
            $warnings[] = "该分类下有 {$productCount} 个商品";
        }

        if (count($warnings) > 0) {
            $this->deleteWarning = implode('，', $warnings) . '。请先移除或转移关联数据后再删除。';
            $this->canDelete = false;
        } else {
            $this->deleteWarning = '确定要删除该分类吗？此操作不可恢复。';
            $this->canDelete = true;
        }

        $this->deletingId = $id;
        $this->showDeleteConfirm = true;
    }

    public function delete(): void
    {
        if (!$this->canDelete) {
            $this->toastWarning('无法删除，请先移除或转移关联数据');
            return;
        }

        $category = Category::findOrFail($this->deletingId);
        $deletedId = (string) $this->deletingId;

        $category->delete();
        $this->toastSuccess('分类已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
        $this->deleteWarning = '';
        $this->canDelete = true;

        // 从展开列表中移除
        $this->expandedIds = array_values(array_diff($this->expandedIds, [$deletedId]));
    }

    /**
     * 拖拽排序：接收源分类ID和目标分类ID，在同层级内重排
     */
    public function updateSortOrder(int $sourceId, int $targetId): void
    {
        $source = Category::findOrFail($sourceId);
        $parentId = $source->parent_id;

        // 同层级的兄弟节点，按当前排序
        $siblings = Category::where('parent_id', $parentId)
            ->ordered()
            ->pluck('id')
            ->toArray();

        // 从列表中移除源，插入到目标位置
        $siblings = array_values(array_diff($siblings, [$sourceId]));
        $targetIndex = array_search($targetId, $siblings);
        if ($targetIndex === false) {
            $siblings[] = $sourceId;
        } else {
            array_splice($siblings, $targetIndex, 0, [$sourceId]);
        }

        // 批量更新排序
        foreach ($siblings as $index => $id) {
            Category::where('id', $id)->update(['sort' => $index + 1]);
        }

        $this->toastSuccess('排序已更新');
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->clearSelection();
    }

    public function closeDeleteConfirm(): void
    {
        $this->showDeleteConfirm = false;
        $this->resetErrorBag();
        $this->deleteWarning = '';
        $this->canDelete = true;
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->formParentId = '0';
        $this->formName = '';
        $this->formIcon = '';
        $this->formSort = 0;
        $this->formStatus = 1;
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'name', 'label' => '名称', 'sortable' => true, 'exportable' => true],
            ['key' => 'parent_id', 'label' => '上级ID', 'sortable' => false, 'exportable' => true],
            ['key' => 'sort', 'label' => '排序', 'sortable' => true, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => false, 'exportable' => true],
            ['key' => 'icon', 'label' => '图标', 'sortable' => false, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return Category::when($this->search, function ($q) {
            $q->where('name', 'like', "%{$this->search}%");
        })->orderBy('sort')->orderBy('id');
    }

    public function getExportFileName(): string
    {
        return '分类_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return Category::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '名称' => 'name',
            '上级ID' => 'parent_id',
            '排序' => 'sort',
            '状态' => 'status',
            '图标' => 'icon',
        ];
    }

    public function getPageIds(): array
    {
        return $this->getExportQuery()->pluck('id')->toArray();
    }

    /**
     * 获取树形扁平化列表（用于渲染）
     * 返回: [[category, depth], ...]
     */
    private function getFlatTree(): array
    {
        $tree = Category::getTree();
        $flat = [];
        $search = $this->search;

        $flatten = function ($nodes, $depth = 0) use (&$flatten, &$flat, $search) {
            foreach ($nodes as $node) {
                // 搜索时只匹配名称
                if ($search && stripos($node->name, $search) === false) {
                    // 但如果子节点匹配，父节点也要显示
                    $hasMatchingChild = false;
                    $childCheck = function ($children) use ($search, &$childCheck, &$hasMatchingChild) {
                        foreach ($children as $child) {
                            if (stripos($child->name, $search) !== false) {
                                $hasMatchingChild = true;
                                return;
                            }
                            $childCheck($child->children ?? []);
                        }
                    };
                    $childCheck($node->children ?? []);
                    if (! $hasMatchingChild) {
                        continue;
                    }
                }

                $flat[] = [$node, $depth];

                // 子节点：展开时才显示
                if ($node->children->isNotEmpty() && in_array((string) $node->id, $this->expandedIds)) {
                    $flatten($node->children, $depth + 1);
                }
            }
        };

        $flatten($tree);
        return $flat;
    }

    public function render()
    {
        $flatTree = $this->getFlatTree();

        // 父级分类选项（供弹窗选择）
        $parentOptions = Category::toSelectOptions();

        $allColumns = $this->getAllColumns();
        $selectedCount = count($this->selectedIds);

        return view('livewire.product.category-list', compact('flatTree', 'parentOptions', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('分类管理');
    }
}
