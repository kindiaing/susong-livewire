<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

/**
 * 通用导出类
 * 接受查询构建器、列定义、列标签，生成 Excel 文件
 */
class GenericExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected ?Builder $query;
    protected array $columns;
    protected array $columnLabels;
    protected $rowCallback;
    protected ?array $staticData;
    protected ?array $staticHeadings;

    public function __construct(
        $queryOrData = null,
        array $columns = [],
        array $columnLabels = [],
        ?callable $rowCallback = null,
    ) {
        // 支持纯数组数据（用于非模型导出）
        if (is_array($queryOrData)) {
            $this->staticData = $queryOrData;
            $this->staticHeadings = $columns;
            $this->query = null;
            $this->columns = [];
            $this->columnLabels = [];
            $this->rowCallback = null;
        } else {
            $this->query = $queryOrData;
            $this->columns = $columns;
            $this->columnLabels = $columnLabels;
            $this->rowCallback = $rowCallback;
            $this->staticData = null;
            $this->staticHeadings = null;
        }
    }

    public function collection()
    {
        if ($this->staticData !== null) {
            return collect($this->staticData);
        }
        if (!$this->query) {
            return collect([]);
        }
        return $this->query->get();
    }

    public function headings(): array
    {
        if ($this->staticHeadings !== null) {
            return $this->staticHeadings;
        }
        return array_map(fn($key) => $this->columnLabels[$key] ?? $key, $this->columns);
    }

    public function map($row): array
    {
        // 纯数组模式：直接返回行数据
        if ($this->staticData !== null) {
            return is_array($row) ? array_values($row) : [];
        }

        if ($this->rowCallback) {
            return call_user_func($this->rowCallback, $row, $this->columns);
        }

        $result = [];
        foreach ($this->columns as $col) {
            $value = data_get($row, $col);

            // 关联字段：用 . 分隔取值（如 merchant.name）
            if (str_contains($col, '.')) {
                $value = data_get($row, $col);
            }

            // 金额字段自动格式化（字段名含 amount / price / fee / money / cost）
            if (is_numeric($value) && preg_match('/(amount|price|fee|money|cost|total|balance|payment)/i', $col)) {
                $value = $value / 100;
            }

            $result[] = $value;
        }
        return $result;
    }
}
