<?php

namespace App\Filament\Pages\Reports;

use Filament\Pages\Page;
use UnitEnum;

class TopSellingProducts extends Page
{

    protected static string | UnitEnum | null $navigationGroup = 'ANBAR HESABATI';
    protected static ?string $navigationLabel = 'Ən çox satılan';
    protected static ?string $modelLabel = 'Məhsul';


    protected string $view = 'filament.pages.reports.top-selling-products';
}
