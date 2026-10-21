<?php

namespace App\Observers;

use App\Models\Purchase;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;

class PurchaseObserver
{
    public function __construct(private StockService $stock) {}
    /**
     * "Anbara daxil edilsin?" statusu dəyişəndə stok da dəyişməlidir.
     * "updating" istifadə olunur ki, stok kifayət etmirsə status dəyişikliyi saxlanmasın.
     */

    public function updating(Purchase $purchase): void
    {
        if (! $purchase->isDirty('status')) {
            return;
        }

        $receiving   = (bool) $purchase->status;
        $wasReceived = (bool) $purchase->getOriginal('status');

        if ($receiving === $wasReceived) {
            return;
        }
        DB::transaction(function () use ($purchase, $receiving) {
            foreach ($purchase->purchaseItems as $item) {
                $qty = (float) $item->quantity;

                $this->stock->adjust((int) $item->product_id, $receiving ? $qty : -$qty);

                if ($receiving) {
                    $this->stock->syncCostPrice($item);
                }
            }
        });
    }
    /**
     * Alış silinəndə purchase_items DB səviyyəsində (cascade) silinir və item event-ləri işləmir,
     * ona görə stoku burada geri qaytarırıq.
     */
    public function deleting(Purchase $purchase): void
    {
        if (! (bool) $purchase->status) {
            return;
        }

        DB::transaction(function () use ($purchase) {
            foreach ($purchase->purchaseItems as $item) {
                $this->stock->adjust((int) $item->product_id, -(float) $item->quantity);
            }
        });
    }
}
