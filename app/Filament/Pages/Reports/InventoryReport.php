<?php

namespace App\Filament\Pages\Reports;

use App\Exports\InventoryReportExport;
use App\Models\Inventory;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class InventoryReport extends Page implements HasTable
{
    use InteractsWithTable;


    public  static function getNavigationLabel(): string
    {
        return __('resource.inventory_report.navigationLabel');
    }



    public function getTitle(): string
    {
        return __('resource.inventory_report.pluralModelLabel');
    }


    public static function getNavigationGroup(): string
    {
        return __('resource.navigationGroup.inventory');
    }




    public function table(Table $table): Table
    {
        return $table
            ->query($this->query())
            ->columns($this->columns())->defaultSort('quantity', 'asc')
            ->filters($this->filters())
            //  ->actions($this->actions())
            ->bulkActions($this->bulkActions())
           // ->headerActions($this->getHeaderActions())
            ;
    }

    protected function query(): Builder
    {
        return Inventory::query()
            ->with('product.category')
            ->where('quantity', '<=', 10)
            ->whereHas('product', function ($q) {
                $q->activeProduct();
            })
            ->limit(50);
    }



    protected function columns(): array
    {
        return [
            TextColumn::make('product.name')
                ->label(__('resource.inventory.product.name'))
                ->searchable()
                ->sortable(),

            TextColumn::make('product.category.name')
                ->label(__('resource.category.name'))
                ->sortable()
                ->searchable(),

            TextColumn::make('quantity')
                ->sortable()
                ->label(__('resource.inventory.quantity'))
                ->badge()
                ->color(fn($state) => match (true) {
                    $state == 0 => 'danger',
                    $state <= 5 => 'warning',
                    default => 'success',
                }),
        ];
    }

    protected function filters(): array
    {
        return [];
    }
    protected function getHeaderActions(): array
    {
        return [
            Action::make('Excel')
            //    ->label('Excelle yukle')
                ->color('success')
                ->action(fn () => InventoryReportExport::download()),
        ];
    }
    protected function bulkActions(): array
    {
        return [];
    }







    protected string $view = 'filament.pages.reports.inventory-report';
}
