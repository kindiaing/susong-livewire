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

    public function __construct(
        ?Builder $query,
        array $columns,        // 列 key 数组 ['id','name','status',...]
        array $columnLabels,   // 列标签 ['id'=>'ID','name'=>'名称',...]
        ?callable $rowCallback = null,
    ) {
        $this->query = $query;
        $this->columns = $columns;
        $this->columnLabels = $columnLabels;
        $this->rowCallback = $rowCallback;
    }

    public function collection()
    {
        if (!$this->query) {
            return collect([]);
        }
        return $this->query->get();
    }

    public function headings(): array
    {
        return array_map(fn($key) => $this->columnLabels[$key] ?? $key, $this->columns);
    }

    public function map($row): array
    {
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
