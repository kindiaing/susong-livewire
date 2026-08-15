<?php

namespace App\Livewire\Delivery;

use App\Models\DeliveryNote;
use App\Models\DeliveryNoteItem;
use App\Livewire\Traits\WithToast;
use Livewire\Component;

class DeliveryNoteDetail extends Component
{
    use WithToast;

    public int $noteId;
    public $note;

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
        if (!in_array($note->status, [DeliveryNote::STATUS_PENDING, DeliveryNote::STATUS_DELIVERED])) {
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
        $item->picked_quantity = $pickedQty;
        $item->status = $pickedQty >= $item->quantity
            ? DeliveryNoteItem::STATUS_DELIVERED
            : DeliveryNoteItem::STATUS_DISCREPANCY;
        $item->save();
        $this->loadNote();
        $this->toastSuccess('明细已更新');
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
            ->title('送货单详情 - ' . $note->note_no);
    }
}