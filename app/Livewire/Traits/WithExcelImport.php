<?php

namespace App\Livewire\Traits;

use App\Imports\GenericImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Livewire\Traits\WithToast;

/**
 * 列表页：Excel 导入 Trait
 * - 支持上传 xlsx/xls/csv 文件
 * - 自动映射列到模型字段
 * - 返回导入结果
 */
trait WithExcelImport
{
    use WithToast;
    public bool $showImportModal = false;
    public $importFile = null;
    public string $importMessage = '';

    /**
     * 打开导入弹窗
     */
    public function openImportModal(): void
    {
        $this->importFile = null;
        $this->importMessage = '';
        $this->showImportModal = true;
    }

    /**
     * 执行导入
     */
    public function doImport(): void
    {
        $this->validate([
            'importFile' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $import = new GenericImport(
                modelClass: $this->getImportModelClass(),
                columnMap: $this->getImportColumnMap(),
                uniqueBy: $this->getImportUniqueBy(),
            );

            Excel::import($import, $this->importFile);

            $imported = $import->getImportedCount();
            $skipped = $import->getSkippedCount();
            $errors = $import->getErrorRows();

            $msg = "成功导入 {$imported} 条";
            if ($skipped > 0) {
                $msg .= "，跳过 {$skipped} 条";
            }
            if (!empty($errors)) {
                $msg .= "，失败 " . count($errors) . " 条";
            }

            $this->importMessage = $msg;
            if ($skipped > 0 || !empty($errors)) {
                $this->toastWarning($msg);
            } else {
                $this->toastSuccess($msg);
            }
        } catch (\Exception $e) {
            $this->importMessage = '导入失败：' . $e->getMessage();
            $this->toastError($this->importMessage);
        }

        $this->importFile = null;
    }

    /**
     * 组件覆盖：导入目标模型类
     */
    public function getImportModelClass(): string
    {
        return '';
    }

    /**
     * 组件覆盖：Excel 列名 → 模型字段映射
     * ['A' => 'name', 'B' => 'phone', ...]
     * 或 ['姓名' => 'name', '手机号' => 'phone', ...]
     */
    public function getImportColumnMap(): array
    {
        return [];
    }

    /**
     * 组件覆盖：唯一键字段（用于跳过重复）
     */
    public function getImportUniqueBy(): array
    {
        return [];
    }
}
