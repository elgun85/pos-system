<?php

namespace App\Filament\Widgets\Dashboard;

use App\Services\DashboardService;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SalesStatsWidget extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = ' Satış statistikaları';
    protected static ?int $sort = 1;



    protected function getStats(): array
    {
        $startDate = $this->pageFilters['startDate'] ?? null;
        $endDate = $this->pageFilters['endDate'] ?? null;

        $dashboardService = app(DashboardService::class);


        $allSales = $dashboardService->allSales($startDate, $endDate);
        $completedSales = $dashboardService->completedSales($startDate, $endDate);
        $partialSales = $dashboardService->partialSales($startDate, $endDate);
        $creditSales = $dashboardService->creditSales($startDate, $endDate);



        return [
            Stat::make('Ümumi satiş', number_format($allSales, 2) . ' AZN'),
            Stat::make('Tam ödənilmiş satış', number_format($completedSales, 2) . ' AZN')->color('danger'),
            Stat::make('Hissəli satış', number_format($partialSales, 2) . ' AZN')->color('danger'),
            Stat::make('Nisyə satış', number_format($creditSales, 2) . ' AZN'),

        ];
    }
}
