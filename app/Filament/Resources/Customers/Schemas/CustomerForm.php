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
                    ->label(__('resource.customer.name'))
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('name', mb_convert_case($state, MB_CASE_TITLE, 'UTF-8'));
                    })
                    ->required(),

                TextInput::make('phone')
                    ->label(__('resource.customer.phone'))
                    ->placeholder('+994 50 123 45 67')
                    ->tel(),

                TextInput::make('address')
                    ->label(__('resource.customer.address')),

                Toggle::make('status')
                    ->default(true),
            ]);
    }
}
