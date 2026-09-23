<?php

namespace App\Filament\Resources\CustomerTransactions;

use App\Filament\Resources\CustomerTransactions\Pages\CreateCustomerTransaction;
use App\Filament\Resources\CustomerTransactions\Pages\EditCustomerTransaction;
use App\Filament\Resources\CustomerTransactions\Pages\ListCustomerTransactions;
use App\Filament\Resources\CustomerTransactions\Pages\ViewCustomerTransaction;
use App\Filament\Resources\CustomerTransactions\Schemas\CustomerTransactionForm;
use App\Filament\Resources\CustomerTransactions\Schemas\CustomerTransactionInfolist;
use App\Filament\Resources\CustomerTransactions\Tables\CustomerTransactionsTable;
use App\Models\CustomerTransaction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CustomerTransactionResource extends Resource
{
    protected static ?string $model = CustomerTransaction::class;

 public  static function getNavigationLabel(): string
    {
        return __('resource.customer_tran.navigationLabel');
    }

    public  static function getModelLabel(): string
    {
        return __('resource.customer_tran.modelLabel');
    }

    public  static function getPluralModelLabel(): string
    {
        return __('resource.customer_tran.pluralModelLabel');
    }

    public static function getNavigationGroup(): string
    {
        return __('resource.navigationGroup.customer');
    }
    protected static ?int $navigationSort = 7;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentList;

    public static function form(Schema $schema): Schema
    {
        return CustomerTransactionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CustomerTransactionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CustomerTransactionsTable::configure($table);
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
            'index' => ListCustomerTransactions::route('/'),
            //'create' => CreateCustomerTransaction::route('/create'),
            //'view' => ViewCustomerTransaction::route('/{record}'),
            //'edit' => EditCustomerTransaction::route('/{record}/edit'),
        ];
    }
}
