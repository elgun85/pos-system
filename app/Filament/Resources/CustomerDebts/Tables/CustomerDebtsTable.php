<?php

namespace App\Filament\Resources\CustomerDebts\Tables;

use App\Models\CustomerTransaction;
use App\Models\PaymentMethod;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class CustomerDebtsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Müştəri')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('total_debt')
                    ->label('Borc')
                    ->money('AZN', true)
                    ->badge()
                    ->color('danger')
                    ->sortable(),

                TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('address')
                    ->label('Ünvan')
                    ->searchable()
                    ->sortable(),

            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('quick_payment')
                    ->label('Ödəniş')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->form([
                        TextInput::make('amount')
                            ->label('Ödənilən məbləğ')
                            ->numeric()
                            ->step(0.01)
                            ->required()
                            ->default(fn($record) => round($record->total_debt, 2))
                            ->minValue(0.01)
                            ->maxValue(fn($record) => round($record->total_debt, 2)),

                        Select::make('payment_method_id')
                            ->label('Ödəniş üsulu')
                            ->options(
                                PaymentMethod::where('status', true)
                                    ->pluck('name', 'id')
                            )
                            // 👈 Adı "Nağd" olanı tapır, tapmasa ilk aktiv olanın ID-sini seçir
                            ->default(fn() => PaymentMethod::where('status', true)->where('name', 'like', '%Nağd%')->first()?->id
                                ?? PaymentMethod::where('status', true)->first()?->id)
                            ->required(),

                        Textarea::make('notes')
                            ->label('Qeyd'),
                    ])
                    ->action(function ($record, array $data) {
                        DB::transaction(function () use ($record, $data) {
                            CustomerTransaction::create([
                                'customer_id'       => $record->id,
                                'payment_method_id' => $data['payment_method_id'],
                                'user_id'           => auth()->id(),
                                'type'              => 'payment',
                                'amount'            => $data['amount'],
                                'notes'             => $data['notes'] ?? 'Ümumi borc ödənişi',
                            ]);
                        });

                        Notification::make()
                            ->title('Ödəniş qəbul edildi.')
                            ->success()
                            ->send();

                        $record->refresh();
                    }),
                ViewAction::make(),
                // EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
