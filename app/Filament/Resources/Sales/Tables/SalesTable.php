<?php

namespace App\Filament\Resources\Sales\Tables;

use App\Models\Sale;
use App\Services\ReturnService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
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
                TextColumn::make('payments.paymentMethod.name')
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
                        'partial' => 'Qismən ödənilib',
                        'unpaid' => 'Ödənilməyib',
                        'returned'           => 'Tam qaytarılıb',
                        'partially_returned' => 'Qismən qaytarılıb'
                    })
                    ->color(fn($state) => match ($state) {
                        'draft' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        'partial' => 'info',
                        'unpaid' => 'primary',
                        'partially_returned' => 'gray',
                        'returned'           => 'danger',
                    }),

                TextColumn::make('user.name')
                    ->label('Satışı edən')
                    ->getStateUsing(fn($record) => $record->user?->name)
                    ->sortable(),


                TextColumn::make('created_at')
                    ->label('Yaradılma tarixi')
                    //  ->dateTime()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Yenilənmə tarixi')
                    ->dateTime()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                Action::make('return')
                    ->icon(Heroicon::ArrowUturnLeft)
                    ->modalHeading('Satışdan Məhsul Qaytarılması')
                    ->modalSubmitActionLabel('Qaytarmanı Təsdiqlə')
                    ->form(
                        [
                            Repeater::make('items')
                                ->label('Qaytarılacaq Məhsullar')
                                ->schema([
                                    Select::make('product_id')
                                        ->label('Məhsul')
                                        ->options(function (Sale $record) {
                                            return $record->items()
                                                ->with('product')
                                                ->get()
                                                ->pluck('product.name', 'product.id');
                                        })
                                        ->required()
                                        ->reactive()
                                        ->columnSpan(3)

                                        ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                                    TextInput::make('quantity')
                                        ->label('Say')
                                        ->numeric()
                                        ->default(1)
                                        ->minValue(1)
                                        ->required(),
                                    TextInput::make('refund_amount')
                                        ->label('Məbləğ (AZN)')
                                        ->required()
                                        ->prefix('₼')

                                ])
                                ->columns(3)
                                ->minItems(1)
                                ->defaultItems(1),

                            TextInput::make('reason')
                                ->label('Qaytarılma Səbəbi')
                                ->placeholder('Məs: Defektli məhsul, razı qalmadı və s.')
                                ->maxLength(255),


                        ]
                    )
                    ->action(function (Sale $record, array $data, ReturnService $returnService) {
                        try {
                            $returnService->processReturn(
                                sale: $record,
                                items: $data['items'],
                                reason: $data['reason'] ?? null
                            );
                            Notification::make()
                                ->title('Məhsul qaytarılması uğurla icra olundu')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Xəta baş verdi')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->hidden(fn(Sale $record) => $record->status === 'cancelled'),

                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
