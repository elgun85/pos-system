<?php

namespace App\Filament\Widgets\Dashboard;

use App\Services\DashboardService;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SalesStatsWidget extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = ' Satış və  Kassaya daxil olan vəsaitlər';
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



        $totalPayments = $dashboardService->totalPayments($startDate, $endDate);
        $cashPayments = $dashboardService->cashPayments($startDate, $endDate);
        $cardPayments = $dashboardService->cardPayments($startDate, $endDate);




        return [
            // Satış Növləri və Dövriyyə
            Stat::make('Ümumi satış', number_format($allSales, 2) . ' AZN')
                ->description('Ümumi yaradılan satış dövriyyəsi')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info'),

            Stat::make('Tam ödənilmiş satış', number_format($completedSales, 2) . ' AZN')
                ->description('Məbləği tam bağlanan satışlar')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Hissəli satış', number_format($partialSales, 2) . ' AZN')
                ->description('Müəyyən hissəsi ödənilən satışlar')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Nisyə satış', number_format($creditSales, 2) . ' AZN')
                ->description('Tamamilə borca edilən satışlar')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('danger'),


            // Kassaya Daxil Olan Vəsaitlər
            Stat::make('Ümumi daxil olan pul', number_format($totalPayments, 2) . ' AZN')
                ->description('Kassaya real daxil olan ümumi məbləğ')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Nağd', number_format($cashPayments, 2) . ' AZN')
                ->description('Nağd şəkildə toplanan vəsait')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            Stat::make('Kart', number_format($cardPayments, 2) . ' AZN')
                ->description('Pos-terminal/Kart ilə ödənişlər')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('info'),

        ];
    }
}
