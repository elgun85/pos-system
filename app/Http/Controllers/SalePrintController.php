<?php

namespace App\Http\Controllers;

use App\Models\Sale;

class SalePrintController extends Controller
{
    public function __invoke(Sale $sale)
    {
        $sale->load(['user','items.product.brand']);

        return view('sales.print', compact('sale'));
    }
}
