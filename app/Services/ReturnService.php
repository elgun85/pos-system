<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerTransaction;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ReturnItem;
use App\Models\Sale;
use App\Models\SaleReturn;
use Exception;
use Illuminate\Support\Facades\DB;

class ReturnService
{

    public function processReturn(Sale $sale, array $items, ?string $reason = null): SaleReturn
    {
        return DB::transaction(function () use ($sale, $items, $reason) {
            // 1) Satışı kilidləyirik ki, eyni anda iki qaytarma keçməsin
            $sale = Sale::whereKey($sale->id)->lockForUpdate()->firstOrFail();
            if (in_array($sale->status, ['cancelled', 'returned', 'draft'], true)) {
                throw new Exception(__('resource.sale.already_returned'));
            }

            // 2) Sorğunu təmizləyirik: məhsul üzrə birləşdir, miqdar > 0
            $requested = [];

            foreach ($items as $item) {
                $productId = (int) ($item['product_id'] ?? 0);
                $quantity  = round((float) ($item['quantity'] ?? 0), 2);

                if ($productId <= 0 || $quantity <= 0) {
                    throw new  Exception(__('resource.quantityZero'));
                }
                $requested[$productId] = round(($requested[$productId] ?? 0) + $quantity, 2);
            }
            if (empty($requested)) {
                throw new Exception(__('resource.quantityZero'));
            }

            ksort($requested);

            // 3) Bu satışda məhsul üzrə nə qədər satılıb

            $sold = $sale->items()->get()->groupBy('product_id')->map(fn($rows) => [
                'quantity' => round((float) $rows->sum('quantity'), 2),
                'total'    => round((float) $rows->sum('total'), 2),
            ]);

            // 4) Bu satış üzrə əvvəl nə qədər qaytarılıb

            $returned = ReturnItem::query()
                ->join('returns', 'returns.id', '=', 'return_items.return_id')
                ->where('returns.sale_id', $sale->id)
                ->selectRaw('return_items.product_id, SUM(return_items.quantity) as qty, SUM(return_items.refund_amount) as amount')
                ->groupBy('return_items.product_id')
                ->get()
                ->keyBy('product_id');

            // Endirim payı: sale.total (xalis) / sətirlərin cəmi
            $itemsSubtotal  = (float) $sold->sum('total');
            $factor        = $itemsSubtotal > 0 ? ((float) $sale->total / $itemsSubtotal) : 0.0;

            // 5) Hər sətir üçün qaytarma miqdarını və məbləğini hesablayırıq
            $lines       = [];
            $totalRefund = 0.0;
            $totalQty    = 0.0;

            foreach ($requested as $productId => $quantity) {
                $name = Product::find($productId)?->name ?? ('#' . $productId);

                if (! $sold->has($productId)) {
                    throw new Exception($name . ': ' . __('resource.sale.return_not_in_sale'));
                }

                $soldQty       = $sold[$productId]['quantity'];
                $alreadyQty    = round((float) ($returned[$productId]->qty ?? 0), 2);
                $alreadyAmount = round((float) ($returned[$productId]->amount ?? 0), 2);
                $remainingQty  = round($soldQty - $alreadyQty, 2);

                if ($quantity > $remainingQty) {
                    throw new Exception($name . ': ' . __('resource.sale.return_qty_error') . $remainingQty);
                }
                $lineNet = round($sold[$productId]['total'] * $factor, 2);

                // Son qalan miqdar qaytarılırsa, qəpik fərqi qalmasın deyə qalığı götürürük
                $refund = $quantity >= $remainingQty
                    ? round($lineNet - $alreadyAmount, 2)
                    : round($lineNet * $quantity / $soldQty, 2);

                $refund = max($refund, 0);

                $lines[] = [
                    'product_id' => $productId,
                    'quantity'   => $quantity,
                    'refund'     => $refund,
                ];


                $totalRefund += $refund;
                $totalQty    += $quantity;
            }
            $totalRefund = round($totalRefund, 2);
            $totalQty    = round($totalQty, 2);

            // 6) Qaytarma qeydi (kim etdi — user_id)
            $returnRecord = SaleReturn::create([
                'sale_id'  => $sale->id,
                'user_id'  => auth()->id(),
                'quantity' => $totalQty,
                'total'    => $totalRefund,
                'reason'   => $reason,
            ]);

            // 7) Sətirlər və stokun bərpası (kilidlə)
            foreach ($lines as $line) {
                ReturnItem::create([
                    'return_id'     => $returnRecord->id,
                    'product_id'    => $line['product_id'],
                    'quantity'      => $line['quantity'],
                    'refund_amount' => $line['refund'],
                ]);

                $inventory = Inventory::where('product_id', $line['product_id'])
                    ->lockForUpdate()
                    ->first();

                if (! $inventory) {
                    throw new Exception(__('resource.sale.return_inventory_missing'));
                }

                $inventory->increment('quantity', $line['quantity']);
            }

            // 8) Borc və nağd hissəsi
            $debtOffset = 0.0;

            if ($sale->customer_id && $totalRefund > 0) {
                $saleDebt = (float) CustomerTransaction::where('sale_id', $sale->id)
                    ->where('type', 'debt')->sum('amount');

                $saleRefunded = (float) CustomerTransaction::where('sale_id', $sale->id)
                    ->where('type', 'refund')->sum('amount');

                $outstandingForSale = max(round($saleDebt - $saleRefunded, 2), 0);

                $customerDebt = (float) (Customer::withTotalDebt()
                    ->whereKey($sale->customer_id)
                    ->first()?->total_debt ?? 0);

                $debtOffset = round(min($totalRefund, $outstandingForSale, max($customerDebt, 0)), 2);

                if ($debtOffset > 0) {
                    CustomerTransaction::create([
                        'customer_id' => $sale->customer_id,
                        'sale_id'     => $sale->id,
                        'user_id'     => auth()->id(),
                        'type'        => 'refund',
                        'amount'      => $debtOffset,
                        'notes'       => "Sale #{$sale->sale_number}" .__('resource.return_debt_note'),
                    ]);
                }
            }

            $returnRecord->setAttribute('debt_offset', $debtOffset);
            $returnRecord->setAttribute('cash_refund', round($totalRefund - $debtOffset, 2));

            // 9) Satışın statusu: bütün məhsullar tam qaytarılıbsa "returned", yoxsa "partially_returned"
            $allReturned = $sold->every(function ($row, $productId) use ($returned, $requested) {
                $total = (float) ($returned[$productId]->qty ?? 0) + (float) ($requested[$productId] ?? 0);

                return round($total, 2) >= $row['quantity'];
            });

            $sale->update(['status' => $allReturned ? 'returned' : 'partially_returned']);

            return $returnRecord;
        });
    }
}
