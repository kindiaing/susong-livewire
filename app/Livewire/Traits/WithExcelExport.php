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
     * 导出全选
     */
    public function exportSelectAllColumns(): void
    {
        $this->exportColumns = collect($this->getExportableColumns())->pluck('key')->toArray();
    }

    /**
     * 执行导出
     */
    public function doExport()
    {
        if (empty($this->exportColumns)) {
            $this->dispatch('toast', message: '请至少选择一列导出', type: 'error');
            return;
        }

        $export = new GenericExport(
            query: $this->getExportQuery(),
            columns: $this->exportColumns,
            columnLabels: collect($this->getExportableColumns())
                ->filter(fn($c) => in_array($c['key'], $this->exportColumns))
                ->pluck('label', 'key')
                ->toArray(),
            rowCallback: method_exists($this, 'getExportRowCallback') ? $this->getExportRowCallback() : null,
        );

        $this->showExportModal = false;

        return Excel::download(
            $export,
            $this->getExportFileName() . '.' . $this->exportFormat,
            $this->exportFormat === 'csv' ? \Maatwebsite\Excel\Excel::CSV : \Maatwebsite\Excel\Excel::XLSX,
        );
    }

    /**
     * 下载导入模板
     */
    public function downloadImportTemplate()
    {
        $columns = collect($this->getExportableColumns())->pluck('label', 'key')->toArray();
        $export = new GenericExport(query: null, columns: array_keys($columns), columnLabels: $columns);

        return Excel::download(
            $export,
            $this->getExportFileName() . '_template.xlsx',
        );
    }

    /**
     * 组件覆盖：可导出列定义
     * 格式：[['key'=>'id','label'=>'ID'], ...]
     */
    public function getExportableColumns(): array
    {
        return collect($this->getAllColumns())->filter(fn($c) => ($c['exportable'] ?? true))->values()->toArray();
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
        return 'export_' . now()->format('Ymd_His');
    }
}
