<?php

namespace App\Livewire\System;

use App\Livewire\Traits\WithListCrud;
use App\Livewire\Traits\WithToast;
use App\Models\Notification;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class NotificationList extends Component
{
    use WithPagination;
    use WithToast;
    use WithListCrud;

    public string $search = '';
    public string $typeFilter = '';

    // 表单字段
    public string $formTitle = '';
    public string $formContent = '';
    public int $formType = Notification::TYPE_SYSTEM;
    public string $formTarget = 'all'; // all=全站广播, user=指定用户
    public ?int $formUserId = null;

    protected function rules(): array
    {
        return [
            'formTitle'   => 'required|string|max:200',
            'formContent' => 'required|string|max:2000',
            'formType'    => 'required|integer',
            'formTarget'  => 'required|in:all,user',
            'formUserId'  => 'required_if:formTarget,user|nullable|integer|exists:users,id',
        ];
    }

    protected $validationAttributes = [
        'formTitle'   => '标题',
        'formContent' => '正文',
        'formUserId'  => '目标用户',
    ];

    public function resetForm(): void
    {
        $this->formTitle = '';
        $this->formContent = '';
        $this->formType = Notification::TYPE_SYSTEM;
        $this->formTarget = 'all';
        $this->formUserId = null;
        $this->editingId = null;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->formTarget === 'all') {
            Notification::broadcastToAll(
                $this->formTitle,
                $this->formContent,
                $this->formType
            );
            $this->toastSuccess('全站通知已发送');
        } else {
            Notification::sendToUser(
                $this->formUserId,
                $this->formTitle,
                $this->formContent,
                $this->formType
            );
            $this->toastSuccess('通知已发送给指定用户');
        }

        $this->closeModal();
    }

    public function delete(): void
    {
        abort_unless(auth()->user()?->can('system.notification.delete'), 403, '无删除权限');

        Notification::findOrFail($this->deletingId)->delete();
        $this->toastSuccess('已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->typeFilter = '';
        $this->resetPage();
    }

    public function render()
    {
        $query = Notification::latestFirst();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('content', 'like', "%{$this->search}%");
            });
        }

        if ($this->typeFilter !== '') {
            $query->where('type', (int) $this->typeFilter);
        }

        $items = $query->paginate(setting('per_page', 10));

        return view('livewire.system.notification-list', [
            'items'     => $items,
            'typeMap'   => Notification::typeMap(),
            'colorMap'  => Notification::typeColorMap(),
            'users'     => User::orderBy('name')->get(['id', 'name', 'username']),
        ])
            ->layout('components.app-layout')
            ->title('通知管理');
    }
}
