<?php

namespace App\Filament\Pages\Reports;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class SalesReport extends Page
{
    protected static string | UnitEnum | null $navigationGroup = 'ANBAR HESABATI';
    protected static ?string $navigationLabel = 'Satış Hesabatı';
    protected static ?string $title = 'Ən çox satılan';



    protected string $view = 'filament.pages.reports.sales-report';
}
