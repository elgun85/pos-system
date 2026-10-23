<?php

namespace App\Filament\Resources\Purchases\Tables;

use App\Models\Purchase;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class PurchasesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')
                    ->label(__('resource.purchase.photo'))
                    ->disk('public')
                    ->circular()
                    ->size(50)
                    ->url(fn($record) => $record->photo ? asset('storage/' . $record->photo) : null)
                    ->openUrlInNewTab(),

                TextColumn::make('invoice_number')
                    ->label(__('resource.purchase.number'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('supplier.name')
                    ->label(__('resource.purchase.supp_name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('total_price')
                    ->label(__('resource.purchase.total_price'))
                    ->prefix(__('resource.prefix'))
                    ->sortable(),

                IconColumn::make('status')
                    ->label(__('resource.status'))
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label(__('resource.purchase.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('resource.purchase.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()->iconButton(),
                DeleteAction::make()
                    ->iconButton()
                    ->before(function (DeleteAction $action, Purchase $record) {
                        if ($message = $record->stockBlockMessage()) {
                            Notification::make()
                                ->danger()
                                ->title(__('resource.purchase.cannot_delete'))
                                ->body($message)
                                ->persistent()
                                ->send();

                            $action->cancel();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function (DeleteBulkAction $action, Collection $records) {
                            foreach ($records as $record) {
                                if ($message = $record->stockBlockMessage()) {
                                    Notification::make()
                                        ->danger()
                                        ->title(__('resource.purchase.cannot_delete'))
                                        ->body(($record->invoice_number ? '#' . $record->invoice_number . ' — ' : '') . $message)
                                        ->persistent()
                                        ->send();
                                    $action->cancel();
                                    return;
                                }
                            }
                        }),
                ]),
            ]);
    }
}
