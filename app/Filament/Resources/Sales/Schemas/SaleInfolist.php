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
                    ->label(__('resource.sale.number'))
                    ->placeholder('-'),

                TextEntry::make('payments.paymentMethod.name')
                    ->label(__('resource.sale.payment'))
                    ->placeholder('-'),

                TextEntry::make('total')
                    ->label(__('resource.sale.total'))
                    ->money(__('resource.money_icon'))
                    ->placeholder('-'),

                TextEntry::make('created_at')
                    ->label(__('resource.sale.created_at'))
                    ->dateTime()
                    ->placeholder('-'),


                RepeatableEntry::make('items')
                    ->schema([
                        TextEntry::make('product.name')
                            ->label(__('resource.sale.product')),

                        TextEntry::make('quantity')
                            ->label(__('resource.sale.quantity')),

                        TextEntry::make('price')
                            ->label(__('resource.sale.price'))
                            ->money(__('resource.money_icon')),

                        TextEntry::make('total')
                            ->label(__('resource.sale.total'))
                            ->state(fn($record) => $record->quantity * $record->price)
                            ->money(__('resource.money_icon')),


                    ])
                    ->columns(4),
            ]);
    }
}
