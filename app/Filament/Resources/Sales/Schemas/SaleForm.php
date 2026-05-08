<?php

namespace App\Filament\Resources\Sales\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SaleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('customer_id')
                    ->numeric(),
                TextInput::make('payment_method_id')
                    ->numeric(),
                TextInput::make('total')
                    ->required()
                    ->numeric(),
                TextInput::make('paid_amount')
                    ->required()
                    ->numeric(),
                TextInput::make('discount')
                    ->required()
                    ->numeric()
                    ->default(0.0),
            ]);
    }
}
