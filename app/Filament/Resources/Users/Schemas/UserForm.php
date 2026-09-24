<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                ->label(__('resource.users.name'))
                    ->required(),

                TextInput::make('email')
                    ->label(__('resource.users.email'))
                    ->email()
                    ->required(),

                TextInput::make('password')
                ->label(__('resource.users.password'))
                    ->password()
                    ->required(),

                    Select::make('roles')
                    ->label(__('resource.users.role'))
                    ->relationship('roles','name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                   
                    ,

            ]);
    }
}
