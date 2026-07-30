<?php

namespace App\Livewire\User;

use App\Models\Permission;
use App\Models\Role;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use Livewire\Component;
use Livewire\WithPagination;

class RoleList extends Component
{
    use WithPagination;
    use WithRowSelection, WithColumnVisibility, WithExcelExport, WithExcelImport;

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
            $this->dispatch('toast', message: '角色已更新', type: 'success');
        } else {
            Role::create($data);
            $this->dispatch('toast', message: '角色已创建', type: 'success');
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
        if ($role->users()->count() > 0) {
            $this->dispatch('toast', message: '该角色下有用户，不可删除', type: 'error');
            $this->showDeleteConfirm = false;
            return;
        }
        $role->delete();
        $this->dispatch('toast', message: '角色已删除', type: 'success');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function openPermissionModal(int $id): void
    {
        $role = Role::findOrFail($id);
        $this->permissionRoleId = $id;
        $this->permissionRoleName = $role->display_name;
        $this->formPermissionIds = $role->permissions->pluck('id')->map(fn($v) => (int) $v)->toArray();
        $this->showPermissionModal = true;
    }

    public function savePermissions(): void
    {
        $role = Role::findOrFail($this->permissionRoleId);
        $permissions = Permission::whereIn('id', $this->formPermissionIds)->get();
        $role->syncPermissions($permissions);
        $this->dispatch('toast', message: '权限分配已更新', type: 'success');
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

        $roles = $query->paginate(20);

        // 权限树（用于弹窗）
        $permissionTree = Permission::with('children.children')
            ->roots()
            ->orderBy('sort')
            ->get();
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();

        return view('livewire.user.role-list', compact('roles', 'permissionTree', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('角色管理');
    }
}
