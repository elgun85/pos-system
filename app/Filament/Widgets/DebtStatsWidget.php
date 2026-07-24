<?php

namespace App\Filament\Widgets;

use App\Services\DashboardService;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DebtStatsWidget extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Borclar və ödənişlər';
    protected static ?int $sort = 3;


    protected function getStats(): array
    {

        $startDate = $this->pageFilters['startDate'] ?? null;
        $endDate = $this->pageFilters['endDate'] ?? null;

        $dashboardService = app(DashboardService::class);

        $partialDebt = $dashboardService->partialDebt($startDate, $endDate);
        $creditDebt = $dashboardService->creditDebt($startDate, $endDate);
        $debtPayments = $dashboardService->debtPayments($startDate, $endDate);
        $currentDebt = $dashboardService->currentDebt();
        return [
            Stat::make('Hissəli satış borcu', number_format($partialDebt, 2) . ' AZN'),
            Stat::make('Tam nisyə borcu', number_format($creditDebt, 2) . ' AZN'),
            Stat::make('Borclardan daxil olan ödəniş', number_format($debtPayments, 2) . ' AZN'),
            Stat::make('Cari qalıq borc', number_format($currentDebt, 2) . ' AZN') ->color('danger'),

        ];
    }
}
