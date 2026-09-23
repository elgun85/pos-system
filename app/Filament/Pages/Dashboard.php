<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\Dashboard\CashStatsWidget;
use App\Filament\Widgets\Dashboard\DebtStatsWidget;
use App\Filament\Widgets\Dashboard\SalesStatsWidget;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use UnitEnum;


class Dashboard extends BaseDashboard
{
    use HasFiltersForm;


    public function getTitle(): string
    {
        return __('resource.dashboard.pluralModelLabel');
    }
    //   protected static ?string $modelLabel = '  ';

    public static function getNavigationGroup(): string
    {
        return __('resource.navigationGroup.analyse');
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        DatePicker::make('startDate'),
                        DatePicker::make('endDate'),
                        // ...
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }

    public function getWidgets(): array
    {
        return [

            SalesStatsWidget::class,
            CashStatsWidget::class,
            DebtStatsWidget::class,

        ];
    }
}
