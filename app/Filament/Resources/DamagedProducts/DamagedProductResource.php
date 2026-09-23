<?php

namespace App\Filament\Resources\DamagedProducts;

use App\Filament\Resources\DamagedProducts\Pages\CreateDamagedProduct;
use App\Filament\Resources\DamagedProducts\Pages\EditDamagedProduct;
use App\Filament\Resources\DamagedProducts\Pages\ListDamagedProducts;
use App\Filament\Resources\DamagedProducts\Schemas\DamagedProductForm;
use App\Filament\Resources\DamagedProducts\Tables\DamagedProductsTable;
use App\Models\Damage;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class DamagedProductResource extends Resource
{
    protected static ?string $model = Damage::class;
    public  static function getNavigationLabel(): string
    {
        return __('resource.damage.navigationLabel');
    }

    public  static function getModelLabel(): string
    {
        return __('resource.damage.modelLabel');
    }

    public  static function getPluralModelLabel(): string
    {
        return __('resource.damage.pluralModelLabel');
    }


    public static function getNavigationGroup(): string
    {
        return __('resource.navigationGroup.inventory');
    }



    public static function form(Schema $schema): Schema
    {
        return DamagedProductForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DamagedProductsTable::configure($table);
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
            'index' => ListDamagedProducts::route('/'),
            'create' => CreateDamagedProduct::route('/create'),
            'edit' => EditDamagedProduct::route('/{record}/edit'),
        ];
    }
}
