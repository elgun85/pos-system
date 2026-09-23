<?php

namespace App\Filament\Widgets\Analytics;

use App\Services\DashboardService;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class CashChartWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    public function getHeading(): ?string
    {
        return __('resource.cashChart_header');
    }
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
                __('resource.totalPayments'),
                __('resource.cashPayments'),
                __('resource.cardPayments'),
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
