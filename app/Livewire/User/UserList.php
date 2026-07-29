<?php

namespace App\Livewire\User;

use App\Models\Role;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserList extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public bool $showRoleModal = false;
    public bool $showResetConfirm = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;
    public ?int $resettingId = null;
    public ?int $roleUserId = null;

    // 创建/编辑表单
    public string $formUsername = '';
    public string $formName = '';
    public string $formPhone = '';
    public string $formEmail = '';
    public string $formPassword = '';
    public int $formStatus = 1;

    // 角色分配
    public array $formRoleIds = [];
    public array $allRoles = [];

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $user = User::findOrFail($id);
        $this->editingId = $id;
        $this->formUsername = $user->username;
        $this->formName = $user->name;
        $this->formPhone = $user->phone ?? '';
        $this->formEmail = $user->email ?? '';
        $this->formPassword = '';
        $this->formStatus = $user->status;
        $this->showModal = true;
    }

    public function save(): void
    {
        $rules = [
            'formUsername' => 'required|string|max:50|unique:users,username' . ($this->editingId ? ',' . $this->editingId : ''),
            'formName' => 'required|string|max:50',
            'formPhone' => 'nullable|string|max:20|unique:users,phone' . ($this->editingId ? ',' . $this->editingId : ''),
            'formEmail' => 'nullable|email|max:100|unique:users,email' . ($this->editingId ? ',' . $this->editingId : ''),
            'formStatus' => 'required|in:0,1',
        ];

        if (!$this->editingId) {
            $rules['formPassword'] = 'required|string|min:6|max:50';
        } else {
            $rules['formPassword'] = 'nullable|string|min:6|max:50';
        }

        $validated = $this->validate($rules);

        $data = [
            'username' => $validated['formUsername'],
            'name' => $validated['formName'],
            'phone' => $validated['formPhone'] ?: null,
            'email' => $validated['formEmail'] ?: null,
            'status' => $validated['formStatus'],
        ];

        if ($validated['formPassword']) {
            $data['password'] = $validated['formPassword'];
        }

        if ($this->editingId) {
            User::findOrFail($this->editingId)->update($data);
            $this->dispatch('toast', message: '用户已更新', type: 'success');
        } else {
            User::create($data);
            $this->dispatch('toast', message: '用户已创建', type: 'success');
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
        $user = User::findOrFail($this->deletingId);
        if ($user->id === auth()->id()) {
            $this->dispatch('toast', message: '不能删除当前登录用户', type: 'error');
            $this->showDeleteConfirm = false;
            return;
        }
        $user->delete();
        $this->dispatch('toast', message: '用户已删除', type: 'success');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function toggleStatus(int $id): void
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            $this->dispatch('toast', message: '不能禁用当前登录用户', type: 'error');
            return;
        }
        $user->status = $user->status === 1 ? 0 : 1;
        $user->save();
        $label = $user->status === 1 ? '启用' : '禁用';
        $this->dispatch('toast', message: "用户已{$label}", type: 'success');
    }

    public function confirmResetPassword(int $id): void
    {
        $this->resettingId = $id;
        $this->showResetConfirm = true;
    }

    public function resetPassword(): void
    {
        $user = User::findOrFail($this->resettingId);
        $user->password = 'Password';
        $user->save();
        $this->dispatch('toast', message: '密码已重置为 Password', type: 'success');
        $this->showResetConfirm = false;
        $this->resettingId = null;
    }

    public function openRoleModal(int $id): void
    {
        $user = User::findOrFail($id);
        $this->roleUserId = $id;
        $this->formRoleIds = $user->roles->pluck('id')->map(fn($v) => (int) $v)->toArray();
        $this->allRoles = Role::orderBy('id')->get()->map(fn($r) => [
            'id' => $r->id,
            'name' => $r->name,
            'display_name' => $r->display_name,
        ])->toArray();
        $this->showRoleModal = true;
    }

    public function saveRoles(): void
    {
        $user = User::findOrFail($this->roleUserId);
        $roles = Role::whereIn('id', $this->formRoleIds)->get();
        $user->syncRoles($roles);
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
        $this->formUsername = '';
        $this->formName = '';
        $this->formPhone = '';
        $this->formEmail = '';
        $this->formPassword = '';
        $this->formStatus = 1;
    }

    public function render()
    {
        $query = User::with('roles')->orderBy('id', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('username', 'like', "%{$this->search}%")
                    ->orWhere('name', 'like', "%{$this->search}%")
                    ->orWhere('phone', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        $users = $query->paginate(20);

        return view('livewire.user.user-list', compact('users'))
            ->layout('components.app-layout')
            ->title('用户管理');
    }
}
