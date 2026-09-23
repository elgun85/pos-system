<?php

namespace App\Filament\Resources\Brands;

use App\Filament\Resources\Brands\Pages\CreateBrand;
use App\Filament\Resources\Brands\Pages\EditBrand;
use App\Filament\Resources\Brands\Pages\ListBrands;
use App\Filament\Resources\Brands\Schemas\BrandForm;
use App\Filament\Resources\Brands\Tables\BrandsTable;
use App\Models\Brand;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    public  static function getNavigationLabel(): string
    {
        return __('resource.brand.navigationLabel');
    }

    public  static function getModelLabel(): string
    {
        return __('resource.brand.modelLabel');
    }

    public  static function getPluralModelLabel(): string
    {
        return __('resource.brand.pluralModelLabel');
    }

    public static function getNavigationGroup(): string
    {
        return __('resource.navigationGroup.shop');
    }

    protected static ?int $navigationSort = 2;


    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    public static function form(Schema $schema): Schema
    {
        return BrandForm::configure($schema);
    }

    public static function getNavigationBadge(): ?string
    {
        return Cache::remember(
            'brand',
            now()->addMinutes(10),
            fn() => static::getModel()::where('status', true)
                ->count()

        );
    }
    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function table(Table $table): Table
    {
        return BrandsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBrands::route('/'),
            'create' => CreateBrand::route('/create'),
            'edit' => EditBrand::route('/{record}/edit'),
        ];
    }
}
