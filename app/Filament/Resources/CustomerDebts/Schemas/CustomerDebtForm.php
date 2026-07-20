<?php

namespace App\Filament\Resources\CustomerDebts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CustomerDebtForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Ad, Soyad')
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label('Telefon')
                    ->required()
                    ->maxLength(255),
                TextInput::make('address')
                    ->label('Ünvan')
                    ->required()
                    ->maxLength(255),
            ]);
    }
}
