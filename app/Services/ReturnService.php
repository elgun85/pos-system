<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleReturn;
use Illuminate\Support\Facades\DB;
use App\Models\Inventory;
use App\Models\ReturnItem;

class ReturnService
{
    private function updateSaleStatusAfterReturn(Sale $sale, float $refundAmount): void
    {

        $totalRetuned = SaleReturn::where('sale_id', $sale->id)
            ->sum('total');
        if ($totalRetuned >= $sale->total) {
            $sale->update(['status' => 'cancelled']);
        } else {
            $sale->update(['status' => 'partially_returned']);
        }
    }

    public function processReturn(Sale $sale, array $items, string $reason = null): SaleReturn
    {
        return DB::transaction(function () use ($sale, $items, $reason) {
            $totalRefund = 0;

            $returnRecord = SaleReturn::create(
                [
                    'sale_id' => $sale->id,
                    'total'   => 0,
                    'reason'  => $reason,
                ]
            );

            foreach ($items as $item) {
                ReturnItem::create(
                    [
                        'return_id'    => $returnRecord->id,
                        'product_id' => $item['product_id'],
                        'quantity'   => $item['quantity'],
                        'refund_amount'      => $item['refund_amount'],
                    ]
                );

                Inventory::where('product_id', $item['product_id'])
                    ->increment('quantity', $item['quantity']);
                $totalRefund += $item['refund_amount'];
            }

            $returnRecord->update(
                [
                    'total' => $totalRefund,
                    'quantity' => collect($items)->sum('quantity'),
                ]
            );

            $this->updateSaleStatusAfterReturn($sale, $totalRefund);
            return $returnRecord;
        });
    }
}
