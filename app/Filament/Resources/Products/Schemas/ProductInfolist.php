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
                    ->label(__('resource.product.image'))
                    ->disk('public')
                    ->circular()
                    ->size(130),




                TextEntry::make('name')
                    ->label(__('resource.product.name'))
                    ->weight(FontWeight::Bold)
                    ->size('md'),


                TextEntry::make('category.name')
                    ->label(__('resource.product.category.name'))
                    ->weight(FontWeight::Bold)
                    ->size('md'),

                TextEntry::make('brand.name')
                    ->label(__('resource.product.brand.name'))
                    ->weight(FontWeight::Bold)
                    ->size('md'),

                TextEntry::make('sku')
                    ->label(__('resource.product.sku'))
                    ->weight(FontWeight::Bold)
                    ->size('md'),

                TextEntry::make('cost_price')
                    ->label(__('resource.product.cost_price'))
                    ->weight(FontWeight::Bold)
                    ->size('md')
                    ->money(),
                TextEntry::make('sale_price')
                    ->label(__('resource.product.sale_price'))
                    ->weight(FontWeight::Bold)
                    ->size('md')
                    ->money(),

                TextEntry::make('inventory.quantity')
                    ->label(__('resource.product.inventory.quantity'))
                    ->weight(FontWeight::Bold)
                    ->size('md')
                    ->numeric(),
                TextEntry::make('supplier.name')
                    ->label(__('resource.product.supplier'))
                    ->weight(FontWeight::Bold)
                    ->size('md'),
                TextEntry::make('status')
                    ->label('Status')
                    ->badge(),
                TextEntry::make('is_favorite')
                    ->label(__('resource.product.favorite'))
                    ->badge()
                    ->formatStateUsing(fn($state) => $state ? 'Favori' : 'Adi')
                    ->color(fn($state) => $state ? 'warning' : 'gray'),

            ]);
    }
}
