<?php

namespace App\Filament\Resources\CustomerDebts\Pages;

use App\Filament\Resources\CustomerDebts\CustomerDebtResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCustomerDebt extends EditRecord
{
    protected static string $resource = CustomerDebtResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
