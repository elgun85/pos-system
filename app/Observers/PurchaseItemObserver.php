<?php

namespace App\Observers;

use App\Models\Inventory;
use App\Models\PurchaseItem;

class PurchaseItemObserver
{
    /**
     * Handle the PurchaseItem "created" event.
     */
    public function created(PurchaseItem $purchaseItem): void
    {
        if ($purchaseItem->purchase && $purchaseItem->purchase->status == 1) {
            $inventory = Inventory::where('product_id', $purchaseItem->product_id)->first();
            if ($inventory) {
                $inventory->increment('quantity', $purchaseItem->quantity);
            }
        }
    }

    /**
     * Handle the PurchaseItem "updated" event.
     */
    public function updated(PurchaseItem $purchaseItem): void
    {
        if ($purchaseItem->purchase && $purchaseItem->purchase->status == 1) {
            $inventory = Inventory::where('product_id', $purchaseItem->product_id)->first();

            if ($inventory) {
                // Köhnə miqdar ilə yeni miqdar arasındakı fərqi tapırıq (Məs: 60 - 50 = 10)
                $difference = $purchaseItem->quantity - $purchaseItem->getOriginal('quantity');

                // Fərqi anbara tətbiq edirik
                $inventory->increment('quantity', $difference);
            }
        }
    }

    /**
     * Handle the PurchaseItem "deleted" event.
     */
    public function deleted(PurchaseItem $purchaseItem): void
    {
        if ($purchaseItem->purchase && $purchaseItem->purchase->status == 1) {
            $inventory = Inventory::where('product_id', $purchaseItem->product_id)->first();

            if ($inventory) {
                // Silinən malın miqdarı qədər anbarı geri azaldırıq
                $inventory->decrement('quantity', $purchaseItem->quantity);
            }
        }
    }

    /**
     * Handle the PurchaseItem "restored" event.
     */
    public function restored(PurchaseItem $purchaseItem): void
    {
        //
    }

    /**
     * Handle the PurchaseItem "force deleted" event.
     */
    public function forceDeleted(PurchaseItem $purchaseItem): void
    {
        //
    }
}
