<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SupplierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Təchizatçının adı')
                    ->placeholder('Təchizatçının adını daxil edin')
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('name', mb_convert_case($state, MB_CASE_TITLE, 'UTF-8'));
                    })
                    ->required(),
                TextInput::make('phone')
                ->label('Telefon nömrəsi')
                ->placeholder('+994 XX XXX XX XX')
                    ->tel(),
                TextInput::make('email')
                    ->label('Email address')
                    ->placeholder('... @email.com')
                    ->email(),
                TextInput::make('address')
                    ->label('Ünvan')
                    ->placeholder('Ünvani daxil edin'),
                Toggle::make('status')
                    ->default(true),
            ]);
    }
}
