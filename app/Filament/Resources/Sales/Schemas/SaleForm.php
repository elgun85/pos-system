<?php

namespace App\Filament\Resources\Sales\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SaleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('sale_number')
                    ->label('Satış nömrəsi'),

                Select::make('payment_method_id')
                    ->label('Ödəmə üsulu')
                    ->relationship('paymentMethod', 'name'),


                TextInput::make('total')
                    ->label('Cəmi')
                    ->numeric()
                    ->suffix('₼')
                    ->readOnly()
                    ->disabled(),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Gözləmədə',
                        'completed' => 'Tamamlanıb',
                        'cancelled' => 'Ləğv edilib',
                    ]),

            ]);
    }
}
