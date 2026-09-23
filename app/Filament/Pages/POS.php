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

    public  static function getNavigationLabel(): string
    {
        return __('resource.sale_pos.navigationLabel');
    }

    public function getTitle(): string
    {
        return __('resource.sale_pos.pluralModelLabel');
    }

    public static function getNavigationGroup(): string
    {
        return __('resource.navigationGroup.sale');
    }
    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ShoppingCart;


    protected string $view = 'filament.pages.p-o-s';
}
