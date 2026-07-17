<?php

namespace App\Filament\Resources\CustomerTransactions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CustomerTransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer.name')
                    ->label('Müştəri')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($state, $record) {
                        return $record->customer
                            ? "{$record->customer->name} ({$record->customer->phone})  {$record->customer->address}"
                            : 'Müştəri silinib';
                    }),
                TextColumn::make('paymentMethod.name')
                    ->label('Ödəmə üsulu')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sale.sale_number')
                    ->label('Satış ID')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('İstifadəçi')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Növ')
                    ->badge(),

                TextColumn::make('amount')
                    ->label('Məbləğ')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
