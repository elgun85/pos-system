<?php

namespace App\Filament\Widgets\Analytics;

use App\Services\DashboardService;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Override;

class SalesChartWidget extends ChartWidget
{
    use InteractsWithPageFilters;

   #[Override]
   public function getHeading(): ?string
   {
    return __('resource.salesChartHeader');
   }
    protected static ?int $sort = 3;


    protected function getData(): array
    {
        $startDate = $this->pageFilters['startDate'] ?? null;
        $endDate = $this->pageFilters['endDate'] ?? null;

        $dashboardService  = app(DashboardService::class);

        $allSales = $dashboardService->allSales($startDate, $endDate);
        $completedSales = $dashboardService->completedSales($startDate, $endDate);
        $partialSales = $dashboardService->partialSales($startDate, $endDate);
        $creditSales = $dashboardService->creditSales($startDate, $endDate);
        return [
            'datasets' =>    [
                [
                    'data' =>
                    [
                        $allSales,
                        $completedSales,
                        $partialSales,
                        $creditSales,
                    ],
                    'backgroundColor' =>
                    [
                        '#000080', // mavi - AllSales
                        '#004d00', // myasil tund - completedSales
                        '#665200', // sari tund - partialSales
                        '#990000', // qirmizi - creditSales

                    ],
                    'borderColor' =>
                    [
                        '#000080', // mavi - AllSales
                        '#004d00', // myasil tund - completedSales
                        '#665200', // sari tund - partialSales
                        '#990000', // qirmizi - creditSales
                    ],
                    'borderWidth' => 1,
                ]


            ],

            'labels' =>
            [
                __('resource.allSales'),
                __('resource.completedSales'),
                __('resource.partialSales'),
                __('resource.creditSales'),


            ]
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
