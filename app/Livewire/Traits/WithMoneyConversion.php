<?php

namespace App\Livewire\Traits;

/**
 * 金额字段自动换算 Trait
 *
 * 在 Livewire 组件中声明 $moneyFields 属性，即可自动处理：
 * - 表单输入：用户输入"元"，自动转为"厘"存储
 * - 表单回显：从数据库读取"厘"，自动转为"元"显示
 *
 * 用法：
 *   class SkuList extends Component {
 *       use WithMoneyConversion;
 *
 *       protected array $moneyFields = ['formPurchasePrice', 'formWholesalePrice'];
 *
 *       // 编辑时回显：$this->formPurchasePrice = money_format($sku->purchase_price, false);
 *       // 保存时转厘：'purchase_price' => money_to_cents($this->formPurchasePrice),
 *   }
 */
trait WithMoneyConversion
{
    /**
     * 将厘转为元（用于表单回显）
     */
    protected function centsToYuan(int|float|null $cents): string
    {
        if ($cents === null || $cents === 0) {
            return '';
        }
        return number_format($cents / 1000, 2, '.', '');
    }

    /**
     * 将元转为厘（用于存储）
     */
    protected function yuanToCents(string|float|null $yuan): int
    {
        return money_to_cents($yuan);
    }
}
