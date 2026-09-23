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
                    ->label(__('resource.customer_deb.name'))
                    ->required()
                    ->maxLength(255),

                TextInput::make('phone')
                    ->label(__('resource.customer_deb.phone'))
                    ->required()
                    ->maxLength(255),

                TextInput::make('address')
                    ->label(__('resource.customer_deb.address'))
                    ->required()
                    ->maxLength(255),
            ]);
    }
}
