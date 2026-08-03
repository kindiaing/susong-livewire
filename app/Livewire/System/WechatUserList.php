<?php

namespace App\Livewire\System;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Livewire\Traits\WithListCrud;
use App\Models\WechatUser;
use Livewire\Component;
use Livewire\WithPagination;

class WechatUserList extends Component
{
    use WithPagination;
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithToast;
    use WithListCrud;

    protected string $modelClass = WechatUser::class;

    public string $search = '';

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function getDefaultColumns(): array
    {
        return ['nickname', 'openid', 'phone', 'created_at'];
    }

    public function getExportRowCallback(): callable
    {
        return function ($row) {
            return [
                'id' => $row->id,
                'nickname' => $row->nickname ?? '',
                'openid' => $row->openid ?? '',
                'phone' => $row->phone ?? '',
                'created_at' => $row->created_at?->format('Y-m-d H:i:s'),
            ];
        };
    }

    public function getImportModelClass(): string
    {
        return WechatUser::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '昵称' => 'nickname',
            'OpenID' => 'openid',
            '手机号' => 'phone',
        ];
    }

    public function getImportUniqueBy(): array
    {
        return ['openid'];
    }

    public function getImportRequiredFields(): array
    {
        return ['OpenID'];
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'nickname', 'label' => '昵称', 'sortable' => false, 'exportable' => true],
            ['key' => 'openid', 'label' => 'OpenID', 'sortable' => false, 'exportable' => true],
            ['key' => 'phone', 'label' => '手机号', 'sortable' => false, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return WechatUser::when($this->search, function ($q) {
            $q->where('nickname', 'like', "%{$this->search}%");
        })->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '微信用户_' . now()->format('Ymd_His');
    }

    public function getPageIds(): array
    {
        return $this->getExportQuery()->forPage($this->page, 20)->pluck('id')->toArray();
    }

    public function delete(): void
    {
        WechatUser::findOrFail($this->deletingId)->delete();
        $this->toastSuccess('已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function render()
    {
        $query = WechatUser::orderBy('id', 'desc');

        if ($this->search) {
            $query->where('nickname', 'like', "%{$this->search}%");
        }

        $items = $query->paginate(setting('per_page', 10));

        return view('livewire.system.wechat-user-list', [
            'items' => $items,
            'allColumns' => $this->getAllColumns(),
            'selectedCount' => $this->getSelectedCount(),
        ])
            ->layout('components.app-layout')
            ->title('微信用户');
    }
}
