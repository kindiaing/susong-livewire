<?php

namespace App\Livewire\Traits;

/**
 * 金额字段自动换算 Trait
 *
 * 在 Livewire 组件中声明 $moneyFields 属性，即可自动处理：
 * - 表单输入：用户输入"元"，自动转为"厘"存储
 * - 表单回显：从数据库读取"厘"，自动转为"元"显示
 *
 * 三层分离架构：存储层（厘）→ 计算层（厘）→ 显示层（可配置精度+舍入）
 * 本 Trait 对齐全局助手函数，支持系统配置驱动的精度和舍入。
 *
 * 用法：
 *   class SkuList extends Component {
 *       use WithMoneyConversion;
 *
 *       protected array $moneyFields = ['formPurchasePrice', 'formWholesalePrice'];
 *
 *       // 编辑时回显：$this->formPurchasePrice = $this->centsToYuan($sku->purchase_price);
 *       // 保存时转厘：'purchase_price' => money_to_cents($this->formPurchasePrice),
 *   }
 */
trait WithMoneyConversion
{
    /**
     * 将厘转为元（用于表单回显）
     * 使用全局 money_format() 助手函数，支持配置化精度
     */
    protected function centsToYuan(int|float|null $cents): string
    {
        if ($cents === null || $cents === 0) {
            return '';
        }
        return money_format($cents, false);
    }

    /**
     * 将元转为厘（用于存储）
     * 使用全局 money_to_cents() 助手函数
     */
    protected function yuanToCents(string|float|null $yuan): int
    {
        return money_to_cents($yuan);
    }
}
