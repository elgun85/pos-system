<?php

namespace App\Filament\Widgets\Dashboard;

use App\Services\DashboardService;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DebtStatsWidget extends StatsOverviewWidget
{
    use InteractsWithPageFilters;


    protected function getHeading(): ?string
    {
        return __('resource.debt_header');
    }

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

            Stat::make(__('resource.partialDebt'), number_format($partialDebt, 2) .   __('resource.money'))
                ->description(__('resource.partialDebtDesc'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make(__('resource.creditDebt'), number_format($creditDebt, 2) .   __('resource.money'))
                ->description(__('resource.creditDebtDesc'))
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning'),

            Stat::make(__('resource.debtPayments'), number_format($debtPayments, 2) .   __('resource.money'))
                ->description(__('resource.debtPaymentsDesc'))
                ->descriptionIcon('heroicon-m-arrow-down-circle')
                ->color('success'),

            Stat::make(__('resource.currentDebt'), number_format($currentDebt, 2) .   __('resource.money'))
                ->description(__('resource.currentDebtDesc'))
                ->descriptionIcon('heroicon-m-scale')
                ->color('danger'),

        ];
    }
}
