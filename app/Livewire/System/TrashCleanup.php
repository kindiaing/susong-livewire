<?php

namespace App\Livewire\System;

use App\Models\DeliveryNote;
use App\Models\DeliveryTask;
use App\Models\Discrepancy;
use App\Models\Invoice;
use App\Models\LossOrder;
use App\Models\Order;
use App\Models\OrderReturn;
use App\Models\PickingTask;
use App\Models\PurchaseOrder;
use App\Models\PurchaseReturn;
use App\Models\Receivable;
use App\Models\Recharge;
use App\Models\SupplierSettlement;
use Livewire\Component;
use Livewire\WithPagination;
use App\Livewire\Traits\WithToast;

class TrashCleanup extends Component
{
    use WithToast;

    public bool $showConfirmModal = false;

    public ?string $confirmType = null;

    public string $confirmLabel = '';

    public int $confirmCount = 0;

    /**
     * 可清理的业务单据类型
     */
    public function getTrashTypes(): array
    {
        $types = [
            'orders' => [
                'label' => '客户订单',
                'model' => Order::class,
                'icon' => 'shopping-cart',
            ],
            'purchase_orders' => [
                'label' => '采购单',
                'model' => PurchaseOrder::class,
                'icon' => 'truck',
            ],
            'order_returns' => [
                'label' => '售后退货',
                'model' => OrderReturn::class,
                'children' => [['model' => \App\Models\OrderReturnItem::class, 'foreign_key' => 'order_return_id']],
                'icon' => 'arrow-uturn-left',
            ],
            'purchase_returns' => [
                'label' => '采购退货',
                'model' => PurchaseReturn::class,
                'children' => [['model' => \App\Models\PurchaseReturnItem::class, 'foreign_key' => 'purchase_return_id']],
                'icon' => 'arrow-left-circle',
            ],
            'supplier_settlements' => [
                'label' => '供应商结算',
                'model' => SupplierSettlement::class,
                'children' => [['model' => \App\Models\SupplierSettlementItem::class, 'foreign_key' => 'supplier_settlement_id']],
                'icon' => 'banknotes',
            ],
            'receivables' => [
                'label' => '应收账款',
                'model' => Receivable::class,
                'icon' => 'currency-yen',
            ],
            'invoices' => [
                'label' => '发票',
                'model' => Invoice::class,
                'icon' => 'document-text',
            ],
            'recharges' => [
                'label' => '客户充值',
                'model' => Recharge::class,
                'icon' => 'credit-card',
            ],
            'loss_orders' => [
                'label' => '损耗单',
                'model' => LossOrder::class,
                'children' => [['model' => \App\Models\LossOrderItem::class, 'foreign_key' => 'loss_order_id']],
                'icon' => 'exclamation-triangle',
            ],
            'discrepancies' => [
                'label' => '差异处理',
                'model' => Discrepancy::class,
                'icon' => 'exclamation-circle',
            ],
            'delivery_tasks' => [
                'label' => '配送任务',
                'model' => DeliveryTask::class,
                'icon' => 'truck',
            ],
            'delivery_notes' => [
                'label' => '送货单',
                'model' => DeliveryNote::class,
                'icon' => 'clipboard-document-list',
            ],
            'picking_tasks' => [
                'label' => '拣货任务',
                'model' => PickingTask::class,
                'icon' => 'clipboard-document-check',
            ],
        ];

        // 查询每种类型的已删除数量
        $result = [];
        foreach ($types as $key => $config) {
            $model = $config['model'];
            $count = $model::onlyTrashed()->count();
            $result[$key] = array_merge($config, ['count' => $count]);
        }

        return $result;
    }

    public function openConfirm(string $type): void
    {
        abort_unless(auth()->user()?->can('system.trash-cleanup.view'), 403, '无数据清理权限');

        $types = $this->getTrashTypes();
        if (! isset($types[$type])) {
            $this->toastError('无效的单据类型');

            return;
        }

        $this->confirmType = $type;
        $this->confirmLabel = $types[$type]['label'];
        $this->confirmCount = $types[$type]['count'];

        if ($this->confirmCount === 0) {
            $this->toastInfo("{$this->confirmLabel} 没有已删除记录");

            return;
        }

        $this->showConfirmModal = true;
    }

    public function closeConfirm(): void
    {
        $this->showConfirmModal = false;
        $this->confirmType = null;
        $this->confirmLabel = '';
        $this->confirmCount = 0;
    }

    public function executeCleanup(): void
    {
        // 双保险：数据清理为管理员专属，仅持有 system.trash-cleanup.view 权限（实际仅 admin）可执行
        abort_unless(auth()->user()?->can('system.trash-cleanup.view'), 403, '无数据清理权限');

        if (! $this->confirmType) {
            $this->toastError('操作失败');

            return;
        }

        $types = $this->getTrashTypes();
        if (! isset($types[$this->confirmType])) {
            $this->toastError('无效的单据类型');

            return;
        }

        $config = $types[$this->confirmType];
        $model = $config['model'];

        // 先清理子表
        if (isset($config['children'])) {
            $parentIds = $model::onlyTrashed()->pluck('id');
            foreach ($config['children'] as $child) {
                $childModel::onlyTrashed()
                    ->whereIn($child['foreign_key'], $parentIds)
                    ->forceDelete();
            }
        }

        // 再清理主表
        $count = $model::onlyTrashed()->forceDelete();

        $this->toastSuccess("已永久清理 {$count} 条 {$config['label']}");

        $this->showConfirmModal = false;
        $this->confirmType = null;
        $this->confirmLabel = '';
        $this->confirmCount = 0;
    }

    public function render()
    {
        $trashTypes = $this->getTrashTypes();
        $totalTrashed = array_sum(array_column($trashTypes, 'count'));

        return view('livewire.system.trash-cleanup', compact('trashTypes', 'totalTrashed'))
            ->layout('components.app-layout')
            ->title('数据清理');
    }
}
