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
                            ->label('Ad, Soyad')
                            ->weight('bold'),
                        TextEntry::make('phone')
                            ->label('Telefon')
                            ->weight('bold'),
                        TextEntry::make('address')
                            ->label('Ünvan')
                            ->weight('bold'),

                        TextEntry::make('total_debt')
                            ->label('Cari Borc Qalığı')
                            ->color(fn ($state) => $state > 0 ? 'danger' : 'success')
                            ->size('lg')
                            ->money('AZN')
                            ->weight('bold'),
                    ])->columns(3),

                // Sağ Tərəf: CƏDVƏL FORMALIDIR (Transactions)
                Section::make('Əməliyyat Tarixçəsi (Borclar və Ödənişlər)')
                    ->columnSpan(2)
                    ->schema([
                        RepeatableEntry::make('transactions') // Bütün hərəkətləri göstərir
                            ->hiddenLabel()
                            ->grid(1) // Sətir-sətir cədvəl effekti verir
                            ->schema([
                                Grid::make(5)->schema([
                                    // 1. Tarix
                                    TextEntry::make('created_at')
                                        ->label('Tarix')
                                        ->dateTime('d.m.Y H:i'),

                                    // 2. Növ (Debitor / Ödəniş)
                                    TextEntry::make('type')
                                        ->label('Əməliyyat Növü')
                                        ->badge()
                                        ->formatStateUsing(fn ($state) => match ($state) {
                                            'debt' => 'Borc (Nisyə)',
                                            'payment' => 'Ödəniş',
                                            default => $state,
                                        })
                                        ->color(fn ($state) => match ($state) {
                                            'debt' => 'danger',
                                            'payment' => 'success',
                                            default => 'gray',
                                        }),

                                    // 3. Məbləğ (Borc qırmızı, ödəniş yaşıl)
                                    TextEntry::make('amount')
                                        ->label('Məbləğ')
                                        ->money('AZN')
                                        ->weight('bold')
                                        ->color(fn ($record) => $record->type === 'debt' ? 'danger' : 'success'),

                                    // 4. Ödəniş Üsulu
                                    TextEntry::make('paymentMethod.name')
                                        ->label('Ödəniş Üsulu')
                                        ->default('-'),

                                    // 5. Satış ID
                                    TextEntry::make('sale.sale_number')
                                        ->label('Satış №')
                                        ->default('-'),
                                ]),
                            ]),
                    ]),
            ]);
    }
}