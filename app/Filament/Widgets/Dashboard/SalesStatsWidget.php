<?php

namespace App\Filament\Widgets\Dashboard;

use App\Services\DashboardService;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SalesStatsWidget extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected function getHeading(): ?string
    {
        return __('resource.sales_header');
    }
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
            Stat::make(__('resource.allSales'), number_format($allSales, 2) .   __('resource.money'))
                ->description(__('resource.allSalesDesc'))
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info'),

            Stat::make(__('resource.completedSales'), number_format($completedSales, 2) .   __('resource.money'))
                ->description(__('resource.completedSalesDesc'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make(__('resource.partialSales'), number_format($partialSales, 2) .   __('resource.money'))
                ->description(__('resource.partialSalesDesc'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make(__('resource.creditSales'), number_format($creditSales, 2) .   __('resource.money'))
                ->description(__('resource.creditSalesDesc'))
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('danger'),


            // Kassaya Daxil Olan Vəsaitlər
            Stat::make(__('resource.totalPayments'), number_format($totalPayments, 2) .   __('resource.money'))
                ->description(__('resource.totalPaymentsDesc'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make(__('resource.cashPayments'), number_format($cashPayments, 2) .   __('resource.money'))
                ->description(__('resource.cashPaymentsDesc'))
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            Stat::make(__('resource.cardPayments'), number_format($cardPayments, 2) .   __('resource.money'))
                ->description(__('resource.cardPaymentsDesc'))
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('info'),

        ];
    }
}
