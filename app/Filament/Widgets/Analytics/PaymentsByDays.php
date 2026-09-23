<?php

namespace App\Filament\Widgets\Analytics;

use App\Services\DashboardService;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class PaymentsByDays extends ChartWidget
{
    use InteractsWithPageFilters;
    public function getHeading(): ?string
    {
        return __('resource.payments');
    }
    protected static bool $isDiscovered = false;
    protected static ?int $sort = 9;


    protected function getData(): array
    {
        $startDate = $this->pageFilters['startDate'] ?? null;
        $endDate = $this->pageFilters['endDate'] ?? null;
        $dashboardService  = app(DashboardService::class);

        $payments  = $dashboardService->paymentsByDays($startDate, $endDate);
        return [
            'datasets' => [
                [
                    'label' => __('resource.payments'),
                    'data' => $payments
                        ->pluck('total')
                        ->map(fn($value) => (float) $value)
                        ->toArray(),
                    'backgroundColor' => '#F5274D',
                    'borderColor' => '#F5274D',
                    'fill' => false,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $payments
                ->pluck('day')
                ->map(fn($day) => \Carbon\Carbon::parse($day)->format('d.m'))
                ->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
