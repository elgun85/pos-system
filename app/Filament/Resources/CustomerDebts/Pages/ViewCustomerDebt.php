<?php

namespace App\Filament\Resources\CustomerDebts\Pages;

use App\Enums\PaymentMethodCode;
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
                ->label(__('resource.pay'))
                ->icon('heroicon-o-banknotes')
                ->color('success')
                ->form([
                    TextInput::make('amount')
                        ->label(__('resource.paid_amount'))
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
                        ->default(fn() => PaymentMethod::where('status', true)->where('code', PaymentMethodCode::CASH)->first()?->id
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
                            'notes' => $data['notes'] ?? __('resource.generalDebtPayment'),
                        ]);
                    });
                    Notification::make('')
                        ->title(__('resource.paySuccess'))
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
