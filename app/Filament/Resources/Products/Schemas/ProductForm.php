<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                FileUpload::make('image')
                    ->image(),
                TextInput::make('category_id')
                    ->required()
                    ->numeric(),
                TextInput::make('supplier_id')
                    ->required()
                    ->numeric(),
                TextInput::make('sku')
                    ->label('SKU'),
                TextInput::make('barcode')
                    ->required(),
                TextInput::make('cost_price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('sale_price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                Select::make('status')
                    ->options(['active' => 'Active', 'inactive' => 'Inactive'])
                    ->default('active')
                    ->required(),
            ]);
    }
}
