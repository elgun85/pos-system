<?php

namespace App\Filament\Resources\PaymentMethods\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentMethodsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('resource.payment.name'))
                    ->sortable()
                    ->searchable(),

                ImageColumn::make('icon')
                    ->label(__('resource.payment.icon'))
                    ->disk('public')
                    ->square()
                    ->size(50),

                TextColumn::make('description')
                   ->label(__('resource.payment.description'))
                    ->limit(20)
                    ->searchable(),

                IconColumn::make('status')
                    ->boolean(),
                    
                TextColumn::make('created_at')
                ->label(__('resource.payment.created_at'))
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
