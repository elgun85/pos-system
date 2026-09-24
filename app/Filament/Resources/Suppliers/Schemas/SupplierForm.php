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
                    ->label(__('resource.supplier.name'))
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('name', mb_convert_case($state, MB_CASE_TITLE, 'UTF-8'));
                    })
                    ->required(),

                TextInput::make('phone')
                    ->label(__('resource.supplier.phone'))
                    ->placeholder('+994 XX XXX XX XX')
                    ->tel(),

                TextInput::make('email')
                    ->label(__('resource.supplier.email'))
                    ->placeholder('... @email.com')
                    ->email(),

                TextInput::make('address')
                    ->label(__('resource.supplier.address')),

                Toggle::make('status')
                    ->default(true),
            ]);
    }
}
