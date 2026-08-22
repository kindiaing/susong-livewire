<?php

namespace App\Livewire\Delivery;

use App\Livewire\Traits\WithToast;
use App\Models\DeliveryNote;
use App\Models\DeliveryNoteItem;
use App\Models\Discrepancy;
use App\Models\OrderItem;
use App\Services\DiscrepancyService;
use Livewire\Component;

class DeliveryNoteDetail extends Component
{
    use WithToast;

    public int $noteId;

    public $note;

    // 实收差异登记弹窗
    public bool $showDiffModal = false;

    public ?int $diffItemId = null;

    public string $diffSkuName = '';

    public int $diffExpectedQty = 0;

    public int $diffActualQty = 0;

    public int $diffType = 1; // 1少收 2拒收 3残次 4其他

    public string $diffReason = '';

    public static array $statusMap = [
        1 => '待分货', 2 => '已分货', 3 => '已签收', 4 => '已作废',
    ];

    public static array $statusColorMap = [
        1 => 'orange', 2 => 'blue', 3 => 'green', 4 => 'gray',
    ];

    public static array $itemStatusMap = [
        1 => '待分货', 2 => '已分货', 3 => '差异',
    ];

    public static array $itemStatusColorMap = [
        1 => 'yellow', 2 => 'green', 3 => 'red',
    ];

    public function mount(int $id): void
    {
        $this->noteId = $id;
        $this->loadNote();
    }

    private function loadNote(): void
    {
        $this->note = DeliveryNote::with([
            'merchant',
            'items.sku',
            'deliveryTask',
        ])->findOrFail($this->noteId);
    }

    // ========== 状态操作 ==========

    public function markDelivered(): void
    {
        $note = DeliveryNote::findOrFail($this->noteId);
        if ($note->status !== DeliveryNote::STATUS_PENDING) {
            $this->toastError('仅待分货状态可确认分货');

            return;
        }
        $note->update([
            'status' => DeliveryNote::STATUS_DELIVERED,
            'delivered_at' => now(),
        ]);
        $note->items()->update(['status' => DeliveryNoteItem::STATUS_DELIVERED]);
        $this->loadNote();
        $this->toastSuccess('已确认分货');
    }

    public function markSigned(): void
    {
        $note = DeliveryNote::findOrFail($this->noteId);
        if ($note->status !== DeliveryNote::STATUS_DELIVERED) {
            $this->toastError('仅已分货状态可确认签收');

            return;
        }
        $note->update(['status' => DeliveryNote::STATUS_SIGNED]);
        $this->loadNote();
        $this->toastSuccess('已确认签收');
    }

    public function cancelNote(): void
    {
        $note = DeliveryNote::findOrFail($this->noteId);
        if (! in_array($note->status, [DeliveryNote::STATUS_PENDING, DeliveryNote::STATUS_DELIVERED])) {
            $this->toastError('当前状态不允许作废');

            return;
        }
        $note->update(['status' => DeliveryNote::STATUS_CANCELLED]);
        $this->loadNote();
        $this->toastSuccess('送货单已作废');
    }

    // ========== 明细操作 ==========

    public function confirmItemDelivery(int $itemId, int $pickedQty): void
    {
        $item = DeliveryNoteItem::findOrFail($itemId);
        $pickedQty = max(0, min((int) $pickedQty, (int) $item->quantity));
        $item->picked_quantity = $pickedQty;
        $item->status = $pickedQty >= $item->quantity
            ? DeliveryNoteItem::STATUS_DELIVERED
            : DeliveryNoteItem::STATUS_DISCREPANCY;
        $item->save();
        $this->loadNote();

        // 实收少于应送：弹出差异登记（实收 0 默认按拒收）
        if ($pickedQty < $item->quantity) {
            $this->diffItemId = $item->id;
            $this->diffSkuName = $item->sku_name ?? $item->sku?->sku_name ?? '';
            $this->diffExpectedQty = (int) $item->quantity;
            $this->diffActualQty = $pickedQty;
            $this->diffType = $pickedQty === 0
                ? Discrepancy::TYPE_REJECT
                : Discrepancy::TYPE_SHORT;
            $this->diffReason = '';
            $this->showDiffModal = true;

            return;
        }

        $this->toastSuccess('明细已更新');
    }

    /**
     * 保存实收差异登记（生成差异单，不动库存和钱）
     */
    public function saveItemDiscrepancy(): void
    {
        $item = DeliveryNoteItem::findOrFail($this->diffItemId);

        if (trim($this->diffReason) === '') {
            $this->toastError('请填写差异原因');

            return;
        }

        // 反查订单明细：送货单明细有 order_id + sku_id，回填 order_item_id 以便金额估算与溯源
        $orderItem = $item->order_id
            ? OrderItem::where('order_id', $item->order_id)->where('sku_id', $item->sku_id)->latest('id')->first()
            : null;

        try {
            app(DiscrepancyService::class)->create([
                'order_id' => $item->order_id,
                'order_item_id' => $orderItem?->id,
                'delivery_note_item_id' => $item->id,
                'sku_id' => $item->sku_id,
                'stage' => Discrepancy::STAGE_RECEIVING,
                'type' => $this->diffType,
                'expected_quantity' => $this->diffExpectedQty,
                'actual_quantity' => $this->diffActualQty,
                'reason' => trim($this->diffReason),
                'remark' => "送货单 {$this->note->note_no} 实收登记",
            ]);
            $this->toastSuccess('差异已登记，待处理台跟进');
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }

        $this->closeDiffModal();
    }

    /**
     * 暂不登记差异（仅保留明细差异标记）
     */
    public function closeDiffModal(): void
    {
        $this->showDiffModal = false;
        $this->diffItemId = null;
        $this->diffReason = '';
    }

    public function render()
    {
        $note = $this->note;
        $statusMap = self::$statusMap;
        $statusColorMap = self::$statusColorMap;
        $itemStatusMap = self::$itemStatusMap;
        $itemStatusColorMap = self::$itemStatusColorMap;

        return view('livewire.delivery.delivery-note-detail', compact(
            'note', 'statusMap', 'statusColorMap', 'itemStatusMap', 'itemStatusColorMap'
        ))
            ->layout('components.app-layout')
            ->title('送货单详情 - '.$note->note_no);
    }
}
