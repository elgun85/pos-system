<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class POS extends Page
{
    protected Width|string|null $maxContentWidth = Width::Full;

   // protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationLabel = 'Kassa / POS';
    protected static string | UnitEnum | null $navigationGroup = 'Satış';
    protected static ?int $navigationSort = 1; 

    //   protected ?string $heading = 'Kassa Satışı';
    protected static ?string $title = 'POS';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::ShoppingCart;


    protected string $view = 'filament.pages.p-o-s';
}
