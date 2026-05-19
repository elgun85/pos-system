<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ImageEntry::make('image')
                    ->label('Şəkil')
                    ->disk('public')
                    ->circular()
                    ->size(130),




                TextEntry::make('name')
                    ->label('Məhsul Adı')
                    ->weight(FontWeight::Bold)
                    ->size('md'),


                TextEntry::make('category.name')
                    ->label('Kateqoriya')
                    ->weight(FontWeight::Bold)
                    ->size('md'),

                    TextEntry::make('brand.name')
                    ->label('Brend')
                    ->weight(FontWeight::Bold)
                    ->size('md'),

                TextEntry::make('sku')
                    ->label('Barkod')
                    ->weight(FontWeight::Bold)
                    ->size('md'),
                /*                 TextEntry::make('barcode')
                    ->label('Barkod')
                    ->weight(FontWeight::Bold)
                    ->size('md'), */
                TextEntry::make('cost_price')
                    ->label('Alış Qiyməti')
                    ->weight(FontWeight::Bold)
                    ->size('md')
                    ->money(),
                TextEntry::make('sale_price')
                    ->label('Satış Qiyməti')
                    ->weight(FontWeight::Bold)
                    ->size('md')
                    ->money(),
                TextEntry::make('supplier.name')
                    ->label('Təchizatçı')
                    ->weight(FontWeight::Bold)
                    ->size('md'),
                TextEntry::make('status')
                    ->label('Status')
                    ->badge(),
                /*                 TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn(Product $record): bool => $record->trashed()), */
            ]);
    }
}
