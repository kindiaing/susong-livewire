<?php

namespace App\Livewire\Finance;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithToast;
use App\Livewire\Traits\WithListCrud;
use App\Models\Merchant;
use App\Models\MerchantAccount;
use Illuminate\Database\Eloquent\Builder;
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
    use WithListCrud;

    protected string $modelClass = MerchantAccount::class;

    public string $search = '';

    // 筛选条件
    public int $filterApprovalStatus = -1;

    public string $filterDateStart = '';

    public string $filterDateEnd = '';

    public int $formMerchantId = 0;
    public int $formCreditLimit = 0;

    // 商家搜索
    public string $merchantSearch = '';
    public string $selectedMerchantName = '';

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
        return $this->applyFilters(MerchantAccount::query()->with('merchant'))
            ->orderBy('id', 'desc');
    }

    /**
     * 应用筛选条件（列表/导出共用）
     */
    /**
     * 应用筛选条件（列表/导出共用）
     *
     * @param Builder<MerchantAccount> $query
     * @return Builder<MerchantAccount>
     */
    private function applyFilters(Builder $query): Builder
    {
        if ($this->search) {
            // 支持英文逗号分隔多商家，命中任一关键词即显示
            $keywords = split_search_keywords($this->search);
            if ($keywords !== []) {
                $query->whereHas('merchant', function ($mq) use ($keywords) {
                    $mq->where(function ($q) use ($keywords) {
                        foreach ($keywords as $keyword) {
                            $q->orWhere('name', 'like', "%{$keyword}%");
                        }
                    });
                });
            }
        }

        if ($this->filterApprovalStatus > 0) {
            $query->where('approval_status', $this->filterApprovalStatus);
        }

        if ($this->filterDateStart !== '') {
            $query->whereDate('created_at', '>=', $this->filterDateStart);
        }
        if ($this->filterDateEnd !== '') {
            $query->whereDate('created_at', '<=', $this->filterDateEnd);
        }

        return $query;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterApprovalStatus = -1;
        $this->filterDateStart = '';
        $this->filterDateEnd = '';
        $this->resetPage();
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

    public function delete(): void
    {
        MerchantAccount::findOrFail($this->deletingId)->delete();
        $this->toastSuccess('客户账户已删除');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->formMerchantId = 0;
        $this->formCreditLimit = 0;
        $this->merchantSearch = '';
        $this->selectedMerchantName = '';
    }

    public function selectMerchant(int $id, string $name): void
    {
        $this->formMerchantId = $id;
        $this->merchantSearch = $name;
        $this->selectedMerchantName = $name;
    }

    public function render()
    {
        $query = $this->applyFilters(MerchantAccount::query()->with('merchant'))
            ->orderBy('id', 'desc');

        $items = $query->paginate(setting('per_page', 10));
        $merchantResults = collect();
        if (strlen($this->merchantSearch) >= 1 && !$this->editingId) {
            // 支持英文逗号分隔多商家：弹窗输入多个名称时一次查出全部候选
            $keywords = split_search_keywords($this->merchantSearch);
            if ($keywords !== []) {
                $merchantResults = Merchant::query()
                    ->where(function ($q) use ($keywords) {
                        foreach ($keywords as $keyword) {
                            $q->orWhere('name', 'like', "{$keyword}%");
                        }
                    })
                    ->orderBy('name')
                    ->limit(10)
                    ->get();
            }
        }
        $allColumns = $this->getAllColumns();
        $selectedCount = $this->getSelectedCount();
        return view('livewire.finance.merchant-account-list', compact('items', 'merchantResults', 'allColumns', 'selectedCount'))
            ->layout('components.app-layout')
            ->title('客户账户');
    }
}
