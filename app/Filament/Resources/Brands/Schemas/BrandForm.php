<?php

namespace App\Filament\Resources\Brands\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BrandForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Brand Adı')
                    ->required()
                    ->placeholder('Brand adını daxil edin')
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('name', mb_convert_case($state, MB_CASE_TITLE, 'UTF-8'));
                    }),

                FileUpload::make('logo')
                    ->label('Logo və ya şəkil')
                    ->image()
                    ->disk('public')
                    ->directory('brands')
                    ->maxSize(4096) 
                    ->imageEditor()
                    ->reorderable()
                    ->openable()
                    ->deletable(true)
                    ->placeholder('Şəkil seçilməyib')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp']),
                Toggle::make('status')
                    ->label('Status')
                    ->default(true),
            ]);
    }
}
