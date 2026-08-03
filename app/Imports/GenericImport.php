<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * 通用导入类
 * 读取 Excel 数据并批量创建模型记录
 */
class GenericImport implements ToCollection, WithHeadingRow
{
    protected string $modelClass;

    protected array $columnMap;

    protected array $uniqueBy;

    protected array $valueMap;

    protected array $moneyFields;

    protected int $importedCount = 0;

    protected int $skippedCount = 0;

    protected array $errorRows = [];

    public function __construct(string $modelClass, array $columnMap = [], array $uniqueBy = [], array $valueMap = [], array $moneyFields = [])
    {
        $this->modelClass = $modelClass;
        $this->columnMap = $columnMap;
        $this->uniqueBy = $uniqueBy;
        $this->valueMap = $valueMap;
        $this->moneyFields = $moneyFields;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            try {
                $data = [];
                foreach ($this->columnMap as $excelKey => $modelField) {
                    // 支持列名映射或字母索引映射
                    if (is_string($excelKey)) {
                        $value = $row[$excelKey] ?? $row[strtolower($excelKey)] ?? null;
                    } else {
                        $value = $row[$excelKey] ?? null;
                    }

                    if ($value !== null && $value !== '') {
                        // 应用字段值转换映射
                        if (isset($this->valueMap[$modelField])) {
                            $value = $this->valueMap[$modelField][(string) $value] ?? $value;
                        }
                        $data[$modelField] = $value;
                    }
                }

                // 金额字段元→厘转换
                foreach ($this->moneyFields as $field) {
                    if (isset($data[$field])) {
                        $data[$field] = money_to_cents($data[$field]);
                    }
                }

                if (empty($data)) {
                    $this->skippedCount++;

                    continue;
                }

                // 检查唯一性
                if (! empty($this->uniqueBy)) {
                    $exists = false;
                    $query = ($this->modelClass)::query();
                    foreach ($this->uniqueBy as $field) {
                        if (isset($data[$field])) {
                            $query->where($field, $data[$field]);
                        }
                    }
                    if ($query->exists()) {
                        $this->skippedCount++;

                        continue;
                    }
                }

                ($this->modelClass)::create($data);
                $this->importedCount++;
            } catch (\Exception $e) {
                $this->errorRows[] = [
                    'row' => $index + 2,
                    'error' => $e->getMessage(),
                ];
            }
        }
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }

    public function getErrorRows(): array
    {
        return $this->errorRows;
    }
}
