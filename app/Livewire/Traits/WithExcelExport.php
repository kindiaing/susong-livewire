<?php

namespace App\Livewire\Traits;

use App\Exports\GenericExport;
use Maatwebsite\Excel\Facades\Excel;

/**
 * 列表页：Excel 导出 Trait
 * - 支持自定义选择导出列
 * - 支持 xlsx/csv 格式
 */
trait WithExcelExport
{
    public bool $showExportModal = false;

    public array $exportColumns = [];

    public string $exportFormat = 'xlsx';

    /**
     * 打开导出弹窗
     */
    public function openExportModal(): void
    {
        $this->exportColumns = collect($this->getExportableColumns())->pluck('key')->toArray();
        $this->showExportModal = true;
    }

    /**
     * 切换导出列勾选
     */
    public function toggleExportColumn(string $column): void
    {
        $idx = array_search($column, $this->exportColumns);
        if ($idx !== false) {
            unset($this->exportColumns[$idx]);
            $this->exportColumns = array_values($this->exportColumns);
        } else {
            $this->exportColumns[] = $column;
        }
    }

    /**
     * 关闭导出弹窗
     */
    public function closeExportModal(): void
    {
        $this->showExportModal = false;
    }

    /**
     * 导出全选
     */
    public function exportSelectAllColumns(): void
    {
        $this->exportColumns = collect($this->getExportableColumns())->pluck('key')->toArray();
    }

    /**
     * 从 getAllColumns() 中提取 type=money 的列 key 列表
     * 用于 GenericExport 金额列自动格式化（厘→元）
     */
    public function getMoneyColumns(): array
    {
        return collect($this->getAllColumns())
            ->filter(fn ($c) => ($c['type'] ?? '') === 'money')
            ->pluck('key')
            ->values()
            ->toArray();
    }

    /**
     * 执行导出
     */
    public function doExport()
    {
        if (empty($this->exportColumns)) {
            $this->toastError('请至少选择一列导出');

            return;
        }

        $moneyColumns = collect($this->getMoneyColumns())
            ->filter(fn ($key) => in_array($key, $this->exportColumns))
            ->values()
            ->toArray();

        $export = new GenericExport(
            queryOrData: $this->getExportQuery(),
            columns: $this->exportColumns,
            columnLabels: collect($this->getExportableColumns())
                ->filter(fn ($c) => in_array($c['key'], $this->exportColumns))
                ->pluck('label', 'key')
                ->toArray(),
            rowCallback: method_exists($this, 'getExportRowCallback') ? $this->getExportRowCallback() : null,
            moneyColumns: $moneyColumns,
        );

        $this->showExportModal = false;

        return Excel::download(
            $export,
            $this->getExportFileName().'.'.$this->exportFormat,
            $this->exportFormat === 'csv' ? \Maatwebsite\Excel\Excel::CSV : \Maatwebsite\Excel\Excel::XLSX,
        );
    }

    /**
     * 下载导入模板
     * 使用 getImportColumnMap() 的中文标签作为表头，确保与导入映射一致
     * 必填列表头红色标记
     */
    public function downloadImportTemplate()
    {
        $columnMap = $this->getImportColumnMap();
        if (empty($columnMap)) {
            $this->toastError('未配置导入列映射');
            return;
        }

        // 获取必填列中文名列表
        $requiredHeadings = method_exists($this, 'getImportRequiredFields')
            ? $this->getImportRequiredFields()
            : [];

        // columnMap 格式：['中文标签' => 'db_field', ...]
        // 传空数据数组 + 中文标签作为 headings，走 staticHeadings 分支
        $export = new GenericExport(
            queryOrData: [],
            columns: array_keys($columnMap),
            columnLabels: [],
            requiredHeadings: $requiredHeadings,
        );

        return Excel::download(
            $export,
            $this->getExportFileName().'_template.xlsx',
        );
    }

    /**
     * 组件覆盖：可导出列定义
     * 格式：[['key'=>'id','label'=>'ID'], ...]
     */
    public function getExportableColumns(): array
    {
        return collect($this->getAllColumns())->filter(fn ($c) => ($c['exportable'] ?? true))->values()->toArray();
    }

    /**
     * 组件覆盖：导出查询构建器
     */
    public function getExportQuery()
    {
        return null;
    }

    /**
     * 组件覆盖：导出文件名
     */
    public function getExportFileName(): string
    {
        return 'export_'.now()->format('Ymd_His');
    }
}
