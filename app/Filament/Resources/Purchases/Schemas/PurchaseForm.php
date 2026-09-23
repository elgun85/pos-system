<?php

namespace App\Filament\Resources\Purchases\Schemas;

use App\Models\Product;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class PurchaseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 1. HİSSƏ: Sənədin Əsas Məlumatları (Qaimə nömrəsi, Təchizatçı və s.)
                Section::make(__('resource.purchase.details'))
                    ->description(__('resource.purchase.det.description'))
                    ->collapsible()
                    ->schema([

                        Grid::make(2)
                            ->schema([
                                TextInput::make('invoice_number')
                                    ->label(__('resource.purchase.number'))
                                    ->placeholder('Məs: Q-1024')
                                    ->maxLength(255),

                                Select::make('supplier_id')
                                    ->label(__('resource.purchase.supp_name'))
                                    ->relationship('supplier', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->label(__('resource.supplier.name'))
                                            ->required(),

                                        TextInput::make('phone')
                                            ->label(__('resource.supplier.phone'))
                                            ->placeholder('+994 XX XXX XX XX')
                                            ->tel(),

                                        Toggle::make('status')
                                            ->label('Status')
                                            ->default(true)
                                    ]),

                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('total_price')
                                    ->label(__('resource.purchase.total_price'))
                                    ->placeholder(__('resource.purchase.total_price.placeholder'))
                                    ->rule('max:99999999')
                                    ->live()
                                    ->numeric()
                                    ->prefix('₼')
                                    ->disabled()
                                    ->dehydrated(),

                                Toggle::make('status')
                                    ->label(__('resource.purchase.status'))
                                    ->default(true)
                                    ->helperText(__('resource.purchase.status.placeholder')),

                            ]),


                        Grid::make(1)
                            ->schema([
                                FileUpload::make('photo')
                                    ->label(__('resource.purchase.photo'))
                                    ->image()
                                    ->disk('public')
                                    ->directory('purchase')
                                    ->imageEditor()
                                    ->maxSize(4096)
                                    ->reorderable()
                                    ->openable()
                                    ->deletable(true)
                                    ->placeholder(__('resource.purchase.photo.placeholder'))
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp']),
                            ]),
                    ]),
                // 2. HİSSƏ: O qəbz altındakı 100-lərlə çeşid malı əlavə etmək üçün "+" Düyməli sahə
                Section::make(__('resource.purchase.main'))
                    ->description(__('resource.purchase.main_det'))
                    ->schema([
                        Repeater::make('purchaseItems')
                            ->relationship()
                            ->schema([
                                Grid::make(1)->schema([
                                    Select::make('product_id')
                                        ->label(__('resource.product.name'))
                                        ->relationship('product', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->options(function (Get $get) {
                                            $products = Product::pluck('name', 'id');
                                            $repeaterItems = $get('../../../purchaseItems') ?? $get('purchaseItems') ?? [];
                                            $selectedIds = collect($repeaterItems)->pluck('product_id')->filter()->toArray();
                                            // Seçilmiş məhsulları siyahıdan çıxarırıq
                                            return $products->forget($selectedIds)->toArray();
                                        })
                                        ->createOptionForm([

                                            TextInput::make('sku')
                                                ->label(__('resource.product.sku'))
                                                ->unique(ignoreRecord: true) // Bazada təkrarlanmasın
                                                ->autofocus()
                                                ->required()
                                                ->suffixAction(
                                                    Action::make('generateBarcode')
                                                        ->icon('heroicon-m-arrow-path')
                                                        ->tooltip('Unikal barkod generasiya et')
                                                        ->action(function (Set $set) {
                                                            do {
                                                                $randomBarcode = '200' . rand(1000000002, 9999999999);
                                                            } while (Product::where('sku', $randomBarcode)->exists()); // Bazada varsa, yenidən yoxla
                                                            $set('sku', $randomBarcode);
                                                        })
                                                ),

                                            TextInput::make('name')
                                                ->label(__('resource.product.name'))
                                                ->maxLength(50)
                                                ->required(),

                                            Select::make('category_id')
                                                ->label(__('resource.category.name'))
                                                ->relationship('category', 'name')
                                                ->preload()
                                                ->native(false)
                                                ->searchable()
                                                ->required()
                                                ->createOptionForm([
                                                    TextInput::make('name')
                                                        ->label(__('resource.category.name'))
                                                        ->required()
                                                        ->unique('categories', 'name'),

                                                    Select::make('parent_id')
                                                        ->label(__('resource.category.parent.name'))
                                                        ->relationship('parent', 'name')
                                                        ->nullable()
                                                        ->preload()
                                                        ->searchable(),

                                                ]),

                                            Select::make('brand_id')
                                                ->label(__('resource.brand.name'))
                                                ->relationship('brand', 'name')
                                                ->preload()
                                                ->native(false)
                                                ->searchable()
                                                ->createOptionForm([
                                                    TextInput::make('name')
                                                        ->label(__('resource.brand.name'))
                                                        ->required()
                                                        ->unique('brands', 'name'),

                                                    Toggle::make('status')
                                                        ->label('Status')
                                                        ->default(true)
                                                ]),
                                        ]),
                                ]),
                                Grid::make(2)->schema([

                                    TextInput::make('quantity')
                                       ->label(__('resource.purchase.quantity'))
                                        ->numeric()
                                        ->default(1)
                                        ->required()
                                        ->live() // Qiymət hesablamaq üçün canlı oxusun
                                        ->placeholder('0.00'),

                                    TextInput::make('cost_price')
                                        ->label(__('resource.purchase.cost_price'))
                                        ->numeric()
                                        ->prefix('₼')
                                        ->required()
                                        ->live()
                                        ->placeholder('0.00'),
                                ]),

                            ])
                            ->label(__('resource.purchase.main_head'))
                            ->addActionLabel(__('resource.purchase.main_plus')) // Düymənin üstündəki yazı
                            ->reorderable(false) // Sürəti artırmaq üçün sıralama funksiyasını bağlayırıq
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $items = $get('purchaseItems') ?? [];
                                $total = 0;
                                foreach ($items as $item) {
                                    $total += (float)($item['quantity'] ?? 0) * (float)($item['cost_price'] ?? 0);
                                }
                                $set('total_price', round($total, 2));
                            }),

                    ]),

            ]);
    }
}
