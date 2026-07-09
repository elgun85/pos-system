<?php

namespace App\Filament\Resources\Sales\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SalesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('sale_number')
                    ->label('Satış nömrəsi')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('items')
                    ->label('Məhsullar')
                    ->state(function ($record) {
                        $items = $record->items;
                        $shown = $items->take(4);
                        $text = $shown
                            ->map(
                                fn($item) =>
                                $item->product?->name . ' (' . $item->quantity . ')'
                            )
                            ->implode(', ');

                        $remaining = $items->count() - $shown->count();

                        if ($remaining > 0) {
                            $text .= " ... (+{$remaining} məhsul)";
                        }

                        return $text;
                    })
                    ->wrap(),


                TextColumn::make('paymentMethod.name')
                    ->label('Ödəmə üsulu')
                    ->sortable(),
                TextColumn::make('total')
                    ->label('Cəmi')
                    ->money('azn')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'draft' => 'Gözləmədə',
                        'completed' => 'Tamamlanıb',
                        'cancelled' => 'Ləğv edilib',
                    })
                    ->color(fn($state) => match ($state) {
                        'draft' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                    }),

                    TextColumn::make('user.name')
                    ->label('Satışı edən')
                    ->getStateUsing(fn($record) => $record->user?->name)
                    ->sortable(),


                TextColumn::make('created_at')
                    ->label('Yaradılma tarixi')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Yenilənmə tarixi')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
