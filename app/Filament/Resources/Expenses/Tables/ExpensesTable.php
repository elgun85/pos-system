<?php

namespace App\Filament\Resources\Expenses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class ExpensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('expenseCategory.name')
                    ->label('Kateqoriya')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('Məbləğ')
                    ->money('AZN')
                    ->sortable(),

                TextColumn::make('paymentMethod.name')
                    ->label('Ödəniş'),

                TextColumn::make('notes')
                    ->label('Qeyd')
                    ->limit(50),

                TextColumn::make('created_at')
                    ->label('Tarix')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('İstifadəçi'),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
