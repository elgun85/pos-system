<?php

namespace App\Filament\Resources\Sales\Tables;

use App\Models\Sale;
use App\Models\SalesItem;
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
                    ->label(__('resource.sale.number'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('items')
                    ->label(__('resource.sale.product'))
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
                            $text .= " ... (+{$remaining}  ". __('resource.product') . ')';
                        }
                        return $text;
                    })
                    ->wrap(),

                TextColumn::make('payments.paymentMethod.name')
                    ->label(__('resource.sale.payment'))
                    ->sortable(),


                TextColumn::make('total')
                    ->label(__('resource.sale.total'))
                    ->money(__('resource.money_icon'))
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'draft'              =>    __('resource.sale.draft'),
                        'completed'          =>    __('resource.sale.completed'),
                        'cancelled'          =>    __('resource.sale.cancelled'),
                        'partial'            =>    __('resource.sale.partial'),
                        'unpaid'             =>    __('resource.sale.unpaid'),
                        'returned'           =>    __('resource.sale.returned'),
                        'partially_returned' =>    __('resource.sale.partially_returned'),
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
                    ->label(__('resource.sale.user'))
                    ->getStateUsing(fn($record) => $record->user?->name)
                    ->sortable(),


                TextColumn::make('created_at')
                    ->label(__('resource.sale.created_at'))
                    //  ->dateTime()
                    ->searchable()
                    ->sortable(),

            ])
            ->filters([])
            ->recordActions([
                Action::make('return')
                    ->icon(Heroicon::ArrowUturnLeft)
                    ->modalHeading(__('resource.sale.modal_head'))
                    ->modalSubmitActionLabel(__('resource.sale.modal_label'))
                    ->form(
                        [
                            Repeater::make('items')
                                ->label(__('resource.sale.modal_header'))
                                ->schema([
                                    Select::make('product_id')
                                        ->label(__('resource.sale.product'))
                                        ->options(function (Sale $record) {
                                            return $record->items()
                                                ->with('product')
                                                ->get()
                                                ->mapWithKeys(function ($item) {
                                                    return [
                                                        $item->product->id => $item->product->name . '(' . number_format($item->price, 2) . '₼ )'
                                                    ];
                                                })
                                            ;
                                        })
                                        ->required()
                                        ->reactive()
                                        ->columnSpan(3)

                                        ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                                    TextInput::make('quantity')
                                        ->label(__('resource.sale.quantity'))
                                        ->numeric()
                                        ->default(1)
                                        ->minValue(1)
                                        ->required(),

                                    TextInput::make('refund_amount')
                                        ->label(__('resource.sale.price'))
                                        ->required()
                                        ->prefix('₼')

                                ])
                                ->columns(3)
                                ->minItems(1)
                                ->defaultItems(1),

                            TextInput::make('reason')
                                ->label(__('resource.sale.reason'))
                                ->placeholder(__('resource.sale.reason_note'))
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
                                ->title(__('resource.sale.title'))
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title(__('resource.sale.title_error'))
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
