<?php

namespace App\Filament\Widgets\Analytics;

use App\Services\DashboardService;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class CashChartWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Ödəniş üsulları';
    protected static ?int $sort = 4;


    protected function getData(): array
    {
        $startDate = $this->pageFilters['startDate'] ?? null;
        $endDate = $this->pageFilters['endDate'] ?? null;

        $service  = app(DashboardService::class);

        $cash = $service->cashPayments($startDate, $endDate);
        $card = $service->cardPayments($startDate, $endDate);
        $totalPayments = $service->totalPayments($startDate, $endDate);
        return [
            'datasets' => [
                [
                    'data' => [
                        $totalPayments,
                        $cash,
                        $card,
                    ],

                    'backgroundColor' => [
                        '#3b82f6', // mavi - umumi
                        '#22c55e', // yaşıl - nagd
                        '#ef4444', // qırmızı - card
                    ],

                    'borderColor' => [
                        '#3b82f6',
                        '#22c55e',
                        '#ef4444',
                    ],

                    'borderWidth' => 1,
                ],
            ],

            'labels' => [
                'Ümumi daxil olan pul',
                'Nağd',
                'Kart',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
