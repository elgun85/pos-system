<?php

namespace App\Filament\Resources\CustomerDebts\Pages;

use App\Filament\Resources\CustomerDebts\CustomerDebtResource;
use App\Models\CustomerTransaction;
use App\Models\PaymentMethod;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\DB;

class ViewCustomerDebt extends ViewRecord
{
    protected static string $resource = CustomerDebtResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            Action::make('payment')
                ->label('Ödəniş et')
                ->icon('heroicon-o-banknotes')
                ->color('success')
                ->form([
                    TextInput::make('amount')
                        ->label('Ödənilən məbləğ')
                        ->numeric()
                        ->step(0.01)
                        ->required()
                        ->default(fn() => round($this->record->total_debt, 2))
                        ->minValue(0.01)
                        ->maxValue(fn() => round($this->record->total_debt, 2)),

                    Select::make('payment_method_id')
                        ->options(
                            PaymentMethod::where('status', true)
                                ->pluck('name', 'id')
                        )
                        // 👈 Adı "Nağd" olanı tapır, tapmasa ilk aktiv olanın ID-sini seçir
                        ->default(fn() => PaymentMethod::where('status', true)->where('name', 'like', '%Nəğd%')->first()?->id
                            ?? PaymentMethod::where('status', true)->first()?->id)
                        ->searchable()
                        ->required(),

                    Textarea::make('notes')
                        ->label('Qeyd'),
                ])
                ->action(function (array $data) {

                    DB::transaction(function () use ($data) {

                        CustomerTransaction::create([
                            'customer_id'       => $this->record->id,
                            'payment_method_id' => $data['payment_method_id'],
                            'user_id'           => auth()->id(),
                            'type'              => 'payment',
                            'amount'            => $data['amount'],
                            'notes'             => $data['notes'] ?? 'Ümumi borc ödənişi',
                        ]);
                    });
                    Notification::make('')
                        ->title('Ödəniş uğurla qəbul edildi.')
                        ->success()
                        ->send();
                    $this->record->refresh();

                    // Əgər borc tam ödənilibsə (0 olubsa), müştərini siyahıdan silsin deyə indeksi yönləndiririk
                    if ($this->record->total_debt <= 0) {
                        $this->redirect(CustomerDebtResource::getUrl('index'));
                    }
                }),
        ];
    }
}
