<?php

namespace App\Filament\Resources\Purchases;

use App\Filament\Resources\Purchases\Pages\CreatePurchase;
use App\Filament\Resources\Purchases\Pages\EditPurchase;
use App\Filament\Resources\Purchases\Pages\ListPurchases;
use App\Filament\Resources\Purchases\Schemas\PurchaseForm;
use App\Filament\Resources\Purchases\Tables\PurchasesTable;
use App\Models\Purchase;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;

class PurchaseResource extends Resource
{
    protected static ?string $model = Purchase::class;

    public  static function getNavigationLabel(): string
    {
        return __('resource.purchase.navigationLabel');
    }

    public  static function getModelLabel(): string
    {
        return __('resource.purchase.modelLabel');
    }

    public  static function getPluralModelLabel(): string
    {
        return __('resource.purchase.pluralModelLabel');
    }

    public static function getNavigationGroup(): string
    {
        return __('resource.navigationGroup.supplier');
    }
    protected static ?int $navigationSort = 4;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    protected static ?string $recordTitleAttribute = 'invoice_number';


    public static function form(Schema $schema): Schema
    {
        return PurchaseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PurchasesTable::configure($table);
    }

    public static function getNavigationBadge(): ?string
    {
        return Cache::remember(
            'purchases',
            now()->addMinutes(10),
            fn() =>  static::getModel()::where('status', true)
                ->count()

        );
    }



    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPurchases::route('/'),
            'create' => CreatePurchase::route('/create'),
            'edit' => EditPurchase::route('/{record}/edit'),
        ];
    }
}
