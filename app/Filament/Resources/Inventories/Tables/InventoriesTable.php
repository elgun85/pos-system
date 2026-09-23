<?php

namespace App\Filament\Resources\Inventories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InventoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('product.name')
                    ->label(__('resource.inventory.product.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('product.brand.name')
                    ->label(__('resource.inventory.brand.name'))
                    ->sortable(),

                TextColumn::make('product.sale_price')
                    ->label(__('resource.inventory.sale_cost'))
                    ->prefix('₼')
                    ->sortable(),

                TextColumn::make('quantity')
                    ->label(__('resource.inventory.quantity'))
                    ->numeric()
                    ->badge()
                    ->color(fn($state) => match (true) {

                        $state == 0 => 'danger',

                        $state <= 5 => 'warning',

                        default => 'success',
                    })
                    ->sortable()
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label(__('resource.inventory.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('resource.inventory.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()->iconButton(),
                DeleteAction::make()->iconButton(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
