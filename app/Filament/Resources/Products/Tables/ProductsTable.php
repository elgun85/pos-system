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
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ProductsTable
{

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sku')
                    ->label('Barkod')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->limit(20)
                    ->sortable()
                    ->searchable(),


                ImageColumn::make('image')
                    ->label('Şəkil')
                    ->disk('public')
                    ->circular()
                    ->size(50),
                TextColumn::make('category.name')
                    ->label('Kateqoriya')
                    ->limit(15)
                    ->sortable(),
                TextColumn::make('brand.name')
                    ->label('Brend')
                    ->limit(15)
                    ->sortable(),



                /*                 TextColumn::make('barcode')
                    ->label('Barkod')
                    ->searchable(), */
                TextColumn::make('cost_price')
                    ->label('Alış Qiyməti')
                    ->sortable(),
                TextColumn::make('sale_price')
                    ->label('Satış Qiyməti')
                    ->color('success')
                    ->sortable(),

                TextColumn::make('inventory.quantity')
                    ->label('Stok Miqdarı')
                    ->badge()
                    ->color(fn($state) => match (true) {

                        $state == 0 => 'danger',

                        $state <= 5 => 'warning',

                        default => 'success',
                    })
                    ->numeric()
                    ->sortable(),
                /*                 TextColumn::make('supplier.name')
                    ->label('Təchizatçı')
                    ->limit(15)
                    ->sortable(), */
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('Yaradılma Tarixi')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Yenilənmə Tarixi')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->label('Silinmə Tarixi')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->defaultSort('created_at', 'desc')
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
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
