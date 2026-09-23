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
                    ->label(__('resource.supplier.name')),

                TextEntry::make('phone')
                    ->label(__('resource.supplier.phone'))
                    ->placeholder('-'),

                TextEntry::make('email')
                    ->label(__('resource.supplier.email'))
                    ->placeholder('-'),

                TextEntry::make('address')
                    ->label(__('resource.supplier.address'))
                    ->placeholder('-'),
                    
                IconEntry::make('status')
                    ->boolean(),

                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn(Supplier $record): bool => $record->trashed()),
            ]);
    }
}
