<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ProductsTable
{

    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')

            ->columns([
                TextColumn::make('sku')
                    ->label(__('resource.product.sku'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label(__('resource.product.name'))
                    ->limit(25)
                    ->sortable()
                    ->searchable(),


                ImageColumn::make('image')
                    ->label(__('resource.product.image'))
                    ->disk('public')
                    ->circular()
                    ->url(fn($record) => $record->image ? asset('storage/' . $record->image) : null)
                    ->openUrlInNewTab()
                    ->size(50),

                TextColumn::make('category.name')
                    ->label(__('resource.product.category.name'))
                    ->limit(15)
                    ->sortable(),

                TextColumn::make('brand.name')
                    ->label(__('resource.product.brand.name'))
                    ->limit(15)
                    ->sortable(),

                TextColumn::make('cost_price')
                    ->label(__('resource.product.cost_price'))
                    ->sortable(),

                TextColumn::make('sale_price')
                    ->label(__('resource.product.sale_price'))
                    ->color('success')
                    ->sortable(),

                TextColumn::make('inventory.quantity')
                    ->label(__('resource.product.inventory.quantity'))
                    ->badge()
                    ->color(fn($state) => match (true) {

                        $state == 0 => 'danger',

                        $state <= 5 => 'warning',

                        default => 'success',
                    })
                    ->numeric()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),

                ToggleColumn::make('is_favorite')
                    ->label(__('resource.product.fovorite'))
                    ->onIcon('heroicon-s-star')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('resource.product.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('resource.product.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->label(__('resource.product.deleted_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->defaultSort('created_at', 'desc')
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()->iconButton(),
                EditAction::make()->iconButton(),
                DeleteAction::make()->iconButton(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
