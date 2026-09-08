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
                    ->label('Məhsul')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('cost_price')
                    ->label('Maya deyeri')
                    ->money('AZN'),

                TextColumn::make('quantity')
                    ->label('Say')
                    ->numeric(),

                TextColumn::make('total_cost')
                    ->label('Ümumi zərər')
                    ->color('danger')
                    ->money('AZN'),

                TextColumn::make('notes')
                    ->label('Qeyd')
                    ->limit(20),

                TextColumn::make('user.name')
                    ->label('İstifadəçi'),


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
