<?php

namespace App\Filament\Resources\Inventories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InventoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->relationship('product', 'name')
                    ->label(__('resource.product.name'))
                    ->searchable()
                    ->preload()


                    ->required(),
                TextInput::make('quantity')
                    ->required()
                    ->label(__('resource.inventory.quantity'))
                    ->placeholder(__('resource.inventory.quantity.plasholder'))
                    ->numeric(),
            ]);
    }
}
