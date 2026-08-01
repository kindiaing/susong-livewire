<?php

namespace App\Livewire\User;

use App\Models\Permission;
use App\Models\Role;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithToast;
use Livewire\Component;
use Livewire\WithPagination;

class RoleList extends Component
{
    use WithPagination;
    use WithRowSelection, WithColumnVisibility, WithExcelExport, WithExcelImport;
    use WithToast;

    protected string $modelClass = Role::class;

    public string $search = '';
    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public bool $showPermissionModal = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;
    public ?int $permissionRoleId = null;

    public string $formName = '';
    public string $formDisplayName = '';
    public string $formGuardName = 'web';
    public string $formDescription = '';

    // 权限分配
    public array $formPermissionIds = [];
    public string $permissionRoleName = '';

    /**
     * 权限树结构（缓存在组件属性上，避免每次 toggle 都查数据库）
     * 结构：[{ id, name, display_name, childIds[], children: [{ id, name, display_name, childIds[], children: [...] }] }]
     */
    public array $permissionTreeData = [];

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $role = Role::findOrFail($id);
        if ($role->name === 'super_admin') {
            $this->toastError('超级管理员角色不可编辑');
            return;
        }
        $this->editingId = $id;
        $this->formName = $role->name;
        $this->formDisplayName = $role->getRawOriginal('display_name') ?? '';
        $this->formGuardName = $role->guard_name;
        $this->formDescription = $role->description ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'formName' => 'required|string|max:50|unique:roles,name' . ($this->editingId ? ',' . $this->editingId : ''),
            'formDisplayName' => 'required|string|max:50',
            'formGuardName' => 'required|string|max:50',
            'formDescription' => 'nullable|string|max:255',
        ]);

        $data = [
            'name' => $validated['formName'],
            'display_name' => $validated['formDisplayName'],
            'guard_name' => $validated['formGuardName'],
            'description' => $validated['formDescription'],
        ];

        if ($this->editingId) {
            $role = Role::findOrFail($this->editingId);
            $role->update($data);
            $this->toastSuccess('角色已更新');
        } else {
            $role = Role::create($data);
            // 新建角色默认分配 dashboard 权限（确保权限界面显示一致）
            $dashboard = \App\Models\Permission::where('name', 'dashboard')->first();
            if ($dashboard) {
                $role->givePermissionTo($dashboard);
            }
            $this->toastSuccess('角色已创建');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteConfirm = true;
    }

    public function delete(): void
    {
        $role = Role::findOrFail($this->deletingId);
        if ($role->name === 'super_admin') {
            $this->toastError('超级管理员角色不可删除');
            $this->showDeleteConfirm = false;
            return;
        }
        if ($role->users()->count() > 0) {
            $this->toastError('该角色下有用户，不可删除');
            $this->showDeleteConfirm = false;
            return;
        }
        $role->delete();
        $this->toastSuccess('角色已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function openPermissionModal(int $id): void
    {
        $role = Role::findOrFail($id);
        $this->permissionRoleId = $id;
        $this->permissionRoleName = $role->display_name;
        $this->formPermissionIds = $role->permissions->pluck('id')->map(fn($v) => (int) $v)->toArray();
        $this->buildPermissionTreeData();
        $this->showPermissionModal = true;
    }

    /**
     * 构建权限树数据缓存（避免每次 toggle 都查数据库）
     */
    private function buildPermissionTreeData(): void
    {
        $tree = Permission::with('children.children')->roots()->orderBy('sort')->get();
        $this->permissionTreeData = $tree->map(function ($module) {
            $childIds = [];
            $pages = $module->children->map(function ($page) use (&$childIds) {
                $btnIds = $page->children->pluck('id')->map(fn($v) => (int) $v)->toArray();
                $childIds = array_merge($childIds, [$page->id], $btnIds);
                return [
                    'id' => (int) $page->id,
                    'name' => $page->name,
                    'display_name' => $page->display_name,
                    'btnIds' => $btnIds,
                    'allIds' => array_merge([(int) $page->id], $btnIds),
                    'children' => $page->children->map(fn($btn) => [
                        'id' => (int) $btn->id,
                        'name' => $btn->name,
                        'display_name' => $btn->display_name,
                    ])->values()->toArray(),
                ];
            })->values()->toArray();

            return [
                'id' => (int) $module->id,
                'name' => $module->name,
                'display_name' => $module->display_name,
                'childIds' => $childIds,
                'allIds' => array_merge([(int) $module->id], $childIds),
                'children' => $pages,
            ];
        })->values()->toArray();
    }

    /**
     * 切换单个权限（按钮级）
     */
    public function togglePermission(int $permissionId): void
    {
        $permissionId = (int) $permissionId;
        $key = array_search($permissionId, $this->formPermissionIds);
        if ($key !== false) {
            array_splice($this->formPermissionIds, $key, 1);
        } else {
            $this->formPermissionIds[] = $permissionId;
        }
    }

    /**
     * 切换模块级权限（全选/全不选）
     * 逻辑：如果模块下所有权限都已选中 → 全不选；否则 → 全选
     */
    public function toggleModulePermissions(int $moduleId): void
    {
        $moduleData = null;
        foreach ($this->permissionTreeData as $m) {
            if ($m['id'] === $moduleId) {
                $moduleData = $m;
                break;
            }
        }
        if (!$moduleData) return;

        $allIds = $moduleData['allIds'];
        $selected = $this->formPermissionIds;

        // 判断：所有子权限是否都在 selected 中
        $allSelected = empty(array_diff($allIds, $selected));

        if ($allSelected) {
            // 全不选：移除模块下所有权限
            $this->formPermissionIds = array_values(array_diff($selected, $allIds));
        } else {
            // 全选：添加模块下所有权限
            $this->formPermissionIds = array_values(array_unique(array_merge($selected, $allIds)));
        }
    }

    /**
     * 切换页面级权限（全选/全不选）
     */
    public function togglePagePermissions(int $pageId): void
    {
        $pageData = null;
        foreach ($this->permissionTreeData as $module) {
            foreach ($module['children'] as $page) {
                if ($page['id'] === $pageId) {
                    $pageData = $page;
                    break 2;
                }
            }
        }
        if (!$pageData) return;

        $allIds = $pageData['allIds'];
        $selected = $this->formPermissionIds;

        $allSelected = empty(array_diff($allIds, $selected));

        if ($allSelected) {
            $this->formPermissionIds = array_values(array_diff($selected, $allIds));
        } else {
            $this->formPermissionIds = array_values(array_unique(array_merge($selected, $allIds)));
        }
    }

    /**
     * 获取模块的三态状态（用于前端渲染）
     * 返回：'checked' / 'partial' / 'unchecked'
     */
    private function getModuleState(array $moduleData): string
    {
        $allIds = $moduleData['allIds'];
        $selected = $this->formPermissionIds;
        $intersect = array_intersect($allIds, $selected);

        if (count($intersect) === 0) {
            return 'unchecked';
        }
        if (count($intersect) === count($allIds)) {
            return 'checked';
        }
        return 'partial';
    }

    /**
     * 获取页面的三态状态
     */
    private function getPageState(array $pageData): string
    {
        $allIds = $pageData['allIds'];
        $selected = $this->formPermissionIds;
        $intersect = array_intersect($allIds, $selected);

        if (count($intersect) === 0) {
            return 'unchecked';
        }
        if (count($intersect) === count($allIds)) {
            return 'checked';
        }
        return 'partial';
    }

    public function savePermissions(): void
    {
        $role = Role::findOrFail($this->permissionRoleId);
        // 确保类型一致
        $ids = array_map('intval', $this->formPermissionIds);
        $permissions = Permission::whereIn('id', $ids)->get();
        $role->syncPermissions($permissions);
        $this->toastSuccess('权限分配已更新');
        $this->showPermissionModal = false;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetErrorBag();
        $this->resetForm();
    }

    public function closeDeleteConfirm(): void
    {
        $this->showDeleteConfirm = false;
        $this->resetErrorBag();
    }

    public function closePermissionModal(): void
    {
        $this->showPermissionModal = false;
        $this->resetErrorBag();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->formName = '';
        $this->formDisplayName = '';
        $this->formGuardName = 'web';
        $this->formDescription = '';
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'name', 'label' => '角色标识', 'sortable' => true, 'exportable' => true],
            ['key' => 'display_name', 'label' => '角色名称', 'sortable' => true, 'exportable' => true],
            ['key' => 'guard_name', 'label' => '守卫', 'sortable' => true, 'exportable' => true],
            ['key' => 'description', 'label' => '描述', 'sortable' => false, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return Role::withCount('users')->withCount('permissions')->orderBy('id');
    }

    public function getExportFileName(): string
    {
        return '角色管理_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return Role::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '角色标识' => 'name',
            '角色名称' => 'display_name',
            '守卫' => 'guard_name',
            '描述' => 'description',
        ];
    }

    public function getPageIds(): array
    {
        return Role::orderBy('id')->limit(20)->pluck('id')->toArray();
    }

    public function render()
    {
        $query = Role::withCount('users')->withCount('permissions')->orderBy('id');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('display_name', 'like', "%{$this->search}%");
            });
        }

        $roles = $query->paginate(setting('per_page', 10));

        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();

        return view('livewire.user.role-list', compact('roles', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('角色管理');
    }
}
