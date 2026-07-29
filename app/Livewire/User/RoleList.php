<?php

namespace App\Livewire\User;

use App\Models\Role;
use Livewire\Component;
use Livewire\WithPagination;

class RoleList extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?int $editingId = null;
    public string $formName = '';
    public string $formDisplayName = '';
    public string $formGuardName = 'web';
    public string $formDescription = '';

    public function mount(): void
    {
        //
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
            'formName' => 'required|string|max:50',
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

    public function delete(int $id): void
    {
        $role = Role::findOrFail($id);
        if ($role->users()->count() > 0) {
            $this->dispatch('toast', message: '该角色下有用户，不可删除', type: 'error');
            return;
        }
        $role->delete();
        $this->dispatch('toast', message: '角色已删除', type: 'success');
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
        $query = Role::withCount('users')->orderBy('id');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('display_name', 'like', "%{$this->search}%");
            });
        }

        $roles = $query->paginate(20);

        return view('livewire.user.role-list', compact('roles'))
            ->layout('components.app-layout')
            ->title('角色管理');
    }
}
