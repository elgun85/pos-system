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
                    ->label(__('resource.category.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('amount')
                    ->label(__('resource.sale.price'))
                    ->money(__('resource.money_icon'))
                    ->sortable(),

                TextColumn::make('paymentMethod.name')
                    ->label(__('resource.payment_method')),

                TextColumn::make('notes')
                    ->label(__('resource.customer_deb.note'))
                    ->limit(50),

                TextColumn::make('created_at')
                    ->label(__('resource.product.created_at'))
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label(__('resource.customer_deb.users')),

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
