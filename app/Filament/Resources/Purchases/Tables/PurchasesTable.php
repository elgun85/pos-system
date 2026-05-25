<?php

namespace App\Filament\Resources\Purchases\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\DeleteAction;

class PurchasesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')
                    ->label('Qaimənin Şəkli')
                    ->disk('public')
                    ->circular()
                    ->size(50)
                    ->url(fn($record) => $record->photo ? asset('storage/' . $record->photo) : null)
                    ->openUrlInNewTab(),
                TextColumn::make('invoice_number')
                    ->label('Qaimə Nömrəsi')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('supplier.name')
                    ->label('Təchizatçı')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('total_price')
                    ->label('Ümumi Məbləğ')
                    ->money()
                    ->sortable(),
                IconColumn::make('status')
                    ->label('Status')
                    ->boolean(),
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
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
