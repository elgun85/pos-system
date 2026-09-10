<?php

namespace App\Filament\Widgets\Dashboard;

use App\Services\DashboardService;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CashStatsWidget extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = ' Maliyyə və Anbar ';
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $startDate = $this->pageFilters['startDate'] ?? null;
        $endDate = $this->pageFilters['endDate'] ?? null;

        $dashboardService = app(DashboardService::class);

        $totalReturns = $dashboardService->totalReturns($startDate, $endDate);
        $totalDamages = $dashboardService->totalDamages($startDate, $endDate);
        $totalExpenses = $dashboardService->totalExpenses($startDate, $endDate);
        $netSales = $dashboardService->netSales($startDate, $endDate);
        $netProfit = $dashboardService->netProfit($startDate, $endDate);




        return [


            Stat::make('Qaytarılan Məbləğ', number_format($totalReturns, 2) . ' AZN')
                ->description('Müştərilərdən geri alınan mallar')
                ->descriptionIcon('heroicon-m-arrow-uturn-left')
                ->color('danger'),

            Stat::make('Xarab Olmuş Məhsullar', number_format($totalDamages, 2) . ' AZN')
                ->description('Zədələnmiş mal itkisi')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),

            Stat::make('Daxili Xərclər', number_format($totalExpenses, 2) . ' AZN')
                ->description('Mağaza daxili xərclər')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('danger'),

            Stat::make('Xalis Satış (Net)', number_format($netSales, 2) . ' AZN')
                ->description('Qaytarmalar çıxıldıqdan sonra xalis dövriyyə')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('success'),

            Stat::make('Xalis Gəlir (Net)', number_format($netProfit, 2) . ' AZN')
                ->description($netProfit >= 0 ? 'Bütün xərclər çıxıldıqdan sonra xalis mənfəət' : 'Diqqət: Zərər qeydə alınıb')
                ->descriptionIcon($netProfit >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($netProfit >= 0 ? 'success' : 'danger'),

        ];
    }
}
