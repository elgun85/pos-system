<?php

namespace App\Filament\Resources\PaymentMethods\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\RichEditor;
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
                    ->label('Ödəniş üsulu')
                    ->placeholder('Ödəniş üsulu')
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('name', mb_convert_case($state, MB_CASE_TITLE, 'UTF-8'));
                    })
                    ->required(),

                FileUpload::make('icon')
                    ->label('İkon')
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
                    ->label('Description')
                    ->placeholder('100 simvola qədər təsvir daxil edin')
                    ->maxLength(100),


                Toggle::make('status')
                    ->label('Vəziyyət')
                    ->default(true),
            ]);
    }
}
