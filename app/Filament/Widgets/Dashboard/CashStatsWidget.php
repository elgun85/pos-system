<?php

namespace App\Filament\Widgets\Dashboard;

use App\Services\DashboardService;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CashStatsWidget extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected function getHeading(): ?string
    {
        return __('resource.cash_header');
    }
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


            Stat::make(__('resource.totalReturns'), number_format($totalReturns, 2) .   __('resource.money'))
                ->description(__('resource.totalReturnsDesc'))
                ->descriptionIcon('heroicon-m-arrow-uturn-left')
                ->color('danger'),

            Stat::make(__('resource.totalDamages'), number_format($totalDamages, 2) .   __('resource.money'))
                ->description(__('resource.totalDamagesDesc'))
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),

            Stat::make(   __('resource.totalExpenses'), number_format($totalExpenses, 2) .   __('resource.money'))
                ->description(   __('resource.totalExpensesDesc'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('danger'),

            Stat::make( __('resource.netSales'), number_format($netSales, 2) .   __('resource.money'))
                ->description( __('resource.netSalesDesc'))
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('success'),

            Stat::make( __('resource.netProfit'), number_format($netProfit, 2) .   __('resource.money'))
                ->description($netProfit >= 0 ?  __('resource.netProfitDesc'):__('resource.netLossDesc'))
                ->descriptionIcon($netProfit >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($netProfit >= 0 ? 'success' : 'danger'),

        ];
    }
}
