<?php

namespace App\Exports;

use App\Models\Inventory;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InventoryReportExport
{

    public static function download(): StreamedResponse
    {
        $fileName = 'inventory_report_' . date('Y-m-d_H-i-s') . '.csv';

        $headers =
            [
                'Content-Type'        => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
                'Pragma'              => 'no-cache',
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
                'Expires'             => '0',
            ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            fputcsv(
                $file,
                [
                    __('resource.inventory.product.name'),
                    __('resource.category.name'),
                    __('resource.inventory.quantity'),
                ],
                ','
            );

            Inventory::query()
                ->with('product.category')
                ->whereHas('product', function ($q) {
                    $q->activeProduct();
                })
                ->lazy(1000)
                ->each(function ($item) use ($file) {
                    fputcsv($file, [
                        $item->product?->name ?? '',
                        $item->product?->category?->name ?? '',
                        $item->quantity,
                    ]);
                });
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
}
