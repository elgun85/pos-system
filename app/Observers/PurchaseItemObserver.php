<?php

namespace App\Observers;

use App\Models\Inventory;
use App\Models\PurchaseItem;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;


class PurchaseItemObserver
{
    public function __construct(private StockService $stock) {}
    /** Alış anbara qəbul olunubsa (status = true) stok dəyişir. */
    private function isReceived(PurchaseItem $item): bool
    {
        return (bool) $item->purchase?->status;
    }

    public function created(PurchaseItem $item): void
    {
        if (! $this->isReceived($item)) {
            return;
        }

        $this->stock->adjust((int) $item->product_id, (float) $item->quantity);
        $this->stock->syncCostPrice($item);
    }

    public function updating(PurchaseItem $item): void
    {
        if (! $this->isReceived($item)) {
            return;
        }

        $oldProductId = (int) $item->getOriginal('product_id');
        $newProductId = (int) $item->product_id;
        $oldQty       = (float) $item->getOriginal('quantity');
        $newQty       = (float) $item->quantity;

        DB::transaction(function () use ($oldProductId, $newProductId, $oldQty, $newQty) {
            if ($oldProductId !== $newProductId) {
                // Məhsul dəyişib: köhnədən çıx, yeniyə əlavə et
                $this->stock->adjust($oldProductId, -$oldQty);
                $this->stock->adjust($newProductId, $newQty);
            } elseif ($newQty !== $oldQty) {
                $this->stock->adjust($newProductId, $newQty - $oldQty);
            }
        });
    }

    public function updated(PurchaseItem $item): void
    {
        if ($this->isReceived($item) && $item->wasChanged(['cost_price', 'product_id'])) {
            $this->stock->syncCostPrice($item);
        }
    }

    public function deleting(PurchaseItem $item): void
    {
        if (! $this->isReceived($item)) {
            return;
        }

        $this->stock->adjust((int) $item->product_id, -(float) $item->quantity);
    }
}
