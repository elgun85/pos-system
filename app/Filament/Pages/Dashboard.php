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


class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    
    //protected static ?string $navigationLabel = 'Analitiksfsdfa';
    //protected static ?string $title = 'Analitik statistikalarfdfsdf';


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
        return[

        SalesStatsWidget::class,
        CashStatsWidget::class,
        DebtStatsWidget::class,
        
        ];
    }
}
