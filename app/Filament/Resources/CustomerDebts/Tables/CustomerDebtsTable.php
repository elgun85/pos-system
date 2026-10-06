<?php

namespace App\Filament\Resources\CustomerDebts\Tables;

use App\Enums\PaymentMethodCode;
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
                    ->label(__('resource.customer_deb.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('total_debt')
                    ->label(__('resource.customer_deb.total'))
                    ->money(__('resource.money_icon'), true)
                    ->badge()
                    ->color('danger')
                    ->sortable(),

                TextColumn::make('phone')
                    ->label(__('resource.customer_deb.phone'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('address')
                    ->label(__('resource.customer_deb.address'))
                    ->searchable()
                    ->sortable(),

            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('quick_payment')
                    ->label(__('resource.customer_deb.pay_head'))
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->form([
                        TextInput::make('amount')
                            ->label(__('resource.customer_deb.payment'))
                            ->numeric()
                            ->step(0.01)
                            ->required()
                            ->default(fn($record) => round($record->total_debt, 2))
                            ->minValue(0.01)
                            ->maxValue(fn($record) => round($record->total_debt, 2)),

                        Select::make('payment_method_id')
                            ->label(__('resource.customer_deb.pay_met'))
                            ->options(
                                PaymentMethod::where('status', true)
                                    ->pluck('name', 'id')
                            )
                            ->default(fn() => PaymentMethod::where('status', true)->where('code', PaymentMethodCode::CASH)->first()?->id
                                ?? PaymentMethod::where('status', true)->first()?->id)
                            ->required(),

                        Textarea::make('notes')
                            ->label(__('resource.customer_deb.note')),
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
                            ->title(__('resource.customer_deb.note_det'))
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
