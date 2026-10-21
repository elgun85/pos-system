<?php

namespace App\Filament\Resources\Purchases\Pages;

use App\Exceptions\StockException;
use App\Filament\Resources\Purchases\PurchaseResource;
use App\Models\Purchase;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPurchase extends EditRecord
{
    protected static string $resource = PurchaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function (DeleteAction $action, Purchase $record) {
                    if ($message = $record->stockBlockMessage()) {
                        Notification::make()
                            ->danger()
                            ->title(__('resource.purchase.cannot_delete'))
                            ->body($message)
                            ->persistent()
                            ->send();

                        $action->cancel();
                    }
                }),
        ];
    }

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        try {
            parent::save($shouldRedirect, $shouldSendSavedNotification);
        } catch (StockException $e) {
            Notification::make()
                ->danger()
                ->title(__('resource.purchase.cannot_save'))
                ->body($e->getMessage())
                ->persistent()
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
