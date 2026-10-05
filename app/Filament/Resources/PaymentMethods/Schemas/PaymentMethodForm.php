<?php

namespace App\Filament\Resources\PaymentMethods\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PaymentMethodForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('resource.payment.name'))
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('name', mb_convert_case($state, MB_CASE_TITLE, 'UTF-8'));
                    })
                    ->required(),

                TextInput::make('code')
                    ->label(__('resource.payment.code'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->alphaDash()
                    ->maxLength(50),

                FileUpload::make('icon')
                    ->label(__('resource.payment.icon'))
                    ->image()
                    ->disk('public')
                    ->directory('payment-methods')
                    ->imageEditor()
                    ->openable()
                    ->maxSize(4096)
                    ->deletable()
                    ->acceptedFileTypes([
                        'image/jpeg',
                        'image/png',
                        'image/gif',
                        'image/webp'
                    ]),

                Textarea::make('description')
                    ->label(__('resource.payment.description'))
                    ->placeholder(__('resource.payment.description.placeholder'))
                    ->maxLength(100),


                Toggle::make('status')
                    ->default(true),
            ]);
    }
}
