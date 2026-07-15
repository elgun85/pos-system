<?php

namespace App\Filament\Resources\Sales\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SaleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('sale_number')
                    ->placeholder('-'),
                TextEntry::make('payments.paymentMethod.name')
                    ->label('Ödəmə üsulu')
                    ->placeholder('-'),
                TextEntry::make('total')
                    ->label('Cəmi')
                    ->money('AZN')
                    ->placeholder('-'),




                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),


                RepeatableEntry::make('items')
                    ->label('Məhsullar')
                    ->schema([
                        TextEntry::make('product.name')
                            ->label('Məhsul'),

                        TextEntry::make('quantity')
                            ->label('Say'),

                        TextEntry::make('price')
                            ->label('Qiymət')
                            ->money('AZN'),

                        TextEntry::make('total')
                            ->label('Məbləğ')
                            ->state(fn($record) => $record->quantity * $record->price)
                            ->money('AZN'),


                    ])
                    ->columns(4),
            ]);
    }
}
