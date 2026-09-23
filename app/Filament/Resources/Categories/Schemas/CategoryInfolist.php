<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Models\Category;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label(__('resource.category.name')),
                TextEntry::make('parent.name')
                    ->label(__('resource.category.parent.name'))
                    ->badge()->color('danger')
                    ->placeholder('-'),
                IconEntry::make('status')
                    ->boolean(),

                TextEntry::make('deleted_at')
                    ->label(__('resource.category.deleted_at'))
                    ->dateTime()
                    ->visible(fn(Category $record): bool => $record->trashed()),
            ]);
    }
}
