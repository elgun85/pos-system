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
                TextInput::make('sku')
                    ->label('Barkod')
                    ->placeholder('Məhsulun Barkod-unu daxil edin')
                    ->unique()
                    ->maxLength(255)
                    ->required(),
                TextInput::make('name')
                    ->label('Məhsul Adı')
                    ->placeholder('Məhsul adını daxil edin')
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('name', mb_convert_case($state, MB_CASE_TITLE, 'UTF-8'));
                    })
                    ->maxLength(255)
                    ->required(),
                FileUpload::make('image')
                    ->label('Məhsul Şəkli')
                    ->disk('public')
                    ->directory('products')
                    ->maxSize(4096)
                    ->imageEditor()
                    ->reorderable()
                    ->openable()
                    ->deletable(true)
                    ->placeholder('Şəkil seçilməyib')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp']),

                Select::make('category_id')
                    ->label('Kateqoriya')
                    ->relationship('category', 'name')
                    ->preload()
                    ->native(false)
                    ->searchable()
                    ->required(),


                    Select::make('brand_id')
                    ->label('Brend')
                    ->relationship('brand', 'name')
                    ->preload()
                    ->native(false)
                    ->searchable()
                   , 


                /*                 TextInput::make('barcode')
                    ->label('Barkod')
                    ->required(), */
                TextInput::make('cost_price')
                    ->label('Alış Qiyməti')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('sale_price')
                    ->label('Satış Qiyməti')
                    ->required()
                    ->numeric()
                    ->prefix('₼'),
                Select::make('supplier_id')
                    ->label('Təchizatçı')
                    ->relationship('supplier', 'name')
                    ->preload()
                    ->native(false)
                    ->searchable(),
                Select::make('status')
                    ->label('Status')
                    ->options(['active' => 'Active', 'inactive' => 'Inactive'])
                    ->default('active')
                    ->required(),
            ]);
    }
}
