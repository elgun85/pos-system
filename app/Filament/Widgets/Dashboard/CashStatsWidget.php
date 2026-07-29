<?php

namespace App\Filament\Widgets\Dashboard;

use App\Services\DashboardService;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CashStatsWidget extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = ' Kassaya daxil olan vəsaitlər';
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $startDate = $this->pageFilters['startDate'] ?? null;
        $endDate = $this->pageFilters['endDate'] ?? null;

        $dashboardService = app(DashboardService::class);

        $totalPayments = $dashboardService->totalPayments($startDate, $endDate);
        $cashPayments = $dashboardService->cashPayments($startDate, $endDate);
        $cardPayments = $dashboardService->cardPayments($startDate, $endDate);



        return [
            Stat::make('Ümumi daxil olan pul',       number_format($totalPayments, 2) . ' AZN'),
            Stat::make('Nağd',       number_format($cashPayments, 2) . ' AZN'),
            Stat::make('Kart',       number_format($cardPayments, 2) . ' AZN'),
        ];
    }
}
