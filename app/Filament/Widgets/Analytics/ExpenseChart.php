<?php

//namespace App\Filament\app\Widgets\Analytics;
namespace App\Filament\Widgets\Analytics; // 👈 Xüsusi diqqət: Analytics əlavə olundu

use App\Services\DashboardService;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class ExpenseChart extends ChartWidget
{
    use InteractsWithPageFilters;
    public function getHeading(): ?string
    {
        return __('resource.expense_header');
    }
    protected static bool $isDiscovered = false;
    protected static ?int $sort = 50;

    protected function getData(): array
    {
        $startDate = $this->pageFilters['startDate'] ?? null;
        $endDate = $this->pageFilters['endDate'] ?? null;
        $dashboardService  = app(DashboardService::class);

        $expensesData = $dashboardService->expensesByCategory($startDate, $endDate);
        $expenses = $dashboardService->totalExpenses($startDate, $endDate);


        return [
            'datasets' =>    [
                [
                    'label' =>   __('resource.totalExpenses'),
                    'data' => $expensesData->values()->toArray(),

                    'backgroundColor' =>
                    [
                        '#ef4444',
                        '#f97316',
                        '#f59e0b',
                        '#10b981',
                        '#06b6d4',
                        '#3b82f6',
                        '#8b5cf6',
                        '#ec4899'

                    ],

                    'borderRadius' => 10,
                ]
            ],
            'labels' => $expensesData->keys()->toArray(),
            //  'labels' => 'salam',
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
