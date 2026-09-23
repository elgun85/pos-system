<?php

namespace App\Filament\Resources\DamagedProducts\Tables;

use App\Services\DamageService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DamagedProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label(__('resource.product.name'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('cost_price')
                    ->label(__('resource.product.cost_price'))
                    ->money(__('resource.money_icon')),

                TextColumn::make('quantity')
                    ->label(__('resource.purchase.quantity'))
                    ->numeric(),

                TextColumn::make('total_cost')
                    ->label(__('resource.totalDamage'))
                    ->color('danger')
                    ->money(__('resource.money_icon')),

                TextColumn::make('notes')
                    ->label(__('resource.customer_deb.note'))
                    ->limit(20),

                TextColumn::make('user.name')
                    ->label(__('resource.customer_deb.users')),

                TextColumn::make('created_at')
                    ->sortable()
                    ->label(__('resource.product.created_at'))
                    ->date('j M Y')
                //->since()




            ])
            ->filters([
                //
            ])
            ->recordActions([
                DeleteAction::make()
                    ->action(function ($record) {
                        app(DamageService::class)->deleteDamage($record);
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
