<?php

namespace App\Livewire\User;

use App\Models\Permission;
use App\Models\Role;
use Livewire\Component;
use Livewire\WithPagination;

class RoleList extends Component
{
    use WithPagination;

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

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->formName = '';
        $this->formDisplayName = '';
        $this->formGuardName = 'web';
        $this->formDescription = '';
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

        return view('livewire.user.role-list', compact('roles', 'permissionTree'))
            ->layout('components.app-layout')
            ->title('角色管理');
    }
}
