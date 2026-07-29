<?php

namespace App\Livewire\User;

use App\Models\Permission;
use App\Models\Role;
use Livewire\Component;
use Livewire\WithPagination;

class PermissionList extends Component
{
    use WithPagination;

    public string $search = '';
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
            $this->dispatch('toast', message: '权限已更新', type: 'success');
        } else {
            Permission::create($data);
            $this->dispatch('toast', message: '权限已创建', type: 'success');
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
            $this->dispatch('toast', message: '该权限已被角色引用，不可删除', type: 'error');
            $this->showDeleteConfirm = false;
            return;
        }
        $perm->delete();
        $this->dispatch('toast', message: '权限已删除', type: 'success');
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

        $this->dispatch('toast', message: '角色分配已更新', type: 'success');
        $this->showRoleModal = false;
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
        $this->formType = 1;
        $this->formParentId = 0;
        $this->formRoute = '';
        $this->formSort = 0;
        $this->formIcon = '';
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

        $permissions = $query->paginate(20);
        $parentOptions = Permission::roots()->get();

        return view('livewire.user.permission-list', compact('permissions', 'parentOptions'))
            ->layout('components.app-layout')
            ->title('权限管理');
    }
}
