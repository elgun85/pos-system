<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')->label(__('resource.users.name')),

                TextEntry::make('email')
                    ->label(__('resource.users.email')),


                TextEntry::make('created_at')
                    ->label(__('resource.users.created_at'))
                    ->dateTime()
                    ->placeholder('-'),

            ]);
    }
}
