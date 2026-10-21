<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\PurchaseItem;
use App\Exceptions\StockException;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Stoku dəyişir ($delta müsbət = artır, mənfi = azalır).
     * Sətir kilidlənir, stok mənfiyə düşə bilməz, anbar qeydi yoxdursa xəta verir (səssiz keçmir).
     */

    public function adjust(int $productId, float $delta): void
    {
        $delta = round($delta, 2);
        if ($delta == 0) {
            return;
        }
        DB::transaction(function () use ($productId, $delta) {
            $inventory = Inventory::where('product_id', $productId)
                ->lockForUpdate()
                ->first();
            if (! $inventory) {
                throw new StockException(__('resource.purchase.inventory_missing'));
            }

            if ($delta < 0 && round((float) $inventory->quantity + $delta, 2) < 0) {
                throw new StockException(__('resource.purchase.stock_too_low'));
            }

            $inventory->increment('quantity', $delta);
        });
    }

    /**
     * Məhsulun maya dəyərini yalnız həmin məhsul üzrə ƏN SON qəbul edilmiş alışdan yeniləyir.
     * Köhnə alışı redaktə etmək cari maya dəyərini pozmasın deyə.
     */
    public function syncCostPrice(PurchaseItem $item): void
    {
        if ((float) $item->cost_price <= 0) {
            return;
        }
        $newerExists = PurchaseItem::where('product_id', $item->product_id)
            ->where('id', '>', $item->id ?? 0)
            ->whereHas('purchase', fn($q) => $q->where('status', true))
            ->exists();

        if (! $newerExists) {
            Product::whereKey($item->product_id)->update(['cost_price' => $item->cost_price]);
        }
    }
}
