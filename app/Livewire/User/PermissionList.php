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

class PermissionList extends Component
{
    use WithPagination;
    use WithRowSelection, WithColumnVisibility, WithExcelExport, WithExcelImport;
    use WithToast;

    protected string $modelClass = Permission::class;

    public string $search = '';
    public int $filterType = 0;       // 0=全部, 1=模块, 2=页面, 3=按钮
    public int $filterModule = 0;    // 0=全部, >0=模块ID
    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public bool $showRoleModal = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;
    public ?int $rolePermissionId = null;

    public string $formName = '';
    public string $formDisplayName = '';
    public int $formType = 1;
    public int $formParentId = 0;
    public string $formRoute = '';
    public int $formSort = 0;
    public string $formIcon = '';

    // 角色勾选
    public array $formRoleIds = [];
    public string $rolePermissionName = '';
    public array $allRoles = [];

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
        $perm = Permission::findOrFail($id);
        $this->editingId = $id;
        $this->formName = $perm->name;
        $this->formDisplayName = $perm->getRawOriginal('display_name') ?? '';
        $this->formType = $perm->type;
        $this->formParentId = $perm->parent_id;
        $this->formRoute = $perm->route ?? '';
        $this->formSort = $perm->sort;
        $this->formIcon = $perm->icon ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'formName' => 'required|string|max:50|unique:permissions,name' . ($this->editingId ? ',' . $this->editingId : ''),
            'formDisplayName' => 'required|string|max:50',
            'formType' => 'required|integer|in:1,2,3',
            'formParentId' => 'integer',
            'formSort' => 'integer',
        ]);

        $data = [
            'name' => $this->formName,
            'display_name' => $this->formDisplayName,
            'type' => $this->formType,
            'parent_id' => $this->formParentId,
            'route' => $this->formRoute ?: null,
            'sort' => $this->formSort,
            'icon' => $this->formIcon ?: null,
        ];

        if ($this->editingId) {
            Permission::findOrFail($this->editingId)->update($data);
            $this->toastSuccess('权限已更新');
        } else {
            Permission::create($data);
            $this->toastSuccess('权限已创建');
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
        $perm = Permission::findOrFail($this->deletingId);
        if ($perm->roles()->count() > 0) {
            $this->toastError('该权限已被角色引用，不可删除');
            $this->showDeleteConfirm = false;
            return;
        }
        $perm->delete();
        $this->toastSuccess('权限已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function openRoleModal(int $id): void
    {
        $perm = Permission::findOrFail($id);
        $this->rolePermissionId = $id;
        $this->rolePermissionName = $perm->display_name;
        $this->formRoleIds = $perm->roles->pluck('id')->map(fn($v) => (int) $v)->toArray();
        $this->allRoles = Role::orderBy('id')->get()->map(fn($r) => [
            'id' => $r->id,
            'name' => $r->name,
            'display_name' => $r->display_name,
        ])->toArray();
        $this->showRoleModal = true;
    }

    public function saveRoles(): void
    {
        $perm = Permission::findOrFail($this->rolePermissionId);
        $roles = Role::whereIn('id', $this->formRoleIds)->get();

        // 同步：把该权限从不在 formRoleIds 中的角色移除，添加到在 formRoleIds 中的角色
        $currentRoleIds = $perm->roles->pluck('id')->toArray();
        $newRoleIds = collect($this->formRoleIds)->map(fn($v) => (int) $v)->toArray();

        $toAdd = array_diff($newRoleIds, $currentRoleIds);
        $toRemove = array_diff($currentRoleIds, $newRoleIds);

        foreach ($toAdd as $roleId) {
            $role = Role::find($roleId);
            if ($role && !$role->hasPermissionTo($perm)) {
                $role->givePermissionTo($perm);
            }
        }

        foreach ($toRemove as $roleId) {
            $role = Role::find($roleId);
            if ($role && $role->hasPermissionTo($perm)) {
                $role->revokePermissionTo($perm);
            }
        }

        $this->toastSuccess('角色分配已更新');
        $this->showRoleModal = false;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterType = 0;
        $this->filterModule = 0;
        $this->resetPage();
    }

    public function updatedFilterType(): void
    {
        $this->resetPage();
    }

    public function updatedFilterModule(): void
    {
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

    public function closeRoleModal(): void
    {
        $this->showRoleModal = false;
        $this->resetErrorBag();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->formName = '';
        $this->formDisplayName = '';
        $this->formType = 1;
        $this->formParentId = 0;
        $this->formRoute = '';
        $this->formSort = 0;
        $this->formIcon = '';
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'name', 'label' => '权限标识', 'sortable' => true, 'exportable' => true],
            ['key' => 'display_name', 'label' => '权限名称', 'sortable' => true, 'exportable' => true],
            ['key' => 'type', 'label' => '类型', 'sortable' => true, 'exportable' => true],
            ['key' => 'parent_id', 'label' => '父级ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'route', 'label' => '路由', 'sortable' => false, 'exportable' => true],
            ['key' => 'sort', 'label' => '排序', 'sortable' => true, 'exportable' => true],
            ['key' => 'icon', 'label' => '图标', 'sortable' => false, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return Permission::withCount('roles')->ordered();
    }

    public function getExportFileName(): string
    {
        return '权限管理_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return Permission::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '权限标识' => 'name',
            '权限名称' => 'display_name',
            '类型' => 'type',
            '父级ID' => 'parent_id',
            '路由' => 'route',
            '排序' => 'sort',
        ];
    }

    public function getPageIds(): array
    {
        return Permission::ordered()->limit(20)->pluck('id')->toArray();
    }

    public function render()
    {
        $query = Permission::withCount('roles')->ordered();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('display_name', 'like', "%{$this->search}%");
            });
        }

        // 类型筛选
        if ($this->filterType > 0) {
            $query->where('type', $this->filterType);
        }

        // 模块筛选：选了模块后，显示该模块及其子级（页面+按钮）
        if ($this->filterModule > 0) {
            $query->where(function ($q) {
                $q->where('id', $this->filterModule)
                  ->orWhere('parent_id', $this->filterModule)
                  ->orWhereIn('parent_id', function ($sub) {
                      $sub->select('id')
                          ->from('permissions')
                          ->where('parent_id', $this->filterModule);
                  });
            });
        }

        $permissions = $query->paginate(setting('per_page', 10));
        $parentOptions = Permission::roots()->get();
        $moduleOptions = Permission::roots()->orderBy('sort')->get();
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();

        return view('livewire.user.permission-list', compact('permissions', 'parentOptions', 'moduleOptions', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('权限管理');
    }
}
