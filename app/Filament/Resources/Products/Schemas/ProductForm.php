<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Product;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;


class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Məhsul Məlumatları')
                    ->schema([
                        TextInput::make('sku')
                            ->label('Barkod')
                            ->unique(ignoreRecord: true) // Bazada təkrarlanmasın
                            ->autofocus()
                            ->required()
                            ->suffixAction(
                                Action::make('generateBarcode')
                                    ->icon('heroicon-m-arrow-path')
                                    ->tooltip('Unikal barkod generasiya et')
                                    ->action(function (Set $set) {
                                        do {
                                            // Məsələn: 200 ilə başlayan 13 rəqəmli daxili barkod formatı
                                            $randomBarcode = '200' . rand(1000000002, 9999999999);
                                        } while (Product::where('sku', $randomBarcode)->exists()); // Bazada varsa, yenidən yoxla

                                        // Tapılan unikal barkodu input xanasına yazdırırıq
                                        $set('sku', $randomBarcode);
                                    })
                            ),
                        TextInput::make('name')
                            ->label('Məhsul Adı')
                            ->placeholder('Məhsul adını daxil edin')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('name', mb_convert_case($state, MB_CASE_TITLE, 'UTF-8'));
                            })
                            ->maxLength(255)
                            ->required(),
                        /*                         TextInput::make('cost_price')
                            ->label('Alış Qiyməti')
                            ->required()
                            ->numeric()
                            ->prefix('₼'), */
                        TextInput::make('sale_price')
                            ->label('Satış Qiyməti')
                            ->required()
                            ->numeric()
                            ->prefix('₼'),
                        FileUpload::make('image')
                            ->label('Məhsul Şəkli')
                            ->disk('public')
                            ->directory('products')
                            ->maxSize(4096)
                            ->imageEditor()
                            ->reorderable()
                            ->openable()
                            ->deletable(true)
                            ->placeholder('Şəkil seçilməyib')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp']),
                    ]),




                Section::make('Əsas Məlumatlar')
                    ->schema([
                        Select::make('category_id')
                            ->label('Kateqoriya')
                            ->relationship('category', 'name')
                            ->preload()
                            ->native(false)
                            ->searchable()
                            ->required()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Kateqoriya Adı')
                                    ->required()
                                    ->unique('categories', 'name'),

                                Select::make('parent_id')
                                    ->label('Üst Kateqoriya')
                                    ->relationship('parent', 'name')
                                    ->nullable()
                                    ->preload()
                                    ->searchable(),

                                Toggle::make('status')
                                    ->label('Status')
                                    ->default(true)
                            ]),
                        Select::make('brand_id')
                            ->label('Brend')
                            ->relationship('brand', 'name')
                            ->preload()
                            ->native(false)
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Brend Adı')
                                    ->required()
                                    ->unique('brands', 'name'),

                                Toggle::make('status')
                                    ->label('Status')
                                    ->default(true)
                            ]),
                        /*                         Select::make('supplier_id')
                            ->label('Təchizatçı')
                            ->relationship('supplier', 'name')
                            ->preload()
                            ->native(false)
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Təchizatçı Adı')
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $set('name', mb_convert_case($state, MB_CASE_TITLE, 'UTF-8'));
                                    })
                                    ->required(),
                                TextInput::make('phone')
                                    ->label('Telefon nömrəsi')
                                    ->placeholder('+994 XX XXX XX XX')
                                    ->tel(),

                                Toggle::make('status')
                                    ->default(true),

                            ]), */
                        Select::make('status')
                            ->label('Status')
                            ->options(['active' => 'Active', 'inactive' => 'Inactive'])
                            ->default('active')
                            ->required(),
                    ]),
            ]);
    }
}
