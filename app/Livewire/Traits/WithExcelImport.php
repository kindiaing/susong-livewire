<?php

namespace App\Livewire\Traits;

use App\Imports\GenericImport;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

/**
 * 列表页：Excel 导入 Trait
 * - 支持上传 xlsx/xls/csv 文件
 * - 自动映射列到模型字段
 * - 支持导入字段值转换
 * - 返回导入结果
 */
trait WithExcelImport
{
    use WithFileUploads;

    public bool $showImportModal = false;

    public $importFile = null;

    public string $importMessage = '';

    public array $importErrors = [];

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

        $this->importErrors = [];

        try {
            $import = new GenericImport(
                modelClass: $this->getImportModelClass(),
                columnMap: $this->getImportColumnMap(),
                uniqueBy: $this->getImportUniqueBy(),
                valueMap: $this->getImportValueMap(),
            );

            Excel::import($import, $this->importFile);

            $imported = $import->getImportedCount();
            $skipped = $import->getSkippedCount();
            $errors = $import->getErrorRows();

            $this->importErrors = $errors;

            $msg = "成功导入 {$imported} 条";
            if ($skipped > 0) {
                $msg .= "，跳过 {$skipped} 条";
            }
            if (! empty($errors)) {
                $msg .= '，失败 '.count($errors).' 条';
            }

            $this->importMessage = $msg;
            if (! empty($errors)) {
                $this->toastError($msg);
            } elseif ($skipped > 0) {
                $this->toastWarning($msg);
            } else {
                $this->toastSuccess($msg);
            }
        } catch (\Exception $e) {
            $this->importMessage = '导入失败：'.$e->getMessage();
            $this->toastError($this->importMessage);
        }

        $this->importFile = null;
    }

    /**
     * 组件覆盖：导入字段值转换映射
     * 格式：['status' => ['启用' => 1, '禁用' => 2]]
     */
    public function getImportValueMap(): array
    {
        return [];
    }

    /**
     * 关闭导入弹窗
     */
    public function closeImportModal(): void
    {
        $this->showImportModal = false;
        $this->importFile = null;
        $this->importMessage = '';
        $this->importErrors = [];
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
