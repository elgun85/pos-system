<?php

namespace App\Filament\Resources\CustomerTransactions\Pages;

use App\Filament\Resources\CustomerTransactions\CustomerTransactionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomerTransaction extends CreateRecord
{
    protected static string $resource = CustomerTransactionResource::class;
}
