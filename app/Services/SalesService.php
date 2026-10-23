<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerTransaction;
use App\Models\Inventory;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SalesItem;
use App\Models\SalesPayment;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SalesService
{
    public function createSale(array $cart, array $data): Sale
    {
        if (empty($cart)) {
            throw new Exception(__('resource.cardEmpty'));
        }

        return DB::transaction(function () use ($cart, $data) {
            // 1) Səbəti məhsul üzrə birləşdiririk və miqdarı yoxlayırıq
            $quantities = [];
            foreach ($cart as $cartItem) {
                $productId = (int) ($cartItem['product_id'] ?? 0);
                $quantity  = round((float) ($cartItem['quantity'] ?? 0), 2);

                if ($productId <= 0 || $quantity <= 0) {
                    throw new Exception(__('resource.quantityZero'));
                }
                $quantities[$productId] = ($quantities[$productId]  ?? 0) + $quantity;
            }
            // Deadlock olmasın deyə həmişə eyni ardıcıllıqla kilidləyirik
            ksort($quantities);

            // 2) Qiymət və maya dəyəri DB-dən, stok isə kilidlə yoxlanır
            $lines = [];
            $subtotal = 0.0;

            foreach ($quantities as $productId => $quantity) {
                $product = Product::find($productId);
                if (!$product || $product->sale_price === null) {
                    throw new Exception(__('resource.productNotFound'));
                }

                $inventory = Inventory::where('product_id', $productId)
                    ->lockForUpdate()
                    ->first();

                if (! $inventory || (float) $inventory->quantity < $quantity) {
                    throw new Exception($product->name . __('resource.no_inventory.error'));
                }

                $price = (float) $product->sale_price;
                $lineTotal = round($quantity * $price, 2);
                $subtotal += $lineTotal;

                $lines[] = [
                    'inventory'  => $inventory,
                    'product_id' => $productId,
                    'quantity'   => $quantity,
                    'price'      => $price,
                    'cost_price' => (float) ($product->cost_price ?? 0),
                    'total'      => $lineTotal,
                ];
            }
            $subtotal = round($subtotal, 2);

            // 3) Endirim 0..subtotal aralığında olmalıdır
            $discount = round(
                min(max((float) ($data['discount_amount'] ?? 0), 0), $subtotal),
                2
            );

            $netTotal = round($subtotal - $discount, 2);

            // 4) Ödənişlər: cəmdən çox yazılmır, metod mövcud və aktiv olmalıdır
            $payments  = [];
            $paidTotal = 0.0;

            foreach (($data['payments'] ?? []) as $payment) {
                $amount = round((float) ($payment['amount'] ?? 0), 2);
                $amount = min($amount, round($netTotal - $paidTotal, 2));

                if ($amount <= 0) {
                    continue;
                }

                $methodId = $payment['payment_method_id'] ?? null;

                if (! PaymentMethod::whereKey($methodId)->where('status', true)->exists()) {
                    throw new Exception(__('resource.payment_info'));
                }

                $payments[] = [
                    'payment_method_id' => $methodId,
                    'amount'            => $amount,
                ];
                $paidTotal = round($paidTotal + $amount, 2);
            }
            // 5) Nisyə borc və müştəri yoxlaması
            $customerId = $data['customer_id'] ?? null;
            $debt       = round($netTotal - $paidTotal, 2);

            if ($debt > 0 && empty($customerId)) {
                throw new Exception(__('resource.customerCreditError'));
            }

            if ($customerId && ! Customer::whereKey($customerId)->exists()) {
                throw new Exception(__('resource.customerNotFound'));
            }

            $status = $debt <= 0
                ? 'completed'
                : ($paidTotal > 0 ? 'partial' : 'unpaid');

            // 6) Yazırıq
            $sale = Sale::create([
                'sale_number' => 'SALE-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(4)),
                'total'       => $netTotal,
                'discount'    => $discount,
                'customer_id' => $customerId,
                'user_id'     => auth()->id(),
                'status'      => $status,
            ]);
            foreach ($payments as $payment) {
                SalesPayment::create([
                    'sale_id'           => $sale->id,
                    'payment_method_id' => $payment['payment_method_id'],
                    'amount'            => $payment['amount'],
                ]);
            }
            if ($debt > 0) {
                CustomerTransaction::create([
                    'customer_id'       => $customerId,
                    'sale_id'           => $sale->id,
                    'payment_method_id' => $data['payment_method_id'] ?? null,
                    'amount'            => $debt,
                    'type'              => 'debt',
                    'user_id'           => auth()->id(),
                    'notes'             => "Sale #{$sale->sale_number}" . __('resource.creditBalance'),
                ]);
            }


            foreach ($lines as $line) {
                SalesItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $line['product_id'],
                    'quantity'   => $line['quantity'],
                    'price'      => $line['price'],
                    'cost_price' => $line['cost_price'],
                    'total'      => $line['total'],
                ]);

                $line['inventory']->decrement('quantity', $line['quantity']);
            }

            return $sale;
        });
    }
}
