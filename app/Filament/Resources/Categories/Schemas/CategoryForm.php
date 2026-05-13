<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Kateqoriya Adı')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('name', mb_convert_case($state, MB_CASE_TITLE, 'UTF-8'));
                    }),
                Select::make('parent_id')
                    ->label('Alt Kateqoriya')
                    ->relationship('parent', 'name')
                    ->nullable()
                    ->preload()
                    ->searchable(),
                Toggle::make('status')
                    ->label('Status')
                    ->default(true)
                    ->required(),
            ]);
    }
}
