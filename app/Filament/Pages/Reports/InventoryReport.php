<?php

namespace App\Filament\Pages\Reports;

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

    protected static string | UnitEnum | null $navigationGroup = 'ANBAR HESABATI';
    protected static ?string $navigationLabel = 'Anbar Hesabatı';
    protected static ?string $title = 'Az qalan məhsullar';




    public function table(Table $table): Table
    {
        return $table
            ->query($this->query())
            ->columns($this->columns())->defaultSort('quantity', 'asc')
            ->filters($this->filters())
          //  ->actions($this->actions())
            ->bulkActions($this->bulkActions())
            ->headerActions($this->getHeaderActions());
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
                ->label('Məhsulun adı')
                ->searchable()
                ->sortable(),

            TextColumn::make('product.category.name')
                ->label('Kateqoriya')
                ->sortable()
                ->searchable(),

            TextColumn::make('quantity')
                ->sortable()
                ->label('Say')
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
                ->label('Excelle yukle')
                ->color('success')
        ];
    }
    protected function bulkActions(): array
    {
        return [];
    }







    protected string $view = 'filament.pages.reports.inventory-report';
}
