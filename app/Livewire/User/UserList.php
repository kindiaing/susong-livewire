<?php

namespace App\Livewire\User;

use App\Models\Role;
use App\Models\User;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithToast;
use Livewire\Component;
use Livewire\WithPagination;

class UserList extends Component
{
    use WithPagination;
    use WithRowSelection, WithColumnVisibility, WithExcelExport, WithExcelImport;
    use WithToast;

    protected string $modelClass = User::class;

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
            $this->toastSuccess('用户已更新');
        } else {
            User::create($data);
            $this->toastSuccess('用户已创建');
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
        if ($user->hasRole('super_admin')) {
            $this->toastError('超级管理员不可删除');
            $this->showDeleteConfirm = false;
            return;
        }
        if ($user->id === auth()->id()) {
            $this->toastError('不能删除当前登录用户');
            $this->showDeleteConfirm = false;
            return;
        }
        $user->delete();
        $this->toastSuccess('用户已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function toggleStatus(int $id): void
    {
        $user = User::findOrFail($id);
        if ($user->hasRole('super_admin')) {
            $this->toastError('超级管理员不可禁用');
            return;
        }
        if ($user->id === auth()->id()) {
            $this->toastError('不能禁用当前登录用户');
            return;
        }
        $user->status = $user->status === 1 ? 0 : 1;
        $user->save();
        $label = $user->status === 1 ? '启用' : '禁用';
        $this->toastSuccess("用户已{$label}");
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
        $this->toastSuccess('密码已重置为 Password');
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
        $this->toastSuccess('角色分配已更新');
        $this->showRoleModal = false;
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

    public function closeResetConfirm(): void
    {
        $this->showResetConfirm = false;
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
        $this->formUsername = '';
        $this->formName = '';
        $this->formPhone = '';
        $this->formEmail = '';
        $this->formPassword = '';
        $this->formStatus = 1;
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'username', 'label' => '用户名', 'sortable' => true, 'exportable' => true],
            ['key' => 'name', 'label' => '姓名', 'sortable' => true, 'exportable' => true],
            ['key' => 'phone', 'label' => '手机号', 'sortable' => true, 'exportable' => true],
            ['key' => 'email', 'label' => '邮箱', 'sortable' => true, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => true, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return User::with('roles')->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '用户管理_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return User::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '用户名' => 'username',
            '姓名' => 'name',
            '手机号' => 'phone',
            '邮箱' => 'email',
        ];
    }

    public function getPageIds(): array
    {
        return User::orderBy('id', 'desc')->limit(20)->pluck('id')->toArray();
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
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();

        return view('livewire.user.user-list', compact('users', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('用户管理');
    }
}
