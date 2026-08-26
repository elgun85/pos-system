<?php

namespace App\Services;

use App\Models\CustomerTransaction;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SalesItem;
use App\Models\SalesPayment;
use Exception;
use Illuminate\Support\Facades\DB;

class SalesService
{
    public function createSale(array $cart, array $data): Sale
    {
        if (empty($cart)) {
            throw new Exception('Səbət boşdur. Satış üçün ən azı bir məhsul əlavə edin.');
        }

        return DB::transaction(function () use ($cart, $data) {

            $totalPayments = collect($data['payments'])->sum('amount');
            $netTotal = $data['total'];




            // Statusu təyin edirik
            if ($totalPayments >= $netTotal) {
                $status = 'completed'; // Tam ödəndi
            } elseif ($totalPayments > 0 && $totalPayments < $netTotal) {
                $status = 'partial';   // Hissəvi ödəndi (qalanı nisyə)
            } else {
                $status = 'unpaid';    // Heç ödənilmədi (tam nisyə)
            }

            // 1. Satış qeydini yaradırıq
            $sale = Sale::create([
                'sale_number'       => 'SALE-' . now()->format('YmdHis') . '-' . rand(1000, 9999),
                'total'             => $data['total'],
                'discount'          => $data['discount_amount'] ?? 0, // DB-ə yazılması təmin olundu
                'customer_id'      => $data['customer_id'] ?? null,
                'user_id'           => auth()->id(),
                'status'            => $status,
            ]);

            // Ödənişləri qeyd edirik
            foreach ($data['payments'] as $payment) {
                if ($payment['amount'] > 0) {
                    SalesPayment::create([
                        'sale_id'           => $sale->id,
                        'payment_method_id' => $payment['payment_method_id'],
                        'amount'            => $payment['amount'],
                    ]);
                }
            }


            $customerPaidTotal = (float)($data['paid_amount'] ?? $totalPayments);

            $remainingDebt = $netTotal - $customerPaidTotal;

            if ($remainingDebt > 0) {
                if (empty($data['customer_id'])) {
                    throw new Exception('Qalan borc (Nisyə) üçün müştəri seçilməlidir.');
                }

                CustomerTransaction::create([
                    'customer_id' => $data['customer_id'],
                    'sale_id'     => $sale->id,
                    'payment_method_id' => $data['payment_method_id'] ?? null,
                    'amount'      => $remainingDebt,
                    'type'        => 'debt',
                    'user_id'     => auth()->id(),
                    'notes'       => "Sale #{$sale->sale_number} üzrə nisyə qalıq borc.",
                ]);
            }

            // 2. Səbətdəki məhsulları dövr edirik
            foreach ($cart as $cartItem) {
                // Stok yarışının (Race Condition) qarşısını almaq üçün sətiri kilidləyirik
                $inventory = Inventory::where('product_id', $cartItem['product_id'])
                    ->lockForUpdate()
                    ->first();

                if (!$inventory || $inventory->quantity < $cartItem['quantity']) {
                    throw new Exception("{$cartItem['name']} məhsulundan stokda kifayət qədər yoxdur.");
                }

                $productCostPrice = $cartItem['cost_price']
                ?? Product::where('id',$cartItem['product_id'])->value('cost_price')
                ?? 0;

                // Satış elementini yazırıq
                SalesItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $cartItem['product_id'],
                    'quantity'   => $cartItem['quantity'],
                    'price'      => $cartItem['sale_price'],
                    'cost_price' => $productCostPrice,
                    'total'      => $cartItem['quantity'] * $cartItem['sale_price'],
                ]);

                // Stoku azaldırıq
                $inventory->decrement('quantity', $cartItem['quantity']);
            }

            return $sale;
        });
    }
}
