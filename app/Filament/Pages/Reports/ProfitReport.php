<?php

namespace App\Filament\Pages\Reports;

use Filament\Pages\Page;
use UnitEnum;

class ProfitReport extends Page
{
    protected static string | UnitEnum | null $navigationGroup = 'ANBAR HESABATI';
    protected static ?string $navigationLabel = ' Mənfəət Hesabatı';
    protected static ?string $title = ' Mənfəət Hesabatı';

    protected string $view = 'filament.pages.reports.profit-report';
}
