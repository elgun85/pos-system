<?php

namespace App\Filament\Resources\CustomerDebts;

use App\Filament\Resources\CustomerDebts\Pages\CreateCustomerDebt;
use App\Filament\Resources\CustomerDebts\Pages\EditCustomerDebt;
use App\Filament\Resources\CustomerDebts\Pages\ListCustomerDebts;
use App\Filament\Resources\CustomerDebts\Pages\ViewCustomerDebt;
use App\Filament\Resources\CustomerDebts\Schemas\CustomerDebtForm;
use App\Filament\Resources\CustomerDebts\Schemas\CustomerDebtInfolist;
use App\Filament\Resources\CustomerDebts\Tables\CustomerDebtsTable;
use App\Models\Customer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use UnitEnum;

class CustomerDebtResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationLabel = 'Borclu Müştərilər';
    protected static ?string $pluralModelLabel = 'Borclu Müştərilər';
    protected static string | UnitEnum | null $navigationGroup = 'MÜŞTƏRİ BORCLARI';
    protected static ?int $navigationSort = 6;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CurrencyDollar;

    protected static ?string $recordTitleAttribute = 'name';


    public static function getEloquentQuery(): Builder
    {
        return Customer::query()
            ->debtors();
    }

    public static function form(Schema $schema): Schema
    {
        return CustomerDebtForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CustomerDebtInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CustomerDebtsTable::configure($table);
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
            'index' => ListCustomerDebts::route('/'),
            'view' => ViewCustomerDebt::route('/{record}'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
