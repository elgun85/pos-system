<?php

namespace App\Http\Controllers;

use App\Models\Sale;

class SalePrintController extends Controller
{
    public function __invoke(Sale $sale)
    {
        $sale->load('items.product');

        return view('sales.print', compact('sale'));
    }
}
