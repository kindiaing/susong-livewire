<?php

namespace App\Livewire\Product;

use App\Livewire\Traits\WithColumnVisibility;
use App\Livewire\Traits\WithExcelExport;
use App\Livewire\Traits\WithExcelImport;
use App\Livewire\Traits\WithRowSelection;
use App\Livewire\Traits\WithMoneyConversion;
use App\Livewire\Traits\WithToast;
use App\Livewire\Traits\WithListCrud;
use App\Models\Product;
use App\Models\Sku;
use App\Models\Unit;
use App\Models\UnitConversion;
use App\Services\ApprovalService;
use App\Services\PricingService;
use App\Services\UnitConversionService;
use App\Models\PriceChangeLog;
use Livewire\Component;
use Livewire\WithPagination;

class SkuList extends Component
{
    use WithColumnVisibility;
    use WithExcelExport;
    use WithExcelImport;
    use WithMoneyConversion;
    use WithPagination;
    use WithRowSelection;
    use WithToast;
    use WithListCrud;

    protected string $modelClass = Sku::class;

    public string $search = '';

    public int $filterStatus = -1;

    public int $filterApprovalStatus = -1;

    public int $formProductId = 0;

    public string $formSkuCode = '';

    public string $formSpecs = '';

    public string $formPurchasePrice = '';

    public string $formWholesalePrice = '';

    public string $formCostPrice = '';

    public string $formMinPurchasePrice = '';

    public string $formListPrice = '';

    public string $formRetailPrice = '';

    public string $formEmployeePrice = '';

    public string $formOfflinePrice = '';

    public string $formMiniappPrice = '';

    public string $formDeliveryPrice = '';

    public string $formMinSalePrice = '';

    public string $formMaxSalePrice = '';

    public int $formStatus = 1;

    // Tab 切换
    public string $activeTab = 'basic';

    // 换算配置
    public ?int $formBaseUnitId = null;
    public array $formConversions = []; // [{from_unit_id, to_unit_id, ratio}]

    public function mount(): void
    {
        $this->initColumnVisibility();
    }

    public function openEditModal(int $id): void
    {
        $sku = Sku::findOrFail($id);
        $this->editingId = $id;
        $this->formProductId = $sku->product_id;
        $this->formSkuCode = $sku->sku_code;
        $this->formSpecs = $sku->specs ? json_encode($sku->specs, JSON_UNESCAPED_UNICODE) : '';
        $this->formPurchasePrice = $this->centsToYuan($sku->purchase_price);
        $this->formWholesalePrice = $this->centsToYuan($sku->wholesale_price);
        $this->formCostPrice = $this->centsToYuan($sku->cost_price);
        $this->formMinPurchasePrice = $this->centsToYuan($sku->min_purchase_price);
        $this->formListPrice = $this->centsToYuan($sku->list_price);
        $this->formRetailPrice = $this->centsToYuan($sku->retail_price);
        $this->formEmployeePrice = $this->centsToYuan($sku->employee_price);
        $this->formOfflinePrice = $this->centsToYuan($sku->offline_price);
        $this->formMiniappPrice = $this->centsToYuan($sku->miniapp_price);
        $this->formDeliveryPrice = $this->centsToYuan($sku->delivery_price);
        $this->formMinSalePrice = $this->centsToYuan($sku->min_sale_price);
        $this->formMaxSalePrice = $this->centsToYuan($sku->max_sale_price);
        $this->formStatus = $sku->status;

        // 换算配置
        $this->formBaseUnitId = $sku->base_unit_id;
        $this->loadConversions($sku);
        $this->activeTab = 'basic';
        $this->showModal = true;
    }

    /**
     * 加载 SKU 的换算链路数据到表单
     */
    private function loadConversions(Sku $sku): void
    {
        $conversions = UnitConversion::where('sku_id', $sku->id)
            ->where('status', UnitConversion::STATUS_ENABLED)
            ->orderBy('sort')
            ->get();

        // 找链路起点
        $startConversion = $conversions->first(fn($c) => $c->parent_conversion_id === null)
            ?? $conversions->first();

        $this->formConversions = [];
        if ($startConversion) {
            $current = $startConversion;
            while ($current) {
                $this->formConversions[] = [
                    'id' => $current->id,
                    'from_unit_id' => (string) $current->from_unit_id,
                    'to_unit_id' => (string) $current->to_unit_id,
                    'ratio' => (string) $current->ratio,
                ];
                $child = $conversions->first(fn($c) => $c->parent_conversion_id === $current->id);
                $current = $child ?: null;
            }
        }
    }

    public function save(): void
    {
        $validated = $this->validate([
            'formProductId' => 'required|integer|min:1|exists:products,id',
            'formSkuCode' => 'required|string|max:50',
            'formSpecs' => 'nullable|string',
            'formPurchasePrice' => 'required|numeric|min:0',
            'formWholesalePrice' => 'required|numeric|min:0',
            'formCostPrice' => 'required|numeric|min:0',
            'formMinPurchasePrice' => 'nullable|numeric|min:0',
            'formListPrice' => 'nullable|numeric|min:0',
            'formRetailPrice' => 'nullable|numeric|min:0',
            'formEmployeePrice' => 'nullable|numeric|min:0',
            'formOfflinePrice' => 'nullable|numeric|min:0',
            'formMiniappPrice' => 'nullable|numeric|min:0',
            'formDeliveryPrice' => 'nullable|numeric|min:0',
            'formMinSalePrice' => 'nullable|numeric|min:0',
            'formMaxSalePrice' => 'nullable|numeric|min:0',
            'formStatus' => 'required|in:0,1',
        ]);

        $specs = $validated['formSpecs'] ? json_decode($validated['formSpecs'], true) : null;

        $data = [
            'product_id' => $validated['formProductId'],
            'sku_code' => $validated['formSkuCode'],
            'specs' => $specs,
            'purchase_price' => money_to_cents($validated['formPurchasePrice']),
            'wholesale_price' => money_to_cents($validated['formWholesalePrice']),
            'cost_price' => money_to_cents($validated['formCostPrice']),
            'min_purchase_price' => money_to_cents($validated['formMinPurchasePrice'] ?: 0),
            'list_price' => money_to_cents($validated['formListPrice'] ?: 0),
            'retail_price' => money_to_cents($validated['formRetailPrice'] ?: 0),
            'employee_price' => money_to_cents($validated['formEmployeePrice'] ?: 0),
            'offline_price' => money_to_cents($validated['formOfflinePrice'] ?: 0),
            'miniapp_price' => money_to_cents($validated['formMiniappPrice'] ?: 0),
            'delivery_price' => money_to_cents($validated['formDeliveryPrice'] ?: 0),
            'min_sale_price' => money_to_cents($validated['formMinSalePrice'] ?: 0),
            'max_sale_price' => money_to_cents($validated['formMaxSalePrice'] ?: 0),
            'status' => $validated['formStatus'],
        ];

        if ($this->editingId) {
            $sku = Sku::findOrFail($this->editingId);

            // 批发价变动幅度检测
            $newWholesaleCents = $data['wholesale_price'];
            $oldWholesaleCents = $sku->wholesale_price;

            if ($oldWholesaleCents > 0 && $newWholesaleCents !== $oldWholesaleCents) {
                $changeRatio = abs($newWholesaleCents - $oldWholesaleCents) / $oldWholesaleCents;

                if ($changeRatio > 0.15) {
                    // 变动幅度 >15%，触发审核
                    $approval = ApprovalService::createRequest(
                        typeCode: 'sku_price_change',
                        targetType: 'sku',
                        targetId: $sku->id,
                        beforeData: [
                            'wholesale_price' => $oldWholesaleCents,
                            'wholesale_price_display' => money_format($oldWholesaleCents),
                        ],
                        afterData: [
                            'wholesale_price' => $newWholesaleCents,
                            'wholesale_price_display' => money_format($newWholesaleCents),
                            'change_ratio' => round($changeRatio * 100, 2) . '%',
                        ],
                    );

                    if ($approval !== null) {
                        // 审核已开启且审核单已创建，暂不写库，等审核通过
                        $this->toastInfo('批发价变动幅度超过15%，已提交审核，审核通过后生效');
                        $this->showModal = false;
                        $this->resetForm();
                        return;
                    }

                    // 审核已关闭（ApprovalService 返回 null），直接执行但写日志
                }
            }

            // 执行更新
            $sku->update($data);

            // 保存换算配置
            if ($this->formBaseUnitId) {
                $sku->update(['base_unit_id' => $this->formBaseUnitId]);
            }
            $this->saveConversions($sku);

            // 写改价日志（编辑且批发价有变动时）
            if ($oldWholesaleCents > 0 && $newWholesaleCents !== $oldWholesaleCents) {
                $pricingService = new PricingService();
                $pricingService->logPriceChange(
                    sku: $sku,
                    originalPrice: $oldWholesaleCents,
                    newPrice: $newWholesaleCents,
                    sourceType: PriceChangeLog::SOURCE_MANUAL,
                    targetType: PriceChangeLog::TARGET_ORDER,
                );
            }

            $this->toastSuccess('SKU已更新');
        } else {
            // 创建 SKU
            $sku = Sku::create($data);

            // 保存换算配置（创建时也可配置）
            if ($this->formBaseUnitId) {
                $sku->update(['base_unit_id' => $this->formBaseUnitId]);
            }
            $this->saveConversions($sku);

            $this->toastSuccess('SKU已创建');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    /**
     * 是否为超级管理员/管理员（可删除、可状态回退）
     */
    public function getIsSuperAdminProperty(): bool
    {
        return can_rollback_status();
    }

    /**
     * 禁用/启用 SKU（非管理员不可删除，仅可禁用）
     */
    public function toggleStatus(int $id): void
    {
        $sku = Sku::findOrFail($id);
        $sku->status = $sku->status === Sku::STATUS_ENABLED ? Sku::STATUS_DISABLED : Sku::STATUS_ENABLED;
        $sku->save();
        $this->toastSuccess($sku->status === Sku::STATUS_ENABLED ? 'SKU已启用' : 'SKU已禁用');
    }

    public function confirmDelete(int $id): void
    {
        // 仅管理员可删除
        if (!$this->isSuperAdmin) {
            $this->toastError('仅管理员可删除SKU，如需停用请使用禁用功能');
            return;
        }

        $sku = Sku::findOrFail($id);
        $warnings = [];

        if ($sku->barcodes()->exists()) {
            $warnings[] = "该SKU关联有 {$sku->barcodes()->count()} 个条码";
        }
        if ($sku->skuSuppliers()->exists()) {
            $warnings[] = "该SKU关联有 {$sku->skuSuppliers()->count()} 个供应商";
        }
        if ($sku->merchantVisibilities()->exists()) {
            $warnings[] = "该SKU关联有 {$sku->merchantVisibilities()->count()} 条可见性配置";
        }

        if (count($warnings) > 0) {
            $this->deleteWarning = implode('，', $warnings) . '。请先移除关联数据后再删除。';
            $this->canDelete = false;
        } else {
            $this->deleteWarning = '确定要删除该SKU吗？删除后将软删除，可从回收站恢复。';
            $this->canDelete = true;
        }

        $this->deletingId = $id;
        $this->showDeleteConfirm = true;
    }

    public function delete(): void
    {
        // 仅管理员可删除
        if (!$this->isSuperAdmin) {
            $this->toastError('仅管理员可删除SKU');
            return;
        }

        if (!$this->canDelete) {
            $this->toastWarning('无法删除，请先移除关联数据');
            return;
        }

        // 软删除
        Sku::findOrFail($this->deletingId)->delete();
        $this->toastSuccess('SKU已删除（软删除，可恢复）');
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
        $this->deleteWarning = '';
        $this->canDelete = true;
    }

    /**
     * 批量删除（覆盖 Trait，仅管理员可操作）
     */
    public function batchDelete(): void
    {
        if (!$this->isSuperAdmin) {
            $this->toastError('仅管理员可删除SKU');
            return;
        }

        if (empty($this->selectedIds)) {
            $this->toastError('请先选择数据');
            return;
        }

        Sku::destroy($this->selectedIds);
        $count = count($this->selectedIds);
        $this->clearSelection();
        $this->toastSuccess("已删除 {$count} 条SKU（软删除，可恢复）");
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterStatus = -1;
        $this->filterApprovalStatus = -1;
        $this->resetPage();
        $this->clearSelection();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->formProductId = 0;
        $this->formSkuCode = '';
        $this->formSpecs = '';
        $this->formPurchasePrice = '';
        $this->formWholesalePrice = '';
        $this->formCostPrice = '';
        $this->formMinPurchasePrice = '';
        $this->formListPrice = '';
        $this->formRetailPrice = '';
        $this->formEmployeePrice = '';
        $this->formOfflinePrice = '';
        $this->formMiniappPrice = '';
        $this->formDeliveryPrice = '';
        $this->formMinSalePrice = '';
        $this->formMaxSalePrice = '';
        $this->formStatus = 1;
        $this->activeTab = 'basic';
        $this->formBaseUnitId = null;
        $this->formConversions = [];
    }

    /**
     * 保存换算链路
     */
    private function saveConversions(Sku $sku): void
    {
        // 删除旧的换算关系
        UnitConversion::where('sku_id', $sku->id)->delete();

        // 清除缓存
        app(UnitConversionService::class)->clearCache($sku->id);

        // 创建新的换算链路
        $parentConversionId = null;
        foreach ($this->formConversions as $i => $row) {
            $fromUnitId = (int) ($row['from_unit_id'] ?? 0);
            $toUnitId = (int) ($row['to_unit_id'] ?? 0);
            $ratio = (int) ($row['ratio'] ?? 0);

            if ($fromUnitId && $toUnitId && $ratio > 0 && $fromUnitId !== $toUnitId) {
                $conversion = UnitConversion::create([
                    'sku_id' => $sku->id,
                    'from_unit_id' => $fromUnitId,
                    'to_unit_id' => $toUnitId,
                    'ratio' => $ratio,
                    'parent_conversion_id' => $parentConversionId,
                    'status' => UnitConversion::STATUS_ENABLED,
                    'sort' => $i,
                ]);
                $parentConversionId = $conversion->id;
            }
        }
    }

    /**
     * 添加换算行
     */
    public function addConversionRow(): void
    {
        $this->formConversions[] = [
            'id' => null,
            'from_unit_id' => '',
            'to_unit_id' => '',
            'ratio' => '',
        ];
    }

    /**
     * 删除换算行
     */
    public function removeConversionRow(int $index): void
    {
        unset($this->formConversions[$index]);
        $this->formConversions = array_values($this->formConversions);
    }

    /**
     * 获取换算链路预览文本
     */
    public function getConversionPreviewProperty(): string
    {
        if (empty($this->formConversions)) {
            return '未配置换算关系';
        }

        $parts = [];
        foreach ($this->formConversions as $row) {
            $fromUnit = Unit::find((int) ($row['from_unit_id'] ?? 0));
            $toUnit = Unit::find((int) ($row['to_unit_id'] ?? 0));
            $ratio = $row['ratio'] ?? 0;

            if ($fromUnit && $toUnit && $ratio > 0) {
                $parts[] = "1{$fromUnit->name}={$ratio}{$toUnit->name}";
            }
        }

        return $parts ? implode(' → ', $parts) : '未配置换算关系';
    }

    public function getAllColumns(): array
    {
        return [
            ['key' => 'id', 'label' => 'ID', 'sortable' => true, 'exportable' => true],
            ['key' => 'product_id', 'label' => '商品', 'sortable' => false, 'exportable' => true],
            ['key' => 'sku_code', 'label' => 'SKU编码', 'sortable' => true, 'exportable' => true],
            ['key' => 'purchase_price', 'label' => '采购价', 'sortable' => false, 'exportable' => true, 'type' => 'money'],
            ['key' => 'cost_price', 'label' => '成本价', 'sortable' => false, 'exportable' => true, 'type' => 'money'],
            ['key' => 'min_purchase_price', 'label' => '最低采购限价', 'sortable' => false, 'exportable' => true, 'type' => 'money'],
            ['key' => 'list_price', 'label' => '吊牌价', 'sortable' => false, 'exportable' => true, 'type' => 'money'],
            ['key' => 'retail_price', 'label' => '零售价', 'sortable' => false, 'exportable' => true, 'type' => 'money'],
            ['key' => 'wholesale_price', 'label' => '批发价', 'sortable' => false, 'exportable' => true, 'type' => 'money'],
            ['key' => 'employee_price', 'label' => '员工价', 'sortable' => false, 'exportable' => true, 'type' => 'money'],
            ['key' => 'offline_price', 'label' => '门店价', 'sortable' => false, 'exportable' => true, 'type' => 'money'],
            ['key' => 'miniapp_price', 'label' => '小程序价', 'sortable' => false, 'exportable' => true, 'type' => 'money'],
            ['key' => 'delivery_price', 'label' => '配送价', 'sortable' => false, 'exportable' => true, 'type' => 'money'],
            ['key' => 'min_sale_price', 'label' => '最低销售限价', 'sortable' => false, 'exportable' => true, 'type' => 'money'],
            ['key' => 'max_sale_price', 'label' => '最高销售限价', 'sortable' => false, 'exportable' => true, 'type' => 'money'],
            ['key' => 'stock', 'label' => '库存', 'sortable' => true, 'exportable' => true],
            ['key' => 'approval_status', 'label' => '审核状态', 'sortable' => false, 'exportable' => true],
            ['key' => 'status', 'label' => '状态', 'sortable' => false, 'exportable' => true],
        ];
    }

    public function getExportQuery()
    {
        return Sku::with('product')
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('sku_code', 'like', "%{$this->search}%")
                        ->orWhereHas('product', function ($pq) {
                            $pq->where('name', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->filterStatus >= 0, function ($q) {
                $q->where('status', $this->filterStatus);
            })
            ->when($this->filterApprovalStatus > 0, function ($q) {
                $q->where('approval_status', $this->filterApprovalStatus);
            })
            ->orderBy('id', 'desc');
    }

    public function getExportFileName(): string
    {
        return 'SKU_'.now()->format('Ymd_His');
    }

    public function getImportModelClass(): string
    {
        return Sku::class;
    }

    public function getImportColumnMap(): array
    {
        return [
            '商品ID' => 'product_id',
            'SKU编码' => 'sku_code',
            '采购价(元)' => 'purchase_price',
            '成本价(元)' => 'cost_price',
            '最低采购限价(元)' => 'min_purchase_price',
            '吊牌价(元)' => 'list_price',
            '零售价(元)' => 'retail_price',
            '批发价(元)' => 'wholesale_price',
            '员工价(元)' => 'employee_price',
            '门店价(元)' => 'offline_price',
            '小程序价(元)' => 'miniapp_price',
            '配送价(元)' => 'delivery_price',
            '最低销售限价(元)' => 'min_sale_price',
            '最高销售限价(元)' => 'max_sale_price',
            '状态' => 'status',
        ];
    }

    public function getPageIds(): array
    {
        return $this->getExportQuery()->forPage($this->getPage(), 20)->pluck('id')->toArray();
    }

    public function getImportMoneyFields(): array
    {
        return [
            'purchase_price', 'cost_price', 'min_purchase_price',
            'list_price', 'retail_price', 'wholesale_price', 'employee_price',
            'offline_price', 'miniapp_price', 'delivery_price',
            'min_sale_price', 'max_sale_price',
        ];
    }

    public function render()
    {
        $query = Sku::with('product')->orderBy('id', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('sku_code', 'like', "%{$this->search}%")
                    ->orWhereHas('product', function ($pq) {
                        $pq->where('name', 'like', "%{$this->search}%");
                    });
            });
        }

        if ($this->filterStatus >= 0) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterApprovalStatus > 0) {
            $query->where('approval_status', $this->filterApprovalStatus);
        }

        $skus = $query->paginate(setting('per_page', 10));
        $allColumns = $this->getAllColumns();
        $selectedCount = count($this->selectedIds);

        $productOptions = Product::orderBy('name')->get()->map(fn($p) => ['value' => $p->id, 'label' => $p->name])->toArray();
        $unitOptions = Unit::enabled()->orderBy('sort')->get(['id', 'name']);

        $conversionPreview = $this->conversionPreview;

        $isSuperAdmin = $this->isSuperAdmin;

        return view('livewire.product.sku-list', compact('skus', 'allColumns', 'selectedCount', 'productOptions', 'unitOptions', 'conversionPreview', 'isSuperAdmin'))
            ->layout('components.app-layout')
            ->title('SKU管理');
    }
}
