<?php

namespace App\Livewire\Finance;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Models\Merchant;
use App\Models\MerchantAccount;
use Livewire\Component;
use Livewire\WithPagination;

class MerchantAccountList extends Component
{
    use WithPagination;
    use WithRowSelection;
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithToast;

    protected string $modelClass = MerchantAccount::class;

    public string $search = '';
    public bool $showModal = false;
    public bool $showDeleteConfirm = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    public int $formMerchantId = 0;
    public int $formCreditLimit = 0;

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'merchant', 'label' => '商家', 'sortable' => false, 'exportable' => true],
            ['key' => 'balance', 'label' => '余额', 'sortable' => true, 'exportable' => true],
            ['key' => 'credit_limit', 'label' => '信用额度', 'sortable' => true, 'exportable' => true],
            ['key' => 'frozen_amount', 'label' => '冻结金额', 'sortable' => true, 'exportable' => true],
            ['key' => 'updated_at', 'label' => '更新时间', 'sortable' => true, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return MerchantAccount::with('merchant')
            ->when($this->search, function ($q) {
                $q->whereHas('merchant', fn($mq) => $mq->where('name', 'like', "%{$this->search}%"));
            })
            ->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return '客户账户_' . now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return MerchantAccount::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '商家ID' => 'merchant_id',
            '信用额度(分)' => 'credit_limit',
        ];
    }

    public function getPageIds(): array
    {
        return $this->getExportQuery()->forPage($this->getPage(), 20)->pluck('id')->toArray();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $item = MerchantAccount::findOrFail($id);
        $this->editingId = $id;
        $this->formMerchantId = $item->merchant_id;
        $this->formCreditLimit = $item->credit_limit;
        $this->showModal = true;
    }

    public function save(): void
    {
        if ($this->editingId) {
            $this->validate([
                'formCreditLimit' => 'required|integer|min:0',
            ]);
            MerchantAccount::findOrFail($this->editingId)->update([
                'credit_limit' => $this->formCreditLimit,
            ]);
            $this->toastSuccess('客户账户已更新');
        } else {
            $this->validate([
                'formMerchantId' => 'required|integer|exists:merchants,id',
                'formCreditLimit' => 'required|integer|min:0',
            ]);
            MerchantAccount::create([
                'merchant_id' => $this->formMerchantId,
                'credit_limit' => $this->formCreditLimit,
                'balance' => 0,
                'frozen_amount' => 0,
            ]);
            $this->toastSuccess('客户账户已创建');
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
        MerchantAccount::findOrFail($this->deletingId)->delete();
        $this->toastSuccess('客户账户已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->resetPage();
        $this->clearSelection();
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

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->formMerchantId = 0;
        $this->formCreditLimit = 0;
    }

    public function render()
    {
        $query = MerchantAccount::with('merchant')->orderBy('id', 'desc');

        if ($this->search) {
            $query->whereHas('merchant', fn($q) => $q->where('name', 'like', "%{$this->search}%"));
        }

        $items = $query->paginate(setting('per_page', 10));
        $merchants = Merchant::orderBy('name')->get();
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();

        return view('livewire.finance.merchant-account-list', compact('items', 'merchants', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('客户账户');
    }
}
