<?php

namespace App\Filament\Resources\Inventories\Pages;

use App\Filament\Resources\Inventories\InventoryResource;
use App\Models\Inventory;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class CreateInventory extends CreateRecord
{
    protected static string $resource = InventoryResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $inventory = Inventory::where('product_id', $data['product_id'])
                ->lockForUpdate()
                ->first();
            if ($inventory) {
                $inventory->update(['quantity' => $data['quantity']]);

                return $inventory;
            }
            return Inventory::create($data);
        });
    }
}
