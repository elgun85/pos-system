<?php

namespace App\Filament\Resources\CustomerTransactions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CustomerTransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                    ->label('Müştəri')
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('payment_method_id')
                    ->label('Ödəmə üsulu')
                    ->relationship('paymentMethod', 'name'),

                Select::make('sale_id')
                    ->label('Satış')
                    ->relationship('sale', 'sale_number')
                    ->searchable()
                    ->disabled()

                    ->preload()

                    ->required(),
                Select::make('user_id')
                    ->label('İstifadəçi')
                    ->relationship('user', 'name')
                    ->searchable()

                    ->preload(),

                Select::make('type')
                    ->options(['debt' => 'Debt', 'payment' => 'Payment', 'refund' => 'Refund', 'adjustment' => 'Adjustment'])
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
