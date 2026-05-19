<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use App\Models\Supplier;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SupplierInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('Təchizatçının adı'),
                TextEntry::make('phone')
                    ->label('Telefon nömrəsi')
                    ->placeholder('-'),
                TextEntry::make('email')
                    ->label('Email address')
                    ->placeholder('-'),
                TextEntry::make('address')
                    ->label('Ünvan')
                    ->placeholder('-'),
                IconEntry::make('status')
                    ->boolean(),

                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Supplier $record): bool => $record->trashed()),
            ]);
    }
}
