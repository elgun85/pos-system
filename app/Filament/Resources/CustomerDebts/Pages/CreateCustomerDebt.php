<?php

namespace App\Filament\Resources\CustomerDebts\Pages;

use App\Filament\Resources\CustomerDebts\CustomerDebtResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomerDebt extends CreateRecord
{
    protected static string $resource = CustomerDebtResource::class;
}
