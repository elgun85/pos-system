<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Settings;

class SalePrintController extends Controller
{
    public function __invoke(Sale $sale)
    {
        $sale->load(['user', 'items.product.brand']);

     // return view('sales.print', compact('sale'));
         return view('sales.print', [
            'sale' => $sale,
            'store' => Settings::current(),

        ]); 
    }
}
