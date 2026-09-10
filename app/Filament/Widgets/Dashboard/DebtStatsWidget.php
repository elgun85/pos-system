<?php

namespace App\Filament\Widgets\Dashboard;

use App\Services\DashboardService;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DebtStatsWidget extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Borclar və ödənişlər';
    protected static ?int $sort = 40;


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

            Stat::make('Hissəli satış borcu', number_format($partialDebt, 2) . ' AZN')
                ->description('Hissəli satışlardan qalan gözlənilən məbləğ')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Tam nisyə borcu', number_format($creditDebt, 2) . ' AZN')
                ->description('Tam nisyə verilən malların ümumi məbləği')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning'),

            Stat::make('Borclardan daxil olan ödəniş', number_format($debtPayments, 2) . ' AZN')
                ->description('Müştərilərin ödədikləri borc məbləğləri')
                ->descriptionIcon('heroicon-m-arrow-down-circle')
                ->color('success'),

            Stat::make('Cari qalıq borc', number_format($currentDebt, 2) . ' AZN')
                ->description('Müştərilərin mağazaya olan ümumi aktiv borcu')
                ->descriptionIcon('heroicon-m-scale')
                ->color('danger'),

        ];
    }
}
