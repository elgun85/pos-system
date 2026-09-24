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
                    ->label(__('resource.customer_deb.name'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($state, $record) {
                        return $record->customer
                            ? "{$record->customer->name} ({$record->customer->phone})  {$record->customer->address}"
                            : 'Müştəri silinib';
                    }),
                TextColumn::make('paymentMethod.name')
                    ->label(__('resource.customer_deb.pay_met'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('sale.sale_number')
                    ->label(__('resource.customer_deb.sale_num'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label(__('resource.customer_deb.users'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label(__('resource.customer_deb.deb_cat'))
                    ->badge(),

                TextColumn::make('amount')
                    ->label(__('resource.customer_deb.total'))
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('resource.customer.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
             //   ViewAction::make()->iconButton(),
            //    EditAction::make()->iconButton(),
            ])
            ->recordUrl(null)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
