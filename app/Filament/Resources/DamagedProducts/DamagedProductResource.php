<?php

namespace App\Filament\Resources\DamagedProducts;

use App\Filament\Resources\DamagedProducts\Pages\CreateDamagedProduct;
use App\Filament\Resources\DamagedProducts\Pages\EditDamagedProduct;
use App\Filament\Resources\DamagedProducts\Pages\ListDamagedProducts;
use App\Filament\Resources\DamagedProducts\Schemas\DamagedProductForm;
use App\Filament\Resources\DamagedProducts\Tables\DamagedProductsTable;
use App\Models\Damage;
use App\Services\DamageService;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class DamagedProductResource extends Resource
{
    protected static ?string $model = Damage::class;
    protected static string | UnitEnum | null $navigationGroup = 'ANBAR HESABATI';
    protected static ?string $navigationLabel = 'Xarab olmuş məhsullar';
    protected static ?string $pluralModelLabel = 'Xarab olmuş məhsullar';
    protected static ?string $modelLabel = ' Xarab olmuş məhsul';



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
