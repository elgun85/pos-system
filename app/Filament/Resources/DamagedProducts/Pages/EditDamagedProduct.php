<?php

namespace App\Filament\Resources\DamagedProducts\Pages;

use App\Filament\Resources\DamagedProducts\DamagedProductResource;
use App\Services\DamageService;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditDamagedProduct extends EditRecord
{
    protected static string $resource = DamagedProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return app(DamageService::class)->updateDamage($record, $data);
    }
}
