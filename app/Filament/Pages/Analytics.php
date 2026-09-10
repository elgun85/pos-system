<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\Analytics\CashChartWidget;
use App\Filament\Widgets\Analytics\ExpenseChart;
use App\Filament\Widgets\Analytics\PaymentsByDays;
use App\Filament\Widgets\Analytics\SalesChartWidget;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;


class Analytics extends BaseDashboard
{
    use HasFiltersForm;
    protected static ?string $navigationLabel = 'Qrafiklər';
    protected static string | UnitEnum | null $navigationGroup = 'ANALİTİKA';

    // 🛑 1. ROUTE XƏTASI ALMAMAQ ÜÇÜN BU 2 SƏTİR MÜTLƏQDİR:
    protected static string $routePath = 'analytics'; // Tip mütləq "string" olmalıdır (?string YOX!)
    protected static ?string $slug = 'analytics';

    // protected static ?string $navigationLabel = 'Analitika';
    protected static ?string $title = 'Analitik statistikalar';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::ChartBar;


    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        DatePicker::make('startDate')->label('Başlanğıc tarixi'),
                        DatePicker::make('endDate')->label('Bitmə tarixi'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    // Yalnız bu səhifədə görünəcək vidcetlər
    public function getWidgets(): array
    {
        return [
            PaymentsByDays::class,
            SalesChartWidget::class,
            CashChartWidget::class,
            ExpenseChart::class,


        ];
    }
}
