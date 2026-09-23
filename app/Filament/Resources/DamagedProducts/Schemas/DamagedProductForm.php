<?php

namespace App\Filament\Resources\DamagedProducts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DamagedProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([



                Select::make('product_id')
                    ->label(__('resource.product.name'))
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),


                TextInput::make('quantity')
                    ->label(__('resource.purchase.quantity'))
                    ->numeric()
                    ->default(1)
                    ->minValue(0.01)
                    ->required(),

                Textarea::make('notes')
                    ->label(__('resource.customer_deb.note'))
                    ->nullable()
                    ->maxLength(300)
                    ->columnSpanFull(),
            ]);
    }
}
