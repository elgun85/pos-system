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
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('name', mb_convert_case($state, MB_CASE_TITLE, 'UTF-8'));
                    }),

                FileUpload::make('logo')
                    ->label('Logo və ya şəkil')
                    ->image()
                    ->disk('public')
                    ->directory('brands')
                    ->imageEditor()
                    // Redaktə zamanı köhnə şəkli silib yenisini qoymaq imkanı yaradır:
                    ->reorderable()
                    ->openable()
                    ->deletable(true)
                    // Bu sətir vacibdir: əgər yükləmə zamanı problem olarsa, köhnəni göstərməyə davam etsin
                    ->placeholder('Şəkil seçilməyib')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp']),
                Toggle::make('status')
                    ->default(true),
            ]);
    }
}
