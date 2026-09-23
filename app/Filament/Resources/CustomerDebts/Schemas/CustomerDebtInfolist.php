<?php

namespace App\Filament\Resources\CustomerDebts\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CustomerDebtInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Sol Tərəf: Müştəri və Borc Xülasəsi
                Section::make('Müştəri Məlumatları')
                    ->columnSpanFull(1)
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('resource.customer_deb.name'))
                            ->weight('bold'),

                        TextEntry::make('phone')
                            ->label(__('resource.customer_deb.phone'))
                            ->weight('bold'),

                        TextEntry::make('address')
                            ->label(__('resource.customer_deb.address'))
                            ->weight('bold'),

                        TextEntry::make('total_debt')
                            ->label(__('resource.customer_deb.total'))
                            ->color(fn($state) => $state > 0 ? 'danger' : 'success')
                            ->size('lg')
                            ->money(__('resource.money_icon'))
                            ->weight('bold'),
                    ])->columns(3),

                // Sağ Tərəf: CƏDVƏL FORMALIDIR (Transactions)
                Section::make(__('resource.customer_deb.deb_data'))
                    ->columnSpan(2)
                    ->schema([
                        RepeatableEntry::make('transactions') // Bütün hərəkətləri göstərir
                            ->hiddenLabel()
                            ->grid(1) // Sətir-sətir cədvəl effekti verir
                            ->schema([
                                Grid::make(5)->schema([
                                    // 1. Tarix
                                    TextEntry::make('created_at')
                                        ->label(__('resource.customer_deb.created_at'))
                                        ->dateTime('d.m.Y H:i'),

                                    // 2. Növ (Debitor / Ödəniş)
                                    TextEntry::make('type')
                                        ->label(__('resource.customer_deb.deb_cat'))
                                        ->badge()
                                        ->formatStateUsing(fn($state) => match ($state) {
                                            'debt' => 'Borc (Nisyə)',
                                            'payment' => 'Ödəniş',
                                            default => $state,
                                        })
                                        ->color(fn($state) => match ($state) {
                                            'debt' => 'danger',
                                            'payment' => 'success',
                                            default => 'gray',
                                        }),

                                    // 3. Məbləğ (Borc qırmızı, ödəniş yaşıl)
                                    TextEntry::make('amount')
                                        ->label(__('resource.customer_deb.total'))
                                        ->money(__('resource.money_icon'))
                                        ->weight('bold')
                                        ->color(fn($record) => $record->type === 'debt' ? 'danger' : 'success'),

                                    // 4. Ödəniş Üsulu
                                    TextEntry::make('paymentMethod.name')
                                        ->label(__('resource.customer_deb.pay_met'))
                                        ->default('-'),

                                    // 5. Satış ID
                                    TextEntry::make('sale.sale_number')
                                        ->label(__('resource.customer_deb.sale_num'))
                                        ->default('-'),
                                ]),
                            ]),
                    ]),
            ]);
    }
}
