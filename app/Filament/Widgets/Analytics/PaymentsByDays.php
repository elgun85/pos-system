<?php

namespace App\Filament\Widgets\Analytics;

use App\Services\DashboardService;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class PaymentsByDays extends ChartWidget
{
    use InteractsWithPageFilters;
   protected static bool $isDiscovered = false;
    protected ?string $heading = 'Günlər üzrə  Ödənişlər';
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
                    'label' => 'Blog posts created',
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
