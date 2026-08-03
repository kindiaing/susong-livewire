<?php

namespace App\Livewire\Order;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Livewire\Traits\WithListCrud;
use App\Models\Merchant;
use App\Models\RepurchaseTemplate;
use Livewire\Component;
use Livewire\WithPagination;

class RepurchaseTemplateList extends Component
{
    use WithPagination;
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithToast;
    use WithListCrud;

    protected string $modelClass = RepurchaseTemplate::class;

    public string $search = '';

    public int $formMerchantId = 0;
    public string $formName = '';
    public int $formStatus = 1;

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function getDefaultColumns(): array
    {
        return ['name', 'merchant', 'status', 'created_at'];
    }

    public function getExportRowCallback(): callable
    {
        return function ($row) {
            return [
                'id' => $row->id,
                'name' => $row->name ?? '',
                'merchant' => $row->merchant?->name ?? '',
                'status' => $row->status,
                'created_at' => $row->created_at?->format('Y-m-d H:i:s'),
            ];
        };
    }

    public function getImportUniqueBy(): array
    {
        return ['id'];
    }

    public function getImportRequiredFields(): array
    {
        return ['商家ID', '模板名称'];
    }

    public function getImportValueMap(): array
    {
        return [
            'status' => ['禁用' => 0, '启用' => 1],
        ];
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'name', 'label' => '模板名', 'sortable' => false, 'exportable' => true],
            ['key' => 'merchant', 'label' => '商家', 'sortable' => false, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => false, 'exportable' => true],
            ['key' => 'created_at', 'label' => '创建时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return RepurchaseTemplate::with('merchant')
            ->when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%");
            })->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '复购模板_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return RepurchaseTemplate::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '商家ID' => 'merchant_id',
            '模板名称' => 'name',
            '状态' => 'status',
        ];
    }

    public function getPageIds(): array
    {
        return $this->getExportQuery()->forPage($this->page, 20)->pluck('id')->toArray();
    }

    public function closeColumnModal(): void
    {
        $this->showColumnModal = false;
    }

    public function closeExportModal(): void
    {
        $this->showExportModal = false;
    }

    public function closeImportModal(): void
    {
        $this->showImportModal = false;
        $this->importFile = null;
        $this->importMessage = '';
    }

    public function openEditModal(int $id): void
    {
        $tpl = RepurchaseTemplate::findOrFail($id);
        $this->editingId = $id;
        $this->formMerchantId = $tpl->merchant_id;
        $this->formName = $tpl->name;
        $this->formStatus = $tpl->status;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'formMerchantId' => 'required|integer|min:1|exists:merchants,id',
            'formName' => 'required|string|max:50',
            'formStatus' => 'required|in:0,1',
        ]);

        $data = [
            'merchant_id' => $validated['formMerchantId'],
            'name' => $validated['formName'],
            'status' => $validated['formStatus'],
        ];

        if ($this->editingId) {
            RepurchaseTemplate::findOrFail($this->editingId)->update($data);
            $this->toastSuccess('复购模板已更新');
        } else {
            RepurchaseTemplate::create($data);
            $this->toastSuccess('复购模板已创建');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(): void
    {
        RepurchaseTemplate::findOrFail($this->deletingId)->delete();
        $this->toastSuccess('复购模板已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->formMerchantId = 0;
        $this->formName = '';
        $this->formStatus = 1;
    }

    public function render()
    {
        $query = RepurchaseTemplate::with('merchant')->orderBy('id', 'desc');

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
        }

        $templates = $query->paginate(setting('per_page', 10));

        $merchantOptions = Merchant::orderBy('name')->get()->map(fn($m) => ['value' => $m->id, 'label' => $m->name])->toArray();

        return view('livewire.order.repurchase-template-list', [
            'templates' => $templates,
            'allColumns' => $this->getAllColumns(),
            'selectedCount' => $this->getSelectedCount(),
            'merchantOptions' => $merchantOptions,
        ])
            ->layout('components.app-layout')
            ->title('复购模板');
    }
}
