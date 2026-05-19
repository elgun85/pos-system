<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Ad')
                    ->placeholder('Adı daxil edin')
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('name', mb_convert_case($state, MB_CASE_TITLE, 'UTF-8'));
                    })
                    ->required(),

                TextInput::make('phone')
                    ->label('Telefon nömrəsi')
                    ->placeholder('+994 50 123 45 67')
                    ->tel(),

                TextInput::make('address')
                    ->label('Ünvan')
                    ->placeholder('Ünvanı daxil edin'),
                /*              TextInput::make('email')
                    ->label('Email address')
                    ->email(), 
             TextInput::make('points')
                    ->required()
                    ->numeric()
                    ->default(0), */
                Toggle::make('status')
                    ->default(true),
            ]);
    }
}
