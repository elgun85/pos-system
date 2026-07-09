<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\Sale;
use App\Models\SalesItem;
use Exception;
use Illuminate\Support\Facades\DB;



class SalesService
{

    public function __construct()
    {
        //
    }


    public function createSale(array $cart, array $data): Sale
    {
        if (empty($cart)) {
            throw new Exception('Səbət boşdur. Satış üçün ən azı bir məhsul əlavə edin.');
        }

        return DB::transaction(function () use ($cart, $data) {

            // 1. Satış qeydini yaradırıq
            $sale = Sale::create([
                'sale_number'       => 'SALE-' . now()->format('YmdHis') . '-' . rand(1000, 9999),
                'total'             => $data['total'],
                'paid_amount'       => $data['paid_amount'],
                'payment_method_id' => $data['payment_method_id'],
                'discount'          => $data['discount_amount'],
                'user_id'           => auth()->id(),
                'status'            => 'completed',
            ]);

            // 2. Səbətdəki məhsulları dövr edirik
            foreach ($cart as $cartItem) {
                // Stok yarışının (Race Condition) qarşısını almaq üçün sətiri kilidləyirik
                $inventory = Inventory::where('product_id', $cartItem['product_id'])
                    ->lockForUpdate()
                    ->first();

                if (!$inventory || $inventory->quantity < $cartItem['quantity']) {
                    throw new Exception("{$cartItem['name']} məhsulundan stokda kifayət qədər yoxdur.");
                }

                // Satış elementini yazırıq
                SalesItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $cartItem['product_id'],
                    'quantity'   => $cartItem['quantity'],
                    'price'      => $cartItem['sale_price'],
                    'total'      => $cartItem['quantity'] * $cartItem['sale_price'],
                ]);

                // Stoku azaldırıq
                $inventory->decrement('quantity', $cartItem['quantity']);
            }

            return $sale;
        });
    }
}
