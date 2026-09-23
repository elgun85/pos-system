<?php

namespace App\Filament\Resources\Sales\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SaleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('sale_number')
                    ->label(__('resource.sale.number')),
                Select::make('payment_method_id')
                    ->label(__('resource.sale.payment'))
                    ->relationship('paymentMethod', 'name'),


                TextInput::make('total')
                    ->label(__('resource.sale.total'))
                    ->numeric()
                    ->suffix('₼')
                    ->readOnly()
                    ->disabled(),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'draft'              =>    __('resource.sale.draft'),
                        'completed'          =>    __('resource.sale.completed'),
                        'cancelled'          =>    __('resource.sale.cancelled'),
                        'partial'            =>    __('resource.sale.partial'),
                        'unpaid'             =>    __('resource.sale.unpaid'),
                        'returned'           =>    __('resource.sale.returned'),
                        'partially_returned' =>    __('resource.sale.partially_returned'),

                    ]),

            ]);
    }
}
