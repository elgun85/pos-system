<?php

namespace App\Filament\Resources\CustomerDebts\Pages;

use App\Filament\Resources\CustomerDebts\CustomerDebtResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCustomerDebts extends ListRecords
{
    protected static string $resource = CustomerDebtResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
