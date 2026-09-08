<?php

namespace App\Filament\Resources\DamagedProducts\Pages;

use App\Filament\Resources\DamagedProducts\DamagedProductResource;
use App\Services\DamageService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateDamagedProduct extends CreateRecord
{
    protected static string $resource = DamagedProductResource::class;

        protected function handleRecordCreation(array $data): Model
    {
        return app(DamageService::class)->createDamage($data);
    }

        protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
